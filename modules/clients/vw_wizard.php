<?php
global $AppUI, $client_id, $obj,$baseDir;

require_once $AppUI->getModuleClass('wizard');
if(isset($_GET['tab'])){
	$_SESSION['selected_tab']=$_GET['fid']=$_SESSION['wiz_tab'][(int)$_GET['tab']];
	$_GET['task_id'] = $_GET['fid']=$_SESSION['wiz_tab'][(int)$_GET['tab']];
}else{
	$_GET['tab']=array_search($_SESSION['selected_tab'],$_SESSION['wiz_tab']);
	$_GET['fid']=$_SESSION['wiz_tab'][(int)$_GET['tab']]=$_SESSION['selected_tab'];
}

include_once ($baseDir.'/modules/wizard/form_use.php');

?>
