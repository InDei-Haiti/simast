<?php /* MEDICAL ASSESSMENT $Id: do_company_aed.php,v 1.9 2005/04/26 06:55:42 ajdonnison Exp $ */
$del = dPgetParam( $_POST, 'del', 0 );

var_dump($_POST);
exit;
$obj = new CMedicalAssessment();
$msg = '';


if (!$obj->bind( $_POST )) {
	$AppUI->setMsg( $obj->getError(), UI_MSG_ERROR );
	$AppUI->redirect();
}
if (!empty($_POST["medical_conditions"]))
{
	$obj->medical_conditions = implode(",", $_POST["medical_conditions"]);
}
require_once("./classes/CustomFields.class.php");
require_once($AppUI->getModuleClass("medicalhistory"));
require_once($AppUI->getModuleClass("medicationhistory"));


// prepare (and translate) the module name ready for the suffix
$AppUI->setMsg( 'Medical Assessment' );
if ($del) {
	if (!$obj->canDelete( $msg )) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
		$AppUI->redirect();
	}
	if (($msg = $obj->delete())) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
		$AppUI->redirect();
	} else {
		$AppUI->setMsg( 'deleted', UI_MSG_ALERT, true );
		$AppUI->redirect( 'm=clients' );
	}
} 
else 
{


	if ($msg = $obj->store())
	{
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
	} 
	else 
	{
 		$custom_fields = New CustomFields( $m, 'addedit', $obj->medical_id, "edit" );
 		$custom_fields->bind( $_POST );
 		$sql = $custom_fields->store( $obj->medical_id ); // Store Custom Fields
		$AppUI->setMsg( @$_POST['medical_id'] ? 'updated' : 'added', UI_MSG_OK, true );
		
		//store medical history
		if (!empty($_POST["medical_history"]))
		{
			$medicalHistObj = new CMedicalHistory();
			$medicalHistory->bind($_POST["medical_history"]);
		}
	}
	$AppUI->redirect();
}
?>
