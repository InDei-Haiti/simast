<?php 
$relative_id = intval( dPgetParam( $_GET, "relative_id", 0 ) );
$client_id = intval( dPgetParam( $_GET, "client_id", 0 ) );

// check permissions for this caregiver
$perms =& $AppUI->acl();
// If the company exists we need edit permission,
// If it is a new company we need add permission on the module.
if ($relative_id)
  $canEdit = $perms->checkModuleItem($m, "edit", $relative_id);
else
  $canEdit = $perms->checkModule($m, "add");

if (!$canEdit) {
	$AppUI->redirect( "m=public&a=access_denied" );
}

// load the status
$status = dPgetSysVal( 'CaregiverStatus' );

// load the status
$boolTypes = dPgetSysVal( 'YesNo' );



// load the physical condition
$gender = dPgetSysVal( 'GenderType' );

// load the physical condition
$employment_types = dPgetSysVal( 'EmploymentType' );

// load the caregiver types
$relationship_types = dPgetSysVal( 'RelationType' );



// load the record data
$q  = new DBQuery;
$q->addTable('relatives');
$q->addQuery('relatives.*');
$q->addQuery('con.contact_first_name');
$q->addQuery('con.contact_last_name');
$q->addJoin('contacts', 'con', 'relatives.relative_contact = con.contact_id');
$q->addWhere('relatives.relative_id = '.$relative_id);
$sql = $q->prepare();
//var_dump($sql);
$q->clear();

$obj = new CRelative();
if (!db_loadObject( $sql, $obj ) && $relative_id > 0) {
	// $AppUI->setMsg( '	$qid =& $q->exec(); Company' ); // What is this for?
	$AppUI->setMsg( "invalidID", UI_MSG_ERROR, true );
	$AppUI->redirect();
}

// collect all the users for the company owner list
$q  = new DBQuery;
$q->addTable('contacts','con');
$q->leftJoin('users','u', 'u.user_contact = con.contact_id');
$q->addQuery('contact_id');
$q->addQuery('CONCAT_WS(", ",contact_last_name,contact_first_name)'); 
$q->addOrder('contact_last_name');
$owners = $q->loadHashList();

// setup the title block
$ttl = $relative_id > 0 ? "Edit Relative" : "New Relative";
$titleBlock = new CTitleBlock( $ttl, '', $m, "$m.$a" );
$titleBlock->addCrumb( "?m=contacts", "contacts list" );
if ($company_id != 0)
  $titleBlock->addCrumb( "?m=relatives&a=view&relative_id=$relative_id", "view this caregiver" );
$titleBlock->show();
?>

<script language="javascript">
function submitIt() {
	var form = document.changerelative;
	if (form.contact_last_name.value.length < 3) {
		alert( "<?php echo $AppUI->_('companyValidName', UI_OUTPUT_JS);?>" );
		form.contact_last_name.focus();
	} else {
		form.submit();
	}
}



</script>

<form name="changerelative" action="?m=relatives" method="post">
	<input type="hidden" name="dosql" value="do_relative_aed" />
	<input type="hidden" name="relative_id" value="<?php echo $relative_id;?>" />
	<input type="hidden" name="client_id" value="<?php echo $client_id;?>" />
<table cellspacing="1" cellpadding="1" border="0" width='100%' class="std">


<tr>
<td>


