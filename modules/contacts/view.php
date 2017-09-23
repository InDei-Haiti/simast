<?php /* CONTACTS $Id: view.php,v 1.14.4.3 2005/11/26 02:11:35 cyberhorse Exp $ */
$contact_id = intval( dPgetParam( $_GET, 'contact_id', 0 ) );
$page = intval( dPgetParam( $_GET, 'page', 1));
$limit = intval($dPconfig['max_limit']);
$AppUI->savePlace();

// check permissions for this record
//$canEdit = !getDenyEdit( $m, $contact_id );
//if (!$canEdit) {
//	$AppUI->redirect( "m=public&a=access_denied" );
//}
$positionOptions = dPgetSysVal('PositionOptions');
// load the record data
$msg = '';
$row = new CContact();


$canDelete = $row->canDelete( $msg, $contact_id );

// Don't allow to delete contacts, that have a user associated to them.

$q  = new DBQuery;
$q->addTable('users');
$q->addQuery('user_id');
$q->addWhere('user_contact = ' . $contact_id);
$sql = $q->prepare();
$q->clear();
$tmp_user = db_loadResult($sql);
if (!empty($tmp_user))
	$canDelete = false; 

$canEdit = $perms->checkModuleItem($m, "edit", $contact_id);

if (!$row->load( $contact_id ) && $contact_id > 0) {
	$AppUI->setMsg( 'Staff' );
	$AppUI->setMsg( "invalidID", UI_MSG_ERROR, true );
	$AppUI->redirect();
} else if ($row->contact_private && $row->contact_owner != $AppUI->user_id
	&& $row->contact_owner && $contact_id != 0) {
// check only owner can edit
	$AppUI->redirect( "m=public&a=access_denied" );
}

// Get the contact details for company and department
$company_detail = $row->getCompanyDetails();
$dept_detail = $row->getDepartmentDetails();

$constat=dPgetSysVal('ContactStatus');

// setup the title block
$ttl = "View Staff";
$titleBlock = new CTitleBlock( $ttl, '', $m, "$m.$a" );
$titleBlock->addCrumb( "?m=contacts", "Staff" );
if ($canEdit && $contact_id)
        $titleBlock->addCrumb( "?m=contacts&a=addedit&contact_id=$contact_id", 'Edit' );
	/*$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_('new project').'" />', '',
		'<form action="?m=projects&a=addedit&company_id='.$row->contact_company.'&contact_id='.$contact_id.'" method="post">', '</form>'
	);*/
if ($canDelete && $contact_id) {
	$titleBlock->addCrumbDelete( 'delete staff', $canDelete, $msg );
}
$titleBlock->show();
?>
<form name="changecontact" action="?m=contacts" method="post">
        <input type="hidden" name="dosql" value="do_contact_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="contact_id" value="<?php echo $contact_id;?>" />
        <input type="hidden" name="contact_owner" value="<?php echo $row->contact_owner ? $row->contact_owner : $AppUI->user_id;?>" />
