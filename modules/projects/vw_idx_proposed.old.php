<?php /* PROJECTS $Id: vw_idx_proposed.php 5685 2008-04-28 23:14:00Z merlinyoda $ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}
// Checking for old template value
$oldtemplate = FALSE;
if (isset($_GET['template']) && ($_GET['template'] == 'old')) {
	$oldtemplate = TRUE;
}
/*
 * Simple function to replace values in message or Template
 */
function TemplateReplace($msg, $patterns, $sp = '{#', $ep = '#}') {
	$keys	= array();
	$values	= array();
	foreach($patterns as $key => $val) {
		$keys[]		= $sp . $key . $ep;
		$values[]	= $val;
	}
	return str_replace($keys, $values, $msg);
}

function InputValue($arr = array(), $values) {
	$values		= explode(',', $values);
	$options	= array();
	foreach($arr as $key => $val) {
		if (in_array($key, $values))
			$options[] = $val;
	}
	return implode(', ', $options);
}

GLOBAL $AppUI, $projects, $company_id, $pstatus, $project_types, $currentTabId, $currentTabName, $project_id;
$output_head = '';
$output_main1 = '';
//New entry values
if (!$oldtemplate) {
	$sProjects			= '';
	$sActivities		= '';
	$project_template	= file_get_contents('./templates/project.tpl');
	$activity_template	= file_get_contents('./templates/activity.tpl');
	$project_disabled	= file_get_contents('./templates/project_disabled.tpl');
	$activity_disabled	= file_get_contents('./templates/activity_disabled.tpl');
	$entry_template		= file_get_contents('./templates/new_entry.tpl');
	$aProject_Types		= dPgetSysVal('ProjectType');
	$aTask_Sectors		= dPgetSysVal('SectorType');
	$aTask_Types		= dPgetSysVal('TaskType');
	$aTask_Implementers	= dPgetSysVal('TaskImplementers');
	$aTask_Status		= dPgetSysVal('TaskStatus');

	$q = new DBQuery();
	$q->addTable('administrative_regions', 'ar');
	$q->addQuery('ar.region_id, ar.region_name, ar.region_parent, ar.region_level');
	$q->addWhere('ar.region_level BETWEEN 1 AND 4');
	$q->addOrder('ar.region_level ASC, ar.region_name ASC');
	$admin_regions = $q->loadList();

	if (file_exists('./templates/locations.tpl')) {
		$locations = file_get_contents('./templates/locations.tpl');
	} else {
		$locations = '<ul id="locations">';
		$_1sts = array_filter($admin_regions, function($var) { return $var['region_level'] == 1; });
		foreach($_1sts as $_1st) {
			$parent1 = $_1st['region_id'];
			$_2nds = array_filter($admin_regions, function($var) use($parent1) { return ($var['region_parent'] == $parent1); });
			$locations .= (count($_2nds))
						? '<li><input type="checkbox"><label>' . $_1st['region_name'] . '</label><ul>'
						: '<li><input type="checkbox" class="last" id="'.$_1st['region_id'].'"><label>' . $_1st['region_name'] . '</label></li>';
			foreach($_2nds as $_2nd) {
				$parent2 = $_2nd['region_id'];
				$_3rds = array_filter($admin_regions, function($var) use($parent2) { return ($var['region_parent'] == $parent2); });
				$locations .= (count($_3rds))
							? '<li><input type="checkbox"><label>' . $_2nd['region_name'] . '</label><ul>'
							: '<li><input type="checkbox" class="last" id="'.$_2nd['region_id'].'"><label>' . $_2nd['region_name'] . '</label></li>';
				foreach($_3rds as $_3rd) {
					$parent3 = $_3rd['region_id'];
					$_4ths = array_filter($admin_regions, function($var) use($parent3) { return ($var['region_parent'] == $parent3); });
					$locations .= (count($_4ths))
								? '<li><input type="checkbox"><label>' . $_3rd['region_name'] . '</label><ul>'
								: '<li><input type="checkbox" class="last" id="'.$_3rd['region_id'].'"><label>' . $_3rd['region_name'] . '</label></li>';
					foreach($_4ths as $_4th) {
						$locations .= '<li><input type="checkbox" class="last" id="' . $_4th['region_id'] . '"><label>' . $_4th['region_name'] . '</label></li>';
					}
					if (count($_4ths))
						$locations .= '</ul></li>';
				}
				if (count($_3rds))
					$locations .= '</ul></li>';
			}
			if (count($_2nds))
				$locations .= '</ul></li>';
		}
		$locations .= '</ul>';
		file_put_contents('./templates/locations.tpl', $locations);
	}

	$q->addTable('companies');
	$q->addQuery('company_id, company_acronym');
	$aCompanies = $q->loadHashList();
}


