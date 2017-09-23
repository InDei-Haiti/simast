<?php
require_once $AppUI->getSystemClass('systemImport');
$newID=0;

$firstPlan=array(
'counselling_visit'=>array('client'=>'counselling_client_id','id'=>'counselling_id','center'=>'counselling_center_id'),
'nutrition_visit'=>array('client'=>'nutrition_client_id','id'=>'nutrition_id','center'=>'nutrition_center'),
'clinical_visits'=>array('client'=>'clinical_client_id','id'=>'clinical_id','center'=>'clinical_clinic_id'),
'chw_info'=>array('client'=>'chw_client_id','id'=>'chw_id','center'=>'chw_center_id'),
'cbc_info'=>array('client'=>'cbc_client_id','id'=>'cbc_id','center'=>'cbc_center_id'),
'mortality_info'=>array('client'=>'mortality_client_id','id'=>'mortality_id','center'=>'mortality_clinic_id'),
'admission_caregivers'=>array('client'=>'client_id','id'=>'id','center'=>false),
'household_info'=>array('client'=>'household_client_id','id'=>'household_id','center'=>false),
'medical_assessment'=>array('client'=>'medical_client_id','id'=>'medical_id','center'=>'medical_clinic_id'),
'status_client'=>array('client'=>'social_client_id','id'=>'id','center'=>false)
);


function proceedTable($arr,$name,$off,$newvals=array(),$deep = false){
	$newTabIDs=array();
	$qur=array('heads'=>array(),'vals'=>array());
	if(!is_array($arr[$name])){
		return FALSE;
	}
	foreach ($arr[$name] as $key => $val) {
		if(is_numeric($key) && is_array($val) && count($val) > 0){
			$newTabIDs[$val[$off[0]]]=proceedTable(array($name=>$val),$name,$off,$newvals,true);
		}else{
			if($off !== false && !in_array($key,$off) && !is_null($val)){
				$qur['heads'][]=$key;
				if(array_key_exists($key,$newvals)){
					if(!is_array($newvals[$key])){
						$val=$newvals[$key];
					}else{
						$val=$newvals[$key][$val];
					}
				}
				$qur['vals'][]='"'.$val.'"';

			}
		}
	}
	if(count($qur['heads']) > 0){
		$sql='insert into '.$name.' ('.join(',',$qur['heads']).') values ('.join(',', $qur['vals']).')';
		$res=mysql_query($sql);
		if($res){
			$newtID=mysql_insert_id();
		}
	}
	return $deep === true ? $newtID : $newTabIDs;
}

