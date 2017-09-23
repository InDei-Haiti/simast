<?php
require_once($AppUI->getModuleClass("followup"));
if($_SERVER['CONTENT_LENGTH'] > 0){
	$siis = json_decode(str_replace('\\','',$_POST['jfk']));
	
	$entries = $_POST['fentry'];
	if(count($entries) > 0){
		$dbe = new CFollowUp();
		
		$entry_date = new CDate ( $_POST ["follow_date"] );
		$dbe->followup_date = $entry_date->format ( FMT_DATETIME_MYSQL );		
		$dbe->followup_center_id = (int)$_POST['clinic_id'];
		$dbe->followup_officer_id = (int)$_POST['officer_id'];
		
		foreach ($entries as $key => $evals){
			$dbe1 = clone $dbe;
			$evals=array_map("trim",$evals);
			$dbe1->followup_adm_no = $evals['adm_no'];
			$dbe1->followup_client_id = $evals['client_id'];
			$dbe1->followup_client_type = $evals['client_type'];
			$dbe1->followup_visit_type = $evals['visit_type'];
			$dbe1->followup_visit_mode = $evals['visit_mode'];
			$dbe1->followup_object = $evals['client_object'];
			$dbe1->followup_issues = @join(',',$siis->store->issue[$key]);
			$dbe1->followup_issues_notes = $siis->store_other->issue[$key];
			$dbe1->followup_service = @join(',',$siis->store->service[$key]);
			$dbe1->followup_service_notes = @$siis->store_other->service[$key];
			$dbe1->store();			
			unset($dbe1);
		}
		$AppUI->setMsg( 'Follow-ups updated ', UI_MSG_OK, true );		
	}
	
	$AppUI->redirect("m=clients");
}

?>