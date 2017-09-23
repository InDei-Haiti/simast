<?php

function getCompany($towner){
	global $owners,$AppUI;	
	if(!isset($owners[$towner])){
		$q = new DBQuery();
		$q->addTable('contacts');
		$q->addTable('users');
		$q->addQuery('contact_company');
		$q->addWhere('user_id="'.$towner.'"');
		$q->addWhere('contact_id = user_contact');
		$uCompany = $q->loadResult();		
		$owners[$towner] = (int)$uCompany;
	}
	return $owners[$towner];	
}

$owners = array();
$sql = 'select * from tasks';

$res = mysql_query($sql);

if($res && mysql_num_rows($res) > 0){
	while($ti = mysql_fetch_assoc($res)){
		$ncomp = getCompany($ti['task_creator']);
		if($ncomp > 0){
			$sql = 'update tasks set task_company ="'.$ncomp.'" where task_id="'.$ti['task_id'].'"';
			$ri = mysql_query($sql);
		}
	}
}

?>