$show_all_projects = false;
if ($currentTabId == 500) {
	$show_all_projects = true;
}

$perms =& $AppUI->acl();
$df = $AppUI->getPref('SHDATEFORMAT');

$base_table_cols = 9;
$base_table_cols += (($show_all_projects) ? 1 : 0);

$table_cols = $base_table_cols + ((($perms->checkModuleItem('projects', 'edit', $row['project_id']))) ? 1 : 0);
$added_cols = $table_cols - $base_table_cols;

$output_head = '<form action="./index.php" method="get">
<table width="100%" border="0" cellpadding="3" cellspacing="1" class="tbl">
<tr>
	<!--  <th nowrap="nowrap">
		<a href="?m=projects&orderby=project_color_identifier" class="hdr">' . $AppUI->_('Color') . '</a>
	</th>
	 -->
	<th nowrap="nowrap" width="20%">
		<a href="?m=projects&orderby=project_name" class="hdr">' . $AppUI->_('Project') . '</a>
	</th>
	<th nowrap="nowrap" width="15%">
		<a href="?m=projects&orderby=company_name" class="hdr">' . $AppUI->_('Agency') . '</a>
	</th>
	<th nowrap="nowrap" width="40%">' . $AppUI->_('Sectors') . '</th>
	<th nowrap="nowrap" width="90px">
		<a href="?m=projects&orderby=project_start_date" class="hdr">' . $AppUI->_('Start') . '</a>
	</th>
	<th nowrap="nowrap" width="90px">
		<a href="?m=projects&orderby=project_end_date" class="hdr">' . $AppUI->_('End') . '</a>
	</th>
	<!--  <th nowrap="nowrap">
		<a href="?m=projects&orderby=project_actual_end_date" class="hdr">' . $AppUI->_('Actual') . '</a>
	</th>
	-->
<tr/>';

$CR = "\n";
$CT = "\n\t";
$none = true;

//Tabbed view
$project_status_filter = $currentTabId;
//Project not defined
if($_GET['m']=="projects" && (isset($_POST['project_id']) && count($_POST['project_id'])> 0 ) && !$project_id){
	$tip=$_POST['project_id'];
}elseif(count($project_id) > 0){
	$tip=$project_id;
}
if(count($tip) > 0){
	$nprojects=array();
	foreach ($projects  as  $prid){
		if(in_array($prid['project_id'] , $tip)){
			$nprojects[]=$prid;
		}
	}
	if(count($nprojects) > 0 )$projects=$nprojects;
}

