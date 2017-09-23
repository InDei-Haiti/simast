<?php /* CONTACTS $Id: addedit.php,v 1.35.2.1 2005/11/21 04:37:54 pedroix Exp $ */
$contact_id = intval( dPgetParam( $_GET, 'contact_id', 0 ) );
$client_id = intval( dPgetParam( $_REQUEST, 'client_id', 0 ) );
$client_name = dPgetParam( $_REQUEST, 'client_name', null );

// check permissions for this record
$perms =& $AppUI->acl();
if (! ($canEdit = $perms->checkModuleItem( 'contacts', 'edit', $contact_id )) ) {
	$AppUI->redirect( "m=public&a=access_denied" );
}

// load the record data
$msg = '';
$row = new CContact();
$canDelete = $row->canDelete( $msg, $contact_id );

if (!$row->load( $contact_id ) && $contact_id > 0) {
	$AppUI->setMsg( 'Contact' );
	$AppUI->setMsg( "invalidID", UI_MSG_ERROR, true );
	$AppUI->redirect();
} else if ($row->contact_private && $row->contact_owner != $AppUI->user_id
	&& $row->contact_owner && $contact_id != 0) {
// check only owner can edit
	$AppUI->redirect( "m=public&a=access_denied" );
}

echo '<br /><br /><div class="card">';
$positionOptions = dPgetSysVal('PositionOptions');
$positionOptions = arrayMerge(array(0=>'Select Position'), $positionOptions);
// setup the title block
$ttl = $contact_id > 0 ? "Edit Staff" : "Add Staff";
$titleBlock = new CTitleBlock( $ttl, '', $m, "$m.$a" );
$titleBlock->addCrumb( "?m=contacts", "Staff" );
if ($canEdit && $contact_id) {
	$titleBlock->addCrumbDelete( 'delete staff', $canDelete, $msg );
}
$titleBlock->show();
$client_detail = $row->getClientDetails();

if (empty($client_detail))
{
   $client_detail = $row->getClientDetail($client_id);
}

if ($contact_id == 0 && $client_id > 0) 
{
	$client_detail['client_id'] = $client_id;
	$client_detail['client_name'] = $client_name;
	echo $client_name;
}

?>

<script language="javascript">
<?php
	echo "window.client_id=" . dPgetParam($client_detail, 'client_id', 0) . ";\n";
	echo "window.client_value='" . addslashes(dPgetParam($client_detail, 'client_name', "")) . "';\n";
?>

function submitIt() {
	var form = document.changecontact;
	if (form.contact_last_name.value.length < 1) {
		alert( "<?php echo $AppUI->_('contactsValidName', UI_OUTPUT_JS);?>" );
		form.contact_last_name.focus();
	} else if (form.contact_order_by.value.length < 1) {
		alert( "<?php echo $AppUI->_('contactsOrderBy', UI_OUTPUT_JS);?>" );
		form.contact_order_by.focus();
	} else {
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
		form.del.value = "<?php echo $contact_id;?>";
		form.submit();
	}
}

function orderByName( x ){
	var form = document.changecontact;
	if (x == "name") {
		form.contact_order_by.value = form.contact_last_name.value + ", " + form.contact_first_name.value;
	} else {
		form.contact_order_by.value = form.contact_client_name.value;
	}
}

function companyChange() {
	var f = document.changecontact;
	if ( f.contact_client.value != window.client_value ){
		f.contact_department.value = "";
	} 
}

</script>

<div class="mtab">
<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">

<form name="changecontact" action="?m=contacts" method="post">
	<input type="hidden" name="dosql" value="do_contact_aed" />
	<input type="hidden" name="del" value="0" />
	<input type="hidden" name="contact_project" value="0" />
	<input type="hidden" name="contact_unique_update" value="<?php echo uniqid("");?>" />
	<input type="hidden" name="contact_id" value="<?php echo $contact_id;?>" />
	<input type="hidden" name="contact_owner" value="<?php echo $row->contact_owner ? $row->contact_owner : $AppUI->user_id;?>" />

<tr>
	<td colspan="2">
		<table border="0" cellpadding="1" cellspacing="1">
		<tr>
			<td align="right"><?php echo $AppUI->_('First Name');?>:</td>
			<td>
				<input type="text" class="form-control" size=25 name="contact_first_name" value="<?php echo @$row->contact_first_name;?>" maxlength="50" />
			</td>
		</tr>
		<tr>
			<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Last Name');?>:</td>
			<td>
				<input type="text" class="form-control" size=25 name="contact_last_name" value="<?php echo @$row->contact_last_name;?>" maxlength="50" <?php if($contact_id==0){?> onBlur="orderByName('name')"<?php }?> />
				<a href="#" onClick="orderByName('name')">[<?php echo $AppUI->_('use in display');?>]</a>
			</td>
		</tr>
		<tr>
			<td align="right" width="100"><?php echo $AppUI->_('Display Name');?>: </td>
			<td>
				<input type="text" class="form-control" size=25 name="contact_order_by" value="<?php echo @$row->contact_order_by;?>" maxlength="50" />
			</td>
		</tr>
		<tr>
			<td align="right" width="100"><?php echo $AppUI->_('Active');?>: </td>
			<td>
				<?php
					echo arraySelectRadio(dPgetSysVal('ContactStatus'),'contact_active','',!is_null($row->contact_active) ? $row->contact_active : "1",$identifiers);
				?>
			</td>
		</tr>

		</table>
	</td>
