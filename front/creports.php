<?php
/**
 * Created by JetBrains PhpStorm.
 * User: stig
 * Date: 21.04.12
 * Time: 16:42
 */

$reportPath = $baseDir . '/files/reports/';

if ($_GET['mode'] != '') {
	$zmode = $_GET['mode'];

	switch ($zmode) {
		case 'inreport' :
			if (count($_FILES) > 0 && isset($_FILES['cname'])) {
				$fdata = $_FILES['cname'];
				$nname = saveUploadedFile($fdata, $reportPath, true);
			}
			$center = (int)$_POST['csource'];
			$pyear = (int)$_POST['rep_year'];
			$pmonth = (int)$_POST['rep_mon'];
			$preport = (int)$_POST['creport'];
			$sql = 'insert into center_reports (center,rmon, ryear,rtpl,rfile)
					values ("%d","%d","%d","%d","%s")';
			$res = mysql_query(sprintf($sql, $center, $pmonth, $pyear, $preport, $nname));
			if ($res) {
				$newr = mysql_insert_id();
				parseReport($newr, $reportPath . '/' . $nname);
				echo $newr;
			} else {
				echo 'fail';
			}

			break;

		case 'vreport':
			if ((int)$_GET['rid'] > 0) {
				$grid = (int)$_GET['rid'];
				$sql = 'select rfile from center_reports where id="' . $grid . '"';
				$rf = mysql_query($sql);
				if ($rf) {
					$rfr = mysql_fetch_assoc($rf);
					$pf = fopen($baseDir . '/files/reports/' . $rfr['rfile'], 'r');
					fpassthru($pf);
				}
			}
			break;
		default:
			break;

	}
	return;
}


$centers = centerList(true);

$tpl = new Templater($baseDir . "/modules/front/creports.tpl");
$centers[0] = "All";
$tpl->centers_selector = arraySelect($centers, 'csource', 'class="text center_target"', false);

$year = intval(date("Y"));
$month = intval(date("n"));

$ySel = '<select class="text" name="rep_year" multiple size="3">';
for ($i = $year - 5; $i < $year + 5; $i++) {
	$sel = ($year === $i ? "selected" : "");
	$ySel .= '<option value="' . ($i) . '" ' . $sel . '>' . $i . '</option>';
}
$ySel .= '</select>';

$tpl->year = $ySel;
$monSel = '<select class="text" name="rep_mon" multiple size="3">';
for ($i = 0, $l = count($monthNames); $i < $l; $i++) {
	$sel = ($month === ($i + 1) ? "selected" : "");
	$monSel .= '<option value="' . ($i + 1) . '" ' . $sel . '>' . $monthNames[$i] . '</option>';
}
$monSel .= '</select>';
$tpl->month = $monSel;

$dept = dPgetSysVal("ClinicalDepartments");
$tpl->dept_selector = arraySelect($dept,"dept_sel","class='text dept_target'",1);



/*$q = new DBQuery;
$q->addTable('reports', 'r');
$q->addQuery('r.id, r.title');
$q->addOrder('r.title');
$reportArray = (arrayMerge(array(0 => '-Select Report Template-'), $q->loadHashList()));

$tpl->report = arraySelect($reportArray, 'creport', 'class="text mandat"', false);*/

$tpl->output(true);

