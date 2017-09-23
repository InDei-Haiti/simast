<?php
/**
 * Created by JetBrains PhpStorm.
 * User: stig
 * Date: 07.07.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 */

require_once($AppUI->getModuleClass('wizard'));
/* function parse_size($size) {
	$unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
	$size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
	if ($unit) {
		// Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
		return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
	}
	else {
		return round($size);
	}
} */
$extension = '.xlsx';
function collectVisualcolumns ($indent = 0){
	global $localStorage,$objReader,$ds,$coords,$usageCase;
	$final = '';
	if (isset($_SESSION['tmp_cache']) && $_SESSION['tmp_cache'] != ''){
		$cacheName = $_SESSION['tmp_cache'];
		/* echo $cacheName;
		echo file_exists($localStorage . $cacheName . $extension);
		echo $localStorage . $cacheName . $extension; */
		$extension = '.xlsx';
		echo $localStorage . $cacheName . $extension;
		echo ini_get('memory_limit');
		if (file_exists($localStorage . $cacheName . $extension) && is_readable($localStorage . $cacheName . $extension)) {
			$objPHPExcel = PHPExcel_IOFactory::load($localStorage . $cacheName . $extension);
			
			$sheets = $objPHPExcel->getAllSheets();
			$rows = array();
			$sheetschild = array();
			foreach ($sheets as $objWorksheet){
				
				$highestRow = $objWorksheet->getHighestRow();
				$highestColumn = $objWorksheet->getHighestColumn();
				$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
				$sheetschild[$objPHPExcel->getIndex($objWorksheet)] = $objWorksheet->getTitle();
				for($col = 0; $col <= $highestColumnIndex; ++$col) {
					$tcolname = trim($objWorksheet->getCellByColumnAndRow($col, (1+$indent))->getValue());
					if ($tcolname!=''){
						$rows[$objPHPExcel->getIndex($objWorksheet)][$col] = $tcolname;
					}
				}	
			}
			
			$formId = $_GET['idfw'];
			$wz = new Wizard('import');
			$wz->loadFormInfo($formId);
			$childformId = $wz->loadchildForm($formId);
			$fields = $wz->showFieldsImport();
			$tpl = new Templater(DP_BASE_DIR . '/modules/wizard/import_columns.html');
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
		   $preheadsection = '<tr>
								<td style="text-align: right;"><b>Section: </b></td><td  style="text-align: left;">%s</td>
							</tr>
			   				<tr>
								<td style="text-align: right;">Sheet</td>
								<td style="text-align: left;">
									<select name="section[%s]"  class="sheet" onchange="populateSelect(\'for_%s\',\'sf_%s\',$(this).val());">
										<option value="-1"> -- Select sheet --</option>
										%s
									</select>
		   							<select id="for_%s" name="foreign[%s]">
										<option value="-1"> -- Select foreign column --</option>
									</select>
		   							<select name="key[%s]">
										<option value="-1"> -- Select parent column --</option>
		   								%s
									</select>
								</td>
							</tr>';
		   
		   $preheadsectionchild = '<tr>
								<td style="text-align: right;"><b>Section: </b></td><td  style="text-align: left;">%s</td>
							</tr>
			   				<tr>
								<td style="text-align: right;">Sheet</td>
								<td style="text-align: left;">
									<select name="child[%s][section][%s]"  class="sheet" onchange="populateSelect(\'for_%s\',\'sf_%s\',$(this).val());">
										<option value="-1"> -- Select sheet --</option>
										%s
									</select>
		   							<select id="for_%s" name="child[%s][foreign][%s]">
										<option value="-1"> -- Select foreign column --</option>
									</select>
		   							<select name="child[%s][key][%s]" class="%s">
										<option value="-1"> -- Select parent column --</option>
									</select>
								</td>
							</tr>';
		   
		   $preheadchildform = '<tr>
								<td style="text-align: left;"><b>Child Form: </b></td><td  style="text-align: left;">%s</td>
							</tr>
			   				<tr>
								<td style="text-align: right;">Sheet</td>
								<td style="text-align: left;">
									<select name="child[%s][sheet]" class="sheet" onchange="populateSelect(\'for_%s\',\'sf_%s\',$(this).val());populateSelect(\'for_%s\',\'%s\',$(this).val());">
										<option value="-1"> -- Select sheet --</option>
										%s
									</select>
		   							<select id="for_%s" name="child[%s][formforeign]" onchange="">
										<option value="-1"> -- Select foreign column --</option>
									</select>
		   							<select name="child[%s][formkey]">
										<option value="-1"> -- Select parent column --</option>
		   								%s
									</select>
								</td>
							</tr>';
		   
		   $preselector = '
						<tr>
							<td style="text-align: right;">%s</td>
							<td style="text-align: left;">
								<select name="%s" class="%s chosen">
									<option value="-1"> -- Select column --</option>
									%s
								</select>
							</td>
						</tr>';
			$optionSample = '<option value="%d">%s</option>';
			$optionCSample = '<option value="%s">%s</option>';
			$options = array();
			$sheetoptions = array();;
			foreach ($rows[0] as $colid => $colName) {
				if ($colName !== '') {
					$options[] = sprintf($optionSample, $colid, $colName);
				}
			}
			foreach ($sheetschild as $sheetid => $sheetname){
				if($sheetid!=0)
					$sheetoptions[] =  sprintf($optionSample, $sheetid, $sheetname);
			}
			$sheetoptions = join("",$sheetoptions);
			$options = join("", $options);

			
			$rowstr = array();
			$i = 1;
			$otms = array();
			$notms = array();
			$childforms = array();
			$tmp = sprintf($preselector, 'Creation Date', 'dbs[entry_date]','', $options);
			$notms[] = $tmp;
			$tmp = sprintf($preselector, 'Creation User', 'dbs[user_creator]','', $options);
			$notms[] = $tmp;
			$tmp = sprintf($preselector, 'Last update', 'dbs[last_update_date]','', $options);
			$notms[] = $tmp;
			$tmp = sprintf($preselector, 'Last User Update', 'dbs[user_last_update]','', $options);
			$notms[] = $tmp;
			if (count($fields['notms']) > 0) {
				foreach ($fields['notms'] as $nitem) {
					if($nitem['raw']['type']!=='note'){
						$tmp = sprintf($preselector, $nitem['title'], 'dbs[' . $nitem['fld'] . ']','', $options);
						$notms[] = $tmp;
					}
				}
				
			}
			
			if (count($fields['otms'])) {
				foreach ($fields['otms'] as $key => $ot) {
					$otms[] = sprintf($preheadsection, $ot['name'],$ot['dbfld'],$ot['dbfld'],$ot['dbfld'], $sheetoptions,$ot['dbfld'],$ot['dbfld'],$ot['dbfld'],$options);
					foreach ($ot['fields'] as $fisub) {
						if($nitem['raw']['type']!=='note'){
							$otms[] = sprintf($preselector, $fisub['title'], 'dbs[section]['.$ot['dbfld'].']'.'[' . $fisub['fld'] . ']', 'sf_'.$ot['dbfld'],'');
						}
					}
				}
			}
			
			
			if(count($childformId)>0){
				foreach ($childformId as $childid){
					$wzchild = new Wizard('import');
					$wzchild->loadFormInfo($childid);
					$fieldschild = $wzchild->showFieldsImport();
					$childforms[] = sprintf($preheadchildform, $wzchild->formName,'wform_'.$childid,'wform_'.$childid,'wform_'.$childid,'wform_'.$childid,'sfc_wform_'.$childid, $sheetoptions,'wform_'.$childid,'wform_'.$childid,'wform_'.$childid,$options);
					$tmp = sprintf($preselector, 'Creation Date', 'child[wform_'.$childid.'][dbs][entry_date]','sf_'.'wform_'.$childid, '');
					$childforms[] = $tmp;
					$tmp = sprintf($preselector, 'Creation User', 'child[wform_'.$childid.'][dbs][user_creator]','sf_'.'wform_'.$childid, '');
					$childforms[] = $tmp;
					$tmp = sprintf($preselector, 'Last update', 'child[wform_'.$childid.'][dbs][last_update_date]','sf_'.'wform_'.$childid, '');
					$childforms[] = $tmp;
					$tmp = sprintf($preselector, 'Last User Update', 'child[wform_'.$childid.'][dbs][user_last_update]','sf_'.'wform_'.$childid, '');
					$childforms[] = $tmp;
					
					if (count($fieldschild['notms']) > 0) {
						foreach ($fieldschild['notms'] as $nitem) {
							if($nitem['raw']['type']!=='note'){
								$tmp = sprintf($preselector, $nitem['title'], 'child[wform_'.$childid.'][dbs][' . $nitem['fld'] . ']','sf_'.'wform_'.$childid, '');
								$childforms[] = $tmp;
							}	
						}
					}
					
					if (count($fieldschild['otms'])) {
						foreach ($fieldschild['otms'] as $key => $ot) {
							$childforms[] = sprintf($preheadsectionchild, $ot['name'],'wform_'.$childid,$ot['dbfld'],'wform_'.$childid.'_'.$ot['dbfld'],'wform_'.$childid.'_'.$ot['dbfld'], $sheetoptions,'wform_'.$childid.'_'.$ot['dbfld'],'wform_'.$childid,$ot['dbfld'],'wform_'.$childid,$ot['dbfld'],'sfc_wform_'.$childid);
							foreach ($ot['fields'] as $fisub) {
								if($nitem['raw']['type']!=='note'){
									$childforms[] = sprintf($preselector, $fisub['title'], 'child[wform_'.$childid.'][dbs][section]['.$ot['dbfld'].']'.'[' . $fisub['fld'] . ']', 'sf_wform_'.$childid.'_'.$ot['dbfld'],'');
								}
							}
						}
					}
				}
			}
			
			$tpl->OTMS = join('', $otms);
			$tpl->nonOTMS = join("", $notms);
			$tpl->CHILDFORM = join("", $childforms);
			$tpl->IDFW = $formId;
			$tpl->ROWS = json_encode($rows);
			$final =  $tpl->output(true,false);
		}
	}
	return $final;
}

