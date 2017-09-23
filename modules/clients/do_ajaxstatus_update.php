<?php

ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

$loginFromPage = 'index.php';
//$baseDir = dirname(__FILE__);
$baseDir = "../..";

$baseUrl = (isset ($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' )? 'https://' : 'http://';
$baseUrl .= isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : getenv('HTTP_HOST');
$pathInfo = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : getenv ('PATH_INFO');

if (@$pathInfo)
{
   $baseUrl .= dirname($pathInfo);
}
else
{
   $baseUrl .= isset($_SERVER['SCRIPT_NAME']) ? dirname($_SERVER['SCRIPT_NAME']): dirname(getenv('SCRIPT_NAME'));
}
$dPconfig = array();

clearstatcache();
if ( is_file("$baseDir/includes/config.php"))
{
    require_once "$baseDir/includes/config.php";
}
else
{
    echo "<html><head><meta http-equiv='refresh' content='5; URL=".$baseUrl."/install/index.php></head><body>";
    	echo "Fatal Error. You haven't created a config file yet.<br/><a href='./install/index.php'>
		Click Here To Start Installation and Create One!</a> (forwarded in 5 sec.)</body></html>";
	exit();
}

if (! isset($GLOBALS['OS_WIN']))
    $GLOBALS['OS_WIN'] = (stristr(PHP_OS, "WIN") !== false);

require_once "$baseDir/includes/db_adodb.php";
require_once "$baseDir/includes/db_connect.php";
require_once "$baseDir/includes/main_functions.php";
require_once "$baseDir/classes/ui.class.php";
require_once "$baseDir/classes/permissions.class.php";
require_once "$baseDir/includes/session.php";

$suppressHeaders = dPgetParam($_GET, 'suppressHeaders', false);
dPsessionStart(array('AppUI'));

header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");	// Date in the past
header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");	// always modified
header ("Cache-Control: no-cache, must-revalidate, no-store, post-check=0, pre-check=0");	// HTTP/1.1
header ("Pragma: no-cache");	// HTTP/1.0

if (!isset ( $_SESSION['AppUI']) || isset($_GET['logout']))
{
  if (isset($_GET['logout']) && isset($_SESSION['AppUI']->user_id))
  {
    $AppUI = & $_SESSION['AppUI'];
    $user_id = $AppUI->user_id;
    addHistory('login', $AppUI->user_id, 'logout', $AppUI->user_first_name . ' ' . $AppUI->user_last_name);
  }
  
  $_SESSION['AppUI'] = new CAppUI;
}

$AppUI = & $_SESSION['AppUI'];
$last_insert_id = $AppUI->last_insert_id;

$AppUI->checkStyle();

require_once( $AppUI->getSystemClass('dp'));
require_once( $AppUI->getSystemClass('date'));
require_once( $AppUI->getSystemClass('query'));

require_once "$baseDir/misc/debug.php";

$AppUI->updateLastAction($last_insert_id);

if ($AppUI->doLogin())
{
  $AppUI->loadPrefs(0);
}
if (isset($user_id) && isset($_GET['logout'])){
    $AppUI->registerLogout($user_id);
}

if (dPgetParam( $_POST, 'lostpass', 0 )) {
	$uistyle = $dPconfig['host_style'];
	$AppUI->setUserLocale();
	@include_once "$baseDir/locales/$AppUI->user_locale/locales.php";
	@include_once "$baseDir/locales/core.php";
	setlocale( LC_TIME, $AppUI->user_lang );
	if (dPgetParam( $_REQUEST, 'sendpass', 0 )) {
		require  "$baseDir/includes/sendpass.php";
		sendNewPass();
	} else {
		require  "$baseDir/style/$uistyle/lostpass.php";
	}
	exit();
}

if (isset($_REQUEST['login'])) {

	$username = dPgetParam( $_POST, 'username', '' );
	$password = dPgetParam( $_POST, 'password', '' );
	$redirect = dPgetParam( $_REQUEST, 'redirect', '' );
	$AppUI->setUserLocale();
	@include_once( "$baseDir/locales/$AppUI->user_locale/locales.php" );
	@include_once "$baseDir/locales/core.php";
	$ok = $AppUI->login( $username, $password );
	if (!$ok) {
		$AppUI->setMsg( 'Login Failed');
	} else {
	           //Register login in user_acces_log
	           $AppUI->registerLogin();
	}
        addHistory('login', $AppUI->user_id, 'login', $AppUI->user_first_name . ' ' . $AppUI->user_last_name);
	$AppUI->redirect( "$redirect" );
}

$uistyle = $AppUI->getPref( 'UISTYLE' ) ? $AppUI->getPref( 'UISTYLE' ) : $dPconfig['host_style'];

$m = '';
$a = '';
$u = '';

if ($AppUI->doLogin()) {
	// load basic locale settings
	$AppUI->setUserLocale();
	@include_once( "./locales/$AppUI->user_locale/locales.php" );
	@include_once( "./locales/core.php" );
	setlocale( LC_TIME, $AppUI->user_lang );
	$redirect = @$_SERVER['QUERY_STRING'];
	if (strpos( $redirect, 'logout' ) !== false) {
		$redirect = '';
	}

	if (isset( $locale_char_set )) {
		header("Content-type: text/html;charset=$locale_char_set");
	}

	require "$baseDir/style/$uistyle/login.php";
	// destroy the current session and output login page
	session_unset();
	session_destroy();
	exit;
}

$AppUI->setUserLocale();

require_once "$baseDir/includes/permissions.php";

$def_a = 'index';

if (!isset($_GET['m']) && !empty ($dPconfig['default_view_m']))
{
  $m = $dPconfig['default_view_m'];
  $def_a = !empty($dPconfig['default_view_a']) ? $dPconfig['default_view_a'] : $def_a;
  $tab = $dPconfig['default_view_tab'];
}
else
{
  $m = $AppUI->checkFileName(dPgetParam( $_GET, 'm', getReadableModule() ));
}

$a = $AppUI->checkFileName(dPgetParam( $_GET, 'a', $def_a));
$u = $AppUI->checkFileName(dPgetParam( $_GET, 'u', '' ));

@include_once "$baseDir/locales/$AppUI->user_locale/locales.php";
@include_once "$baseDir/locales/core.php";

setlocale( LC_TIME, $AppUI->user_lang );
$m_config = dPgetConfig($m);
@include_once "$baseDir/functions/" . $m . "_func.php";

$perms =& $AppUI->acl();
$canAccess = $perms->checkModule($m, 'access');
$canRead = $perms->checkModule($m, 'view');
$canEdit = $perms->checkModule($m, 'edit');
$canAuthor = $perms->checkModule($m, 'add');
$canDelete = $perms->checkModule($m, 'delete');

if ( !$suppressHeaders )
{
	if (isset( $locale_char_set ))
        {
		header("Content-type: text/html;charset=$locale_char_set");
        }
}

$modclass = $AppUI->getModuleClass($m);

if (file_exists($modclass))
	include_once( $modclass );
if ($u && file_exists("$baseDir/modules/$m/$u/$u.class.php"))
	include_once "$baseDir/modules/$m/$u/$u.class.php";

if (isset( $_REQUEST["dosql"]) )
{
    require  "$baseDir/modules/$m/" . ($u ? "$u/" : "") . $AppUI->checkFileName($_REQUEST["dosql"]) . ".php";
}
include  "$baseDir/style/$uistyle/overrides.php";
ob_start();

require_once( $AppUI->getModuleClass('admin'));
require_once( $AppUI->getModuleClass('contacts'));
$company_id = dPgetParam( $_REQUEST, 'company_id', 0 );
$user = dPgetParam( $_REQUEST, 'user', 0 );


//load user
$userObj = new CUser();
if ($userObj->load($user))
{
	//var_dump($userObj);
	$contactObj = new CContact();
	$contactObj->load($userObj->user_contact);

}
//$AppUI->setMsg ("The following graphs were assigned to " . $contactObj->getFullname() ." :" , UI_MSG_OK);

//$company_id = dPgetParam( $_POST, 'company_id', 0 );
//$company_owner = dPgetParam( $_POST, 'company_owner', 0 );
$obj = new CClient();

if (!$obj->load($client_id))
{
	$AppUI->setMsg( $obj->getError(), UI_MSG_ERROR);
	$AppUI->redirect();
}

//require_once("./classes/CustomFields.class.php");

//$AppUI->setMsg("Client $obj->company_name", 0, true);
$obj->client_status = $status;
    
if ($msg = $obj->store())
{
 	$AppUI->setMsg( 'status' , UI_MSG_ERROR);
	$AppUI->redirect();
}
	// Let the client know we're sending back XML
header("Content-Type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
echo "<client><status>".$obj->client_status."</status></client>";
//$AppUI->redirect();
//$AppUI->setMsg("Client $obj->company_name, " , UI_MSG_OK,true );
	
	
?>
