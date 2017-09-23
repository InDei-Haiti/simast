<?php
/**
 * Created by JetBrains PhpStorm.
 * User: stig
 * Date: 21.04.12
 * Time: 16:42
 */
function DOMinnerHTML($element)
{
	$innerHTML = "";
	$children = $element->childNodes;
	foreach ($children as $child) {
		$tmp_dom = new DOMDocument();
		$tmp_dom->appendChild($tmp_dom->importNode($child, true));
		$innerHTML .= trim($tmp_dom->saveHTML());
	}
	return $innerHTML;
}

function parseReport($id, $file)
{
	global $baseDir;
	require_once($baseDir . '/lib/ZendDomQuery/Query.php');
	$ffile = file_get_contents($file);
	$dom = new Zend_Dom_Query($ffile);
	$cols = array();
	$rows = array();
	$rowb = array();
	$colb = array();
	$dataset = array();

	$paper = $dom->query("#paper");

	$rroot = $dom->query("#report_root");

	$q = new DBQuery;
	$q->addTable('reports', 'r');
	$q->addQuery('r.id, r.title');
	$q->addOrder('r.title');
	$reportArray = $q->loadHashList();
	
	foreach ($paper as $pip) {
		$start = $pip->getAttribute("data-start");
		$stop = $pip->getAttribute("data-stop");
	}

	if (count($rroot) > 0) {

		foreach ($rroot as $rtab) {
			$pyear = (int)$rtab->getAttribute("data-year");
			$pmonth = (int)$rtab->getAttribute("data-month");
			$pcenter_id = (int)$rtab->getAttribute("data-center_id");
			$pcenter_name = $rtab->getAttribute("data-center_name");
			$dept = (int)$rtab->getAttribute("data-dptmt");

			$tpl = (int)$rtab->getAttribute("data-report_id");
			$tplName = $rtab->getAttribute("data-report_name");

			$centers = centerList();

			if (strtolower($centers[$pcenter_id]) == trim($pcenter_name) || $pcenter_name == '') {
				$useCenter = $pcenter_id;
			} else {
				$centers = array_map("strtolower", $centers);
				$useCenter = array_search($pcenter_name, $centers, false);
			}

			$dbReportId = $tpl;
			
			//we check for matching between key of report and its name, expecting to have same names all over leatoto centers
			if($tplName != $reportArray[$tpl]){
				$dbReportId = array_search($tplName, $reportArray);
			}
			
			$sql = 'update center_reports set 
							rtpl="'.$dbReportId.'",
							rmon="' . $pmonth . '",
							ryear="' . $pyear . '",
							center="' . $useCenter . '",
							rdept="' . $dept . '",
							start="' . $start . '",
							stop="' . $stop . '"
					where id="' . $id . '"';
			$resup = mysql_query($sql);
		}
		$sections = $dom->query(".sec_part");


		foreach ($sections as $i => $rowhead) {
			$sid_full = $rowhead->getAttribute("id");
			$sid = str_replace("sec_", "", $sid_full);

			$scont = DOMinnerHTML($rowhead);

			$sql = 'insert into cr_items (crid, section_id, content)
				values ("%d","%d","%s")';
			$res = mysql_query(sprintf($sql, $id, $sid, mysql_real_escape_string(gzencode($scont, 9, FORCE_GZIP))));

		}
	}


}

$reportPath = $baseDir . '/files/reports/';

if ($_GET['mode'] != '') {
	$zmode = $_GET['mode'];

	switch ($zmode) {
		case 'inreport' :
			$resOut = 'fail';
			$nname = false;
			if (count($_FILES) > 0 && isset($_FILES['cname'])) {
				$fdata = $_FILES['cname'];
				$nname = saveUploadedFile($fdata, $reportPath, true);

				if ($nname) {
					//$preport = (int)$_POST['creport'];
					$sql = 'insert into center_reports (rfile, title)
					values ("%s","%s")';
					$res = mysql_query(sprintf($sql, $nname, $fdata['name']));
					if ($res) {
						$newr = mysql_insert_id();
						// We have report file saved to disk, now we break it into sections, for future extra analysis
						parseReport($newr, $reportPath . '/' . $nname);
						$resOut = $newr;
					}
				}
			}
			echo $resOut;

			break;

		case 'vreport':
			$grid = (int)$_GET['rid'];
			if ($grid > 0) {
				$sql = 'select rfile from center_reports where id="' . $grid . '"';
				$rf = mysql_query($sql);
				if ($rf) {
					$rfr = mysql_fetch_assoc($rf);
					$pf = fopen($baseDir . '/files/reports/' . $rfr['rfile'], 'r');
					fpassthru($pf);
				}
			}
			break;

		case 'vision':
			$repid = (int)$_GET['rep_id'];
			$stat = (int)$_GET['rep_status'];
			$res = 'fail';
			if ($repid > 0) {
				$sql = 'update center_reports set visible="%d" where id="%d"';
				$res = mysql_query(sprintf($sql, $stat, $repid));
				if ($res)
					$res = 'ok';
			}
			echo $res;
			break;

		default:
			break;

	}
	return;
}


$centers = centerList(true);
$dept = dPgetSysVal("ClinicalDepartments");

$tpl = new Templater($baseDir . "/modules/system/creports.tpl");
$tpl->centers = arraySelect($centers, 'csource', 'class="text mandat"', false);

$year = intval(date("Y"));
$month = intval(date("n"));


$q = new DBQuery;
$q->addTable('reports', 'r');
$q->addQuery('r.id, r.title');
$q->addOrder('r.title');
//$reportArray = (arrayMerge(array(0 => '-Select Report Template-'), $q->loadHashList()));

//$tpl->report = arraySelect($reportArray, 'creport', 'class="text mandat"', false);

//$tpl->dept = arraySelect(dPgetSysVal("ClinicalDepartments"), 'cdept', 'class="text mandat"', false);

require_once($baseDir . '/classes/genericTable.class.php');
$gt = new genericTable();

$gt->setEmptyText('No reports uploaded');

$headers = array(
	'Title' => 'string',
	'Center' => 'string',
	'Department' => 'string',
	'Start' => 'date',
	'End' => 'date',
	'Visible' => 'string'
);

$gt->makeHeader($headers);

$decs = array(5 => '<input type="checkbox" class="visw" data-id="##6##" ##7##>', 3 => 'date', 4 => 'date');

$gt->setDecorators($decs);

$row_data = array();

$sql = 'select * from center_reports order by title';

$res = mysql_query($sql);
$rows = 0;
if ($res) {
	while ($row = mysql_fetch_assoc($res)) {

		$tar = array(
			$row['title'],
			$centers[$row['center']],
			$dept[$row['rdept']],
			$row['start'],
			$row['stop'],
			$row['visible'],
			$row['id'],
			$row['visible'] == '1' ? 'checked' : ''

		);
		$gt->fillBody($tar);
		++$rows;
	}
}

//echo "$s\n";
/*if ($rows == 0) {
    $gt->addTableHtmlRow($CR . '<tr><td colspan="6">' . $AppUI->_('No reports uploaded') . '</td></tr>');
}*/
//get compiled table into another template var
$tpl->rtable = $gt->compile(true);

$tpl->output(true);
