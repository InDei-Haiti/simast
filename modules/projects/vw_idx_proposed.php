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
/*if (!$oldtemplate) {
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
	$aTask_Status		= dPgetSysVal('TaskStatus');
	

	$locations = buildLocations();

	$q->addTable('companies');
	$q->addQuery('company_id, company_acronym');
	$imCompanies = $q->loadHashList();
	
	$q->clear();
	$q->addTable('programs');
	$q->addQuery('id, pacro');
	$aCompanies = $q->loadHashList();
}*/


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


$is_superAdmin = false;
$roles = $perms->getUserRoles($AppUI->user_id);
foreach ($roles as $role){
	if($role['value']=='super_admin'){
		$is_superAdmin = true;
	}
}


/*
<table width="100%" border="0" cellpadding="3" cellspacing="1" class="tbl tablesorter" id="tbl">
<thead title="Click to sort">
<tr>	
	<th nowrap="nowrap" width="30%" class="head">
		' . $AppUI->_('Project') . '
		<div class="head_menu"></div>
	</th>
	<th nowrap="nowrap" class="head">
		' . $AppUI->_('Project type') . '
		<div class="head_menu"></div>
	</th>
	<th nowrap="nowrap" width="15%" class="head">
		' . $AppUI->_('Agency') . '
		<div class="head_menu"></div>
	</th>	
	<th nowrap="nowrap" class="head">' . $AppUI->_('Sectors') . '
		<div class="head_menu"></div>
	</th>
	<th nowrap="nowrap" class="head">' . $AppUI->_('Str. Areas') . '
		<div class="head_menu"></div>
	</th>
	<th nowrap="nowrap" width="90px" class="head">
		' . $AppUI->_('Start') . '
		<div class="head_menu"></div>
	</th>
	<th nowrap="nowrap" width="90px" class="head">
		' . $AppUI->_('End') . '
		<div class="head_menu"></div>
	</th>	
</tr>
</thead>
<tbody></tbody>
</table>
*/
$output_head = '
<div id="mholder">
</div>
';

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
	if($projects && is_array($projects)){
		foreach ($projects  as $prid){
			if(in_array($prid['project_id'] , $tip)){
				$nprojects[]=$prid;
			}
		}
	}
	if(count($nprojects) > 0 )
		$projects=$nprojects;
}
$aFile_Type = dPgetSysVal('FileType');
$sector_types = dPgetSysVal('SectorType');
$project_types = dPgetSysVal("ProjectType");
$programs = buildProgramsListPure();
$plist = array();

