<?php
global $AppUI, $baseDir;
//require_once ($baseDir . '/modules/outputs/php_json.class.php');

function findItembyT($t){
	$sql = 'select * from report_items';
	$res = mysql_query($sql);
	$items = array();
	if (is_resource($res)) {
		while ($row = mysql_fetch_assoc($res)) {
			$datarow = json_decode($row['idata'],true);
			//echo '<pre>';
			//var_dump(['t']);
			//echo '</pre>';
			/*if (!is_array($items[$row['itype']])) {
				$items[$row['itype']] = array();
			}
			$items[$row['itype']][$row['id']] = $row['idata'];*/
			if($t==$datarow['t']){
				return $row['html'];
			}
		}
	}
}

function findRowItem($rt, &$glback, &$entries)
{
	//var_dump($glback);
	$rsaved = $glback['bdata'];
	$rt = intval($rt);
	foreach ($rsaved as $rid => $rvals) {
		if (intval($rvals['t']) === $rt) {
			return $rvals;
		}
	}
	//return $glback['bdata'][$rt];
}

function utf8_urldecode($str)
{
	$str = preg_replace("/%u([0-9a-f]{3,4})/i", "&#x\\1;", urldecode($str));
	return html_entity_decode($str, null, 'UTF-8');
}


$textFormat = "%b %e, %Y"; //"j F Y";

$df = $AppUI->getPref('SHDATEFORMAT');

