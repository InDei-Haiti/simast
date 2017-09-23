<?php /* COMPANIES $Id: index.php 5631 2008-03-04 07:31:09Z ajdonnison $ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

// First order check if we are allowed to view
if (!$canAccess) {
	$AppUI->redirect('m=public&a=access_denied');
}
$AppUI->savePlace();

$valid_ordering = array(
	'company_name',
	'countp',
	'inactive',
	'company_type',
);

global $filshow;

$filshow=false;
// retrieve any state parameters
if (isset( $_GET['orderby'] ) && in_array($_GET['orderby'], $valid_ordering)) {
    $orderdir = $AppUI->getState( 'CompIdxOrderDir' ) ? ($AppUI->getState( 'CompIdxOrderDir' )== 'asc' ? 'desc' : 'asc' ) : 'desc';
	$AppUI->setState( 'CompIdxOrderBy', $_GET['orderby'] );
    $AppUI->setState( 'CompIdxOrderDir', $orderdir);
}
$orderby         = $AppUI->getState( 'CompIdxOrderBy' ) ? $AppUI->getState( 'CompIdxOrderBy' ) : 'company_name';
$orderdir        = $AppUI->getState( 'CompIdxOrderDir' ) ? $AppUI->getState( 'CompIdxOrderDir' ) : 'asc';

if(isset($_REQUEST["owner_filter_id"])){
	$AppUI->setState("owner_filter_id", $_REQUEST["owner_filter_id"]);
	$owner_filter_id = $_REQUEST["owner_filter_id"];
} else {
	$owner_filter_id = $AppUI->getState( 'owner_filter_id');
	if (! isset($owner_filter_id)) {
		$owner_filter_id = $AppUI->user_id;
		$AppUI->setState('owner_filter_id', $owner_filter_id);
	}
}
// load the company types
$types = dPgetSysVal( 'CompanyType' );

// get any records denied from viewing
$obj = new CCompany();
$deny = $obj->getDeniedRecords( $AppUI->user_id );


// Company search by Kist
$search_string = dPgetParam( $_REQUEST, 'search_string', "" );
if($search_string != ""){
	$search_string = $search_string == "-1" ? "" : $search_string;
	$AppUI->setState("search_string", $search_string);
} else {
	$search_string = $AppUI->getState("search_string");
}

// $canEdit = getPermission($m, 'edit');
// retrieve list of records
$search_string = dPformSafe($search_string, true);

$perms =& $AppUI->acl();
$owner_list = array( 0 => $AppUI->_("All", UI_OUTPUT_RAW)) + $perms->getPermittedUsers("companies"); // db_loadHashList($sql);
$owner_combo = arraySelect($owner_list, "owner_filter_id", "class='text' onchange='javascript:document.searchform.submit()'", $owner_filter_id, false);

// setup the title block

//fetch list of countries
/* $q = new DBQuery();
$q->addTable('administrative_regions');
$q->addQuery('region_id, region_name');
$q->addWhere('region_parent = 0');
$country_list = arrayMerge(array(-1 => 'All Countries'), $q->loadHashList()); */

$q = new DBQuery();
$q->addTable('projects');
$q->addQuery('project_id, project_name');
$q->addOrder("project_name");
$projects = arrayMerge(array(-1 => 'All Projects'), $q->loadHashList());

echo '<br /><br /><div class="card">';


$titleBlock = new CTitleBlock('', 'shim.gif');
$titleBlock->showhelp = false;
//$titleBlock->addCell($AppUI->_('Country') . ':');

$sel_str = "selected='selected'";
unset($tvar,$selphtml);


$titleBlock->addCell ( $selphtml, '', '<form action="?m=companies" method="post" name="companyFilter">', '' );

if (isset( $_GET['tab'] )) {
	$AppUI->setState( 'CompaniesIdxTab', $_GET['tab'] );
}
$companiesTypeTab = defVal( $AppUI->getState( 'CompaniesIdxTab' ),  0 );

// $tabTypes = array(getCompanyTypeID('Client'), getCompanyTypeID('Supplier'), 0);
$companiesType = $companiesTypeTab;

$tabBox = new CTabBox( '?m=companies', DP_BASE_DIR.'/modules/companies/', $companiesTypeTab );
if ($tabbed = $tabBox->isTabbed()) {
	$add_na = true;
	/*if (isset($types[0])) { // They have a Not Applicable entry.
		$add_na = false;
		$types[] = $types[0];
	}*/
	$types[0] = "All Agencies";
	/*//if ($add_na)
		$types[100] = "Not Applicable";
	unset($types[-1]);*/
}
$type_filter = array();
$q = new DBQuery();
$q->addTable('companies');
$q->addQuery('count(*) as c');
ksort($types);
foreach($types as $type => $type_name){
	$q1 = clone $q;
	if(is_numeric($type) && (int)$type > 0 || $type_name === 'Not Applicable'){
		if($type_name === 'Not Applicable'){
			$type=100;
		}
		$q1->addWhere('company_category="'.($type == 100 ? 0 : $type).'"');
	}
	
	$count=$q1->loadResult();
	$type_filter[] = $type;
	//echo $type_name.' ';
	$tabBox->add('vw_companies',  $AppUI->_($type_name).' ('.$count.')',true,$type);
}

$tabBox->show();
echo "</div>";
?>
