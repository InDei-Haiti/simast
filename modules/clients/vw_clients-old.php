<?php
require_once ($AppUI->getModuleClass ( 'counsellinginfo' ));
require_once ($AppUI->getModuleClass ( 'admission' ));

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

$types = dPgetSysVal ( 'ClientStatus' );
$search = false;

$obj = new CClient ();

$allowedClients = $obj->getAllowedRecords ( $AppUI->user_id, 'client_id, client_first_name' );
if ($AppUI->user_type != 1) //not admin user type
{
	//get allowed clinics if user is not an admin
	$q = new DBQuery ();
	$q->addTable ( "users" );
	$q->addQuery ( "users.user_clinics" );
	$q->addWhere ( "users.user_id = " . $AppUI->user_id );
	$allowedClinics = $q->loadHashList ();

}
$client_type_filter = $currentTabId;
//pager settings
if (strncmp ( $currentTabName, "Not Applicable", strlen ( "Not Applicable" ) ) == 0)
	$client_type_filter = NULL;
if (strncmp ( $currentTabName, "Not Active", strlen ( "Not Active" ) ) == 0)
	$client_type_filter = 99;

$count = $obj->getCount ( $AppUI->user_type, $AppUI->user_id, $client_type_filter );

$num_pages = ceil ( $count / $limit );
$offset = ($page - 1) * $limit;
//var_dump($num_pages);
if ($offset < 0) {
	$limit = intval ( $count );
	$offset = 0;
}

$where = $AppUI->getState ( 'ClientIdxWhere' ) ? $AppUI->getState ( 'ClientIdxWhere' ) : '%';

if ($where != '%')
	$search = true;

$clientType = true;

if (strncmp ( $currentTabName, "All Clients", strlen ( "All Clients" ) ) == 0)
	$clientType = false;
if (strncmp ( $currentTabName, "Not Applicable", strlen ( "Not Applicable" ) ) == 0)
	$client_type_filter = NULL;
if (strncmp ( $currentTabName, "Not Active", strlen ( "Not Active" ) ) == 0)
	$client_type_filter = 99;
if (strncmp ( $currentTabName, "VCT only", strlen ( "VCT only" ) ) == 0)
	$client_type_filter = 100;
	//if ($currentTabName == "Not Applicable")
//	$company_type_filter = 0;


//var_dump($limit);
//var_dump($offset);
$q = new DBQuery ();

$q->setLimit ( $limit, $offset );
$q->addTable ( 'clients', 'c' );
$q->innerJoin ( 'counselling_info', 'ci', 'ci.counselling_client_id = c.client_id' );
$q->leftJoin ( 'social_visit', 'sv', 'sv.social_client_id = c.client_id' );
$q->addQuery ( 'DISTINCT (c.client_id)' );
$q->addWhere ( "c.client_first_name LIKE '$where%'" );
//$q->addOrder('c.client_entry_date','desc');

//if (count($allowedClients) > 0) { $q->addWhere('c.client_id IN (' . implode(',', array_keys($allowedClients)) . ')'); }
if ((count ( $allowedClinics ) > 0) && ($allowedClinics [0] != NULL)) {
	$q->addWhere ( 'c.client_clinic IN (' . implode ( ',', array_keys ( $allowedClinics ) ) . ')' );
}

if ($clientType) {
	if ($client_type_filter > 0 && $client_type_filter < 99) {
		//$q->addWhere('ci.counselling_clinic = '.$client_type_filter ); 
		$q->addWhere ( 'ci.counselling_clinic = ' . $client_type_filter . ' AND (sv.social_client_status = 1 OR sv.social_client_status IS NULL)' );
	} else if ($client_type_filter === NULL) {
		//$q->addWhere( ' (ci.counselling_clinic = '.$client_type_filter . ' OR ci.counselling_clinic IS NULL OR c.client_status <> 1 )'); 
		$q->addWhere ( '(ci.counselling_clinic NOT IN ( SELECT CONCAT_WS(",", clinic_id) FROM clinics)) OR ci.counselling_clinic IS NULL ' );
	} else if ($client_type_filter == 99) {
		//$q->addWhere( ' (ci.counselling_clinic = '.$client_type_filter . ' OR ci.counselling_clinic IS NULL OR c.client_status <> 1 )'); 
		$q->addWhere ( '(sv.social_client_status <> 1 AND sv.social_client_status IS NOT NULL) ' );
	} elseif ($client_type_filter == 100) {
		//case of VCT only tab
		$q->addWhere ( 'c.client_status="9"' );
	
	}
}
$q->addWhere ( ' (sv.social_id IS NULL OR sv.social_id IN ( SELECT svi.social_id FROM social_visit svi INNER JOIN (SELECT social_client_id, MAX( social_entry_date ) AS social_max_date FROM social_visit GROUP BY social_client_id ) AS s2 ON svi.social_client_id = s2.social_client_id and svi.social_entry_date = s2.social_max_date)) ' );

$q->addOrder ( $orderby . ' ' . $orderdir );

$sql = $q->prepare ();

//print $sql;
$qid = db_exec ( $sql );
$count = db_num_rows ( $qid );

//var_dump($count);
$num_pages = ceil ( $count / $limit );

$offset = ($page - 1) * $limit;

if ($offset < 0) {
	$limit = intval ( $count );
	$offset = 0;
}

