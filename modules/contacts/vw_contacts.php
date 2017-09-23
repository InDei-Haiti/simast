<?php

global $search_string;
global $currentTabId;
global $currentTabName;
global $tabbed;
global $page;
global $orderby;
global $orderdir;
global $limit;


$search = false;

$obj = new CContact();

//pager settings
$count = $obj->getCount();
$num_pages = ceil ($count / $limit);
$offset = ($page - 1) * $limit;
//var_dump($num_pages);

$where = $AppUI->getState( 'ContactIdxWhere' ) ? $AppUI->getState( 'ContactIdxWhere' ) : '%';

if ($where != '%') $search=true;

//if ($offset <= 0)
//{
	//$limit = intval($count);
	//$offset = 0;
//}
	
//var_dump($limit);
//var_dump($offset);
$q = new DBQuery;

/*
 * Removed for new version of table
 */
//$q->setLimit($limit, $offset  );
$q->addTable('contacts', 'c');
$q->addQuery('c.contact_id, c.contact_first_name, c.contact_other_name, c.contact_last_name, c.contact_email, c.contact_email2, c.contact_mobile, c.contact_phone, c.contact_phone2');
$q->addWhere("c.contact_first_name LIKE '$where%'");
$q->addOrder($orderby.' '.$orderdir);
$q->addWhere('contact_id <> "13"');

//$sql = $q->prepare();
//var_dump($sql);

$rows = $q->loadList();

echo printPageNavigation( '?m=contacts', $page, $num_pages, $offset, $limit, $count, 'Staff');

?>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<tr>
	<th nowrap="nowrap" width="55%">
		<a href="?m=contacts&orderby=contact_first_name" class="hdr"><?php echo $AppUI->_('Contact Name');?></a>
	</th>
	<th nowrap="nowrap">
		<?php echo $AppUI->_('Mobile Contact');?>
	</th>
	<th nowrap="nowrap">
		<?php echo $AppUI->_('Contact Email');?>
	</th>
	<th nowrap="nowrap">
		<?php echo $AppUI->_('Contact Phone');?>
	</th>
	<th nowrap="nowrap">
		<?php echo $AppUI->_('Contact Phone(2)');?>
	</th>
	<th nowrap="nowrap">
		<?php echo $AppUI->_('Clients Assigned');?>
	</th>	
	<th nowrap="nowrap">
		<?php echo $AppUI->_('vCard');?>
	</th>

</tr>
<?php
$s = '';
$CR = "\n"; // Why is this needed as a variable?
$none = true;


foreach ($rows as $row)
{
	$obj = & new CContact();
	$obj->load($row["contact_id"]);
	
	//$url
	//$obj->getUrl('view') 
	
	$none = false;
	$s .= $CR . '<tr>';
	$s .= $CR . '<td nowrap="nowrap"><a href="index.php?m=contacts&a=view&contact_id='. $obj->contact_id .  '" title="'.$obj->contact_description.'">' . $obj->getFullName() .'</a></td>';
	$s .= $CR . '<td align="center" nowrap="nowrap">' . $obj->contact_mobile . '</td>';
	$s .= $CR . '<td align="center" nowrap="nowrap"><a href="mailto:' . $obj->contact_email . '">'.$obj->contact_email . '</td>';
	$s .= $CR . '<td align="center" nowrap="nowrap">' . $obj->contact_phone . '</td>';
	$s .= $CR . '<td align="center" nowrap="nowrap">' . $obj->contact_phone2 . '</td>';
	$s .= $CR . '<td align="center" nowrap="nowrap">' . $obj->getRoleCount(). '</td>';
	$s .= $CR . '<td align="center" nowrap="nowrap"><a href ="?m=contacts&a=vcardexport&suppressHeaders=true&contact_id='. $obj->contact_id . '"> (vCard)</a></td>';
	
	//$s .= $CR . '<td align="center" nowrap="nowrap"><a title=" ' . $AppUI->_('Export vCard for').' '. $obj->contact_first_name .' '.$obj->contact_last_name . '" href="?m=contacts&a=vcardexport&suppressHeaders=true&contact_id= ' .  $obj->contact_id . '>(vCard)</a></td>';
	$s .= $CR . '</tr>';
}
echo "$s\n";
if ($none)
{
	echo $CR . '<tr><td colspan="5">' . $AppUI->_( 'No contacts available' ) . '</td></tr>';
}
?>
</table>
<?php
   echo printPageNavigation( '?m=contacts', $page, $num_pages, $offset, $limit, $count, 'Staff');
?>