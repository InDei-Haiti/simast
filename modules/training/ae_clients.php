<?php
require_once ($AppUI->getModuleClass('clients'));
require_once ($AppUI->getModuleClass('counsellinginfo'));
require_once ($AppUI->getModuleClass('admission'));

global $search_string;
global $activity_id;
global $owner_filter_id;
global $currentTabId;
global $currentTabName;
global $tabbed;
global $page;
global $type_filter;
global $city_filter;
global $clientorderby;
global $clientorderdir;
global $limit;
global $options;
global $perms;
//global $AppUI;


if (empty($clientorderby))
{
	$clientorderby = 'client_first_name';
}

if (empty($clientorderdir))
{
	$clientorderdir = 'asc';
}

//pager settings
$page = dPgetParam($_GET, 'page', 1);
$limit = intval($dPconfig['max_limit']);

$types = dPgetSysVal('ClientStatus');
$search = false;

$obj = new CClient();


$allowedClients = $obj->getAllowedRecords($AppUI->user_id, 'client_id, client_first_name' );
if ($AppUI->user_type <> 1) //not admin user type
{
	//get allowed clinics if user is not an admin
	$q = new DBQuery();
	$q->addTable("users");
	$q->addQuery("users.user_clinics");
	$q->addWhere("users.user_id = " . $AppUI->user_id);
	$allowedClinics = $q->loadHashList();

}
$client_type_filter = $currentTabId;
//pager settings
if (strncmp($currentTabName, "Not Applicable", strlen("Not Applicable")) == 0)
	$client_type_filter = NULL;
if (strncmp($currentTabName, "Not Active", strlen("Not Active")) == 0)
	$client_type_filter = 99;
	
//$count = $obj->getCount($AppUI->user_type, $AppUI->user_id,$client_type_filter);


$where = $AppUI->getState( 'ClientIdxWhere' ) ? $AppUI->getState( 'ClientIdxWhere' ) : '%';

if ($where != '%') $search=true;


$clientType = true;

if (strncmp($currentTabName,"All Clients", strlen("All Clients")) == 0)
	$clientType = false;
if (strncmp($currentTabName,"Not Applicable", strlen("Not Applicable")) == 0)
	$client_type_filter = NULL;	
if (strncmp($currentTabName,"Not Active", strlen("Not Active")) == 0)
	$client_type_filter = 99;	
//if ($currentTabName == "Not Applicable")
//	$company_type_filter = 0;

	
    
if (isset($activity_id) && $activity_id > 0)
{
	$q = new DBQuery;
	
	//$q->setLimit($limit, $offset  );
	$q->addTable('clients', 'c');
	$q->innerJoin('counselling_info', 'ci', 'ci.counselling_client_id = c.client_id');
	$q->innerJoin('activity_clients', 'ac', 'ac.activity_clients_client_id = c.client_id');
	$q->addQuery('c.client_id, c.client_first_name, c.client_status, c.client_notes');
	$q->addWhere("c.client_first_name LIKE '$where%'");

	if (count($allowedClients) > 0) { $q->addWhere('c.client_id IN (' . implode(',', array_keys($allowedClients)) . ')'); }
	if ((count($allowedClinics) > 0) && ($allowedClinics[0] <> NULL)) { $q->addWhere('c.client_clinic IN (' . implode(',', array_keys($allowedClinics)) . ')'); }

	if ($clientType) 
	{
	  if ($client_type_filter > 0 && $client_type_filter < 99)	
	  { 
			//$q->addWhere('ci.counselling_clinic = '.$client_type_filter ); 
			$q->addWhere('ci.counselling_clinic = '.$client_type_filter . ' AND c.client_status = 1'); 
	  }
	  else if ($client_type_filter ===  NULL)
	  {
			//$q->addWhere( ' (ci.counselling_clinic = '.$client_type_filter . ' OR ci.counselling_clinic IS NULL OR c.client_status <> 1 )'); 
			$q->addWhere( '(ci.counselling_clinic NOT IN ( SELECT CONCAT_WS(",", clinic_id) FROM clinics)) OR ci.counselling_clinic IS NULL '); 
	  }
	  else if ($client_type_filter ==  99)
	  {
			//$q->addWhere( ' (ci.counselling_clinic = '.$client_type_filter . ' OR ci.counselling_clinic IS NULL OR c.client_status <> 1 )'); 
			$q->addWhere( '(c.client_status <> 1) '); 
	  }
	}
	$q->addWhere('ac.activity_clients_activity_id = ' . $activity_id);
	$q->addOrder($clientorderby.' '.$clientorderdir);
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
	echo printPageNavigation( '?m=activity', $page, $num_pages, $offset, $limit, $count, 'clients');
}