function importProceed() {
	global $newID,$newTabIDs,$firstPlan;
	$imported=0;


	$res = 'fail';
	/*if (is_uploaded_file ( $_FILES ['ncomes'] ['tmp_name'] ) && $_FILES ['ncomes'] ['error'] == 0) {
	$fpath = $_FILES ['ncomes'] ['tmp_name'];
	$news = file_get_contents ( $fpath );
	if (strlen ( $news ) > 0) {
	$usersEn = preg_split( '/===###===/', $news,-1,PREG_SPLIT_NO_EMPTY );
	unset ( $news );
	if (count ( $usersEn ) > 1) {
	$imported = 0;
	foreach ( $usersEn as $iid => $uencode ) {
	$bat = gzdecode ( $uencode );
	$udata = unserialize ( $bat );
	unset ( $bat, $uencode );
	if ($iid === 0) {
	$oldCenters = $udata;
	foreach ( $oldCenters as $cid => $cname ) {
	if (is_numeric ( $cid )) {
	$q = new DBQuery ();
	$q->addTable ( 'clinics' );
	$q->addWhere ( 'lower(clinic_name) = "' . strtolower ( $cname ) . '"' );
	$q->setLimit ( 1 );
	$q->addQuery ( 'clinic_id' );
	$newcid = $q->loadResult ();
	if (! is_numeric ( $newcid )) {
	$sql = 'insert into clinics  (clinic_name,clinic_owner) values ("' . $cname . '","1")';
	db_exec ( $sql );
	$newcid = db_insert_id ();
	}
	$newCenters [$cid] = $newcid;
	}else{
	$target=$cname;
	}
	}
	$q=new DBQuery();
	$q->addTable('config');
	$q->addWhere('config_name="current_center"');
	$q->addWhere('lower(config_value) = "'.strtolower($target).'"');
	$q->addQuery("1");
	$res=$q->loadResult();
	if($res != '1'){
	echo 'wrong_center';
	return false;
	}

	} else {*/

	$q = new DBQuery();
	$q->addQuery('id,client_real_data');
	$q->addTable('ltp_inbox');
	$q->addWhere('ltp_status="pending"');
	$incomes=$q->loadList();
	$target=getThisCenter();
	foreach ($incomes as $new_user) {
		$row_id=$new_user['id'];
		$bdata=unserialize(gzdecode($new_user['client_real_data']));
		$udata=unserialize($bdata[1]);
		$newCenters=$bdata[0];
		unset($bdata,$new_user);



		//1.Insert into client's table, first of all we need to check existence of same adm_no
		$sql = 'select 1  from clients where client_adm_no="' . $udata ['clients'] ['client_adm_no'] . '" limit 1';
		$res = mysql_query ( $sql );
		if ($res && mysql_num_rows ( $res ) == 0) {
			//Fine, we haven't found such adm_no
			//1a. Insert client into table and get new client ID.
			$clientID = proceedTable (
			&$udata, 'clients', array ('client_id' ),
			array ('client_status'=>'1','client_center'=>$newCenters[$target]), true
			);
			if (is_numeric ( $clientID ) && $clientID > 0) {
				//2.We have new valid client id and can proceed with all other info from other tables
				foreach ( $firstPlan as $table => $clid ) {
					$newTabIDs = array ();
					if (is_array ( $udata [$table] ) && count ( $udata [$table] ) > 0) {
						$newTabIDs = proceedTable ( &$udata, $table, array ($clid ['id'] ),
						array ($clid ['client'] => $clientID,
						$clid ['center'] => ($clid ['center'] !== false ? $newCenters  : array())
						)
						);

						switch ($table) {
							case 'nutrition_visit' :
								if (count ( $newTabIDs ) > 0) {
									proceedTable ( &$udata, 'nutrition_service', array ('nutrition_service_id' ), array ($clid ['client'] => $clientID, 'nutrition_service_visit_id' => $newTabIDs ) );
								}
								break;
							case 'admission_caregivers' :
								//$carez=$newTabIDs;
								proceedTable ( &$udata, 'admission_info', array ('admission_id' ),
								array ('admission_client_id' => $clientID,
								'admission_father' => $newTabIDs,
								'admission_mother' => $newTabIDs,
								'admission_caregiver_pri' => $newTabIDs,
								'admission_caregiver_sec' => $newTabIDs,
								'admission_clinic_id' => $newCenters
								)
								);
								$socials = proceedTable ( &$udata, 'social_visit', array ('social_id' ),
								array ('social_client_id' => $clientID,
								'social_caregiver_pri' => $newTabIDs,
								'social_caregiver_sec' => $newTabIDs,
								'social_clinic_id' => $newCenters
								)
								);
								if (count ( $socials ) > 0) {
									proceedTable ( &$udata, 'social_services', array ('social_services_id' ), array ('social_services_client_id' => $clientID, 'social_services_social_id' => $socials ) );
								}
								break;
							case 'medical_assessment' :
								if (count ( $newTabIDs ) > 0) {
									proceedTable ( &$udata, 'medical_history', array ('medical_history_id' ), array ('medical_history_client_id' => $clientID, 'medical_history_medical_id' => $newTabIDs ) );
									proceedTable ( &$udata, 'medications_history', array ('medications_history_id' ), array ('medications_history_client_id' => $clientID, 'medications_history_medical_id' => $newTabIDs ) );
								}
								break;
							default :
								break;
						}
					}
					/*
					* TODO verify and enable adding rentry to status_history and update clients table client_clinic field
					*
					*/

				}
				$sql='update ltp_inbox set ltp_status="accepted" where id="'.$row_id.'"';
				$res=mysql_query($sql);
				$vtables = array (
				'counselling_info'=> array('counselling_entry_date','counselling_client_id','counselling_id'),
				'clinical_visits' => array('clinical_entry_date','clinical_client_id','clinical_id'),
				'counselling_visit' => array('counselling_entry_date','counselling_client_id','counselling_id'),
				'social_visit'=>array('social_entry_date' , 'social_client_id','social_id'),
				'nutrition_visit' => array('nutrition_entry_date','nutrition_client_id','nutrition_id'),
				'medical_assessment' => array('medical_entry_date','medical_clinet_id','medical_id'),
				'admission_info' => array('admission_entry_date','admission_client_id','admission_id')
				);


				$dsql='select UNIX_TIMESTAMP(%s),%s from %s where %s = "%d" order by %s desc limit 1';

				$maxDate = 0;
				$maxForm='';
				foreach ($vtables as $table => $fdz) {
					$sql=sprintf($dsql,$fdz[0],$fdz[2],$table,$fdz[1],$clientID,$fdz[0]);
					$res=mysql_query($sql);
					if($res){
						$rv = mysql_fetch_array($res);
						if($rv[0] > $maxDate){
							$maxDate=$rv[0];
							$maxForm = $table.'|'.$rv[1];
						}
					}
				}
				if($maxDate > 0 && $maxForm != ''){
					$isql='update clients set client_lvd ="'.date("Y-m-d",$maxDate).'",client_lvd_form="'.$maxForm.'" where client_id="'.$clientID.'"';
					$res2=mysql_query($isql);
				}

				/*
				*	'admission_info'=>array('client'=>'admission_client_id','id'=>'admission_id'),
				'social_visit'=>array('client'=>'social_client_id','id'=>'social_id'),


				*/
				++ $imported;
			}

		}
	}
	//}


	echo ($imported > 0 ? 'ok' : 'fail');
	return ;
}

