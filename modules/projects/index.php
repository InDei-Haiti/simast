<?php  /* PROJECTS $Id: index.php 5776 2008-07-22 13:36:04Z merlinyoda $ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}
// Checking for old template value
$oldtemplate = FALSE;
if (isset($_GET['template']) && ($_GET['template'] == 'old')) {
	$oldtemplate = TRUE;
}

if($_GET['mode'] == 'uploadf'){
	if (isset($_FILES['file']) && !$_FILES['file']['error'] && $_FILES['file']['name'] != '') {
		$file_id = intval(dPgetParam($_POST, 'file_id', 0));
		$upload = $_FILES['file'];
		$file_data = json_decode(stripslashes($_POST['fdata']),true);
		require './modules/files/files.class.php';
			global $db;
			$fobj = new CFile();
			$fobj->file_nick			= $file_data['file_nick'];
			$fobj->file_category		= $file_data['file_type'];
			$fobj->file_name			= $upload['name'];
			$fobj->file_type			= $upload['type'];
			$fobj->file_size			= $upload['size'];
			$fobj->file_date			= str_replace("'", '', $db->DBTimeStamp(time()));
			$fobj->file_view_date		= str_replace("'", '', $db->DBTimeStamp(time()));
			$fobj->file_real_filename	= uniqid(rand());
			$fobj->file_version_id		= getNextVersionID();
			$fobj->file_project			= $file_data['project_id'];
			$fobj->file_country			= 1;
			$fobj->file_owner			= $AppUI->user_id;
			$fobj->file_sector			= -1;
			$fobj->file_activity_type	= -1;
			$fobj->file_version			= 1;
			//$fobj->moveTemp($upload);
			$fobj->moveCommonStore($upload);
			$fobj->store();

		if($fobj->file_id > 0){
			echo json_encode(array('id'=>$fobj->file_id,'file_name'=>$upload['name']));
		}
	}
	return;
}

if($_GET['mode'] == 'getProjects'){
	$q = new DBQuery();
	$q->addTable("projects");
	$q->addQuery('project_id,project_name');
	$sql = $q->prepare();
	$res = mysql_query($sql);
	$data = array();
	if ($res) {
		while($row = mysql_fetch_assoc($res)){
			$data[] = $row;
		}
		echo json_encode($data);
	}else{
		echo 'Failed';
	}
	
	return; 
}


$AppUI->savePlace();
//var_dump($_POST);
// load the companies class to retrieved denied companies
require_once ($AppUI->getModuleClass('companies'));

// Let's update project status!
if (isset($_GET['update_project_status']) && isset($_GET['project_status']) 
   && isset($_GET['project_id'])) {
	$projects_id = $_GET['project_id']; // This must be an array
	
	foreach ($projects_id as $project_id) {
		$r  = new DBQuery;
		$r->addTable('projects');
		$r->addUpdate('project_status', $_GET['project_status']);
		$r->addWhere('project_id = ' . $project_id);
		$r->exec();
		$r->clear();
	}
}



// End of project status update
// retrieve any state parameters
if (isset($_GET['tab'])) {
	$AppUI->setState('ProjIdxTab', $_GET['tab']);
}

$tab = $AppUI->getState('ProjIdxTab') !== NULL ? $AppUI->getState('ProjIdxTab') : 500;
$currentTabId = $tab;
$active = intval(!$AppUI->getState('ProjIdxTab'));

$company_prefix = 'company_';

if (isset($_REQUEST['template']) && ($_REQUEST['template'] == 'old')) {
	if (isset($_REQUEST['company_id'])) {
		$AppUI->setState('ProjIdxCompany', $_REQUEST['company_id']);
	}
	if (isset($_REQUEST['province_id'])) {
		$AppUI->setState('ProjIdxCountry', $_REQUEST['province_id']);
	}
	if (isset($_REQUEST['sector'])) {
		$AppUI->setState('ProjIdxSector', $_REQUEST['sector']);
	}
	if (isset($_REQUEST['project_id'])) {
		$AppUI->setState('ProjIdxProject', $_REQUEST['project_id']);
	}
	if (isset($_REQUEST['progs'])) {
		$AppUI->setState('ProjIdxProg', $_REQUEST['progs']);
	}
	if (isset($_REQUEST['pareas'])) {
		$AppUI->setState('ProjIdxPareas', $_REQUEST['pareas']);
	}
	if (isset($_REQUEST['project_type'])) {
		$AppUI->setState('ProjIdxPTypes', $_REQUEST['project_type']);
	}
}

if($AppUI->getState('ProjIdxCompany') !== NULL){
	$company_id =$AppUI->getState('ProjIdxCompany'); 			
}else{
	if ($AppUI->user_id > 1 && $AppUI->user_company > 0){	
    	$company_id = array($company_prefix.$AppUI->user_company);
    }else{
    	$company_id = null;
    }
}

$country_id = (($AppUI->getState('ProjIdxCountry') !== NULL) 
               ? $AppUI->getState('ProjIdxCountry') 
               : NULL);

$sector_id = (($AppUI->getState('ProjIdxSector') !== NULL) 
               ? $AppUI->getState('ProjIdxSector') 
               : NULL);

$project_id = (($AppUI->getState('ProjIdxProject') !== NULL) 
               ? $AppUI->getState('ProjIdxProject')  
               : NULL);

$progs_id = (   ($AppUI->getState('ProjIdxProg') !== NULL)
               ? $AppUI->getState('ProjIdxProg')
               : NULL);
$pareas = (   ($AppUI->getState('ProjIdxPareas') !== NULL)
               ? $AppUI->getState('ProjIdxPareas')
               : NULL);
$projtypes = (   ($AppUI->getState('ProjIdxPTypes') !== NULL)
               ? $AppUI->getState('ProjIdxPTypes')
               : NULL);

//since now all the flags will be turned right after they were used, so no more stable flags on
$AppUI->setState('ProjIdxCompany', array(0));
$AppUI->setState('ProjIdxCountry', array(0));
$AppUI->setState('ProjIdxSector', array(0));
$AppUI->setState('ProjIdxProject', array(0));
$AppUI->setState('ProjIdxProg', array(0));
$AppUI->setState('ProjIdxPareas', array(0));
$AppUI->setState('ProjIdxPTypes', array(-1));


if($_POST['clear'] == "clear"){
	$company_id = array(0);
	$country_id = array(0);
	$sector_id  = array(0);
	$project_id  = array(0);
	$progs_id  = array(0);
	$pareas  = array(0);
	$projtypes  = array(-1);
}
global $filshow;

$filshow = false;

$valid_ordering = array(
	'project_name',
	'user_username',
	'my_tasks desc',
	'total_tasks desc',
	'total_tasks',
	'my_tasks',
	'project_color_identifier',
	'company_name',
	'project_end_date',
	'project_start_date',
	'project_actual_end_date',
	'task_log_problem DESC,project_priority',
	'project_status'
);

if (isset($_GET['orderby']) && in_array($_GET['orderby'], $valid_ordering)) {
	$orderdir = (($AppUI->getState('ProjIdxOrderDir') == 'asc') ? 'desc' : 'asc');
	$AppUI->setState('ProjIdxOrderBy', $_GET['orderby']);
}else{
	$orderdir = $AppUI->getState('ProjIdxOrderDir') ? $AppUI->getState('ProjIdxOrderDir') : 'asc';
}
$orderby = (($AppUI->getState('ProjIdxOrderBy'))
            ? $AppUI->getState('ProjIdxOrderBy') : 'project_end_date');
$AppUI->setState('ProjIdxOrderDir', $orderdir);

// prepare the users filter
if (isset($_POST['show_owner'])) {
	$AppUI->setState('ProjIdxowner', intval($_POST['show_owner']));
}
$owner = $AppUI->getState('ProjIdxowner') !== NULL ? $AppUI->getState('ProjIdxowner') : 0;
if($oldtemplate === false){
	$owner = $AppUI->user_id;
}

$q = new DBQuery();
$q->addTable('st_area');
$q->addQuery('id,concat(prex,".",title) as name');
$q->addWhere('parent_id="0"');
$areas = $q->loadHashList();

if((is_array($pareas) && in_array("0",$pareas)) || !$pareas){
	$zstr = 'selected';
}else{
	$zstr='';
}
$par_code="<select name='pareas[]' title='Strategic Areas' class='text multiple' multiple size=3 style='width: 200px;'>\n\t<option value='0' $zstr style='font-weight: bold;'>All Areas</option>\n";
foreach($areas as $akey => $aval){
	$zstr='';
	if(is_array($pareas) && in_array($akey,$pareas)){
		$zstr = 'selected';
	}
	$par_code.='<option value="'.$akey.'" '.$zstr.'>'.$aval.'</option>';
}
$par_code.='</select>';

if ($tab != 7 && $tab != 8) {
	$project_status = $tab;
} elseif ($tab == 0) {
	$project_status = 0;
}
if ($tab == 5 || $tab == 7) {
	$project_active = 0;
}


//for getting permissions for records related to projects
$obj_project = new CProject();
// collect the full (or filtered) projects list data via function in projects.class.php
projects_list_data();

$project_types = dPgetSysVal('ProjectStatus');

$q  = new DBQuery();
$q->clear();
$q->addTable('projects', 'p');

$q->addQuery('p.project_status, COUNT(p.project_id) as count');
//$q->addJoin('tasks', 't', 'p.project_id = t.task_project');	
$obj_project->setAllowedSQL($AppUI->user_id, $q, null, 'p');
if ($owner > 0) {
	$q->addWhere('p.project_owner = ' . $owner);
}

if ($sector_id) {

	if ($sector_id) {
		if (count($sector_id) > 0){
			/*$dsql=" in (".implode(", ",$sector_id).")";*/
			$pres = array();
			foreach ($sector_id as $seid){
				if($seid > 0){
					$pres[]='task_sector REGEXP [[:<:]]'.$seid.'[[:>:]]';
				}
			}
			$tsector = '( '. join(" OR ",$pres).' )';
			//$lwehre[] = $dsql;
		}else {
			$tsector="";
		}
	}
}

