<?php /* CONTACTS $Id: view.php,v 1.14.4.3 2005/11/26 02:11:35 cyberhorse Exp $ */
$activity_id = intval( dPgetParam( $_GET, 'activity_id', 0 ) );
$page = intval( dPgetParam( $_GET, 'page', 1));
$limit = intval($dPconfig['max_limit']);
$AppUI->savePlace();

// check permissions for this record
//$canEdit = !getDenyEdit( $m, $activity_id );
//if (!$canEdit) {
//	$AppUI->redirect( "m=public&a=access_denied" );
//}
$positionOptions = dPgetSysVal('PositionOptions');
// load the record data
$msg = '';
$row = new CActivity();

$canDelete = $row->canDelete( $msg, $activity_id );

// Don't allow to delete contacts, that have a user associated to them.
if (isset ( $_GET ['tab'] )) {
	$AppUI->setState ( 'ClientVwTab', $_GET ['tab'] );
}

$tab = $AppUI->getState ( 'ClientVwTab' ) !== NULL ? $AppUI->getState ( 'ClientVwTab' ) : 0;

$q  = new DBQuery;
$q->addTable('users');
$q->addQuery('user_id');
$q->addWhere('user_contact = ' . $activity_id);
$sql = $q->prepare();
$q->clear();
$tmp_user = db_loadResult($sql);
if (!empty($tmp_user))
	$canDelete = false; 

$canEdit = true;//$perms->checkModuleItem($m, "edit", $activity_id);

if (!$row->load( $activity_id ) && $activity_id > 0) {
	$AppUI->setMsg( 'Staff' );
	$AppUI->setMsg( "invalidID", UI_MSG_ERROR, true );
	$AppUI->redirect();
} else if ($row->contact_private && $row->contact_owner != $AppUI->user_id
	&& $row->contact_owner && $activity_id != 0) {
// check only owner can edit
	$AppUI->redirect( "m=public&a=access_denied" );
}

// Get the contact details for company and department
$company_detail = $row->getCompanyDetails();
$dept_detail = $row->getDepartmentDetails();

$constat=dPgetSysVal('ContactStatus');
$q = new DBQuery();

if(@$row->activity_administration_section){
	$q->addTable ('administration_section', 'adm_sec' );
	$q->addQuery ( 'adm_sec.administration_section_code_com' );
	//cho @$row->activity_administration_section;
	$q->addWhere('administration_section_code in ('.@$row->activity_administration_section.')');
	$activity_administration_com = array_unique($q->loadColumns());
	
	$q = new DBQuery();
	$q->addTable ('administration_section', 'adm_sec' );
	$q->addQuery ( 'adm_sec.administration_section_name');
	$q->addWhere('adm_sec.administration_section_code_com in ('.implode(',', $activity_administration_com).')');
	$zone = $q->loadColumn();
}

if(@$row->activity_strategies){
	$q->addTable ('st_area', 'st' );
	//$q->addQuery ( 'st.title','st.prex' );
	//cho @$row->activity_administration_section;
	$q->addWhere('st.id in ('.@$row->activity_strategies.')');
	$q->addOrder('st.prex');
	$strategies = $q->loadList();
}

//for($i=0;$i<count();$i++){
	
//}
if($row->activity_type_of_intervention)
	$row->activity_type_of_intervention = explode ( ',', $row->activity_type_of_intervention );
if($row->activity_type_of_beneficiery)
	$row->activity_type_of_beneficiery = explode ( ',', $row->activity_type_of_beneficiery );

// setup the title block
$ttl = "View Activity";
$titleBlock = new CTitleBlock( $ttl, '', $m, "$m.$a" );
$titleBlock->addCrumb( "?m=activity", "Activity" );
if ($canEdit && $activity_id)
        $titleBlock->addCrumb( "?m=activity&a=addedit&activity_id=$activity_id", 'Edit' );
	/*$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_('new project').'" />', '',
		'<form action="?m=projects&a=addedit&company_id='.$row->contact_company.'&activity_id='.$activity_id.'" method="post">', '</form>'
	);*/
if ($canDelete && $activity_id) {
	$titleBlock->addCrumbDelete( 'delete activity', $canDelete, $msg );
}
$titleBlock->show();
?>
<form name="changecontact" action="?m=activity" method="post">
        <input type="hidden" name="dosql" value="do_activity_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="activity_id" value="<?php echo $activity_id;?>" />
        <input type="hidden" name="contact_owner" value="<?php echo $row->contact_owner ? $row->contact_owner : $AppUI->user_id;?>" />
</form>
<script language="JavaScript">
function delIt(){
        var form = document.changecontact;
        if(confirm( "<?php echo $AppUI->_('activityDelete', UI_OUTPUT_JS);?>" )) {
                form.del.value = "<?php echo $activity_id;?>";
                form.submit();
        }
}
</script>

