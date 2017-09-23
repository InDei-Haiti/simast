<?php
/* CONTACTS $Id: addedit.php,v 1.35.2.1 2005/11/21 04:37:54 pedroix Exp $ */
$activity_id = intval ( dPgetParam ( $_GET, 'activity_id', 0 ) );
$client_id = intval ( dPgetParam ( $_REQUEST, 'client_id', 0 ) );
$client_name = dPgetParam ( $_REQUEST, 'client_name', null );

// check permissions for this record
if (! ($canEdit = $perms->checkModuleItem ( 'activity', 'edit', $activity_id ))) {
	$AppUI->redirect ( "m=public&a=access_denied" );
}

// load the record data
$msg = '';
$row = new CActivity ();
$canDelete = $row->canDelete ( $msg, $activity_id );
$ver = true;
if (! $row->load ( $activity_id ) && $activity_id > 0) {
	$ver = false;
	$AppUI->setMsg ( 'Activity' );
	$AppUI->setMsg ( "invalidID", UI_MSG_ERROR, true );
	$AppUI->redirect ();
} else if ($row->contact_private && $row->contact_owner != $AppUI->user_id && $row->contact_owner && $activity_id != 0) {
	// check only owner can edit
	$AppUI->redirect ( "m=public&a=access_denied" );
}

if ($ver) {
	if (@$row->activity_administration_section) {
		$q->addTable ( 'administration_section', 'adm_sec' );
		$q->addQuery ( 'adm_sec.administration_section_code_com' );
		// cho @$row->activity_administration_section;
		$q->addWhere ( 'administration_section_code in (' . @$row->activity_administration_section . ')' );
		$activity_administration_com = implode ( ',', array_unique ( $q->loadColumns () ) );
	}
}
$positionOptions = dPgetSysVal ( 'PositionOptions' );
$positionOptions = arrayMerge ( array (
		0 => 'Select Position' 
), $positionOptions );
// setup the title block
$ttl = $activity_id > 0 ? "Edit Activity" : "Add Activity";
$titleBlock = new CTitleBlock ( $ttl, '', $m, "$m.$a" );
$titleBlock->addCrumb ( "?m=activity", "Activities" );
if ($canEdit && $activity_id) {
	$titleBlock->addCrumbDelete ( 'delete activity', $canDelete, $msg );
}
$titleBlock->show ();
$client_detail = $row->getClientDetails ();
$q = new DBQuery ();
$q->addTable ( 'administration_com', 'adm_com' );
$q->addQuery ( 'adm_com.administration_com_code, adm_com.administration_com_name, adm_com.administration_com_code_arr, adm_com.administration_com_code_dep' );
$q->addOrder ( 'adm_com.administration_com_name' );
$administration_com1 = $q->loadHashList ();
$administration_com = arrayMerge ( array (
		0 => '' 
), $administration_com1 );

$administration_section = array ();
foreach ( $administration_com1 as $k => $v ) {
	$q = new DBQuery ();
	$q->addTable ( 'administration_section', 'adm_sec' );
	$q->addQuery ( 'adm_sec.administration_section_code, adm_sec.administration_section_name, adm_sec.administration_section_code_com, adm_sec.administration_section_code_arr, adm_sec.administration_section_code_dep' );
	$q->addWhere ( 'adm_sec.administration_section_code_com=' . $k );
	$administration_section [$k] = $q->loadHashList ();
}

if (empty ( $client_detail )) {
	$client_detail = $row->getClientDetail ( $client_id );
}

if ($activity_id == 0 && $client_id > 0) {
	$client_detail ['client_id'] = $client_id;
	$client_detail ['client_name'] = $client_name;
	echo $client_name;
}
?>

<script language="javascript">
var jsonsec = <?php echo json_encode($administration_section)?>;
<?php

echo "window.client_id=" . dPgetParam ( $client_detail, 'client_id', 0 ) . ";\n";
echo "window.client_value='" . addslashes ( dPgetParam ( $client_detail, 'client_name', "" ) ) . "';\n";

?>





function submitIt() {
	var form = document.changecontact;
	if (form.activity_pers_last_name.value.length < 1) {
		alert( "<?php echo $AppUI->_('activitysValidName', UI_OUTPUT_JS);?>" );
		form.activity_pers_last_name.focus();
	} 
	/*
	else if (form.contact_order_by.value.length < 1) {
		alert( "<?php //echo $AppUI->_('contactsOrderBy', UI_OUTPUT_JS);?>" );
		form.contact_order_by.focus();
	}*/ 
	else {
		form.submit();
	}
}