</tr>

	<td valign="top" width="50%">
		<table border="0" cellpadding="1" cellspacing="1" class="details" width="100%">
		<tr>
			<td align="right" width="100"><?php echo $AppUI->_('Job Title');?>:</td>
			<td nowrap>
				<input type="text" class="form-control" name="contact_job" value="<?php echo @$row->contact_job;?>" maxlength="100" size="25" />
			</td>
		</tr>
		<!--
		<tr>
			<td align="right" width="100"><?php echo $AppUI->_('Client');?>:</td>
			<td nowrap>
				<input type="text" class="text" name="contact_client_name" value="<?php 
					//echo $client_detail['client_name'];
					?>" maxlength="100" size="25" />
				<input type="button" class="button" value="<?php //echo $AppUI->_('select client...');?>..." onclick="popClient()" />
				<input type='hidden' name='contact_client' value="<?php //echo $client_detail['client_id']; ?>">
				<a href="#" onClick="orderByName('client')">[<?php //echo $AppUI->_('use in display');?>]</a>
				</td>
		</tr>
		-->
		<tr>
			<td align="right"><?php echo $AppUI->_('Title');?>:</td>
			<td><input type="text" class="form-control" name="contact_title" value="<?php echo @$row->contact_title;?>" maxlength="50" size="25" /></td>
		</tr>
		<tr>
			<td align="right"><?php echo $AppUI->_('Position');?>:</td>
			<td>
			<?php
				echo arraySelect( $positionOptions, 'contact_type', 'class=form-control size=1', @$row->contact_type, true );
			?>
			</td>
		</tr>
		<tr>
			<td align="right" width="100"><?php echo $AppUI->_('Address');?>1:</td>
			<td><input type="text" class="form-control" name="contact_address1" value="<?php echo @$row->contact_address1;?>" maxlength="60" size="25" /></td>
		</tr>
		<tr>
			<td align="right"><?php echo $AppUI->_('Address');?>2:</td>
			<td><input type="text" class="form-control" name="contact_address2" value="<?php echo @$row->contact_address2;?>" maxlength="60" size="25" /></td>
		</tr>
		<tr>
			<td align="right"><?php echo $AppUI->_('City');?>:</td>
			<td><input type="text" class="text" name="contact_city" value="<?php echo @$row->contact_city;?>" maxlength="30" size="25" /></td>
		</tr>
		<tr>
			<td align="right"><?php echo $AppUI->_('State');?>:</td>
			<td><input type="text" class="form-control" name="contact_state" value="<?php echo @$row->contact_state;?>" maxlength="30" size="25" /></td>
		</tr>
		<tr>
			<td align="right"><?php echo $AppUI->_('Postcode').' / '.$AppUI->_('Zip');?>:</td>
			<td><input type="text" class="form-control" name="contact_zip" value="<?php echo @$row->contact_zip;?>" maxlength="11" size="25" /></td>
		</tr>
		<tr>
			<td align="right" width="100"><?php echo $AppUI->_('Phone');?>:</td>
			<td>
				<input type="text" class="form-control" name="contact_phone" value="<?php echo @$row->contact_phone;?>" maxlength="30" size="25" />
			</td>
		</tr>
		<tr>
			<td align="right"><?php echo $AppUI->_('Phone');?>2:</td>
			<td>
				<input type="text" class="form-control" name="contact_phone2" value="<?php echo @$row->contact_phone2;?>" maxlength="30" size="25" />
			</td>
		</tr>
		<tr>
			<td align="right"><?php echo $AppUI->_('Fax');?>:</td>
			<td>
				<input type="text" class="form-control" name="contact_fax" value="<?php echo @$row->contact_fax;?>" maxlength="30" size="25" />
			</td>
		</tr>
		<tr>
			<td align="right"><?php echo $AppUI->_('Mobile Phone');?>:</td>
			<td>
				<input type="text" class="form-control" name="contact_mobile" value="<?php echo @$row->contact_mobile;?>" maxlength="30" size="25" />
			</td>
		</tr>
		<tr>
			<td align="right" width="100"><?php echo $AppUI->_('Email');?>:</td>
			<td nowrap>
				<input type="text" class="form-control" name="contact_email" value="<?php echo @$row->contact_email;?>" maxlength="255" size="25" />
			</td>
		</tr>
		<tr>
			<td align="right"><?php echo $AppUI->_('Email');?>2:</td>
			<td>
				<input type="text" class="form-control" name="contact_email2" value="<?php echo @$row->contact_email2;?>" maxlength="255" size="25" />
			</td>
		</tr>
		</table>
	</td>
	<td valign="top" width="50%">
		<strong><?php echo $AppUI->_('Notes');?></strong><br />
		<textarea class="textarea" name="contact_notes" class="form-control"  rows="20" cols="40"><?php echo @$row->contact_notes;?></textarea></td>
	</td>
</tr>
<tr>
	<td>
		<input class="button ce pi ahr" type="button" value="<?php echo $AppUI->_('back');?>" class="button" onClick="javascript:window.location='./index.php?m=contacts';" />
	</td>
	<td align="right">
		<input class="button ce pi ahr" type="button" value="<?php echo $AppUI->_('submit');?>" class="button" onClick="submitIt()" />
	</td>
</tr>
</form>
</table>
</div>

<?php echo '</div>';?>