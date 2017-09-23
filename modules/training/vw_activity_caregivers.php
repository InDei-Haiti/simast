<?php
require_once ($AppUI->getModuleClass('contacts'));
require_once ($AppUI->getModuleClass('counsellinginfo'));
require_once ($AppUI->getModuleClass('caregivers'));

global $search_string;
global $activity_id;
global $owner_filter_id;
global $currentTabId;
global $currentTabName;
global $tabbed;
global $page;
global $type_filter;
global $city_filter;
global $caregiverorderby;
global $caregiverorderdir;
global $limit;
global $options;
global $perms;
//global $AppUI;


if (empty($caregiverorderby))
{
	$caregiverorderby = 'caregiver_fname';
}

if (empty($orderdir))
{
	$orderdir = 'asc';
}

//pager settings
$page = dPgetParam($_GET, 'page', 1);
$limit = intval($dPconfig['max_limit']);

$types = dPgetSysVal('CaregiverTypes');
$search = false;

$obj = new CCaregiver();


$allowedCaregivers = $obj->getAllowedRecords($AppUI->user_id, 'caregiver_id, caregiver_fname' );
if ($AppUI->user_type <> 1) //not admin user type
{
	//get allowed clinics if user is not an admin
	$q = new DBQuery();
	$q->addTable("users");
	$q->addQuery("users.user_clinics");
	$q->addWhere("users.user_id = " . $AppUI->user_id);
	$allowedClinics = $q->loadHashList();

}
$caregiver_type_filter = $currentTabId;
//pager settings
if (strncmp($currentTabName, "Not Applicable", strlen("Not Applicable")) == 0)
	$caregiver_type_filter = NULL;
if (strncmp($currentTabName, "Not Active", strlen("Not Active")) == 0)
	$caregiver_type_filter = 99;
	
//$count = $obj->getCount($AppUI->user_type, $AppUI->user_id,$client_type_filter);


$where = $AppUI->getState( 'CaregiverIdxWhere' ) ? $AppUI->getState( 'CaregiverIdxWhere' ) : '%';

if ($where != '%') $search=true;


$caregiverType = true;

if (strncmp($currentTabName,"All Caregivers", strlen("All Caregivers")) == 0)
	$caregiverType = false;

//if ($currentTabName == "Not Applicable")
//	$company_type_filter = 0;

	
    
if (isset($activity_id) && $activity_id > 0)
{
	$q = new DBQuery;
	
	//$q->setLimit($limit, $offset  );
	$q->addTable('caregiver_client', 'cc');
	$q->innerJoin('activity_caregivers', 'ac', 'ac.activity_caregivers_caregiver_id = cc.caregiver_id');
	$q->leftJoin('clients', 'c', 'c.client_id = cc.caregiver_client_id');
	$q->addQuery('cc.caregiver_id, cc.caregiver_fname, cc.caregiver_lname, c.client_adm_no, c.client_first_name, c.client_last_name,c.client_id');
	$q->addWhere("cc.caregiver_fname LIKE '$where%'");

	if (count($allowedCaregivers) > 0) { $q->addWhere('cc.caregiver_id IN (' . implode(',', array_keys($allowedCaregivers)) . ')'); }


	
	$q->addWhere('ac.activity_caregivers_activity_id = ' . $activity_id);
	$q->addOrder($caregiverorderby.' '.$caregiverorderdir);
	
	$num_pages = ceil ($count / $limit);
	$offset = ($page - 1) * $limit;
	//var_dump($num_pages);
	if ($offset < 0)
	{
		$limit = intval($count);
		$offset = 0;
	}
	//var_dump($count);
	$q->setLimit($limit, $offset  );
	$sql = $q->prepare();

	//var_dump($sql);
	$qid = db_exec($sql);

	$count = db_num_rows($qid);
	$rows = $q->loadList();
}	





//echo printPageNavigation( '?m=companies', $page, $num_pages, $offset, $limit, $count);
if ($count > 0)
{
	echo printPageNavigation( '?m=activity', $page, $num_pages, $offset, $limit, $count, 'caregivers');
}

?>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<tr>
	<th nowrap="nowrap">
		<?php echo $AppUI->_('Client Adm/No');?>
	</th>	
	<th nowrap="nowrap">
		<?php echo $AppUI->_('Client Name');?>
	</th>	
	<th nowrap="nowrap">
		<?php echo $AppUI->_('Caregiver Name');?>
	</th>	
    
</tr>
<?php
	$s = '';
	$CR = "\n"; // Why is this needed as a variable?
	$none = true;
	$df = $AppUI->getPref('SHDATEFORMAT');

if (!empty($rows))	
{	foreach ($rows as $row)
	{

	    $obj = new CCaregiver();	
		$obj->load($row["caregiver_id"]);
		$none = false;
		$s .= $CR . '<tr>';
		$s .= $CR . '<td nowrap="nowrap"><a href="index.php?m=clients&a=view&client_id=' . $row["client_id"] .'">'. $row["client_adm_no"] . '</a></td>';
		$s .= $CR . '<td nowrap="nowrap"><a href="index.php?m=clients&a=view&client_id=' . $row["client_id"] .'">'. $row["client_first_name"] . "&nbsp;" .  $row["client_last_name"] . '</a></td>';
		$s .= $CR . '<td nowrap="nowrap">' . $obj->caregiver_fname . ' ' . $obj->caregiver_lname . '</td>';
		$s .= $CR . '</tr>';

	}
}
echo "$s\n";
if ($none)
{
	echo $CR . '<tr><td colspan="8">' . $AppUI->_( 'No caregivers selected' ) . '</td></tr>';
}
?>
</table>
<?php
if ($count > 0)
{
   echo printPageNavigation( '?m=activity', $page, $num_pages, $offset, $limit, $count, 'caregivers');
}
?>