function popClient() {
//        window.open('./index.php?m=public&a=selector&dialog=1&callback=setCompany&table=companies', 'company','left=50,top=50,height=250,width=400,resizable');
	window.open("./index.php?m=contacts&a=select_contact_company&dialog=1&table_name=clients&client_id=<?php echo $client_detail['client_id'];?>", "company", "left=50,top=50,height=250,width=400,resizable");
}

function setClient( key, val ){
	var f = document.changecontact;
 	if (val != '') {
    	f.contact_company.value = key;
			f.contact_client_name.value = val;

    	window.client_id = key;
    	window.client_value = val;
    }
}

function delIt(){
	var form = document.changecontact;
	if(confirm( "<?php echo $AppUI->_('contactsDelete', UI_OUTPUT_JS);?>" )) {
		form.del.value = "<?php echo $activity_id;?>";
		form.submit();
	}
}

function orderByName( x ){
	var form = document.changecontact;
	if (x == "name") {
		//form.contact_order_by.value = form.contact_last_name.value + ", " + form.contact_first_name.value;
	} else {
		//form.contact_order_by.value = form.contact_client_name.value;
	}
}

function companyChange() {
	var f = document.changecontact;
	if ( f.contact_client.value != window.client_value ){
		f.contact_department.value = "";
	} 
}

