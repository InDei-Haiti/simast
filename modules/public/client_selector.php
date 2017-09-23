<?php
require_once $AppUI->getModuleClass('clients');
require_once($AppUI->getModuleClass('wizard'));
require_once($AppUI->getSystemClass("genericTable"));	
    

	$show_all             = dPgetParam($_REQUEST, 'show_all', 0);
	$fields_all             = dPgetParam($_REQUEST, 'fields', null);
	if($fields_all!=null){
		$fields_all = explode(',', $fields_all);
		if (($key = array_search('', $fields_all)) !== false) {
			unset($fields_all[$key]);
		}
	}
	$task_id           = dPgetParam($_REQUEST, 'task_id', 0);
	$client_id           = dPgetParam($_POST, 'client_id', 0);
	$call_back            = dPgetParam($_GET, 'call_back', null);
	$clients_submited    = dPgetParam($_POST, 'clients_submited', 0);
	$selected_clients_id = dPgetParam($_GET, 'selected_clients_id', '');
	if (dPgetParam($_POST, 'selected_clients_id'))	{
		$selected_clients_id = dPgetParam($_POST, 'selected_clients_id');
	}
	$data_id = $_POST['client_id'];
	
	
	
	$q = new DBQuery;
	
	$q->addTable('tasks');
	$q->addQuery('task_project');
	$q->addWhere('task_id='.$task_id);
	
	$project = $q->loadResult();
	
	$q->clear();
	$q->addTable('form_master');
	$q->addQuery('id');
	$q->addWhere('project_id='.$project.' AND forregistration=1');
	$fuid = $q->loadResult();
	
	
	if($data_id && count($data_id)>0){
		$clients = array();
		
		foreach ($data_id as $index=>$value){
			$clients[] = $value;
		}
		
		if(count($clients)){
			foreach ($clients as $cid){
				/* $client = new CClient();
				$client->load($cid);
				$client->client_activities = explode(',', $client->client_activities);
				
				$client->client_activities[] = $task_id;
				$client->client_activities = array_unique($client->client_activities);
				$client->client_activities = join(',', $client->client_activities);//implode(',', $client->client_activities);
				if($client->client_activities[0]==',')
					$client->client_activities = substr($client->client_activities, 1);
				$client->store(); */
				//$sql = "INSERT INTO 'activity_clients'(`activity_clients_activity_id`, `activity_clients_client_id`) VALUES ($task_id,$cid)";
				/*$q = new DBQuery();
				$q->addTable("activity_clients");
				$q->addQuery("activity_clients_client_id");
				$q->addWhere("activity_clients_activity_id=".$task_id." AND activity_clients_client_id".$cid);
				$q->limit = 1;
				$res = $q->loadResult();
				if(!$res){
					$sql = "INSERT INTO `activity_clients`(`activity_clients_activity_id`, `activity_clients_client_id`) VALUES ($task_id,$cid)";
					db_exec($sql);
				}*/
				$q = new DBQuery();
				$q->addTable("beneficieries");
				$q->addQuery("registry_id");
				$q->addWhere("task_id=".$task_id." AND form_id=".$fuid." AND registry_id=".$cid);
				$q->limit = 1;
				$res = $q->loadResult();
				if(!$res){
					$sql = "INSERT INTO `beneficieries`(`task_id`, `form_id`, `registry_id`) VALUES ($task_id,$fuid,$cid)";
					db_exec($sql);
					$q = new DBQuery();
					$q->addTable('wform_'.$fuid);
					$q->addUpdate('valid', 1);
					$q->addWhere('id='.$cid);
					$q->exec();
				}
			}
		}
		
	}
	if($_GET['fpart'] != ""){
		$part=$_GET['fpart'];
		$partStr='&fpart='.$part;
		$partFunc='"'.$part.'",';
	}else{
		$part='';
		$partStr='';
		$partFunc='';
	}