$ssql="";

if($tsector!="") $ssql=$tsector;
if ($tcountry!=""){
	if($ssql!=""){
		$ssql.=" AND ";
	}
	$ssql.=$tcountry;
}
$project_typesSys = dPgetSysVal("ProjectType");

//if ($company_id) {
$tcomp = "";
if (is_array ( $company_id )) {
		if (count ( $company_id ) == 1 && $company_id[0] != "0") {
			$tcomp=" task_company = '".substr($company_id [0],strlen($company_prefix))."'";
			if($ssql != "")$ssql.=" AND ";
			$ssql.=$tcomp;
			$q->addWhere ( '( p.project_company = "' .substr($company_id [0],strlen($company_prefix)) ."\" OR  p.project_id in (select distinct  task_project from tasks where $ssql) )" );
			
		} elseif (count ( $company_id ) > 1) {
			$comps=array();
			foreach ($company_id as $cid){
				
				$tco=substr($cid,strlen($company_prefix));
				if($tco > 0)$comps[]=$tco;
			}
			$tcomp=" task_company IN ( ".implode ( ", ", $comps ) ." ) ";
			if($ssql != "")$ssql.=" AND ";
			$ssql.=$tcomp;
			$q->addWhere ( '( p.project_company in (' . implode ( ", ", $comps ) . ") OR 
							p.project_id in (select distinct  task_project from tasks where $ssql) )" );
			
		}
}
if ($ssql != "" && $tcomp == ""){
	$q->addWhere("project_id in (select distinct  task_project from tasks where $ssql)");	
}