<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">
<tr>
	<td colspan="2">
		<table border="0" cellpadding="1" cellspacing="1">
		<tr>
			<td align="right"><?php echo $AppUI->_('First Name');?>:</td>
			<td class="hilite"><?php echo @$row->activity_pers_first_name;?></td>
		</tr>
		<tr>
			<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Last Name');?>:</td>
			<td class="hilite"><?php echo @$row->activity_pers_last_name;?></td>
		</tr>
		<tr>
			<td align="right" width="100"><?php echo $AppUI->_('Phone');?>: </td>
			<td class="hilite"><?php echo @$row->activity_pers_telephon;?></td>
		</tr>
		<tr>
			<td align="right" width="100"><?php echo $AppUI->_('Email');?>:</td>
			<td class="hilite">
				<?php echo @$row->activity_pers_email;?>
			</td>
		</tr>
		<tr>
			<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Activity Name');?>:</td>
			<td class="hilite">
				<?php echo @$row->activity_name;?>
			</td>
		</tr>
		<tr>
			<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Domain of activity');?>:</td>
			<td class="hilite">
				<?php 
				$ActivityDomaine = dPgetSysVal('ActivityDomaine'); 
				if(@$row->activity_domaine) 
					echo $ActivityDomaine[@$row->activity_domaine];
				
				?>
			</td>
		</tr>
		<tr>
			<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Strategies');?>:</td>
			<td class="hilite">
				<?php if($strategies){
					echo '<table>';
					for($i=0;$i<count($strategies);$i++)
						echo '<tr><td>'.$strategies[$i]['prex'].'.</td><td> '.$strategies[$i]['title'].'</td></tr>';
					echo '</table>';
					
				}?>
			</td>
		</tr>
		<tr><td>&nbsp;&nbsp;</td><td>&nbsp;&nbsp;</td></tr>
		<tr>
			<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Type of intervention');?></td>
			<td class="hilite">
				<?php 
					if(@$row->activity_type_of_intervention || @$row->activity_type_of_intervention=='0'){
						echo '<ul style="list-style:none;">';
						for($i=0;$i<count(@$row->activity_type_of_intervention);$i++){
							$TypeOfIntervention = dPgetSysVal('TypeOfIntervention');
							echo '<li>'.($i+1).'.- '.$TypeOfIntervention[@$row->activity_type_of_intervention[$i]].'</li>';
						}
							echo '</ul>';
					}
				?>
			</td>
		</tr>
		<tr>
			<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Start date of activity');?>:</td>
			<td class="hilite">
				<?php echo @$row->activity_start_date;?>
			</td>
		</tr>
		<tr>
			<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('End date of activity');?>:</td>
			<td class="hilite">
				<?php echo @$row->activity_end_date;?>
			</td>
		</tr>
		<tr><td>&nbsp;&nbsp;</td><td>&nbsp;&nbsp;</td></tr>
		<tr>
			<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Type of beneficiary');?>:</td>
			<td class="hilite">
				<?php
				    if(@$row->activity_type_of_beneficiery || @$row->activity_type_of_beneficiery=='0'){ 
						echo '<ul style="list-style:none;">';
						for($i=0;$i<count(@$row->activity_type_of_beneficiery);$i++){
							$TypeOfBeneficiery = dPgetSysVal('TypeOfBeneficiery');
							echo '<li>'.($i+1).'.- '.$TypeOfBeneficiery[@$row->activity_type_of_beneficiery[$i]].'</li>';
						}
							echo '</ul>';
				    }
					?>
			</td>
		</tr>
		<tr>
			<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Number of beneficiaries');?>:</td>
			<td class="hilite">
				<?php echo @$row->activity_number_of_beneficiery;?>
			</td>
		</tr>
		<tr>
			<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Area of intervention');?>:</td>
			<td class="hilite">
				<?php if($zone){
					echo '<ul style="list-style:none;">';
					for($i=0;$i<count($zone);$i++)
						echo '<li>'.($i+1).'.- '.$zone[$i].'</li>';
								echo '</ul>';
					
				}?>
			</td>
		</tr>
		</table>
	</td>
</tr>


<tr>
	<td>
		<input type="button" value="<?php echo $AppUI->_('back');?>" class="button" onClick="javascript:window.location='./index.php?m=activity';" />
	</td>
</tr>
</form>
</table>
<?php
//include customer roles
/* $tabBox = new CTabBox ("?m=contacts", dPgetConfig('root_dir')."/modules/activity/");
$tabBox->add('vw_clients', 'Beneficieries' );
$tabBox->show(); */

$tabBox = new CTabBox ("?m=activity&a=view&activity_id=$activity_id", dPgetConfig('root_dir')."/modules/activity/",$tab);
$types[0] = "All Beneficieries";
for($i=0;$i<count(@$row->activity_type_of_beneficiery);$i++){
	$TypeOfBeneficiery = dPgetSysVal('TypeOfBeneficiery');
	$types[$i+1]=$TypeOfBeneficiery[@$row->activity_type_of_beneficiery[$i]];
}
$type_filter = array ();
foreach ( $types as $type => $type_name ) {
	$type_filter [] = $type;
	/*if (strncmp ( $type_name, "Not Applicable", strlen ( "Not Applicable" ) ) == 0)
		$type = NULL;*/
	if (strncmp ( $type_name, "Not Active", strlen ( "Not Active" ) ) == 0)
		$type = 99;
	/*if (strncmp ( $type_name, "LTP", strlen ( "LTP" ) ) == 0)
		$type = 98;*/

	$tabBox->add ( "vw_clients", $type_name /*. " (" . $obj->getCount ( $AppUI->user_type, $AppUI->user_id, $type ) . ")"*/, false, $type );

}
$tabBox->show ();


?>