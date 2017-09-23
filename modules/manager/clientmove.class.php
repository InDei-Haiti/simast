<?php

function buildList(){
	$q = new DBQuery();
	$q->addTable('clients','c');
	$q->addQuery('distinct client_adm_no, c.client_id, concat_ws(" ",client_first_name,client_other_name, client_last_name) as name,lf.ondate,lf.status,lf.clinic_id');
	$q->addWhere('client_status="7"'); // exclude transfered to ltp and deceased
	$q->addOrder('lf.id ASC,name ASC');
	$q->leftJoin('ltp_transfers', 'lf', 'c.client_id=lf.client_id');
	//$q->addWhere('lf.clinic_id is null ');
	$q->addWhere('lf.id > 0 ');
	//$q->addWhere('lf.clinic_id is null');
	// now select all transferred clients, the diff will be in color and UNDO action
	
	$list=$q->loadList();

	$clinicArray=centerList(TRUE);

	$ddown='<select name="cli[##CLNT##][center]" class="d2chk text">';
	foreach ($clinicArray as $key => $val) {
		if($key == 0){
			$dop=' selected="selected"';
		}else{
			$dop='';
		}
		$ddown.='<option value="'.$key.'" '.$dop.'>'.$val.'</option>';
	}
	$ddown.='</select>';
	$futures=0;

	$code='<form id="xlist" name="xlist" action="/?m=manager&mode=makecenters&suppressHeaders=1" method="post" onsubmit="return AIM.submit(this, {\'onStart\' : manager.forzip, \'onComplete\' : \'\'})">
			<input type="hidden" name="ekey">
		<table class="tbl tout tablesorter" cellpadding="2" cellspacing="1" id="clitab" width="100%">
	<thead><tr><th>ADM #</th><th>Name</th><th>Date</th><th>Center</th><th>Transfer Status</th></tr></thead><tbody>';
	if(count($list) > 0){
		foreach ($list as $row) {

			$q = new DBQuery();
			$q->addTable('social_visit');
			$q->addWhere('social_client_id="'.$row['client_id'].'"');
			$q->addWhere('social_client_status="7"');
			$q->addOrder('social_entry_date desc');
			$q->setLimit(1);
			$q->addQuery('social_entry_date');
			$ondate=$q->loadResult();

			$exported=((int)$row['status']  === 1);
			$link='<a href="/?m=clients&a=view&client_id='.$row['client_id'].'" class="'.($exported === true ? 'exported' : 'fresh_users').'">';
			$code.='<tr data-clinic="'.$row['clinic_id'].'" data-clid="'.$row['client_id'].'" class="'.($exported === true ? 'past' : 'future').'">
						<td >'.$link.$row['client_adm_no'].'</a></td>
						<td >'.$link.$row['name'].'</a></td>
						<td>'.$ondate.'<input type="hidden" name="cli['.$row['client_id'].'][ondate]" value="'.$ondate.'">
						<input type="hidden" name="cli['.$row['client_id'].'][status]" value="'.($exported === true ? '1' : '0').'">
						</td>						
						<td>'.
						( $exported === false ?
						str_replace('##CLNT##', $row['client_id'],$ddown )
						: $clinicArray[$row['clinic_id']]
						).
						'</td>
						<td>'.
						($exported === true ?
						'&nbsp;<span>DONE</span>&nbsp;<div class="fbutton undobutt" title="Undo"></div>'
						:
						'&nbsp;'
						)
						.'</td>
						</tr>'."\n";
						if($exported !== true){
							++$futures;
						}
		}
	}
	return $code.'</tbody></table><br>
		<input type="button" class="button" value="Submit" id="tabfinish" onclick="manager.checkDrops();" '.($futures === 0 ? 'disabled="disabled"' : '').'>
		</form><div id="sample_select" style="display:none;">'.$ddown.'</div>';
}