$q->setLimit ( $limit, $offset );

$rows = $q->loadList ();

//echo printPageNavigation( '?m=companies', $page, $num_pages, $offset, $limit, $count);
echo printPageNavigation ( '?m=clients', $page, $num_pages, $offset, $limit, $count, 'clients' );

?>


<table width="100%" border="0" cellpadding="2" cellspacing="1"
	class="tbl">
	<tr>
		<th nowrap="nowrap"><a href="?m=clients&orderby=client_adm_no"
			class="hdr"><?php
			echo $AppUI->_ ( 'Adm. No' );
			?></a></th>
		<th nowrap="nowrap"><a href="?m=clients&orderby=client_last_name"
			class="hdr"><?php
			echo $AppUI->_ ( 'Client Name' );
			?></a></th>
		<th nowrap="nowrap">
		<?php
		echo $AppUI->_ ( 'Client DOB' );
		?>
	</th>
		<th nowrap="nowrap">
		<?php
		echo $AppUI->_ ( 'Client DOA' );
		?>
	</th>
		<th nowrap="nowrap">
		<?php
		echo $AppUI->_ ( 'Location' );
		?>
	</th>

		<th nowrap="nowrap">
		<?php
		echo $AppUI->_ ( "Mother's Name" );
		?>
	</th>
		<th nowrap="nowrap">
		<?php
		echo $AppUI->_ ( "Father's Name" );
		?>
	</th>
		<th nowrap="nowrap">
		<?php
		echo $AppUI->_ ( 'Caregiver Mobile No.' );
		?>
	</th>

	</tr>
<?php
$s = '';
$CR = "\n"; // Why is this needed as a variable?
$none = true;
$df = $AppUI->getPref ( 'SHDATEFORMAT' );
$q = new DBQuery ();
$q->addTable ( 'clinic_location' );
$q->addQuery ( 'clinic_location.clinic_location_id, clinic_location.clinic_location' );
$locations = $q->loadHashList ();
foreach ( $rows as $row ) {
	
	$obj = new CClient ();
	$obj->load ( $row ["client_id"] );
	
	$q = new DBQuery ();
	$q->addTable ( "counselling_info" );
	$q->addQuery ( "counselling_info.*" );
	$q->addWhere ( "counselling_info.counselling_client_id = " . $row ["client_id"] );
	$sql = $q->prepare ();
	$counsellingObj = new CCounsellingInfo ();
	//$counsellingObj->reset();
	$date = " ";
	if (db_loadObject ( $sql, $counsellingObj )) {
		$client_dob = intval ( $counsellingObj->counselling_dob ) ? new CDate ( $counsellingObj->counselling_dob ) : null;
		if (isset ( $client_dob )) {
			$date = $client_dob->format ( $df );
		}
	}
	
	$client_doa = intval ( $counsellingObj->counselling_entry_date ) ? new CDate ( $counsellingObj->counselling_entry_date ) : null;
	$doa = NULL;
	if (isset ( $client_doa )) {
		$doa = $client_doa->format ( $df );
	}
	
	$q = new DBQuery ();
	$q->addTable ( "admission_info" );
	$q->addQuery ( "admission_info.*" );
	$q->addWhere ( "admission_info.admission_client_id = " . $row ["client_id"] );
	$sql = $q->prepare ();
	$admissionObj = new CAdmissionRecord ();
	$mother_name = "";
	$father_name = "";
	$admission_loaded = db_loadObject ( $sql, $admissionObj );
	
	/*if (!db_loadObject($sql, $obj) && ($client_id > 0))
	{
		$AppUI->setMsg( "invalidID", UI_MSG_ERROR, true );
		$AppUI->redirect();
	}*/
	
	//$url
	

	$none = false;
	$s .= $CR . '<tr>';
	$s .= $CR . '<td nowrap="nowrap">' . $obj->client_adm_no . '</td>';
	$s .= $CR . '<td nowrap="nowrap"><a href="' . $obj->getUrl ( 'view' ) . '&client_id=' . $obj->client_id . '" title="' . $obj->client_description . '">' . $obj->getFullName () . '</a></td>';
	$s .= $CR . '<td align="center" nowrap="nowrap">' . $date . '</td>';
	$s .= $CR . '<td align="center" nowrap="nowrap">' . $doa . '</td>';
	$s .= $CR . '<td align="center" nowrap="nowrap">' . $locations [$admissionObj->admission_location] . '</td>';
	
	$s .= $CR . '<td align="center" nowrap="nowrap">' . $admissionObj->admission_mother_fname . ' ' . $admissionObj->admission_mother_lname . '</td>';
	$s .= $CR . '<td align="center" nowrap="nowrap">' . $admissionObj->admission_father_fname . ' ' . $admissionObj->admission_father_lname . '</td>';
	$s .= $CR . '<td align="center" nowrap="nowrap">' . $admissionObj->admission_caregiver_mobile . '</td>';
	$s .= $CR . '</tr>';
}
//var_dump($obj);
echo "$s\n";
if ($none) {
	echo $CR . '<tr><td colspan="8">' . $AppUI->_ ( 'No clients available' ) . '</td></tr>';
}
?>
</table>
<?php
echo printPageNavigation ( '?m=clients', $page, $num_pages, $offset, $limit, $count, 'clients' );
?>
