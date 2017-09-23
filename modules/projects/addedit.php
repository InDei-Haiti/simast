<?php /* PROJECTS $Id: addedit.php 5681 2008-04-18 23:45:55Z merlinyoda $ */
if (!defined('DP_BASE_DIR')){
    die('You should not access this file directly.');
}

if($_GET['mode'] == 'getcomps'){
    $progs = trim($_POST['progs']);
    if($progs != ''){
        $sql = 'SELECT id, prog_id, ctitle
				FROM program_comps
				WHERE prog_id
				IN ( %s)
				order by prog_id asc ';
        $list = array();
        $res = mysql_query(sprintf($sql,mysql_real_escape_string($progs)));
        if($res && mysql_num_rows($res) > 0){
            while($prow = mysql_fetch_assoc($res)){
                $list[]=$prow;
            }
        }
        echo json_encode($list);

    }
    return;
}

$project_id = intval( dPgetParam( $_GET, "project_id", 0 ) );
$company_id = intval( dPgetParam( $_GET, "company_id", 0 ) );
$company_internal_id = intval( dPgetParam( $_GET, "company_internal_id", 0 ) );
$contact_id = intval( dPgetParam( $_GET, "contact_id", 0 ) );

$perms =& $AppUI->acl();
// check permissions for this record

$is_superAdmin = false;
$roles = $perms->getUserRoles($AppUI->user_id);
foreach ($roles as $role){
    if($role['value']=='super_admin'){
        $is_superAdmin = true;
    }
}

$canEdit = $perms->checkModuleItem( $m, 'edit', $project_id );
$canAuthor = $perms->checkModuleItem( $m, 'add' );

$row = new CProject();

$scan_edit= $row->localCheck($AppUI->user_id,$project_id);

$adm_rights = $AppUI->isAdmin();

if($canEdit && ( $scan_edit || $adm_rights)){
    $canEdit=true;
}else{
    $canEdit=false;
}
if($is_superAdmin){
    $canEdit = true;
}
if ((!$canEdit && $project_id > 0) || (!$canAuthor && $project_id == 0)) {
    $AppUI->redirect( "m=public&a=access_denied" );
}
if(!$is_superAdmin){
    if( $project_id > 0){
        if(!$perms->checkForm($AppUI->user_id,'projects',$project_id,'edit')){
            $AppUI->redirect ( 'm=public&a=access_denied' );
        }
    }
}

// get a list of permitted companies
require_once( $AppUI->getModuleClass ('companies' ) );

$row1 = new CCompany();
//$companies = $row1->getAllowedRecords( $AppUI->user_id, 'company_id,company_acronym', 'company_acronym' );
$q  = new DBQuery;
$q->addTable ( 'companies' );
$q->addQuery ( 'company_id,company_acronym' );
//$q->addWhere ( 'company_id in ('.$obj->project_cdonors.')' );
$companies = $q->loadHashList();
//$companies = implode(",", $companies);
// load the record data

if (!$row->load( $project_id, false ) && $project_id > 0) {
    $AppUI->setMsg( 'Project' );
    $AppUI->setMsg( "invalidID", UI_MSG_ERROR, true );
    $AppUI->redirect();
} else if (count( $companies ) < 2 && $project_id == 0) {
    $AppUI->setMsg( "noCompanies", UI_MSG_ERROR, true );
    $AppUI->redirect();
}

if ($project_id == 0 && $company_id > 0) {
    $row->project_company = $company_id;
}


// pull users
if ($row->project_owner > 0) {
    $q = new DBQuery ( );
    $q->addTable ( 'users', 'u' );
    $q->addTable ( 'contacts', 'con' );
    //$q->addQuery ( 'user_id' );
    $q->addQuery ( 'CONCAT_WS(" ",contact_first_name,contact_last_name)' );
    $q->addOrder ( 'contact_last_name' );
    $q->addWhere ( 'u.user_contact = con.contact_id' );
    $q->addWhere("user_id='".$row->project_owner."'");
    //$users = $q->loadHashList ();
    $users = $q->loadResult();
}
// add in the existing company if for some reason it is dis-allowed
if ($project_id && !array_key_exists( $row->project_company, $companies )) {
    $q  = new DBQuery;
    $q->addTable('companies');
    $q->addQuery('company_name');
    $q->addWhere('companies.company_id = '.$row->project_company);
    $sql = $q->prepare();
    $q->clear();
    $companies[$row->project_company] = db_loadResult($sql);
}

