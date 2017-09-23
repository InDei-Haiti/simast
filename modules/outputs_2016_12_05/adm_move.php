<?php

function clearasil($str,$lower =false){
	if(strstr($str,'amp;')){
		$str=preg_replace("/amp;/","",$str);
		$str=str_replace('&',' and ',$str);
		$str=preg_replace('/\s{2,}/',' ',$str);
	}
	return $lower === false ? $str : strtolower($str);
}

$sql = 'select * from admission_info';
$res = mysql_query ( $sql );
$slim_str='';
if ($res) {
	$olds = array ();
	while ( $row = mysql_fetch_assoc ( $res ) ) {
		$olds [] = $row;
	}
	mysql_free_result ($res);
	foreach ( $olds as $row ) {
		$updates = array ();
		if ($row ['admission_father_fname'] != '' && $row['admission_father_fname'] != 'N/A' && trim($row['admission_father_fname']) !== '-') {
			$sql = 'insert into admission_caregivers
				(fname,lname,age,health_status,marital_status,educ_level,employment,idno,mobile,role,client_id)
				values("' . clearasil($row ['admission_father_fname']) . '",
						"' . clearasil($row ['admission_father_lname']) . '",
						"' . $row ['admission_father_age'] . '",
						"' . $row ['admission_father_health_status'] . '",
						"' . $row ['admission_father_marital_status'] . '",
						"' . $row ['admission_father_educ_level'] . '",
						"' . $row ['admission_father_employment'] . '",						
						"' . $row ['admission_father_idno'] . '",
						"' . $row ['admission_father_mobile'] . '",
						"father",
						"' . $row ['admission_client_id'] . '"
						)';
			$finr = mysql_query ( $sql );
			if ($finr) {
				$father_id = mysql_insert_id ();
				//$sql='update admission_info_slim set  where admission_id="'.$row['admission_id'].'"';
				//mysql_query($sql);
				$updates [] = 'admission_father="' . $father_id . '"';				
			}else{
				$father_id=null;
			}
		}else{
			$father_id=null;
		}
		unset( $row ['admission_father_fname'],$row ['admission_father_lname'],$row ['admission_father_age'],$row ['admission_father_health_status'],
			   $row ['admission_father_marital_status'],$row ['admission_father_educ_level'],$row ['admission_father_employment'],$row ['admission_father_idno'],
			   $row ['admission_father_mobile'],$row ['admission_father_income']);
		$row['admission_father']=$father_id;
		if ($row ['admission_mother_fname'] != '' && $row['admission_mother_lname'] != 'N/A' && trim($row['admission_mother_fname']) !== '-') {
			$finr = false;
			$sql = 'insert into admission_caregivers
				(fname,lname,age,health_status,marital_status,educ_level,employment,idno,mobile,role,client_id)
				values("' . clearasil($row ['admission_mother_fname']) . '",
						"' . clearasil($row ['admission_mother_lname']) . '",
						"' . $row ['admission_mother_age'] . '",
						"' . $row ['admission_mother_health_status'] . '",
						"' . $row ['admission_mother_marital_status'] . '",
						"' . $row ['admission_mother_educ_level'] . '",
						"' . $row ['admission_mother_employment'] . '",						
						"' . $row ['admission_mother_idno'] . '",
						"' . $row ['admission_mother_mobile'] . '",
						"mother",
						"' . $row ['admission_client_id'] . '"
						)';
			$finr = mysql_query ( $sql );
			if ($finr) {
				$mother_id = mysql_insert_id ();
				//$sql='update admission_info_slim set  where admission_id="'.$row['admission_id'].'"';
				//mysql_query($sql);
				$updates [] = 'admission_mother="' . $mother_id . '"';
			}else{
				$mother_id=null;
			}
		}else{
			$mother_id=null;
		}
		unset( $row ['admission_mother_fname'],$row ['admission_mother_lname'],$row ['admission_mother_age'],$row ['admission_mother_health_status'],
				$row ['admission_mother_marital_status'],$row ['admission_mother_educ_level'],$row ['admission_mother_employment'],						
				$row ['admission_mother_idno'],$row ['admission_mother_mobile'],$row ['admission_mother_income']);
		$row['admission_mother']=$mother_id;
		$finr = false;
		if ($row ['admission_caregiver_fname'] != '' && $row ['admission_caregiver_fname'] != 'N/A' && trim($row['admission_caregiver_fname']) !== '-') {
			$sql = 'insert into admission_caregivers
				(fname,lname,age,health_status,marital_status,educ_level,employment,idno,mobile,role,client_id,relationship)
				values("' . clearasil($row ['admission_caregiver_fname']) . '",
						"' . clearasil($row ['admission_caregiver_lname']) . '",
						"' . $row ['admission_caregiver_age'] . '",
						"' . $row ['admission_caregiver_health_status'] . '",
						"' . $row ['admission_caregiver_marital_status'] . '",
						"' . $row ['admission_caregiver_educ_level'] . '",
						"' . $row ['admission_caregiver_employment'] . '",						
						"' . $row ['admission_caregiver_idno'] . '",
						"' . $row ['admission_caregiver_mobile'] . '",
						"pri",
						"' . $row ['admission_client_id'] . '",
						"' . $row ['admission_caregiver_relationship'] . '"
						)';
			$finr = mysql_query ( $sql );
			if ($finr) {
				$caregiver_id = mysql_insert_id ();
				//$sql='update admission_info_slim set admission_caregiver="'.$father_id.'" where admission_id="'.$row['admission_id'].'"';
				//mysql_query($sql);
				$updates [] = 'admission_caregiver_pri="' . $caregiver_id . '"';
			}else{
				$caregiver_id = null;
			}
		}else{
			$caregiver_id=null;
		}
		//$row['admission_caregiver_pri_relationship']=$row['admission_caregiver_relationship'];
		unset($row ['admission_caregiver_fname'],$row ['admission_caregiver_lname'],$row ['admission_caregiver_age'],
			  $row ['admission_caregiver_health_status'],$row ['admission_caregiver_marital_status'],$row ['admission_caregiver_educ_level'],
			  $row ['admission_caregiver_employment'],$row ['admission_caregiver_idno'],$row ['admission_caregiver_mobile'],
			  $row ['admission_caregiver_income'], $row['admission_caregiver_status'],$row['admission_caregiver_relationship']);
		$row['admission_caregiver_pri']=$caregiver_id;
		
		/*if (count ( $updates ) > 0) {
			$sql = 'update admission_info_slim set ' . join ( ',', $updates ) . ' where admission_id="' . $row ['admission_id'] . '"';
			mysql_query ( $sql );
		}*/
		
		/*$slim_str='insert into admission_info_slim (';
		$kars=array();$vars=array();
		foreach ($row as $key=> $val){
			$kars[]=$key;
			$vars[]='"'.$val.'"';
		}
		$slim_str.=implode(',',$kars).') values ('.implode(',',$vars).')';
		
		$res=mysql_query($slim_str);*/
		db_insertArray('admission_info_slim',&$row);
	}
}
unset($olds);
/*
 * update admission_caregivers,admission_info set client_id=admission_client_id where admission_caregiver_sec=id
 */

