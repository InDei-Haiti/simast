<?php
require_once ($AppUI->getModuleClass('clients'));
require_once ($AppUI->getModuleClass('contacts'));
require_once ($AppUI->getModuleClass('admin'));

global $search_string;
global $activity_id;
global $owner_filter_id;
global $currentTabId;
global $currentTabName;
global $tabbed;
global $page;
global $type_filter;
global $city_filter;
global $contactorderby;
global $orderdir;
global $limit;
global $options;
global $perms;
//global $AppUI;



if (empty($contactorderby))
{
	$contactorderby = 'contact_first_name';
}

if (empty($orderdir))
{
	$orderdir = 'asc';
}

//pager settings
$page = dPgetParam($_GET, 'page', 1);
$limit = intval($dPconfig['max_limit']);

$types = dPgetSysVal('ClientStatus');
$search = false;

$obj = new CContact();


$allowedContacts = $obj->getAllowedRecords($AppUI->user_id, 'contact_id, contact_first_name' );
if ($AppUI->user_type <> 1) //not admin user type
{
	//get allowed clinics if user is not an admin
	$q = new DBQuery();
	$q->addTable("users");
	$q->addQuery("users.user_clinics");
	$q->addWhere("users.user_id = " . $AppUI->user_id);
	$allowedClinics = $q->loadHashList();

}
$contact_type_filter = $currentTabId;
//pager settings
if (strncmp($currentTabName, "Not Applicable", strlen("Not Applicable")) == 0)
	$contact_type_filter = NULL;
if (strncmp($currentTabName, "Not Active", strlen("Not Active")) == 0)
	$contact_type_filter = 99;
	
//$count = $obj->getCount($AppUI->user_type, $AppUI->user_id,$client_type_filter);


$where = $AppUI->getState( 'ContactIdxWhere' ) ? $AppUI->getState( 'ContactIdxWhere' ) : '%';

if ($where != '%') $search=true;


$clientType = true;

if (strncmp($currentTabName,"All Staff", strlen("All Staff")) == 0)
	$staffType = false;
if (strncmp($currentTabName,"Not Applicable", strlen("Not Applicable")) == 0)
	$contact_type_filter = NULL;	
if (strncmp($currentTabName,"Not Active", strlen("Not Active")) == 0)
	$contact_type_filter = 99;	
//if ($currentTabName == "Not Applicable")
//	$company_type_filter = 0;

	
    
if (isset($activity_id) && $activity_id > 0)
{
	$q = new DBQuery;
	
	//$q->setLimit($limit, $offset  );
	$q->addTable('contacts', 'c');
	$q->leftJoin('users', 'u', 'u.user_contact = c.contact_id');
	$q->innerJoin('activity_contacts', 'ac', 'ac.activity_contacts_contact_id = c.contact_id');
	$q->addQuery('c.contact_id, c.contact_first_name,c.contact_other_name, c.contact_last_name');
	//$q->addWhere("c.contact_first_name LIKE '$where%'");

	//if (count($allowedContacts) > 0) { $q->addWhere('c.contact_id IN (' . implode(',', array_keys($allowedContacts)) . ')'); }


	
	$q->addWhere('ac.activity_contacts_activity_id = ' . $activity_id);
	$q->addOrder($contactorderby.' '.$orderdir);
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
	echo printPageNavigation( '?m=activity', $page, $num_pages, $offset, $limit, $count, 'staff');
}

?>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<tr>
	<th nowrap="nowrap">
		<?php echo $AppUI->_('Staff ID');?>
	</th>	
	<th nowrap="nowrap">
		<?php echo $AppUI->_('Staff Name');?>
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

$obj = & new CContact();
	$obj->load($row["contact_id"]);
	
	//$url
	//$obj->getUrl('view') 
	
	$none = false;
	$s .= $CR . '<tr>';
	$s .= $CR . '<td nowrap="nowrap">' . $row["contact_id"] .'</td>';
	$s .= $CR . '<td nowrap="nowrap"><a href="index.php?m=contacts&a=view&contact_id='. $obj->contact_id .  '" title="'.$obj->contact_description.'">' . $obj->getFullName() .'</a></td>';
	}
}
if ( $AppUI->isActiveModule('contacts') && $perms->checkModule('contacts', 'view')) {
							$s .= "<input type='button' class='button' value='".$AppUI->_("Select staff...")."' onclick='javascript:popContacts();' />";
						}
//$s .= '<input type="button" class=button value="'.$AppUI->_( 'add new participant' ).'" onClick="javascript:window.location=\'./index.php?m=clients&a=pick&activity_id='.$activity_id.'\'">';
//var_dump($obj);
echo "$s\n";
if ($none)
{
	echo $CR . '<tr><td colspan="8">' . $AppUI->_( 'No staff selected' ) . '</td></tr>';
}
?>
</table>
<?php
if ($count > 0)
{
   echo printPageNavigation( '?m=activity', $page, $num_pages, $offset, $limit, $count, 'staff');
}
?>