// get critical tasks (criteria: task_end_date)
$criticalTasks = ($project_id > 0) ? $row->getCriticalTasks() : NULL;

// get ProjectPriority from sysvals
$projectPriority = dPgetSysVal( 'ProjectPriority' );

// format dates
$df = $AppUI->getPref('SHDATEFORMAT');
echo '<br /><br /><div class="card">';

$thisYear = date("Y",time());
$annuals = range(2000, 2030); //range($thisYear,$thisYear+15);
$sel_arr= array();
for($i=0,$l = count($annuals);$i < $l; $i++){
    $sel_arr[]="<option value='".$annuals[$i]."'>".$annuals[$i].'</option>';
}
$bud_annual = '<select class="text yr">'.join("",$sel_arr).'</select>';

//$start_date = new CDate( $row->project_start_date );
$start_date = intval( $row->project_start_date ) ? new CDate( $row->project_start_date ) : null;
$end_date = intval( $row->project_end_date ) ? new CDate( $row->project_end_date ) : null;
$actual_end_date = intval( $criticalTasks[0]['task_end_date'] ) ? new CDate( $criticalTasks[0]['task_end_date'] ) : null;
$style = (( $actual_end_date > $end_date) && !empty($end_date)) ? 'style="color:red; font-weight:bold"' : '';

// setup the title block
$ttl = $project_id > 0 ? "Edit Project" : "New Project";
$titleBlock = new CTitleBlock( $ttl, '', $m, "$m.$a" );
$titleBlock->addCrumb( "?m=projects", "List projects" );
if ($project_id != 0)
    $titleBlock->addCrumb( "?m=projects&a=view&project_id=$project_id", "view this project" );
if ($canDelete) {
    $titleBlock->addCrumbDelete ( 'delete project', $canDelete, $msg );
}
$titleBlock->show();
?>



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

<form name="frmDelete" action="./index.php?m=projects" method="post"><input
            type="hidden" name="dosql" value="do_project_aed" /> <input
            type="hidden" name="del" value="1" /> <input type="hidden"
                                                         name="project_id" value="<?php
    echo $project_id;
    ?>" />
</form>
<?php
//Build display list for departments
$company_id = $row->project_company;

// Get contacts list
$selected_contacts = array();
if ($project_id) {
    $q =& new DBQuery;
    $q->addTable('project_contacts');
    $q->addQuery('contact_id');
    $q->addWhere('project_id = ' . $project_id);
    $res =& $q->exec();
    for ( $res; ! $res->EOF; $res->MoveNext())
        $selected_contacts[] = $res->fields['contact_id'];
    $q->clear();
}
if ($project_id == 0 && $contact_id > 0){
    $selected_contacts[] = "$contact_id";
}
$aFile_Type = dPgetSysVal('FileType');
$blank = array();
?>

<script type="text/javascript">
    var calendarField = '',calWin = null,ab_word='project';
</script>
<!--<script type="text/javascript" src="/modules/public/pa_edit.js"></script>-->
<form name="upq-back" action="/?m=projects&suppressHeaders=1&mode=uploadf" enctype="multipart/form-data" method="POST" onsubmit="return AIM.submit(this, {onStart : startCallback, onComplete : acceptPrFile})">
    <input type="file" name="file" id="fultra" style="visibility:hidden;position:absolute;top:0;left:0">
    <input type="hidden" name="fdata" value="">
    <input type="submit" style="display: none;" id='push_file'>
