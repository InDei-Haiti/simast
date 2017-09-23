<?php /* RELATIVE $Id: do_caregiver_aed.php,v 1.13.10.1 2005/08/10 07:38:41 ajdonnison Exp $ */
include $AppUI->getModuleClass('contacts');
$del = isset($_REQUEST['del']) ? $_REQUEST['del'] : FALSE;

$obj = new CRelative();
$contact = new CContact();

if (!$obj->bind( $_POST )) {
	$AppUI->setMsg( $obj->getError(), UI_MSG_ERROR );
	$AppUI->redirect();
}
if (!$contact->bind( $_POST )) {
	$AppUI->setMsg( $contact->getError(), UI_MSG_ERROR );
	$AppUI->redirect();
}
        

// prepare (and translate) the module name ready for the suffix
$AppUI->setMsg( 'Relative ' );

// !Caregivers's contact information not deleted - left for history.
if ($del) {
	if (($msg = $obj->delete())) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
		$AppUI->redirect();
	} else {
		$AppUI->setMsg( "deleted", UI_MSG_ALERT, true );
		$AppUI->redirect();
	}
	return;
}
	
    if (($msg = $contact->store())) 
	{
                $AppUI->setMsg( $msg, UI_MSG_ERROR );
    }
	else 
	{
		$isNotNew = @$_POST['contact_id'];
		$isNewClient = @$_POST['contact_unique_update'];
		$client_id = @$_REQUEST['client_id'];
		
		$obj->relative_contact = $contact->contact_id;
		if (($msg = $obj->store())) 
		{
			$AppUI->setMsg( $msg, UI_MSG_ERROR );
		}
		
		//store the contact  types.
		$relative_type = dPgetParam($_POST, 'relative_type', NULL);
		
		if (($client_id <= 0) && ($contact_unique_update > 0))
		{
			$client_id = $contact_unique_update; 
		}
		
		if (isset($relative_type))
		{
			$sql = 'DELETE FROM client_contacts WHERE client_contacts_contact_id = ' . $obj->relative_contact . ' AND client_contacts_client_id = \'' . $client_id . '\'';
			if (!$ret = db_exec($sql))
			{
			   $AppUI->setMsg($msg, 'delete::update of roles failed');
			}

			$sql = 'DELETE FROM relative_client WHERE relative_client_relative_id = ' . $obj->relative_id . ' AND relative_client_client_id = \'' . $client_id . '\'';
			if (!$ret = db_exec($sql))
			{
			   $AppUI->setMsg($msg, 'delete::update of roles failed');
			}
			
		
				$sql = "INSERT INTO client_contacts(client_contacts_contact_id, client_contacts_client_id, client_contacts_contact_type) VALUES ( $obj->relative_contact,'$client_id', $relative_type)";  
				
				if (!$ret = db_exec($sql))
				{
					$AppUI->setMsg($msg, 'insert::update of roles failed');
				}
				$sql = "INSERT INTO relative_client(relative_client_relative_id, relative_client_client_id, relative_client_relative_type) VALUES ( $obj->relative_id,'$client_id', $relative_type)";  
				
				if (!$ret = db_exec($sql))
				{
					$AppUI->setMsg($msg, 'insert::update of roles failed');
				}


			

		}
		$AppUI->setMsg( $isNotNew ? 'updated' : 'added', UI_MSG_OK, true );        
     
    }
	$AppUI->redirect();

?>