//set file used per project status title
$fixed_status = array('In Progress' => 'vw_idx_proposed',
					  'Complete' => 'vw_idx_proposed',
					  'Archived' => 'vw_idx_archived');

/**
* Now, we will figure out which vw_idx file are available
* for each project status using the $fixed_status array 
*/
$project_status_file = array();
foreach ($project_types as $status_id => $status_title) {
	//if there is no fixed vw_idx file, we will use vw_idx_proposed
	$project_status_file[$status_id] = ((isset($fixed_status[$status_title])) 
										? $fixed_status[$status_title] : 'vw_idx_proposed');
}
$df = $AppUI->getPref('SHDATEFORMAT');
$oldtemplate = false;
$sql= 'select * from st_area where parent_id = "0" order by id';
$res = mysql_query($sql);
$top_areas = array();
if($res){
    while($row = mysql_fetch_assoc($res)){
        $top_areas[$row['id']]=$row['prex'].'.'.$row['title'];
    }
}
$topArId = array_keys($top_areas);
if ($oldtemplate) {
	// tabbed information boxes
	$tabBox = new CTabBox('?m=projects', DP_BASE_DIR . '/modules/projects/', $tab);

	$q = new DBQuery();
	$q->addTable('projects');
	$q->addQuery("count(*) as c");
	
	$tabBox->add('vw_idx_proposed', $AppUI->_('All') , true,  500);
	krsort($project_types);
	foreach ($project_types as $psk => $project_status) {
		$sql= 'select count(*) as count from projects where project_status="'.$psk.'"';
			$q1 = new DBQuery();
			$q1->addTable('projects');
			$q1->addQuery("count(*) as c");
			$q1->addWhere('project_status="'.$psk.'"');
			$count = $q1->loadResult();
			$tabBox->add($project_status_file[$psk],
						 (($project_status_tabs[$psk]) ? $project_status_tabs[$psk] : $AppUI->_($project_status)), true, $psk);
	}
	$min_view = true;
	$tabBox->show();
	$myspeed->addJs('/modules/projects/projects.module.js','file');
	$myspeed->addCss('/modules/projects/projects.module.css');
	
} else {
	/*echo '<table cellspacing="0" cellpadding="3" border="0" width="100%">
			<tr>
				<td width="100%">Welcome ' . $AppUI->user_first_name . ' ' . $AppUI->user_last_name . '</td>
				<td nowrap="nowrap"> <a href="./index.php?logout=-1&inqms=1">Logout</a></td>
			</tr>
		</table>';//<a href="./index.php?m=projects&template=old">Old Template</a>*/	
	//require_once(DP_BASE_DIR . '/modules/projects/vw_idx_proposed.php');
	?>
    <br/>
<div class="card">
    <!--<div class="block-header">
        <h2 style="border-bottom: 1px solid #d0d0d0;padding-bottom: 10px">Projects</h2>
    </div>-->

    <DIV id="tabs" class="bigtab" style="background: transparent !important;">
		<ul class="topnav tabs-nav" style="background: transparent !important;border-color: transparent">

			<?php
			 $i=0;  echo '<LI><A href="#tabs-'.$i.'"><span>'.$AppUI->_('All').'</span></A></LI>';
            foreach ($project_types as $psk => $project_status) {
                $i++;
                echo '<LI><A href="#tabs-'.$i.'"><span>'.$AppUI->_($project_status).'</span></A></LI>';
            }
			?>

		</ul>
		<?php
        $i = 0;
        echo '<div id="tabs-' . $i . '" class="mtab" style="margin-left: -20px">';
        echo '<table cellspacing="1" cellpadding="2" border="0" id="stable" class="tbl tablesorter moretable" style="clear:both;width: 100%">
				<thead>
				<tr>
				<th colspan="8">
				<form class="ox axy ail" method=GET action="./index.php">
        <input type="hidden" name="m" value="projects" />
        <input type="hidden" name="a" value="addedit" />
        <input class="form-control ce pi ahr btn" type="submit" data-action="grow" value="'.$AppUI->__("New Project").'" placeholder="Search" style="font-weight: 500">
    </form>
</th>
</tr>
					<tr>';
        echo "<th>". $AppUI->_('Project') ."</th>".
            "<th>". $AppUI->_('Project Type') ."</th>".
            "<th>". $AppUI->_('Partner Agencies') ."</th>".
            "<th>". $AppUI->_('Donor Agencies') ."</th>".
            "<th>". $AppUI->_('Sectors') ."</th>".
            "<th>". $AppUI->_('Structure Areas') ."</th>".
            "<th>". $AppUI->_('Start') ."</th>".
            "<th>". $AppUI->_('End') ."</th>";
        echo '</tr>
		</thead>
		<tbody>';
        if($projects && is_array($projects)) {
            foreach ($projects as &$row) {
                $proj_obj = new CProject();
                $uname	= htmlspecialchars( ((strlen($row['project_name']) > 55) ? substr($row['project_name'],0,55).'...' : $row['project_name']), ENT_QUOTES);
                if($row['project_partners']){
                    $q = new DBQuery();
                    $q->addTable ( 'companies' );
                    $q->addQuery ( 'company_acronym' );
                    $q->addWhere ( 'company_id in ('.$row['project_partners'] .')' );
                    $partners = $q->loadColumn();
                    $partners = implode(", ", $partners);
                }
                if($row['project_cdonors']){
                    $q = new DBQuery();
                    $q->addTable ( 'companies' );
                    $q->addQuery ( 'company_acronym' );
                    $q->addWhere ( 'company_id in ('.$row['project_cdonors'].')' );
                    $donors = $q->loadColumn();
                    $donors  = implode(", ", $donors);
                }
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

                $start_date			= ((intval(@$row['project_start_date']))	? new CDate($row['project_start_date']) : null);
                $end_date			= ((intval(@$row['project_end_date']))	? new CDate($row['project_end_date']) : null);
                $start_date = $start_date ? $start_date->format($df) : '-';
                $end_date = $end_date ? $end_date->format($df) : '-';

				echo '<tr>'.
					    '<td><a href="?m=projects&a=view&project_id='.$row['project_id'].'&tab=0">'.$uname.'</a></td>'.
						'<td>'.$project_typesSys[$row['project_type']].'</td>'.
						'<td>'.$partners.'</td>'.
						'<td>'.$donors.'</td>'.
						'<td>'.$sector_teaser.'</td>'.
						'<td>'.$area_teaser.'</td>'.
						'<td>'.$start_date.' '.$row['project_status'].'</td>'.
						'<td>'.$end_date.'</td>'.
					 '</tr>';
            }
        }
        echo '</tbody>
		</table>
        </div>';
        foreach ($project_types as $psk => $project_status) {
            $i++;
            echo '<div id="tabs-' . $i . '" class="mtab" style="margin-left: -20px">';
            echo '<table cellspacing="1" cellpadding="2" border="0" id="stable" class="tbl tablesorter moretable" style="clear:both;width: 100%">
				<thead>
					<tr>';
            echo "<th>". $AppUI->_('Project') ."</th>".
                "<th>". $AppUI->_('Project Type') ."</th>".
                "<th>". $AppUI->_('Partner Agencies') ."</th>".
                "<th>". $AppUI->_('Donor Agencies') ."</th>".
                "<th>". $AppUI->_('Sectors') ."</th>".
                "<th>". $AppUI->_('Structure Areas') ."</th>".
                "<th>". $AppUI->_('Start') ."</th>".
                "<th>". $AppUI->_('End')."</th>";
            echo '</tr>
                  </thead>
                    <tbody>';
        if($projects && is_array($projects)) {
            foreach ($projects as &$row) {
                if($row['project_status'] != $psk)
                    continue;
                $proj_obj = new CProject();
                $uname	= htmlspecialchars( ((strlen($row['project_name']) > 55) ? substr($row['project_name'],0,55).'...' : $row['project_name']), ENT_QUOTES);
                if($row['project_partners']){
                    $q = new DBQuery();
                    $q->addTable ( 'companies' );
                    $q->addQuery ( 'company_acronym' );
                    $q->addWhere ( 'company_id in ('.$row['project_partners'] .')' );
                    $partners = $q->loadColumn();
                    $partners = implode(", ", $partners);
                }
                if($row['project_cdonors']){
                    $q = new DBQuery();
                    $q->addTable ( 'companies' );
                    $q->addQuery ( 'company_acronym' );
                    $q->addWhere ( 'company_id in ('.$row['project_cdonors'].')' );
                    $donors = $q->loadColumn();
                    $donors  = implode(", ", $donors);
                }
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

                $start_date			= ((intval(@$row['project_start_date']))	? new CDate($row['project_start_date']) : null);
                $end_date			= ((intval(@$row['project_end_date']))	? new CDate($row['project_end_date']) : null);
                $start_date = $start_date ? $start_date->format($df) : '-';
                $end_date = $end_date ? $end_date->format($df) : '-';
                echo '<tr>'.
                        '<td>'.$uname.'</td>'.
                        '<td>'.$project_typesSys[$row['project_type']].'</td>'.
                        '<td>'.$partners.'</td>'.
                        '<td>'.$donors.'</td>'.
                        '<td>'.$sector_teaser.'</td>'.
                        '<td>'.$area_teaser.'</td>'.
                        '<td>'.$start_date.'</td>'.
                        '<td>'.$end_date.'</td>'.
                    '</tr>';
            }
        }
        echo '</tbody>
		</table>';
            echo '</div>';
        }
		?>
	</DIV>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    &nbsp;&nbsp;&nbsp;
</div>
<?php
}
?>
