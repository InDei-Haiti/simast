<?php
/**
 * Created by JetBrains PhpStorm.
 * User: stig
 * Date: 28.04.12
 * Time: 14:34
 */

require_once $AppUI->getSystemClass('systemImport');

$xmode = $_POST['xmode'];
$result = '';
switch ($xmode){
	case 'reports':
		$sql = 'select * from reports where id IN (select distinct rtpl from center_reports)';
		$res = mysql_query($sql);
		if($res){
			$replist = array("-1" => 'Select report template');
			while($row = mysql_fetch_assoc($res)){
				$replist[$row['id']] = $row['title'];
			}
			$result = arraySelect($replist,'rep_id','id="rep_selected" class="text psels"',-1);
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
		$snames=array("-1"=>'Select report section');
		if (count($rdata) > 0) {
			eval ('$glback=' . gzdecode($rdata ["backdoor"]) . ';');
			eval ('$entries=' . gzdecode($rdata ["entries"]) . ';');
			foreach($entries['sec'] as $sid => $sdata){
				$snames[$sid]=$sdata['name'];
			}

			$result = arraySelect($snames,'rep_items',"id='rep_sitems' class='text psels'",-1);

		}
		break;
	case 'crdata':
		$tpl = intval($_GET['rpid']);
		$sec = intval($_GET['rsid']);

		$sql = 'select ci.content, cr.ryear, cr.rmon, cr.center from cr_items ci,  center_reports cr
				where rtpl="%d" and ci.crid = cr.id and ci.section_id="%d" order by rmon, ryear';
		$res = mysql_query(sprintf($sql,$tpl,$sec));

		$centers = centerList();
		$html='';
		$result = '<ul class="mute-list">';
		if($res){
			while($rdata = mysql_fetch_assoc($res)){
				$html.='<li class="cr_item"><span>'. $monthNames[($rdata['rmon']-1)].' / '.$rdata['ryear'].' '.$centers[$rdata['center']].'</span>';
				$html.='<div class="sec_cont">'.gzdecode($rdata['content']).'</div>';
				$html.='</li>';
			}
		}
		$result.=$html.'</ul>';

		break;
	default:
		break;
}

echo $result;
