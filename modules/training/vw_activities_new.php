<?php
require_once ($AppUI->getModuleClass('clients'));
require_once ($AppUI->getSystemClass("genericTable"));
//require_once ($AppUI->getModuleClass('activity'));
//require_once("activity.class.php");
global $search_string;
global $owner_filter_id;
global $currentTabId;
global $currentTabName;
global $tabbed;
global $page;
global $type_filter;
global $city_filter;
global $orderby_act;
global $orderdir_act;
global $limit;
global $options;


$types = dPgetSysVal('CurriculumTypes');
$search = false;

$obj = new CActivity();


$allowedActivities = $obj->getAllowedRecords($AppUI->user_id, 'activity_id, activity_description' );
if ($AppUI->user_type <> 1) //not admin user type
{
	//get allowed clinics if user is not an admin
	$q = new DBQuery();
	$q->addTable("users");
	$q->addQuery("users.user_clinics");
	$q->addWhere("users.user_id = " . $AppUI->user_id);
	$allowedClinics= $q->loadHashList();

}
//load clinics
$q = new DBQuery();
$q->addTable('clinics', 'c');
$q->addQuery('c.clinic_id, c.clinic_name');
$q->addOrder('c.clinic_name');
$clinics = $q->loadHashList();

$activity_type_filter = $currentTabId;
//pager settings
if (strncmp($currentTabName, "Not Applicable", strlen("Not Applicable")) == 0)
	$activity_type_filter = NULL;
	
$count = $obj->getCount($activity_type_filter);

$where = $AppUI->getState( 'ActivityIdxWhere' ) ? $AppUI->getState( 'ActivityIdxWhere' ) : '%';

if ($where != '%') $search=true;


$activityType = true;

if (strncmp($currentTabName,"All Activities", strlen("All Activities")) == 0)
	$activityType = false;
if (strncmp($currentTabName,"Not Applicable", strlen("Not Applicable")) == 0)
	$activity_type_filter = NULL;	

	$q = new DBQuery;
	
	//$q->setLimit($limit, $offset  );
	$q->addTable('activity', 't');
	//$q->innerJoin('activity_clients', 'ac', 'ac.activity_clients_activity_id = a.activity_id');
	$q->addQuery('t.activity_id,t.activity_cadres, t.activity_description, t.activity_entry_date, t.activity_date, t.activity_clinic, t.activity_male_count + t.activity_female_count as activity_count');
	//$q->addWhere("a.activity_name LIKE '$where%'");

if (count($allowedActivities) > 0) { $q->addWhere('t.activity_id IN (' . implode(',', array_keys($allowedActivities)) . ')'); }
if ((count($allowedClinics) > 0) && ($allowedClinics[0] <> NULL)) { $q->addWhere('t.activity_clinic IN (' . implode(',', array_keys($allowedClinics)) . ')'); }

if ($activityType) 
{
  if ($activity_type_filter > 0)	
  { 
		//$q->addWhere('ci.counselling_clinic = '.$activity_type_filter ); 
		$q->addWhere('t.activity_curriculum = '.$activity_type_filter); 
  }
}

//$q->addOrder($orderby_act.' '.$orderdir_act);

$sql = $q->prepare();

$qid = db_exec($sql);
$count = db_num_rows($qid);

$rows = $q->loadList();

$q = new DBQuery();
$q->addTable('trainings', 'c');
$q->addQuery('c.training_id, c.training_name');
$q->addOrder('c.training_name');
$activityOptions = $q->loadHashList();


//load activities for this group activity

$q1 = new DBQuery();
$q1->addTable("activity_facilitator");
$q1->addQuery("activity_facilitator.*");
	

$gt = new genericTable(true);

$header = array(
	'Activity Name'     => 'string',
	'Cadres Trained'    => 'string',
	'Start Date'        => 'date',
	'Trainings'         => 'string',
	'Topics'            => 'string',
	'Total Attended'    => 'int'
);

$decs = array(0=>'<a href="/?m=training&a=view_activity&activity_id=##6##">##0##</a>',2=>'date');

$gt->makeHeader($header, array(3,4) );

$gt->setDecorators($decs);

$s = '';
$CR = "\n"; // Why is this needed as a variable?
$none = true;
$df = $AppUI->getPref('SHDATEFORMAT');
	$q  = new DBQuery;
	$q->addTable('clinic_location');
	$q->addQuery('clinic_location.clinic_location_id, clinic_location.clinic_location');
	$locations = $q->loadHashList();
	
foreach ($rows as $rid => $row)
{

    //$obj->reset;	
	$obj = new CActivity();
	$obj->load($row["activity_id"]);
	
	$activity_date = intval($obj->activity_date) ? new CDate($obj->activity_date ) :  null;
	$act_date = NULL;
	if (isset($activity_date))
	{
			$act_date = $activity_date->format($df);
	}
	$q2= clone $q1;
	$q2->addWhere("activity_facilitator.facilitator_activity_id = " . $row['activity_id']);
	$activities = $q2->loadList();
	
	$colz=array('trains'=>array(),'topics'=>array());
	
	foreach ($activities as $zrow){
		if($zrow["facilitator_training_id"] !== null && (int)$zrow["facilitator_training_id"] > 0  ){
			$colz['trains'][]=$activityOptions[$zrow["facilitator_training_id"]];
		} 
		if($zrow['facilitator_topic'] !== null){
			$colz['topics'][]=$zrow['facilitator_topic'];
		}
	}

	$colz['topics'] = array_merge(array(),array_unique($colz['topics']));
	$colz['trains'] = array_merge(array(), array_unique($colz['trains']));
	
	$none = false;

	$rd = array($obj->activity_description,
				buildStringVals(dPgetSysVal("CadresTrained"),$row['activity_cadres']),
				$obj->activity_date,
				$colz['trains'],
				$colz['topics'],
				$row['activity_count'],
				$obj->activity_id
	);

	$gt->fillBody($rd);

}

if ($none)
{
	$gt->addTableHtmlRow($CR . '<tr><td colspan="8">' . $AppUI->_( 'No activities available' ) . '</td></tr>');
}

$gt->compile();