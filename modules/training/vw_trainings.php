<?php
require_once ($AppUI->getModuleClass('clients'));

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

$num_pages = ceil ($count / $limit);
$offset = ($page - 1) * $limit;
//var_dump($num_pages);
if ($offset < 0)
{
	$limit = intval($count);
	$offset = 0;
}

$where = $AppUI->getState( 'TrainingIdxWhere' ) ? $AppUI->getState( 'TrainingIdxWhere' ) : '%';

if ($where != '%') $search=true;


$trainingType = true;

if (strncmp($currentTabName,"All Training Activities", strlen("All Training Activities")) == 0)
	$trainingType = false;
if (strncmp($currentTabName,"Not Applicable", strlen("Not Applicable")) == 0)
	$training_type_filter = NULL;	

	
    
	
	//var_dump($limit);
	//var_dump($offset);
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

//var_dump($sql);
$qid = db_exec($sql);
$count = db_num_rows($qid);

//var_dump($count);
$num_pages = ceil ($count / $limit);

$offset = ($page - 1) * $limit;

if ($offset < 0)
{
	$limit = intval($count);
	$offset = 0;
}


$q->setLimit($limit, $offset  );

$rows = $q->loadList();
//var_dump($rows);

//echo printPageNavigation( '?m=companies', $page, $num_pages, $offset, $limit, $count);
echo printPageNavigation( '?m=training', $page, $num_pages, $offset, $limit, $count, 'trainings');

?>


<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<tr>
	<th nowrap="nowrap">
		<a href="?m=training&orderby=training_name" class="hdr"><?php echo $AppUI->_('Description');?></a>
	</th>	
	<th nowrap="nowrap">
		<a href="?m=training&orderby=training_entry_date" class="hdr"><?php echo $AppUI->_('Date Entered');?></a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=training&orderby=training_clinic" class="hdr"><?php echo $AppUI->_('Center');?></a>
	</th>
	<!--<th nowrap="nowrap">
		<a href="?m=training&orderby=training_curriculum" class="hdr"><?php echo $AppUI->_('Curriculum');?></a>
	</th>-->

</tr>
<?php
$s = '';
$CR = "\n"; // Why is this needed as a variable?
$none = true;
$df = $AppUI->getPref('SHDATEFORMAT');
	$q  = new DBQuery;
	$q->addTable('clinic_location');
	$q->addQuery('clinic_location.clinic_location_id, clinic_location.clinic_location');
	$locations = $q->loadHashList();
	
foreach ($rows as $row)
{

    //$obj->reset;	
	$obj = new CTraining();
	$obj->load($row["training_id"]);
	
	$training_date = intval($obj->training_entry_date) ? new CDate($obj->training_entry_date ) :  null;
	$act_date = NULL;
	if (isset($training_date))
	{
			$act_date = $training_date->format($df);
	}
	
	
	$none = false;
	$s .= $CR . '<tr>';
	$s .= $CR . '<td nowrap="nowrap"><a href="'. $obj->getUrl('view')   .'&training_id=' . $obj->training_id . '" title="'.$obj->training_notes.'">' . $obj->training_name .'</a></td>';
	$s .= $CR . '<td align="center" nowrap="nowrap">' . $act_date . '</td>';
	$s .= $CR . '<td align="center" nowrap="nowrap">' . $clinics[$obj->training_clinic] . '</td>';
	//$s .= $CR . '<td align="center" nowrap="nowrap">' . $types[$obj->training_curriculum]  . '</td>';		
	$s .= $CR . '</tr>';
}
//var_dump($obj);
echo "$s\n";
if ($none)
{
	echo $CR . '<tr><td colspan="8">' . $AppUI->_( 'No trainings available' ) . '</td></tr>';
}
?>
</table>
<?php
   echo printPageNavigation( '?m=training', $page, $num_pages, $offset, $limit, $count, 'trainings');
?>
