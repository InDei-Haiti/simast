<?
ini_set('display_errors', 1); // Ensure errors get to the user.
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

// If you experience a 'white screen of death' or other problems,
// uncomment the following line of code:
//error_reporting( E_ALL );

$loginFromPage = 'index.php';
require_once '../base.php';

clearstatcache();
if (is_file("$baseDir/includes/config.php")) {

	require_once "$baseDir/includes/config.php";

} else {
	echo "<html><head><meta http-equiv='refresh' content='5; URL=" . $baseUrl . "/install/index.php'></head><body>";
	echo "Fatal Error. You haven't created a config file yet.<br/><a href='./install/index.php'>
		Click Here To Start Installation and Create One!</a> (forwarded in 5 sec.)</body></html>";
	exit();
}

if (!isset($GLOBALS['OS_WIN']))
	$GLOBALS['OS_WIN'] = (stristr(PHP_OS, "WIN") !== false);

// tweak for pathname consistence on windows machines
require_once "$baseDir/includes/db_adodb.php";
require_once "$baseDir/includes/db_connect.php";
require_once "$baseDir/includes/main_functions.php";
require_once "$baseDir/classes/ui.class.php";
require_once "$baseDir/classes/permissions.class.php";
require_once "$baseDir/includes/session.php";

// don't output anything. Usefull for fileviewer.php, gantt.php, etc.
$suppressHeaders = dPgetParam($_GET, 'suppressHeaders', false);

// manage the session variable(s)
dPsessionStart(array('AppUI'));

// write the HTML headers
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header("Cache-Control: no-cache, must-revalidate, no-store, post-check=0, pre-check=0"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.0


// check if session has previously been initialised
if (!isset($_SESSION['AppUI']) || isset($_GET['logout'])) {
	if (isset($_GET['logout']) && isset($_SESSION['AppUI']->user_id)) {
		$AppUI =& $_SESSION['AppUI'];
		$user_id = $AppUI->user_id;
		addHistory('login', $AppUI->user_id, 'logout', $AppUI->user_first_name . ' ' . $AppUI->user_last_name);
	}

	$_SESSION['AppUI'] = new CAppUI;
}
$AppUI =& $_SESSION['AppUI'];
$last_insert_id = $AppUI->last_insert_id;

$AppUI->checkStyle();

// load the commonly used classes
require_once($AppUI->getSystemClass('date'));
require_once($AppUI->getSystemClass('dp'));
require_once($AppUI->getSystemClass('query'));

require_once "$baseDir/misc/debug.php";

if($_POST['mode'] == 'printpdf' && $_POST['pdata'] !=''){
	setcookie("filePDF","1");
	require_once($baseDir . '/lib/mpdf/mpdf.php');
	$dompdf = new mPDF();
	$stylez = file_get_contents('creports.css');
	$code = $_POST['pdata'];

	$tpl = new Templater('print-pdf.tpl');
	$tpl->content = $code;
	$tpl->style = $stylez;

	$dompdf->WriteHTML($tpl->output());
	$savedName = 'pdf-'.time().'.pdf';
	$pdfPath = $baseDir . '/files/tmp/'.$savedName;
	$dompdf->Output($savedName , 'D');
	return;
}elseif($_GET['mode'] === 'dig'){
	include("cr_browser.php");
	return;
}
/*-----------------------------------------------------------------------------------------------------------------------------*/

$centers = centerList(true);

$tpl = new Templater("creports.tpl");
$centers[0] = "All";
$tpl->centers_selector = buildHTMLList($centers);

$year = intval(date("Y"));
$month = intval(date("n"));

$ySel = '<select class="text" name="rep_year" >
<option value="-1"><b>--Blank--</b></option>';
for ($i = $year - 5; $i < $year + 5; $i++) {
	$sel = ($year === $i ? "selected" : "");
	$ySel .= '<option value="' . ($i) . '" ' . $sel . '>' . $i . '</option>';
}
$ySel .= '</select>';

$tpl->year = $ySel;
$monSel = '<select class="text" name="rep_mon" >
<option value="-1"><b>--Blank--</b></option>';
for ($i = 0, $l = count($monthNames); $i < $l; $i++) {
	$sel = ($month === ($i + 1) ? "selected" : "");
	$monSel .= '<option value="' . ($i + 1) . '" ' . $sel . '>' . $monthNames[$i] . '</option>';
}
$monSel .= '</select>';
$tpl->month = $monSel;

$dept = dPgetSysVal("ClinicalDepartments");
$tpl->dept_selector = buildHTMLList($dept);


$sql = 'select id, title from center_reports  where visible="1" order by title';
$res = mysql_query($sql);
$allReps = array();
while($row = mysql_fetch_assoc($res)){
    $allReps[] = '<option value="'.$row['id'].'">'.$row['title'].'</option>';
}

$tpl->all_reports=join("\n",$allReps);

$tpl->output(true);


$fileName = trim($_GET['file_name']);
$dirPath = dirname(__FILE__).'/files/';
$filePath = $dirPath.$fileName;
echo 'is readable = '. is_readable($filePath);
echo 'is exist = ',file_exists($filePath);
if($fileName != '' && file_exists($filePath) && is_readable($filePath) ){
    setcookie($fileName,"1");
    $fh = fopen($filePath,"r");
    fpassthru($fh);
    fclose($fh);
}

?>

