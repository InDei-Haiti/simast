<?php /* COMPANIES $Id: do_company_aed.php 4800 2007-03-06 00:34:46Z merlinyoda $ */
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}

$del = dPgetParam( $_POST, 'del', 0 );
$obj = new CCompany();
$msg = '';

if (!$obj->bind( $_POST )) {
	$AppUI->setMsg( $obj->getError(), UI_MSG_ERROR );
	$AppUI->redirect();
}
if(strlen($obj->company_description) > 150){
	$obj->company_description=substr($obj->company_description,0,150);
}

require_once($AppUI->getSystemClass( 'CustomFields' ));

if (sizeof ( $_FILES ) > 0 && $_FILES ['company_logo'] ['name'] != "" ) {
	
	
	$imgpath=$dPconfig['root_dir']."/images/logos/";
	//echo $imgpath;
	//exit;
	$file_once = true;
	$temp_file_name = $_FILES ['company_logo'] ['tmp_name'];
	$user_file_name = $_FILES ['company_logo'] ['name'];
	$user_file_type = $_FILES ['company_logo'] ['type'];
	$user_file_size = $_FILES ['company_logo'] ['size'];

	if (!($user_file_name == "" || ($temp_file_name == "" xor ! @is_uploaded_file ( $temp_file_name ))) ) {
		$temp_file_name = addslashes ( $temp_file_name );
		$user_file_name = addslashes ( $user_file_name );

		$fname = explode ( ".", $user_file_name );
		if (sizeof ( $fname ) > 1) {
			$fname = array_reverse ( $fname );
			$fext = "." . $fname [0];
		} else {
			$fext = ".dat";
		}
		$real_name = substr ( md5 ( rand () . rand () . rand () ), 0, 16 ) . $fext;
		//
		$drf = imageResize(50,50,$temp_file_name,$imgpath.$real_name);

		//if (! move_uploaded_file ( $temp_file_name, $imgpath . $real_name )) {
		if(!((file_exists($imgpath.$real_name) && filesize($imgpath.$real_name) > 0) || !$drf)){
			$dname = false;
		} else {
			unlink($imgpath.$obj->company_logo);
			$dname = true;
			$obj->company_logo="/images/logos/".$real_name;
		}
		

	}
}


// prepare (and translate) the module name ready for the suffix
$AppUI->setMsg( 'Agency' );
if ($del) {
	$q = new DBQuery();
	$q->addTable("projects");
	$q->addQuery("project_partners,project_cdonors");
	$res = $q->loadList();
	
	$busy = false;
	foreach ($res as $i => $arrayvalue){
		foreach ($arrayvalue as $j => $value){
			$value = explode(',', $value);
			$busy = in_array($obj->company_id, $value);
			if($busy)
				break;
		}
		if($busy)
			break;
	}
	if(!$busy){
		$q = new DBQuery();
		$q->addTable("tasks");
		$q->addQuery("task_agency");
		$res = $q->loadColumn();
		var_dump($res);
		foreach ($res as $i => $value){
		
			$value = explode(',', $value);
			$busy = in_array($obj->company_id, $value);
			if($busy)
				break;
		}
	}
	
	
	if(!$busy){
		if (!$obj->canDelete( $msg )) {
			$AppUI->setMsg( $msg, UI_MSG_ERROR );
			$AppUI->redirect();
		}
		if (($msg = $obj->delete())) {
			$AppUI->setMsg( $msg, UI_MSG_ERROR );
			$AppUI->redirect();
		} else {
			$AppUI->setMsg( 'deleted', UI_MSG_ALERT, true );
			$AppUI->redirect( 'm=companies' );
		}
	}else{//l'enregistrement que vous voulez supprimer est en utilisation
		$msg = 'the record you want to delete is in use';
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
		$AppUI->redirect();
	}
	
}
else {
	if (($msg = $obj->store())) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
	} else {
		$custom_fields = New CustomFields( $m, 'addedit', $obj->company_id, "edit" );
		$custom_fields->bind( $_POST );
		$sql = $custom_fields->store( $obj->company_id ); // Store Custom Fields
		$AppUI->setMsg( @$_POST['company_id'] ? 'updated' : 'added', UI_MSG_OK, true );
	}
	$AppUI->redirect();
}
?>
