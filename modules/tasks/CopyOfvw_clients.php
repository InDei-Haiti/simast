<?php
require_once($AppUI->getModuleClass('clients'));
global $contact_id;
global $currentTabId;
global $currentTabName;
global $tabbed;
global $limit;
global $dPconfig;
global $page;

global $task_name;
$search = false;
$task_id = intval( dPgetParam( $_GET, "task_id", 0 ) );
//$obj = new CContact();
/*
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
}*/
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

//echo ( printPageNavigation( "?m=contacts&a=view&contact_id=".(int)$contact_id, $page, $num_pages, $offset, $limit, $count, 'Clients Assigned to this staff member'));




$q = new DBQuery;

$q->setLimit($limit, $offset  );
$q->addTable('clients', 'c');
//$q->addQuery('client_id');
//$q->addWhere($task_id.' in (SPLIT(",",c.client_activities))');
$q->addWhere("c.client_id IN (SELECT activity_clients_client_id FROM activity_clients WHERE activity_clients_activity_id = ".$task_id. ")");
$sql = $q->prepare ();

//echo $sql;
//var_dump($sql);
//print $sql;

$qid = db_exec ( $sql );
//var_dump($qid);
$count = db_num_rows ( $qid );

$rows = $q->loadList ();
//var_dump($rows);
$ob = new CClient();
?>

<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<?php
echo '<tr><th colspan="4" align="right" valign="top" style="background-color:#ffffff">';
//echo '<input type="button" class=button style="float:right" value="'.$AppUI->_( 'Add New Beneficiery' ).'" onClick="javascript:window.location=\'./index.php?a=addedit&m=clients&activity_id='.$activity_id.'\'">';
echo '<input type="button" class=button style="float:right" value="'.$AppUI->_( 'Add Beneficieries' ).'" onClick="popSelects(\'client\','.$task_id.')">';
echo '</th></tr>';
?>
<tr>
	<th nowrap="nowrap" width="40%">
		<?php echo $AppUI->_('Beneficiery name');?>
	</th>	
	<th nowrap="nowrap">
		<?php echo $AppUI->_('Beneficiery CIN');?>
	</th>
	<th nowrap="nowrap">
		<?php echo $AppUI->_('Beneficiery Other Identification');?>
	</th>
	<th nowrap="nowrap">
		<?php echo $AppUI->_('Gender');?>
	</th>
</tr>
<?php
$none = true;

foreach ( $rows as $rid =>  $row ){
	$none = false;
	$ob->reset();
	$ob->load($row["client_id"]);
	echo '<tr>';
	echo '<td><a href="'. $ob->getUrl('view')   . '">'.$row['client_first_name'].' '.$row['client_last_name'].'</a></td>';
	echo '<td>'.$row['client_cin'].'</a></td>';
	echo '<td>'.$row['client_other_id'].'</a></td>';
	$GenderType = dPgetSysVal('GenderType');
	echo '<td>'.$GenderType[$row['client_gender']].'</a></td>';
	echo '</tr>';
}

if ($none)
{
	echo $CR . '<tr><td colspan="4">' . $AppUI->_( 'No Beneficieries' ) . '</td></tr>';
}

?>
</table>

<script type="text/javascript" src="/modules/tasks/view.js"></script>
