<?php

	global $AppUI;
	require_once $AppUI->getModuleClass('contacts');
	
	$contacts_submited    = dPgetParam($_POST, "contacts_submited", 0);	
	//get temporary id for contacts
	$contact_unique_update = dPgetParam($_GET, 'contact_unique_update', 0);
	$type_ui_active = intval (dPgetParam($_GET, 'type_ui_active', 0));
	
	$row = new CContact();
	
	$client_id = intval (dPgetParam($_REQUEST, 'client_id', 0));
	$client_name = dPgetParam($_REQUEST, 'client_name', null);

	if ($contacts_submited == 1)
	{
		if (!$row->bind( $_POST )) 
		{
			$AppUI->setMsg( $row->getError(), UI_MSG_ERROR );
			$AppUI->redirect();
		}

		$del = dPgetParam( $_POST, 'del', 0 );

		// prepare (and translate) the module name ready for the suffix

		if ($del) 
		{
			if (($msg = $row->delete())) 
			{
				$AppUI->setMsg( $msg, UI_MSG_ERROR );

			} 
			else 
			{
				$AppUI->setMsg( "deleted", UI_MSG_ALERT, true );
			}
		} 
		else 
		{
			$isNotNew = @$_POST['contact_id'];

			if (($msg = $row->store())) 
			{
				$AppUI->setMsg( $msg, UI_MSG_ERROR );
			} 
			else 
			{
				//store the contact  types.
				$contactTypes = dPgetParam($_POST, 'contact_roles', NULL);
				if (($client_id <= 0) && ($contact_unique_update > 0))
				{
					$client_id = $contact_unique_update; 
				}
		
				if (isset($contactTypes))
				{
		
					$sql = 'DELETE FROM client_contacts WHERE client_contacts_contact_id = ' . $row->contact_id . ' AND client_contacts_client_id = "' . $client_id . '"';
					if (!$ret = db_exec($sql))
					{
						$AppUI->setMsg($msg, 'delete::update of roles failed');
					}
			
					foreach ($contactTypes as $typeid => $typeval)
					{
						$sql = "INSERT INTO client_contacts(client_contacts_contact_id, client_contacts_client_id, client_contacts_contact_type) VALUES ( $row->contact_id,\"$client_id\", $typeval)";  
				
						if (!$ret = db_exec($sql))
						{
							$AppUI->setMsg($msg, 'insert::update of roles failed');
						}
					}
				}
			
				$AppUI->setMsg( $isNotNew ? 'updated' : 'added', UI_MSG_OK, true );
				?>
				<script language="javascript">
					//document.contactsEdit.reload();
					self.close();
				</script>
			<?php
			}

		}	
	}
	else
	{
		//load types of contacts for company contacts list 
		$q  = new DBQuery;
		//$q->addTable('users','u');
		$q->addTable('contact_types');
		$q->addQuery('typ_id');
		$q->addQuery('typ_desc');
		$q->addOrder('typ_desc');
		if ($type_ui_active == 1)
			$q->addWhere("type_ui_active = 1");
			
		$contactTypes = $q->loadHashList();

		$ttl = $contact_id > 0 ? "Edit Contact" : "Add Contact";
		$titleBlock = new CTitleBlock ($ttl, '', $m, "$m.$a");
		//$titleBlock->addCrumb("?m=contacts", "contacts list");


		$titleBlock->show();
		


		if (!isset($contact_unique_update) || ($contact_unique_update == 0))
			$contact_unique_update = uniqid("");
	
		// load the record data
		$msg = '';
		$row = new CContact();
	
		$canDelete = $row->canDelete( $msg, $contact_id );
?>
<script language="javascript">

function submitIt()
{
  var form = document.addcontact;
  if (form.contact_last_name.value.length < 1)
  {
    alert("<?php echo $AppUI->_('contactsValidName', UI_OUTPUT_JS);?>");
    form.contact_last_name.focus();
  }
  else
  {
    form.submit();
  }
}

</script>
<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">
<form name="addcontact" action="index.php?m=public&a=contact_adder&contact_unique_update=<?php echo $contact_unique_update; ?>" method="post">
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="contact_unique_update" value="<?php echo $contact_unique_update;?>" />
  <input type="hidden" name="contact_id" value="<?php echo $contact_id;?>" />
  <input type="hidden" name="contact_company_id" value="<?php echo $company_id;?>" />

<tr>
    <td colspan="2">
      <table border="0" cellpadding = "1" cellspacing="1">
       <tr>
         <td align="right"><?php echo $AppUI->_('First Name');?>:</td>
         <td>
          <input type="text" class="text" size=25 name="contact_first_name" value="<?php echo @$row->contact_first_name;?>" maxlength="50" />
         </td>
       </tr>
       <tr>
         <td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Last Name');?>:</td>
	 <td>
	    <input type="text" class="text" size=25 name="contact_last_name" value="<?php echo @$row->contact_last_name;?>" maxlength="50" />
	 </td>
      </tr>
      <tr>
         <td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Contact Type');?>:</td>
		 <td nowrap>
		 <?php
				$cTypeString =  '';
				$cTypes = $row->getContactRoles($client_id);
				
				foreach ($contactTypes  as $type_id => $type_desc)
				{
					$cTypeString .= "&nbsp;&nbsp;<input type='checkbox' name='contact_roles[". $type_id. "]' value='" . $type_id . "'";
						if ( isset($cTypes) && in_array($type_id, $cTypes))
							$cTypeString .= " checked"; 
					$cTypeString .= "/>" . $AppUI->_( $type_desc );

				}
				echo $cTypeString;

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
            <input type="text" class="text" name="contact_job" value="<?php echo @$row->contact_job;?>" maxlength="100" size="25" />
        </td>
      </tr>
      <tr>
			<td align="right"><?php echo $AppUI->_('Title');?>:</td>
			<td><input type="text" class="text" name="contact_title" value="<?php echo @$row->contact_title;?>" maxlength="50" size="25" /></td>
     </tr>
     <tr>
			<td align="right" width="100"><?php echo $AppUI->_('Address');?>1:</td>
			<td><input type="text" class="text" name="contact_address1" value="<?php echo @$row->contact_address1;?>" maxlength="60" size="25" /></td>
     </tr>
     <tr>
			<td align="right"><?php echo $AppUI->_('Address');?>2:</td>
			<td><input type="text" class="text" name="contact_address2" value="<?php echo @$row->contact_address2;?>" maxlength="60" size="25" /></td>
    </tr>
    <tr>
			<td align="right"><?php echo $AppUI->_('City');?>:</td>
			<td><input type="text" class="text" name="contact_city" value="<?php echo @$row->contact_city;?>" maxlength="30" size="25" /></td>
   </tr>
  <tr>
			<td align="right" width="100"><?php echo $AppUI->_('Phone');?>:</td>
			<td>
				<input type="text" class="text" name="contact_phone" value="<?php echo @$row->contact_phone;?>" maxlength="30" size="25" />
			</td>
		</tr>
		<tr>
			<td align="right"><?php echo $AppUI->_('Fax');?>:</td>
			<td>
				<input type="text" class="text" name="contact_fax" value="<?php echo @$row->contact_fax;?>" maxlength="30" size="25" />
			</td>
		</tr>
		<tr>
			<td align="right"><?php echo $AppUI->_('Mobile Phone');?>:</td>
			<td>
				<input type="text" class="text" name="contact_mobile" value="<?php echo @$row->contact_mobile;?>" maxlength="30" size="25" />
			</td>
		</tr>
		<tr>
			<td align="right" width="100"><?php echo $AppUI->_('Email');?>:</td>
			<td nowrap>
				<input type="text" class="text" name="contact_email" value="<?php echo @$row->contact_email;?>" maxlength="255" size="25" />
			</td>
		</tr>

   </table>
   </td>
   	<td valign="top" width="50%">
		<strong><?php echo $AppUI->_('Contact Notes');?></strong><br />
		<textarea class="textarea" name="contact_notes" rows="20" cols="40"><?php echo @$row->contact_notes;?></textarea></td>
	</td>
</tr>
<tr>
	<td>
		<input type="button" value="<?php echo $AppUI->_('back');?>" class="button" onClick="javascript:self.close();" />
	</td>
	<td align="right">
		<input name='contacts_submited' type='hidden' value='1' />
		<input type="button" value="<?php echo $AppUI->_('submit');?>" class="button" onClick="submitIt()" />
	</td>
</tr>
</form>
</table>
<?php
}
?>
