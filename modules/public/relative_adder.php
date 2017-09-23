<?php

	global $AppUI;
	require_once $AppUI->getModuleClass('contacts');
	require_once $AppUI->getModuleClass('relatives');
	
	$contacts_submited    = dPgetParam($_POST, "contacts_submited", 0);	
	//get temporary id for contacts
	$contact_unique_update = dPgetParam($_GET, 'contact_unique_update', 0);
	$type_ui_active = intval (dPgetParam($_GET, 'type_ui_active', 0));
	
	$contact = new CContact();
	$relative = new CRelative();
	
	$client_id = intval (dPgetParam($_REQUEST, 'client_id', 0));
	$client_name = dPgetParam($_REQUEST, 'client_name', null);

	if ($contacts_submited == 1)
	{
		if (!$contact->bind( $_POST )) 
		{
			$AppUI->setMsg( $contact->getError(), UI_MSG_ERROR );
			$AppUI->redirect();
		}

		
		if (!$caregiver->bind( $_POST )) 
		{
			$AppUI->setMsg( $contact->getError(), UI_MSG_ERROR );
			$AppUI->redirect();
		}	
		$del = dPgetParam( $_POST, 'del', 0 );

		// prepare (and translate) the module name ready for the suffix

		if ($del) 
		{
			if (($msg = $contact->delete())) 
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

			if (($msg = $contact->store())) 
			{
				$AppUI->setMsg( $msg, UI_MSG_ERROR );
			} 
			else 
			{
				//store the contact  types.

				$contactTypes = dPgetParam($_POST, 'relative_type', NULL);
				if (($client_id <= 0) && ($contact_unique_update > 0))
				{
					$client_id = $contact_unique_update; 
				}
		
				if (isset($contactTypes))
				{
		
					$sql = 'DELETE FROM client_contacts WHERE client_contacts_contact_id = ' . $contact->contact_id . ' AND client_contacts_client_id = "' . $client_id . '"';
					if (!$ret = db_exec($sql))
					{
						$AppUI->setMsg($msg, 'delete::update of roles failed');
					}

					$sql = "INSERT INTO client_contacts(client_contacts_contact_id, client_contacts_client_id, client_contacts_contact_type) VALUES ( $contact->contact_id,\"$client_id\", $contactTypes)";  
				
					if (!$ret = db_exec($sql))
					{
							$AppUI->setMsg($msg, 'insert::update of roles failed');
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
		$contactTypes = dPgetSysVal('RelationType');
		$genderTypes = dPgetSysVal('GenderType');
		$relativeTypes = dPgetSysVal('RelationType');
		$ttl = $contact_id > 0 ? "Edit Relative" : "Add Relative";
		$titleBlock = new CTitleBlock ($ttl, '', $m, "$m.$a");
		//$titleBlock->addCrumb("?m=contacts", "contacts list");


		$titleBlock->show();
		


		if (!isset($contact_unique_update) || ($contact_unique_update == 0))
			$contact_unique_update = uniqid("");
	
		// load the record data
		$msg = '';
		$contact = new CContact();
		$relative = new CRelative();
	
		$canDelete = $contact->canDelete( $msg, $contact_id );
?>
<script language="javascript">

function submitIt()
{
  var form = document.addrelative;
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
<form name="addrelative" action="index.php?m=public&a=contact_adder&contact_unique_update=<?php echo $contact_unique_update; ?>" method="post">
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="contact_unique_update" value="<?php echo $contact_unique_update;?>" />
  <input type="hidden" name="contact_id" value="<?php echo $contact_id;?>" />
  <input type="hidden" name="contact_client_id" value="<?php echo $client_id;?>" />
  <input type="hidden" name="relative_client_id" value="<?php echo $client_id;?>" />

<tr>
    <td colspan="2">
      <table border="0" cellpadding = "1" cellspacing="1">
       <tr>
         <td align="right"><?php echo $AppUI->_('First Name');?>:</td>
         <td>
          <input type="text" class="text" size=25 name="contact_first_name" value="<?php echo @$contact->contact_first_name;?>" maxlength="50" />
         </td>
       </tr>
       <tr>
         <td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Last Name');?>:</td>
	 <td>
	    <input type="text" class="text" size=25 name="contact_last_name" value="<?php echo @$contact->contact_last_name;?>" maxlength="50" />
	 </td>
      </tr>
      <tr>
		<td align="left" valign="top"><?php echo $AppUI->_('Relationship to client');?>:</td>
		<td align="left" valign="top">
			<?php echo arraySelect($relativeTypes, "relative_type", 'class="text"', $relative->relative_type? $relative->relative_type :1); ?>
        </td>
	 </tr>	
     </table>
    </td>
</tr>
 <td valign="top" width="50%">
   <table border="0" cellpadding="1" cellspacing="1" class="details" width="100%">
	<tr>
		<td align="right"><?php echo $AppUI->_('Year of Birth');?>:</td>
		<td>
			<input type="text" class="text" name="relative_yob" value="<?php echo dPformSafe(@$relative->relative_yob);?>" size="30" maxlength="255" />
		</td>
	</tr>		
	<tr>
		<td align="right"><?php echo $AppUI->_('ID No');?>:</td>
		<td>
			<input type="text" class="text" name="relative_idno" value="<?php echo dPformSafe(@$relative->relative_idno);?>" size="30" maxlength="255" />
		</td>
	</tr>	
     <tr>
			<td align="right" width="100"><?php echo $AppUI->_('Address');?>1:</td>
			<td><input type="text" class="text" name="contact_address1" value="<?php echo @$contact->contact_address1;?>" maxlength="60" size="25" /></td>
     </tr>
     <tr>
			<td align="right"><?php echo $AppUI->_('Address');?>2:</td>
			<td><input type="text" class="text" name="contact_address2" value="<?php echo @$contact->contact_address2;?>" maxlength="60" size="25" /></td>
    </tr>
    <tr>
			<td align="right"><?php echo $AppUI->_('City');?>:</td>
			<td><input type="text" class="text" name="contact_city" value="<?php echo @$contact->contact_city;?>" maxlength="30" size="25" /></td>
   </tr>
  <tr>
			<td align="right" width="100"><?php echo $AppUI->_('Phone');?>:</td>
			<td>
				<input type="text" class="text" name="contact_phone" value="<?php echo @$contact->contact_phone;?>" maxlength="30" size="25" />
			</td>
		</tr>
		<tr>
			<td align="right"><?php echo $AppUI->_('Fax');?>:</td>
			<td>
				<input type="text" class="text" name="contact_fax" value="<?php echo @$contact->contact_fax;?>" maxlength="30" size="25" />
			</td>
		</tr>
		<tr>
			<td align="right"><?php echo $AppUI->_('Mobile Phone');?>:</td>
			<td>
				<input type="text" class="text" name="contact_mobile" value="<?php echo @$contact->contact_mobile;?>" maxlength="30" size="25" />
			</td>
		</tr>
		<tr>
			<td align="right" width="100"><?php echo $AppUI->_('Email');?>:</td>
			<td nowrap>
				<input type="text" class="text" name="contact_email" value="<?php echo @$contact->contact_email;?>" maxlength="255" size="25" />
			</td>
		</tr>
		<tr>
		<td align="left" valign="top"><?php echo $AppUI->_('Gender');?>:</td>
		<td align="left" valign="top">
			<?php echo arraySelectRadio($genderTypes, "relative_gender", 'onclick=toggleButtons()', $relative->relative_gender? $relative->relative_gender :1); ?>
        </td>
	 </tr>	

    
   </table>
   </td>
   	<td valign="top" width="50%">
		<strong><?php echo $AppUI->_('Contact Notes');?></strong><br />
		<textarea class="textarea" name="contact_notes" rows="20" cols="40"><?php echo @$relative->relative_notes;?></textarea></td>
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
