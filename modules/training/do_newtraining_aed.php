<?php

require_once ($AppUI->getModuleClass('socialinfo'));
require_once ($AppUI->getModuleClass('counsellinginfo'));
$del = isset($_POST['del']) ? $_POST['del'] : 0;


$sub_form = isset($_POST['sub_form']) ? $_POST['sub_form'] : 0;
//social info
$socialinfo = setItem("social");
//counselling info
$counsellinginfo = setItem("counselling");

//handle new clients
$contact_unique_update = setItem("insert_id");

$training = setItem("training");

//print_r($_POST);
$training_id  = setItem("training_id", 0);


$del = setItem("del", 0);

$AppUI->setMsg('Training');

$trainingObj = new CTraining();



	// Include any files for handling module-specific requirements
	foreach (findTabModules('training', 'addedit') as $mod) 
	{
		$fname = dPgetConfig('root_dir') . "/modules/$mod/training_dosql.addedit.php";
		dprint(__FILE__, __LINE__, 3, "checking for $training_name");
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
		if (!$trainingObj->load($training_id))
		{
			$AppUI->setMsg( $trainingObj->getError(), UI_MSG_ERROR );
			$AppUI->redirect();

		}
		if (!$trainingObj->canDelete( $msg ))
		{
			$AppUI->setMsg( $msg, UI_MSG_ERROR );
			$AppUI->redirect();
		}
		if (($msg = $trainingObj->delete()))
		{
			$AppUI->setMsg( $msg, UI_MSG_ERROR );
			$AppUI->redirect();
		}
		else
		{
			$AppUI->setMsg( 'deleted', UI_MSG_ALERT, true );
			$AppUI->redirect( "index.php?m=training" );
		}
	} 
	else
	{	
		
		if (!$trainingObj->bind( $training)) 
		{
			$AppUI->setMsg( $trainingObj->getError(), UI_MSG_ERROR );
			$AppUI->redirect();
		}
		
		//var_dump($clientObj->client_entry_date);
		$entry_date = new CDate( $training["training_entry_date"] );
		$trainingObj->training_entry_date = $entry_date->format( FMT_DATETIME_MYSQL );

		$training_date = new CDate( $training["training_date"] );
		$trainingObj->training_entry_date = $training_date->format( FMT_DATETIME_MYSQL );
	
		
		if (($msg = $trainingObj->store())) 
		{
			$AppUI->setMsg( $msg, UI_MSG_ERROR );
			$AppUI->redirect(); // Store failed don't continue?
		}
		else
		{
			
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
			if ($msg = $trainingObj->notify($comment)) 
			{
				$AppUI->setMsg( $msg, UI_MSG_ERROR );
			}
		}
		$AppUI->setMsg( @$training['training_id'] ? 'updated' : 'added', UI_MSG_OK, true );
		//$AppUI->setMsg(' added', UI_MSG_OK, true);	
		$AppUI->redirect('m=training&a=view&training_id='. $trainingObj->training_id);
	}
} // end of if subform
?>
