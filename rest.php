<?php
$baseDir = dirname(__FILE__);
require_once $baseDir.'/classes/query.class.php';

if($_GET['mode'] == 'getProjects'){
	/* $q = new DBQuery();
	$q->addTable("projects");
	$q->addQuery('project_id,project_name'); */
	$sql = "SELECT project_id,project_name FROM `projects`";
	$res = mysql_query($sql);
	$data = array();
	if ($res) {
		while($row = mysql_fetch_assoc($res)){
			$data[] = $row;
		}
		echo json_encode($data);
	}else{
		echo 'Failed';
	}

	return;
}

?>