$sql= 'select * from st_area where parent_id = "0" order by id';
$res = mysql_query($sql);
$top_areas = array();
if($res){
	while($row = mysql_fetch_assoc($res)){
		$top_areas[$row['id']]=$row['prex'].'.'.$row['title'];
	}
}
$topArId = array_keys($top_areas);
if($projects && is_array($projects)){
	foreach ($projects as &$row) {
		if (! $perms->checkModuleItem('projects', 'view', $row['project_id'])) {
			continue;
		}
		if(!$is_superAdmin){
			if(!$perms->checkForm($AppUI->user_id,'projects',$row['project_id'],'view')){
				continue;
			}
		}
		
	
		if ($show_all_projects || $row['project_status'] == $project_status_filter) {
			$none = false;
			$proj_obj = new CProject();
	
			$start_date			= ((intval(@$row['project_start_date']))	? new CDate($row['project_start_date']) : null);
			$end_date			= ((intval(@$row['project_end_date']))	? new CDate($row['project_end_date']) : null);
			$actual_end_date	= ((intval(@$row['project_actual_end_date']))	? new CDate($row['project_actual_end_date']) : null);
			$style				= ((($actual_end_date > $end_date) && !empty($end_date)) ? 'style="color:red; font-weight:bold"' : '');
	
			$uname	= htmlspecialchars( ((strlen($row['project_name']) > 55) ? substr($row['project_name'],0,55).'...' : $row['project_name']), ENT_QUOTES);
			//$uname	= htmlspecialchars( $row['project_name'], ENT_QUOTES);
			$description =  addslashes($row['project_description']);
			$agency	= ($perms->checkModuleItem('companies', 'access', $row['project_company']))
			? $CT . '<a href="?m=companies&a=view&company_id=' . $row['project_company'] . '" title="' . htmlspecialchars($row['company_description'], ENT_QUOTES) . '">' . htmlspecialchars($row['company_name'], ENT_QUOTES) . '</a>'
					: $CT . htmlspecialchars($row['company_name'], ENT_QUOTES);
	
			$prog_html='';
			$prog_view=array();
			if($row['project_donor']!=''){
				$prog_used = explode(",",$row['project_donor']);
				foreach($prog_used as $pui){
					$prog_view[] = $programs[$pui];
				}
			}
			$prog_html.= (count($prog_view) > 0 ? join(",",$prog_view) : '&nbsp;');
			//$contractors = $proj_obj->getContractors($row["project_id"]);
			$scom = '';
	
			$sectors = $proj_obj->getSectors($row["project_id"]);
			$sex='';
			$pure_sectors = array();
			if (!empty($sectors)) {
				foreach ($sectors as $sector) {
					if($sector['task_sector'] !=""){
						$pure_sectors =array_merge($pure_sectors,explode(',',$sector['task_sector']));
					}
				}
				if(count($pure_sectors) > 0){
					$pure_sectors = array_unique($pure_sectors);
				}
				$sex = trim(multiView($sector_types,join(",",$pure_sectors),true));
				$sector_teaser = $sector_types[$pure_sectors[0]];
				if(count($pure_sectors) > 1){
					$sector_teaser.='&nbsp;...';
				}else{
					$sex='';
				}
			}
	
			$start_date = $start_date ? $start_date->format($df) : '-';
			$end_date = $end_date ? $end_date->format($df) : '-';
	
			$lars = array();
			$sql = 'select task_id, task_areas from tasks where task_project="'.$row['project_id'].'"';
			$rep = mysql_query($sql);
			if(mysql_num_rows($rep) > 0){
				while($rar = mysql_fetch_assoc($rep)){
					if($rar['task_areas'] != ''){
						$tar = explode(",",$rar['task_areas']);
						if(count($tar) > 0){
							foreach ($topArId as $tid){
								if(in_array($tid,$tar) && !in_array($tid,$lars)){
									$lars[] = $tid;
								}
							}
						}
					}
				}
			}
			$showArea = array();
			if(count($lars) > 0){
				foreach ($lars as $li){
					$showArea[] = $top_areas[$li];
				}
			}
	
			$area_teaser = (count($showArea) > 0 ? $showArea[0] : '');
			$area_html = join(",<br>",$showArea);
			if(count($showArea) > 1){
				$area_teaser .= '&nbsp;...';
			}else{
				$area_html='';
			}
			if($row['project_partners']){
				$q = new DBQuery();
				$q->addTable ( 'companies' );
				$q->addQuery ( 'company_acronym' );
				$q->addWhere ( 'company_id in ('.$row['project_partners'] .')' );
				$row['project_partners'] = $q->loadColumn();
				$row['project_partners']  = implode(", ", $row['project_partners']);
			}
			if($row['project_cdonors']){
				$q = new DBQuery();
				$q->addTable ( 'companies' );
				$q->addQuery ( 'company_acronym' );
				$q->addWhere ( 'company_id in ('.$row['project_cdonors'].')' );
				$row['project_cdonors'] = $q->loadColumn();
				$row['project_cdonors']  = implode(", ", $row['project_cdonors']);
			}
			$plist[]=array(
					$row['project_id'],
					stripslashes($description),
					$uname,
					$start_date,
					$end_date,
					$sex,
					$row['project_partners'],
					$row['project_cdonors'],
					//$prog_html,
					$project_types[$row['project_type']],
					$row['project_company'],
					$area_html,
					$area_teaser,
					$sector_teaser
			);
	
				
			// Here is a new entry start
	
		}
	}	
}


if ($none) {
	$output_main1 .= $CR . '<tr><td colspan="' . $table_cols . '">' . $AppUI->_('No projects available') . '</td></tr></table>';//</form>
} else {
	$output_main1 .= '<tr><td colspan="' . $table_cols . '" align="right"></td></tr></table>
	<script></script>';//</form>
}


if ($oldtemplate) {
	echo $output_head,'<script> 
		var rawlist='.(count($plist) > 0 ? json_encode($plist) : '[]').';
		window.onload = up;
		function up(){
			pf.init({
				type: ["string","string","string","string","string","string","date","date"],
				cdata: [2,8,6,7,12,11,3,4],
				lects: ["plain","plain","plain","list","list","plain","plain"],
				links:{
					0:{
						url:"/?m=projects&a=view&project_id=#0#&tab=0",
						val:0
					}
				},
				columns:{
					0:{val:2,link:true,extra:{tag:"data-detail",val:1},class:"verbose cut_head inwrap"},
					1:{val:8,link:false},
					2:{val:6,link:false},
					3:{val:7},//sectors
					4:{val:12,extra:{tag:"data-detail",val:9},class:"verbose cut_head"},//stra-areas
					5:{val:11,link:false},
					6:{val:3,link:false},
					7:{val:4,link:false}
				},
				heads:[
					{title: "' . $AppUI->_('Project') . '"},
					{title:"' . $AppUI->_('Project Type') . '"},
					{title:"' . $AppUI->_('Partner Agencies') . '"},
					{title:"' . $AppUI->_('Donor Agencies') . '"},
					{title: "' . $AppUI->_('Sectors') . '"},
					{title:"' . $AppUI->_('Structure Areas') . '"},
					{title:"' . $AppUI->_('Start') . '"},
					{title:"' . $AppUI->_('End') . '"}		
				]				
			});verboseWindow(".verbose");
		}
	</script>';
	require_once(DP_BASE_DIR.'/modules/public/pa_table.code.php');
}
?>