?>
<script language="javascript">
function setClientIDs (method,querystring)
{
	var URL = 'index.php?m=public&a=client_selector';
    
	var field = document.getElementsByName('client_id[]');
	var selected_clients_id = document.frmClientSelect.selected_clients_id;
	var tmp = new Array();
	
	if (method == 'GET' && querystring){
		URL += '&' + querystring;
	}
	
	var count = 0;
	for (i = 0; i < field.length; i++) {
		if (field[i].checked) {
			tmp[count++] = field[i].value;
		}
	}
	selected_clients_id.value = tmp.join(',');
	//alert(selected_clients_id.value);
	//return false;
    
	if (method == 'GET') {
		URL +=  '&selected_clients_id=' + selected_clients_id.value;
		return URL;
	} else {
		return selected_clients_id;
	}

}

</script>
<?php

function remove_invalid($arr) {
	$result = array();
	foreach ($arr as $val) {
		if (! empty($val) && trim($val) !== '' && is_numeric($val)) {
			$result[] = $val;
		}
	}	
	return $result;
}
//var_dump($selected_clients_id);
//var_dump($clients_submited);
//var_dump($call_back);
if($clients_submited == 1){	
	$call_back_string = !is_null($call_back) ? "window.opener.$call_back(".$partFunc."'$selected_clients_id');" : '';
?>
<script language="javascript">
	<?php echo $call_back_string ?>
	self.close();
</script>

<?php
return ;
}

// Remove any empty elements
$clients_id = remove_invalid(explode(',', $selected_clients_id));
$selected_clients_id = implode(',', $clients_id);

require_once( $AppUI->getModuleClass( 'clients' ) );
$oClnt = new CClient ();
$aClnts = $oClnt->getAllowedRecords ($AppUI->user_id, 'client_id', 'client_id');
$aClnts_esc = array();
foreach ($aClnts as $key => $client) {
	$aClnts_esc[$key] = db_escape($client);
}

$q = new DBQuery;

if (strlen($selected_clients_id) > 0 && ! $show_all && ! $client_id){
	$q->addTable('clients');
	$q->addQuery('client_id');
	$q->addWhere('client_id IN (' . $selected_clients_id . ')');
	$where = implode(',', $q->loadColumn());
	$q->clear();
	if (substr($where, 0, 1) == ',' && $where != ',') { 
		$where = '0'.$where; 
	} else if ($where == ',') {
		$where = '0';
	}
	$where = (($where)?('client_id IN('.$where.')'):'');
} 
/*else if ( ! $company_id ) {
	//  Contacts from all allowed companies
	$where = ("contact_company = ''"
			  ." OR (contact_company IN ('".implode('\',\'' , array_values($aCpies_esc)) ."'))"
			  ." OR ( contact_company IN ('".implode('\',\'', array_keys($aCpies_esc)) ."'))") ;
	$company_name = $AppUI->_('Allowed Companies');
} */
/*else 
{
	// Contacts for this company only
	$q->addTable('companies', 'c');
	$q->addQuery('c.company_name');
	$q->addWhere('company_id = '.$company_id);
	$company_name = $q->loadResult();
	$q->clear();
	/*
		$sql = "select c.company_name from companies as c where company_id = $company_id";
		$company_name = db_loadResult($sql);
	*/
/*	
	$company_name_sql = db_escape($company_name);
	$where = " ( contact_company = '$company_name_sql' or contact_company = '$company_id' )";
}*/

// This should now work on company ID, but we need to be able to handle both
//$q->addTable('clients', 'a');
//$q->leftJoin('companies', 'b', 'company_id = contact_company');
//$q->leftJoin('departments', 'c', 'dept_id = contact_department');
//$q->addQuery('client_id, client_first_name, client_other_name, client_last_name,client_adm_no');
//$q->addQuery('a.*');
//$q->addQuery('company_name');
//$q->addQuery('dept_name');
//$q->addWhere("client_id NOT IN (SELECT activity_clients_client_id FROM activity_clients)");
//if ($where) { // Don't assume where is set. Change needed to fix Mantis Bug 0002056
//	$q->addWhere($where);
//}
//$q->addWhere("(contact_owner = '".$AppUI->user_id."' OR contact_private = '0')");
//$q->addOrder('client_first_name'); // May need to review this.


//$clients = $q->loadHashList('client_id');


