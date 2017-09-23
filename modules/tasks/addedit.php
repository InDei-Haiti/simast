<?php /* TASKS $Id: addedit.php 5773 2008-07-21 15:04:07Z merlinyoda $ */
if (! (defined ( 'DP_BASE_DIR' ))) {
	die ( 'You should not access this file directly.' );
}

/*if ($_GET ['pulp'] = "jsel" && ( int ) $_GET ['par_id'] > 0) {
	//print "you posted ".$_GET['par_id']. " er.gjwekrgjwelkg";
	$q = new DBQuery ( );
	$q->addTable ( 'administrative_regions' );
	$q->addQuery ( 'region_id, region_name' );
	$q->addWhere ( 'region_parent = "' . ( int ) $_GET ['par_id'] . '"' );
	$q->addOrder ( 'region_name' );
	$admin_list = arrayMerge ( array (- 1 => 'all' ), $q->loadHashList () );
	
	$q = new DBQuery ( );
	$q->addTable ( 'administrative_regions' );
	$q->addQuery ( ' region_name' );
	$q->addWhere ( 'region_parent in (' . implode ( ",", array_keys ( $admin_list ) ) . ')' );
	//$q->addOrder('region_name');
	$admin2_list = arrayMerge ( array (- 1 => 'all' ), $q->loadHashList () );
	
	$con_id = ( int ) $_GET ['top_id'];
	
	if (sizeof ( $admin2_list ) > 1) {
		$pfur = 1;
	} else {
		$pfur = 0;
	}
	if (sizeof ( $admin_list ) == 1) {
		$sel_str = "fool";
	} elseif (count ( $admin_list ) > 1) {
		$clev = ( int ) $_GET ['clevel'];
		$tcase = $_GET ['scase'];
		if ($tcase != "" && $tcase != "act_top") {
			$ucase = $tcase . "_";
		} else {
			$ucase = "";
		}
		$clev ++;
		$sel_str = arraySelect ( $admin_list, $ucase . "b_task_admin_" . $clev, "id='" . $ucase . "level_" . $clev . "' onchange='show_sel(\"" . $tcase . "\",this,$clev)'  class='text' style='width: 230px;'", '', false );
		$sel_str = preg_replace ( "/\r/", "", $sel_str );
		$sel_str = preg_replace ( "/\n/", "", $sel_str );
	}
	
	$q = new DBQuery ( );
	$q->addTable ( 'admin_titles' );
	$q->addQuery ( 'title' );
	$q->addWhere ( "country_id='$con_id' and level='$clev'" );
	$rtitle = $q->loadResult ();
	
	print '["' . $pfur . '","' . mysql_real_escape_string ( $rtitle ) . '","' . mysql_real_escape_string ( $sel_str ) . '"]';
	return;
}*/

if($_GET['pmode'] == 'newsub'){
	$cnew=true;
}else{
	$cnew=false;
}

if (isset ( $_GET ['scope'] ) && trim ( $_GET ['scope'] ) == "subs") {
	$we_do = "sub";
} else {
	$we_do = "root";
}

$cur_subs = 0;
/*
 * Tasks :: Add/Edit Form
 */

$task_id = intval ( dPgetParam ( $_REQUEST, 'task_id', 0 ) );
$perms = & $AppUI->acl ();

$is_superAdmin = false;
$roles = $perms->getUserRoles($AppUI->user_id);
foreach ($roles as $role){
	if($role['value']=='super_admin'){
		$is_superAdmin = true;
	}
}

//load the record data
$obj = new CTask ( );
$obj0 = new CTask ( );
$q = new DBQuery ( );
$projTasks = array ();

$subs = array ();

//check if we are in a subform
if ($task_id > 0 && ! $obj->load ( $task_id )) {
	$AppUI->setMsg ( 'Activity' );
	$AppUI->setMsg ( 'invalidID', UI_MSG_ERROR, true );
	$AppUI->redirect ();
}
if ($obj->task_country > 0) {
	$obj0->task_country = $obj->task_country;
	for($n = 2; $n < 7; $n ++) {
		$tval = "task_admin" . $n;
		if ($obj->$tval > 0) {
			$obj0->$tval = $obj->$tval;
		}
	}
}
$subs [0] = $obj0;

