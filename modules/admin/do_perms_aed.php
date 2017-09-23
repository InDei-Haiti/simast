<?php /* ADMIN $Id: do_perms_aed.php 5722 2008-05-31 02:25:52Z ajdonnison $ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

$del = isset($_POST['del']) ? $_POST['del'] : 0;
$delf = isset($_POST['delf']) ? $_POST['delf'] : 0;
$obj =& $AppUI->acl();

$AppUI->setMsg( 'Permission' );

if ($del) {
	if (! $obj->checkModule($m, 'delete')) {
		$AppUI->redirect('m=public&a=access_denied');
	}
	if ($obj->del_acl($_REQUEST['permission_id'])) {
		$AppUI->setMsg( "deleted", UI_MSG_ALERT, true );
		$AppUI->redirect();
	} else {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
		$AppUI->redirect();
	}
}elseif($delf){
	//if (! $obj->checkModule($m, 'delete')) {
		//$AppUI->redirect('m=public&a=access_denied');
	//}
	//var_dump($_REQUEST);
	//echo 'DELETE FROM `permission_form` WHERE pf_id='.$_REQUEST['pf_id'];
	//exit;
	if (mysql_query('DELETE FROM `permission_form` WHERE pf_id='.$_REQUEST['pf_id'])) {
		$AppUI->setMsg( "deleted", UI_MSG_ALERT, true );
		$AppUI->redirect();
	} else {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
		$AppUI->redirect();
	}
} else {
	//var_dump($_POST);
	//exit;
	if($_POST['perms_form']){
		//var_dump($_POST);
		if($_POST['permission_type'] && count($_POST['permission_type'])>0){
			if($_POST['forms']!="-1" || $_POST['tasks']!="-1" || $_POST['projects']!="-1"){
				if($_POST['forms']!="-1"){
					if($_POST['forms']==='all'){
						if($_POST['tasks']==='all'){
							$q1 = new DBQuery();
							$q1->addTable('form_master');
							$q1->addQuery('id');
							$q1->addWhere('project_id='.$_POST['projects']);
							$listform = $q1->loadColumn();
							foreach ($listform as $form){
								$q1 = new DBQuery();
								$q1->addTable('permission_form');
								$q1->addQuery('pf_id');
								$q1->addWhere('user_id='.$_POST['user_id']);
								$q1->addWhere('form='.$form);
								$q1->addWhere('module="wizard"');
								$pf_id = $q1->loadResult();
								if($pf_id){
									$q = new DBQuery();
									$q->addTable('permission_form');
									$q->addUpdate('type', implode(',',$_POST['permission_type']));
									$q->addUpdate('status', $_POST['permission_access']);
									$q->addWhere('pf_id='.$pf_id);
									$query = $q->prepare();
									mysql_query($query);
								}else{
									$q = new DBQuery();
									$q->addTable('permission_form');
									$q->addInsert('user_id', $_POST['user_id']);
									$q->addInsert('form', $form);
									$q->addInsert('module', 'wizard');
									$q->addInsert('type', implode(',',$_POST['permission_type']));
									$q->addInsert('status', $_POST['permission_access']);
									$query = $q->prepare();
									mysql_query($query);
								}
							}
						}else{
							$q1 = new DBQuery();
							$q1->addTable('form_master');
							$q1->addQuery('id');
							$q1->addWhere('task_id='.$_POST['tasks']);
							$listform = $q1->loadColumn();
							foreach ($listform as $form){
								$q1 = new DBQuery();
								$q1->addTable('permission_form');
								$q1->addQuery('pf_id');
								$q1->addWhere('user_id='.$_POST['user_id']);
								$q1->addWhere('form='.$form);
								$q1->addWhere('module="wizard"');
								$pf_id = $q1->loadResult();
								if($pf_id){
									$q = new DBQuery();
									$q->addTable('permission_form');
									$q->addUpdate('type', implode(',',$_POST['permission_type']));
									$q->addUpdate('status', $_POST['permission_access']);
									$q->addWhere('pf_id='.$pf_id);
									$query = $q->prepare();
									mysql_query($query);
								}else{
									$q = new DBQuery();
									$q->addTable('permission_form');
									$q->addInsert('user_id', $_POST['user_id']);
									$q->addInsert('form', $form);
									$q->addInsert('module', 'wizard');
									$q->addInsert('type', implode(',',$_POST['permission_type']));
									$q->addInsert('status', $_POST['permission_access']);
									$query = $q->prepare();
									mysql_query($query);
								}
							}
						}
						
					}else{
						$q1 = new DBQuery();
						$q1->addTable('permission_form');
						$q1->addQuery('pf_id');
						$q1->addWhere('user_id='.$_POST['user_id']);
						$q1->addWhere('form='.$_POST['forms']);
						$q1->addWhere('module="wizard"');
						$pf_id = $q1->loadResult();
						if($pf_id){
							$q = new DBQuery();
							$q->addTable('permission_form');
							$q->addUpdate('type', implode(',',$_POST['permission_type']));
							$q->addUpdate('status', $_POST['permission_access']);
							$q->addWhere('pf_id='.$pf_id);
							$query = $q->prepare();
							mysql_query($query);
						}else{
							$q = new DBQuery();
							$q->addTable('permission_form');
							$q->addInsert('user_id', $_POST['user_id']);
							$q->addInsert('form', $_POST['forms']);
							$q->addInsert('module', 'wizard');
							$q->addInsert('type', implode(',',$_POST['permission_type']));
							$q->addInsert('status', $_POST['permission_access']);
							$query = $q->prepare();
							mysql_query($query);
						}
					}
				}elseif($_POST['tasks']!="-1"){
					if($_POST['tasks']==='all'){
						$q1 = new DBQuery();
						$q1->addTable('tasks');
						$q1->addQuery('task_id');
						$q1->addWhere('task_project='.$_POST['projects']);
						$listtask = $q1->loadColumn();
						foreach ($listtask as $task){
							$q1 = new DBQuery();
							$q1->addTable('permission_form');
							$q1->addQuery('pf_id');
							$q1->addWhere('user_id='.$_POST['user_id']);
							$q1->addWhere('form='.$task);
							$q1->addWhere('module="activity"');
							$pf_id = $q1->loadResult();
							if($pf_id){
								$q = new DBQuery();
								$q->addTable('permission_form');
								$q->addUpdate('type', implode(',',$_POST['permission_type']));
								$q->addUpdate('status', $_POST['permission_access']);
								$q->addWhere('pf_id='.$pf_id);
								$query = $q->prepare();
								mysql_query($query);
							}else{
								$q = new DBQuery();
								$q->addTable('permission_form');
								$q->addInsert('user_id', $_POST['user_id']);
								$q->addInsert('form', $task);
								$q->addInsert('module', 'activity');
								$q->addInsert('type', implode(',',$_POST['permission_type']));
								$q->addInsert('status', $_POST['permission_access']);
								$query = $q->prepare();
								mysql_query($query);
							}
						}
					}else{
					    $q1 = new DBQuery();
						$q1->addTable('permission_form');
						$q1->addQuery('pf_id');
						$q1->addWhere('user_id='.$_POST['user_id']);
						$q1->addWhere('form='.$_POST['tasks']);
						$q1->addWhere('module="activity"');
						$pf_id = $q1->loadResult();
						if($pf_id){
							$q = new DBQuery();
							$q->addTable('permission_form');
							$q->addUpdate('type', implode(',',$_POST['permission_type']));
							$q->addUpdate('status', $_POST['permission_access']);
							$q->addWhere('pf_id='.$pf_id);
							$query = $q->prepare();
							mysql_query($query);
						}else{
							$q = new DBQuery();
							$q->addTable('permission_form');
							$q->addInsert('user_id', $_POST['user_id']);
							$q->addInsert('form', $_POST['tasks']);
							$q->addInsert('module', 'activity');
							$q->addInsert('type', implode(',',$_POST['permission_type']));
							$q->addInsert('status', $_POST['permission_access']);
							$query = $q->prepare();
							mysql_query($query);
						}
					}
				}elseif($_POST['projects']!="-1"){
					$q1 = new DBQuery();
					$q1->addTable('permission_form');
					$q1->addQuery('pf_id');
					$q1->addWhere('user_id='.$_POST['user_id']);
					$q1->addWhere('form='.$_POST['projects']);
					$q1->addWhere('module="projects"');
					$pf_id = $q1->loadResult();
					if($pf_id){
						$q = new DBQuery();
						$q->addTable('permission_form');
						$q->addUpdate('type', implode(',',$_POST['permission_type']));
						$q->addUpdate('status', $_POST['permission_access']);
						$q->addWhere('pf_id='.$pf_id);
						$query = $q->prepare();
						mysql_query($query);
					}else{
						$q = new DBQuery();
						$q->addTable('permission_form');
						$q->addInsert('user_id', $_POST['user_id']);
						$q->addInsert('form', $_POST['projects']);
						$q->addInsert('module', 'projects');
						$q->addInsert('type', implode(',',$_POST['permission_type']));
						$q->addInsert('status', $_POST['permission_access']);
						$query = $q->prepare();
						mysql_query($query);
					}
				}
				
			}
		}
		$AppUI->redirect();
		//exit;
		//$AppUI->redirect('m=public&a=access_denied');
	}
	
	
	
	if (($isNotNew && ! $obj->checkModule($m, 'edit'))
	|| (!$isNotNew && ! $obj->checkModule($m, 'add'))) {
		$AppUI->redirect('m=public&a=access_denied');
	}
	if ($obj->addUserPermission()) {
		$AppUI->setMsg( $isNotNew ? 'updated' : 'added', UI_MSG_OK, true );
	} else {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
	}
	$AppUI->redirect();
}
?>