foreach ($projects as $row) {
	if (! $perms->checkModuleItem('projects', 'view', $row['project_id'])) {
		continue;
	}
	if ($show_all_projects || $row['project_status'] == $project_status_filter) {
		$none = false;
		$proj_obj = new CProject();
		
		$start_date			= ((intval(@$row['project_start_date']))	? new CDate($row['project_start_date']) : null);
		$end_date			= ((intval(@$row['project_end_date']))	? new CDate($row['project_end_date']) : null);
		$actual_end_date	= ((intval(@$row['project_actual_end_date']))	? new CDate($row['project_actual_end_date']) : null);
		$style				= ((($actual_end_date > $end_date) && !empty($end_date)) ? 'style="color:red; font-weight:bold"' : '');

		$uname	= htmlspecialchars( ((strlen($row['project_name']) > 30) ? substr($row['project_name'],0,30).'...' : $row['project_name']), ENT_QUOTES);
		$description = htmlspecialchars('<div><p>' . str_replace(array("\r\n", "\n", "\r"), '</p><p>', addslashes($row['project_description'])) . '</p></div>', ENT_QUOTES);
		$agency	= ($perms->checkModuleItem('companies', 'access', $row['project_company']))
				? $CT . '<a href="?m=companies&a=view&company_id=' . $row['project_company'] . '" title="' . htmlspecialchars($row['company_description'], ENT_QUOTES) . '">' . htmlspecialchars($row['company_name'], ENT_QUOTES) . '</a>'
				: $CT . htmlspecialchars($row['company_name'], ENT_QUOTES);
		$contractors = $proj_obj->getContractors($row["project_id"]);
		$scom = '';
		if (!empty($contractors)) {
			foreach ($contractors as $contractor){
				$scom .= ',' . $CT . "<a href='?m=companies&a=view&company_id=".$contractor['task_company']."'>".$companies[$contractor['task_company']]."</a>" ;
			}
		}
		$sectors = $proj_obj->getSectors($row["project_id"]);
		$sector_types = dPgetSysVal('SectorType');
		$sex='';
		if (!empty($sectors)) {
			foreach ($sectors as $sector) {
				//var_dump($sector['task_sector']);
				if($sector_types[$sector['task_sector']] !=""){
					$sex .=  $sector_types[$sector['task_sector']] .", ". $CR;
				}
			}
		}
		$sex=preg_replace("/,\s\n$/","",$sex);
		if ($row['task_log_problem']) {
			$tasks .= ('<a href="?m=tasks&a=index&f=all&project_id=' . $row['project_id'] . '">' . dPshowImage('./images/icons/dialog-warning5.png', 16, 16, 'Problem', 'Problem!') . '</a>');
		} else if ($row['project_priority'] != 0) {
			$tasks .= "\n\t\t" . dPshowImage('./images/icons/priority' . (($row['project_priority'] > 0) . abs($row['project_priority']) . '.gif'), 13, 16, '', '');
		} else {
			$tasks .= '&nbsp;';
		}
		$start_date = $start_date ? $start_date->format($df) : '-';
		$end_date = $end_date ? $end_date->format($df) : '-';

		$output_main1 .= '<tr>' .
			$CR . '<td width="20%">' . $CT . '<a href="?m=projects&a=view&project_id=' . $row['project_id'] . '" onmouseover="return overlib(\'' . $description . '\', CAPTION, \'' . $AppUI->_('Description') . '\', CENTER);" onmouseout="nd();">' . $uname . '</a>' . $CR . '</td>' . 
			$CR . '<td width="25%">' . $agency . $scom . $CR . '</td>' .
			$CR . '<td align="center" width="15%" >' . $sex . $tasks . $CR . '</td>' .
			$CR . '<td align="center">'. $start_date . '</td>' .
			$CR . '<td align="center">'. $end_date .'</td>' .
			$CR . '</tr>';

		// Here is a new entry start
		if (!$oldtemplate) {
			// Checking all activities for this project
			$q->addTable('tasks', 't');
			$q->addQuery('*');
			$q->addWhere('t.task_project = '.$row['project_id']);
			$q->addOrder('t.task_name ASC');
			$activities = $q->loadList();
			$sActivities = '';
			foreach($activities as $task) {
				$task_id			= 'id="activity_'.$task['task_id'].'" style="display: none;"';
				$task_start_date	= ((intval(@$task['task_start_date']))	? new CDate($task['task_start_date']) : null);
				$task_end_date		= ((intval(@$task['task_end_date']))	? new CDate($task['task_end_date']) : null);
				$task_start_date	= $task_start_date ? $task_start_date->format($df) : '-';
				$task_end_date		= $task_end_date ? $task_end_date->format($df) : '-';
				if ($task['task_owner'] == $AppUI->user_id) {
					$p_template			= $activity_template;
					$task_sector		= arraySelect($aTask_Sectors, 'task_sector', 'title="Select one or more Sectors for this Activity" multiple="multiple" class="multiple"', explode(',', $task['task_sector']));
					$task_type			= arraySelect($aTask_Types, 'task_type', 'title="Select one or more Activity Types" multiple="multiple" class="multiple"', explode(',', $task['task_type']));
					$task_implementer	= arraySelect($aTask_Implementers, 'task_implementer', 'title="Select one or more Implementers" multiple="multiple" class="multiple"', explode(',', $task['task_implementer']));
					$task_status		= arraySelect($aTask_Status, 'task_status', 'title="Select Activity Status"', $task['task_status']);
					$task_locations		= $task['task_locations'];
				} else {
					$p_template			= $activity_disabled;
					$task_sector		= '<input class="select_text" type="text" name="task_sector" value="' . InputValue($aTask_Sectors, $task['task_sector']) . '" disabled="disabled" />';
					$task_type			= '<input class="select_text" type="text" name="task_type" value="' . InputValue($aTask_Types, $task['task_type']) . '" disabled="disabled" />';
					$task_implementer	= '<input class="select_text" type="text" name="task_type" value="' . InputValue($aTask_Implementers, $task['task_implementer']) . '" disabled="disabled" />';
					$task_status		= arraySelect($aTask_Status, 'task_status', 'title="Select Activity Status" disabled="disabled"', $task['task_status']);
					$values		= explode(',', $task['task_locations']);
					$options	= array();
					foreach($admin_regions as $val) {
						if (in_array($val['region_id'], $values))
							$options[] = $val['region_name'];
					}
					$task_locations		= '<input class="select_text" type="text" name="task_locations" value="' . implode(', ', $options) . '" disabled="disabled" />';
				}
				$sActivities	.= TemplateReplace(
					$p_template,
					array('activity_id'=>$task_id, 'activity_name'=>$task['task_name'], 'activity_sector'=>$task_sector, 'activity_type'=>$task_type, 'activity_description'=>$task['task_description'], 'activity_implementers'=>$task_implementer, 'activity_locations'=>$task_locations, 'activity_start_date'=>$task_start_date, 'activity_end_date'=>$task_end_date, 'activity_status'=>$task_status, 'activity_budget'=>$task['task_target_budget'], 'activity_benef'=>$task['task_beneficiaries'])
				);
			}
			$ds				= ($row['project_owner'] == $AppUI->user_id) ? '' : ' disabled="disabled"';

			$project_id		= 'id="project_'.$row['project_id'].'"';
			$project_type	= arraySelect($aProject_Types, 'project_type', 'title="Select the Project Type"' . $ds, $row['project_type']);
			$donor			= arraySelect($aCompanies, 'project_company', ' title="Enter the Donor Agencies"' . $ds, $row['project_company']);
			$project_status	= arraySelect($project_types, 'project_status', 'title="Select the Project Status"' . $ds, $row['project_status']);

			$sProjects		.= TemplateReplace(
				($row['project_owner'] == $AppUI->user_id) ? $project_template : $project_disabled,
				array('project_id'=>$project_id, 'project_name'=>$uname, 'project_type'=>$project_type, 'project_description'=>$row['project_description'], 'project_donor'=>$donor, 'project_start_date'=>$start_date, 'project_end_date'=>$end_date, 'project_status'=>$project_status, 'project_budget'=>$row['project_target_budget'], 'activities'=>$sActivities)
			);
		}
	}
}

