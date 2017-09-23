<?php
/**
 * Created by JetBrains PhpStorm.
 * User: stig
 * Date: 07.07.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 */

require_once($AppUI->getModuleClass('wizard'));

function collectVisualcolumns ($indent = 0){
	global $localStorage,$objReader,$ds,$coords,$usageCase;
	$final = '';
	if (isset($_SESSION['tmp_cache']) && $_SESSION['tmp_cache'] != ''){
		$cacheName = $_SESSION['tmp_cache'];
		if (file_exists($localStorage . $cacheName . '.xls') && is_readable($localStorage . $cacheName . '.xls')) {
			$objPHPExcel = PHPExcel_IOFactory::load($localStorage . $cacheName . '.xls');
			//var_dump($objPHPExcel->getSheetNames());
			$sheet = $objPHPExcel->getSheetNames();
			
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

			/*$formId = $ds->getUsedForm($coords['dataset_id'], 'id');
			$formtitle = $ds->getUsedForm($coords['dataset_id'], 'title');
			*/
			$formId = $_GET['idfw'];
			$wz = new Wizard('import'/*, array('dataset' => (int)$coords['dataset_id'], 'instance' => $coords['instance_id'],
			                                 'ucs'     => $usageCase
			), 0*/);
			$wz->loadFormInfo($formId);

			$tpl = new Templater(DP_BASE_DIR . '/modules/wizard/import_columns.html');
			//$tpl->dataset = $coords['dataset_id'];
			//$tpl->instance = $coords['instance_id'];
			$tpl->form = $formId;
			$tpl->FILENAME = $_FILES['qfile']['name'];
			$tpl->FORM_NAME = $formtitle;
			$tpl->usagecase = $usageCase;
			$tpl->vindent = $indent;
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

			$fields = $wz->showFieldsImport();
			$rowstr = array();
			$i = 1;
			//foreach ($fields as $key => $val){
				
				//$rowstr[] = sprintf($preselector, $val, 'dbs[' . $key . ']', $options);
			//}
			$otms = array();
			$notms = array();
			if (count($fields['notms']) > 0) {
				foreach ($fields['notms'] as $nitem) {
					/*echo '----------' .' => '.str_replace("fld_", "", $nitem['fld']);
					var_dump($nitem);
					echo '<br/><br/><br/>';*/
					//$tmp = $wz->outputField(str_replace("fld_", "", $nitem['fld']), $nitem['raw'], false);
					$tmp = sprintf($preselector, $nitem['title'], 'dbs[' . $nitem['fld'] . ']', $options);
					$notms[] = $tmp; //sprintf($preNotm, $nitem['title'], $tmp);
					
				}
			}
			//var_dump($notms);
			if (count($fields['otms'])) {
				foreach ($fields['otms'] as $fisub) {
					$otms[] = sprintf($preselector, $fisub['title'], 'dbs[' . $fisub['fld'] . ']', $options);
				}
			}
			
			/*
			 $rowstr = array();
			foreach ($fields as $key => $val){
				$rowstr[] = sprintf($preselector, $val, 'dbs[' . $key . ']', $options);
			}
			$tpl->ROWSTR = join('', $rowstr);
			*/

			$tpl->OTMS = join('', $otms);
			$tpl->nonOTMS = join("", $notms);
			$tpl->IDFW = $formId;
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
	$ds = new DataSets();
	$destUrl = 'data&level=3';
}

//copy file to own storage, get first row column names and output it for client choice
if ($_GET['mode'] === 'read' && count($_FILES) === 1 && !isset($_GET['vidn'])) {

	$cacheName = md5(time());

	$coords = magic_json_decode($_POST['exdata'], true);

	$_SESSION['tmp_cache'] = $cacheName;
	$_SESSION['coords'] = $coords;

	if (move_uploaded_file($_FILES['qfile']['tmp_name'], $localStorage . $cacheName . '.xls')){
		echo collectVisualcolumns();
	}else{
		echo 'Failed to upload file';
		return;
	}
}elseif(isset($_GET['vidn']) && (int)$_GET['vidn'] > 0 && count($_FILES) == 0 && $_GET['mode'] === 'read'){
	//case for treating indents, for now only vertical
	$coords = $_SESSION['coords'];
	echo collectVisualcolumns((int)$_GET['vidn']);
} elseif ($_GET['mode'] === 'write') {
	$formId = $_GET['idfw'];
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
		$xmode = 'add';
		$wz = new Wizard($xmode, null, null, null);
		$wz->loadFormInfo($formId);
		
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
				$res = false;
				if(count($rowClient))
					$res = $wz->saveFormData($rowClient,0);
				if ($res === true) {
					//$AppUI->setMsg(' added', UI_MSG_OK, true);
				} else {
						
					$isError = true;
					$error = array();
					$error['Line'] = $r;
					$error['Row'] = $rowClient;
					//$AppUI->setMsg ( $msg, UI_MSG_ERROR );
					//$AppUI->redirect (); // Store failed don't continue?
					$error['Type'] = 'Saving';
					/* if(is_array($msg)){
					 $msg = implode(" ", $msg);
					 }
					 $error['Msg'] = $msg;*/
					$errors[] = $error;
						
					//$AppUI->setMsg(' error during saving', UI_MSG_ERROR, true);
				}
				
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
	
	
	/*
	if ($cacheName != '' && file_exists($localStorage . $cacheName . '.xls')) {
		//we work only with existing files
		$objPHPExcel = PHPExcel_IOFactory::load($localStorage . $cacheName . '.xls');
		$objWorksheet = $objPHPExcel->getActiveSheet();

		//$rowIterator = $objWorksheet->getRowIterator();


		//$highestRow = $objWorksheet->getHighestRow();
		//$highestColumn = $objWorksheet->getHighestColumn();
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
		///?m=wizard&amp;a=form_use&amp;todo=save&amp;fid=29
		$rows = array();
		foreach($objWorksheet->getRowIterator() as $row => $rowObj){
			$chunksEmpty = 0;
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

				if (count ($tarRow) > 0 && count($tarRow) >= count ($pureColumnsToRead) && $chunksEmpty < count($pureColumnsToRead)){
					$rows[$row] = $tarRow;
				}
			}
		}

		$coords = array('dataset_id' => (int)$_POST['dataset'], 'instance_id' => (int)$_POST['instance']);

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
		///?m=wizard&amp;a=form_use&amp;todo=save&amp;fid=29
		$extras = array();
		foreach ($_POST as $key => $val) {
			if (strpos($key, 'fld_') !== false ) {
				$extras[$key] = $val;
			}
		}

		//$savedRows = $wz->massSaveToDB($extras, $finalMap);

		unset($objReader, $wz);

		if (file_exists($localStorage . $cacheName . '.xls')) {
			unlink($localStorage . $cacheName . '.xls');
		}
		unset($_SESSION['tmp_cache']);

		$AppUI->redirect('m='./*data&level=3$destUrl.'&dataset=' . $coords['dataset_id'] . '&instance=' . $coords['instance_id']);
	}*/

}else{
	echo "Unable to upload file to server";
	return;
}
global $myspeed;
$myspeed->addJs('/modules/wizard/form_usagew.js','file');
$myspeed->addJs('indenter ();','code');
$myspeed->addJs('regions.init ()','code');

