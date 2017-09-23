<?php /* PROJECTS $Id: vw_files.php 4800 2007-03-06 00:34:46Z merlinyoda $ */
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}

GLOBAL $AppUI, $project_id, $deny, $canRead, $canEdit, $dPconfig;
//global $TView;
//$showProject = false;

$TView = "manager";
require( DP_BASE_DIR.'/modules/files/index_table.php' );
?>
