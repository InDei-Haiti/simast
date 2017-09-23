<?php /* PROJECTS $Id: view.php 5714 2008-05-21 00:47:24Z merlinyoda $ */
if (! defined ( 'DP_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

GLOBAL $project_id;

$project_id = intval ( dPgetParam ( $_GET, 'project_id', 0 ) );
$q = new DBQuery ( );

//check permissions for this record
$perms = & $AppUI->acl ();

$is_superAdmin = false;
$roles = $perms->getUserRoles($AppUI->user_id);
foreach ($roles as $role){
	if($role['value']=='super_admin'){
		$is_superAdmin = true;
	}
}

$canRead = $perms->checkModuleItem ( $m, 'view', $project_id );
$canEdit = $perms->checkModuleItem ( $m, 'edit', $project_id );

$canAddTask = $perms->checkModule ( 'tasks', 'add' );
if(!$is_superAdmin){
	$canRead = true;
	$canEdit = true;
	$canAddTask = true;
}
if (! $canRead) {
	$AppUI->redirect ( 'm=public&a=access_denied' );
}
if(!$is_superAdmin){
	if(!$perms->checkForm($AppUI->user_id,'projects',$project_id,'view')){
		$AppUI->redirect ( 'm=public&a=access_denied' );
	}
}

//retrieve any state parameters
if (isset ( $_GET ['tab'] )) {
	$AppUI->setState ( 'ProjVwTab', $_GET ['tab'] );
}
$tab = $AppUI->getState ( 'ProjVwTab' ) !== NULL ? $AppUI->getState ( 'ProjVwTab' ) : 0;

//check if this record has dependencies to prevent deletion
$msg = '';
$obj = new CProject ( );
//Now check if the proect is editable/viewable.
//$denied = $obj->getDeniedRecords ( $AppUI->user_id );
//if (in_array ( $project_id, $denied )) {
//	$AppUI->redirect ( 'm=public&a=access_denied' );
//}


$scan_edit= $obj->localCheck($AppUI->user_id,$project_id);

$adm_rights = $AppUI->isAdmin();

if($canEdit && ( $scan_edit || $adm_rights)){
	$canEdit=true;
}else{
	$canEdit=false;
}

$canDelete = $obj->canDelete ( $msg, $project_id );

if($canDelete && ( $scan_edit || $adm_rights) ){
	$canDelete=true;
}else{
	$canDelete=false;
}
if(!$is_superAdmin){
	$canDelete=true;
	$canEdit=true;
}
//get critical tasks (criteria: task_end_date)
$criticalTasks = ($project_id > 0) ? $obj->getCriticalTasks ( $project_id ) : NULL;

//get ProjectPriority from sysvals
$projectPriority = dPgetSysVal ( 'ProjectPriority' );
$projectPriorityColor = dPgetSysVal ( 'ProjectPriorityColor' );

$working_hours = ($dPconfig ['daily_working_hours'] ? $dPconfig ['daily_working_hours'] : 8);

//check that project has tasks; otherwise run seperate query
$q->addTable ( 'tasks' );
$q->addQuery ( 'COUNT(distinct tasks.task_id) AS total_tasks' );
$q->addWhere ( 'task_project = ' . $project_id );
$hasTasks = $q->loadResult ();
$q->clear ();

//load the record data
//GJB: Note that we have to special case duration type 24
//and this refers to the hours in a day, NOT 24 hours
$q->addTable ( 'projects' );
$q->addJoin ( 'companies', 'com', 'com.company_id = project_company' );
$q->addJoin ( 'companies', 'com_internal', 'com_internal.company_id = project_company_internal' );
$q->addJoin ( 'users', 'u', 'user_id = project_owner' );
$q->addJoin ( 'contacts', 'con', 'contact_id = user_contact' );
if ($hasTasks) {
	$q->addJoin ( 'tasks', 't1', 'projects.project_id = t1.task_project' );
	$q->addQuery ( 'com.company_name AS company_name, com_internal.company_name' . ' AS company_name_internal' . ", CONCAT_WS(', ',contact_last_name,contact_first_name) user_name" . ', projects.*, SUM(t1.task_duration * t1.task_percent_complete' . " * IF(t1.task_duration_type = 24, {$working_hours}, t1.task_duration_type))" . " / SUM(t1.task_duration * IF(t1.task_duration_type = 24, {$working_hours}," . ' t1.task_duration_type)) AS project_percent_complete' );
	$q->addWhere ( 't1.task_id = t1.task_parent' );
} else {
	$q->addQuery ( 'com.company_name AS company_name, com_internal.company_name' . ' AS company_name_internal' . ", CONCAT_WS(' ',contact_first_name,contact_last_name) user_name, projects.*, " . '(0.0) AS project_percent_complete' );
}
$q->addWhere ( 'project_id = ' . $project_id );
$q->addGroup ( 'project_id' );
$sql = $q->prepare ();
$q->clear ();

$bobj=$obj;
$obj = null;
if (! db_loadObject ( $sql, $obj )) {
	$AppUI->setMsg ( 'Project' );
	$AppUI->setMsg ( 'invalidID', UI_MSG_ERROR, true );
	$AppUI->redirect ();
} else {
	$AppUI->savePlace ();
}

$sname = dPgetSysVal ( "SectorType" );

// get a list of permitted companies
require_once( $AppUI->getModuleClass ('companies' ) );

//$row1 = new CCompany();
//$companies = $row1->getAllowedRecords( $AppUI->user_id, 'company_id,company_acronym', 'company_acronym' );
if($obj->project_cdonors){
	$q = new DBQuery();
	$q->addTable ( 'companies' );
	$q->addQuery ( 'company_acronym' );
	$q->addWhere ( 'company_id in ('.$obj->project_cdonors.')' );
	$companies = $q->loadColumn();
	$companies = implode(", ", $companies);
}


if($obj->project_partners){
	//$obj->project_partners = explode(',', $obj->project_partners);
	$q = new DBQuery();
	$q->addTable ( 'tasks' );
	$q->addQuery ( 'task_agency' );
	$q->addWhere('task_project='.$obj->project_id);
	//var_dump(array_filter($q->loadColumn(), function($var){return !is_null($var);} ));
	$restask = $q->loadColumn();
	if($restask && is_array($restask) && count($restask)>0){
		$v = implode(',', array_filter($restask, function($var){return !is_null($var);} ));
		if($v)
			$obj->project_partners .= ','.$v;
		//$obj->project_partners = implode(',',array_unique(explode(',', $obj->project_partners)));
	}
}else{
	$q = new DBQuery();
	$q->addTable ( 'tasks' );
	$q->addQuery ( 'task_agency' );
	$q->addWhere('task_project='.$obj->project_id);
	//var_dump(array_filter($q->loadColumn(), function($var){return !is_null($var);} ));
	$restask = $q->loadColumn();
	if($restask && count($restask)>0){
		$obj->project_partners = implode(',', array_filter($restask, function($var){return !is_null($var);} ));
		//$obj->project_partners = implode(',',array_unique(explode(',', $obj->project_partners)));
	}
}


if($obj->project_partners){
	$q = new DBQuery();
	$q->addTable ( 'companies' );
	$q->addQuery ( 'company_acronym' );
	$q->addWhere ( 'company_id in ('.$obj->project_partners.')' );
	$obj->project_partners = $q->loadColumn();
	$obj->project_partners = implode(", ", $obj->project_partners);
}

//worked hours
//now milestones are summed up, too, for consistence with the tasks duration sum
//the sums have to be rounded to prevent the sum form having many (unwanted) decimals because of the mysql floating point issue
//more info on http://www.mysql.com/doc/en/Problems_with_float.html
if ($hasTasks) {
	$q->addTable ( 'task_log' );
	$q->addTable ( 'tasks' );
	$q->addQuery ( 'ROUND(SUM(task_log_hours),2)' );
	$q->addWhere ( 'task_log_task = task_id AND task_project = ' . $project_id );
	$sql = $q->prepare ();
	$q->clear ();
	$worked_hours = db_loadResult ( $sql );
	$worked_hours = rtrim ( $worked_hours, '.' );

	//total hours
	//same milestone comment as above, also applies to dynamic tasks
	$q->addTable ( 'tasks' );
	$q->addQuery ( 'ROUND(SUM(task_duration),2)' );
	$q->addWhere ( 'task_duration_type = 24 AND task_dynamic != 1 AND task_project = ' . $project_id );
	$sql = $q->prepare ();
	$q->clear ();
	$days = db_loadResult ( $sql );

	$q->addTable ( 'tasks' );
	$q->addQuery ( 'ROUND(SUM(task_duration),2)' );
	$q->addWhere ( 'task_duration_type = 1 AND task_dynamic != 1 AND task_project = ' . $project_id );
	$sql = $q->prepare ();
	$q->clear ();
	$hours = db_loadResult ( $sql );
	$total_hours = $days * $dPconfig ['daily_working_hours'] + $hours;

	$total_project_hours = 0;

	$q->addTable ( 'tasks', 't' );
	$q->addQuery ( 'ROUND(SUM(t.task_duration*u.perc_assignment/100),2)' );
	$q->addJoin ( 'user_tasks', 'u', 't.task_id = u.task_id' );
	$q->addWhere ( 't.task_duration_type = 24 AND t.task_dynamic != 1 AND t.task_project = ' . $project_id );
	$total_project_days_sql = $q->prepare ();
	$q->clear ();

	$q->addTable ( 'tasks', 't' );
	$q->addQuery ( 'ROUND(SUM(t.task_duration*u.perc_assignment/100),2)' );
	$q->addJoin ( 'user_tasks', 'u', 't.task_id = u.task_id' );
	$q->addWhere ( 't.task_duration_type = 1 AND t.task_dynamic != 1 AND t.task_project = ' . $project_id );
	$total_project_hours_sql = $q->prepare ();
	$q->clear ();

	$total_project_hours = (db_loadResult ( $total_project_days_sql ) * $dPconfig ['daily_working_hours'] + db_loadResult ( $total_project_hours_sql ));
	//due to the round above, we don't want to print decimals unless they really exist
//$total_project_hours = rtrim($total_project_hours, '0');
} else { //no tasks in project so "fake" project data
	$worked_hours = $total_hours = $total_project_hours = 0.00;
}
//get the prefered date format
$df = $AppUI->getPref ( 'SHDATEFORMAT' );

//create Date objects from the datetime fields
$start_date = (intval ( $obj->project_start_date ) ? new CDate ( $obj->project_start_date ) : null);
$end_date = (intval ( $obj->project_end_date ) ? new CDate ( $obj->project_end_date ) : null);
$actual_end_date = (intval ( $criticalTasks [0] ['task_end_date'] ) ? new CDate ( $criticalTasks [0] ['task_end_date'] ) : null);
$style = ((($actual_end_date > $end_date) && ! empty ( $end_date )) ? 'style="color:red; font-weight:bold"' : '');

//setup the title block
//$titleBlock = new CTitleBlock('View Project', 'applet3-48.png', $m, "$m.$a");
$titleBlock = new CTitleBlock ( 'View Project:'.$obj->project_name, '', $m, "$m.$a" );

if($obj->project_comps != ''){
	$q= new DBQuery();
	$q->addTable("program_comps");
	$q->addQuery('ctitle');
	$q->addWhere('id IN ('.$obj->project_comps . ')');
	$comps_html = @join(", ",@array_keys($q->loadArrayList()));
}

//patch 2.12.04 text to search entry box
if (isset ( $_POST ['searchtext'] )) {
	$AppUI->setState ( 'searchtext', $_POST ['searchtext'] );
}

$search_text = (($AppUI->getState ( 'searchtext' )) ? $AppUI->getState ( 'searchtext' ) : '');
//$titleBlock->addCell ( $AppUI->_ ( 'Search' ) . ':' );
//$titleBlock->addCell ( ('<input type="text" class="text" SIZE="10" name="searchtext"' . ' onChange="document.searchfilter.submit();" value="' . $search_text . '"' . 'title="' . $AppUI->_ ( 'Search in name and description fields' ) . '"/><!--<input type="submit" class="button" value=">" title="' . $AppUI->_ ( 'Search in name and description fields' ) . '"/>-->'), '', ('<form action="?m=projects&a=view&project_id=' . $project_id . '" method="post" id="searchfilter">'), '</form>' );

/*if ($canAddTask) {
	$titleBlock->addCell();
	$titleBlock->addCell(('<input type="submit" class="button" value="' . $AppUI->_('new activity')
	                      . '">'), '', ('<form action="?m=tasks&a=addedit&task_project='
	                                    . $project_id . '" method="post">'), '</form>');
}*/
/*if ($canEdit) {
	/*$titleBlock->addCell();
	$titleBlock->addCell(('<input type="submit" class="button" value="' . $AppUI->_('new event')
	                      . '">'), '', ('<form action="?m=calendar&a=addedit&event_project='
	                                    . $project_id . '" method="post">'), '</form>');

	$titleBlock->addCell();
	$titleBlock->addCell(('<input type="submit" class="button" value="' . $AppUI->_('new file')
	                      . '">'), '', ('<form action="?m=files&a=addedit&project_id='
	                                    . $project_id . '" method="post">'), '</form>');

}*/
//$titleBlock->addCrumb('?m=projects', 'projects list');
if ($canEdit) {
	$titleBlock->addCrumb ( ('?m=projects&a=addedit&project_id=' . $project_id), 'edit this project' );
	$titleBlock->addCrumb ( ('?m=projects'), 'lists of projects' );
    if ($_GET ['m'] == "projects" && $_GET ['a'] == "view" && $_GET ['project_id'] > 0 && $canEdit) {
        $titleBlock->addCrumb ('?m=tasks&a=addedit&task_project=' . $project_id , $AppUI->_('new activity') );
    }
	/*$titleBlock->addCrumb ( ('?m=projects'), 'lists of projects' );

    echo '<a href="?m=tasks&a=addedit&task_id='.$parent_task.'&pmode=newsub"><b>New Sub-Activity</b></a>';*/
	if ($canDelete) {
		//$titleBlock->addCrumbDelete ( 'delete project', $canDelete, $msg );
	}
	//$titleBlock->addCrumb('?m=tasks&a=organize&project_id=' . $project_id, 'organize activities');
}
//$titleBlock->addCrumb('?m=projects&a=reports&project_id=' . $project_id, 'reports');
?>
    <br/>
    <div class="card">
<!--				<strong>My Test--><?php //echo $project_id?><!--</strong>-->
        <div class="block-header">
            <h2 style="border-bottom: 1px solid #d0d0d0;padding-bottom: 10px">
                <?php
                $titleBlock->show ();
                ?>
            </h2>
            </div>&nbsp;
        <script language="javascript">
            <?php
            //security improvement:
            //some javascript functions may not appear on client side in case of user not having write permissions
            //else users would be able to arbitrarily run 'bad' functions
            if ($canDelete) {
            ?>
            function delIt() {
                if (confirm("<?php
                        echo ($AppUI->_ ( 'doDelete', UI_OUTPUT_JS ) . ' ' . $AppUI->_ ( 'Project', UI_OUTPUT_JS ) . '?');
                        ?>")) {
                    document.frmDelete.submit();
                }
            }
            <?php
            }
            //background-color:#<?php $obj->project_color_identifier;
            ?>
        </script>
                <!--<div class="fa fa-toggle-down" style="margin-left: -20px;padding-top: 10px;padding-bottom: 10px"
                      onclick="if(!$j('#view_project').is(':visible'))$j('#view_project').show();else $j('#view_project').hide()"> Show more</div>-->
        <table border="0" cellpadding="2" cellspacing="0" width="100%"
               class="std" id="view_project" style="">

            <form name="frmDelete" action="./index.php?m=projects" method="post"><input
                        type="hidden" name="dosql" value="do_project_aed" /> <input
                        type="hidden" name="del" value="1" /> <input type="hidden"
                                                                     name="project_id" value="<?php
                echo $project_id;
                ?>" />
            </form>

            <tr>
                <td style="border: outset #d1d1cd 0px;" colspan="2">
                   <!-- --><?php
/*                    echo ('<span style="font-weight:bold">' . $obj->project_name . '</span>');
                    */?>
                </td>
            </tr>
            <tr>
                <td width="49%" valign="top" class="mtab" style="padding-right: 10px;"><!-- <strong><?php
                    echo $AppUI->_ ( 'Details' );
                    ?></strong> -->
                    <table cellspacing="1" cellpadding="1" border="0" width="100%" class="tbl">
                        <tr>
                            <td align="right" nowrap style="background-color: #fff;;height: 40px"><?php
                                echo $AppUI->_ ( 'Project Type' );
                                ?>:
                            </td>
                            <td class="hilite" style="width: 70%"><?php
                                $t = dPgetSysVal('ProjectType');
                                echo $t[$obj->project_type];
                                ?></td>
                        </tr>
                        <tr>
                            <td align="right" nowrap style="background-color: #fff;height: 40px"><?php
                                echo $AppUI->_ ( 'Partners' );
                                ?>:</td>
                            <td class="hilite" style=""><?php
                                echo $obj->project_partners;
                                //$obj->project_partners2;//multiView($obj->project_partners,$obj->project_donor );
                                ?></td>
                        </tr>


                        <tr>
                            <td align="right" nowrap style="background-color: #fff;height: 40px"><?php echo $AppUI->_ ( 'Sectors' ); ?>:</td>
                            <td class="hilite" style="">
                                <?php


                                $ts=implode(", ",$sname);
                                $pobj = new CProject();
                                $slist = $pobj->getSectors (  $obj->project_id);
                                $sex = array();
                                foreach ( $slist as $sector ) {
                                    if ($sector ['task_sector'] != "") {
                                        //$sex[]= multiView($sname, $sector ['task_sector']) . ", ";
                                        $sex[] = $sector['task_sector'];
                                    }
                                }
                                //echo join(",",$sex);
                                $sex = array_unique($sex);
                                //var_dump($sex);
                                echo multiView($sname, implode(",",$sex));
                                ?></td>
                        </tr>

                        <tr>
                            <td align="right" nowrap style="background-color: #fff;!important;height: 40px"><?php
                                echo $AppUI->_ ( 'Donors' );
                                ?>:</td>
                            <td class="hilite" style=""><?php
                                echo $companies;//multiView($companies,$obj->project_cdonors);
                                ?></td>
                        </tr>

                        <tr>
                            <td align="right" nowrap style="background-color: #fff;height: 40px"><?php echo $AppUI->_ ( 'Strategic Areas' ); ?>:</td>
                            <td class="hilite moreless" style="">
                                <?php

                                $slist = $pobj->getAreas( $obj->project_id);
                                $inlist = array();
                                foreach ($slist as $svl) {
                                    if($svl['task_areas'] != ''){
                                        $inlist[]=$svl['task_areas'];
                                    }
                                }
                                if(count($inlist) > 0){
                                    $q  =new DBQuery();
                                    $q->addTable('st_area');
                                    $q->addWhere('id  in ('.join(",",$inlist) .')');
                                    //$q->addQuery('concat(prex," ",title) as name');
                                    $q->addQuery('id, prex,title');
                                    //var_dump($q->loadList());
                                    //$area_list = @join(", ",array_keys(@$q->loadList()));
                                    $area_list = @$q->loadList();
                                }
                                if(count($area_list)>0){
                                    $area_l_h = array();
                                    foreach ($area_list as $area){
                                        $area_l_h[] = '<span title="'.$area['title'].'">'.$area['prex'].'</span>';
                                    }
                                    $area_l_h = @join(", ",$area_l_h);
                                    echo $area_l_h;
                                }
                                ?></td>
                        </tr>
                        <tr>
                            <td align="right" nowrap style="background-color: #fff;!important;height: 40px"><?php echo $AppUI->_ ( 'Locations' ); ?>:</td>
                            <td class="hilite moreless" style="">
                                <?php

                                $slist = $pobj->getLocations( $obj->project_id);

                                $inlist = array();
                                foreach ($slist as $svl) {
                                    if($svl['task_locations'] != ''){
                                        $inlist[]=$svl['task_locations'];
                                    }
                                }
                                $locs_html = '';
                                if(count($inlist) > 0){
                                    $q  = new DBQuery();
                                    $q->addTable('administration_com');
                                    $q->addWhere('administration_com_code  in ('.join(",",$inlist) .')');
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
                                    $locs_html .= '<script>

										var morevar = "<b>'.$AppUI->_ ( 'More' ).'...</b>";
									var hidevar = "<b>'.$AppUI->_ ( 'Less' ).'</b>";
									function showhide(){
										ele = document.getElementById("moreless");
										classv = ele.getAttribute("class");
										if(classv === "moreAction"){
											ele.setAttribute("class","lessAction");
											$(\'.chidden\').addClass(\'cvisible\');
											$(\'.chidden\').removeClass(\'chidden\');
											$(\'#tab_loc > td\').css(\'width\',50);
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
                                        $locs_html .= '<a href="javascript:showhide()" id="moreless" class="moreAction"><b>'.$AppUI->_ ( 'More' ).'...</b></a>';
                                    }
                                }


                                echo $locs_html;

                                ?></td>
                        </tr>



                    </table>
                </td>
                <td width="49%" rowspan="9" valign="top" class="mtab" style="padding-left: 10px;">
                    <table cellspacing="1" cellpadding="2" border="0" width="100%" class="tbl">
                        <tr>
                            <td align="right" nowrap style="background-color: #fff;height: 40px"><?php
                                echo $AppUI->_ ( 'Start Date' );
                                ?>:</td>
                            <td class="hilite" style="width: 70%"><?php
                                echo $start_date ? $start_date->format ( $df ) : '-';
                                ?></td>
                        </tr>
                        <tr>
                            <td align="right" nowrap style="background-color: #fff;height: 40px"><?php
                                echo $AppUI->_ ( 'End Date' );
                                ?>:</td>
                            <td class="hilite" style="width: 70%"><?php
                                echo $end_date ? $end_date->format ( $df ) : '-';
                                ?></td>
                        </tr>
                        <tr>
                            <td align="right" nowrap style="background-color: #fff;height: 40px"><?php
                                echo $AppUI->_ ( 'Budget' );
                                ?>:</td>
                            <td class="hilite" style=""><?php
                                echo $dPconfig ['currency_symbol']?>&nbsp;<?php

                                echo @$obj->project_target_budget;
                                ?></td>
                        </tr>
                        <tr>
                            <td align="right" nowrap style="background-color: #fff;height: 40px"><?php
                                echo $AppUI->_ ( 'Yearly Budget' );
                                ?>:</td>
                            <td class="hilite" style=""><?php
                                $yb = str_replace(array(";","|"),array("<br>"," - "),$obj->project_annual_budget);
                                echo $yb;

                                ?></td>
                        </tr>
                        <tr>
                            <td align="right" nowrap style="background-color: #fff;height: 40px"><?php
                                echo $AppUI->_ ( 'Status' );
                                ?>:</td>
                            <td class="hilite" width="100%" style=""><?php
                                echo $AppUI->_ ( $pstatus [$obj->project_status] );
                                ?></td>
                        </tr>
                        <tr>
                            <td align="right" nowrap style="background-color: #fff;height: 40px"><?php
                                echo $AppUI->_ ( 'Beneficiaries' );
                                ?>:</td>
                            <td class="hilite" style=""><?php
                                echo @$obj->project_actual_budget;
                                ?></td>
                        </tr>
                        <tr>
                            <td align="right" style="height: 40px"><strong><?php
                                    echo $AppUI->_ ( 'Description' );
                                    ?></strong></td>

                            <td class="hilite" style="">
                                <?php if(strlen($obj->project_description) <= 60)
                                    echo$obj->project_description;
                                else{
                                    echo substr($obj->project_description,0,59).'<font color="red">...</font><span class="title">'.$obj->project_description.'</span>';
                                }

                                ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    </div>

    <div class="card">

<?php
$me = '';
if(isset($_GET['user']) && $_GET['user']==='me'){
	$me = '&user='.$_GET['user'];
}
$tabBox = new CTabBox ( ('?m=projects&a=view&project_id=' . $project_id.$me), '', $tab );
$query_string = ('?m=projects&a=view&project_id=' . $project_id);
//tabbed information boxes
//Note that we now control these based upon module requirements.
$canViewTask = $perms->checkModule ( 'tasks', 'view' );
$canViewTaskLog = $perms->checkModule ( 'task_log', 'view' );
if ($canViewTask) {
	$q = new DBQuery();
	$q->addTable('tasks');
	$q->addQuery("count(*)");
	$q->addWhere('task_project='.$project_id);
	if(!$is_superAdmin){
		$q->addJoin('permission_form','pf', 'task_id=pf.form');
		$q->addWhere('pf.user_id='.$AppUI->user_id);
		$q->addWhere('pf.form=task_id');
		$q->addWhere('pf.module="activity"');
		$option = $perms->getAcoIdByValue('view');
		$q->addWhere('find_in_set("'.$option.'",replace(type, " ", ","))');
	}


	$fentries=(int) $q->loadResult();
	$tabBox->add ( DP_BASE_DIR . '/modules/tasks/tasks', $AppUI->_('Activities').' ('.$fentries.')' );
	//$tabBox->add(DP_BASE_DIR.'/modules/tasks/tasks', 'Activities (Inactive)');
}

if ($perms->checkModule('files', 'view')) {
	$tabBox->add(DP_BASE_DIR.'/modules/projects/vw_files', $AppUI->_('Files'));
}



$q = new DBQuery();
$q->addTable('form_master');
$q->addQuery('id,title');
//$q->addWhere('registry = "0"');
$q->addWhere('valid="1"');
$q->addWhere('(project_id='.$project_id.' AND alltask=1)');
$option = $perms->getAcoIdByValue('view');
$newforms = $q->loadHashList();

$_SESSION['wiz_tab']=array();
if(count($newforms) > 0){
	foreach($newforms as $nid => $nform){
		if(!$is_superAdmin){
			if($perms->checkForm($AppUI->user_id, 'wizard', $nid, 'view')){
				$q = new DBQuery();
				$q->addTable('wform_'.$nid);
				$q->addQuery("count(*)");
				if(isset($_GET['user']) && $_GET['user']==='me'){
					$q->addWhere('wform_'.$nid.'.user_creator='.$AppUI->user_id);
				}
				$fentries=(int) $q->loadResult();

				$tpos=$tabBox->add ( DP_BASE_DIR . '/modules/projects/vw_wizard', $AppUI->_($nform).' ('.$fentries.')' );
				$_SESSION['wiz_tab'][$tpos]=$nid;

			}
		}else{
			$q = new DBQuery();
			$q->addTable('wform_'.$nid);
			$q->addQuery("count(*)");
			if(isset($_GET['user']) && $_GET['user']==='me'){
				$q->addWhere('wform_'.$nid.'.user_creator='.$AppUI->user_id);
			}
			$fentries=(int) $q->loadResult();
			$tpos=$tabBox->add ( DP_BASE_DIR . '/modules/projects/vw_wizard', $AppUI->_($nform).' ('.$fentries.')');
			$_SESSION['wiz_tab'][$tpos]=$nid;
		}

	}
}

//$tabBox->add ( DP_BASE_DIR . '/modules/projects/vw_wizard', 'Resgistrations' );






if ($canViewTask) {
	//$tabBox->add(DP_BASE_DIR.'/modules/tasks/viewgantt', 'Gantt Chart');
	if ($canViewTaskLog) {
		//$tabBox->add(DP_BASE_DIR.'/modules/projects/vw_logs', 'Activity Logs');
	}
}
//$tabBox->loadExtras ( $m, NULL, array ('files' ) );//, 'links', 'events'

/*if ($perms->checkModule ( 'forums', 'view' )) {
	$tabBox->add ( DP_BASE_DIR . '/modules/projects/vw_forums', 'Forums' );
}*/
$f = array('all');
$min_view = true;

echo '<script>var project_id='.$project_id.';</script>';

$tabBox->show('','');
?>
    </div>
