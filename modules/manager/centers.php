<?php

$myCenters=array(
			6=>'Kangemi',
			7=>'Kibera',
			8=>'Kawangware',
			9=>'Kariobangi',
			10=>'Mukuru',
			11=>'Dandora',
			12=>'Kangora',
			13=>'Mwiki'
			);


$myNewCenterNames = array(
	12=>'Dagoretti'
);

$q = new DBQuery();
$q->addTable('clinics');
$q->addQuery('clinic_id, clinic_name');
$clinics = $q->loadArrayList(0);

$fixIds = array();
$missingClinics = array();
$metClinics = array();
foreach ($clinics  as $centerId => $cname) {
	$lpos = array_search($cname[1],$myCenters);
	$lpos2 = array_search($cname[1],$myNewCenterNames);
	if(is_numeric($lpos) || is_numeric($lpos2)){
		$metClinics[]=($lpos ? $lpos : $lpos2);
	}
	if($myCenters[$centerId] !== $cname[1] || ($lpos2 !== false && array_key_exists($lpos2,$myNewCenterNames)
		&& $cname[1] !== $myNewCenterNames[$centerId])){
		$newId = array_search($cname[1],$myCenters);
		$fixIds[$centerId] = $newId;
	}
}
sort($metClinics);
$tkeys=array_keys($myCenters);
$missingClinics = array_diff($tkeys,$metClinics);

$tables = array (
	'counselling_clinic' => 'counselling_info',
	'clinical_clinic_id' => 'clinical_visits',
	'counselling_center_id' => 'counselling_visit',
	'social_clinic_id' => 'social_visit',
	'nutrition_center' => 'nutrition_visit',
	'medical_clinic_id' => 'medical_assessment',
	'admission_clinic_id' => 'admission_info',
	'mortality_clinic_id' => 'mortality_info',
	'clinic_id' => 'clinics',
	'clinic_location_clinic_id' => 'clinic_location',
	'activity_clinic' => 'activity',
	'followup_center_id' => 'followup_info',
	'chw_center_id'		=> 'chw_info',
	'cbc_center_id'		=> 'cbc_info',
	'client_center'		=> 'clients'
);



$sql='update %s set %s="%d" where %s="%d"';
if(count($fixIds) > 0){
	foreach ($fixIds as $old_id => $new_id) {
		foreach ($tables as $column => $table){
			$query=sprintf($sql,$table,$column,($new_id + 100),$column,$old_id);
			$res=mysql_query($query);
		}
	}
	foreach ($fixIds as $new_id) {
		foreach ($tables as $column => $table){
			$query=sprintf($sql,$table,$column,$new_id,$column,($new_id+100));
			$res=mysql_query($query);
		}
	}
}

if(count($missingClinics) > 0){
	$sql='insert into clinics (clinic_id,clinic_name,clinic_owner) values ';
	$sql_add=array();
	foreach ($missingClinics as $missId) {

		$bename = (isset($myNewCenterNames[$missId]) ? $myNewCenterNames[$missId] : $myCenters[$missId]);
		$sql_check = 'select 1 from clinics where clinic_name="'.$bename.'" and clinic_id="'.$missId.'"';
		$rcheck = mysql_query($sql_check);
		if(mysql_num_rows($rcheck) === 0){
			$sql_add[]='("'.$missId.'","'.
						$bename
						.'","1")';
		}
	}
	$sql.=join(',',$sql_add);
	$res=db_exec($sql);
}


foreach ($myNewCenterNames as $cid => $cname) {
	$query ='update clinics set clinic_name="'.$cname.'" where clinic_id="'.$cid.'"';
	$res=mysql_query($query);
}


$q = new DBQuery();
$q->addTable('clients');
$q->addQuery('client_id,client_doa');
$clients = $q->loadArrayList(0);

$vtables = array (
	'counselling_info'=> array('counselling_entry_date','counselling_client_id','counselling_id'),
	'clinical_visits' => array('clinical_entry_date','clinical_client_id','clinical_id'),
	'counselling_visit' => array('counselling_entry_date','counselling_client_id','counselling_id'),
	'social_visit'=>array('social_entry_date' , 'social_client_id','social_id'),
	'nutrition_visit' => array('nutrition_entry_date','nutrition_client_id','nutrition_id'),
	'medical_assessment' => array('medical_entry_date','medical_clinet_id','medical_id'),
	'admission_info' => array('admission_entry_date','admission_client_id','admission_id')
	/* 'followup_center_id' => 'followup_info',
	'chw_center_id'		=> 'chw_info',
	'cbc_center_id'		=> 'cbc_info'	*/
);


$dsql='select UNIX_TIMESTAMP(%s),%s from %s where %s = "%d" order by %s desc limit 1';
foreach ($clients as $clt) {
	$maxDate = 0;
	$maxForm='';
	foreach ($vtables as $table => $fdz) {
		$sql=sprintf($dsql,$fdz[0],$fdz[2],$table,$fdz[1],$clt[0],$fdz[0]);
		$res=mysql_query($sql);
		if($res){
			$rv = mysql_fetch_array($res);
			if($rv[0] > $maxDate){
				$maxDate=$rv[0];
				$maxForm = $table; //.'|'.$rv[1];
			}
		}
	}
	if($maxDate > 0 && $maxForm != ''){
		$isql='update clients set client_lvd ="'.date("Y-m-d",$maxDate).'",client_lvd_form="'.$maxForm.'" where client_id="'.$clt[0].'"';
		$res2=mysql_query($isql);
	}
}
$sql='update config set config_value="1" where config_name="regular_lvd"';
db_exec($sql);

echo 'ok';
?>