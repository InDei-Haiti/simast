<?php
GLOBAL $AppUI, $projects;

$perms = & $AppUI->acl ();

$is_superAdmin = false;
$roles = $perms->getUserRoles($AppUI->user_id);
foreach ($roles as $role){
	if($role['value']=='super_admin'){
		$is_superAdmin = true;
	}
}
$q = new DBQuery();
$q->addTable('projects');
$q->addQuery('project_id');
if(!$is_superAdmin){
	$q->addJoin('permission_form','pf', 'task_id=pf.form');
	$q->addWhere('pf.user_id='.$AppUI->user_id);
	$q->addWhere('pf.form=task_id');
	$q->addWhere('pf.module="projects"');
	$option = $perms->getAcoIdByValue('view');
	$q->addWhere('find_in_set("'.$option.'",replace(type, " ", ","))');
}
$q->limit = 1;
$q->order_by = 'project_name';

$tab1 = $q->loadResult();
$tabBox = new CTabBox('?m=ownpage', DP_BASE_DIR . '/modules/ownpage/', $tab1);



$q = new DBQuery();
$q->addTable('projects');
if(!$is_superAdmin){
	$q->addJoin('permission_form','pf', 'task_id=pf.form');
	$q->addWhere('pf.user_id='.$AppUI->user_id);
	$q->addWhere('pf.form=task_id');
	$q->addWhere('pf.module="projects"');
	$option = $perms->getAcoIdByValue('view');
	$q->addWhere('find_in_set("'.$option.'",replace(type, " ", ","))');
}
$q->order_by = 'project_name';
$projects = $q->loadList();
if($projects && is_array($projects)){
	foreach ($projects as $row) {
		$tabBox->add('ow_project', $row['project_name'],false, $row['project_id']);
	}
}
$tabBox->show();
?>