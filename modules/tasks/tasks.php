<?php /* TASKS $Id: tasks.php 5730 2008-06-06 18:44:57Z merlinyoda $ */
if (! defined ( 'DP_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

GLOBAL $m, $a, $project_id, $f, $min_view, $query_string, $durnTypes, $selected_project_id, $sector,$pstatus;
GLOBAL $task_sort_item1, $task_sort_type1, $task_sort_order1;
GLOBAL $task_sort_item2, $task_sort_type2, $task_sort_order2;
GLOBAL $user_id, $dPconfig, $currentTabId, $currentTabName, $canEdit, $showEditCheckbox;
global $tasks_opened, $tasks_closed, $titleBlock;
global $adm_rights,$adwn,$top_parent,$topmode,$myspeed,$AppUI;

/*
 tasks.php
 
 This file contains common task list rendering code used by
 modules/tasks/index.php and modules/projects/vw_tasks.php
 
 in
 
 External used variables:
 
 * $min_view: hide some elements when active (used in the vw_tasks.php)
 * $project_id
 * $f
 * $query_string
*/

if (empty ( $query_string )) {
	$query_string = "?m=$m&a=$a";
}

// Number of columns (used to calculate how many columns to span things through)
$cols = 18;

/*
 * Let's figure out which tasks are selected
 */

if(!isset($adm_rights)){
	$adm_rights=$AppUI->isAdmin();
}

if($_GET['mode']='childs' && (int)$_GET['parid'] > 0){
	$topmode='ajax';
	$top_parent=(int)$_GET['parid'];
	$project_id = (int)$_GET['project_id'];
	$task_id = (int)$_GET['task_id'];
}else{
	$topmode='html';
	$top_parent=0;
}

$tasks_closed = (($AppUI->getState ( 'tasks_closed' )) ? $AppUI->getState ( 'tasks_closed' ) : array ());
$tasks_opened = (($AppUI->getState ( 'tasks_opened' )) ? $AppUI->getState ( 'tasks_opened' ) : array ());

$task_id = intval ( dPgetParam ( $_GET, 'task_id', 0 ) );
$pinned_only = intval ( dPgetParam ( $_GET, 'pinned', 0 ) );
if (isset ( $_GET ['pin'] )) {
	$pin = intval ( dPgetParam ( $_GET, 'pin', 0 ) );
	
	$msg = '';
	
	// load the record data
	$sql = (($pin) ? "INSERT INTO user_task_pin (user_id, task_id) VALUES($AppUI->user_id, $task_id)" : "DELETE FROM user_task_pin WHERE user_id=$AppUI->user_id AND task_id=$task_id");
	
	if (! db_exec ( $sql )) {
		$AppUI->setMsg ( 'ins/del err', UI_MSG_ERROR, true );
	}
	$AppUI->redirect ( '', - 1 );
}

if ($task_id > 0) {
	$_GET ['open_task_id'] = $task_id;
}

//save place is at end
//$AppUI->savePlace();


if(!$project_id && $task_id > 0){
	$q= new DBQuery();
	$q->addQuery('task_project,task_parent');
	$q->addTable('tasks');
	$q->addWhere('task_id = "'.(int)$task_id.' and task_parent<>task_id"');
	$rt=$q->loadHash();
	$parent_task=$rt['task_parent'];
	$rproject_id=$rt['task_project'];
	unset($q);
}

// shall all tasks be either opened or opened?
$open_task_all = dPGetParam ( $_GET, 'open_task_all', 0 );
$close_task_all = dPGetParam ( $_GET, 'close_task_all', 0 );
// Closing and opening tasks only applies to dynamic tasks
$open_task_id = dPGetParam ( $_GET, 'open_task_id', 0 );
$close_task_id = dPGetParam ( $_GET, 'close_task_id', 0 );

if ($open_task_all) {
	$tasks_opened = array_unique ( $tasks_closed );
	$tasks_closed = array ();
} else if ($close_task_all) {
	$tasks_closed = array_unique ( array_merge ( $tasks_closed, $tasks_opened ) );
	$tasks_opened = array ();
} else if ($open_task_id) {
	openClosedTask ( $open_task_id );
} else if ($close_task_id) {
	closeOpenedTask ( $close_task_id );
}

$durnTypes = dPgetSysVal ( 'TaskDurationType' );
$taskPriority = dPgetSysVal ( 'TaskPriority' );

$task_project = intval ( dPgetParam ( $_GET, 'task_project', null ) );
//$task_id = intval(dPgetParam($_GET, 'task_id', null));


$task_sort_item1 = dPgetParam ( $_GET, 'task_sort_item1', '' );
$task_sort_type1 = dPgetParam ( $_GET, 'task_sort_type1', 0 );
$task_sort_order1 = intval ( dPgetParam ( $_GET, 'task_sort_order1', 0 ) );
$task_sort_item2 = dPgetParam ( $_GET, 'task_sort_item2', '' );
$task_sort_type2 = dPgetParam ( $_GET, 'task_sort_type2', 0 );
$task_sort_order2 = intval ( dPgetParam ( $_GET, 'task_sort_order2', 0 ) );
if (isset ( $_POST ['show_task_options'] )) {
	$AppUI->setState ( 'TaskListShowIncomplete', dPgetParam ( $_POST, 'show_incomplete', 0 ) );
}
$showIncomplete = $AppUI->getState ( 'TaskListShowIncomplete', 0 );

require_once $AppUI->getModuleClass ( 'projects' );
$project = & new CProject ( );
$allowedProjects = $project->getAllowedSQL ( $AppUI->user_id );

if (count ( $allowedProjects )) {
	$where_list = implode ( ' AND ', $allowedProjects );
}

$working_hours = ($dPconfig ['daily_working_hours'] ? $dPconfig ['daily_working_hours'] : 8);

$q = new DBQuery ( );
$q->addTable ( 'projects' );
$q->addQuery ( 'company_name, project_id, project_color_identifier, project_name, ' . ' SUM(t1.task_duration * t1.task_percent_complete' . ' * IF(t1.task_duration_type = 24, ' . $working_hours . ', t1.task_duration_type))' . ' / SUM(t1.task_duration * IF(t1.task_duration_type = 24, ' . $working_hours . ', t1.task_duration_type)) AS project_percent_complete ' );
$q->addJoin ( 'companies', 'com', 'company_id = project_company' );
$q->addJoin ( 'tasks', 't1', 'projects.project_id = t1.task_project' );
$q->addWhere ( $where_list . (($where_list) ? ' AND ' : '') . 't1.task_id = t1.task_parent' );
$q->addGroup ( 'project_id' );
$q->addOrder ( 'project_name' );
$psql = $q->prepare ();
$q->clear ();

$q->addTable ( 'projects' );
$q->addQuery ( 'project_id, COUNT(t1.task_id) AS total_tasks' );
$q->addJoin ( 'tasks', 't1', 'projects.project_id = t1.task_project' );
if ($where_list) {
	$q->addWhere ( $where_list );
}
$q->addGroup ( 'project_id' );
$psql2 = $q->prepare ();
$q->clear ();

$perms = & $AppUI->acl ();

$is_superAdmin = false;
$roles = $perms->getUserRoles($AppUI->user_id);
foreach ($roles as $role){
	if($role['value']=='super_admin'){
		$is_superAdmin = true;
	}
}

$projects = array ();
$canViewTask = $perms->checkModule ( 'tasks', 'view' );
if ($canViewTask) {
	
	$prc = db_exec ( $psql );
	echo db_error ();
	while ( $row = db_fetch_assoc ( $prc ) ) {
		$projects [$row ['project_id']] = $row;
	}
	
	$prc2 = db_exec ( $psql2 );
	echo db_error ();
	while ( $row2 = db_fetch_assoc ( $prc2 ) ) {
		if ($projects [$row2 ['project_id']]) {
			array_push ( $projects [$row2 ['project_id']], $row2 );
		}
	}
}

$join = '';
// pull tasks
$select = ('distinct tasks.task_id, task_parent, task_name,
			 task_sector, task_type,
			 task_mode,task_start_date, task_end_date,
			 task_dynamic, task_pinned, pin.user_id as pin_user, 
			 task_priority, task_percent_complete, task_duration,
			 task_duration_type, task_project, task_description, 
			 task_status, usernames.user_username,
			 usernames.user_id, task_milestone, assignees.user_username as assignee_username, 
			 count(distinct assignees.user_id) as assignee_count, 
			 co.contact_first_name, co.contact_last_name, 
			 count(distinct files.file_task) as file_count, 
			 project_name,task_project,
			 if(tlog.task_log_problem IS NULL, 0, tlog.task_log_problem) AS task_log_problem');
 

$from = 'tasks';
$mods = $AppUI->getActiveModules ();
if (! empty ( $mods ['history'] ) && getPermission ( 'history', 'view' )) {
	$select .= ', MAX(history_date) as last_update';
	$join = "LEFT JOIN history ON history_item = tasks.task_id AND history_table='tasks' ";
}
$join .= 'LEFT JOIN projects ON project_id = task_project';
$join .= ' LEFT JOIN users as usernames ON task_owner = usernames.user_id';
// patch 2.12.04 show assignee and count
$join .= ' LEFT JOIN user_tasks as ut ON ut.task_id = tasks.task_id';
$join .= ' LEFT JOIN users as assignees ON assignees.user_id = ut.user_id';
$join .= ' LEFT JOIN contacts as co ON co.contact_id = usernames.user_contact';

// check if there is log report with the problem flag enabled for the task
$join .= (' LEFT JOIN task_log AS tlog ON tlog.task_log_task = tasks.task_id ' . 'AND tlog.task_log_problem > 0');

// to figure out if a file is attached to task
$join .= ' LEFT JOIN files on tasks.task_id = files.file_task';
$join .= ' LEFT JOIN user_task_pin as pin ON tasks.task_id = pin.task_id AND pin.user_id = ';
$join .= $user_id ? $user_id : $AppUI->user_id;

$where = $project_id ? ' task_project = ' . $project_id : 'project_status <> 7';
if($task_id > 0) {
	$where.=" AND task_parent='$task_id' and tasks.task_id<>'$task_id' " ;
}

if($topmode == 'ajax'){
	$where.=' AND task_parent="'.$top_parent.'" AND tasks.task_id<> '.$top_parent.'';
}elseif ($task_id == 0){
	$where.=' AND task_mode="root" ';
}


if ($pinned_only) {
	$where .= ' AND task_pinned = 1 ';
}



/*$f = (($f) ? $f : '');
$never_show_with_dots = array(/*'children', '' ); //used when displaying tasks

$tc = array ();
$tfrom = explode ( ",", $from );
foreach ( $tfrom as $tf ) {
	if (! in_array ( trim ( $tf ), $tc )) {
		$tc [] = $tf;
	}
}
$from = implode ( ",", $tc );

if ($project_id && $showIncomplete) {
	$where .= ' AND (task_percent_complete < 100 or task_percent_complete is null)';
}

$task_status = 0;
if ($min_view && isset ( $_GET ['task_status'] )) {
	$task_status = intval ( dPgetParam ( $_GET, 'task_status', null ) );
} else if (stristr ( $currentTabName, 'inactive' )) {
	$task_status = '-1';
} // If we aren't tabbed we are in the tasks list.
else if (! $currentTabName) {
	$task_status = intval ( $AppUI->getState ( 'inactive' ) );
}
*/
//$where .= ' AND task_status = ' . $task_status;


// patch 2.12.04 text search
/*if ($search_text = $AppUI->getState('searchtext')) {
	$where .= (" AND (task_name LIKE ('%{$search_text}%') " 
			   . "OR task_description LIKE ('%{$search_text}%'))");
}*/

// filter tasks considering task and project permissions


/*$projects_filter = '';
$tasks_filter = '';

// TODO: Enable tasks filtering


$allowedProjects = $project->getAllowedSQL ( $AppUI->user_id, 'task_project' );
if (count ( $allowedProjects )) {
	$where .= ' AND ' . implode ( ' AND ', $allowedProjects );
}

//
$obj = & new CTask ( );
$allowedTasks = $obj->getAllowedSQL ( $AppUI->user_id, 'tasks.task_id' );
if (count ( $allowedTasks )) {
	$where .= ' AND ' . implode ( ' AND ', $allowedTasks );
}
$allowedChildrenTasks = $obj->getAllowedSQL ( $AppUI->user_id, 'tasks.task_parent' );
if (count ( $allowedChildrenTasks )) {
	$where .= ' AND ' . implode ( ' AND ', $allowedChildrenTasks );
}
// echo "<pre>$where</pre>";


// Filter by company
if (! $min_view) {
	if (isset ( $f2 )) {
		if (count ( $f2 ) == 1 && (int)$f2 [0] > 0 ) {
			$join .= ' LEFT JOIN companies ON company_id = projects.project_company';
			$where .= ' AND company_id = ' . intval ( $f2 [0] );
			$where .= ' OR task_company = '.intval($f2[0]);
		} elseif (count ( $f2 ) > 1) {
			foreach ( $f2 as $fr ) {
				if ($fr > 0) {
					$fs .= ( int ) $fr . ",";
				}
			}
			if ($fs != "") {
				$fs = preg_replace ( "/\,$/", "", $fs );
				$join .= ' LEFT JOIN companies ON company_id = projects.project_company';
				$where .= ' AND company_id IN (' . $fs . ')';
				$where .= ' ANd task_company IN (' . $fs . ')';
			}
		}
	}
}

if (isset ( $f ) && $f != "") {
	if(count($f) == 1 && $f[0] == "children"){
		$task_child_search = new CTask ( );
		$task_child_search->peek ( $task_id );
		$childrenlist = $task_child_search->getDeepChildren ();
		$where .= ' AND tasks.task_id IN (' . implode ( ',', $childrenlist ) . ')';
	}
	elseif (count ( $f ) == 1 && $f [0] != '-1' && $f[0] > 0) {
		//$join .= ' LEFT JOIN companies ON company_id = projects.project_company';
		$where .= ' AND task_country = ' . intval ( $f [0] );
	} elseif (count ( $f ) > 1) {
		foreach ( $f as $fr ) {
			if ($fr > 0 ) {
				$fs .= ( int ) $fr . ",";
			}
		}
		if ($fs != "") {
			$fs = preg_replace ( "/\,$/", "", $fs );
			//$join .= ' LEFT JOIN companies ON company_id = projects.project_company';
			$where .= ' AND task_country IN (' . $fs . ')';
		}
	}
}
//filter by sector
if ($sector) {
	if (count ( $sector ) == 1 && $sector [0] > - 1) {
		$where .= ' AND tasks.task_sector = ' . intval ( $sector [0] );
	} elseif (count ( $sector ) > 1) {
		foreach ( $sector as $sec ) {
			if ($sec > 0) {
				$swer .= $sec . ",";
			}
		}
		if ($swer != "") {
			$swer = preg_replace ( "/\,$/", "", $swer );
			$where .= ' AND tasks.task_sector IN (' . $swer . ")";
		}
	}
}

//filter by projects
if ($selected_project_id) {
	if (count ( $selected_project_id ) == 1 && ( int ) $selected_project_id [0] > 0) {
		$where .= ' AND projects.project_id = ' . intval ( $selected_project_id [0] );
	} elseif (count ( $selected_project_id ) > 1) {
		foreach ( $selected_project_id as $sp ) {
			if (( int ) $sp > 0)
				$sst .= ( int ) $sp . ",";
		}
		if ($sp != "") {
			$sst = preg_replace ( "/\,$/", "", $sst );
			$where .= ' AND projects.project_id IN ( ' . mysql_real_escape_string ( $sst ) . ")";
		}
	}
}*/

//var_dump($selected_project_id);
//var_dump($sector);
//echo "<pre>$where</pre>";
// patch 2.12.04 ADD GROUP BY clause for assignee count
//$tsql = ('SELECT ' . $select . ' FROM (' . $from . ') ' . $join . ' WHERE ' . $where . ' GROUP BY task_id ORDER BY project_id, task_start_date');

//$tsql = ('SELECT ' . $select . ' FROM (' . $from . ') ' . $join . ' WHERE ' . $where . ' GROUP BY task_id ORDER BY task_name, task_start_date');
//$tsql = ('SELECT * FROM (' . $from . ')  WHERE task_project='.$project_id);

//echo "<pre>$tsql</pre>";


/* if ($canViewTask) {
	$ptrc = db_exec ( $tsql );
	if ($ptrc != false) {
		$nums = db_num_rows ( $ptrc );
		
	}
	echo db_error ();
} else {
	$nums = 0;
}
echo 'Numbre :'.$nums; */

//$ptrc = db_exec ( $tsql );

//echo $ptrc;

$q = new DBQuery();

$q->addTable($from);
$q->addQuery('task_id');
$q->addWhere('task_project='.$project_id);

if(!$is_superAdmin){
	$q->addJoin('permission_form','pf', 'task_id=pf.form');
	$q->addWhere('pf.user_id='.$AppUI->user_id);
	$q->addWhere('pf.form=task_id');
	$q->addWhere('pf.module="activity"');
	$option = $perms->getAcoIdByValue('view');
	$q->addWhere('find_in_set("'.$option.'",replace(type, " ", ","))');
}


$sql = $q->prepare ();
$qid = db_exec ( $sql );
$count = db_num_rows ( $qid );
$rows = $q->loadList ();




//pull the tasks into an array
/*
for ($x=0; $x < $nums; $x++) {
	$row = db_fetch_assoc($ptrc);
	$projects[$row['task_project']]['tasks'][] = $row;
}
*/
/*$adwn= $a;

$ttasks = array();
for($x = 0; $x < $nums; $x ++) {
	$row = db_fetch_assoc ( $ptrc );
	
	//add information about assigned users into the page output
	$ausql = ('
			SELECT ut.user_id, u.user_username, contact_email, 
				ut.perc_assignment, SUM(ut.perc_assignment) AS assign_extent, 
				contact_first_name, contact_last_name 
			FROM user_tasks ut LEFT JOIN users u ON u.user_id = ut.user_id 
			LEFT JOIN contacts ON u.user_contact = contact_id 
			WHERE ut.task_id=' . $row ['task_id'] . ' 
			GROUP BY ut.user_id 
			ORDER BY ut.perc_assignment desc, u.user_username');
	
	$assigned_users = array ();
	$paurc = db_exec ( $ausql );
	$nnums = db_num_rows ( $paurc );
	echo db_error ();
	for($xx = 0; $xx < $nnums; $xx ++) {
		$row ['task_assigned_users'] [] = db_fetch_assoc ( $paurc );
	}
	//pull the final task row into array
	//$projects [$row ['task_project']] ['tasks'] [] = $row;
	$ttasks[] = $row;
	
	
}*/

$showEditCheckbox = ((isset ( $canEdit ) && $canEdit && $dPconfig ['direct_edit_assignment']) ? true : false);
if($topmode == 'html'){
?>

<script type="text/JavaScript">
function toggle_users(id){
  var element = document.getElementById(id);
  element.style.display = (element.style.display == '' || element.style.display == "none") ? "inline" : "none";
}

<?php
// security improvement:
// some javascript functions may not appear on client side in case of user not having write permissions
// else users would be able to arbitrarily run 'bad' functions
if (isset ( $canEdit ) && $canEdit && $dPconfig ['direct_edit_assignment']) {
	?>
function checkAll(project_id) {
	var f = eval('document.assFrm' + project_id);
	var cFlag = f.master.checked ? false : true;
	
	for (var i=0;i< f.elements.length;i++) {
		var e = f.elements[i];
		// only if it's a checkbox.
		if(e.type == "checkbox" && e.checked == cFlag && e.name != 'master') {
			e.checked = !e.checked;
		}
	}

}

function chAssignment(project_id, rmUser, del) {
	var f = eval('document.assFrm' + project_id);
	var fl = f.add_users.length-1;
	var c = 0;
	var a = 0;
	
	f.hassign.value = "";
	f.htasks.value = "";
	
	// harvest all checked checkboxes (tasks to process)
	for (var i=0;i< f.elements.length;i++) {
		var e = f.elements[i];
		// only if it's a checkbox.
		if(e.type == "checkbox" && e.checked == true && e.name != 'master') {
			c++;
			f.htasks.value = f.htasks.value +", "+ e.value;
		}
	}
	
	// harvest all selected possible User Assignees
	for (fl; fl > -1; fl--) {
		if (f.add_users.options[fl].selected) {
			a++;
			f.hassign.value = ", " + f.hassign.value +", "+ f.add_users.options[fl].value;
		}
	}
	
	if (del == true) {
		if (c == 0) {
			alert ('<?php
	echo $AppUI->_ ( 'Please select at least one Activity!', UI_OUTPUT_JS );
	?>');
		} 
		else if (a == 0 && rmUser == 1){
			alert ('<?php
	echo $AppUI->_ ( 'Please select at least one Assignee!', UI_OUTPUT_JS );
	?>');
		} 
		else if (confirm('<?php
	echo $AppUI->_ ( 'Are you sure you want to unassign the User from Activity(ies)?', UI_OUTPUT_JS );
	?>')) {
			f.del.value = 1;
			f.rm.value = rmUser;
			f.project_id.value = project_id;
			f.submit();
		}
	}
	else {
		
		if (c == 0) {
			alert ('<?php
	echo $AppUI->_ ( 'Please select at least one Activity!', UI_OUTPUT_JS );
	?>');
		} 
		else if (a == 0) {
			alert ('<?php
	echo $AppUI->_ ( 'Please select at least one Assignee!', UI_OUTPUT_JS );
	?>');
		} 
		else {
			f.rm.value = rmUser;
			f.del.value = del;
			f.project_id.value = project_id;
			f.submit();
		}
	}
}
<?php
}
?>
</script>


<?php
function getStrategies($strategies){
	$rep = '';
	if($strategies && is_array($strategies)){
		$strategies = implode(',', $strategies);
		if($strategies){
			$q  =new DBQuery();
			$q->addTable('st_area');
			$q->addWhere('id  in ('.$strategies.')');
			//$q->addQuery('concat(prex," ",title) as name');
			$q->addQuery('id, prex,title');
			//var_dump($q->loadList());
			//$area_list = @join(", ",array_keys(@$q->loadList()));
			$strategies = @$q->loadList();
			if(count($strategies)>0){
				$area_l_h = array();
				foreach ($strategies as $area){
					$area_l_h[] = '<span title="'.$area['title'].'">'.$area['prex'].'</span>';
				}
				$area_l_h = @join(", ",$area_l_h);
				$rep = $area_l_h;
			}
		}
		
	}
	return $rep;
}
if ($project_id) {
	?>

   <!-- <div class="row">
        <div class="col-md-2">
            <br/>
            <form name='task_list_options' method='POST'
                  action='<?php
/*                  echo $query_string;
                  */?>'><input type='hidden' name='show_task_options' value='1'>


                <?php
/*                        if ($_GET ['m'] == "projects" && $_GET ['a'] == "view" && $_GET ['project_id'] > 0 && $canEdit) {
                            /*$titleBlock->addCrumb ('?m=tasks&a=addedit&task_project=' . $project_id , $AppUI->_('new activity') );
                            $titleBlock->show();*/

                            /*echo "<a href='?m=tasks&a=addedit&task_project=" . $project_id . "' class=\"ce pi ahr btn\"><b>".$AppUI->_('New Activity')."</b></a>&nbsp;&nbsp;";*/

                            //<a href='?m=tasks&a=organize&project_id=" . $project_id . "'><b>Organize Activities</b></a>";
                        //}
                        //echo '<input type="hidden" name="type" value="activity"/><input type="hidden" name="query" value="all"/><input type="submit">';
                        //echo "<a href='?m=outputs&a=map&type=activity&query=all'><b>Map</b></a>&nbsp;&nbsp;";
                        ?>
            </form>
            <br/>
        </div>
    </div>-->

    </div>
<?php
}elseif($canEdit && $m=='tasks' && $a=='view' && $rproject_id > 0 && $parent_task > 0) {
	//http://dotp/rdd/index.php?m=tasks&a=addedit&task_id=43
	/*echo '<a href="?m=tasks&a=addedit&task_id='.$parent_task.'&pmode=newsub"><b>New Sub-Activity</b></a>';*/
}
$sector_list = dPgetSysVal('SectorType');

?>
<div class="mtab">
    <table width="100%" border="0" cellpadding="2" cellspacing="1">
	<thead>
		<tr>
			<!--<th width="1%">&nbsp;</th>-->
			<th><?php 	echo $AppUI->_ ( 'Activity Name' )?></th>
			<th ><?php 	echo $AppUI->_ ( 'Sector' )?> </th>
			<th nowrap="nowrap"><?php
			echo $AppUI->_ ( 'Partner Agencies' )?></th>
			<th nowrap="nowrap"><?php
			echo $AppUI->_ ( 'Strategic Areas' )?></th>
			<!-- <th nowrap="nowrap"><?php
			echo $AppUI->_ ( 'Locations' )?></th> -->
			<th nowrap="nowrap"><?php
			echo $AppUI->_ ( 'Start Date' );?></th>
			<th nowrap="nowrap"><?php
			echo $AppUI->_ ( 'End Date' ); ?></th>
			<th nowrap="nowrap"><?php
			echo $AppUI->_ ( 'Status' )?></th>
			</tr>
	</thead>
	<tbody>
		<?php
		if(count($rows)){

			foreach ( $rows as $rid =>  $row ) {
				$obj = new CTask();
				$obj->load ( $row ["task_id"] );
				echo '<tr>';
				/*echo '<td>&nbsp;</td>';*/
				echo '<td><a href="'. $obj->getUrl('view')   . '&tab=0">'.$obj->task_name.'</a></td>';
				echo '<td>'.multiView($sector_list,$obj->task_sector).'</td>';
				$obj->task_agency = explode(',', $obj->task_agency);
				if($obj->task_agency && is_array($obj->task_agency) && count($obj->task_agency)>0){
					$obj->task_agency = implode(',', $obj->task_agency);
					if($obj->task_agency){
						$q = new DBQuery();
						$q->addTable ( 'companies' );
						$q->addQuery ( 'company_acronym' );
						$q->addWhere ( 'company_id in ('.$obj->task_agency .')' );
						$obj->task_agency = $q->loadColumn();
						$obj->task_agency  = implode(", ", $obj->task_agency);
					}
				}else{
					$obj->task_agency = "";
				}
				echo '<td>'.$obj->task_agency.'</td>';
				echo '<td>'.getStrategies(explode(',', $obj->task_areas)).'</td>';
				//echo '<td>'.$obj->task_locations.'</td>';
				echo '<td>'.$obj->task_start_date.'</td>';
				echo '<td>'.$obj->task_end_date.'</td>';
				echo '<td>'.$AppUI->_ ( $pstatus [$obj->task_status] ).'</td>';
				echo '</tr>';
			}
		}
		?>
	</tbody>
    </table>
</div>
<?php
}
?>