$moduleScripts[]='./modules/public/tsjq.js';
$wz = new Wizard(null);
$wz->loadFormInfo($fuid);
$fields = $wz->showFieldsImport();
//
$formsFlds = array();
if (count($fields['notms']) > 0) {
	foreach ($fields['notms'] as $nitem) {
		$formsFlds[$nitem['fld']] = $nitem['title'];
	}
}
$digest = $wz->getDigest();
$q->clear();
$q->addTable('wform_'.$fuid);
$q->addWhere('id NOT IN (SELECT registry_id FROM beneficieries WHERE task_id='.$task_id.' AND form_id='.$fuid.')');
//$q->addWhere($query);
$clients = $q->loadHashList('id');
//var_dump($fields['notms']);
function getSysval($array,$field,$val){
	$result = false;
	if (count($array) > 0) {
		foreach ($array as $nitem) {
			if($nitem['fld']===$field){
				$psv = $nitem["raw"]["sysv"];
				if($psv==='SysCommunes' || $psv==='SysCommunalSection'){
					if($psv==='SysCommunes'){
						$q = new DBQuery();
						$q->addTable("administration_com");
						$q->addQuery("administration_com_name");
						$q->addWhere('administration_com_code="'.$val.'"');
						$result = $q->loadResult();
					}
					
					if($psv==='SysCommunalSection'){
						$q = new DBQuery();
						$q->addTable("administration_section");
						$q->addQuery("administration_section_name");
						$q->addWhere('administration_section_code="'.$val.'"');
						$result = $q->loadResult();
					}
				}else{
					$tab = dPgetSysValSet($psv);
					$result = $tab[$val];
				}
			}
		}
	}
	return $result;
}

?>

<!-- <form action="index.php?m=public&a=client_selector&dialog=1&<?php if(!is_null($call_back)) echo 'call_back='.$call_back.'&'; ?>task_id=<?php echo $task__id.$partStr ?>" method='post' name='frmClientSelect'>
 -->
<form action="index.php?m=public&a=client_selector&dialog=1&task_id=<?php echo $task_id.$partStr ?>" method='post' name='frmClientSelect'>

<input type="submit" value="<?php echo $AppUI->_('Continue'); ?>" onClick="setClientIDs()" class="button" />
<?php
$actual_department = '';
$actual_company    = '';
//$companies_names = array(0 => $AppUI->_('Select a company')) + $aCpies;
/*echo arraySelect($companies_names, 'company_id', 
				 'onchange="document.frmContactSelect.contacts_submited.value=0; '
				 .'setContactIDs(); document.frmContactSelect.submit();"', 
				 0);*/

/*$fields = array( 
		
		'client_nickname'=>'Nickname',
		'client_gender'=>'Gender',
		'client_birthday'=>'Day Of Birth',
		'client_place_of_birth'=>'Place of Birth',
		'client_address'=>'Adress',
		'client_administration_section'=>'Commune',
		'client_administration_section'=>'Communal Section',
		'client_marital_status'=>'Marital Status',
		'client_education_level'=>'Education Level',
		'client_health_status'=>'Health Status',
		'client_profession'=>'Profession',
		'client_occupation'=>'Occupation',
		'client_status_house'=>'Status In The House',
		'client_number_rooms'=>'How Many Rooms',
		
);
$fields1 = $fields;
$fieldsSys = array(
		'client_gender'=>'GenderType',
		'client_marital_status'=>'MaritalStatus',
		'client_education_level'=>'EducationLevel',
		'client_health_status'=>'CaregiverHealthStatus',
);*/
?>

<br>
 <!-- <h4><a href="#" onClick="window.location.href=setClientIDs('GET','dialog=1&<?php if(!is_null($call_back)) echo 'call_back='.$call_back.'&'; ?>show_all=1<?php echo  $partStr;?>');"><?php echo $AppUI->_('Click to view all clients'); ?></a>
 
 </h4> -->
<hr />
<?php $fieldsJs = json_encode($formsFlds);?>

<button type="button" onclick='dialogNewFields("<?php echo $task_id?>",<?php echo $fieldsJs;?>);'>Add Field(s) To Table</button>
<h2><?php echo $AppUI->_('Clients for'); ?> <?php echo $activity_name ?></h2>
<?php 
$gt = new genericTable();
$headers = array();
?>
 <!-- <table id="qtable" class="tablesorter tbl" cellpadding=2 cellspacing=1 border=0>
