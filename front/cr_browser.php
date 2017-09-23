<?php
/**
 * Created by JetBrains PhpStorm.
 * User: stig
 * Date: 28.04.12
 * Time: 14:34
 */

global $baseDir;
require_once $AppUI->getSystemClass('systemImport');

function templates(){
	$tpls = array();
	$sql= 'select id,title from reports order by ID';
	$res = mysql_query($sql);
	while($row = mysql_fetch_assoc($res)){
		$tpls[$row['id']]=$row['title'];
	}
	return $tpls;
}

$xmode = $_GET['xmode'];
if(!isset($xmode)){
	$xmode = $_POST['xmode'];
}
$result = '';
switch ($xmode) {
	case 'reports':
		$sql = 'select * from reports where id IN (select distinct rtpl from center_reports)';
		$res = mysql_query($sql);
		if ($res) {
			$replist = array("-1" => 'Select report template');
			while ($row = mysql_fetch_assoc($res)) {
				$replist[$row['id']] = $row['title'];
			}
			$result = arraySelect($replist, 'rep_id', 'id="rep_selected" class="text psels"', -1);
		}
		break;
	case 'report-items':

		$rid = intval($_GET['rpid']);
		$q = new DBQuery ();
		$q->addTable('reports');
		$q->addWhere('id="' . $rid . '"');
		$q->setLimit(1);
		$rdata = $q->loadList();
		$rdata = $rdata [0];
		$entries = array();
		$snames = array("-1" => 'Select report section');
		if (count($rdata) > 0) {
			eval ('$glback=' . gzdecode($rdata ["backdoor"]) . ';');
			eval ('$entries=' . gzdecode($rdata ["entries"]) . ';');
			foreach ($entries['sec'] as $sid => $sdata) {
				$snames[$sid] = $sdata['name'];
			}

			$result = arraySelect($snames, 'rep_items', "id='rep_sitems' class='text psels'", -1);

		}
		break;
	case 'crdata_search':
		$tpl = intval($_POST['rpid']);
		$sec = intval($_POST['rsid']);
		$qmode = $_POST['rtype'];
		$rparams = magic_json_decode($_POST['vals']);

		//inside rparams we have limits - array of date limits


		if ($qmode === 'parts') {
			$sql = 'select ci.content, cr.ryear, cr.rmon, cr.center from cr_items ci,  center_reports cr
				where rtpl="%d" and ci.crid = cr.id and ci.section_id="%d" order by rmon, ryear';
			$res = mysql_query(sprintf($sql, $tpl, $sec));

			$centers = centerList();
			$html = '';
			$result = '<ul class="mute-list">';
			if ($res) {
				while ($rdata = mysql_fetch_assoc($res)) {
					$html .= '<li class="cr_item">
						<div class="bcenter">' . $monthNames[($rdata['rmon'] - 1)] . ' / ' . $rdata['ryear'] . ' ' . $centers[$rdata['center']] . '</div>
						<div class="icns bkill"></div>
						<div class="sec_cont">' . gzdecode($rdata['content']) . '
						</div>
					</li>';
				}
			}
			$result .= $html . '</ul>';
		}
		elseif ($qmode === 'complete') {
			$where = array();
			if ($rparams['cntr'] > 0) {
				$where[] = ' center ="' . $rparams['cntr'] . '"';
			}
			if ($rparams['dept'] > 1) {
				$where[] = ' rdept ="' . $rparams['dept'] . '"';
			}
			//$sql = 'select center, rfile from center_reports where rmon="%d" and ryear="%d" %s';

			$startDs = $rparams['limits']['start'];
			if($startDs['year'] != '-1' && $startDs['mon'] != '-1'){
				$pointDate = $startDs['year'] . '-' . $startDs['mon'] . '-01';
				$pres = "(
				          (start IS NOT NULL
				                  AND (
				                      start <= '@PDDP@' OR
				                      start <= DATE_SUB((DATE_ADD('@PDDP@', INTERVAL 1 MONTH)) , INTERVAL 1 DAY)
				                  )
				           )
				                  AND
				           (stop IS NOT NULL and (
				                 stop >=  '@PDDP@' OR
				                 stop >=  DATE_SUB((DATE_ADD('@PDDP@', INTERVAL 1 MONTH)) , INTERVAL 1 DAY)
				                )
				           )
 			    )";

				$pres = str_replace("@PDDP@", $pointDate, $pres);
				$where[] = $pres;
				$pres = null;
			}


			$sql = "SELECT * FROM center_reports where visible='1'";

			if(count($where) > 0){
				$sql.= ' AND '.join(" AND ",$where);
			}
			//$reps = mysql_query(sprintf($sql, $rparams['mon'], $rparams['year'], (count($where) > 0 ? ' AND ' . join(' AND ', $where) : '')));
			$reps = mysql_query($sql);
			$centers = centerList();
			$tpls = templates();
			$pre_res=array();
			if (is_resource($reps) && mysql_num_rows($reps) > 0) {
				while ($rf = mysql_fetch_assoc($reps)) {
					/*$pre_res[] = '<li class="cr_item">'.
							'<div class="bcenter">'.$centers[$rf['center']].'</div>
							<div class="bkill icns" title="Delete"></div>
							<div class="sec_cont">'.
							file_get_contents($baseDir.'/files/reports/'.$rf['rfile']).
							'</div></li>';*/
					//verbose version of reponse
					/*$pre_res[] = array(
						'id'=>$rf['id'],
						'center'=>$centers[$rf['center']],
						'tpl'=>$tpls[$rf['rtpl']],
						'sdate'=>printDate($rf['start']),
						'edate' => printDate($rf['stop'])
					);*/
					//mutual with only Ids
					$pre_res[]=$rf['id'];
				}
			}
			$result = json_encode($pre_res);
		}

		break;
	case 'crdata_load':
		/*$tpl = intval($_POST['rpid']);
		$sec = intval($_POST['rsid']);*/
		$qmode = $_POST['rtype'];
		$tagsMode = $_GET['wrap'];
		$rparams = magic_json_decode($_POST['vals']);

		//inside rparams we have limits - array of date limits


		if ($qmode === 'parts') {
			$sql = 'select ci.content, cr.ryear, cr.rmon, cr.center from cr_items ci,  center_reports cr
				where rtpl="%d" and ci.crid = cr.id and ci.section_id="%d" order by rmon, ryear';
			$res = mysql_query(sprintf($sql, $tpl, $sec));

			$centers = centerList();
			$html = '';
			$result = '<ul class="mute-list">';
			if ($res) {
				while ($rdata = mysql_fetch_assoc($res)) {
					$html .= '<li class="cr_item">
						<div class="bcenter">' .
							$monthNames[($rdata['rmon'] - 1)] . ' / ' . $rdata['ryear'] . ' ' . $centers[$rdata['center']] .
						'</div>
						<div class="icns bkill"></div>
						<div class="sec_cont">' .
							gzdecode($rdata['content']) . '
						</div>
					</li>';
				}
			}
			$result .= $html . '</ul>';
		}
		elseif ($qmode === 'complete') {
			$where = array();

			$sql = "SELECT center, rfile FROM center_reports ";

			if (count($rparams) > 0) {
				$sql .= ' where id IN (' . join(",", $rparams).')';
			}
			$reps = mysql_query($sql);
			$centers = centerList();
			$pre_res = array("<ul class='mute-list'>");
			if($tagsMode == 1){
				$pre_res= array();
			}
			if (is_resource($reps) && mysql_num_rows($reps) > 0) {
				while ($rf = mysql_fetch_assoc($reps)) {
					if($tagsMode == 1){
						$pre_res[] = '<li class="cr_item">' .
								'<div class="bcenter">' . $centers[$rf['center']] . '</div>
							<div class="bkill icns" title="Delete"></div>
							<div class="sec_cont">' .
								file_get_contents($baseDir . '/files/reports/' . $rf['rfile']) .
								'</div></li>';
					}else{
						$pre_res[] = file_get_contents($baseDir . '/files/reports/' . $rf['rfile']);
					}

				}
			}
			if($tagsMode == 1){
				$result = (count($pre_res) > 1 ? join("", $pre_res) . '</ul>' : '');
			}else{
				$result = join("", $pre_res);
			}
		}

		break;
	case 'get_report_body':
		$rep_id = (int)$_GET['rep_id'];

		$sql = 'select rfile from center_reports where id="%d"';
		$res = mysql_query(sprintf($sql,$rep_id));
		if($res){
			$frow = mysql_fetch_assoc($res);
			$fname = $frow['rfile'];
            $filePath = $baseDir . '/files/reports/' . $fname;
            if(is_file($filePath) && is_readable($filePath)){
                $fh = fopen($filePath,'r');
                fpassthru($fh);
                fclose($fh);
            }
		}
		break;
	default:
		break;
}

echo $result;
