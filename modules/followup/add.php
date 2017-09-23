<?php
require_once($AppUI->getModuleClass("followup"));

global $issues;

// collect all the users for the staff list
$q  = new DBQuery;
$q->addTable('contacts','con');
$q->leftJoin('users','u', 'u.user_contact = con.contact_id');
$q->addQuery('contact_id');
$q->addQuery('CONCAT_WS(", ",contact_last_name,contact_first_name)');
$q->addOrder('contact_last_name');
$q->addWhere('contact_active="1"');
$owners = $q->loadHashList();


//load clinics
$q  = new DBQuery;
$q->addTable('clinics','c');
$q->addQuery('clinic_id');
$q->addQuery('clinic_name');
$q->addOrder('clinic_name');
//$clinics = $q->loadHashList();
$clinics = arrayMerge(array(-1=> '-Select Clinic -'),$q->loadHashList());

$childIssues = dPgetSysVal('FollowChildIssues');
$parentIssues = dPgetSysVal('FollowParentIssues');
$today = date('d/m/Y');

$clientTypes = arrayMerge (array('-1'=>'-- Select --'), dPgetSysVal('FollowClientType'));
$visitTypes = arrayMerge(array(-1=> '-- Select --'),  dPgetSysVal('FollowVisitType'));
$serviceTypes = dPgetSysVal('FollowServices');

$titleBlock = new CTitleBlock( 'Counselor Daily Follow-Up Register', '', "$m.$a" );
$titleBlock->show();

$moduleScripts[]='/modules/followup/edit.js';
$moduleScripts[]='/modules/public/form_edit.js';

prepareIssue();
$ilister = new lister('issue','followup',$issues,'issues');
$slister = new lister('service','followup',$serviceTypes,'services');
$vModes=array('follow up','visit');
?>
<form method="post" action="?m=followup" id="filid">
<input type="hidden" name="dosql" value="do_newfollowup_aed" />
<input type="hidden" name="jfk" id="jfk">
<table id="qtab" class="csclear">
<tr>
	<td>Center</td>
	<td><?php echo arraySelect($clinics,'clinic_id','id="clinic_id"',"");?>
	</td>
	<td>Counsellor</td>
	<td><?php echo arraySelect($owners,'officer_id','id="officer_id"',"");?>
	</td>
	<td>Date</td>
	<td><?php echo drawDateCalendar('follow_date',$today);?>
	</td>
</tr>
</table>
<br>
<br>
<table id="opf" class="tbl atbl" border=0 cellspacing="1" cellpadding="2">
<thead>
<tr>
	<th>Adm. #</th>
	<th>Name  (First, Family, Other)</th>
	<th>Client Type</th>
	<th>Visit Type</th>
	<th>Issues</th>
	<th>Services</th>
	<th>&nbsp;</th>
</tr>
</thead>
<tbody>
<tr data-row="0">
	<td><input type="text" name="fentry[0][adm_no]" class="adm_field text">&nbsp;
	<div class="bsubmit" style="float:left;" onclick="editor.postName(this);" title="Retrieve name"></div>
	<input type="hidden" name="fentry[0][client_id]" class="clid">
	</td>	
	<td class="client_name">&nbsp;</td>
	<td>
		<?php echo arraySelect($clientTypes,'fentry[0][client_type]','','');?>
	</td>
	<td>
		<?php echo arraySelect($visitTypes,'fentry[0][visit_type]','','')?>
	</td>
	<td class="issue_block ltrigger">&nbsp;
		<input type="button" class="text" onclick="editor.show(this);" value="Edit">
	</td>
	<td class="service_block ltrigger">&nbsp;
		<input type="button" class="text" onclick="editor.show(this);" value="Edit">
	</td>
	<td >
		<div style="float: left;">
			<div class="add_row" title="Add entry" onclick="editor.counter(this);"></div>
			<div class="del_row" title="Delete entry" onclick="editor.counter(this);"></div>
		</div>
	</td>
</tr>
</tbody>
</table>
<input type="button" value="Submit" onclick="editor.forSend();">
</form>
<div id="ilist" style="display:none;"></div>
<div id="iinfo" style="display:none;"></div>
<script>
var vlist={},intlist={},dsplit=false;
intlist['service'] = <?php echo json_encode($serviceTypes);?>;
intlist['care'] = false;
intlist['issue'] = <?php echo json_encode($issues);?>;
vlist['issue'] = '<?php echo $ilister->build(true);?>';
vlist['service'] = '<?php echo $slister->build(true);?>';
	

function ddd1(){
	initEdits();
}
window.onload=ddd1;
</script>