if ($_POST ['mode'] == 'save' || $_POST ['mode'] == 'update') {

	$allData = magic_json_decode($_POST['bps']);

	$fdata = $allData['entries'];
	$bdata['bdata'] = $allData['bdata'];
	$bdata['order'] = $allData['order'];
	$bdata['rows'] = $allData['rows'];
	$bdata['types'] = $allData['types'];
	$bdata['second'] = $allData['second'];
	$bdata['columns'] = $allData['columns'];
	$sdate = '';
	$edate = '';
	if (strlen($allData ['start']) > 8) {
		$tdate = new CDate ($allData ['start']);
		$sdate = $tdate->format(FMT_DATE_MYSQL);
	}
	if (strlen($allData ['end']) > 8) {
		$tdate = new CDate ($allData ['end']);
		$edate = $tdate->format(FMT_DATE_MYSQL);
	}
	$zmode = $_POST ['mode'];
	$zid = ( int )$_POST ['indb'];
	if ($zmode == 'save') {
		$stmpl = 'insert into reports (title,rep_desc,rep_dept,start_date,end_date,entries,backdoor) values ("%s","%s","%d","%s","%s","%s","%s")';
	} elseif ($zmode == 'update' && (isset ($_POST ['indb']) && $zid > 0)) {
		$stmpl = 'update reports set title="%s",rep_desc="%s",rep_dept="%d", start_date="%s", end_date="%s", entries="%s", backdoor="%s" where id="' . $zid . '"';
	}
	$rep_name = $fdata ['rep_name'];
	unset ($fdata ['rep_end'], $fdata ['rep_start'], $fdata ['rep_name']);
	$sql = sprintf($stmpl,
		mysql_real_escape_string($rep_name),
		mysql_real_escape_string($fdata['rep_desc']),
		intval($fdata['rep_dept']),
		$sdate,
		$edate,
		mysql_real_escape_string(gzencode(var_export($fdata, true), 9, FORCE_GZIP)),
		mysql_real_escape_string(gzencode(var_export($bdata, true), 9, FORCE_GZIP))
	);
	$res = mysql_query($sql);
	if ($res) {
		$rtext = mysql_insert_id();
	} else {
		$rtext = 'fail';
	}
	echo $zid > 0 ? $zid : $rtext;
	return;
} else
	switch ($_GET['mode']) {
		//if ($_GET ['mode'] == 'loadinfo') {
		case 'loadinfo':
			require_once $AppUI->getSystemClass('systemImport');
			$rid = ( int )($_GET ['dbrid']);
			$df = $AppUI->getPref('SHDATEFORMAT');
			$q = new DBQuery ();
			$q->addQuery('backdoor');
			$q->addTable('reports');
			$q->addWhere('id="' . $rid . '"');
			$datab = $q->loadResult();
			$sql = 'select title,rep_desc,end_date,start_date,entries from reports where id="' . $rid . '"';
			$res = mysql_query($sql);
			if ($res) {
				$datas = mysql_fetch_assoc($res);
				//echo $datab;
				eval ('$bdd=' . gzdecode($datab) . ';');
				eval ('$bde=' . gzdecode($datas ["entries"]) . ';');
				$datas ["backdoor"] = $bdd;
				$datas ["entries"] = $bde;
				$sdate = new CDate ($datas ["start_date"]);
				$edate = new CDate ($datas ["end_date"]);
				$datas ['start_date'] = ($sdate->getYear() > 0 ? $sdate->format($df) : '');
				$datas ['end_date'] = ($edate->getYear() > 0 ? $edate->format($df) : '');
				echo json_encode($datas);
				return;
			}
			break;

        case 'entries':
            require_once $AppUI->getSystemClass('systemImport');
            $rid = ( int )($_GET ['dbrid']);
            $df = $AppUI->getPref('SHDATEFORMAT');
            $q = new DBQuery ();
            $q->addQuery('backdoor');
            $q->addTable('reports');
            $q->addWhere('id="' . $rid . '"');
            $datab = $q->loadResult();
            $sql = 'select entries from reports where id="' . $rid . '"';
            $res = mysql_query($sql);
            if ($res) {
                $datas = mysql_fetch_assoc($res);
                //echo $datab;
                //eval ('$bdd=' . gzdecode($datab) . ';');
                eval ('$bde=' . gzdecode($datas ["entries"]) . ';');
                //$datas ["backdoor"] = $bdd;
                $datas ["entries"] = $bde;
                echo '<pre>';
                var_dump($datas ["entries"]);
                echo '</pre>';
                /*$sdate = new CDate ($datas ["start_date"]);
                $edate = new CDate ($datas ["end_date"]);
                $datas ['start_date'] = ($sdate->getYear() > 0 ? $sdate->format($df) : '');
                $datas ['end_date'] = ($edate->getYear() > 0 ? $edate->format($df) : '');*/
                //echo json_encode($datas);
                return;
            }
            break;

		case '2pdf':
			$zkey = trim($_GET['zkey']);
			$savedName = $_SESSION['rnames'][$zkey];
			if (!$savedName) {
				$savedName = 'report';
			}
			$savedName = str_replace(" ", '_', $savedName);

			require_once($baseDir . '/lib/mpdf/mpdf.php');

			if ((int)$_GET['2cols'] === 1) {
				$dompdf = new mPDF('utf-8', '', 0, '', 3, 3, 4, 4, 2, 2, 'L'); // ('utf-8', 'A4-L'); //let 2 column layout will be in landscape
			} else {
				$dompdf = new mPDF();
			}
//			echo "<script>alert('Je suis')</script>";
			$dompdf->WriteHTML(file_get_contents($baseDir . '/files/tmp/' . $zkey . '.rfs'));
			//echo $savedName;
//			echo "<script>alert('Merde')</script>";
			$pdfPath = $baseDir . '/files/tmp' . $savedName . ".pdf";


//			echo "<script>alert('".$savedName."+Le Test')</script>";
			$dompdf->Output($pdfPath ,'D');
			$dompdf->Output($savedName . '.pdf', 'D');
			//printForSaveFromFile('application/pdf',$pdfPath,$savedName.'.pdf');
			return;
			break;
		case '2html':
			$zkey = trim($_GET['zkey']);
			$savedName = $_SESSION['rnames'][$zkey];
			if (!$savedName) {
				$savedName = 'report';
			}
            if(trim($_GET['rn']) != ''){
                $savedName = trim($_GET['rn']);
                $savedName = str_replace(" ","_",$savedName);
            }
			$savedName = str_replace(" ", '_', $savedName);
			printForSave(file_get_contents($baseDir . '/files/tmp/' . $zkey . '.rfs'), 'text/html', $savedName . '.html');
			return;
			break;
		case 'compile':
			echo "<br /><br />";
			if (( int )($_GET ['itid']) > 0) {
				buildTableDataDemand();
				$cid = intval($_GET['itid']);
				$currentKey = uniqid();
				$onceShown = ($_GET['kadze'] == 'kami');
				?>
			<style type="text/css">
				span table thead th {
					vertical-align: bottom;
					border-bottom: 2px solid #eceeef;
				}
				span table thead th {
					color: #737171 !important;
					/*background-color: #222 !important;*/
					font-weight: bold !important;
					padding-top: 10px;
					padding-bottom: 10px;
				}

				span table td, mtab table th {
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

				span table tr:hover {
					background:rgba(0,0,0,.1) !important;
					border-bottom:1px solid black !important;
					border-top: 1px solid black !important;
				}

			</style>
			<div id="load_res">Loading&nbsp;<span style="font-weight: 800;" id="pcent">0</span><b>%</b>...</div>
			<script type="text/javaScript">
				window.onload = up;
				function up() {
					monitorPs("<?php echo $currentKey?>", "pcent");
					$j.post("/?m=outputs&a=reports&suppressHeaders=1&mode=wfrm&ds=<?php echo $_GET['ds']?>&de=<?php echo $_GET['de']?>&itid=<?php echo $cid?>&urkey=<?php echo $currentKey . ($onceShown === true ? "&akill=1" : "") ?>", function (dh) {
						$j("#load_res").replaceWith(dh);
					});
				}
			</script>
			<?php

				return;
			}
			break;
		case 'wfrm':
			if (( int )($_GET ['itid']) > 0) {
				$cid = intval($_GET['itid']);
				buildTableDataDemand();
				require_once('report.func.php');
				//if($_GET['akill'] == 1){ /// :))))
				if ($_GET['kadze'] == 'kami') {
					$sql = 'delete from reports where id ="' . $cid . '"';
					$killres = mysql_query($sql);
				}
			}
			break;
		case 'save_item':
			$new_id = 'fail';
			$idata = magic_json_decode($_POST['itemfo']);
			/************************************************/
			// Cleaning saved list of rows visible/hidden in order to reduce size of future json string.
			/************************************************/
			$sdata = magic_json_decode($_POST['sddata']);
			$sdata['list'] = array();
			$idata['tbsdata'] = $sdata;
			//var_dump($_POST['html']);
			if($_POST['html']==='table'){
				$html = '<span style="background-color: inherit;" id="tthome">';
				$html .= file_get_contents($baseDir . '/files/tmp/' . $_SESSION ['fileNameCshBack'] . '.tss');
				$html .= '</span>';
			}elseif ($_POST['html']==='img'){
			    //var_dump($idata);
			    $filteredData = substr($_POST['imgBase64'],strpos($_POST['imgBase64'],",")+1);
			    $unencodedData = base64_decode($filteredData);
			    file_put_contents($baseDir . '/files/reports/' . $_SESSION ['fileNameCshBack'].'.png',$unencodedData);
                $html = '<img src="/files/reports/' . $_SESSION ['fileNameCshBack'].'.png" class="grer" data-rep_item="graph">';
			    /*$fh=fopen($_SESSION ['fileNameCshfBack'],'r');
				$blob = base64_encode(fread($fh,filesize($_SESSION ['fileNameCshfBack'])));
				$html = '<img src="data:image/png;base64,'.$blob.'" class="grer" data-rep_item="graph">';*/
			}elseif ($_POST['html']==='maps'){
                //var_dump($idata);
                $filteredData = substr($_POST['imgBase64'],strpos($_POST['imgBase64'],",")+1);
                $unencodedData = base64_decode($filteredData);
                file_put_contents($baseDir . '/files/reports/' . $_SESSION ['fileNameCshBack'].'.png',$unencodedData);
                $html = '<img src="/files/reports/' . $_SESSION ['fileNameCshBack'].'.png" class="grer" data-rep_item="graph">';
                /*$fh=fopen($_SESSION ['fileNameCshfBack'],'r');
                $blob = base64_encode(fread($fh,filesize($_SESSION ['fileNameCshfBack'])));
                $html = '<img src="data:image/png;base64,'.$blob.'" class="grer" data-rep_item="graph">';*/
            }
			
			//$_SESSION ['fileNameCshfBack']
			//var_dump($_POST);
            $html = mysql_real_escape_string($html);
			$sql = 'insert into report_items (title,itype,idata,html) values ("%s","%s","%s",\'%s\')';
			$res = mysql_query(sprintf($sql, $idata['n'], $idata['c'], mysql_real_escape_string(json_encode($idata)),$html));
			if ($res) {
				$new_id = mysql_insert_id();
			}
			echo mysql_errno().':'.mysql_error();
			echo $new_id;
			break;
		case 'get_item_list':
			$sql = 'select * from report_items order by itype';
			$res = mysql_query($sql);
			$items = array();
			if (is_resource($res)) {
				while ($row = mysql_fetch_assoc($res)) {
					if (!is_array($items[$row['itype']])) {
						$items[$row['itype']] = array();
					}
					$items[$row['itype']][$row['id']] = $row['idata'];
				}
			}
			echo json_encode($items);
			break;
		case 'item_view':
			$itemID = intval($_GET['iid']);
			if ($itemID > 0) {
				$sql = 'select idata from report_items where id="%d"';
				$res = mysql_query(sprintf($sql, $itemID) );
				if ($res) {
					$irdata = mysql_fetch_row($res);
                    //var_dump($irdata);
					//$irdata = magic_json_decode($irdata[0], true);
                    $irdata = json_decode($irdata[0], true);
					$selectedCase = $irdata ['c'];
					if($selectedCase==='stat' || $selectedCase==='graph'){
						$sql = 'select html from report_items where id="%d"';
						$res = mysql_query(sprintf($sql, $itemID) );
						if ($res) {
							$html = mysql_fetch_row($res);
							echo $html[0];
						}
						
					}else
						echo proceedReportItem($irdata);
				}
			}
			return;
			break;
		case 'item_kill':
			$pok = 'fail';
			if (intval($_GET['iid']) > 0) {
				$sql = 'delete from report_items where id="%d"';
				$res = mysql_query(sprintf($sql, intval($_GET['iid'])));
				if ($res) {
					$pok = 'ok';
				}
			}
			echo $pok;
			break;
		case 'item_import':
			break;
		case 'updated':
			$q = new DBQuery ();
			$q->addTable('reports');
			$q->addWhere('id ="' . $_POST['idRep'] . '"');
//			$q->addWhere('id="174"');
			$q->setLimit(1);
			$rdata = $q->loadList();
//			echo json_encode($_POST['idRep']);return;
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

				echo json_encode(array("titre"=>$title,
					"start_date"=>$rdata['start_date'],
					"end_date"=>$rdata['end_date'],
					"back"=>$glback,
					"entries"=>$entries));
				return;
			}

			echo "Fails";
			return;
			break;
		default:
			break;
//}elseif($_GET['mode'] === 'item_import'){}
	}
?>