<thead>
	<tr>
		<th>&nbsp;</th>
 -->		<?php 
 			  $headers['*'] = 'string';
 			  foreach ($digest as $fld){?>
				<!-- <th><?php $headers[$formsFlds[$fld]] = 'string'; //echo $formsFlds[$fld]?></th> -->
				
		<?php }?>
		<?php if($fields_all) foreach($fields_all as $i => $val){  if(!in_array($val, $digest)){?>
			<!-- <th><?php $headers[$formsFlds[$val]]; //echo $formsFlds[$val]?></th> -->
		<?php }}?>
	<!-- </tr>
</thead>
<tbody> -->
<?php	
	$gt->makeHeader($headers);
	$headers_cnt = count($headers);
	$decs = array(
			0=>'<input type="checkbox" name="client_id[]" id="client_##'.($headers_cnt).'##" value="##'.($headers_cnt).'##" ##'.($headers_cnt+1).'## />'
			
	);
	$gt->setDecorators($decs);
	//var_dump($clients);
	foreach($clients as $client_id => $client_data)
	{
		$row_data = array();
		$checked = in_array($client_id, $clients_id) ? 'checked="checked"' : '';
		/*echo "<tr>\n\t".'<td style="width: 30px"><input type="checkbox" name="client_id[]" id="client_'.$client_id.'" value="'.$client_id.'" '.$checked.' /></td>
			<td>'.$client_data['client_cin'].'</td>';
		echo '<td >
				<label for="client_'.$client_id.'" data-skort="'.$client_data['client_last_name'].'">'.$client_data['client_first_name'].' '.$client_data['client_last_name'].'</label>
			</td>';*/
		
		//echo "<tr>\n\t".'<td style="width: 30px"><input type="checkbox" name="client_id[]" id="client_'.$client_id.'" value="'.$client_id.'" '.$checked.' /></td>';
		$row_data[] = '';
		foreach ($digest as $fld){
			if($client_data[$fld]=='-1')
				$client_data[$fld] = '';
			$test =getSysval($fields['notms'],$fld,strval($client_data[$fld]));
			if($test){
				$client_data[$fld] = $test;
			}
			/* echo '<td >
				<label for="client_'.$client_id.'" data-skort="'.$client_data[$fld].'">'.$client_data[$fld].'</label>
			</td>'; */
			$row_data[] = $client_data[$fld];
		}
		if($fields_all) foreach($fields_all as $i => $val){
			if(!in_array($val, $digest)){
			/*if(isset($fieldsSys[$val])){
				$tables = dPgetSysVal($fieldsSys[$val]);
				$client_data[$val] = $tables[$client_data[$val]];
			}*/
				if($client_data[$val]=='-1')
					$client_data[$val] = '';
				$test =getSysval($fields['notms'],$val,strval($client_data[$val]));
				if($test){
					$client_data[$val] = $test;
				}
				/* echo '<td >
					<label for="client_'.$client_id.'" data-skort="'.$client_data[$val].'">'.$client_data[$val].'</label>
				</td>'; */
				$row_data[] = $client_data[$fld];
			}
			
		}
		$row_data[] = $client_id;
		$row_data[] = $checked;
		//var_dump($caregivers[$client_id]);
		//echo "\n</tr>\n";
		//var_dump($row_data);echo '<br/>';
		$gt->fillBody($row_data);
	}
?>
<!-- </tbody>
</table> -->
<?php $gt->compile();?>
<hr />
<input name="clients_submited" type="hidden" value="1" />
<input name="selected_clients_id" type="hidden" value="<?php echo $selected_clients_id; ?>">
<!-- <input type="submit" value="<?php echo $AppUI->_('Continue'); ?>" onClick="setClientIDs()" class="button" /> -->
</form>
<script type="text/javascript" src="/modules/public/add_fields.js"></script>
<script type="text/javascript">
//window.onload=boost;function boost(){$j("#qtable").tablesorter({headers:{0:{sorter:false},2:{sorter: "soname"}},widgets:['fixHead']});}

</script>