$clients = array();
//counselling_caregiver_status as status,counselling_caregiver_health_status as health_status,
$sql='SELECT admission_client_id AS client_id,"sec" as role, 
			 counselling_caregiver_fname  AS fname,
			 counselling_caregiver_lname  AS lname,
			 counselling_caregiver_age  AS age, 
			 counselling_caregiver_idno  AS idno, 
			 counselling_caregiver_mobile  AS mobile, 
			 counselling_caregiver_relationship  AS relationship, 
			 counselling_caregiver_marital_status  AS marital_status, 
			 counselling_caregiver_educ_level  AS educ_level, 
			 counselling_caregiver_employment  AS employment
		FROM admission_info ai
		LEFT JOIN counselling_visit cv ON ai.admission_client_id = cv.counselling_client_id
		WHERE counselling_caregiver_fname IS NOT NULL
			AND counselling_caregiver_fname <> ""
			AND counselling_caregiver_lname IS NOT NULL
			AND counselling_caregiver_lname <> ""';
$res=mysql_query($sql);
$clients=array();
if($res){
	while ($row = mysql_fetch_assoc($res)) {
		$row1=$row;
		//,$row1['relationship']
		unset($row1['id']);
		db_insertArray('admission_caregivers',&$row1);
		//,	admission_caregiver_sec_relationship="'.$row['relationship'].'"
		$sql='update admission_info_slim set admission_caregiver_sec="'.$row1['id'].'"
									where admission_client_id="'.$row['id'].'"';
		$resx=mysql_query($sql);
	}
}
mysql_free_result($res);

