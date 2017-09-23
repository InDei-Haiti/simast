<?php /* TASKS $Id: tasks.php 5730 2008-06-06 18:44:57Z merlinyoda $ */
if (! defined ( 'DP_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

GLOBAL $m, $a, $project_id, $f, $min_view, $query_string, $durnTypes, $selected_project_id, $sector;
GLOBAL $task_sort_item1, $task_sort_type1, $task_sort_order1;
GLOBAL $task_sort_item2, $task_sort_type2, $task_sort_order2;
GLOBAL $user_id, $dPconfig, $currentTabId, $currentTabName, $canEdit, $showEditCheckbox;
global $tasks_opened, $tasks_closed;

$perms = & $AppUI->acl ();
$canViewTask = $perms->checkModule ( 'tasks', 'view' );

$project = & new CProject ( );
$allowedProjects = $project->getAllowedSQL ( $AppUI->user_id );

$join = '';
// pull tasks
$select = ('distinct tasks.task_id, task_parent, task_name,
					 task_sector, task_type, task_country, 
					 task_admin2, task_admin3,task_admin4,
					 task_admin5,task_admin6,task_mode, 
					 task_other_location, task_gps_x, 
					 task_gps_y,task_start_date, task_end_date,
					 task_dynamic, task_pinned, pin.user_id as pin_user,
					 task_priority,task_percent_complete, task_duration, 
					 task_duration_type, task_project,task_description, task_areas,
					 task_owner, task_status, usernames.user_username,
					 usernames.user_id, task_milestone, assignees.user_username as assignee_username,
					 count(distinct assignees.user_id) as assignee_count,
					 co.contact_first_name, co.contact_last_name,
					 count(distinct files.file_task) as file_count,
					 if(tlog.task_log_problem IS NULL, 0, tlog.task_log_problem) AS task_log_problem');
$from = 'tasks';
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

//$where = $project_id ? ' task_project = ' . $project_id : 'project_status <> 7';
$where.=" task_company='$company_id'";

$f = (($f) ? $f : '');
$never_show_with_dots = array(/*'children', */'' ); //used when displaying tasks

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

//$where .= ' AND task_status = ' . $task_status;


// patch 2.12.04 text search
/*if ($search_text = $AppUI->getState('searchtext')) {
	$where .= (" AND (task_name LIKE ('%{$search_text}%') " 
			   . "OR task_description LIKE ('%{$search_text}%'))");
}*/

// filter tasks considering task and project permissions
$projects_filter = '';
$tasks_filter = '';

// TODO: Enable tasks filtering


/*

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

/*
// Filter by company
if (! $min_view) {
	if (isset ( $f2 )) {
		if (count ( $f2 ) == 1 && $f2 [0] != "all") {
			$join .= ' LEFT JOIN companies ON company_id = projects.project_company';
			$where .= ' AND company_id = ' . intval ( $f2 [0] );
		} elseif (count ( $f2 ) > 1) {
			foreach ( $f2 as $fr ) {
				if ($fr != "all") {
					$fs .= ( int ) $fr . ",";
				}
			}
			if ($fs != "") {
				$fs = preg_replace ( "/\,$/", "", $fs );
				$join .= ' LEFT JOIN companies ON company_id = projects.project_company';
				$where .= ' AND company_id IN (' . $fs . ')';
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
	elseif (count ( $f ) == 1 && $f [0] != "all") {
		//$join .= ' LEFT JOIN companies ON company_id = projects.project_company';
		$where .= ' AND task_country = ' . intval ( $f [0] );
	} elseif (count ( $f ) > 1) {
		foreach ( $f as $fr ) {
			if ($fr != "all") {
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
}
*/
//var_dump($selected_project_id);
//var_dump($sector);
//echo "<pre>$where</pre>";
// patch 2.12.04 ADD GROUP BY clause for assignee count
$tsql = ('SELECT ' . $select . ' FROM (' . $from . ') ' . $join . ' WHERE ' . $where . ' GROUP BY task_id ORDER BY project_id, task_start_date');

//echo "<pre>$tsql</pre>";


if ($canViewTask) {
	$ptrc = db_exec ( $tsql );
	if ($ptrc != false) {
		$nums = db_num_rows ( $ptrc );
	}
	echo db_error ();
} else {
	$nums = 0;
}

//pull the tasks into an array
/*
for ($x=0; $x < $nums; $x++) {
	$row = db_fetch_assoc($ptrc);
	$projects[$row['task_project']]['tasks'][] = $row;
}
*/
for($x = 0; $x < $nums; $x ++) {
	$row = db_fetch_assoc ( $ptrc );
	
	//add information about assigned users into the page output
	$ausql = ('SELECT ut.user_id, u.user_username, contact_email, ut.perc_assignment, ' . 'SUM(ut.perc_assignment) AS assign_extent, contact_first_name, contact_last_name ' . 'FROM user_tasks ut LEFT JOIN users u ON u.user_id = ut.user_id ' . 'LEFT JOIN contacts ON u.user_contact = contact_id ' . 'WHERE ut.task_id=' . $row ['task_id'] . ' GROUP BY ut.user_id ' . 'ORDER BY ut.perc_assignment desc, u.user_username');
	
	$assigned_users = array ();
	$paurc = db_exec ( $ausql );
	$nnums = db_num_rows ( $paurc );
	echo db_error ();
	for($xx = 0; $xx < $nnums; $xx ++) {
		$row ['task_assigned_users'] [] = db_fetch_assoc ( $paurc );
	}
	//pull the final task row into array
	$projects [$row ['task_project']] ['tasks'] [] = $row;
}

$min_view=true;
$showEditCheckbox = ((isset ( $canEdit ) && $canEdit && $dPconfig ['direct_edit_assignment']) ? true : false);

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
if ($project_id) {
	?>
<table width='100%' border='0' cellpadding='1' cellspacing='0'>
	<form name='task_list_options' method='POST'
		action='<?php
	echo $query_string;
	?>'><input type='hidden' name='show_task_options' value='1'>
	
	
	<tr>
		<td><?php
	if ($_GET ['m'] == "projects" && $_GET ['a'] == "view" && $_GET ['project_id'] > 0) {
		echo "<a href='?m=tasks&a=addedit&task_project=" . $project_id . "'><b>New Activity</b></a>";
				// <a href='?m=tasks&a=organize&project_id=" . $project_id . "'><b>Organize Activities</b></a>";
	}
	
	?>
	</td>
		<td align='right'>
		<!-- <table>
			<tr>
				<td><?php
	echo $AppUI->_ ( 'Show' );
	?>:</td>
				<td><input type="checkbox" name="show_incomplete"
					id="show_incomplete" onclick="document.task_list_options.submit();"
					<?php
	echo $showIncomplete ? 'checked="checked"' : '';
	?> /></td>
				<td><label for="show_incomplete"><?php
	echo $AppUI->_ ( 'Incomplete Activities Only' );
	?></label></td>
			</tr>
		</table>
		-->
		</td>
	</tr>
	</form>
</table>
<?php
}
?>
<table width="100%" border="0" cellpadding="2" cellspacing="1"
	class="tbl">
	<tr>
		<th width="17px">&nbsp;</th>
		<th><?php /*sort_by_item_title('Activity Name', 'task_name', SORT_STRING);*/		echo $AppUI->_ ( 'Activity Name' );
		?></th>
		<!-- <th nowrap="nowrap"><?php
		echo $AppUI->_ ( 'Activity Type' )?></th> -->
		<th nowrap="nowrap"><?php /*sort_by_item_title('Sector', 'activity_sector', SORT_STRING);*/		echo $AppUI->_ ( 'Sector' );
		?></th>
		<!-- <th nowrap="nowrap"><?php
		echo $AppUI->_ ( 'Agency' )?></th> -->
		<th nowrap="nowrap"><?php
		echo $AppUI->_ ( 'Str. Area' )?></th>
		<th nowrap="nowrap"><?php
		echo $AppUI->_ ( 'Locations' )?></th>
		<th nowrap="nowrap"><?php
		echo $AppUI->_ ( 'Start Date' ); /*sort_by_item_title('Start Date', 'task_start_date', SORT_NUMERIC);*/		?></th>
		
		<th nowrap="nowrap"><?php
		echo $AppUI->_ ( 'End Date' ); /*sort_by_item_title('Finish Date', 'task_end_date', SORT_NUMERIC);*/		?></th>
		<th nowrap="nowrap"><?php
		echo $AppUI->_ ( 'Status' )?></th>
		<!-- <th nowrap="nowrap"><?php
		echo $AppUI->_ ( 'Progress' )?></th> -->
<?php
if (! empty ( $mods ['history'] ) && getPermission ( 'history', 'view' )) {
	?>
  <th nowrap="nowrap"><?php
	echo $AppUI->_ ( 'Last Update' ); /*sort_by_item_title('Last Update', 'last_update', SORT_NUMERIC);*/	?></th>
<?php
} else {
	$cols --;
}
?>
  <?php
		if ($showEditCheckbox) {
			echo '<th width="1">&nbsp;</th>';
		} else {
			$cols --;
		}
		?>
</tr>
<?php
@reset ( $projects );

global $pbox_class;
$activity_types = dPgetSysVal('TaskType');
if(is_array($projects) && count($projects) > 0){
foreach ( $projects as $k => $p ) {
	$tnums = count ( @$p ['tasks'] );
	$pbox_class = "pbox_" . $p ['project_id'];
	// don't show project if it has no tasks
	// patch 2.12.04, show project if it is the only project in view
	if ($tnums > 0 || $project_id == $p ['project_id']) {
		//echo '<pre>'; print_r($p); echo '</pre>';
		if (! $min_view) {
			// not minimal view
			?>
<form name="assFrm<?php echo ($p ['project_id'])?>" action="index.php?m=<?php echo ($m); ?>&a=<?php echo ($a); ?>" method="post">
			<input type="hidden" name="del" value="1" /> 
			<input type="hidden" name="rm" value="0" /> 
			<input type="hidden" name="store" value="0" /> 
			<input type="hidden" name="dosql" value="do_task_assign_aed" /> 
			<input type="hidden" name="project_id" value="<?php echo ($p ['project_id']); ?>" /> 
			<input type="hidden" name="hassign" /> <input type="hidden" name="htasks" />	
	<tr>
		<td><a href="#" onclick="make_box('<?php echo $pbox_class; ?>');"> 
			<img src="./images/icons/expand.gif" width="16" height="16" border="0" id="fimg_<?php echo $p ['project_id']; ?>" alt="<?php echo (($project_id) ? $AppUI->_ ( 'show other projects' ) : $AppUI->_ ( 'show only this project' ));?>"> </a></td>
		<td colspan="<?php echo $dPconfig ['direct_edit_assignment'] ? $cols - 4 : $cols - 1; ?>">
		<table width="100%" border="0">
			<tr>
				<!-- patch 2.12.04 display company name next to project name -->
				<td nowrap style="border: outset black 2px;>
				<a href="./index.php?m=projects&a=view&project_id=<?php echo $k; ?>"> 
					<span><strong><?php
			echo @$p ['company_name'] . ' :: ' . @$p ['project_name'];
			?></strong></span></a></td>
				<td width="<?php echo (101 - intval ( @$p ['project_percent_complete'] ));?>%">
	<?php
			?>
	</td>
			</tr>
		</table>
		</td>
<?php
			if ($dPconfig ['direct_edit_assignment']) {
				// get Users with all Allocation info (e.g. their freeCapacity)
				$tempoTask = new CTask ( );
				$userAlloc = $tempoTask->getAllocation ( 'user_id' );
				?>
  <td colspan="3" align="right" valign="middle">
		<table width="100%" border="0">
			<tr>
				<td align="right"><select name="add_users" style="width: 200px"
					size="2" multiple="multiple" class="text"
					ondblclick="javascript:chAssignment(<?php
				echo ($p ['project_id']);
				?>, 0, false)">
<?php
				foreach ( $userAlloc as $v => $u ) {
					echo '	  <option value="' . $u ['user_id'] . '">' . dPformSafe ( $u ['userFC'] ) . "</option>\n";
				}
				?>
	</select></td>
				<td align="center">
<?php
				echo ("	 <a href='javascript:chAssignment({$p['project_id']}, 0, 0);'>" . dPshowImage ( dPfindImage ( 'add.png', 'tasks' ), 16, 16, 'Assign Users', 'Assign selected Users to selected Activities' ) . "</a>\n");
				
				echo ("	  <a href='javascript:chAssignment({$p['project_id']}, 1, 1);'>" . dPshowImage ( dPfindImage ( 'remove.png', 'tasks' ), 16, 16, 'Unassign Users', 'Unassign Users from Activity' ) . "</a>\n");
				
				?>
	<br />

				<select class="text" name="percentage_assignment"
					title="<?php
				echo ($AppUI->_ ( 'Assign with Percentage' ));
				?>">
<?php
				for($i = 0; $i <= 100; $i += 5) {
					echo ("\t<option " . (($i == 30) ? 'selected="true"' : '') . ' value="' . $i . '">' . $i . '%</option>');
				}
				?>
	</select></td>
			</tr>
		</table>
		</td>
<?php
			}
			?>
</tr>
<?php
		}
		
		if ($task_sort_item1 != '') {
			if ($task_sort_item2 != '' && $task_sort_item1 != $task_sort_item2) {
				$p ['tasks'] = array_csort ( $p ['tasks'], $task_sort_item1, $task_sort_order1, $task_sort_type1, $task_sort_item2, $task_sort_order2, $task_sort_type2 );
			} else {
				$p ['tasks'] = array_csort ( $p ['tasks'], $task_sort_item1, $task_sort_order1, $task_sort_type1 );
			}
		} else {
			/* we have to calculate the end_date via start_date+duration for 
			 ** end='0000-00-00 00:00:00' if array_csort function is not used
			 ** as it is normally done in array_csort function in order to economise
			 ** cpu time as we have to go through the array there anyway
			 */
			if (is_array ( $p ['tasks'] )) {
				foreach ( $p ['tasks'] as $j => $task_change_end_date ) {
					if ($task_change_end_date ['task_end_date'] == '0000-00-00 00:00:00') {
						$task_change_end_date ['task_end_date'] = calcEndByStartAndDuration ( $task_change_end_date );
					}
				}
			}
		}
		
		global $tasks_filtered, $children_of;
		//get list of task ids and set-up array of children
		if (is_array ( $p ['tasks'] )) {
			foreach ( $p ['tasks'] as $i => $t ) {
				$tasks_filtered [] = $t ['task_id'];
				$children_of [$t ['task_parent']] = (($children_of [$t ['task_parent']]) ? $children_of [$t ['task_parent']] : array ());
				if ($t ['task_parent'] != $t ['task_id']) {
					array_push ( $children_of [$t ['task_parent']], $t ['task_id'] );
				}
			}
		}
		
		//start displaying tasks
		

		if (is_array ( $p ['tasks'] )) {
			foreach ( $p ['tasks'] as $i => $t1 ) {
				if ($task_sort_item1) {
					// already user sorted so there is no call for a "task tree" or "open/close" links
					showtask ( $t1, - 1, true, false, true, $pbox_class );
				} else {
					if ($t1 ['task_parent'] == $t1 ['task_id']) {
						$is_opened = (! ($t1 ['task_dynamic']) || ! (in_array ( $t1 ['task_id'], $tasks_closed )));
						
						//check for child
						$no_children = empty ( $children_of [$t1 ['task_id']] );
						
						showtask ( $t1, 0, $is_opened, false, $no_children, $pbox_class );
						if ($is_opened && ! ($no_children)) {
							findchild ( $p ['tasks'], $t1 ['task_id'] );
						}
					} else if (! (in_array ( $t1 ['task_parent'], $tasks_filtered ))) {
						/*
						 * don't "mess with" display when showing certain views 
						 * (or similiar filters that don't involve "breaking apart" a task tree 
						 * even though they might not use this page ever)
						 */
						if ((in_array ( $f, $never_show_with_dots ))) {
							showtask ( $t1, 1, true, false, true, $pbox_class );
						} else {
							//display as close to "tree-like" as possible
							$is_opened = (! ($t1 ['task_dynamic']) || ! (in_array ( $t1 ['task_id'], $tasks_closed )));
							
							//check for child
							$no_children = empty ( $children_of [$t1 ['task_id']] );
							
							$my_level = (($task_id && $t1 ['task_parent'] == $task_id) ? 0 : - 1);
							showtask ( $t1, $my_level, $is_opened, false, $no_children, $pbox_class ); // indeterminate depth for child task
							if ($is_opened && ! ($no_children)) {
								findchild ( $p ['tasks'], $t1 ['task_id'] );
							}
						}
					}
					/*
					 * MerlinYoda: Not 100% sure if moving code from below to above always puts orphan trees 
					 * closer to their ancestors. At worst it just displays orphans a little earlier
					 */
				}
			}
		}
		

	}
}
}

$AppUI->setState ( 'tasks_opened', $tasks_opened );
$AppUI->setState ( 'tasks_closed', $tasks_closed );

$AppUI->savePlace ();

?>
</table>

<?php

//echo '<pre>Opened ::'; print_r($tasks_opened); echo '</pre><br />';
//echo '<pre>Closed ::'; print_r($tasks_closed); echo '</pre><br />';


?>
<script>



function make_pre(way){
	var timg= document.createElement('img');
	timg.src='./images/icons/fplus.gif';
	timg.width="12";
	timg.height="12";
	timg.style.border="0";
	var chld = new Array();
	$j("tr.[class^='rowc_']").each( function(){
			var rt = $j(this).attr("class");
			rt=rt.replace("rowc_","");
			rt=parseInt(rt);
			chld[rt]=1;
		})
		.hide();
	$j("span.[id^='fon_']").
	each(function(){
		var st= this.id;
		st=st.replace("fon_","");
		if(chld[st] > 0){
			$j(this).append( $j(timg).clone(true))
				.click(function(){
					make_click(this);
				});
		}
	} );
	if(way == "tpov"){
		$j("tr.[class*='pbox_']").hide();
	}
}

function make_click(rpar){
	var pid=rpar.id;
	pid=pid.replace("fon_","");
	pid=parseInt(pid);
	$j("#fon_" + pid + " > img").attr("src", function(){
		var isrc=$j(this).attr("src");
		if(isrc == './images/icons/fplus.gif'){
			return "./images/icons/fminus.gif";
		}else{
			return "./images/icons/fplus.gif";
		}
	} );
	$j("tr.rowc_"+pid).toggle();
}

function make_box(pcl){
	var imid=pcl.replace("pbox_","");
	$j("#fimg_"+imid).attr("src",function (){
		var isrc=$j(this).attr("src");
		if(isrc == './images/icons/expand.gif'){
			return "./images/icons/collapse.gif";
		}else{
			return "./images/icons/expand.gif";
		}
	} );
	$j("."+pcl).each( function(){
		var tcl= $j(this).attr("class");
		if(tcl.match("rowc_")){
			$j(this).hide();
		}else{
			$j(this).toggle();
		}
		if(tcl.match("rowp_")){
			$j(this).find("span").find("img").attr("src",'./images/icons/fplus.gif');
		}

});
}
</script>
<?php
if ($_GET ['a'] != "view") {
	$mpar = 'tpov';
} elseif (($_GET ['m'] == "projects" && $_GET ['a'] == "view" && ( int ) $_GET ['project_id'] > 0)) {
	$mpar = 'tprd';
}
global $myspeed;
$myspeed->addJs('make_pre("'.$mpar.'");','code');
//echo "make_pre('$mpar');"?>
