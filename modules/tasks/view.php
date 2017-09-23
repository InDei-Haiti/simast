<?php /* $Id: view.php 5761 2008-07-01 18:52:45Z merlinyoda $ */
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}
require_once($AppUI->getModuleClass('wizard'));
$task_id = intval(dPgetParam($_GET, 'task_id', 0));
$task_log_id = intval(dPgetParam($_GET, 'task_log_id', 0));
$reminded = intval(dPgetParam($_GET, 'reminded', 0));

$obj = new CTask();
$msg = '';

$adm_rights=$AppUI->isAdmin();

$obj->peek($task_id); //we need to peek at the task's data to determine its access level

/*if (!($obj->canAccess($AppUI->user_id))) {
	$AppUI->redirect('m=public&a=access_denied');
}*/
require_once $AppUI->getModuleClass ( 'projects' );

$pobj = new CProject();

$pcan_edit= $pobj->localCheck($AppUI->user_id,$obj->task_project);

$tcan_edit = $obj->localcheck($obj->task_id);

//if($obj->task_mode != 'root' && $obj->task_parent > 0){
if($obj->task_id!= $obj->task_parent){
	$par_obj = new CTask();
	$par_obj->peek($obj->task_parent);
	$par_rights=$par_obj->localcheck($obj->task_parent);
}else{
	$par_rights=false;
}

// check permissions for this record
$canRead = getPermission($m, 'view', $task_id);
$canEdit = getPermission($m, 'edit', $task_id);
// check if this record has dependencies to prevent deletion
$canDelete = $obj->canDelete($msg, $task_id);
// check permissions for this record (module level)
$canReadModule = getPermission($m, 'view');

if($canEdit && ($tcan_edit || $par_rights || $pcan_edit || $adm_rights)){
	$canEdit=true;
}else{
	$canEdit=false;
}

if($canDelete && ($pcan_edit || $adm_rights)){
	$canDelete=true;
}else{
	$canDelete=false;
}



if (!($canRead)) {
	$AppUI->redirect('m=public&a=access_denied');
}


$q =& new DBQuery;
$perms =& $AppUI->acl();

$is_superAdmin = false;
$roles = $perms->getUserRoles($AppUI->user_id);
foreach ($roles as $role){
	if($role['value']=='super_admin'){
		$is_superAdmin = true;
	}
}
if($is_superAdmin){
	$canEdit=true;
	$canDelete=true;
}
if(!$is_superAdmin){
	if(!$perms->checkForm($AppUI->user_id,'activity',$task_id,'view')){
		$AppUI->redirect ( 'm=public&a=access_denied' );
	}
}

$q->addTable('tasks');
$q->leftJoin('projects', 'p', 'p.project_id = task_project');
$q->leftJoin('task_log', 'tl', 'tl.task_log_task = task_id');
$q->addWhere('task_id = ' . $task_id);
$q->addQuery('tasks.*');
$q->addQuery('project_name, project_color_identifier');
$q->addQuery('ROUND(SUM(task_log_hours),2) as log_hours_worked');
$q->addGroup('task_id');


//$obj = null;
$sql = $q->prepare();
$q->clear();

if (!db_loadObject($sql, $obj, true, false)) {
	$AppUI->setMsg('Activity');
	$AppUI->setMsg('invalidID', UI_MSG_ERROR, true);
	$AppUI->redirect();
} else {
	$AppUI->savePlace();
}

// Clear any reminders
if ($reminded) {
	$obj->clearReminder();
}

// retrieve any state parameters
if (isset($_GET['tab'])) {
	$AppUI->setState('TaskLogVwTab', $_GET['tab']);
}
$tab = $AppUI->getState('TaskLogVwTab') !== NULL ? $AppUI->getState('TaskLogVwTab') : 0;

// get the prefered date format
$df = $AppUI->getPref('SHDATEFORMAT');
//Also view the time
$df .= ' ' . $AppUI->getPref('TIMEFORMAT');

$start_date = intval($obj->task_start_date) ? new CDate($obj->task_start_date) : null;
$end_date = intval($obj->task_end_date) ? new CDate($obj->task_end_date) : null;

//check permissions for the associated project
$canReadProject = getPermission('projects', 'view', $obj->task_project);

