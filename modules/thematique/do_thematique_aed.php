<?php /* CONTACTS $Id: do_contact_aed.php,v 1.5 2005/01/17 07:48:25 ajdonnison Exp $ */

$obj = new CActivity();
$msg = '';
//var_dump($_POST);
if (!$obj->bind( $_POST )) 
{
	$AppUI->setMsg( $obj->getError(), UI_MSG_ERROR );
	$AppUI->redirect();
}

$del = dPgetParam( $_POST, 'del', 0 );

// prepare (and translate) the module name ready for the suffix
$AppUI->setMsg( 'Activity' );
if ($del) {
	if (($msg = $obj->delete())) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
		$AppUI->redirect();
	} else {
		$AppUI->setMsg( "deleted", UI_MSG_ALERT, true );
		$AppUI->redirect( "m=activity" );
	}
} else {
	$isNotNew = @$_POST['activity_id'];
	
	if (is_array ( $_POST ['activity_type_of_intervention'] ) && count ( $_POST ['activity_type_of_intervention'] ) > 0) {
		$obj->activity_type_of_intervention = implode ( ',', $_POST ['activity_type_of_intervention'] );
	}
	if (is_array ( $_POST ['activity_type_of_beneficiery'] ) && count ( $_POST ['activity_type_of_beneficiery'] ) > 0) {
		$obj->activity_type_of_beneficiery = implode ( ',', $_POST ['activity_type_of_beneficiery'] );
	}
	
	if (is_array ( $_POST ['activity_administration_section'] ) && count ( $_POST ['activity_administration_section'] ) > 0) {
		$obj->activity_administration_section = implode ( ',', $_POST ['activity_administration_section'] );
	}
	
	if (($msg = $obj->store())) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
	} else {
		$AppUI->setMsg( $isNotNew ? 'updated' : 'added', UI_MSG_OK, true );
	}
	$AppUI->redirect("m=activity");
}
?>