?>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<tr>
	<th nowrap="nowrap">
		<a href="?m=activity&a=add&clientorderby=client_adm_no&activity_id=<?php echo $activity_id;?>" class="hdr"><?php echo $AppUI->_('Adm. No');?></a>
	</th>	
	<th nowrap="nowrap">
		<a href="?m=activity&a=add&clientorderby=client_last_name&activity_id=<?php echo $activity_id;?>" class="hdr"><?php echo $AppUI->_('Client Name');?></a>
	</th>
	<th nowrap="nowrap">
		<?php echo $AppUI->_('Age (years)');?>
	</th>		
	<th nowrap="nowrap">
		<?php echo $AppUI->_('Age (months)');?>
	</th>	
    
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
	
if (!empty($rows))	
{	foreach ($rows as $row)
	{

	    $obj->reset;	
		$obj->load($row["client_id"]);
		
		$q = new DBQuery();
		$q->addTable("counselling_info");
		$q->addQuery("counselling_info.*");
		$q->addWhere("counselling_info.counselling_client_id = " . $row["client_id"]);
		$sql = $q->prepare();
		$counsellingObj = new CCounsellingInfo();
		//$counsellingObj->reset();
		$date = " ";
		if (db_loadObject($sql,$counsellingObj))
		{
			$client_dob = intval($counsellingObj->counselling_dob) ? new CDate($counsellingObj->counselling_dob ) :  null;
			if (isset($client_dob))
			{
				$date = $client_dob->format($df);
			}
		}
		
		$client_doa = intval($obj->client_entry_date) ? new CDate($obj->client_entry_date ) :  null;
		$doa = NULL;
		if (isset($client_doa))
		{
				$doa = $client_doa->format($df);
		}
		
		$q = new DBQuery();
		$q->addTable("admission_info");
		$q->addQuery("admission_info.*");
		$q->addWhere("admission_info.admission_client_id = " . $row["client_id"]);
		$sql = $q->prepare();
		$admissionObj  = new CAdmissionRecord();
		$mother_name = "";
		$father_name = "";
		$admission_loaded = db_loadObject($sql,$admissionObj);
		
				
		/*if (!db_loadObject($sql, $obj) && ($client_id > 0))
		{
			$AppUI->setMsg( "invalidID", UI_MSG_ERROR, true );
			$AppUI->redirect();
		}*/

		
		//$url
		
		$none = false;
		$years = 0;
		$months = 0;
		$obj->getAge($years, $months);
		$s .= $CR . '<tr>';
		$s .= $CR . '<td nowrap="nowrap">' . $obj->client_adm_no . '</td>';
		$s .= $CR . '<td nowrap="nowrap"><a href="'. $obj->getUrl('view')   .'&client_id=' . $obj->client_id . '" title="'.$obj->client_description.'">' . $obj->getFullName() .'</a></td>';
		$s .= $CR . '<td nowrap="nowrap">' . $years . '</td>';
		$s .= $CR . '<td nowrap="nowrap">' . $months . '</td>';
		$s .= $CR . '</tr>';
	}
}
if ( $AppUI->isActiveModule('clients') && $perms->checkModule('clients', 'view')) {
							$s .= "<input type='button' class='button' value='".$AppUI->_("Select clients...")."' onclick='javascript:popClients();' />";
						}
//$s .= '<input type="button" class=button value="'.$AppUI->_( 'add new participant' ).'" onClick="javascript:window.location=\'./index.php?m=clients&a=pick&activity_id='.$activity_id.'\'">';
//var_dump($obj);
echo "$s\n";
if ($none)
{
	echo $CR . '<tr><td colspan="8">' . $AppUI->_( 'No clients available' ) . '</td></tr>';
}
?>
</table>
<?php
if ($count > 0)
{
   echo printPageNavigation( '?m=clients', $page, $num_pages, $offset, $limit, $count, 'clients');
}
?>