//Now lets get non-root tasks, grouped by the task parent
$q->addTable ( 'tasks' );
$q->addQuery ( 'task_id' );
$q->addWhere ( 'task_parent="' . $obj->task_id . '" AND task_mode="sub" ' );
$q->addOrder ( "task_id" );
$sub_list = $q->loadHashList ();
$q->clear ();

$q->addTable('programs');
$q->addQuery('id,pacro');
$progs = $q->loadList();

if (! $sub_list) {
	$sub_list = array ();
	$sub_list [0] = $obj0;
}
$scnt = 1;
//$companies = $row1->getAllowedRecords( $AppUI->user_id, 'company_id,company_acronym', 'company_acronym' );

//check for a valid project parent
$task_project = intval ( $obj->task_project );
if (! $task_project) {
	$task_project = dPgetParam ( $_REQUEST, 'task_project', 0 );
	if (! $task_project) {
		$AppUI->setMsg ( 'badTaskProject', UI_MSG_ERROR );
		$AppUI->redirect ();
	}
}

//check permissions
if ($task_id) {
	//we are editing an existing task
	$canEdit = $perms->checkModuleItem ( $m, 'edit', $task_id );
} else {
	//do we have access on this project?
	$canEdit = $perms->checkModuleItem ( 'projects', 'view', $task_project );
	//And do we have add permission to tasks?
	if ($canEdit) {
		$canEdit = $perms->checkModule ( 'tasks', 'add' );
	}
}
$canDelete = $obj->canDelete($msg, $task_id);
if($is_superAdmin){
	$canEdit = true;
	$canDelete = true;
}
if (! $canEdit) {
	$AppUI->redirect ( 'm=public&a=access_denied&err=noedit' );
}

//check permissions for the associated project
$canReadProject = $perms->checkModuleItem ( 'projects', 'view', $obj->task_project );
if(!$is_superAdmin){
	if($task_id > 0){
		if(!$perms->checkForm($AppUI->user_id,'activity',$task_id,$task_id ? 'edit' : 'add')){
			$AppUI->redirect ( 'm=public&a=access_denied' );
		}
	}
}


$durnTypes = dPgetSysVal ( 'TaskDurationType' );
$sector_list = dPgetSysVal ( 'SectorType' );


$hvars = "";
$plevel = 1;

$usobj = $obj;



echo '<br /><br /><div class="card">';
//check the document access (public, participant, private)
if (! $obj->canAccess ( $AppUI->user_id )) {
	//$AppUI->redirect ( 'm=public&a=access_denied&err=noaccess' );
}

// get a list of permitted companies
require_once ($AppUI->getModuleClass ( 'companies' ));

$row = new CCompany ( );
$companies = $row->getAllowedRecords ( $AppUI->user_id, 'company_id,company_acronym', 'company_acronym' );
//$companies = arrayMerge ( array ('0' => 'none' ), $companies );

// get internal companies
$companies_internal = $row->listCompaniesByType ( array ('6' ) ); // 6 is standard value for internal companies
$companies_internal = arrayMerge ( array ('0' => 'all' ), $companies_internal );
//pull the related project
$project = new CProject ( );
$project->load ( $task_project );

//Pull all users
$users = $perms->getPermittedUsers ( 'tasks' );

//setup the title block
$ttl = (($task_id > 0) ? 'Edit Activity' : 'New Activity');
$titleBlock = new CTitleBlock ( $ttl, '', $m, "$m.$a" );


if ($canDelete) {
	//$titleBlock->addCrumbDelete('delete activity', $canDelete, $msg);
	$titleBlock->addCrumbDelete('delete activity', $canDelete, $msg);
}
$titleBlock->show ();
//Let's gather all the necessary information from the department table
//collect all the departments in the company

//ALTER TABLE `tasks` ADD `task_departments` CHAR(100) ;
$company_id = $project->project_company;

//Dynamic tasks are by default now off because of dangerous behavior if incorrectly used
if (is_null ( $obj->task_dynamic )) {
	$obj->task_dynamic = 0;
}

$can_edit_time_information = $obj->canUserEditTimeInformation ();

//Time arrays for selects
$start = intval ( dPgetConfig ( 'cal_day_start' ) );
$end = intval ( dPgetConfig ( 'cal_day_end' ) );
$inc = intval ( dPgetConfig ( 'cal_day_increment' ) );
if ($start === null)
	$start = 8;
