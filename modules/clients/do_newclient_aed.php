<?php
require_once("./classes/CustomFields.class.php");
//require_once ($AppUI->getModuleClass ( 'socialinfo' ));
//require_once ($AppUI->getModuleClass ( 'counsellinginfo' ));

$del = isset ( $_POST ['del'] ) ? $_POST ['del'] : 0;
$changestatus = isset ( $_POST ['changestatus'] ) ? $_POST ['changestatus'] : 0;
$task_id = intval ( dPgetParam ( $_POST, "task_id", 0 ) );
$sub_form = isset ( $_POST ['sub_form'] ) ? $_POST ['sub_form'] : 0;
//social info
$socialinfo = setItem ( "social" );
//counselling info
$counsellinginfo = setItem ( "counselling" );

//handle new clients
$contact_unique_update = setItem ( "insert_id" );

$client = setItem ( "client" );
//var_dump($client);
//exit();
//print_r($_POST);
$client_id = setItem ( "client_id", 0 );
$new_status = setItem ( "status", 0 );

$del = setItem ( "del", 0 );

$AppUI->setMsg ( 'Beneficiery' );

$clientObj = new CClient ( );

// If we have an array of pre_save functions, perform them in turn.
if (isset ( $pre_save )) {
	foreach ( $pre_save as $pre_save_function )
		$pre_save_function ();
} else {
	dprint ( __FILE__, __LINE__, 1, "No pre_save functions." );
}



if ($del) {
	if (! $clientObj->load ( $client_id )) {
		$AppUI->setMsg ( $clientObj->getError (), UI_MSG_ERROR );
		$AppUI->redirect ();

	}
	if (! $clientObj->canDelete ( $msg )) {
		$AppUI->setMsg ( $msg, UI_MSG_ERROR );
		$AppUI->redirect ();
	}
	if (($msg = $clientObj->delete ())) {
		$AppUI->setMsg ( $msg, UI_MSG_ERROR );
		$AppUI->redirect ();
	} else {
		$AppUI->setMsg ( 'deleted', UI_MSG_ALERT, true );
		$AppUI->redirect ( "index.php?m=clients" );
	}
} else {
	if($client['client_id'] > 0){
		$clientObj->load($client['client_id']);
	}
	if (! $clientObj->bind ( $client )) {
		$AppUI->setMsg ( $clientObj->getError (), UI_MSG_ERROR );
		$AppUI->redirect ();
	}

	if (($msg = $clientObj->store ())) {
		$msg = str_replace("<br/>", ". 1.- ", $msg);
		$AppUI->setMsg ( $msg, UI_MSG_ERROR );
		$AppUI->redirect (); // Store failed don't continue?
	}else{
		if($task_id){
			$q = new DBQuery();
			$q->addTable("activity_clients");
			$q->addQuery("activity_clients_client_id");
			$q->addWhere("activity_clients_activity_id=".$task_id." AND activity_clients_client_id".$clientObj->client_id);
			$q->limit = 1;
			$res = $q->loadResult();
			if(!$res){
				$sql = "INSERT INTO `activity_clients`(`activity_clients_activity_id`, `activity_clients_client_id`) VALUES ($task_id,$clientObj->client_id)";
				db_exec($sql);
			}
		}
		$custom_fields = New CustomFields( $m, 'addedit', $clientObj->client_id, "edit" );
		$custom_fields->bind( $_POST );
		$sql = $custom_fields->store( $clientObj->client_id );
		
		$isNotNew = $client['client_id'];
		$AppUI->setMsg( $isNotNew ? 'Beneficiery updated' : 'Beneficiery inserted', UI_MSG_OK);
	}
	$AppUI->redirect();
} 
?>