$sql = 'select * from social_visit_old ';
$res = mysql_query ( $sql );
$arrs = array ();
if ($res) {
	while ( $row = mysql_fetch_assoc ( $res ) ) {
		$arrs [] = $row;
	}
	mysql_free_result ( $res );
	unset($res);
	foreach ( $arrs as $row ) {
		/**
		 * 
		 * part for work with client status
		 */
		$addLog=false;
		if(!array_key_exists($row['social_client_id'],$clients)){
			$clients[$row['social_client_id']]=$row['social_client_status'];
			$addLog=true;
		}else{
			if($clients[$row['social_client_id']] != $row['social_client_status']){
				$addLog=true;
				$clients[$row['social_client_id']] = $row['social_client_status'];
			}
		}
		if($addLog){
			$sql='insert into status_client (social_client_id,social_client_status,social_entry_date,mode)
					values ("'.$row['social_client_id'].'","'.$row['social_client_status'].'","'.$row['social_entry_date'].'","status")';
			$res=mysql_query($sql);			
		}
		$newid=null;
		if($row['social_caregiver_fname'] != '' && $row['social_caregiver_fname'] != 'N/A' && trim($row['social_caregiver_fname']) !== '-'){
			$csql='select id,fname,lname from admission_caregivers  where client_id="'.$row['social_client_id'].'" and role="pri" order by id desc limit 1';
			$fres=mysql_query($csql);			
			if($fres !== false && mysql_num_rows($fres) > 0){
				$ppri=mysql_fetch_assoc($fres);	
				if(clearasil($ppri['fname'],true) == clearasil($row['social_caregiver_fname'],true) && clearasil($ppri['lname'],true) == clearasil($row['social_caregiver_lname'],true)){
					$newid=$ppri['id'];
				}else{
					$usql='update admission_caregivers set datesoff="'.$row['social_entry_date'].'" where id="'.$ppri['id'].'"';
					mysql_query($usql);
					$fres=false;
				}
			}else{
				$fres=null;
			}
			if(!$fres){
				$sql = 'insert into admission_caregivers 
					(fname, lname, idno,mobile, health_status,age, educ_level,employment,role,client_id,relationship)
					values("%s","%s","%s","%s","%s","%s","%s","%s","pri","%d","%s")';
				$sql = sprintf ( $sql, clearasil($row ['social_caregiver_fname']), clearasil($row ['social_caregiver_lname']), 
								$row ['social_caregiver_idno'], $row ['social_caregiver_mobile'], $row ['social_caregiver_status'], 
								$row ['social_caregiver_age'], $row ['social_caregiver_education'], $row ['social_caregiver_employment'], 
								$row ['social_client_id'] ,$row['social_caregiver_relationship']);
				$lres=mysql_query ( $sql );
				$newid = mysql_insert_id ();
			}
			$row['social_caregiver_pri']=$newid;
			$row['social_caregiver_pri_change']=$row['social_caregiver_change'];
			$row['social_caregiver_pri_change_notes']=$row['social_caregiver_change_notes'];
			$row['social_caregiver_pri_employment_change']=$row['social_caregiver_employment_change'];
			$row['social_caregiver_pri_new_employment']=$row['social_caregiver_new_employment'];
			$row['social_caregiver_pri_new_employment_desc']=$row['social_caregiver_new_employment_desc'];
			$row['social_caregiver_pri_health']=$row['social_caregiver_health'];
			$row['social_caregiver_pri_health_child_impact']=$row['social_caregiver_health_child_impact'];
			/*$sql = 'update social_visit set social_caregiver_pri="' . $newid . '" where social_id=' . $row ['social_id'];*/
			
			//mysql_query ( $sql );
		}
		unset( $row ['social_caregiver_fname'], $row ['social_caregiver_lname'], $row ['social_caregiver_idno'], $row ['social_caregiver_mobile'], $row ['social_caregiver_health'], 
				$row ['social_caregiver_age'], $row ['social_caregiver_education'],	$row ['social_caregiver_employment'], $row['social_caregiver_relationship'],
				$row['social_caregiver_income'],$row['social_caregiver_change'],$row['social_caregiver_change_notes'],$row['social_caregiver_status'],$row['social_caregiver_health_child_impact'],
				$row['social_caregiver_new_income']);
				//$row['social_caregiver_employment_change'],$row['social_caregiver_new_employment'],$row['social_caregiver_new_employment_desc']
		db_insertArray('social_visit',&$row);
	}
}

$sql='rename table admission_info to admission_info_old, admission_info_slim to admission_info';
mysql_query($sql);
/**
 * alter table activity add activity_end_date DATE default null,
 * add activity_visitors_total INT(10) NULL default null,
 * add activity_hpd int(10) NULL default null,
 * ADD `activity_cadres` VARCHAR( 30 ) NULL DEFAULT NULL AFTER `activity_visiters_total`;
 *
 * update activity set activity_end_date = activity_date
 * ALTER TABLE `activity_facilitator` ADD `facilitator_topic` VARCHAR( 100 ) NULL AFTER `facilitator_name` 
 */

?>