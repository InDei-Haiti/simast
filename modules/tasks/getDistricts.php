<?php
$loginFromPage = 'index.php';
require_once '../../base.php';

clearstatcache();
if( is_file( DP_BASE_DIR . '/includes/config.php' ) ) {

	require_once DP_BASE_DIR . '/includes/config.php';

} else {
	echo '<html><head><meta http-equiv="refresh" content="5; URL='.DP_BASE_URL.'/install/index.php"></head><body>';
	echo 'Fatal Error. You haven\'t created a config file yet.<br/><a href="./install/index.php">'
	  .'Click Here To Start Installation and Create One!</a> (forwarded in 5 sec.)</body></html>';
	exit();
}

if (! isset($GLOBALS['OS_WIN']))
	$GLOBALS['OS_WIN'] = (stristr(PHP_OS, 'WIN') !== false);

// tweak for pathname consistence on windows machines
require_once DP_BASE_DIR.'/includes/main_functions.php';
require_once DP_BASE_DIR.'/includes/db_adodb.php';
require_once DP_BASE_DIR.'/includes/db_connect.php';

require_once DP_BASE_DIR.'/classes/ui.class.php';
require_once DP_BASE_DIR.'/classes/permissions.class.php';
require_once DP_BASE_DIR.'/includes/session.php';

// don't output anything. Usefull for fileviewer.php, gantt.php, etc.
$suppressHeaders = dPgetParam( $_GET, 'suppressHeaders', false );

// manage the session variable(s)
dPsessionStart(array('AppUI'));

// write the HTML headers
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');	// Date in the past
header ('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');	// always modified
header ('Cache-Control: no-cache, must-revalidate, no-store, post-check=0, pre-check=0');	// HTTP/1.1
header ('Pragma: no-cache');	// HTTP/1.0

// check if session has previously been initialised
if (!isset( $_SESSION['AppUI'] ) || isset($_GET['logout'])) {
    if (isset($_GET['logout']) && isset($_SESSION['AppUI']->user_id))
    {
        $AppUI =& $_SESSION['AppUI'];
	$user_id = $AppUI->user_id;
        addHistory('login', $AppUI->user_id, 'logout', $AppUI->user_first_name . ' ' . $AppUI->user_last_name);
    }

	$_SESSION['AppUI'] = new CAppUI;
}
$AppUI =& $_SESSION['AppUI'];
$last_insert_id =$AppUI->last_insert_id;

$AppUI->checkStyle();

// load the commonly used classes
require_once( $AppUI->getSystemClass( 'date' ) );
require_once( $AppUI->getSystemClass( 'dp' ) );
require_once( $AppUI->getSystemClass( 'query' ) );

require_once DP_BASE_DIR.'/misc/debug.php';

//Function for update lost action in user_access_log
$AppUI->updateLastAction($last_insert_id);
// load default preferences if not logged in
if ($AppUI->doLogin()) {
	$AppUI->loadPrefs( 0 );
}

//Function register logout in user_acces_log
if (isset($user_id) && isset($_GET['logout'])){
    $AppUI->registerLogout($user_id);
}

// check is the user needs a new password
if (dPgetParam( $_POST, 'lostpass', 0 )) {
	$uistyle = dPgetConfig('host_style');
	$AppUI->setUserLocale();
	@include_once DP_BASE_DIR.'/locales/'.$AppUI->user_locale.'/locales.php';
	@include_once DP_BASE_DIR.'/locales/core.php';
	setlocale( LC_TIME, $AppUI->user_lang );
	if (dPgetParam( $_REQUEST, 'sendpass', 0 )) {
		require  DP_BASE_DIR.'/includes/sendpass.php';
		sendNewPass();
	} else {
		require  DP_BASE_DIR.'/style/'.$uistyle.'/lostpass.php';
	}
	exit();
}

// check if the user is trying to log in
// Note the change to REQUEST instead of POST.  This is so that we can
// support alternative authentication methods such as the PostNuke
// and HTTP auth methods now supported.
if (isset($_REQUEST['login'])) {

	$username = dPgetCleanParam( $_POST, 'username', '' );
	$password = dPgetCleanParam( $_POST, 'password', '' );
	$redirect = dPgetCleanParam( $_REQUEST, 'redirect', '' );
	$AppUI->setUserLocale();
	@include_once( DP_BASE_DIR.'/locales/'.$AppUI->user_locale.'/locales.php' );
	@include_once DP_BASE_DIR.'/locales/core.php';
	$ok = $AppUI->login( $username, $password );
	if (!$ok) {
		$AppUI->setMsg( 'Login Failed');
	} else {
		//Register login in user_acces_log
		$AppUI->registerLogin();
	}
	addHistory('login', $AppUI->user_id, 'login', $AppUI->user_first_name . ' ' . $AppUI->user_last_name);
	$AppUI->redirect($redirect);
}