/*}
}
}

}*/

function putInList(){
	$wfile=&$_FILES['formfile'];
	$imported=0;
	if (is_uploaded_file ( $wfile['tmp_name'] ) && $wfile ['error'] == 0) {
		$fpath = $wfile ['tmp_name'];
		$news = file_get_contents ( $fpath );
		if (strlen ( $news ) > 0) {
			$usersEn = preg_split( '/===###===/', $news,-1,PREG_SPLIT_NO_EMPTY );
			unset ( $news );
			if (count ( $usersEn ) > 1) {
				$imported = 0;
				foreach ( $usersEn as $iid => $uencode ) {
					$bat = gzdecode ( $uencode );
					$udata = unserialize ( $bat );
					unset ( $uencode );
					if ($iid === 0) {
						unset($bat);
						$oldCenters = $udata;
						foreach ( $oldCenters as $cid => $cname ) {
							if (is_numeric ( $cid )) {
								$q = new DBQuery ();
								$q->addTable ( 'clinics' );
								$q->addWhere ( 'lower(clinic_name) = "' . strtolower ( $cname ) . '"' );
								$q->setLimit ( 1 );
								$q->addQuery ( 'clinic_id' );
								$newcid = $q->loadResult ();
								if (! is_numeric ( $newcid )) {
									$sql = 'insert into clinics  (clinic_name,clinic_owner) values ("' . $cname . '","1")';
									db_exec ( $sql );
									$newcid = db_insert_id ();
								}
								$newCenters [$cid] = $newcid;
							}else{
								$target=$cname;
							}
						}
						$q=new DBQuery();
						$q->addTable('config');
						$q->addWhere('config_name="current_center"');
						$q->addWhere('lower(config_value) = "'.strtolower($oldCenters[$target]).'"');
						$q->addQuery("1");
						$res=$q->loadResult();
						if($res != '1'){
							echo 'wrong_center';
							return false;
						}

					}else{
						//we'll check the presence of adm_no during file import process

						$sql = 'select 1  from clients where client_adm_no="' . $udata ['clients'] ['client_adm_no'] . '" limit 1';
						$res = mysql_query ( $sql );

						$sql='select count(*) from ltp_inbox where client_adm_no="'.$udata['clients']['client_adm_no'].'"';
						$res2 = mysql_query($sql);
						$already = mysql_fetch_array($res2);
						if ($res && mysql_num_rows ( $res ) == 0 && $already[0] == 0){

							$cprofile=$udata['profile'];
							$sql='insert into ltp_inbox (client_name, client_adm_no, center_origin,client_arrived,client_real_data)
							values ("'.$cprofile['name'].'",
									"'.$cprofile['adm_no'].'",
									"'.$cprofile['center'].'",
									"'.$cprofile['date'].'",
									"'.addslashes(gzencode(serialize(array($newCenters,$bat)))).'")';
							mysql_query($sql);
							++$imported;
						}
					}
				}
			}
		}
	}
	echo $imported;
}

function buildListTins(){

	$centers = centerList();

	$q = new DBQuery();
	$q->addTable('ltp_inbox');
	$q->addOrder('client_name asc');
	$list=$q->loadList();

	if(!$list || count($list) == 0){
		return '<p><i>Clients pending or already imported not found in system</i></p>';
	}

	$html='<form id="xtlist" name="xtlist" action="/?m=manager&mode=proceed_tin&suppressHeaders=1" method="post" onsubmit="return AIM.submit(this, {\'onStart\' : startCallBack, \'onComplete\' : \'\'})">
			<input type="hidden" name="ekey">
		<table class="tbl tin tablesorter" cellpadding="2" cellspacing="1" id="clittab" width="100%">
	<thead><tr><th>ADM #</th><th>Name</th><th>Date</th><th>Center</th><th>Transfer Status</th></tr></thead><tbody>';
	foreach ($list as $x => $vars) {
		$imported=($vars['ltp_status'] === 'accepted');
		$html.='<tr data-tid="'.$vars['id'].'">
			<td class="'.($imported ? 'past' : 'future' ).'">'.$vars['client_adm_no'].'
			<input type="hidden" name="cli[]" value="'.$vars['id'].'">
			</td>
			<td>'.$vars['client_name'].'</td>
			<td>'.$vars['client_arrived'].'</td>
			<td>'.$vars['center_origin'].'</td>
			<td>'.($imported ? 
			'&nbsp;<span>DONE</span>&nbsp;<div class="fbutton undobutt" title="Undo"></div>'
			:
			'Not Done'
			).'</td>
		</tr>';
	}
	$html.='</tbody></table><br>
	<input type="button" value="Submit" onclick="manager.startTIN(this);" class="text">
	';

	return $html;
}

?>