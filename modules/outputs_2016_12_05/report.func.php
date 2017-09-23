<?php
require_once $AppUI->getSystemClass('systemImport');

function prettyDate($d)
{

	if (preg_match("/\d\d\/\d\d\/\d\d\d\d/", $d)) {
		$date = (int)join("", array_reverse(explode("/", $d)));
	} else {
		$date = (int)$d;
	}
	if ($date <= 0) {
		$date = false;
	}
	return $date;
}


global $titles, $dPconfig;
$uqkey = uniqid();
$pov = $glback = '';
$sblk = 1;
$rid = ( int )($_GET ['itid']);
$monitorKey = $_GET['urkey'];
//$masterStart = (intval($_GET ['ds']) > 0 ? ( int )$_GET ['ds'] : false);
//$masterEnd = (intval($_GET ['de']) > 0 ? ( int )$_GET ['de'] : false);
$masterStart = prettyDate($_GET['ds']);
$masterEnd = prettyDate($_GET['de']);
$q = new DBQuery ();
$q->addTable('reports');
$q->addWhere('id="' . $rid . '"');
$q->setLimit(1);
$rdata = $q->loadList();
$rdata = $rdata [0];
$entries = array();
if (count($rdata) > 0) {
	eval ('$glback=' . gzdecode($rdata ["backdoor"]) . ';');
	eval ('$entries=' . gzdecode($rdata ["entries"]) . ';');
	$cliTabMet = false;
	$title = $rdata ['title'];
	//var_dump($rdata);
	$sdate = new CDate ($masterStart === false ? $rdata ['start_date'] : $masterStart);
	$edate = new CDate ($masterEnd === false ? $rdata ['end_date'] : $masterEnd);
	$sdvalid = $sdate->getYear();
	$edvalid = $edate->getYear();
	
	//echo '<pre>';
	//var_dump($entries);
	//echo '</pre>';
    $html_pre = '
    <script type="text/javascript">
        function getRT(){
            var ntit;
            if(ntit = prompt("Enter name for file") ){
                if(trim(ntit) != "" ){
                    document.bwork.mode.value = "2html";
                    document.bwork.rn.value = ntit;
                    document.bwork.submit();
                }
            }
        }
    </script>
    ';
	$html_pre.= '
	<iframe style="display:none;" name="balda" src="about:blank;"></iframe>
		&nbsp;&nbsp;<input type="button" class="text" value="Pop out" onclick="popTable(\'paper\');">&nbsp;&nbsp;
		<form action="index.php" method="get" target="balda" style="float:left;" name="bwork">
		<input type="hidden" name="zkey" value="' . $uqkey . '">
		<input type="hidden" name="mode" value="2pdf">
		<input type="hidden" name="m" value="outputs">
		<input type="hidden" name="a" value="reports">
		<input type="hidden" name="rn" value="">
		<input type="hidden" name="suppressHeaders" value="1">
		<input type="hidden" name="2cols" value="#@cols@#">
		</form>
		<input type="submit" class="text" value="Save as PDF" onclick="document.bwork.mode.value=\'2pdf\'; document.bwork.submit();">
		<input type="submit" class="text" value="Save as HTML" onclick="getRT()">';
	$html = '
		<div style="background-color: #ffffff;border: 2px solid black; margin: 5px;padding: 10px;" id="paper" data-start="'.
			($sdvalid > 0 ? $sdate->format(FMT_DATE_MYSQL)  : 0 ).'" data-stop="'.
			($edvalid > 0 ? $edate->format(FMT_DATE_MYSQL) : 0).'"><h1><b>' . $title . '</b></h1>';
	if (!is_array($_SESSION['rnames'])) {
		$_SESSION['rnames'] = array();
	}
	if ($_GET['akill'] == 1 || $_GET['kadze'] == 'kami') {
		$html_pre = '';
	}
	$_SESSION['rnames'][$uqkey] = $title;
	/* $datePre = ' - ';
	if ($sdvalid > 0) {
		$html .= $datePre . $sdate->format($textFormat);
		$datePre = '';
	}
	if ($edvalid > 0) {
		$html .= $datePre . ' to ' . $edate->format($textFormat);
	} */

	//$thisCntr = getThisCenter();
	//$centers = centerList();
	$html .= "<br><table class='report' id='report_root' data-month='" . $sdate->getMonth() . "'
					data-year='" . $sdate->getYear() . "' data-center_name='" . strtolower($dPconfig['current_center']) . "'
					data-center_id='" . $thisCntr . "' data-dptmt='" . $rdata['rep_dept'] . "'
					data-report_id='".$rid."' data-report_name='".mysql_real_escape_string($rdata['title']) ."'>";
	$totalSections = count($glback['order']);
	$html .= '<tbody><tr>';
	$html_pre = str_replace("#@cols@#", $glback['second'], $html_pre);
	if (is_array($glback['columns'])) {
		
		foreach ($glback['columns'] as $columnList) {
			$html .= '<td style="width: 50%;vertical-align: top;">
		<table>
			<tbody><tr><td>';
			//foreach ($glback ['order'] as &$sec_id) {
			//var_dump($columnList);
			foreach ($columnList as $sec_id) {
				//foreach ($entries['sec'] as $sec_id => $svals) {
				$svals = $entries ['sec'] [$sec_id];
				$sec_rows = $entries ['rows'] [$sec_id];
				$colsCount = count($svals ['cols']);
				$rowsCount = count($sec_rows);
				/* echo '<pre>';
				var_dump($svals);
				echo '</pre>'; */
				$html .= "\n\n" . '<tr><td><b>' . $sblk . '&nbsp;' . $svals ['name'] . '</b><br></td></tr>' . "\n
				<tr>
				<!-- Start of section -->
				<td id='sec_" . $sec_id . "' class='sec_part'>";

				//here will be place for distinct attention to type of report value, but for now we're developing for plain cell value
				if (count($svals ['cols']) > 0) {
					$html .= '<table cellpadding="2" cellspacing="0" class="tbgrid" border="0">' . "\n\t" . '<tr><th>&nbsp;</th>';
					foreach ($svals ['cols'] as &$col_name) {
						$html .= '<th>' . (is_null($col_name) ? '&nbsp;' : $col_name) . '</th>';
					}
					$html .= "</tr>\n\n";
				}
				$id = 0;
				$new_row = true;

				if ($svals['type'] != 'text') {
					$rcid = $svals['content']; // $row_cells ['item'];
					//$rbdata = findRowItem($rcid, $glback, $entries);
					//if()
					//var_dump($rbdata);
					//echo $rcid;
					$html .= findItembyT($rcid);;
					//$html .= proceedReportItem($rbdata);
				} else {
					$html .= nl2br(stripslashes($svals ['content']));
				}

				// } // end of iteration over rows of cell
				$html .= "\n</td></tr><!-- End of section -->\n\n";
				updateLiveState($monitorKey, $sblk, $totalSections);
				++$sblk;
			} //end of section iteration in ORDER
			$html .= '</td></tr></tbody></table></td>';
		}
	}
	//walk through columns
	$html .= '</tr></tbody></table></div>';

	$fileTmp = $baseDir . '/files/tmp/' . $uqkey . '.rfs';
	$fh = fopen($fileTmp, 'a+');
	fputs($fh, '<html>
		<head>
		<style>
		.tbl {background: #a5cbf7;}
		.tbl th {background-color: #08245b ;color: #ffffff;font-size:8pt;list-style-type: disc;list-style-position: inside;border: outset #D1D1CD 1px ;font-weight: normal;text-align:center;}
		.tbl td {font-size:8pt;background-color:#fff;}
		.vdata,.summr{text-align: right;}
		.offwall{display :none ;}
		.vdata,.summr{text-align: right;}
		.report {font-weight: 500;margin-left: 20px;}
		.tbgrid {border: 1px solid #dfdfdf;}
		.tbgrid th,.rowhead {color: #4B4A4E; padding: 1px 3px;}
		.tbgrid td {border: 1px solid #dfdfdf;}
		</style>
		</head>
		<body>' . $html .
			'</body></html>');
	fclose($fh);
	echo $html_pre . $html;
}

function cleanFils($fils)
{
	$res = array();
	foreach ($fils as $d => &$vals) {
		if ($vals['state'] === true) {
			$res[$d] = $vals;
		}
	}
	return $res;
}

function getRealValue($item, &$list)
{
	foreach ($list as $sp) {
		if ($sp['v'] == $item) {
			return $sp['r'];
		}
	}
}

?>