</form>
<table cellspacing="0" cellpadding="4" border="0" width="100%"
       class="std">
    <form id="fPA" action="./index.php?m=projects" enctype="multipart/form-data" method="post" name="big-front">
        <input type="hidden" name="dosql" value="do_project_aed" />
        <input type="hidden" name="project_id" value="<?php echo $project_id;?>" />
        <input 	type="hidden" name="project_creator" value="<?php echo $AppUI->user_id;?>" />
        <input 	type="hidden" name="project_file" value="0" />
        <tr>
            <td width="50%" valign="top">
                <table cellspacing="0" cellpadding="2" border="0">
                    <tr>
                        <td align="left" nowrap="nowrap"><?php echo $AppUI->_('Project Name');?>&nbsp;*</td>
                        <td width="100%" colspan="2">
                            <input type="text" name="project_name" value="<?php echo dPformSafe( $row->project_name );?>" size="25" maxlength="50" class="text mandat form-control" style="width: 230px;" />
                        </td>
                    </tr>
                    <tr>
                        <td align="left" nowrap="nowrap"><?php echo $AppUI->_('Project Type');?></td>
                        <td colspan="2" width="100%">
                            <?php echo "<br />".arraySelect( arrayMerge(array('-1'=>'- Select Project Type -'),dPgetSysVal('ProjectType')), 'project_type', 'size="1" class="text form-control" style="width:400px"', $row->project_type ? $row->project_type : -1, true )."<br />";?>
                        </td>
                    </tr>
                    <tr>
                        <td align="left" nowrap="nowrap"><?php echo $AppUI->_('Partners');?></td>
                        <td width="100%" nowrap="nowrap" colspan="2">
                            <?php
                            echo "<br />".arraySelect( $companies, 'project_partners[]', 'id="pdonor" multiple="multiple" style="width: 230px;" class="text chosen form-control" size="2" ', @explode(",",$row->project_donor ))."<br />";
                            ?>
                            <script type="text/javascript">
                                var select = document.getElementById("pdonor");
                                var val="";

                                <?php if($row->project_partners){?>
                                val = "<?php echo $row->project_partners?>";
                                val = val.split(',');
                                <?php }?>
                                if(val){
                                    for(var i=0;i<select.length;i++){
                                        var value = select.options[i].value;
                                        for(var j=0;j<val.length;j++){
                                            if(value==val[j]){
                                                select.options[i].selected = true;
                                                break;
                                            }
                                        }
                                    }
                                }
                            </script>
                        </td>
                    </tr>

                    <tr>
                        <td align="left" nowrap="nowrap"><?php echo $AppUI->_('Donors');?></td>
                        <td width="100%" nowrap="nowrap" colspan="2">
                            <?php
                            echo "<br />".arraySelect( $companies, 'project_cdonors[]', 'id="project_cdonors" style="width: 230px;" class="text chosen form-control" size="1" multiple="multiple"', $row->project_cdonors ? $row->project_cdonors : '-1')."<br />";
                            ?>
                            <script type="text/javascript">
                                var select = document.getElementById("project_cdonors");
                                var val="";

                                <?php if($row->project_cdonors){?>
                                val = "<?php echo $row->project_cdonors?>";
                                val = val.split(',');
                                <?php }?>
                                if(val){
                                    for(var i=0;i<select.length;i++){
                                        var value = select.options[i].value;
                                        for(var j=0;j<val.length;j++){
                                            if(value==val[j]){
                                                select.options[i].selected = true;
                                                break;
                                            }
                                        }
                                    }
                                }
                            </script>
                        </td>
                    </tr>
                    <tr>
                        <td align="left" nowrap="nowrap"><?php echo $AppUI->_('Status');?>&nbsp;*</td>
                        <td align="left">
                            <?php /*krsort($pstatus);
						if($row->project_id > 0 || $project_id > 0){
							$ust=$row->project_status;
						}else{
							$ust=3;
						}*/
                            $status = arrayMerge(array("-1"=> "-- select status --"),dPgetSysVal( 'ProjectStatus' ) );
                            echo arraySelect( $status, 'project_status', 'size="1" class="text mandat form-control" style="width: 230px;"', @$row->project_status, true ); ?>
                        </td>

                    </tr>


                </table>
            </td>
            <td width="50%" valign="top">
                <table cellspacing="0" cellpadding="2" border="0" width="100%">

                    <tr>
                        <td align="left" nowrap="nowrap" colspan="2">
                            <div class="pretty_td"><?php echo $AppUI->_('Start Date');?>&nbsp;*</div>
                            <input type="text" class="text dfield mandat form-control" readonly="readonly" style="width: 290px;" name="project_start_date" value="<?php echo $start_date ? $start_date->format( $df ) : '';?>" />
                            <?php echo $AppUI->_('End Date');?>&nbsp;*&nbsp;
                            <input type="text" class="text dfield mandat form-control" readonly="readonly" style="width: 290px;" name="project_end_date" value="<?php echo $end_date ? $end_date->format( $df ) : '';?>" />
                        </td>
                    </tr>
                    <tr>
                        <td align="left" nowrap="nowrap" colspan="2"><br />
                            <div class="pretty_td"><?php echo $AppUI->_('Budget');?> <?php echo $dPconfig['currency_symbol'] ?>&nbsp;*</div>
                            <input type="Text" name="project_target_budget" value="<?php echo @$row->project_target_budget;?>" class="text mandat form-control" style="width: 290px;" /><br />
                            &nbsp;<?php echo $AppUI->_('Yearly');?>&nbsp;&nbsp;
                            <input type="button" class="text button ce pi ahr" onclick="annualBudget(this)" value="Edit"/>
                            <input type="hidden" name='project_annual_budget' value="<?php echo $row->project_annual_budget;?>">
                        </td>
                    </tr>
                    <tr>
                        <td align="left" nowrap="nowrap" style="width:105px;"><?php echo $AppUI->_('Beneficiaries');?></td>
                        <td colspan="2"><input type="text" name="project_actual_budget" value="<?php echo @$row->project_actual_budget;?>" size="10" maxlength="10" class="text form-control" style="width: 310px;" /></td><br /><br />
                    </tr>
                    <tr>
                        <td nowrap="nowrap" style="width:105px;"><?php echo $AppUI->_('Upload File');?>:</td>
                        <td align="left">
                            <div id="importbox" class="myimporter" style="float: left;">
                                <input type="text" class="text" title='File Name' name="file_nick" style="width: 110px;">
                                <?php echo arraySelect($aFile_Type,'file_type',' class="text" ',6);?>
                                <input type="button" class="text" value="Browse" onclick="$('#fultra').trigger('click');">
                                <div class="file_dock"></div>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="4">
                            <div class="pretty_td"><?php echo $AppUI->_('Description').'(1000 '.$AppUI->_('Chars max').')';?></div>
                            <textarea name="project_description"  maxlength="1000" cols="54" rows="3" wrap="virtual" class="textarea form-control" style="width:400px"><?php echo dPformSafe( @$row->project_description );?></textarea>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

