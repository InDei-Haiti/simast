<?php

require_once ("./classes/CustomFields.class.php");
require_once $AppUI->getModuleClass ( 'followup' );

$obj = new CFollowUp ();
$msg = '';
if (! $obj->bind ( $_POST )) {
	$AppUI->setMsg ( $obj->getError (), UI_MSG_ERROR );
	$AppUI->redirect ();
}

/*$sql='select client_id from clients where client_adm_no="'.$obj->followup_adm_no.'" limit 1';
$res=mysql_query($sql);
$clid= mysql_fetch_object($res);
*/
if (! empty ( $_POST ["followup_date"] )) {
	$entry_date = new CDate ( $_POST ["followup_date"] );
	$obj->followup_date = $entry_date->format ( FMT_DATETIME_MYSQL );
}
if(isset($_POST['followup_issues'])){
	$obj->followup_issues = join(',',$_POST['followup_issues']);
}

if(isset($_POST['followup_service'])){
	$obj->followup_service = @join(',',$_POST['followup_service']);
}

if ($msg = $obj->store ()) {
	$AppUI->setMsg ( $msg, UI_MSG_ERROR );
}

$AppUI->redirect ( 'm=clients&a=view&tab=1&client_id=' . $obj->followup_client_id );

?>