function checkLTPT(){
	$res='';
	$q = new DBQuery();
	$q->addQuery('lt.clinic_id,count(lt.id) as amount');
	$q->addTable('ltp_transfers','lt');
	$q->addWhere('lt.ondate is null');
	$q->addGroup('lt.clinic_id');
	$toexports=$q->loadList();
	$cents=array();
	if (count ( $toexports ) > 0) {
		$outh = array ();
		$clinarr = centerList ();
		foreach ( $toexports as $row ) {
			//$cents [$row ['clinic_id']] ++;
			$outh [] = array ('title' => $clinarr [$row['clinic_id']], 'cid' => $row['clinic_id'], 'amount' => $row['amount'] );
		}
		$res = json_encode ( $outh );
	}
	return $res;
}

function cancelClinic($clin_id,$olds=false){
	$sql='delete from ltp_transfers where clinic_id="'.mysql_real_escape_string($clin_id).'" and ondate '.($olds !== false ? '="'.mysql_real_escape_string($olds).'"' : ' is null');
	$res=mysql_query($sql);
	if($res){
		return 'ok';
	}
}

function getAllExports($clin_id){
	$sql='select count(distinct lt1.id) as amount ,
				lt2.ondate as edate, 
				lt2.clinic_id as clinic 
			from ltp_transfers lt1 
				left join ltp_transfers lt2 on lt1.clinic_id = lt2.clinic_id and lt2.ondate=lt1.ondate 
			where lt1.ondate is not null and lt2.clinic_id="'.$clin_id.'" and
				 lt1.clinic_id=lt2.clinic_id group by lt2.ondate';
	$res=mysql_query($sql);
	$html='';
	$zcnt=0;
	if($res){
		$zcnt=mysql_num_rows($res);
		$html='<table>';

		while ($dset = mysql_fetch_assoc($res)) {
			$html.='<tr><td>'.$dset['edate'].'</td><td><b>'.$dset['amount'].'</b>'.($dset['amount'] > 1 ? ' clients' : 'client').'</td><td><input type="button" class="text" onclick="exim.dateClean(this);" value="Cancel"></td></tr>';
		}
		$html.='</table>';
	}
	if($zcnt === 0){
		$html='<span>No clients transferred to the specified clinic</span>';
	}
	return $html;
}



function buildCFile(){
	$resh='fail';
	if(count($_POST['cli'] ) >= 1){
		//$pcents=json_decode ( str_replace ( '\\"', '"', $_POST['cparts'] ) ,true);
		$pcents=array();
		$centers=array();
		foreach ($_POST['cli'] as $key => $vals) {
			//$pcents[$key]=$val;
			//$clid=str_replace('cli_', '', $key);
			if(!array_key_exists($vals['center'], $centers)){
				$centers[$vals['center']]=array();
			}
			if($vals['status'] == '0'){
				$centers[$vals['center']][]=array('id'=>$key,'date'=>$vals['ondate'] ) ;
			}
		}

		if(count($centers) > 0){
			$outh=0;
			$clinarr= centerList();
			$query='update ltp_transfers set clinic_id="%d",ondate="%s",status="0" where client_id="%d"';
			foreach ($centers as $cid => $cusers) {
				//$outh[]=array('title'=>$clinarr[$cid],'cid'=>$cid,'amount'=>count($cusers));
				//$sql='insert into ltp_transfers (client_id, clinic_id,ondate,status) VALUES ';
				$sqlvs=array();
				foreach ($cusers as $cuser) {
					//$sqlvs[]='("'.$cuser['id'].'","'.$cid.'","'.$cuser['date'].'","0")';
					$sql=sprintf($query,$cid,$cuser['date'],$cuser['id']);
					db_exec($sql);
					++$outh;
				}
				//$sql.=join(',', $sqlvs);
				//$res=mysql_query($sql);				
			}
			if(count($outh) > 0){
				return $centers;
			}
		}
	}
}

function exportCs(){
	global $AppUI,$m;
	require_once $AppUI->getFileInModule($m, 'doexfile');
	exportProceed();
}

function importUsers(){
	if(count($_FILES) == 1){
		global $AppUI,$m;
		require_once $AppUI->getFileInModule($m, 'doimfile');
		importProceed();
	}
}
?>