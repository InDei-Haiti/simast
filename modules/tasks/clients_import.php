<?php
/**
 * Created by JetBrains PhpStorm.
 * User: stig
 * Date: 07.07.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 */

//require_once($AppUI->getModuleClass('wizard'));
require_once($AppUI->getModuleClass('clients'));
$task_id = dPgetParam($_REQUEST, 'task_id',0);
if($task_id){
	$q= new DBQuery();
	$q->addQuery('task_name');
	$q->addTable('tasks');
	$q->addWhere('task_id = "'.(int)$task_id.'"');
	$task_name=$q->loadResult();
}
function collectVisualcolumns ($task_id,$duplicate,$indent = 0){
	global $localStorage,$objReader,$usageCase;
	$final = '';
	if (isset($_SESSION['tmp_cache']) && $_SESSION['tmp_cache'] != ''){
		$cacheName = $_SESSION['tmp_cache'];
		if (file_exists($localStorage . $cacheName . '.xls') && is_readable($localStorage . $cacheName . '.xls')) {
			$objPHPExcel = PHPExcel_IOFactory::load($localStorage . $cacheName . '.xls');
			$objWorksheet = $objPHPExcel->getActiveSheet();

			$highestRow = $objWorksheet->getHighestRow();
			$highestColumn = $objWorksheet->getHighestColumn();
			$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			$rows = array();

			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$tcolname = trim($objWorksheet->getCellByColumnAndRow($col, (1+$indent))->getValue());
				if ($tcolname!=''){
					$rows[$col] = $tcolname;
				}
			}

			//$formId = $ds->getUsedForm($coords['dataset_id'], 'id');
			//$formtitle = $ds->getUsedForm($coords['dataset_id'], 'title');
			/* $wz = new Wizard('import', array('dataset' => (int)$coords['dataset_id'], 'instance' => $coords['instance_id'],
			                                  'ucs'     => $usageCase
			), 0);
			$wz->loadFormInfo($formId);*/
			$tpl = new Templater(DP_BASE_DIR . '/modules/tasks/clients_import_columns.html');
			/* $tpl->dataset = $coords['dataset_id'];
			$tpl->instance = $coords['instance_id'];
			$tpl->form = $formId;
			
			$tpl->FORM_NAME = $formtitle;
			$tpl->usagecase = $usageCase;
			$tpl->vindent = $indent; */
			$tpl->FILENAME = $_FILES['excelfile']['name'];
			$tpl->TASK = $task_id;
			$tpl->DUPLICATE = $duplicate;
			//$tpl->TITLEFORM = "Import Beneficieries To "+$task_name;
			$preNotm = '
	<tr>
		<td style="text-align: right;">%s</td>
		<td style="text-align: left;">%s</td>
	</tr>';

			$preselector = '
	<tr>
		<td style="text-align: right;">%s</td>
		<td style="text-align: left;">
			<select name="%s">
				<option value="-1"> -- Select column --</option>
				%s
			</select>
		</td>
	</tr>';
			$optionSample = '<option value="%d">%s</option>';
			$options = array();
			foreach ($rows as $colid => $colName) {
				if ($colName !== '') {
					$options[] = sprintf($optionSample, $colid, $colName);
				}
			}

			$options = join("", $options);
			
			$fields = array(
					'client_first_name'=>'First Name',
					'client_last_name'=>'Last Name',
					'client_nickname'=>'Nickname',
					'client_gender'=>'Gender',
					'client_birthday'=>'Day Of Birth',
					'client_education_level'=>'Education Level',
					'client_health_status'=>'Health Status',
					'client_cin'=>'NIF/CIN',
					'client_other_id'=>'Other Identification',
					'client_occupation'=>'Occupation',
					'client_place_of_birth'=>'Place of Birth',
					'client_address'=>'Address',
					'client_phone1'=>'Phone 1',
					'client_phone2'=>'Phone 2',
					'client_phone3'=>'Phone 3',
					'client_marital_status'=>'Marital Status',
					'client_profession'=>'Profession',
					'client_status_house'=>'Status in the house',
					'client_number_rooms'=>'How many rooms',
					'client_administration_section'=>'Code Communal Section',
					'client_notes'=>'Notes',
					
			
			);

			//$fields = $wz->showFieldsImport();

			/*$otms = array();
			$notms = array();
			if (count($fields['notms']) > 0) {
				foreach ($fields['notms'] as $nitem) {
					$tmp = $wz->outputField(str_replace("fld_", "", $nitem['fld']), $nitem['raw'], false);
					$notms[] = $tmp; //sprintf($preNotm, $nitem['title'], $tmp);
				}
			}
			if (count($fields['otms'])) {
				foreach ($fields['otms'] as $fisub) {
					$otms[] = sprintf($preselector, $fisub['title'], 'dbs[' . $fisub['fld'] . ']', $options);
				}
			}

			$tpl->OTMS = join('', $otms);
			$tpl->nonOTMS = join("", $notms);*/
			$rowstr = array();
			foreach ($fields as $key => $val){
				$rowstr[] = sprintf($preselector, $val, 'dbs[' . $key . ']', $options);
			}
			$tpl->ROWSTR = join('', $rowstr);
			$final =  $tpl->output(true,false);
		}
	}
	return $final;
}