<table>
	<tr>
		<td align="right"><?php echo $AppUI->_('First Name');?>:</td>
		<td>
			<input type="text" class="text" name="contact_first_name" value="<?php echo dPformSafe(@$obj->contact_first_name);?>" size="50" maxlength="255" /> (<?php echo $AppUI->_('required');?>)
		</td>
	</tr>	
	<tr>
		<td align="right"><?php echo $AppUI->_('Other Name(s)');?>:</td>
		<td>
			<input type="text" class="text" name="contact_other_names" value="<?php echo dPformSafe(@$obj->contact_other_names);?>" size="50" maxlength="255" /> (<?php echo $AppUI->_('required');?>)
		</td>
	</tr>	
	<tr>
		<td align="right"><?php echo $AppUI->_('Last Name');?>:</td>
		<td>
			<input type="text" class="text" name="contact_last_name" value="<?php echo dPformSafe(@$obj->contact_last_name);?>" size="50" maxlength="255" /> (<?php echo $AppUI->_('required');?>)
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo $AppUI->_('Year of Birth');?>:</td>
		<td>
			<input type="text" class="text" name="relative_yob" value="<?php echo dPformSafe(@$obj->relative_yob);?>" size="30" maxlength="255" />
		</td>
	</tr>		
	<tr>
		<td align="right"><?php echo $AppUI->_('ID No');?>:</td>
		<td>
			<input type="text" class="text" name="relative_idno" value="<?php echo dPformSafe(@$obj->relative_idno);?>" size="30" maxlength="255" />
		</td>
	</tr>	
	<tr>
		<td align="right"><?php echo $AppUI->_('Email');?>:</td>
		<td>
			<input type="text" class="text" name="contact_email" value="<?php echo dPformSafe(@$obj->company_email);?>" size="30" maxlength="255" />
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo $AppUI->_('Phone');?>:</td>
		<td>
			<input type="text" class="text" name="contact_phone1" value="<?php echo dPformSafe(@$obj->company_phone1);?>" maxlength="30" />
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo $AppUI->_('Phone');?>2:</td>
		<td>
			<input type="text" class="text" name="contact_phone2" value="<?php echo dPformSafe(@$obj->company_phone2);?>" maxlength="50" />
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo $AppUI->_('Fax');?>:</td>
		<td>
			<input type="text" class="text" name="contact_fax" value="<?php echo dPformSafe(@$obj->company_fax);?>" maxlength="30" />
		</td>
	</tr>
	<tr>
		<td colspan=2 align="center">
			<img src="images/shim.gif" width="50" height="1" /><?php echo $AppUI->_('Address');?><br />
			<hr width="500" align="center" size=1 />
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo $AppUI->_('Address');?>1:</td>
		<td><input type="text" class="text" name="contact_address1" value="<?php echo dPformSafe(@$obj->contact_address1);?>" size=50 maxlength="255" /></td>
	</tr>
	<tr>
		<td align="right"><?php echo $AppUI->_('Address');?>2:</td>
		<td><input type="text" class="text" name="contact_address2" value="<?php echo dPformSafe(@$obj->contact_address2);?>" size=50 maxlength="255" /></td>
	</tr>
	<tr>
		<td align="right"><?php echo $AppUI->_('City');?>:</td>
		<td><input type="text" class="text" name="contact_city" value="<?php echo dPformSafe(@$obj->contact_city);?>" size=50 maxlength="50" /></td>
	</tr>
	<tr>
		<td align="right"><?php echo $AppUI->_('State');?>:</td>
		<td><input type="text" class="text" name="contact_state" value="<?php echo dPformSafe(@$obj->contact_state);?>" maxlength="50" /></td>
	</tr>
	<tr>
		<td align="right"><?php echo $AppUI->_('Zip');?>:</td>
		<td><input type="text" class="text" name="contact_zip" value="<?php echo dPformSafe(@$obj->contact_zip);?>" maxlength="15" /></td>
	</tr>
    <tr>
		<td align="left" valign="top"><?php echo $AppUI->_('Gender');?>:</td>
		<td align="left" valign="top">
			<?php echo arraySelect($gender, "relative_gender", 'class="text"', $obj->relative_gender? $obj->relative_gender :1); ?>
        </td>
	 </tr>	
    <tr>
		<td align="left" valign="top"><?php echo $AppUI->_('Relationship to client');?>:</td>
		<td align="left" valign="top">
			<?php echo arraySelect($relationship_types, "relative_type", 'class="text"', $obj->relative_type? $obj->relative_type :1); ?>
        </td>
	 </tr>	 

	<tr>	
		<td align="left" valign=top><?php echo $AppUI->_('Description');?>:</td>
		<td valign="top">
					<textarea cols="70" rows="10" class="textarea" name="relative_notes"><?php echo @$obj->relative_notes;?></textarea>
		</td>
	</tr>		
</table>


</td>
 
	<td align='left'>
		<?php
 			require_once("./classes/CustomFields.class.php");
 			$custom_fields = New CustomFields( $m, $a, $obj->relative_id, "edit" );
 			$custom_fields->printHTML();
		?>	
			
	</td>

	
	
</tr>

<tr>
	<td><input type="button" value="<?php echo $AppUI->_('back');?>" class="button" onClick="javascript:history.back(-1);" /></td>
	<td align="right"><input type="button" value="<?php echo $AppUI->_('submit');?>" class="button" onClick="submitIt()" /></td>
</tr>


</table>
</form>
