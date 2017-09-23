<?php //$_GET['mode'] == 'loadtask' && (int)$_GET['pid'] > 0
if($_GET['mode'] == 'loadsection' && !empty($_GET['ccode'])){
	$code = $_GET['ccode'];
	$q = new DBQuery();
	$q->addTable("administration_section","section");
	$q->addQuery("section.administration_section_code,section.administration_section_name");
	$q->addWhere("section.administration_section_code_com='".$code."'");
	$result = $q->loadHashList();
	if($result)
		echo json_encode($result);
	else echo 'fail';
	return;
}elseif ($_GET['mode'] == 'loadcommun' && !empty($_GET['ccode'])){
	$code = $_GET['ccode'];
	$q = new DBQuery();
	$q->addTable("administration_section","section");
	$q->addJoin('administration_com', 'com', 'com.administration_com_code = "'.$code.'"');
	$q->addQuery("com.administration_com_code,com.administration_com_name");
	$q->addWhere("section.administration_section_code='".$code."'");
	$result = $q->loadHashList();
	if($result)
		echo json_encode($result);
	else echo 'fail';
	return;
}

$AppUI->savePlace ();

// retrieve any state parameters
if (isset ( $_GET ['orderby'] )) {
	$orderdir = $AppUI->getState ( 'ClientIdxOrderDir' ) ? ($AppUI->getState ( 'ClientIdxOrderDir' ) == 'asc' ? 'desc' : 'asc') : 'desc';
	$AppUI->setState ( 'ClientIdxOrderBy', $_GET ['orderby'] );
	$AppUI->setState ( 'ClientIdxOrderDir', $orderdir );
}
$orderby = $AppUI->getState ( 'ClientIdxOrderBy' ) ? $AppUI->getState ( 'ClientIdxOrderBy' ) : 'client_lvd';//'client_first_name';
$orderdir = $AppUI->getState ( 'ClientIdxOrderDir' ) ? $AppUI->getState ( 'ClientIdxOrderDir' ) : 'desc';//: 'asc';

if (isset ( $_GET ['where'] )) {
	$AppUI->setState ( 'ClientIdxWhere', $_GET ['where'] );
}

if ($AppUI->user_type != 1) //not admin user type
{
	//get allowed clinics if user is not an admin
	$q = new DBQuery ();
	$q->addTable ( "users" );
	$q->addQuery ( "users.user_clinics" );
	$q->addWhere ( "users.user_id = " . $AppUI->user_id );
	$allowedClinics = $q->loadResult ();

}

// load the centers
$q = new DBQuery ();
$q->addTable ( 'tasks' );
$q->addQuery ( 'task_id, task_name' );
if (! empty ( $allowedClinics ))
	$q->addWhere ( 'task_id IN (' . $allowedClinics . ')' );
$q->addOrder ( 'task_name' );

$types = $q->loadHashList ();

// get any records denied from viewing
$obj = new CClient ();
$deny = $obj->getDeniedRecords ( $AppUI->user_id );

$canEdit = ! getDenyEdit ( $m );
// retrieve list of records


$perms = & $AppUI->acl ();

if (isset ( $_GET ['tab'] )) {
	$AppUI->setState ( 'ClientIdxTab', $_GET ['tab'] );
}
$clientTypeTab = defVal ( $AppUI->getState ( 'ClientIdxTab' ), 0 );

// $tabTypes = array(getCompanyTypeID('Client'), getCompanyTypeID('Supplier'), 0);
$clientType = $clientTypeTab;

