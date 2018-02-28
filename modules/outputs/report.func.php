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
//echo "<pre>";
//var_dump($rdata);
//echo "</pre>";die();
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
//	echo "<pre>";
//	var_dump($glback);echo "</pre>";die();
	$sdvalid = $sdate->getYear();
	$edvalid = $edate->getYear();

	if(isset($_GET['rep'])){
		echo json_encode(array("1"=>"Toi"));return;
	}
	
//	echo '<pre>';
//	var_dump($entries);
//	echo '</pre>';die();
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
    <div id="editor"></div>
	<iframe style="display:none;" name="balda" src="about:blank;"></iframe>
		&nbsp;&nbsp;<!--<input type="button" class="text button ce pi ahr" style="background-color: rgba(255, 255, 255, 0.75);" value="Pop out" onclick="popTable(\'paper\');">-->&nbsp;&nbsp;
		<form action="index.php" method="get" target="balda" style="float:left;" name="bwork">
		<input type="hidden" name="zkey" value="' . $uqkey . '">
		<input type="hidden" name="mode" value="2pdf">
		<input type="hidden" name="m" value="outputs">
		<input type="hidden" name="a" value="reports">
		<input type="hidden" name="rn" value="">
		<input type="hidden" name="suppressHeaders" value="1">
		<input type="hidden" name="2cols" value="#@cols@#">
		</form>
		<input type="submit" class="text button ce pi ahr no-print" style="background-color: rgba(255, 255, 255, 0.75);" value="Save as PDF" onclick="Printer();">
		<!--<input type="submit" class="text button ce pi ahr" style="background-color: rgba(255, 255, 255, 0.75);" value="Save as PDF" id="cmd">-->
		<!--<a href="javascript:genPDF()" class="text button ce pi ahr" style="background-color: rgba(255, 255, 255, 0.75);">Save as PDF</a>-->
		<input type="submit" class="text button ce pi ahr no-print" style="background-color: rgba(255, 255, 255, 0.75);" value="Save as HTML" onclick="getRT()">';
	$html = '
		<div id="content" style="background-color: #ffffff;border: 0px solid black; margin: 5px;padding: 10px;" id="paper" data-start="'.
			($sdvalid > 0 ? $sdate->format(FMT_DATE_MYSQL)  : 0 ).'" data-stop="'.
			($edvalid > 0 ? $edate->format(FMT_DATE_MYSQL) : 0).'"><h1><b></b></h1>';//$title
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
//	echo "<pre>";
//	var_dump($glback['columns']);die();
//	echo "</pre>";
	if (is_array($glback['columns'])) {
		
		foreach ($glback['columns'] as $columnList) {
			$html .= '<td style="width: 50%;vertical-align: top;margin-bottom:40px;">
		<table>
			<tbody><tr><td>';
			//foreach ($glback ['order'] as &$sec_id) {
			//var_dump($columnList);
			foreach ($columnList as $sec_id) {
//				echo "<pre>";
//				var_dump($sec_id);
//				echo "</pre>";
				//foreach ($entries['sec'] as $sec_id => $svals) {
				$svals = $entries ['sec'] [$sec_id];
				$sec_rows = $entries ['rows'] [$sec_id];
				$colsCount = count($svals ['cols']);
				$rowsCount = count($sec_rows);
//				 echo '<pre>';
//				var_dump($svals);
//				echo '</pre>';die();
//				$html .= "\n\n" . '<tr><td><b>' . /*$sblk . '&nbsp;' . */$svals ['name'] . '</b><br></td></tr>' . "\n
				$html .= "
				<tr>
				<!-- Start of section -->
				<td id='sec_" . $sec_id . "' class='sec_part' style='padding:15px;'>";

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
				$html .= "</td></tr><!-- End of section -->\n\n";
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
		/*.tbl thead th {
		  vertical-align: bottom;
		  border-bottom: 2px solid #eceeef;
		}
		.tbl thead th {
		  color: #737171 !important;
		  font-weight: bold !important;
		  padding-top: 10px;
		  padding-bottom: 10px;
		}
		
		.tbl td, .tbl th {
		  padding: .75rem;
		  vertical-align: top;
		  border:  1px solid  #eceeef !important;
		  font-family: "Roboto", "Helvetica Neue", Helvetica, Arial, sans-serif;
		  font-size: 1.3rem;
		  font-weight: 300;
		  line-height: 1.5;
		  color: #000;
		  font-weight: 300;
		  letter-spacing: 0;
		}
		
		.tbl tr:hover {
		  background:rgba(0,0,0,.1) !important;
		  border-bottom:1px solid black !important;
		  border-top: 1px solid black !important;
		}*/
		/*.tbl {background: #a5cbf7;}
		.tbl th {background-color: #08245b ;color: #ffffff;font-size:8pt;list-style-type: disc;list-style-position: inside;border: outset #D1D1CD 1px ;font-weight: normal;text-align:center;}
		.tbl td {font-size:8pt;background-color:#fff;}*/
		.tbl { /*background: #a5cbf7;*/    
		border-collapse: collapse;}
		.tbl th {color: #000;font-size:8pt;list-style-type: disc;list-style-position: inside;border:  1px solid  #eceeef;font-weight: normal;text-align:center;}
		.tbl td { border:  1px solid  #eceeef;font-size:8pt;background-color:#fff;}
		.vdata,.summr{text-align: right; font-weight: 300;}
		.offwall{display :none ;}
		.vdata,.summr{text-align: right;font-weight: 300;}
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