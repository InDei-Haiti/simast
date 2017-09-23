<?php
require_once ($AppUI->getModuleClass('clients'));
require_once ($AppUI->getSystemClass('genericTable'));

global $search_string;
global $owner_filter_id;
global $currentTabId;
global $currentTabName;
global $tabbed;
global $page;
global $type_filter;
global $city_filter;
global $orderby;
global $orderdir;
global $limit;
global $options;


$types = dPgetSysVal('CurriculumTypes');
$search = false;

$obj = new CTraining();


$allowedTrainings = $obj->getAllowedRecords($AppUI->user_id, 'training_id, training_name' );
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

$training_type_filter = $currentTabId;
//pager settings
if (strncmp($currentTabName, "Not Applicable", strlen("Not Applicable")) == 0)
	$training_type_filter = NULL;
	
$count = $obj->getCount($training_type_filter);

$where = $AppUI->getState( 'TrainingIdxWhere' ) ? $AppUI->getState( 'TrainingIdxWhere' ) : '%';

if ($where != '%') $search=true;


$trainingType = true;

if (strncmp($currentTabName,"All Training Activities", strlen("All Training Activities")) == 0)
	$trainingType = false;
if (strncmp($currentTabName,"Not Applicable", strlen("Not Applicable")) == 0)
	$training_type_filter = NULL;	

	$q = new DBQuery;
	
	//$q->setLimit($limit, $offset  );
	$q->addTable('trainings', 't');
	//$q->innerJoin('training_clients', 'ac', 'ac.training_clients_training_id = a.training_id');
	$q->addQuery('t.training_id, t.training_name, t.training_entry_date, t.training_date, t.training_clinic');
	//$q->addWhere("a.training_name LIKE '$where%'");

if (count($allowedActivities) > 0) { $q->addWhere('t.training_id IN (' . implode(',', array_keys($allowedActivities)) . ')'); }
if ((count($allowedClinics) > 0) && ($allowedClinics[0] <> NULL)) { $q->addWhere('t.training_clinic IN (' . implode(',', array_keys($allowedClinics)) . ')'); }

if ($trainingType) 
{
  if ($training_type_filter > 0)	
  { 
		//$q->addWhere('ci.counselling_clinic = '.$training_type_filter ); 
		//$q->addWhere('t.training_curriculum = '.$training_type_filter); 
  }
}

$q->addOrder($orderby.' '.$orderdir);

$sql = $q->prepare();

$qid = db_exec($sql);
$count = db_num_rows($qid);

$rows = $q->loadList();

$gt = new genericTable(true);

$headers = array(
	'Description'   => 'string',
	'Date Entered'  => 'date',
	'Center'        => 'string'
);

$gt->makeHeader($headers);
$decs = array(0=>'<a href = "/?m=training&a=view&training_id=##3##" title = "##4##" > ##0##</a >',1=>'date');

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

	$obj = new CTraining();
	$obj->load($row["training_id"]);
	
	$training_date = intval($obj->training_entry_date) ? new CDate($obj->training_entry_date ) :  null;
	$act_date = NULL;
	if (isset($training_date))
	{
			$act_date = $training_date->format($df);
	}

	$rd = array($obj->training_name, $obj->training_entry_date, $clinics[$obj->training_clinic],$obj->training_id,$obj->training_notes);
	$gt->fillBody($rd);

	$none = false;
}

if ($none){
	$gt->addTableHtmlRow($CR . '<tr><td colspan="8">' . $AppUI->_( 'No trainings available' ) . '</td></tr>');
}
$gt->compile();