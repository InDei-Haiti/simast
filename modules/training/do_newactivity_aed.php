<?php

require_once ($AppUI->getModuleClass('socialinfo'));
require_once ($AppUI->getModuleClass('counsellinginfo'));

$del = isset($_POST['del']) ? $_POST['del'] : 0;
$activity_num_rows = dPgetParam( $_POST, 'activity_num_rows', 0 );

//var_dump($activity_num_rows);
//var_dump($_POST);

$sub_form = isset($_POST['sub_form']) ? $_POST['sub_form'] : 0;
//social info

//handle new clients
$contact_unique_update = setItem("insert_id");

$activity = setItem("activity");

//print_r($_POST);
$activity_id  = $activity["activity_id"] ? $activity["activity_id"] : $_POST["activity_id"];


$del = setItem("del", 0);

$AppUI->setMsg('Group Activity');

$activityObj = new CActivity();



	// Include any files for handling module-specific requirements
	foreach (findTabModules('activity', 'addedit') as $mod) 
	{
		$fname = dPgetConfig('root_dir') . "/modules/$mod/activity_dosql.addedit.php";
		dprint(__FILE__, __LINE__, 3, "checking for $activity_name");
		if (file_exists($fname))
			require_once $fname;
	}
	
	// If we have an array of pre_save functions, perform them in turn.
	if (isset($pre_save)) 
	{
		foreach ($pre_save as $pre_save_function)
			$pre_save_function();
	} 
	else 
	{
		dprint(__FILE__, __LINE__, 1, "No pre_save functions.");
	}
	
	
	if ($del) 
	{
		//var_dump($activity_id);
		if (!$activityObj->load($activity_id))
		{
			$AppUI->setMsg( $activityObj->getError(), UI_MSG_ERROR );
			$AppUI->redirect();

		}
		//var_dump($activityObj);
		if (!$activityObj->canDelete( $msg ))
		{
			echo "cant delete";
			$AppUI->setMsg( $msg, UI_MSG_ERROR );
			$AppUI->redirect();
		}
		if (($msg = $activityObj->delete()))
		{
			$AppUI->setMsg( $msg, UI_MSG_ERROR );
			$AppUI->redirect();
		}
		else
		{
			$AppUI->setMsg( 'deleted', UI_MSG_ALERT, true );
			$AppUI->redirect( "index.php?m=activity" );
		}
	} 
	else
	{	
		
		if (!$activityObj->bind( $activity)) 
		{
			$AppUI->setMsg( $activityObj->getError(), UI_MSG_ERROR );
			$AppUI->redirect();
		}
		
		//var_dump($clientObj->client_entry_date);
		$entry_date = new CDate( $activity["activity_entry_date"] );
		$activityObj->activity_entry_date = $entry_date->format( FMT_DATETIME_MYSQL );

		$activity_date = new CDate( $activity["activity_date"] );
		$activityObj->activity_date = $activity_date->format( FMT_DATETIME_MYSQL );
	
		$end_date = new CDate( $activity["activity_end_date"] );
		$activityObj->activity_end_date = $end_date->format( FMT_DATETIME_MYSQL );
		
		if(is_array($_POST['activity_cadres']) && count($_POST['activity_cadres']) > 0){
			$activityObj->activity_cadres=implode(',',$_POST['activity_cadres']);
		}
		
		//var_dump($activity["activity_clients"]);
		//var_dump($activity["activity_contacts"]);
		if (($msg = $activityObj->store())) 
		{
			$AppUI->setMsg( $msg, UI_MSG_ERROR );
			$AppUI->redirect(); // Store failed don't continue?
		}
		else
		{
			
			if (!empty($activity["activity_clients"] ) )
			{
				//get array of clients
				$clientArray = explode (",",$activity["activity_clients"]);
				//var_dump($clientArray);
				//clear the existing clients
				$sql = " DELETE FROM activity_clients WHERE activity_clients_activity_id = $activityObj->activity_id";
				db_exec($sql);

				foreach ($clientArray as $client)
				{
					$sql = " INSERT INTO activity_clients (activity_clients_activity_id, activity_clients_client_id) VALUES ($activityObj->activity_id, $client)";
					//var_dump($sql);
					db_exec($sql);
				}
			
			}			
			if (!empty($activity["activity_contacts"] ) )
			{
				//get array of contacts
				$contactArray = explode (",",$activity["activity_contacts"]);
				//var_dump($contactArray);
				//clear the existing contacts
				$sql = " DELETE FROM activity_contacts WHERE activity_contacts_activity_id = $activityObj->activity_id";
				db_exec($sql);

				foreach ($contactArray as $contact)
				{
					$sql = " INSERT INTO activity_contacts (activity_contacts_activity_id, activity_contacts_contact_id) VALUES ($activityObj->activity_id, $contact)";
					//var_dump($sql);
					db_exec($sql);
				}
			
			}			
			if (!empty($activity["activity_caregivers"] ) )
			{
				//get array of contacts
				$caregiverArray = explode (",",$activity["activity_caregivers"]);
				//var_dump($contactArray);
				//clear the existing contacts
				$sql = " DELETE FROM activity_caregivers WHERE activity_caregivers_activity_id = $activityObj->activity_id";
				db_exec($sql);

				foreach ($caregiverArray as $caregiver)
				{
					$sql = " INSERT INTO activity_caregivers (activity_caregivers_activity_id, activity_caregivers_caregiver_id) VALUES ($activityObj->activity_id, $caregiver)";
					//var_dump($sql);
					db_exec($sql);
				}
			
			}
			
			if (($activity_num_rows > 0) && (!empty($_POST["facilitator_1"])))
			{
				$sql = 'DELETE FROM activity_facilitator WHERE facilitator_activity_id = ' . $activityObj->activity_id;
				db_exec($sql);
				
				
				for ($count = 1 ; $count < $activity_num_rows; $count++)
				{
					//var_dump($_POST["facilitator_activity_$count"]);
					$sql = " INSERT INTO activity_facilitator (facilitator_activity_id, facilitator_training_id,facilitator_topic, facilitator_name) 
					VALUES ($activityObj->activity_id, " . mysql_real_escape_string($_POST["trainingid_$count"]) . ",
								'" .  mysql_real_escape_string($_POST["topic_$count"  ]). "',
								'" .  mysql_real_escape_string($_POST["facilitator_$count"  ]). "')";
					//var_dump($sql);
					db_exec($sql);				
				}
			}	
		//handle status update

		if (isset($post_save)) 
		{
			foreach ($post_save as $post_save_function) 
			{
				$post_save_function();
			}
		}

		if ($notify) 
		{
			if ($msg = $activityObj->notify($comment)) 
			{
				$AppUI->setMsg( $msg, UI_MSG_ERROR );
			}
		}
		
		$AppUI->setMsg( $activity_id > 0 ? 'updated' : 'added', UI_MSG_OK, true );
		//$AppUI->setMsg(' added', UI_MSG_OK, true);	
		$AppUI->redirect('m=activity&a=view&activity_id='. $activityObj->activity_id);
	}
} // end of if subform
?>