//print_r ($_FILES);

//my_query('DELETE FROM xdata_level_3 WHERE xdata_instance = "' . (int)$_POST["instance"] . '" and xdata_dataset = "' . (int)$_POST["dataset"] . '"')
//or die(my_error());
require_once DP_BASE_DIR . '/XClasses/PHPExcel/IOFactory.php';


/** @var  $objReader PHPExcel_Reader_Excel2007 */
$objReader = PHPExcel_IOFactory::createReader('Excel2007');

$objReader->setReadDataOnly(true);

global $localStorage,$coords;
$localStorage = DP_BASE_DIR . '/files/tmp/';


$usageCase = $_GET['ucs'];

$destUrl = '';
if ($usageCase == 'lists') {
	$ds = new Lists();
	$destUrl = 'lists&level=2';
} else {
	//$ds = new DataSets();
	$destUrl = 'data&level=3';
}

//copy file to own storage, get first row column names and output it for client choice
if ($_GET['mode'] === 'read' && count($_FILES) === 1 && !isset($_GET['vidn'])) {

	$cacheName = md5(time());
    $duplicate = '';
    if(isset($_POST['duplicate']))
    	$duplicate = $_POST['duplicate'];
	//$coords = magic_json_decode($_POST['exdata'], true);

	$_SESSION['tmp_cache'] = $cacheName;
	//$_SESSION['coords'] = $coords;
	/* is_uploaded_file($filename)
	echo 'merde'; */
	
	if (move_uploaded_file($_FILES['excelfile']['tmp_name'], $localStorage .$cacheName . '.'.$pathinfo['extension'])){
		echo "<div style='width: 90%;margin: auto;'>".collectVisualcolumns($task_id,$duplicate)."</div>";
	}else{
		echo 'Failed to upload file';
		return;
	}
}elseif(isset($_GET['vidn']) && (int)$_GET['vidn'] > 0 && count($_FILES) == 0 && $_GET['mode'] === 'read'){
	//case for treating indents, for now only vertical
	$coords = $_SESSION['coords'];
	$duplicate = '';
	if(isset($_POST['duplicate']))
		$duplicate = $_POST['duplicate'];
	echo collectVisualcolumns($task_id,$duplicate,(int)$_GET['vidn']);
} elseif ($_GET['mode'] === 'write') {
	$cacheName = $_SESSION['tmp_cache'];
	$vindent = (int)$_POST['vindent'];
	$columnsToRead = array_values($_POST['dbs']);
	
	foreach($columnsToRead as $ctr){
		if ((int)$ctr >= 0){
			$pureColumnsToRead[]=$ctr;
		}
	}
	if ($cacheName != '' && file_exists($localStorage . $cacheName . '.xls')) {
		//we work only with existing files
		$objPHPExcel = PHPExcel_IOFactory::load($localStorage . $cacheName . '.xls');
		$objWorksheet = $objPHPExcel->getActiveSheet();

		//$rowIterator = $objWorksheet->getRowIterator();


		//$highestRow = $objWorksheet->getHighestRow();
		//$highestColumn = $objWorksheet->getHighestColumn();
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
		$rows = array();
		$duplicate = false;
		if(isset($_POST['duplicate']) && $_POST['duplicate']=='duplicate')
			$duplicate = true;
		var_dump($objWorksheet->getRowIterator());
		exit();
		foreach($objWorksheet->getRowIterator() as $row => $rowObj){
			$chunksEmpty = 0;
			//echo $row." => ";
			if ($row >= (2 + $vindent)){
		//for ($row = (2+$vindent); $row <= $highestRow; ++$row) {
				//$rows[$row] = array();
				$tarRow = array();
				//for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				//foreach($columnsToRead as $colid => $rcol){
				foreach($rowObj->getCellIterator() as $rcol => $colObj){
					if ($rcol >= 0 && in_array($rcol, $columnsToRead)){
						$tVal = trim($objWorksheet->getCellByColumnAndRow($rcol, $row)->getValue());
						$tarRow[$rcol] = $tVal;
						if ($tVal == '' ){ //&& in_array($rcol, $columnsToRead)
							++$chunksEmpty;
						}
					}
				}
				//print_r($tarRow);
				//echo "<br/><br/>";
				//if (count ($tarRow) > 0 && count($tarRow) >= count ($pureColumnsToRead) && $chunksEmpty < count($pureColumnsToRead)){
					$rows[$row] = $tarRow;
				//}
			}
		}
		/* echo "<table class='tbl'>";
			$dbMap = $_POST['dbs'];
			$finalMap = array();
		echo "<tr>";
		foreach ($dbMap as $dbKey => $dbCol) {
			if ((int)$dbCol !== -1) {
				$finalMap[$dbKey] = $dbCol;
			}
			echo "<th nowrap=\"nowrap\" width=\"40%\">".$dbKey."</th>";
		}
		echo "</tr>"; */
		$dbMap = $_POST['dbs'];
		$errors = array();
		$isError = false;
		foreach ($rows as $r => $v){
			if(is_array($v)){
				//echo "<tr>";
				$rowClient = array();
				foreach ($dbMap as $dbKey => $dbCol) {
					if ((int)$dbCol !== -1) {
						$rowClient[$dbKey] = $v[$dbCol];
						//echo "<td>".$v[$dbCol]."</td>";
						//$finalMap[$dbKey] = $dbCol;
					}
						
				}
				if(count($rowClient)>0){
					$client = new CClient();
					if($duplicate){
						$q = new DBQuery();
						$q->addTable('clients');
						$q->addQuery("client_id");
						$q->addWhere("client_cin='".$rowClient['client_cin']."'");
						$q->limit = 1;
						$res = $q->loadResult();
						if($res)
							$client->load($res);
					}
					if (! $client->bind ( $rowClient )) {
						$isError = true;
						$error = array();
						$error['Line'] = $r;
						$error['Row'] = $rowClient;
						//$AppUI->setMsg ( $client->getError (), UI_MSG_ERROR );
						//$AppUI->redirect ();
						$error['Type'] = 'Binding';
						$error['Msg'] = $client->getError ();
						$errors[] = $error;
						
					}elseif (($msg = $client->store ())) {
						$isError = true;
						$error = array();
						$error['Line'] = $r;
						$error['Row'] = $rowClient;
						//$AppUI->setMsg ( $msg, UI_MSG_ERROR );
						//$AppUI->redirect (); // Store failed don't continue?
						$error['Type'] = 'Saving';
						if(is_array($msg)){
							$msg = implode(" ", $msg);
						}
						$error['Msg'] = $msg;
						$errors[] = $error;
					}else{
						if($task_id){
							$q = new DBQuery();
							$q->addTable("activity_clients");
							$q->addQuery("activity_clients_client_id");
							$q->addWhere("activity_clients_activity_id=".$task_id." AND activity_clients_client_id".$cid);
							$q->limit = 1;
							$res = $q->loadResult();
							if(!$res){
								$sql = "INSERT INTO `activity_clients`(`activity_clients_activity_id`, `activity_clients_client_id`) VALUES ($task_id,$client->client_id)";
								db_exec($sql);
							}
						}
					}
					
					//var_dump($rowClient);
					//echo '<br/><br/><br/>';
				}
				//echo "</tr>";
			}
		} 
		
		if($isError){
			if(count($rows)==count($errors)){
				$AppUI->setMsg ( "All lines data in this file are incorrect", UI_MSG_ERROR );
				$AppUI->redirect ();
			}
			echo "<h1 style='color:red'>Error import csv</h1>";
			echo "<table class='tbl'>";
			echo "<tr><th nowrap=\"nowrap\" width=\"40%\">Line</th>
					<th nowrap=\"nowrap\" width=\"40%\">Type</th>
					<th nowrap=\"nowrap\" width=\"40%\">Msg</th>
					</tr>";
			foreach ($errors as $error){
				echo "<tr>";
				echo "<td>".$error['Line']."</td>";
				echo "<td>".$error['Type']."</td>";
				echo "<td>".$error['Msg']."</td>";
				echo "</tr>";
			}
			echo "</table>";
		}else{
			$AppUI->redirect ();
		}
		
		//echo "</table>";
		/*$coords = array('dataset_id' => (int)$_POST['dataset'], 'instance_id' => (int)$_POST['instance']);

		//$ds = new DataSets();
		$formId = $ds->getUsedForm($coords['dataset_id'], 'id');
		$wz = new Wizard('import', array('dataset'  => $coords['dataset_id'],
		                                 'instance' => $coords['instance_id'],
		                                 'ucs'      => $usageCase //'dataset'
		), 0);
		$wz->loadFormInfo($formId);
		$dbMap = $_POST['dbs'];
		$finalMap = array();
		foreach ($dbMap as $dbKey => $dbCol) {
			if ((int)$dbCol !== -1) {
				$finalMap[$dbKey] = $dbCol;
			}
		}
		foreach ($rows as &$irow) {
			$wz->importRow($irow, $finalMap);
		}

		$extras = array();
		foreach ($_POST as $key => $val) {
			if (strpos($key, 'fld_') !== false ) {
				$extras[$key] = $val;
			}
		}

		$savedRows = $wz->massSaveToDB($extras, $finalMap);

		unset($objReader, $wz);

		if (file_exists($localStorage . $cacheName . '.xls')) {
			unlink($localStorage . $cacheName . '.xls');
		}*/
		unset($_SESSION['tmp_cache']);

		//$AppUI->redirect('m='./*data&level=3*/$destUrl.'&dataset=' . $coords['dataset_id'] . '&instance=' . $coords['instance_id']);
	}

}else{
	echo "Unable to upload file to server";
	return;
}
/* global $myspeed;
$myspeed->addJs('/modules/wizard/form_usagew.js','file');
$myspeed->addJs('indenter ();','code');
$myspeed->addJs('regions.init ()','code'); */

