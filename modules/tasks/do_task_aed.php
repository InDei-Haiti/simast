<?php /* TASKS $Id: do_task_aed.php 5731 2008-06-06 23:02:31Z merlinyoda $ */
if (! defined ( 'DP_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

function setItem1($item_name, $tar, $defval = null) {
	return ((isset ( $tar [$item_name] )) ? $tar [$item_name] : $defval);
}

global $AppUI;


$var = false;
$all_subs = array ();
$total_act = array(0 => array());
$scnt = 0;
foreach ( $_POST as $key => $val ) {
	if (strstr ( "sub_", $key )) {
		$vard = preg_match ( "/sub_(\d{1,})/", $key, $var );
		if ($vard) {
			$all_subs [$var [1]] ['task_mode'] = "sub";
			$var [2] = str_replace ( "sub_" . $var [1] . "_", "", $key );
			if (! is_array ( $all_subs [$var [1]] )) {
				$all_subs [$var [1]] = array ();
			}
			if ($key == "sub_" . $var [1]) {
				//this is real id of exisiting subactivity
				//$all_subs [$scnt] = $tobj->load ( $var [1] );
				//$cnt ++;
				$all_subs [$var [1]] ['task_id'] = $val;
			} elseif ($var [2] != "") {
				//store in array all subactivites data post from client
				if (preg_match ( "/b_task_admin/", $var [2] )) {
					$var [2] = str_replace ( "b_", "", $var [2] );
					if ($all_subs [$var [1]] [$var [2]] == 0) {
						$can = true;
					} else {
						$can = false;
					}
				} else {
					$can = true;
				}
				
				if ($can) {
					$all_subs [$var [1]] [$var [2]] = $val;
				}
			}
		}
	}else{
		$total_act[0][$key]=$val;
	}
}
//$total_act = array ($_POST );
foreach ( $all_subs as $asf ) {
	$total_act [] = $asf;
}

$parent_task = ( int ) $_POST ['task_id'];

$q = new DBQuery();
$q->addTable('contacts');
$q->addQuery('contact_company');
$q->addWhere('contact_id="'.$AppUI->user_contact.'"');
$uCompany = $q->loadResult();
$task_company=(int)$uCompany;


foreach ( $total_act as $tact ) {
	
	$adjustStartDate =setItem1 ( 'set_task_start_date', $tact );
	$del = isset ( $_POST ['del'] ) ? $_POST ['del'] : 0;
	$task_id = setItem1 ( 'task_id', $tact, 0 );
	$hassign = setItem1 ( 'hassign', $tact );
	$hperc_assign = setItem1( 'hperc_assign', $tact );
	$hdependencies = setItem1 ( 'hdependencies', $tact );
	$notify = setItem1 ( 'task_notify', $tact, 0 );
	$comment = setItem1 ( 'email_comment', $tact, '' );
	$sub_form = isset ( $_POST ['sub_form'] ) ? $_POST ['sub_form'] : 0;
	
	if ($sub_form) {
		// in add-edit, so set it to what it should be
		$AppUI->setState ( 'TaskAeTabIdx', $_POST ['newTab'] );
		if (isset ( $_POST ['subform_processor'] )) {
			$mod = ((isset ( $_POST ['subform_module'] )) ? $AppUI->checkFileName ( $_POST ['subform_module'] ) : 'tasks');
			$proc = $AppUI->checkFileName ( $_POST ['subform_processor'] );
			include (DP_BASE_DIR . '/modules/' . $mod . '/' . $proc . '.php');
		}
	} else {
		
		// Include any files for handling module-specific requirements
		foreach ( findTabModules ( 'tasks', 'addedit' ) as $mod ) {
			$fname = (DP_BASE_DIR . '/modules/' . $mod . '/tasks_dosql.addedit.php');
			dprint ( __FILE__, __LINE__, 3, ('checking for ' . $fname) );
			if (file_exists ( $fname )) {
				require_once $fname;
			}
		}
		
		$obj = new CTask ( );
		
		// If we have an array of pre_save functions, perform them in turn.
		if (isset ( $pre_save )) {
			foreach ( $pre_save as $pre_save_function ) {
				$pre_save_function ();
			}
		} else {
			dprint ( __FILE__, __LINE__, 2, 'No pre_save functions.' );
		}
		
		// Find the task if we are set
		$task_end_date = null;
		if ($task_id && $tact ['task_mode'] != "sub") {
			$obj->load ( $task_id );
			$task_end_date = new CDate( web2dbDate( $obj->task_end_date ));
			$we_do = "root";
		} else if ($parent_task > 0) { // ($_POST ['task_mode'] == "sub" || $obj->task_mode == "sub") {
			$we_do = "sub";
			/*if (( int ) $_POST ['task_parent'] > 0) {*/
			$obj->load ( $parent_task );
			//}
			if ($tact ['task_id'] == 0 && $obj->task_id > 0) {
				$obj->task_id = 0;
			}
		}

		if ($we_do == "root") {
			if ($tact ['task_start_date'] === '') {
				$tact ['task_start_date'] = '000000000000';
			}
			if ($tact ['task_end_date'] === '') {
				$tact ['task_end_date'] = '000000000000';
			}
		}
		if (isset ( $tact ) && ! ($obj->bind ( $tact ))) {
			$AppUI->setMsg ( $obj->getError (), UI_MSG_ERROR );
			$AppUI->redirect ();
		}
		
		
		
		// Check to see if the task_project has changed
		if (isset ( $tact ['new_task_project'] ) && $tact ['new_task_project'] && ($obj->task_project != $tact ['new_task_project'])) {
			$obj->task_project = $tact ['new_task_project'];
			$obj->task_parent = $obj->task_id;
		}
		//var_dump($_POST ['task_sector']);
		//exit();
		if (is_array ( $_POST ['task_sector'] ) && count ( $_POST ['task_sector'] ) > 0) {
			$obj->task_sector = implode ( ',', $_POST ['task_sector'] );
		}else{
			$obj->task_sector = '';
		}
		
		if (is_array ( $_POST ['task_agency'] ) && count ( $_POST ['task_agency'] ) > 0) {
			$obj->task_agency = implode ( ',', $_POST ['task_agency'] );
		}else{
			$obj->task_agency = '';
		}
		
		// Map task_dynamic checkboxes to task_dynamic values for task dependencies.
		if ($obj->task_dynamic != 1) {
			$task_dynamic_delay = setItem1 ( 'task_dynamic_nodelay', $tact, '0' );
			if (in_array ( $obj->task_dynamic, $tracking_dynamics )) {
				$obj->task_dynamic = $task_dynamic_delay ? 21 : 31;
			} else {
				$obj->task_dynamic = $task_dynamic_delay ? 11 : 0;
			}
		}
		
		// Let's check if task_dynamic is unchecked
		if (! (array_key_exists ( 'task_dynamic', $tact ))) {
			$obj->task_dynamic = false;
		}
		
		// Make sure task milestone is set or reset as appropriate
		if (! (isset ( $tact ['task_milestone'] ))) {
			$obj->task_milestone = false;
		}
		
		
		if(strlen($obj->task_description) > 1000){
			$obj->task_description=substr($obj->task_description,0,1000);
		}
		
		//format hperc_assign user_id=percentage_assignment;user_id=percentage_assignment;user_id=percentage_assignment;
		$tmp_ar = explode ( ';', $hperc_assign );
		$hperc_assign_ar = array ();
		for($i = 0, $xi = sizeof ( $tmp_ar ); $i < $xi; $i ++) {
			$tmp = explode ( '=', $tmp_ar [$i] );
			$hperc_assign_ar [$tmp [0]] = ((count ( $tmp ) > 1) ? $tmp [1] : 100);
		}
		
		
		// let's check if there are some assigned contacts to task
		//$obj->task_contacts = implode(',', setItem1('contact_ids', array()));
		

		// convert dates to SQL format first
		if ($obj->task_start_date) {
			$date = new CDate (web2dbDate( $obj->task_start_date ));
			$obj->task_start_date = $date->format ( FMT_DATETIME_MYSQL );
		}
		$end_date = null;
		if ($obj->task_end_date) {
			if (strpos ( $obj->task_end_date, '2400' ) !== false) {
				$obj->task_end_date = str_replace ( '2400', '2359', $obj->task_end_date );
			}
			$end_date = new CDate ( web2dbDate($obj->task_end_date ));
			$obj->task_end_date = $end_date->format ( FMT_DATETIME_MYSQL );
		}
		
		$tact['task_target_budget']=preg_replace("/[^\d\.]/",'',$tact['task_target_budget']);
		$tact['task_beneficiares']=preg_replace("/[^\d\.]/",'',$tact['task_beneficiares']);
		$obj->task_type_of_beneficiery = implode(',', $tact['task_type_of_beneficiery']);
		
		if (is_array ( $_POST ['task_type_of_beneficiery'] ) && count ( $_POST ['task_type_of_beneficiery'] ) > 0) {
			$obj->task_type_of_beneficiery = implode ( ',', $_POST ['task_type_of_beneficiery'] );
		}else{
			$obj->task_type_of_beneficiery = '';
		}
		
		$tact['task_quantity']=preg_replace("/[^\d\.]/",'',$tact['task_quantity']);
		
		require_once ($AppUI->getSystemClass ( 'CustomFields' ));
		
		
		
		// prepare (and translate) the module name ready for the suffix
		if ($del) {
			$q = new DBQuery();
			$q->addTable('form_master');
			$q->addQuery('COUNT(*)');
			$q->addWhere('task_id='.$obj->task_id);
			$countproj = $q->loadResult();
			if($countproj > 0){
				$AppUI->setMsg ( 'You can\'t delete this activity, it contains forms', UI_MSG_ERROR );
				$AppUI->redirect ();
			}
			
			/*$q = new DBQuery();
			$q->addTable('beneficieries');
			$q->addQuery('COUNT(*)');
			$q->addWhere('task_id='.$obj->task_id);
			$countproj = $q->loadResult();
			if($countproj > 0){
				$AppUI->setMsg ( 'You can\'t delete this activity, it contains beneficieries', UI_MSG_ERROR );
				$AppUI->redirect ();
			}*/
			
			if (($msg = $obj->delete ($tact['contdel']))) {
				if($suppressHeaders){
					print $msg;
					exit;
				}else{
					$AppUI->setMsg ( $msg, UI_MSG_ERROR );
					$AppUI->redirect ();
				}
			} else {
				if($suppressHeaders){
					print 'ok';
					exit ;
				}else{
					$AppUI->setMsg ( 'Activity deleted' );
					$AppUI->redirect ( '', - 1 );
				}
			}
		} else {
			if (($msg = $obj->store ())) {				
				$AppUI->setMsg ( $msg, UI_MSG_ERROR );
				$AppUI->redirect (); // Store failed don't continue?
			} else {
				if($obj->task_mode == "root" && $task_id == 0 && $obj->task_id > 0){
					$parent_task=$obj->task_id;
				}

				$custom_fields = New CustomFields ( $m, 'addedit', $obj->task_id, 'edit' );
				$custom_fields->bind ( $tact );
				$sql = $custom_fields->store ( $obj->task_id ); // Store Custom Fields
                updateProjectTotals($obj->task_project);

				// Now add any task reminders
				// If there wasn't a task, but there is one now, and
				// that task date is set, we need to set a reminder.
				if (empty ( $task_end_date ) || (! (empty ( $end_date )) && $task_end_date->dateDiff ( $end_date ))) {
					$obj->addReminder ();
				}
				$AppUI->setMsg ( $task_id ? 'Activity updated' : 'Activity added', UI_MSG_OK );
			}
			
			if (isset ( $hassign )) {
				$obj->updateAssigned ( $hassign, $hperc_assign_ar );
			}
			
			if (isset ( $hdependencies )) { // && !empty($hdependencies)) 
				// there are dependencies set!
				

				// backup initial start and end dates
				$tsd = new CDate ( $obj->task_start_date );
				$ted = new CDate ( $obj->task_end_date );
				
				// updating the table recording the 
				// dependency relations with this task
				$obj->updateDependencies ( $hdependencies );
				
				// we will reset the task's start date based upon dependencies
				// and shift the end date appropriately
				if ($adjustStartDate && ! is_null ( $hdependencies )) {
					
					// load already stored task data for this task
					$tempTask = new CTask ( );
					$tempTask->load ( $obj->task_id );
					
					// shift new start date to the last dependency end date
					$nsd = new CDate ( $tempTask->get_deps_max_end_date ( $tempTask ) );
					
					// prefer Wed 8:00 over Tue 16:00 as start date
					$nsd = $nsd->next_working_day ();
					
					// prepare the creation of the end date
					$ned = new CDate ( );
					$ned->copy ( $nsd );
					
					if (empty ( $obj->task_start_date )) {
						// appropriately calculated end date via start+duration
						$ned->addDuration ( $obj->task_duration, $obj->task_duration_type );
					} else {
						// calc task time span start - end
						$d = $tsd->calcDuration ( $ted );
						
						// Re-add (keep) task time span for end date.
						// This is independent from $obj->task_duration.
						// The value returned by Date::Duration() is always in hours ('1') 
						$ned->addDuration ( $d, '1' );
					}
					
					// prefer tue 16:00 over wed 8:00 as an end date
					$ned = $ned->prev_working_day ();
					
					$obj->task_start_date = $nsd->format ( FMT_DATETIME_MYSQL );
					$obj->task_end_date = $ned->format ( FMT_DATETIME_MYSQL );
					
					$q = new DBQuery ( );
					$q->addTable ( 'tasks', 't' );
					$q->addUpdate ( 'task_start_date', $obj->task_start_date );
					$q->addUpdate ( 'task_end_date', $obj->task_end_date );
					$q->addWhere ( 'task_id = ' . $obj->task_id );
					$q->addWhere ( 'task_dynamic != 1' );
					$q->exec ();
					$q->clear ();
				}
			}
			
			// If there is a set of post_save functions, then we process them
			if (isset ( $post_save )) {
				foreach ( $post_save as $post_save_function ) {
					$post_save_function ();
				}
			}
			
			if ($notify && $msg = $obj->notify ( $comment )) {
				$AppUI->setMsg ( $msg, UI_MSG_ERROR );
			}
		
		}
	
	} // end of if subform
} // end of global iterate over root activitry and its subs
$AppUI->redirect ();
?>