if ($none) {
	$output_main1 .= $CR . '<tr><td colspan="' . $table_cols . '">' . $AppUI->_('No projects available') . '</td></tr></table></form>';
} else {
	$output_main1 .= '<tr><td colspan="' . $table_cols . '" align="right"></td></tr></table></form>';
}

if ($oldtemplate) {
	echo $output_head, $output_main1;
} else {
	//New entry output
	$project_type		= arraySelect($aProject_Types, 'project_type', 'title="Select the Project Type"', -1);
	$donor				= arraySelect($aCompanies, 'project_company', ' title="Enter the Donor Agencies"', -1);
	$project_status		= arraySelect($project_types, 'project_status', 'title="Select the Project Status"', -1);
	$empty_project		= TemplateReplace($project_template, array('project_id'=>'', 'project_name'=>'', 'project_type'=>$project_type, 'project_description'=>'', 'project_donor'=>$donor, 'project_start_date'=>'', 'project_end_date'=>'', 'project_status'=>$project_status, 'project_budget'=>'', 'activities'=>''));
	$task_sectors		= arraySelect($aTask_Sectors, 'task_sector', 'title="Select one or more Sectors for this Activity" multiple="multiple" class="multiple"', -1);
	$task_type			= arraySelect($aTask_Types, 'task_type', 'title="Select one or more Activity Types" multiple="multiple" class="multiple"', -1);
	$task_implementer	= arraySelect($aTask_Implementers, 'task_implementer', 'title="Select one or more Implementers" multiple="multiple" class="multiple"', -1);
	$task_status		= arraySelect($aTask_Status, 'task_status', 'title="Select Activity Status"', -1);
	$empty_activity		= TemplateReplace($activity_template, array('activity_id'=>'', 'activity_name'=>'', 'activity_sector'=>$task_sectors, 'activity_type'=>$task_type, 'activity_description'=>'', 'activity_implementers'=>$task_implementer, 'activity_locations'=>'', 'activity_start_date'=>'', 'activity_end_date'=>'', 'activity_status'=>$task_status, 'activity_budget'=>'', 'activity_benef'=>''));

	echo TemplateReplace($entry_template, array('empty_project'=>$empty_project, 'empty_activity'=>$empty_activity, 'projects'=>$sProjects, 'locations'=>$locations));
}
?>