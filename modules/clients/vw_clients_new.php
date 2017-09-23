<?php
//require_once ($AppUI->getModuleClass ( 'counsellinginfo' ));
//require_once ($AppUI->getModuleClass ( 'admission' ));

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
$genderType = dPgetSysVal('GenderType');
$education_level = dPgetSysVal('EducationLevel');
$today = date("Ymd");

if($today > $dPconfig['regular_scan']){
	$sql = 'select client_id from clients where client_obsolete="0" and '.$dPconfig['regular_definition'];
	$res=mysql_query($sql);
	if($res && mysql_num_rows($res) > 0){
		while($clac = mysql_fetch_array($res)){
			$actives[]=$clac[0];
		}
		$actives_sql = join(",",$actives);

		if(strlen($actives_sql) > 0){
			$sup="update clients set client_obsolete='1' where client_status <> '9' and client_id not in ( ".$actives_sql." )";
			$res2= mysql_query($sup);

			if($res2){
				$sup2 = 'update config set config_value="'.$today.'" where config_name="regular_scan"';
				$res3 = mysql_query($sup2);
			}
		}
	}
}

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

$count = 0;//$obj->getCount ( $AppUI->user_type, $AppUI->user_id, $client_type_filter );

$num_pages = ceil ( $count / $limit );
$offset = ($page - 1) * $limit;
if ($offset < 0) {
	$limit = intval ( $count );
	$offset = 0;
}

$where = $AppUI->getState ( 'ClientIdxWhere' ) ? $AppUI->getState ( 'ClientIdxWhere' ) : '%';

if ($where != '%')
	$search = true;

$clientType = true;

if (strncmp ( $currentTabName, "All Beneficieries", strlen ( "All Beneficieries" ) ) == 0)
	$clientType = false;
if (strncmp ( $currentTabName, "Not Applicable", strlen ( "Not Applicable" ) ) == 0)
	$client_type_filter = NULL;
if (strncmp ( $currentTabName, "Not Active", strlen ( "Not Active" ) ) == 0)
	$client_type_filter = 99;
if (strncmp ( $currentTabName, "VCT", strlen ( "VCT" ) ) == 0)
	$client_type_filter = 100;
	//if ($currentTabName == "Not Applicable")
//	$company_type_filter = 0;


$q = new DBQuery ();

//$q->setLimit ( $limit, $offset );
$q->addTable ( 'clients' );
//$q->innerJoin ( 'counselling_info', 'ci', 'ci.counselling_client_id = c.client_id' );
//$q->leftJoin ( 'social_visit', 'sv', 'sv.social_client_id = c.client_id' );
$q->addQuery ( 'DISTINCT (client_id)' );
$q->addWhere ( "client_first_name LIKE '$where%'" );
//$q->addOrder('c.client_entry_date','desc');

//if (count($allowedClients) > 0) { $q->addWhere('c.client_id IN (' . implode(',', array_keys($allowedClients)) . ')'); }
if ((count ( $allowedClinics ) > 0) && ($allowedClinics [0] != NULL)) {
	//$q->addWhere ( 'client_center IN (' . implode ( ',', array_keys ( $allowedClinics ) ) . ')' );
}