function searchChild($worksheet,$column,$searchValue){
	$foundInCells = array();
	foreach($worksheet->getRowIterator() as $srow => $srowObj){
		if($srow >= 2){
			$cell = $worksheet->getCellByColumnAndRow((int)$column,$srow);
			//echo $cell->getFormattedValue(). '=='. $searchValue.' <br/>';
			if (($searchValue != null && $searchValue != '') && strcasecmp(strtolower(strval($cell->getFormattedValue())), strtolower(strval($searchValue))	) == 0) {
				$foundInCells[$srow] = $srowObj;
			}
		}
	}
	return $foundInCells;
}

function getChildForm($objPHPExcel,$objWorksheet,$fkey,$fchild,$rowparent){
	$childform = array();
	$secval = $fchild["sheet"];
	if ((int)$secval != -1) {
		$formWorksheet = $objPHPExcel->getSheet($secval);
		$rowObjforms = array();
		$rowObjforms = searchChild($formWorksheet, $fchild['formforeign'], $objWorksheet->getCellByColumnAndRow((int)$fchild['formkey'], $rowparent)->getFormattedValue());
		if(is_array($rowObjforms)){
			foreach($rowObjforms as $rowidx => $rowObjform){
				$dbMapForm = $fchild['dbs'];
				$tarRowForm = array();
				$columnsToReadform = array_values($fchild['dbs']);
				foreach($rowObjform->getCellIterator() as $rcolform => $colObjform){
					$cellValue = "";
					if ($rcolform >= 0 && in_array($rcolform, $columnsToReadform)){
						$cell = $formWorksheet->getCellByColumnAndRow($rcolform, $rowidx);
						$cellValue = $cell->getFormattedValue();
						$tVal = trim($cellValue);
						$tarRowForm[$rcolform] = $tVal;
					}
				}
				foreach ($dbMapForm as $fld => $cvalue) {
					if(!is_array($cvalue)){
						if ((int)$cvalue !== -1) {
							$childform[$fld] =  $tarRowForm[$cvalue];
						}
					}
				}
				
				$section = $fchild["section"];
				if(count($section)>0){
					foreach ($section as $sec => $secval){
						if ((int)$secval === -1) {
							continue;
						}
						$childWorksheet = $objPHPExcel->getSheet($secval);
						$rowObjchilds = searchChild($childWorksheet, $fchild['foreign'][$sec], trim($objWorksheet->getCellByColumnAndRow((int)$fchild['key'][$sec], $rowidx)->getFormattedValue()));
						$sectionidx = 0;
						foreach($rowObjchilds as $rowObjchild){
							
							$dbMapChild = $dbMapForm['section'][$sec];
							$tarRowchild = array();
							$columnsToReadchild = array_values($dbMapChild);
							foreach($rowObjchild->getCellIterator() as $rcolchild => $colObjchild){
								if ($rcolchild >= 0 && in_array($rcolchild, $columnsToReadchild)){
									$cell = $childWorksheet->getCellByColumnAndRow($rcolchild, $rowObjchild->getRowIndex());
									$cellValue = $cell->getFormattedValue();
									$tVal = trim($cellValue);
									$tarRowchild[$rcolchild] = $tVal;
								}
							}
							foreach ($dbMapChild as $dbKeychild => $dbColchild) {
								if(!is_array($dbColchild)){
									if ((int)$dbColchild !== -1) {
										$childform[$sec][$sectionidx][$dbKeychild] = $tarRowchild[$dbColchild];
									}
								}
							}
							$sectionidx++;
						}
				
					}
						
						
				}
			}
		}
	}
	/* echo '<pre>';
	var_dump($childform);
	echo '</pre>'; */
	return $childform;
}



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
	//echo $localStorage . $cacheName . $extension;
	if (move_uploaded_file($_FILES['qfile']['tmp_name'], $localStorage . $cacheName . $extension)){
		//echo $localStorage . $cacheName . $extension;
		echo collectVisualcolumns();
	}else{
		//echo 'Failed to upload file';
		return;
	}
}elseif(isset($_GET['vidn']) && (int)$_GET['vidn'] > 0 && count($_FILES) == 0 && $_GET['mode'] === 'read'){
	$coords = $_SESSION['coords'];
	echo collectVisualcolumns((int)$_GET['vidn']);
} elseif ($_GET['mode'] === 'write') {
	/* echo '<pre>';
	var_dump($_POST);
	echo '</pre>'; */
	//exit;
	$formId = $_GET['idfw'];
	$cacheName = $_SESSION['tmp_cache'];
	$vindent = (int)$_POST['vindent'];
	$section = $_POST['section'];
	$fchilds = $_POST['child'];
	$columnsToRead = array_values($_POST['dbs']);// Prendre uniquement les valeurs dans le POST[dbs]
	
	foreach($columnsToRead as $ctr){
		if ((int)$ctr >= 0){
			$pureColumnsToRead[]=$ctr; //Trier entierement les columns qui ont ete selectionne pour le MAPPING
		}
	}
	
	if ($cacheName != '' && file_exists($localStorage . $cacheName . $extension)) {
		//we work only with existing files
		$errorPHPExcel = new PHPExcel();
		
		$objPHPExcel = PHPExcel_IOFactory::load($localStorage . $cacheName . $extension);
		$objWorksheet = $objPHPExcel->getSheet(0);
		
		$rows = array();
		
		$dbMap = $_POST['dbs'];
		$errors = array();
		$isError = false;
		$xmode = 'add';
		$wz = new Wizard($xmode, null, null, null);
		$wz->loadFormInfo($formId);
		$rowClients = array();
		$iii = 0;
		$rowForDelete = array();
		$sectionTodelete = array();
		$counterArray = array();

		foreach($objWorksheet->getRowIterator() as $rowObj){
		//while($objWorksheet->getRowIterator()->){
		
			$chunksEmpty = 0;
			/* if($rowObj->getRowIndex() < (2 + $vindent)){
				echo $rowObj->getRowIndex();
				echo '<pre>';
				var_dump($rowObj);
				echo '</pre>';
			} */
			if ($rowObj->getRowIndex() >= (2 + $vindent)){
				try {
					$tarRow = array();
					foreach($rowObj->getCellIterator() as $rcol => $colObj){
						$cellValue = "";
						if ($rcol >= 0 && in_array($rcol, $columnsToRead)){
							$tVal = trim($objWorksheet->getCellByColumnAndRow($rcol, $rowObj->getRowIndex())->getValue());
							$cell = $objWorksheet->getCellByColumnAndRow($rcol, $rowObj->getRowIndex());
							$cellValue = $cell->getFormattedValue();
							$tVal = trim($cellValue);
							if ($tVal == '' ){
								$tVal = null;
							}
							$tarRow[$rcol] = $tVal;
							if ($tVal == '' ){
								++$chunksEmpty;
							}
						}
					}
					if(count($tarRow)){
						$rowClient = array();
						foreach ($dbMap as $dbKey => $dbCol) {
							if(!is_array($dbCol)){
								if ((int)$dbCol !== -1) {
									$rowClient[$dbKey] = $tarRow[$dbCol];
								}
							}
						}
						$res = false;
							
						if(count($rowClient)){
							if(!isset($counterArray[$objWorksheet->getTitle()]['count']))
								$counterArray[$objWorksheet->getTitle()]['count'] = 0;
							$counterArray[$objWorksheet->getTitle()]['count'] += 1;
							if(count($section)>0){
								foreach ($section as $sec => $secval){
									if ((int)$secval === -1) {
										continue;
									}
									$rowClient['key_ui'] = trim($objWorksheet->getCellByColumnAndRow((int)$_POST['key'][$sec], $rowObj->getRowIndex())->getFormattedValue());
									$childWorksheet = $objPHPExcel->getSheet($secval);
									$rowObjchilds = searchChild($childWorksheet, $_POST['foreign'][$sec], trim($objWorksheet->getCellByColumnAndRow((int)$_POST['key'][$sec], $rowObj->getRowIndex())->getFormattedValue()));
									$sectionindex = array();
									$dbMapChild = $dbMap['section'][$sec];
									$columnsToReadchild = array_values($dbMapChild);
									$iii = 0;
									$sectionidx = 0;
									foreach($rowObjchilds as $rowObjchild){
										
										$tarRowchild = array();
										$sectionindex[] = $rowObjchild->getRowIndex();
										$sectionObj = array();
										foreach($rowObjchild->getCellIterator() as $rcolchild => $colObjchild){
											$cellValue = "";
											
											if ($rcolchild >= 0 && in_array($rcolchild, $columnsToReadchild)){
												
												$cell = $childWorksheet->getCellByColumnAndRow($rcolchild, $rowObjchild->getRowIndex());
												$cellValue = $cell->getFormattedValue();
												$tVal = trim(strval($cellValue));
												$tarRowchild[$rcolchild] = $tVal;
											}
										}
										
										foreach ($dbMapChild as $dbKeychild => $dbColchild) {
											
											if(!is_array($dbColchild)){
												if ((int)$dbColchild !== -1) {
													$sectionObj[$dbKeychild] = $tarRowchild[$dbColchild];
													//$rowClient[$sec][$sectionidx][$dbKeychild] = $tarRowchild[$dbColchild];
													
												}
											}
										}
										
										$rowClient[$sec][$sectionidx] = $sectionObj;
										/* if(!isset($counterArray[$objWorksheet->getTitle()]['section'][$childWorksheet->getTitle()]))
											$counterArray[$objWorksheet->getTitle()]['section'][$childWorksheet->getTitle()] = 0;
										$counterArray[$objWorksheet->getTitle()]['section'][$childWorksheet->getTitle()] +=1;
										$sectionTodelete[$childWorksheet->getTitle()][]=$rowObjchild->getRowIndex(); */
										$sectionidx++;
									}
								}
							}
							$childs = array();
							if(count($fchilds)>0){
								foreach ($fchilds as $fkey => $fchild){
									$childs[$fkey] = getChildForm($objPHPExcel,$objWorksheet,$fkey,$fchild,$rowObj->getRowIndex());
								}
							}

							$res = $wz->saveFormData($rowClient,0);
							
							/* if($rowObj->getRowIndex()<=5){
								echo '<pre>';
								var_dump($rowClient);
								echo '</pre>';
							}
							
							if($rowObj->getRowIndex()==5)
								break; */
							$pid = $res;
							$cid = $res;
							if($pid > 0){
								//$rowForDelete[$objWorksheet->getTitle()][]=$rowObj->getRowIndex();
							}
							if($res){
								
								/* $q = new DBQuery();
								$q->addTable('wform_77');
								$q->addInsert('fld_2', '3');
								$q->addInsert('fld_3', '1500');
								$q->addInsert('fld_10', $AppUI->user_id);
								$q->addInsert('user_creator', $AppUI->user_id);
								$q->addInsert('entry_date', date("Y-m-d"));
								$q->addInsert('last_update_date', date("Y-m-d"));
								$q->addInsert('user_last_update', $AppUI->user_id);
								$q->addInsert('ref', $res);
								$q->exec(); */
								
								if(count($childs)>0)
									foreach ($childs as $childk => $child){
									if(count($child)>0){
										$xmode = 'add';
										$wzf = new Wizard($xmode, null, null, null);
										$fid = str_replace('wform_', '', $childk);
										$wzf->loadFormInfo($fid);
										$child['ref'] = $res;
										$res = $wzf->saveFormData($child,0);
										if($res > 0){
											//$sheetCh = $objPHPExcel->getSheet($fchild["sheet"]);
											//$rowForDelete[][$sheetCh->getTitle()][]=$rowObj->getRowIndex();
										}
										/* if($res){
										 $q = new DBQuery();
										 $q->addTable('form_master');
										 $q->addQuery('task_id');
										 $q->addWhere('id='.$fid);
										 $task_id = $q->loadResult();
										 if($task_id){
										 $date_entry = date("Y-m-d");
										 $sql = "INSERT INTO `beneficieries`(`date_entry`, `task_id`, `form_id`, `registry_id`) VALUES ('".$date_entry."',".$task_id.",".$formId.",".$cid.")";
										 db_exec($sql);
										 //$rowClient['child'][$childk] = $child;
										 }
					
										} */
											
									}
								}
							}else{
									
							}
							
							
							/* if($pid > 0){
							 $objWorksheet->removeRow($rowObj->getRowIndex());
									
							 //$sectionindex
							 } */
							//$rowClients[] = $rowClient;
						}
							
						/* if ($res) {
							$isError = false;
						} else {
							$isError = true;
							$error = array();
							$error['Line'] = $rowObj->getRowIndex();
							$error['Row'] = $rowClient;
							$error['Type'] = 'Saving';
							$error['Msg'] = $msg;
							$errors[] = $error;
						} */
							
					}			
				}catch (Exception $e){
					//$isError = true;
				}
			}
			
		}
		if($isError){
			$AppUI->setMsg ( "Some data in this file are incorrect to import", UI_MSG_ERROR );
			//$AppUI->redirect ();
		}else{
			//$AppUI->redirect ();
		}
		//var_dump($rowClient);
		if(count($rowForDelete)>0){
			/* foreach ($rowForDelete as $sheetname => $deletingRows){
				$objPHPExcel->getSheetByName($sheetname)->removeRow($deletingRows[0],count($deletingRows));
				
			}
			foreach ($sectionTodelete as $sheetname => $deletingRows){
				$objPHPExcel->getSheetByName($sheetname)->removeRow($deletingRows[0],count($deletingRows));
			}
			$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
			$objWriter->save($localStorage . $cacheName."_error_" . $extension); */
		}
	
		/* if($isError){
			if(count($objWorksheet->getRowIterator())==count($errors)){
				$AppUI->setMsg ( "All lines data in this file are incorrect", UI_MSG_ERROR );
				//$AppUI->redirect ();
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
			//$AppUI->redirect ();
		} */
		unset($_SESSION['tmp_cache']);
	}
}else{
	/*var_dump($_GET);echo '<br/>';
	var_dump($_FILES);echo '<br/>';
	$upload_max = ini_get('upload_max_filesize');//parse_size(ini_get('upload_max_filesize'));
	echo ini_get('post_max_size');echo '<br/>';
	echo $upload_max;echo '<br/>';*/
	echo "Unable to upload file to server";
	return;
}
global $myspeed;
$myspeed->addJs('/modules/wizard/form_usagew.js','file');
$myspeed->addJs('indenter ();','code');
$myspeed->addJs('regions.init ()','code');