// get the users on this task
$q->addTable('users', 'u');
$q->addTable('user_tasks', 't');
$q->leftJoin('contacts', 'c' , 'user_contact = contact_id');
$q->addQuery('u.user_id, u.user_username, contact_email');
$q->addWhere('t.task_id = ' . $task_id);
$q->addWhere('t.user_id = u.user_id');
$q->addOrder('u.user_username');

$sql = $q->prepare();
$q->clear();
$users = db_loadList($sql);

$durnTypes = dPgetSysVal('TaskDurationType');
$sector_list = dPgetSysVal('SectorType');
$status_types = dPgetSysVal('TaskStatus');



//location information

//fetch list of countries
/*$q = new DBQuery();
$q->addTable('administrative_regions');
$q->addQuery('region_id, region_name');
$q->addWhere('region_parent = 0');
$country_list = $q->loadHashList();*/

//$country_list = buildProvinceListPure();

//var_dump($country_list);
/*
//fetch admin level 1
$q = new DBQuery();
$q->addTable('administrative_regions');
$q->addQuery('region_id, region_name');
$q->addWhere('region_parent in (' . implode(",", array_keys($country_list) ) . ')');
$q->addOrder('region_parent, region_name');
$admin2_list =  $q->loadHashList();

//fetch admin level 2
$q = new DBQuery();
$q->addTable('administrative_regions');
$q->addQuery('region_id, region_name');
$q->addWhere('region_parent in (' . implode(",", array_keys($admin2_list) ) . ')');
$q->addOrder('region_parent, region_name');
$admin3_list = $q->loadHashList();

//fetch admin level 2
$q = new DBQuery();
$q->addTable('administrative_regions');
$q->addQuery('region_id, region_name');
$q->addWhere('region_parent in (' . implode(",", array_keys($admin3_list) ) . ')');
$q->addOrder('region_parent, region_name');
$admin4_list =  $q->loadHashList();

//fetch admin level 3
$q = new DBQuery();
$q->addTable('administrative_regions');
$q->addQuery('region_id, region_name');
$q->addWhere('region_parent in (' . implode(",", array_keys($admin4_list) ) . ')');
$q->addOrder('region_parent, region_name');
$admin5_list =  $q->loadHashList();

//fetch admin level 4
$q = new DBQuery();
$q->addTable('administrative_regions');
$q->addQuery('region_id, region_name');
$q->addWhere('region_parent in (' . implode(",", array_keys($admin5_list) ) . ')');
$q->addOrder('region_parent, region_name');
$admin6_list =  $q->loadHashList();
*/
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

if($obj->task_locations != ''){
	$q = new DBQuery();
	$q->addTable("administration_com");
	$q->addWhere('administration_com_code IN ('.$obj->task_locations.')');
	$q->addQuery('administration_com_name');
	$list = array_keys($q->loadArrayList());
	//var_dump($list);
	//if(count($list)>5)
	$list = array_chunk($list, 5);echo '<br/>';
	//var_dump($list);
	$locs_html = '<table style="border: 1px;border-color: red">';
	foreach($list as $arr){
		$locs_html .= '<tr>';
		$i = 0;
		foreach ($arr as $index => $val){
			//$val = explode(" ", $val);
			//unset($val[0]);
			//$val = implode(" ", $val);
			$virgule = '';
			if($i != count($arr)-1)
				$virgule = ',';
			$locs_html .= '<td><span>'.$val.'</span>'.$virgule.' </td>';
			$i += 1;
		}
		$locs_html .= '</tr>';
	}
	$locs_html .= '</table>';
	//$locs_html = join(", <br>",array_keys($q->loadArrayList()));
}


// get the list of permitted companies
$obj_company = new CCompany();
$companies = $obj_company->getAllowedRecords($AppUI->user_id, 'company_id,company_name', 
	                                             'company_name');
if(count($companies) == 0) { 
		$companies = array(0);
}

// setup the title block
//$titleBlock = new CTitleBlock('View Activity', 'applet-48.png', $m, "$m.$a");
$titleBlock = new CTitleBlock('View' .($obj->task_id == $obj->task_parent? '' : ' Sub '). ' Activity:'.$obj->task_name, '', $m, "$m.$a");
$titleBlock->addCell();

