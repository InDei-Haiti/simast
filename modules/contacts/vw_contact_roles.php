<?php
require_once($AppUI->getModuleClass('clients'));
global $contact_id;
global $currentTabId;
global $currentTabName;
global $tabbed;
global $limit;
global $dPconfig;
global $page;

$search = false;

$obj = new CContact();
$obj->load($contact_id);
$offset = ($page - 1) * $limit;
$obj->setRoleViewLimits($limit,$offset);

//pager settings
$count = $obj->getRoleCount();
$qid  = $obj->getContactRoles();



$num_pages = ceil ($count / $limit);


if ($where != '%') $search=true;

if ($offset < 0)
{
	$limit = intval($count);
	$offset = 0;
}
//var_dump($count);
//var_dump($offset);
//var_dump($limit);
/*
$q = new DBQuery;
$q->setLimit($limit, $offset  );	
$q->addTable('clients', 'c');
$q->innerJoin('counselling_info', 'ci', 'ci.counselling_client_id = c.client_id');
$q->innerJoin('contacts', 'si', 'ci.counselling_staff_id = si.contact_id');
$q->addQuery('c.client_adm_no, c.client_first_name, c.client_last_name, "Intake Officer" AS role');
$q->addWhere("si.contact_id=$contact_id");
//$q->addQuery('');
$q->addTable('clients', 'c');
$q->innerJoin('admission_info', 'ai', 'ai.admission_client_id = c.client_id');
$q->innerJoin('contacts', 'sa', 'ai.admission_staff_id = sa.contact_id');
$q->addQuery('UNION c.client_adm_no, c.client_first_name, c.client_last_name, "Admission Officer" AS role');
$q->addWhere("sa.contact_id=$contact_id");
*/
//$sql = $q->prepare();
//var_dump($sql);

//$rows = $roles;

echo ( printPageNavigation( "?m=contacts&a=view&contact_id=".(int)$contact_id, $page, $num_pages, $offset, $limit, $count, 'Clients Assigned to this staff member'));

?>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<tr>
	<th nowrap="nowrap">
		<?php echo $AppUI->_('Client Admission Number');?>
	</th>	
	<th nowrap="nowrap" width="55%">
		<?php echo $AppUI->_('Client Name');?>
	</th>
	<th nowrap="nowrap">
		<?php echo $AppUI->_('Staff Role');?>
	</th>
</tr>
<?php
$s = '';
$CR = "\n"; // Why is this needed as a variable?
$none = true;

$clientObj = new CClient();

while ($row = db_fetch_row($qid)){
	$none = false;
	//var_dump($row);
	
	
	$clientObj->load($row["client_id"]);
	//$url
	$none = false;
	$s .= $CR . '<tr>';
	$s .= $CR . '<td nowrap="nowrap"><a href="'. $clientObj->getUrl('view')   . '">' . $clientObj->client_adm_no .'</a></td>';
	$s .= $CR . '<td nowrap="nowrap"><a href="'. $clientObj->getUrl('view')   . '" title="'.$obj->contact_description.'">' . $AppUI->_($clientObj->getFullName()) .'</a></td>';
	$s .= $CR . '<td nowrap="nowrap">' . $row["role"] . '</td>';
	$s .= $CR . '</tr>';
}
echo "$s\n";
if ($none)
{
	echo $CR . '<tr><td colspan="5">' . $AppUI->_( 'No roles assigned to this staff member' ) . '</td></tr>';
}
?>
</table>
<?php
  echo ( printPageNavigation( "?m=contacts&a=view&contact_id=".(int)$contact_id, $page, $num_pages, $offset, $limit, $count, 'Clients Assigned to this staff member'));
?>