$let = ':';
/*
$q = new DBQuery ();
$q->addTable ( 'clients' );
$q->innerJoin ( 'counselling_info', 'ci', 'ci.counselling_client_id = clients.client_id' );
$q->addQuery ( "DISTINCT UPPER(SUBSTRING(client_first_name,1,1)) as L" );

if ($clientType > 0) {
	if ($types [$clientType] == NULL) {
		$q->addWhere ( "client_status <> 1" );
	} else {
		$q->addWhere ( "ci.counselling_clinic = $clientType" );
	}
}

$arr = $q->loadList ();

foreach ( $arr as $L ) {
	$let .= $L ['L'];
}

$a2z = "\n<table cellpadding=\"2\" cellspacing=\"1\" border=\"0\">";
$a2z .= "\n<tr>";
$a2z .= "<td width='100%' align='right'>" . $AppUI->_ ( 'Show' ) . ": </td>";
$a2z .= '<td><a href="./index.php?m=clients&where=0">' . $AppUI->_ ( 'All' ) . '</a></td>';

for($c = 65; $c < 91; $c ++) {
	
	$cu = chr ( $c );
	$cell = strpos ( $let, "$cu" ) > 0 ? "<a href=\"?m=clients&where=$cu\">$cu</a>" : "<font color=\"#999999\">$cu</font>";
	
	if ($cu == $where)
		$a2z .= "\n\t<td class=\"selected\">$cell</td>";
	else
		$a2z .= "\n\t<td>$cell</td>";
}

$a2z .= "\n</tr>\n<tr><td colspan='28'>$form</td></tr></table>";

// setup the title block
/*$titleBlock = new CTitleBlock ( 'Clients', NULL, $m, "$m.$a" );
$titleBlock->addCell ( $a2z );

$titleBlock->addCell ( "<form name='searchform' action='?m=search' method='post'>
                        <table>
                         <tr>
                          <td>						   
                              <input class = 'text' type='text' name ='search_string' value='$search_string' />
						   </td>
						   <td>
							  <input type='submit' value='" . $AppUI->_ ( 'search' ) . "' class='button' />
						   </td>
						  </tr>
                         </table>
                        </form>" );
if ($canEdit) {
	$titleBlock->addCell ( '<input type="submit" class="button" value="' . $AppUI->_ ( 'new intake' ) . '">', '', '<form action="?m=clients&a=add" method="post">', '</form>' );
}*/
/*$titleBlock->addCell("<form name='searchform' action='?m=search&amp;search_string=$search_string' method='post'>
						<table>
							<tr>
                      			<td>
                                    <strong>".$AppUI->_('Search')."</strong>
                                    <input class='text' type='text' name='search_string' value='$search_string' /><br />
							</tr>
						</table>
                      </form>");
$titleBlock->show ();
*/
//$search_string = addslashes ( $search_string );


$tabBox = new CTabBox ( "?m=clients", dPgetConfig ( 'root_dir' ) . "/modules/clients/", $clientTypeTab );

if ($tabbed = $tabBox->isTabbed ()) {
	$add_na = true;
	if (isset ( $types [0] )) { // They have a Not Applicable entry.
		$add_na = false;
		$types [] = $types [0];
	}
	$types = arrayMerge ( array (0 => "All Beneficieries" ), $types );
	if ($add_na) {
		//$types [] = "Not Applicable";
		// $types [98] = "LTP";
		//$types [99] = "Not Active";
		//$types [100] = 'VCT';
	}
}

//pager settings
$page = dPgetParam ( $_GET, 'page', 1 );
$limit = intval ( $dPconfig ['max_limit'] );

$type_filter = array ();
foreach ( $types as $type => $type_name ) {
	$type_filter [] = $type;
	/*if (strncmp ( $type_name, "Not Applicable", strlen ( "Not Applicable" ) ) == 0)
		$type = NULL;*/
	if (strncmp ( $type_name, "Not Active", strlen ( "Not Active" ) ) == 0)
		$type = 99;
    /*if (strncmp ( $type_name, "LTP", strlen ( "LTP" ) ) == 0)
		$type = 98;*/
	$tabBox->add ( "vw_clients_new", $type_name . " (" . $obj->getCount ( $AppUI->user_type, $AppUI->user_id, $type ) . ")", false, $type );
}

$tabBox->show ();
?>
<style type="text/css">
	#rtable{
		width: 100% !important;
	}
</style>