</script>
<script type="text/javascript" src="/modules/public/pa_edit.js"></script>
<table border="0" cellpadding="4" cellspacing="0" width="100%"
	class="std">

	<form name="changecontact" action="#" method="post"
		class="form-style-2">
		<input type="hidden" name="dosql" value="do_activity_aed" /> <input
			type="hidden" name="del" value="0" /> <input type="hidden"
			name="activity_unique_update" value="<?php echo uniqid("");?>" /> <input
			type="hidden" name="activity_id" value="<?php echo $activity_id;?>" />

		<tr>
			<td colspan="2">
				<table border="0" cellpadding="1" cellspacing="1">
					<tr>
						<td align="right"><?php echo $AppUI->_('First Name');?>:</td>
						<td><input type="text" class="text"
							name="activity_pers_first_name"
							value="<?php echo @$row->activity_pers_first_name;?>"
							maxlength="50" /></td>
					</tr>
					<tr>
						<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Last Name');?>:</td>
						<td><input type="text" class="text" name="activity_pers_last_name"
							value="<?php echo @$row->activity_pers_last_name;?>"
							maxlength="50" <?php if($activity_id==0){?>
							onBlur="orderByName('name')" <?php }?> /></td>
					</tr>
					<tr>
						<td align="right" width="100"><?php echo $AppUI->_('Phone');?>:</td>
						<td><input type="text" class="text" name="activity_pers_telephon"
							value="<?php echo @$row->activity_pers_telephon;?>"
							maxlength="30" size="25" /></td>
					</tr>
					<tr>
						<td align="right" width="100"><?php echo $AppUI->_('Email');?>:</td>
						<td nowrap><input type="text" class="text"
							name="activity_pers_email"
							value="<?php echo @$row->activity_pers_email;?>" maxlength="255"
							size="25" /></td>
					</tr>
					<tr>
						<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Activity Name');?>:</td>
						<td><input type="text" class="text" size=25 name="activity_name"
							value="<?php echo @$row->activity_name;?>" maxlength="200"
							<?php if($activity_id==0){?> onBlur="orderByName('name')"
							<?php }?> /></td>
					</tr>
					<tr>
						<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Domain of activity');?>:</td>
						<td>
				<?php
				echo arraySelect ( dPgetSysVal ( 'ActivityDomaine' ), 'activity_domaine', 'class=text size=1', @$row->activity_domaine ? @$row->activity_domaine : '-1' );
				?>
			</td>
					</tr>
					<tr>
						<td>
					<?php echo $AppUI->_ ( 'Strategic Areas' ); ?>
				</td>
						<td><input type="button" class="text tree_edit" data-part="area"
							value="Select"> <input type="hidden" name="activity_strategies"
							value="<?php echo @$row->activity_strategies ?>" class="unq"> <span
							class="snote"></span></td>
					</tr>
					<tr>
						<td colspan="2">
							<div id="bub" style="display: none;"></div>
							<div id="area_box" style="display: none;"><?php echo htmlAreasList();?></div>
							<div id="locations_skin" style="display: none;"
								class="ui-widget ui-widget-content ui-corner-all ui-movable">
								<button type="button" id="lox_shut"
									class="ui-multiselect ui-widget ui-state-default ui-corner-all"
									title="Save and Close">
									<span>Save and Close</span>
								</button>
								<div id="land" style="padding: 1px;"></div>
							</div>
							<?php
							$thisYear = date ( "Y", time () );
							$annuals = range ( 2000, 2030 ); // range($thisYear,$thisYear+15);
							$sel_arr = array ();
							for($i = 0, $l = count ( $annuals ); $i < $l; $i ++) {
								$sel_arr [] = "<option value='" . $annuals [$i] . "'>" . $annuals [$i] . '</option>';
							}
							$bud_annual = '<select class="text yr">' . join ( "", $sel_arr ) . '</select>';
							echo '<div id="year_shelter" style="display: none;">', $bud_annual, '</div>';
							?>
						</td>
					</tr>
					<tr>
						<td>&nbsp;&nbsp;</td>
						<td>&nbsp;&nbsp;</td>
					</tr>
					<tr>
						<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('What type of intervention').'?';?></td>
						<td>
				<?php
				
				echo arraySelectCheckbox ( dPgetSysVal ( "TypeOfIntervention" ), 'activity_type_of_intervention[]', 'class=text size=1', @$row->activity_type_of_intervention || @$row->activity_type_of_intervention=='0' ? @$row->activity_type_of_intervention : '-1', null );
				?>
				
			</td>
					</tr>
					<tr>
						<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Start date of activity');?>:</td>
						<td><input type="date" class="text" size=25
							name="activity_start_date"
							value="<?php echo @$row->activity_start_date;?>" maxlength="50"
							<?php if($activity_id==0){?> onBlur="orderByName('name')"
							<?php }?> /></td>
					</tr>
					<tr>
						<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('End date of activity');?>:</td>
						<td><input type="date" class="text" size=25
							name="activity_end_date"
							value="<?php echo @$row->activity_end_date;?>" maxlength="50"
							<?php if($activity_id==0){?> onBlur="orderByName('name')"
							<?php }?> /></td>
					</tr>
					<tr>
						<td>&nbsp;&nbsp;</td>
						<td>&nbsp;&nbsp;</td>
					</tr>
					<tr>
						<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Type of beneficiary');?>:</td>
						<td>
				<?php
				
				echo arraySelectCheckbox ( dPgetSysVal ( 'TypeOfBeneficiery' ), 'activity_type_of_beneficiery[]', 'class=text size=1', @$row->activity_type_of_beneficiery || @$row->activity_type_of_beneficiery=='0' ? @$row->activity_type_of_beneficiery : '-1' );
				// echo arraySelectCheckboxFlat($activity_domaines,'activity_type_of_beneficiery[]','',null,$identifiers);
				
				?>
				
			</td>
					</tr>
					<tr>
						<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Number of beneficiaries');?>:</td>
						<td><input type="number" class="text" size=25
							name="activity_number_of_beneficiery"
							value="<?php echo @$row->activity_number_of_beneficiery;?>"
							maxlength="50" <?php if($activity_id==0){?>
							onBlur="orderByName('name')" <?php }?> /></td>
					</tr>
					<tr>
						<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Communes of intervention');?>:</td>
						<td>
				<?php
				// $administration_com = array('Bombardopolis','Mole Saint Nicolas','Anse Rouge','Cerca la Source','Cerca Carvajal','Anse a Galet','Pointe a Raquette','Anse a Pitre','Grand Gosier','Baie de Henne','Boucan Carre','Thomassique','Belle Anse');
				echo arraySelect ( $administration_com, 'administration_com[administration_com_name]', 'class="text chosen" id="administration_com" size=1 multiple=multiple', $activity_administration_com ? $activity_administration_com : '-1' );
				
				?>
				
				</td>
					</tr>
					<tr>
						<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Communal section of intervention');?>:</td>
						<td>
				<?php
				$ar = array (
						0 => '' 
				);
				$defaultsection = $administration_section ['010101'];
				if(activity_id && @$row->activity_administration_section){
					$tabtemp = explode(',', @$row->activity_administration_section);
					for($i=0;$i<count($tabtemp);$i++)
						$defaultsection[] = $administration_section [$tabtemp[$i]];
				}
				echo arraySelect ($defaultsection , 'activity_administration_section[]', 'class="text chosen" id="administration_sect" size=1 multiple=multiple', @$row->activity_administration_section ? @$row->activity_administration_section : '-1' );
				?>
				
				
						</td>
					</tr>
				</table>
			</td>
		</tr>
		</tr>


		<tr>
			<td><input type="button" value="<?php echo $AppUI->_('back');?>"
				class="button"
				onClick="javascript:window.location='./index.php?m=activity';" /></td>
			<td align="right"><input type="button"
				value="<?php echo $AppUI->_('submit');?>" class="button"
				onClick="submitIt()" /></td>
		</tr>
	</form>
</table>
