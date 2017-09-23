<?php


require_once ($AppUI->getSystemClass('genericTable'));

global $search_string;
global $currentTabId;
global $currentTabName;
global $tabbed;
global $page;
global $orderby;
global $orderdir;
global $limit;

$gt = new genericTable(true);

$search = false;

$obj = new CContact();

$where = $AppUI->getState( 'ContactIdxWhere' ) ? $AppUI->getState( 'ContactIdxWhere' ) : '%';

if ($where != '%') $search=true;

$q = new DBQuery;

echo '<br /><br /><div class="card">';
/*
 * Removed for new version of table
 */
//$q->setLimit($limit, $offset  );
$q->addTable('contacts', 'c');
$q->addQuery('c.contact_id, c.contact_first_name, c.contact_other_name, c.contact_last_name, c.contact_email, c.contact_email2, c.contact_mobile, c.contact_phone, c.contact_phone2');
$q->addWhere("c.contact_first_name LIKE '$where%'");
//$q->addOrder($orderby.' '.$orderdir);
$q->addWhere('contact_id <> "13"');

//$sql = $q->prepare();
//var_dump($sql);

$rows = $q->loadList();
//echo printPageNavigation( '?m=contacts', $page, $num_pages, $offset, $limit, $count, 'Staff');

$headers = array(
				'Contact Name'=> 'string',
				'Mobile Contact'=>'int',
				'Contact Email'=>'string',
				'Contact Phone'=>'int',
				'Contact Phone(2)'=>'int',
				'Clients Assigned'=>'int',
				'vCard'=>'string');

$gt->makeHeader($headers);

$decs = array(  0=>'<a href = "index.php?m=contacts&a=view&contact_id=##7##" > ##0##</a >',
				2=>'<a href="mailto:##2##">##2##</a>',
				6=>'<a href ="?m=contacts&a=vcardexport&suppressHeaders=true&contact_id=##7##">##6##</a>'
);

$gt->setDecorators($decs);
$gt->setPageTitle("Contacts");


$s = '';
$CR = "\n"; // Why is this needed as a variable?
$none = true;
if ($canEdit) {
		$gt->setToolBar(
			'<a class="button ce pi ahr" href="?m=contacts&a=addedit">' . $AppUI->_('New Staff') .
			'<a class="button ce pi ahr" href="./index.php?m=contacts&a=csvexport&suppressHeaders=true">' . $AppUI->_('CSV Download') . "</a>".
			'<a class="button ce pi ahr" href="./index.php?m=contacts&a=vcardimport&dialog=0">' . $AppUI->_('Import vCard') . '</a>'
		);
}
//$row_data = array();
//$nfei = new evolver();
foreach ($rows as $rid => $row)
{
	$obj = & new CContact();
	$obj->load($row["contact_id"]);
	
	//$url
	//$obj->getUrl('view') 

	$row_data = array(
					$obj->getFullname(),
					$obj->contact_mobile,
					$obj->contact_email,
					$obj->contact_phone,
					$obj->contact_phone2,
					'roles',
					//$obj->getRoleCount(),
					'vCard',
					$obj->contact_id
	);
	$gt->fillBody($row_data);

	$none = false;
	/*
	$s='';
	$s .= $CR . '<tr id="row_'.$rid.'">';
	$s .= $CR . '<td ><a href="index.php?m=contacts&a=view&contact_id='. $obj->contact_id .  '" title="'.$obj->contact_description.'">' . $row_data[$rid][0] .'</a></td>';
	$s .= $CR . '<td >' . $row_data[$rid][1] . '</td>';
	$s .= $CR . '<td ><a href="mailto:' . $row_data[$rid][2] . '">'.$row_data[$rid][2] . '</td>';
	$s .= $CR . '<td >' . $row_data[$rid][3] . '</td>';
	$s .= $CR . '<td >' . $row_data[$rid][4] . '</td>';
	$s .= $CR . '<td >' . $row_data[$rid][5] . '</td>';
	$s .= $CR . '<td align="center""><a href ="?m=contacts&a=vcardexport&suppressHeaders=true&contact_id='. $obj->contact_id . '"> (vCard)</a></td>';
	
	//$s .= $CR . '<td align="center" nowrap="nowrap"><a title=" ' . $AppUI->_('Export vCard for').' '. $obj->contact_first_name .' '.$obj->contact_last_name . '" href="?m=contacts&a=vcardexport&suppressHeaders=true&contact_id= ' .  $obj->contact_id . '>(vCard)</a></td>';
	$s .= $CR . '</tr>';
	$gt->addTableHtmlRow($s);*/
}

if ($none){
	$gt->addTableHtmlRow($CR . '<tr><td colspan="5">' . $AppUI->_( 'No contacts available' ) . '</td></tr>)');
}


$gt->compile();
//   echo printPageNavigation( '?m=contacts', $page, $num_pages, $offset, $limit, $count, 'Staff');

echo "</div>";?>