if ($end === null)
	$end = 17;
if ($inc === null)
	$inc = 15;
$hours = array ();
for($current = $start; $current <= $end; $current ++) {
	if ($current < 10) {
		$current_key = "0" . $current;
	} else {
		$current_key = $current;
	}
	
	if (stristr ( $AppUI->getPref ( 'TIMEFORMAT' ), "%p" )) {
		//User time format in 12hr
		$hours [$current_key] = ($current > 12 ? $current - 12 : $current);
	} else {
		//User time format in 24hr
		$hours [$current_key] = $current;
	}
}

$minutes = array ();
$minutes ["00"] = "00";
for($current = 0 + $inc; $current < 60; $current += $inc) {
	$minutes [$current] = $current;
}



// format dates
$df = $AppUI->getPref ( 'SHDATEFORMAT' );

if (intval ( $obj->task_start_date )){
	$start_date = new CDate ( $obj->task_start_date );
}else if ($task_id != 0){
	$start_date = null;
}else{
		$start_date = new CDate ( );
}
//$start_date = intval( $obj->task_start_date ) ? new CDate( $obj->task_start_date ) : new CDate();
$end_date = intval ( $obj->task_end_date ) ? new CDate ( $obj->task_end_date ) : null;

// convert the numeric calendar_working_days config array value to a human readable output format
$cwd = explode ( ',', $dPconfig ['cal_working_days'] );

//$cwd_conv = array_map ( 'cal_work_day_conv', $cwd );
//$cwd_hr = implode ( ', ', $cwd_conv );


/*$q->addQuery ( 'project_id, project_name' );
$q->addTable ( 'projects' );
$q->addWhere ( 'project_company = ' . $company_id );
$q->addWhere ( '(project_status <> 7 OR project_id <> ' . $task_project . ')' );
$q->addOrder ( 'project_name' );
$project->setAllowedSQL ( $AppUI->user_id, $q );
$projects = $q->loadHashList ();*/
$status = arrayMerge(array("-1"=> "-- select status --"),dPgetSysVal( 'ProjectStatus' ) );
?>

<form name="frmDelete" action="./index.php?m=tasks" method="post">
	<input type="hidden" name="dosql" value="do_task_aed">
	<input type="hidden" name="del" value="1" />
	<input type="hidden" name="task_id" value="<?php echo $task_id;?>" />
</form>
<form name="editFrm" action="?m=tasks&project_id=<?php 	echo $task_project; ?>" method="post" id="fPA">
<?php require_once(DP_BASE_DIR.'/modules/public/activity_table_tpl.php');?>
<div id='bnew' style='width: 100%;'>
<div float='left' widht="30%"><br/>
	<input class="button ce pi ahr" type="button" name="btnFuseAction"
						value="<?php
						echo $AppUI->_ ( 'save' );
						?>"
						onClick="postDataPA();" />
	<input class="button ce pi ahr" type="button" name="cancel"
					value="<?php
					echo $AppUI->_ ( 'cancel' );
					?>"
					onClick="if(confirm('<?php
					echo $AppUI->_ ( 'taskCancel', UI_OUTPUT_JS );
					?>')){location.href = '?<?php
					echo $AppUI->getPlace ();
					?>';}" />
	
	
</div>
<div>
		* <?php
		echo $AppUI->_ ( 'requiredField' );
		?>
</div>
</div>
</form>

<script type="text/javascript">


var ab_word ='activity';
<?php if ($canEdit) {?>
function delIt() {
	if (confirm("<?php echo $AppUI->_('doDelete', UI_OUTPUT_JS).' '.$AppUI->_('Activity', UI_OUTPUT_JS).'?';?>")) {
		document.frmDelete.submit();
	}
}
<?php }?>
</script>
<!--<script type="text/javascript" src="/modules/public/pa_edit.js"></script>-->
<?php taskAddon(); ?>
</div>


<?php if($m === "tasks"){?>
	<script>
//		alert("Jes suis Alexis");
// Get the modal
var modal = document.getElementById('myModal');

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal
btn.onclick = function() {
	modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
	modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
	if (event.target == modal) {
		modal.style.display = "none";
	}
}
	</script>
<?php }?>