// supported since PHP 4.2
// writeDebug( var_export( $AppUI, true ), 'AppUI', __FILE__, __LINE__ );

// set the default ui style
$uistyle = $AppUI->getPref( 'UISTYLE' ) ? $AppUI->getPref( 'UISTYLE' ) : dPgetConfig('host_style');

// clear out main url parameters
$m = '';
$a = '';
$u = '';

// check if we are logged in
if ($AppUI->doLogin()) {
	// load basic locale settings
	$AppUI->setUserLocale();
	@include_once( './locales/'.$AppUI->user_locale.'/locales.php' );
	@include_once( './locales/core.php' );
	setlocale( LC_TIME, $AppUI->user_lang );
	$redirect = $_SERVER['QUERY_STRING']?strip_tags($_SERVER['QUERY_STRING']):'';
	if (strpos( $redirect, 'logout' ) !== false) {
		$redirect = '';
	}

	if (isset( $locale_char_set )) {
		header('Content-type: text/html;charset='.$locale_char_set);
	}

	require DP_BASE_DIR.'/style/'.$uistyle.'/login.php';
	// destroy the current session and output login page
	session_unset();
	session_destroy();
	exit;
}
$AppUI->setUserLocale();


// bring in the rest of the support and localisation files
require_once DP_BASE_DIR.'/includes/permissions.php';


$def_a = 'index';
if (! isset($_GET['m']) && !empty($dPconfig['default_view_m'])) {
  	$m = $dPconfig['default_view_m'];
	$def_a = !empty($dPconfig['default_view_a']) ? $dPconfig['default_view_a'] : $def_a;
	$tab = $dPconfig['default_view_tab'];
} else {
	// set the module from the url
	$m = $AppUI->checkFileName(dPgetCleanParam( $_GET, 'm', getReadableModule() ));
}
// set the action from the url
$a = $AppUI->checkFileName(dPgetCleanParam( $_GET, 'a', $def_a));

/* This check for $u implies that a file located in a subdirectory of higher depth than 1
 * in relation to the module base can't be executed. So it would'nt be possible to
 * run for example the file module/directory1/directory2/file.php
 * Also it won't be possible to run modules/module/abc.zyz.class.php for that dots are
 * not allowed in the request parameters.
*/

$u = $AppUI->checkFileName(dPgetCleanParam( $_GET, 'u', '' ));

// load module based locale settings
@include_once DP_BASE_DIR.'/locales/'.$AppUI->user_locale.'/locales.php';
@include_once DP_BASE_DIR.'/locales/core.php';

setlocale( LC_TIME, $AppUI->user_lang );
$m_config = dPgetConfig($m);
@include_once DP_BASE_DIR.'/functions/' . $m . '_func.php';

// TODO: canRead/Edit assignements should be moved into each file

// check overall module permissions
// these can be further modified by the included action files
$perms =& $AppUI->acl();
$canAccess = $perms->checkModule($m, 'access');
$canRead = $perms->checkModule($m, 'view');
$canEdit = $perms->checkModule($m, 'edit');
$canAuthor = $perms->checkModule($m, 'add');
$canDelete = $perms->checkModule($m, 'delete');

if ( !$suppressHeaders ) {
	// output the character set header
	if (isset( $locale_char_set )) {
		header('Content-type: text/html;charset='.$locale_char_set);
	}
}

// include the module class file - we use file_exists instead of @ so
// that any parse errors in the file are reported, rather than errors
// further down the track.
$modclass = $AppUI->getModuleClass($m);
if (file_exists($modclass))
	include_once( $modclass );
if ($u && file_exists(DP_BASE_DIR.'/modules/'.$m.'/'.$u.'/'.$u.'.class.php'))
	include_once DP_BASE_DIR.'/modules/'.$m.'/'.$u.'/'.$u.'.class.php';

// do some db work if dosql is set
// TODO - MUST MOVE THESE INTO THE MODULE DIRECTORY
if (isset( $_REQUEST['dosql']) ) {
    //require('./dosql/' . $_REQUEST['dosql'] . '.php');
  require  DP_BASE_DIR.'/modules/'.$m.'/' . ($u ? ($u.'/') : '') . $AppUI->checkFileName($_REQUEST['dosql']) . '.php';
}

// start output proper
//include  DP_BASE_DIR.'/style/'.$uistyle.'/overrides.php';
ob_start();

$province_string = dPgetParam( $_REQUEST, 'province_id', 0 );
$province_array = explode("," , $province_string);
$province_id = $province_array[0];
//fetch admin level 1
$q = new DBQuery();
$q->addTable('administrative_regions');
$q->addQuery('region_id, region_name');
$q->addWhere('region_parent in (' . $province_id  . ')');
$q->addOrder('region_parent, region_name');
$admin3_list = $q->loadArray();

$encoded = json_encode($admin3_list);
echo $encoded;

?>