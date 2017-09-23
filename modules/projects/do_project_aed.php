<?php /* PROJECTS $Id: do_project_aed.php 5599 2008-01-08 12:57:22Z gregorerhardt $ */
if (! defined ( 'DP_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

$project_id = (int)$_POST['project_id'];

$obj = new CProject ( );
$msg = '';

$perms = & $AppUI->acl ();
$canRead = $perms->checkModuleItem ( $m, 'view', $project_id );
$canEdit = $perms->checkModuleItem ( $m, 'edit', $project_id );
$canAuthor = $perms->checkModuleItem( $m, 'add' );

$scan_edit= $obj->localCheck($AppUI->user_id,$project_id);

$adm_rights = $AppUI->isAdmin();

if($project_id > 0){
	$check_flag=$canEdit;
	$emsg="edit";
}else{
	$check_flag=$canAuthor;
	$emsg="add";	
	$q = new DBQuery();
	$q->addTable('contacts');
	$q->addQuery('contact_company');
	$q->addWhere('contact_id="'.$AppUI->user_contact.'"');
	$uCompany = $q->loadResult();
	if((int)$uCompany > 0){
		$project_company=(int)$uCompany;
	}
}
if($project_id == 0){
	$obj->project_owner = $AppUI->user_id;
}else{
	$tp = new CProject();
	$tp->load($project_id);
	$obj->project_owner = $tp->project_owner;
	unset($tp);
}

if( ($scan_edit && $project_id > 0) || ($canAuthor && $project_id == 0) || $adm_rights){
	$access=true;
}else{
	$access=false;
}

if(!$access){
	$AppUI->setMsg ( "You have no rights to $emsg project", UI_MSG_ERROR );
	$AppUI->redirect ();
}



if (! $obj->bind ( $_POST )) {
	$AppUI->setMsg ( $obj->getError (), UI_MSG_ERROR );
	$AppUI->redirect ();
}




$obj->project_actual_budget=preg_replace("/[^\d\.]/",'',$obj->project_actual_budget);
$obj->project_target_budget=preg_replace("/[^\d\.]/",'',$obj->project_target_budget);
if($project_company > 0)
	$obj->project_company = $project_company;

require_once ($AppUI->getSystemClass ( 'CustomFields' ));
// convert dates to SQL format first
if ($obj->project_start_date) {
	$date = new CDate ( web2dbDate($obj->project_start_date ));
	$obj->project_start_date = $date->format ( FMT_DATETIME_MYSQL );
}
if ($obj->project_end_date) {
	$date = new CDate ( web2dbDate($obj->project_end_date ));
	$date->setTime ( 23, 59, 59 );
	$obj->project_end_date = $date->format ( FMT_DATETIME_MYSQL );
}
if ($obj->project_actual_end_date) {
	$date = new CDate ( web2dbDate($obj->project_actual_end_date ));
	$obj->project_actual_end_date = $date->format ( FMT_DATETIME_MYSQL );
}

// let's check if there are some assigned departments to project
if (! dPgetParam ( $_POST, "project_departments", 0 )) {
	$obj->project_departments = implode ( ",", dPgetParam ( $_POST, "dept_ids", array () ) );
}

$del = dPgetParam ( $_POST, 'del', 0 );

// prepare (and translate) the module name ready for the suffix
if ($del) {
	$project_id = dPgetParam ( $_POST, 'project_id', 0 );
	$canDelete = $obj->canDelete ( $msg, $project_id );
	
	$q = new DBQuery();
	$q->addTable('form_master');
	$q->addQuery('COUNT(*)');
	$q->addWhere('project_id='.$project_id);
	$countproj = $q->loadResult();
	if($countproj > 0){
		$AppUI->setMsg ( 'You can\'t delete this project, it contains forms', UI_MSG_ERROR );
		$AppUI->redirect ();
	}
	
	$q = new DBQuery();
	$q->addTable('tasks');
	$q->addQuery('COUNT(*)');
	$q->addWhere('task_project='.$project_id);
	$countproj = $q->loadResult();
	if($countproj > 0){
		$AppUI->setMsg ( 'You can\'t delete this project, it contains activity', UI_MSG_ERROR );
		$AppUI->redirect ();
	}
	
	
	if (! $canDelete) {
		$AppUI->setMsg ( $msg, UI_MSG_ERROR );
		$AppUI->redirect ();
	}
	if (($msg = $obj->delete ())) {
		$AppUI->setMsg ( $msg, UI_MSG_ERROR );
		$AppUI->redirect ();
	} else {
		$AppUI->setMsg ( "Project deleted", UI_MSG_ALERT );
		$AppUI->redirect ( "m=projects" );
	}
} else {
	//if($obj->project_cdonors && count($obj->project_cdonors)>0){
		//$obj->project_cdonors = implode(",", $obj->project_cdonors);
	//}
	//if($obj->project_partners && count($obj->project_partners)>0){
		//$obj->project_partners = implode(",", $obj->project_partners);
	//}
	if (is_array ( $_POST ['project_partners'] ) && count ( $_POST ['project_partners'] ) > 0) {
		$obj->project_partners = implode ( ',', $_POST ['project_partners'] );
	}else{
		$obj->project_partners = '';
	}
	
	if (is_array ( $_POST ['project_cdonors'] ) && count ( $_POST ['project_cdonors'] ) > 0) {
		$obj->project_cdonors = implode ( ',', $_POST ['project_cdonors'] );
	}else{
		$obj->project_cdonors = '';
	}
	
	if (($msg = $obj->store ())) {
		$AppUI->setMsg ( $msg, UI_MSG_ERROR );
	} else {
		$isNotNew = @$_POST ['project_id'];
		
		if ($importTask_projectId = dPgetParam ( $_POST, 'import_tasks_from', '0' ))
			$obj->importTasks ( $importTask_projectId );
		$AppUI->setMsg ( $isNotNew ? 'Project updated' : 'Project inserted', UI_MSG_OK, true );
		
		$custom_fields = New CustomFields ( $m, 'addedit', $obj->project_id, "edit" );
		$custom_fields->bind ( $_POST );
		$sql = $custom_fields->store ( $obj->project_id ); // Store Custom Fields
		/*if (sizeof ( $_FILES ) > 0 && $_FILES ['formlogo'] ['name'] != "" ) {
			$imgpath=$dPconfig['root_dir']."/images/logos/";
			$file_once = true;
			$temp_file_name = $_FILES ['formlogo'] ['tmp_name'];
			$user_file_name = $_FILES ['formlogo'] ['name'];
			$user_file_type = $_FILES ['formlogo'] ['type'];
			$user_file_size = $_FILES ['formlogo'] ['size'];
			
			if ($user_file_name == "" || ($temp_file_name == "" xor ! @is_uploaded_file ( $temp_file_name )) || (($user_file_size / 1024) > 30)) {
				1;
			} else {
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
				

				if (! move_uploaded_file ( $temp_file_name, $imgpath . $real_name )) {
					$dname = false;
				} else {
					$dname = true;
				}
				if ($dname) {
					$q = new DBQuery ( );
					$q->addTable ( "projects" );
					$q->addUpdate ( "project_logo", $real_name );
					$q->addWhere ( "project_id='" . $obj->project_id . "'" );
					$q->exec ();
					$q->clear ();
				}
			
			}
		}*/
		if((int)$_POST['project_file']  > 0){
			require_once($AppUI->getModuleClass("files"));
			$fobj = new CFile();
			$fobj->load((int)$_POST['project_file']);
			$fobj->file_project = $obj->project_id;
			$fobj->moveProjectStore();
			$fobj->store();
		}
		if (isset($_FILES['file']) && !$_FILES['file']['error'] && $_FILES['file']['name'] != '') {
			$file_id = intval(dPgetParam($_POST, 'file_id', 0));
			$upload = $_FILES['file'];
			//require './modules/files/files.class.php';
			global $db;
			$fobj = new CFile();
			$fobj->file_category		= 0;
			//$fobj->file_description		= $project['file_description'];
			$fobj->file_nick			= $_POST['file_nick'];
			$fobj->file_category		= $_POST['file_type'];
			$fobj->file_name			= $upload['name'];
			$fobj->file_type			= $upload['type'];
			$fobj->file_size			= $upload['size'];
			$fobj->file_date			= str_replace("'", '', $db->DBTimeStamp(time()));
			$fobj->file_view_date		= str_replace("'", '', $db->DBTimeStamp(time()));
			$fobj->file_real_filename	= uniqid(rand());
			$fobj->file_version_id		= getNextVersionID();
			$fobj->file_project			= $obj->project_id;
			$fobj->file_country			= 1;
			$fobj->file_owner			= $AppUI->user_id;
			$fobj->file_sector			= -1;
			$fobj->file_activity_type	= -1;
			$fobj->file_version			= 1;
			$fobj->moveTemp($upload);
			$fobj->store();
		}
		
		if($_POST['pro_activ'] != ''){
		/*
		Place to store activities saved with new project
		*/
			require_once($AppUI->getModuleClass("tasks"));
			$act_list = json_decode(stripslashes($_POST['pro_activ']),true);
			if(count($act_list) > 0){
				$tobj  = new CTask();
				foreach ($act_list as $aitem) {
					$tobj->bind($aitem);
					list($day, $month, $year) = explode('/', $tobj->task_start_date);
					$tobj->task_start_date = "{$year}-{$month}-{$day} 00:00:00";
					
					list($day, $month, $year) = explode('/', $tobj->task_end_date);
					$tobj->task_end_date = "{$year}-{$month}-{$day} 23:59:59";
					
					$tobj->task_project = $obj->project_id;
					$tobj->task_id = 0;
					
					/*if (is_array ( $_POST ['task_sector'] ) && count ( $_POST ['task_sector'] ) > 0) {
			$obj->task_sector = implode ( ',', $_POST ['task_sector'] );
		}else{
			$obj->task_sector = '';
		}*/			
					if (is_array ( $aitem ['task_sector'] ) && count ( $aitem ['task_sector'] ) > 0) {
						$tobj->task_sector = implode ( ',', $aitem ['task_sector'][0] );
					}else{
						$tobj->task_sector = '';
					}
					if (is_array ( $aitem ['task_agency']) && count ( $aitem ['task_agency']) > 0) {
						
						$tobj->task_agency = implode ( ',', $aitem ['task_agency'][0]);
					}else{
						$tobj->task_agency = '';
					}
					if (is_array ( $aitem ['task_type_of_beneficiery'] ) && count ( $aitem ['task_type_of_beneficiery'] ) > 0) {
						$tobj->task_type_of_beneficiery = implode ( ',', $aitem ['task_type_of_beneficiery'] );
					}else{
						$tobj->task_type_of_beneficiery = '';
					}
					
					$tobj->store();
					$nact = $tobj->task_id;
					$tobj->clean();
				}
                updateProjectTotals($obj->project_id);
			}
		}
	}
	$AppUI->redirect ("m=projects&a=view&project_id=".$obj->project_id);
}
?>