</table>
* <?php echo $AppUI->_('requiredField');?>
<input type="hidden" name="pro_activ" id="act_data">
</form>
<script type="text/javascript">
    window.onload = up;
    function up (){
        $(".multiple").multiselect({
            click : function(event, ui){
                if($(this).attr("id") == 'pdonor'){
                    updComps([]);
                }
            }
        });
        updComps([<?php echo $row->project_comps ?>]);
    }
</script>
<?php
if($project_id == 0){
    //we have case for initial page build
    echo '<br><br><span class="flink" style="font-size: 10pt;margin-top: 5px;" onclick="addAct();" title="'.$AppUI->_('Add new activity').'">'.$AppUI->_('Add activity').'</span><br>
		<ul id="act_box"><li>';
    require_once (DP_BASE_DIR.'/modules/public/activity_table_tpl.php');
    echo '</li></ul>';
    taskAddon();
}else{
    ?>
    <div id="year_shelter" style="display: none;">
        <?php echo $bud_annual;?>
    </div>
    <div id="bub" style="display: none;"></div>
    <?php
}
?>
<tr>
    <td><input class="button ce pi ahr" type="button"
               name="btnFuseAction" value="<?php echo $AppUI->_('submit');?>"
               onClick="postDataPA(this);" />
    </td>
    <td align="right">
        <input class="button ce pi ahr" type="button" name="cancel" value="<?php echo $AppUI->_('cancel');?>"
               onClick="if(confirm('Are you sure you want to cancel.')){location.href = './index.php?m=projects';}" />
    </td>
</tr>
</div>