if ($canEdit) {
	/*if($obj->task_mode == "root"){
		$titleBlock->addCell(
			'<input type="submit" class="button" value="'.$AppUI->_('new activity').'">', '',
			'<form action="?m=tasks&a=addedit&task_project='.$obj->task_project.'&task_parent=' . $task_id . '" method="post">', '</form>'
		);
	
		$titleBlock->addCell(
			'<input type="submit" class="button" value="'.$AppUI->_('new sub activity').'">', '',
			'<form action="?m=tasks&a=addedit&scope=subs&task_project='.$obj->task_project.'&task_parent=' . $task_id . '" method="post">', '</form>'
		);
	}
	$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_('new file').'">', '',
		'<form action="?m=files&a=addedit&project_id=' . $obj->task_project . '&file_task=' . $obj->task_id . '" method="post">', '</form>'
	);*/
}
//$titleBlock->addCrumb('?m=tasks', 'activities list');
if ($canReadProject && ($pcan_edit || $adm_rights)) {
//	$titleBlock->addCrumb("?m=projects&a=addedit&project_id=$obj->task_project", 'edit this project');
	//'view this project'
}
if ($canEdit && ($par_rights || $tcan_edit || $pcan_edit ||  $adm_rights)) {
	if($obj->task_parent != $obj->task_id /*&& $obj->task_mode == "sub"*/ && ($par_rights == true || $adm_rights)){
		$titleBlock->addCrumb("?m=projects&a=view&project_id=".$obj->task_project, 'view project');
		//if(!$perms->checkForm($AppUI->user_id,'activity',$task_id,'edit')){
			$titleBlock->addCrumb("?m=tasks&a=addedit&task_id=".$obj->task_parent, 'edit this activity');
		//}
	}elseif($obj->task_mode == 'root'){
		$titleBlock->addCrumb("?m=projects&a=view&project_id=".$obj->task_project, 'view project');
		//if(!$perms->checkForm($AppUI->user_id,'activity',$task_id,'edit')){
			$titleBlock->addCrumb("?m=tasks&a=addedit&task_id=$task_id", 'edit this activity');
		//}
	}
	//$titleBlock->addCrumb("?m=wizard&task_id=$task_id", 'Create Form Wizard');
	//$titleBlock->addCrumb("javascript:popSelects('client')", 'Add Beneficieries');
	//$titleBlock->addCrumb("javascript:popSelects('client')", 'Add Beneficieries');
	//echo "<input type='button' class='button' value='" . $AppUI->_ ( "Select clients..." ) . "' onclick='javascript:popSelects(\"clients\");' />";
}
if ($canDelete) {
	//$titleBlock->addCrumbDelete('delete activity', $canDelete, $msg);
	//$titleBlock->addCrumbDelete('delete activity', $canDelete, $msg);
}
//$titleBlock->show();

$task_types = dPgetSysVal('TaskType');

$status_types = dPgetSysVal( 'ProjectStatus' );//dPgetSysVal ( 'TaskStatus' );

if($obj->task_areas && is_array($obj->task_areas) && count($obj->task_areas)>0){
	$q = new DBQuery();
	$q->addTable("st_area");
	$q->addQuery("concat(prex,' ',title) as name");
	$q->addWhere("id IN (".$obj->task_areas .')');
	$task_areas = join(", <br>",array_keys($q->loadArrayList()));
}

if($obj->task_agency && is_array($obj->task_agency) && count($obj->task_agency)>0){
	$q = new DBQuery();
	$q->addTable("companies");
	$q->addQuery("company_name");
	$q->addWhere("company_id IN (".$obj->task_agency .')');
	$agency_html = join(", <br>",array_keys($q->loadArrayList()));
}

?>
<div class="card">
    <div class="block-header">
        <h2 style="border-bottom: 1px solid #d0d0d0;padding-bottom: 10px">
            <?php
            $titleBlock->show ();
            ?>
        </h2>
    </div><br/>
<script type="text/javascript">
var calendarField = '';