if ($clientType) {
	
	$q->addWhere("client_id IN (SELECT activity_clients_client_id FROM activity_clients WHERE activity_clients_activity_id = ".$client_type_filter. ")");
	
	//$q->addWhere($client_type_filter.' in (client_activities)');
	/* if ($client_type_filter > 0 && $client_type_filter < 98) {
		//$q->addWhere('ci.counselling_clinic = '.$client_type_filter ); 
		//$q->addWhere ( 'ci.counselling_clinic = ' . $client_type_filter . ' AND (c.client_status = 1 OR sv.social_client_status IS NULL )' );
		//$q->addWhere ( 'ci.counselling_clinic = ' . $client_type_filter . ' AND (c.client_status = 1 OR c.client_status IS NULL )' );
		//$q->addWhere ( 'c.client_center = ' . $client_type_filter . ' AND (c.client_status ="1" )');//IS NULL OR c.client_status <> "9"
		$q->addWhere ( 'client_center = ' . $client_type_filter . 
			($dPconfig['regular_definition']!= '' ? ' AND ('.$dPconfig['regular_definition'].' )' : '')
		);//IS NULL OR c.client_status <> "9"
		echo 'salut';
		$q->addWhere($client_type_filter.' in (client_activities)');
	} else if ($client_type_filter === NULL) {
		//$q->addWhere( ' (ci.counselling_clinic = '.$client_type_filter . ' OR ci.counselling_clinic IS NULL OR c.client_status <> 1 )'); 
		//$q->addWhere ( '(client_center NOT IN ( SELECT CONCAT_WS(",", clinic_id) FROM clinics)) OR client_center IS NULL ' );
		//$q->addWhere ( '(c.client_center NOT IN ( '.$_SESSION['aclinics'].')) OR c.client_center IS NULL ' );
		//$q->addWhere('client_status <> "9"');
	} else if ($client_type_filter == 98) {
		//$q->addWhere( ' (ci.counselling_clinic = '.$client_type_filter . ' OR ci.counselling_clinic IS NULL OR c.client_status <> 1 )'); 
		//$q->addWhere ( '( client_status IS NOT NULL AND client_status="7") ' );
	} else if ($client_type_filter == 99) {
		if($dPconfig['regular_definition'] != ''){
			$q1 = new DBQuery();
			$q1->addTable('clients');
			$q1->addQuery('client_id');
			//$q1->addWhere($dPconfig['regular_definition']);
			$actives = $q1->loadColumn();
			if(count($actives) > 0){
				$q->addWhere('client_id not in ('.join(',',$actives).')');
			}
		}
		
		//$q->addWhere( ' (ci.counselling_clinic = '.$client_type_filter . ' OR ci.counselling_clinic IS NULL OR c.client_status <> 1 )'); 
		//$q->addWhere ( '( client_status IS NOT NULL AND client_status  NOT IN (1,11,7,9)) ' );
	} elseif ($client_type_filter == 100) {
		//case of VCT only tab
		$q->addWhere ( 'client_status="9"' );
	
	} */
}
//$q->addWhere ( ' (sv.social_id IS NULL OR sv.social_id IN ( SELECT svi.social_id FROM social_visit svi INNER JOIN (SELECT social_client_id, MAX( social_entry_date ) AS social_max_date FROM social_visit GROUP BY social_client_id ) AS s2 ON svi.social_client_id = s2.social_client_id and svi.social_entry_date = s2.social_max_date)) ' );

//$q->addOrder ( $orderby . ' ' . $orderdir .($client_type_filter === 99 ? ', client_obsolete DESC ' : ''));

$sql = $q->prepare ();

//print $sql;
$qid = db_exec ( $sql );
$count = db_num_rows ( $qid );


/*$num_pages = ceil ( $count / $limit );

$offset = ($page - 1) * $limit;

if ($offset < 0) {
	$limit = intval ( $count );
	$offset = 0;
}

$q->setLimit ( $limit, $offset );*/

$rows = $q->loadList ();
//echo printPageNavigation( '?m=companies', $page, $num_pages, $offset, $limit, $count);

//echo printPageNavigation ( '?m=clients', $page, $num_pages, $offset, $limit, $count, 'clients' );

require_once($AppUI->getSystemClass("genericTable"));

$gt = new genericTable();

$headers = array(
	'Name'   => 'string',
	'NIF/CIN'    => 'string',
	'Other Identification'   => 'string',
	'Gender'   => 'string'
);

$gt->makeHeader($headers);

$decs = array(0=>'<a href="/index.php?m=clients&a=view&client_id=##4##">##0##</a>'//,1=>'date',2=>'date'
		
);
$gt->setDecorators($decs);

$s = '';
$CR = "\n"; // Why is this needed as a variable?
$none = true;
$df = $AppUI->getPref ( 'SHDATEFORMAT' );
/*$q = new DBQuery ();
$q->addTable ( 'clinic_location' );
$q->addQuery ( 'clinic_location.clinic_location_id, clinic_location.clinic_location' );
$locations = $q->loadHashList ();*/
$obj = new CClient ();
$bbold = '';

$row_data = array();
;
$GenderType = dPgetSysVal('GenderType');
if(count($rows)){
	$i = 0;
	foreach ( $rows as $rid =>  $row ) {
		$i += 1;
		$obj->reset();
		//echo $i.": ".$row ["client_id"].'<br/> ';
		$obj->load ( $row ["client_id"] );
		$row_data[$rid] = array($obj->getFullname(),$obj->client_cin,$obj->client_other_id,$GenderType[$obj->client_gender],$obj->client_id);
		$gt->fillBody($row_data[$rid]);
		/* var_dump($row_data[$rid]);
		echo '<br/><br/><br/>'; */
	}
}

echo "\n";
if (count($rows) == 0) {
	$gt->addTableHtmlRow($CR . '<tr><td colspan="8">' . $AppUI->_ ( 'No clients available' ) . '</td></tr>');
}
$gt->compile();
//echo printPageNavigation ( '?m=clients', $page, $num_pages, $offset, $limit, $count, 'clients' );