</form>
<script language="JavaScript">
function delIt(){
        var form = document.changecontact;
        if(confirm( "<?php echo $AppUI->_('contactsDelete', UI_OUTPUT_JS);?>" )) {
                form.del.value = "<?php echo $contact_id;?>";
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
			<td class="hilite"><?php echo @$row->contact_first_name;?></td>
		</tr>
		<tr>
			<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Last Name');?>:</td>
			<td class="hilite"><?php echo @$row->contact_last_name;?></td>
		</tr>
		<tr>
			<td align="right" width="100"><?php echo $AppUI->_('Display Name');?>: </td>
			<td class="hilite"><?php echo @$row->contact_order_by;?></td>
		</tr>
		<tr>
			<td align="right" width="100"><?php echo $AppUI->_('Status');?>: </td>
			<td class="hilite"><?php echo @$constat[$row->contact_active];?></td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td valign="top" width="50%">
		<table border="0" cellpadding="1" cellspacing="1" class="details" width="100%">
		<tr>
			<td align="right"><?php echo $AppUI->_('Job Title');?>:</td>
			<td class="hilite"><?php echo @$row->contact_job;?></td>
		</tr>
		<!--
		<tr>
			<td align="right" width="100"><?php echo $AppUI->_('Client');?>:</td>
			<?php //if ($perms->checkModuleItem( 'companies', 'access', $row->contact_company )) {?>
            			<td nowrap> <?php //echo "<a href='?m=companies&a=view&company_id=" . @$row->contact_company ."'>" . htmlspecialchars( $company_detail['company_name'], ENT_QUOTES) . '</a>' ;?></td>
			<?php //} else {?>
						<td nowrap><?php //echo htmlspecialchars( $company_detail['company_name'], ENT_QUOTES);?></td>
			<?php //}?>
		</tr>
		-->
		<tr>
			<td align="right"><?php echo $AppUI->_('Title');?>:</td>
			<td class="hilite"><?php echo @$row->contact_title;?></td>
		</tr>
		<tr>
			<td align="right"><?php echo $AppUI->_('Position');?>:</td>
			<td class="hilite"><?php echo $positionOptions[@$row->contact_type];?></td>
		</tr>
		<tr>
			<td align="right" valign="top" width="100"><?php echo $AppUI->_('Address');?>:</td>
			<td class="hilite">
                                <?php echo @$row->contact_address1;?><br />
			        <?php echo @$row->contact_address2;?><br />
			        <?php echo @$row->contact_city . ', ' . @$row->contact_state . ' ' . @$row->contact_zip;?>
                        </td>
		</tr>
		<tr>
			<td align="right" width="100"><?php echo $AppUI->_('Phone');?>:</td>
			<td class="hilite"><?php echo @$row->contact_phone;?></td>
		</tr>
		<tr>
			<td align="right"><?php echo $AppUI->_('Phone');?>2:</td>
			<td class="hilite"><?php echo @$row->contact_phone2;?></td>
		</tr>
		<tr>
			<td align="right"><?php echo $AppUI->_('Fax');?>:</td>
			<td class="hilite"><?php echo @$row->contact_fax;?></td>
		</tr>
		<tr>
			<td align="right"><?php echo $AppUI->_('Mobile Phone');?>:</td>
			<td class="hilite"><?php echo @$row->contact_mobile;?></td>
		</tr>
		<tr>
			<td align="right" width="100"><?php echo $AppUI->_('Email');?>:</td>
			<td nowrap class="hilite"><a href="mailto:<?php echo @$row->contact_email;?>"><?php echo @$row->contact_email;?></a></td>
		</tr>
		<tr>
			<td align="right"><?php echo $AppUI->_('Email');?>2:</td>
			<td nowrap class="hilite"><a href="mailto:<?php echo @$row->contact_email2;?>"><?php echo @$row->contact_email2;?></a></td>
		</tr>
		<tr>
			<td align="right"><?php echo $AppUI->_('URL');?>:</td>
			<td nowrap class="hilite"><a href="<?php echo @$row->contact_url;?>"><?php echo @$row->contact_url;?></a></td>
		</tr>
		<tr>
			<td align="right"><?php echo $AppUI->_('Birthday');?>:</td>
			<td nowrap class="hilite"><?php echo @substr($row->contact_birthday, 0, 10);?></td>
		</tr>
		</table>
	</td>
	<td valign="top" width="50%">
		<strong><?php echo $AppUI->_('Notes');?></strong><br />
		<?php echo @nl2br($row->contact_notes);?>
	</td>
</tr>
<tr>
	<td>
		<input type="button" value="<?php echo $AppUI->_('back');?>" class="button" onClick="javascript:window.location='./index.php?m=contacts';" />
	</td>
</tr>
</form>
</table>
<?php
//include customer roles
$tabBox = new CTabBox ("?m=contacts", dPgetConfig('root_dir')."/modules/contacts/");
$tabBox->add('vw_contact_roles', 'Staff Roles' );
$tabBox->show();

?>