function popCalendar(field){
	calendarField = field;
	idate = eval('document.editFrm.task_' + field + '.value');
	window.open('index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'width=250, height=220, scrollbars=no, status=no,resizable');
}

/**
 *	@param string Input date in the format YYYYMMDD
 *	@param string Formatted date
 */
function setCalendar(idate, fdate) {
	fld_date = eval('document.editFrm.task_' + calendarField);
	fld_fdate = eval('document.editFrm.' + calendarField);
	fld_date.value = idate;
	fld_fdate.value = fdate;
}

<?php

// security improvement:
// some javascript functions may not appear on client side in case of user not having write permissions
// else users would be able to arbitrarily run 'bad' functions
if ($canEdit) {
?>

function updateTask() {
	var f = document.editFrm;
	if (f.task_log_description.value.length < 1) {
		alert("<?php echo $AppUI->_('tasksComment', UI_OUTPUT_JS);?>");
		f.task_log_description.focus();
	} else if (isNaN(parseInt(f.task_percent_complete.value+0))) {
		alert("<?php echo $AppUI->_('tasksPercent', UI_OUTPUT_JS);?>");
		f.task_percent_complete.focus();
	} else if(f.task_percent_complete.value  < 0 || f.task_percent_complete.value > 100) {
		alert("<?php echo $AppUI->_('tasksPercentValue', UI_OUTPUT_JS);?>");
		f.task_percent_complete.focus();
	} else {
		f.submit();
	}
}
function delIt() {
	if (confirm("<?php echo $AppUI->_('doDelete', UI_OUTPUT_JS).' '.$AppUI->_('Activity', UI_OUTPUT_JS).'?';?>")) {
		document.frmDelete.submit();
	}
}
function popSelects(part){
	var brief=part.replace(/s$/,''),postVar;
	//eval("postVar=selected_"+part+"_id;");
	//if(part == "contacts"){
		//brief='staff';
	//} 
	//window.open("./index.php?m=public&a="+brief+"_selector&dialog=1&call_back=postStaff&fpart="+part+"&task_id="+<?php echo $task_id ?>+"&selected_"+part+"_id="+postVar, part, "height=600,width=400,resizable,scrollbars=yes");
}
<?php } ?>
</script>
<style>
<!--

-->

</style>
<table border="0" cellpadding="2" cellspacing="0" width="100%" class="std">

<form name="frmDelete" action="./index.php?m=tasks" method="post">
	<input type="hidden" name="dosql" value="do_task_aed">
	<input type="hidden" name="del" value="1" />
	<input type="hidden" name="task_id" value="<?php echo $task_id;?>" />
</form>
<tr>
		<td style="border: outset #d1d1cd 0px;" colspan="2">
	<?php
	//echo ('<span style="font-weight:bold">' . $obj->task_name . '</span>');
	?>
	</td>
	</tr>
<tr>
	<td  width="50%" valign="top" class="mtab">
		<table width="100%" cellspacing="1" cellpadding="2" class="titleds">
		<!--  <tr>
			<td nowrap="nowrap" colspan="2"><strong><?php echo $AppUI->_('Details');?></strong></td>
		</tr> ?m=tasks&a=view&task_id=608&tab=0-->
		<!--<tr>
			<td align="right" nowrap="nowrap"><?php /*echo $AppUI->_('Project Name');*/?>:</td>
			<td class='hilite' width="100%">
				<a href="?m=projects&a=view&project_id=<?php /*echo @$obj->task_project;*/?>&tab=0"><?php /*echo @$obj->project_name;*/?></a>
			</td>
		</tr>-->
		<!-- <tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Contractor');?> :</td>
			<td class="hilite" width="300"><?php
			$q = new DBQuery();
			$q->addTable("projects");
			$q->addQuery("project_company");
			$q->addWhere("project_id='".$obj->task_project."'"); 
			$tval= $q->loadResult();
			$q->clear();
			echo $AppUI->_($companies[$tval]);
			?></td>
		</tr> -->
		<!--<tr>
			<td align="right" nowrap="nowrap"><?php /*echo $AppUI->_('Activity Name');*/?>:</td>
			<td class="hilite"><strong><?php /*echo @$obj->task_name;*/?></strong></td>
		</tr>-->
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Partners');?> :</td>
			<td class="hilite" width="300" style="width: 70%"><?php
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
				echo $obj->task_agency;
				?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Sector');?> :</td>
			<td class="hilite" width="300" style="width: 70%"><?php
				echo  multiView($sector_list,$obj->task_sector);
				?></td>
		</tr>	
		
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Start Date');?>:</td>
			<td class="hilite" width="300" style="width: 70%;height: 30px;"><?php echo $start_date ? $start_date->format($df) : '-';?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('End Date');?>:</td>
			<td class="hilite" width="300"><?php echo $end_date ? $end_date->format($df) : '-';?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Type Of Beneficiary');?>:</td>
			<td class="hilite" width="300"><?php 
			$task_type_of_beneficiery = dPgetSysVal('TypeOfBeneficiery');
			
			foreach(explode(',', $obj->task_type_of_beneficiery) as $item){
				$l[] = $task_type_of_beneficiery[$item];
			}
			if($l) 
				echo implode(', ', $l);?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Beneficiaries');?>:</td>
			<td class="hilite" width="300"><?php echo $obj->task_beneficiares;?></td>
		</tr>
		</table>
	</td>

	<td width="50%" rowspan="9" valign="top" class="mtab">
		<table cellspacing="1" cellpadding="2" border="0" width="100%">
            <tr>
                <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Status');?>:</td>
                <td class="hilite" width="300"><?php
                    //var_dump($status_types);
                    echo $AppUI->_($status_types[$obj->task_status]);
                    ?></a></td>
            </tr>
            <tr>
                <td align="right" nowrap="nowrap"><strong><?php echo $AppUI->_('Budget');?></strong> <?php echo $dPconfig['currency_symbol'] ?>:
                </td>
                <td class="hilite" width="100%"><?php echo $obj->task_target_budget;?></td>
            </tr>

            <tr>
                <td align="right"  nowrap="nowrap"><strong><?php echo $AppUI->_('Yearly Budget');?></strong> :	</td>
                <td class="hilite" width="300"><?php
                        $yb = str_replace(array(";","|"),array("<br>"," - "),$obj->task_annual_budget);
                        echo $yb;
                ?></td>
            </tr>
            <tr>
                <td align="right"  nowrap="nowrap"><strong><?php echo $AppUI->_('Strategy Areas');?></strong> :	</td>
                <td class="hilite moreless" width="300"><?php
                echo getStrategies(explode(',', $obj->task_areas))
                        //echo $task_areas;
                ?></td>
            </tr>
            <tr>
                <td  align="right" nowrap="nowrap"><strong><?php echo $AppUI->_('Locations');?></strong> :	</td>
                <td class="hilite moreless" width="300"><?php
                $locs_html = '';
                if($obj->task_locations)
                    $obj->task_locations = explode(',', $obj->task_locations);
                if($obj->task_locations && count($obj->task_locations) > 0){
                    $q  = new DBQuery();
                    $q->addTable('administration_com');
                    $q->addWhere('administration_com_code  in ('.join(",",$obj->task_locations) .')');
                    $q->addQuery('administration_com_name');
                    //$loc_list = @join(", ",array_keys(@$q->loadArrayList()));

                    $list = array_keys($q->loadArrayList());
                    $list_cnt = count($list);
                    //var_dump($list);
                    //if(count($list)>5)
                    $list = array_chunk($list, 7);
                    //var_dump($list);
                    $locs_html = '';
                    $it = 0;
                    $ismore = false;
                    foreach($list as $arr){
                        $it++;
                        $class = '';
                        if($list_cnt>7 && $it>1){
                            $class = 'class="chidden"';
                            $ismore = true;
                        }
                        $locs_html .= '<span '.$class.'>';
                        $i = 0;
                        foreach ($arr as $index => $val){
                            //$val = explode(" ", $val);
                            //unset($val[0]);
                            //$val = implode(" ", $val);
                            $virgule = '';
                            if($i != count($arr)-1)
                                $virgule = ', ';
                            $locs_html .= $val.$virgule;
                            $i += 1;
                        }
                        $locs_html .= ' </span>';
                    }
                    $locs_html .= '
                                    <script>
                                        var morevar = "<b>'.$AppUI->_ ( 'More' ).'...</b>";
                                        var hidevar = "<b>'.$AppUI->_ ( 'Less' ).'</b>";
                                        function showhide(){
                                            ele = document.getElementById("moreless");
                                            classv = ele.getAttribute("class");
                                            if(classv === "moreAction"){
                                                ele.setAttribute("class","lessAction");
                                                $(\'.chidden\').addClass(\'cvisible\');
                                                $(\'.chidden\').removeClass(\'chidden\');
                                                ele.innerHTML=hidevar;
                                            }else{
                                                ele.setAttribute("class","moreAction");
                                                $(\'.cvisible\').addClass(\'chidden\');
                                                $(\'.cvisible\').removeClass(\'cvisible\');
                                                ele.innerHTML=morevar;
                                            }
                                        }
            
                                    </script>';
                    if($ismore){
                        $locs_html .= '<a href="javascript:showhide()" id="moreless" class="moreAction"><b>'.$AppUI->_ ( 'more' ).'...</b></a>';
                    }
                }


                echo $locs_html;
                ?></td>
            </tr>
            <tr>
              <td  align="right" nowrap="nowrap">
                 <strong><?php echo $AppUI->_('Description');?></strong><br />
              </td>

              <td class='hilite moreless' >
                    <?php if(strlen($obj->task_description) <= 100)
                            echo $obj->task_description;
                          else{
                            echo substr($obj->task_description,0,99).'<font color="red">...</font><span class="title">'.$obj->task_description.'</span>';
                          }

                    ?>
              </td>
            </tr>
            <?php

                    $q->clear();
                    require_once  $AppUI->getSystemClass('CustomFields');
                    $custom_fields = New CustomFields($m, $a, $obj->task_id, 'view');
                    $custom_fields->printHTML();
                 ?>
                        </td>
                    </tr>
		</table>
	</td>
</tr>
</table>
        </div>

<div class="card">
<?php
//if(@$obj->task_type_of_beneficiery)
	//@$obj->task_type_of_beneficiery = explode ( ',', @$obj->task_type_of_beneficiery );
//$tabBox = new CTabBox ( "?m=tasks&a=view&task_id=$task_id", "", $tab );

/* 
$q = new DBQuery();
$q->addTable('form_master');
$q->addQuery('id,title');
//$q->addWhere('registry = "0"');
$q->addWhere('valid="1"');
$newforms = $q->loadHashList(); */

/* /* $_SESSION['wiz_tab']=array();
if(count($newforms) > 0){
	foreach($newforms as $nid => $nform){
		/* $tpos=$tabBox->add ( $moddir.'vw_wizard', $nform );
		$_SESSION['wiz_tab'][$tpos]=$nid; 
		//$tabBox->add ( "vw_wizard", $nform . " (" . $obj->getCount ( $AppUI->user_type, $AppUI->user_id, $type ) . ")", false, '' );	
	//}
//}
//$tabBox->show();
if (isset ( $_GET ['tab'] )) {
	$AppUI->setState ( 'FormIdxTab', $_GET ['tab'] );
}
$formTab = defVal ( $AppUI->getState ( 'FormIdxTab' ), 0 ); 
$moddir = $dPconfig['root_dir'] . '/modules/tasks/';
$tabBox = new CTabBox( "?m=tasks&a=view&task_id=$task_id", "", $tab );
if(count($newforms) > 0){
	$type_filter = array ();
	foreach ( $newforms as $nid => $nform ) {
		$type_filter [] = $nid;
		/*if (strncmp ( $type_name, "Not Applicable", strlen ( "Not Applicable" ) ) == 0)
			$type = NULL;*/
		/* if (strncmp ( $type_name, "Not Active", strlen ( "Not Active" ) ) == 0)
			$type = 99; */
		/*if (strncmp ( $type_name, "LTP", strlen ( "LTP" ) ) == 0)
			$type = 98;
		$_SESSION['wiz_tab'][$tpos]=$nid;
		$tabBox->add ( $moddir . "vw_wizard", $nform /*. " (" . $obj->getCount ( $AppUI->user_type, $AppUI->user_id, $type ) . ")", false, $nid );
	}
}

$tabBox->show ();

 */

global $task_name;

$task_name = $obj->task_name;

$moddir = $dPconfig ['root_dir'] . '/modules/tasks/';

$me = '';
if(isset($_GET['user']) && $_GET['user']==='me'){
	$me = '&user='.$_GET['user'];
}
$tabBox = new CTabBox ( "?m=tasks&a=view&task_id=$task_id$me", "", $tab );

$q->addTable('tasks');
$q->addQuery('task_project');
$q->addWhere('task_id='.$task_id);

$project = $q->loadResult();

$q->clear();
$q->addTable('form_master');
$q->addQuery('id');
$q->addWhere('project_id='.$project.' AND forregistration=1');
$fuid = $q->loadResult();

$q->clear();
/* if($fuid){
	$q->addTable('wform_'.$fuid);
	$q->addQuery('COUNT(id)');
	$q->addWhere('id IN (SELECT registry_id FROM beneficieries WHERE task_id='.$task_id.' AND form_id='.$fuid.')');
	if(isset($_GET['user']) && $_GET['user']==='me'){
		$q->addWhere('wform_'.$fuid.'.user_creator='.$AppUI->user_id);
	}
	//$query = $q->prepare();
	//echo $query;
	//$nbr = Wizard::COUNT($query);
	$nbr = $q->loadResult();
	$tabBox->add ( $moddir.'vw_clients', 'Beneficieries ('.$nbr.')');
} */
$q = new DBQuery();
$q->addTable('form_master');
$q->addQuery('id,title');
//$q->addWhere('registry = "0"');
$q->addWhere('valid="1"');
$q->addWhere('task_id='.$task_id);
$newforms = $q->loadHashList();

$q->addTable('activity_queries');
$q->addQuery('id,qname');
$q->addWhere('activity_id='.$task_id);
$queries = $q->loadHashList();

$_SESSION['wiz_tab']=array();
if(count($newforms) > 0){
	foreach($newforms as $nid => $nform){
		if(!$is_superAdmin){
			if($perms->checkForm($AppUI->user_id, 'wizard', $nid, 'view')){
				$tpos=$tabBox->add ( $moddir.'vw_wizard', $nform );
				$_SESSION['wiz_tab'][$tpos]=$nid;
			}
		}else{
			$tpos=$tabBox->add ( $moddir.'vw_wizard', $nform );
			$_SESSION['wiz_tab'][$tpos]=$nid;
		}
	}

}
if(count($queries) > 0) {
    foreach ($queries as $nid => $nform) {
        $tpos = $tabBox->add($moddir . 'vw_queries', $nform);
        $_SESSION['wiz_tab'][$tpos] = $nid;
    }
}
$tabBox->show();


/*
$query_string = '?m=tasks&a=view&task_id=' . $task_id;
$tabBox = new CTabBox('?m=tasks&a=view&task_id=' . $task_id, '', $tab);

$tabBox_show = 0;
if ($obj->task_dynamic != 1) {
	// tabbed information boxes
	if ($perms->checkModuleItem('task_log', 'view', $task_id)) {
		$tabBox_show = 1;
		//$tabBox->add(DP_BASE_DIR.'/modules/tasks/vw_logs', 'Activity Logs');
		// fixed bug that dP automatically jumped to access denied if user does not
		// have read-write permissions on task_id and this tab is opened by default (session_vars)
		// only if user has r-w perms on this task, new or edit log is beign showed
		/*if ($perms->checkModuleItem('tasks', 'edit', $task_id)) {
			if ($task_log_id == 0) {
				if ($perms->checkModuleItem('task_log', 'add', $task_id)) {
					$tabBox->add(DP_BASE_DIR.'/modules/tasks/vw_log_update', 'New Log');
				}
			} elseif ($perms->checkModuleItem('task_log', 'edit', $task_id)) {
				$tabBox->add(DP_BASE_DIR.'/modules/tasks/vw_log_update', 'Edit Log');
			} elseif ($perms->checkModuleItem('task_log', 'add', $task_id)) {
				$tabBox_show = 1;
				$tabBox->add(DP_BASE_DIR.'/modules/tasks/vw_log_update', 'New Log');
			}
		}///
	}
}

if (count($obj->getChildren()) > 0) {
	// Has children
	// settings for tasks
	$f = array('children');
	$min_view = true;
	$tabBox_show = 1;
	// in the tasks file there is an if that checks
	// $_GET[task_status]; this patch is to be able to see
	// child tasks withing an inactive task
	$_GET['task_status'] = $obj->task_status;
	$tabBox->add(DP_BASE_DIR.'/modules/tasks/tasks', 'Sub-Activities');
}


//if ($tabBox->loadExtras($m, $a)) {
if($tabBox->loadExtras($m,NULL,array('files','links','events'))){
	$tabBox_show = 1;
}



if ($tabBox_show == 1) {
	$tabBox->show();
}*/
?>
</div>