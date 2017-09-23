<?php
/**
 * Created by PhpStorm.
 * User: stig
 * Date: 26.11.13
 * Time: 14:45
 */

/*global $moduleScripts;
$moduleScripts[]='/modules/outputs/gchart.js';
$moduleScripts[]='/modules/outputs/jquery9.min.js';
$moduleScripts[]="https://maps.googleapis.com/maps/api/js?key=AIzaSyAXBklWqmHO8dubF66h9VEBlRQnuLS9P_g&sensor=false";
*/
require_once($AppUI->getModuleClass('wizard'));
function getFieldParent($idParent){
	$trow = array();
	$sql='select id,title,parent_id FROM form_master WHERE id='.$idParent;
	$res=mysql_query($sql);
	while($trow=mysql_fetch_assoc($res)){
		/* $fieldsdata = getFields($trow['id'],null);
			if($fieldsdata && count($fieldsdata)>0)
				$trow['fieldsv'] = $fields;
				$all[] = $trow; */
			if($trow['parent_id'])
				$trow['parent_id'] = getFieldParent($trow['parent_id']);
			$formId = $trow['id'];
			$wz = new Wizard('import');
			$wz->loadFormInfo($formId);
			$fields = $wz->showFieldsImport();
			$fielda = array();
			if (count($fields['notms'])) {
				foreach ($fields['notms'] as $fisub) {
					if($fisub['raw']['type']!=='note'){
						$fielda['section'] = null;
						//$fielda[$fisub['fld']] = $fisub['title'];
						$fielda['fields'][$fisub['fld']]['title'] = $fisub['title'];
						$fielda['fields'][$fisub['fld']]['type'] = $fisub['raw']['type'];
						if(isset($fisub['raw']['sysv'])){
							$fielda['fields'][$fisub['fld']]['sysv'] = $fisub['raw']['sysv'];
							$fielda['fields'][$fisub['fld']]['sysdata'] = $wz->getValues($fisub['raw']['type'],$fisub['raw']['sysv'], false, true,false,false,'wform_'.$formId,$fisub['fld']);
						
							$fielda['fields'][$fisub['fld']]['html'] = 'html';//$wz->getValues($fisub['raw']['type'],$fisub['raw']['sysv']);//$fisub['raw']['sysv'];
						}else
							$fielda['fields'][$fisub['fld']]['sysv'] = null;
					}
					
				}
			}
			if (count($fields['otms'])) {
				foreach ($fields['otms'] as $inx => $sectio) {
					$fielda['section'][$inx]['name'] = $sectio['name'];
					foreach ($sectio["fields"] as $iny => $infof) {
						$fielda['section'][$inx]['fields'][$infof['fld']]['title'] = $infof['title'];
						/* $fielda['fields'][$fisub['fld']]['type'] = $fisub['raw']['type'];
						 if(isset($fisub['raw']['sysv'])){
						 $fielda['fields'][$fisub['fld']]['sysv'] = $fisub['raw']['sysv'];
						 $fielda['fields'][$fisub['fld']]['sysdata'] = $wz->getValues($fisub['raw']['type'],$fisub['raw']['sysv'], false, true,false,false,'wform_'.$formId,$fisub['fld']);
						 }else{
						 $fielda['fields'][$fisub['fld']]['sysv'] = null;
						} */
					}
				}
			}
			if(count($fielda)>0)
				$trow['fieldsv'] = $fielda;
			return $trow;
			//$all[] = $trow;
	}
	return $trow;
}
function getFields($idProject){
	//if(!$allbool){
		$all = array();
		$sql='select id,title,parent_id FROM form_master WHERE project_id='.$idProject.' AND forregistration=0';
		$res=mysql_query($sql);
		while($trow=mysql_fetch_assoc($res)){
			/* $fieldsdata = getFields($trow['id'],null);
				if($fieldsdata && count($fieldsdata)>0)
					$trow['fieldsv'] = $fields;
					$all[] = $trow; */
				$formId = $trow['id'];
				if($trow['parent_id'])
					$trow['parent_id'] =  getFieldParent($trow['parent_id']);
				$wz = new Wizard('import');
				$wz->loadFormInfo($formId);
				$fields = $wz->showFieldsImport();
				$fielda = array();
				if (count($fields['notms'])) {
					foreach ($fields['notms'] as $fisub) {
						//$fielda[$fisub['fld']] = $fisub['title'];
						if($fisub['raw']['type']!=='note'){
							$fielda['fields'][$fisub['fld']]['title'] = $fisub['title'];
							$fielda['fields'][$fisub['fld']]['type'] = $fisub['raw']['type'];
							if(isset($fisub['raw']['sysv'])){
								$fielda['fields'][$fisub['fld']]['sysv'] = $fisub['raw']['sysv'];
								$fielda['fields'][$fisub['fld']]['sysdata'] = $wz->getValues($fisub['raw']['type'],$fisub['raw']['sysv'], false, true,false,false,'wform_'.$formId,$fisub['fld']);
							}else
								$fielda['fields'][$fisub['fld']]['sysv'] = null;
						}	
					}
				}
				
				if (count($fields['otms'])) {
					foreach ($fields['otms'] as $inx => $sectio) {
						$fielda['section'][$inx]['name'] = $sectio['name'];
						foreach ($sectio["fields"] as $iny => $infof) {
							if($infof['type']!='note'){
								$fielda['section'][$inx]['fields'][$infof['fld']]['title'] = $infof['title'];
								$fielda['section'][$inx]['fields'][$infof['fld']]['type'] = $infof['type'];
								if(isset($infof['sysv'])){
									$fielda['section'][$inx]['fields'][$infof['fld']]['sysv'] = $fisub['raw']['sysv'];
									$fielda['section'][$inx]['fields'][$infof['fld']]['sysdata'] = $wz->getValues($fisub['raw']['type'],$fisub['raw']['sysv'], false, true,false,false,'wform_'.$formId,$fisub['fld']);
								}else{
									$fielda['section'][$inx]['fields'][$infof['fld']]['sysv'] = null;
								}
							}
							 
						}
					}
				}
				if(count($fielda)>0)
					$trow['fieldsv'] = $fielda;
				$all[] = $trow;
		}
		
		/*$formId = $idf;//$trow['id'];
		 $wz = new Wizard('import'/*, array('dataset' => (int)$coords['dataset_id'], 'instance' => $coords['instance_id'],
		 'ucs'     => $usageCase
		 ), 0*);
		 $wz->loadFormInfo($formId);
		 $fields = $wz->showFieldsImport();
		 $fielda = array();
		 if (count($fields['notms'])) {
		 foreach ($fields['notms'] as $fisub) {
		 $fielda[$fisub['fld']] = $fisub['title'];
		 }
		 }
		 if(count($fielda)>0){
		
		 return $fielda;
		 }*/
	//}
	/*else{
		$all = array();
		$sql='select id,title,parent_id FROM form_master WHERE project_id='.$idtask.'  AND forregistration=0';
		$res=mysql_query($sql);
		while($trow=mysql_fetch_assoc($res)){
			/* $fieldsdata = getFields($trow['id'],null);
				if($fieldsdata && count($fieldsdata)>0)
					$trow['fieldsv'] = $fields;
					$all[] = $trow; //
			$formId = $trow['id'];
			if($trow['parent_id'])
				$trow['parent_id'] =  getFieldParent($trow['parent_id']);
			$wz = new Wizard('import');
			$wz->loadFormInfo($formId);
			$fields = $wz->showFieldsImport();
			$fielda = array();
			if (count($fields['notms'])) {
				foreach ($fields['notms'] as $fisub) {
					//$fielda[$fisub['fld']] = $fisub['title'];
	
					$fielda['fields'][$fisub['fld']]['title'] = $fisub['title'];
					$fielda['fields'][$fisub['fld']]['type'] = $fisub['raw']['type'];
					if(isset($fisub['raw']['sysv']))
						$fielda['fields'][$fisub['fld']]['sysv'] = $fisub['raw']['sysv'];
					else
						$fielda['fields'][$fisub['fld']]['sysv'] = null;
	
				}
			}
			if(count($fielda)>0)
				$trow['fieldsv'] = $fielda;
			$all[] = $trow;
		}
		
		///$formId = $idf;//$trow['id'];
		 //$wz = new Wizard('import'//, array('dataset' => (int)$coords['dataset_id'], 'instance' => $coords['instance_id'],
		 'ucs'     => $usageCase
		 ), 0//);
		 $wz->loadFormInfo($formId);
		 $fields = $wz->showFieldsImport();
		 $fielda = array();
		 if (count($fields['notms'])) {
		 foreach ($fields['notms'] as $fisub) {
		 $fielda[$fisub['fld']] = $fisub['title'];
		 }
		 }
		 if(count($fielda)>0){
		
		 return $fielda;
		 }///
	}*/
	
	return $all;
}
if($_GET['mode']=='loadtask' && (int)$_GET['pid'] > 0){
	$sql='select task_id,task_name FROM tasks WHERE task_project='.(int)$_GET['pid'];
	$res=mysql_query($sql);
	if($res && mysql_num_rows($res)  > 0){
		while($trow=mysql_fetch_assoc($res)){
			$all[] = $trow;
		}
		echo json_encode($all);
	}else{
		echo ' fail '.$sql.' '.mysql_error();
	}
	return ;
}elseif($_GET['mode']=='loadforms' && isset($_GET['pid'])){
		$all = array();
		$q = new DBQuery;
		$project = $_GET['pid'];
		
		if($project){
			$sql='select id,title,parent_id FROM form_master WHERE project_id='.$project.' AND forregistration=1';
			$res=mysql_query($sql);
			if($res && mysql_num_rows($res)  > 0){
				while($trow=mysql_fetch_assoc($res)){
					$formId = $trow['id'];
					$wz = new Wizard('import');
					$wz->loadFormInfo($formId);
					$fields = $wz->showFieldsImport();
					$fielda = array();
					if (count($fields['notms'])) {
						foreach ($fields['notms'] as $fisub) {
							if($fisub['raw']['type']!=='note'){
								$fielda['fields'][$fisub['fld']]['title'] = $fisub['title'];
								$fielda['fields'][$fisub['fld']]['type'] = $fisub['raw']['type'];
								if(isset($fisub['raw']['sysv'])){
									$fielda['fields'][$fisub['fld']]['sysv'] = $fisub['raw']['sysv'];
									$fielda['fields'][$fisub['fld']]['sysdata'] = $wz->getValues($fisub['raw']['type'],$fisub['raw']['sysv'], false, true,false,false,'wform_'.$formId,$fisub['fld']);
								}else
									$fielda['fields'][$fisub['fld']]['sysv'] = null;
							}
							
						}
					}
					
					if (count($fields['otms'])) {
						foreach ($fields['otms'] as $inx => $sectio) {
							$fielda['section'][$inx]['name'] = $sectio['name'];
							foreach ($sectio["fields"] as $iny => $infof) {
								$fielda['section'][$inx]['fields'][$infof['fld']]['title'] = $infof['title'];
								$fielda['section'][$inx]['fields'][$infof['fld']]['type'] = $infof['type'];
								if(isset($infof['sysv'])){
									$fielda['section'][$inx]['fields'][$infof['fld']]['sysv'] = $infof['sysv'];
									$fielda['section'][$inx]['fields'][$infof['fld']]['sysdata'] =  $wz->getValues($infof['type'],$infof['sysv'], false, true,false,false,'wform_'.$formId,$fisub['fld']);
								}else{
									$fielda['section'][$inx]['fields'][$infof['fld']]['sysv'] = null;
								}
							}
						}
					}
					
					if(count($fielda)>0)
						$trow['fieldsv'] = $fielda;
		
					$all[] = $trow;
				}
			}
		}
		
		$all1 = getFields($_GET['pid']);
		//$all = arrayMerge($all, $all1);
		$all = array_merge($all, $all1);
		if($all && count($all)>0){
			//$all = array_reverse($all);
			echo json_encode($all);
		}else{
			echo ' fail '.$sql;
		}
		
		
	//}
	
	/* else{
		$all = array();
		$q = new DBQuery;
		
		$q->addTable('tasks');
		$q->addQuery('task_project');
		$q->addWhere('task_id='.$_GET['tid']);
		
		$project = $q->loadResult();
		if($project){
			$sql='select id,title,parent_id FROM form_master WHERE project_id='.$project.' AND forregistration=1';
			$res=mysql_query($sql);
			if($res && mysql_num_rows($res)  > 0){
				while($trow=mysql_fetch_assoc($res)){
					//$trow['fieldsda'] =
		
					$formId = $trow['id'];
					$wz = new Wizard('import'/*, array('dataset' => (int)$coords['dataset_id'], 'instance' => $coords['instance_id'],
							'ucs'     => $usageCase
					), 0///);
					$wz->loadFormInfo($formId);
					$fields = $wz->showFieldsImport();
					$fielda = array();
					if (count($fields['notms'])) {
						foreach ($fields['notms'] as $fisub) {
							$fielda['fields'][$fisub['fld']]['title'] = $fisub['title'];
							$fielda['fields'][$fisub['fld']]['type'] = $fisub['raw']['type'];
							if(isset($fisub['raw']['sysv'])){
								$fielda['fields'][$fisub['fld']]['sysv'] = $fisub['raw']['sysv'];
								$fielda['fields'][$fisub['fld']]['sysdata'] = $wz->getValues($fisub['raw']['type'],$fisub['raw']['sysv']);//$fisub['raw']['sysv'];
							}else
								$fielda['fields'][$fisub['fld']]['sysv'] = null;
						}
					}
					if(count($fielda)>0)
						$trow['fieldsv'] = $fielda;
		
					$all[] = $trow;
					// $formId = $_GET['idfw'];
						$wz = new Wizard('import'/, array('dataset' => (int)$coords['dataset_id'], 'instance' => $coords['instance_id'],
						'ucs'     => $usageCase
						), 0*); //
				}
			}
		}
		
		$all1 = getFields(false,$_GET['tid']);
		//$all = arrayMerge($all, $all1);
		$all = array_merge($all, $all1);
		if($all && count($all)>0){
			//$all = array_reverse($all);
		
			echo json_encode($all);
		}else{
			echo ' fail '.$sql;
		}
	}*/ 
	
	return ;
}
		
$q = new DBQuery();
$q->addTable("projects");
$q->addQuery("project_id,project_name");
$rows = $q->loadHashList();

$projects = '';
foreach($rows as $k => $v){
	$projects .= '<label><input type="radio" name="dataSrc" value="'.$k.'">&nbsp;&nbsp;'.$v.'</label>';
}
?>

<!-- <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAXBklWqmHO8dubF66h9VEBlRQnuLS9P_g&sensor=false"></script>
 --><?php 
$tpl = new Templater(__DIR__."/gchart.tpl");

global $fielder;
buildTableDataDemand();
//$areaParts = buildGroupSelect($fielder);

$tpl->set('datasets',@join("",$areaParts[1]));
$tpl->set('lists', @join("",$areaParts[2]));
$tpl->set('PROJECTS', $projects);

/**
 * Get ids of admin levels sysvals
 */
$sql = 'select id, title from svsets where title in ("Region","Municipality","Village")';
$res = mysql_query($sql);

$alevels = array();
if (mysql_num_rows($res) > 0){
	while ($ra = my_fetch_assoc($res)){
		$alevels[$ra['id']] = $ra['title'];
	}
}
$tpl->set("alevels",json_encode($alevels));

$tpl->output(true);