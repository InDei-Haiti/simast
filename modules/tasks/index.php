<?php /* TASKS $Id: index.php 5702 2008-05-06 22:28:35Z merlinyoda $ */
global $localStorage;
if (! defined ( 'DP_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}


if($_GET['mode'] == 'uploadfcli'){
	
	/* if (isset($_FILES['file']) && !$_FILES['file']['error'] && $_FILES['file']['name'] != '') {
		

		if($fobj->file_id > 0){
			echo json_encode(array('id'=>$fobj->file_id,'file_name'=>$upload['name']));
		}
	} */
	$cacheName = md5(time());
	$ext = pathinfo($_FILES['excelfile']['tmp_name'], PATHINFO_EXTENSION);
	if (move_uploaded_file($_FILES['excelfile']['tmp_name'], $localStorage . $cacheName . '.'.$ext)){
		$response = array(
			'status' => 'OK',
			'token' => $cacheName,
			'filename' => $_FILES['excelfile']['tmp_name']
		);
	}else{
		$response = array(
			'status' => 'FAILED',
		);
	}
	$response = array(
			'status' => 'OK',
			'token' => $cacheName,
			'filename' => $_POST['excelfile']
	);
	echo json_encode($response);
	exit();
}



$AppUI->savePlace ();
$perms = & $AppUI->acl ();
// retrieve any state parameters
$user_id = $AppUI->user_id;
if ($perms->checkModule ( 'admin', 'view' )) { // Only sysadmins are able to change users
	if (dPgetParam ( $_POST, 'user_id', 0 ) != 0) { // this means that 
		$user_id = dPgetParam ( $_POST, 'user_id', 0 );
		$AppUI->setState ( 'user_id', $_POST ['user_id'] );
	} else if ($AppUI->getState ( 'user_id' )) {
		$user_id = $AppUI->getState ( 'user_id' );
	} else {
		$AppUI->setState ( 'user_id', $user_id );
	}
}




if (isset ( $_POST ['f'] )) {
	$AppUI->setState ( 'TaskIdxFilter', $_POST ['f'] );
}
//$f = $AppUI->getState ( 'TaskIdxFilter' ) ? $AppUI->getState ( 'TaskIdxFilter' ) : array('myunfinished');
$f = $AppUI->getState ( 'TaskIdxFilter' ) ? $AppUI->getState ( 'TaskIdxFilter' ) : array ('-1' );

if (isset ( $_REQUEST ['f2'] )) {
	$AppUI->setState ( 'CompanyIdxFilter', $_REQUEST ['f2'] );
}
$f2 = $AppUI->getState ( 'CompanyIdxFilter' ) ? $AppUI->getState ( 'CompanyIdxFilter' ) : array ('-1' );

if (isset ( $_GET ['project_id'] )) {
	$AppUI->setState ( 'TaskIdxProject', $_GET ['project_id'] );
}
if (isset ( $_POST ['sector'] )) {
	$AppUI->setState ( 'SectorIdxFilter', $_POST ['sector'] );
}
$sector = $AppUI->getState ( 'SectorIdxFilter' ) ? $AppUI->getState ( 'SectorIdxFilter' ) : NULL;

if (isset ( $_POST ['selected_project_id'] )) {
	$AppUI->setState ( 'ProjectIdxFilter', $_POST ['selected_project_id'] );
}
$selected_project_id = $AppUI->getState ( 'ProjectIdxFilter' ) ? $AppUI->getState ( 'ProjectIdxFilter' ) : NULL;

$project_id = $AppUI->getState ( 'TaskIdxProject' ) ? $AppUI->getState ( 'TaskIdxProject' ) : 0;

if ($_POST ['clear'] == "clear") {
	$f = array ('-1' );
	$AppUI->setState ( 'TaskIdxFilter', $f );
	$f2 = array ('-1' );
	$AppUI->setState ( 'CompanyIdxFilter', $f2 );
	$project_id = 0;
	$AppUI->setState ( 'TaskIdxProject', $project_id );
	$sector = null;
	$AppUI->setState ( 'SectorIdxFilter', $sector );
	$selected_project_id = null;
	$AppUI->setState ( 'ProjectIdxFilter', $selected_project_id );

}

global $filshow;

$filshow=false;

// get CCompany() to filter tasks by company
require_once ($AppUI->getModuleClass ( 'companies' ));
$obj = new CCompany ( );
$companies = $obj->getAllowedRecords ( $AppUI->user_id, 'company_id,company_name', 'company_name' );
$filters2 = arrayMerge ( array ('-1' => $AppUI->_ ( 'All Agencies', UI_OUTPUT_RAW ) ), $companies );

/*$q = new DBQuery ( );
$q->addTable ( 'administrative_regions' );
$q->addQuery ( 'region_id, region_name' );
$q->addWhere ( 'region_parent = 0' );
$country_list = arrayMerge ( array ("-1" => 'All Countries' ), $q->loadHashList () );*/

// setup the title block
//$titleBlock = new CTitleBlock ( 'Activities', '', $m, "$m.$a" );


$titleBlock = new CTitleBlock ( '', '', $m, "$m.$a" );

// patch 2.12.04 text to search entry box
if (isset ( $_POST ['searchtext'] )) {
	$AppUI->setState ( 'searchtext', $_POST ['searchtext'] );
}

$titleBlock->show ();

$titleBlock = new CTitleBlock ( '', 'shim.gif' );
$sel_str = "selected='selected'";

/*$self2html = "<select name='f2[]' size=3 multiple class='text' style='width: 200px'>";
//if (isset ( $_POST ['f2'] )) {
//	$tvar = $_POST ['f2'];
//}

$iz = 0;
foreach ( $filters2 as $pl => $pname ) {
	if (isset ( $f2 )) {
		if (in_array ( $pl, $f2 )) {
			if($pl != -1){
				$filshow=true;
			}
			$dstr = $sel_str;
		} else {
			$dstr = "";
		}
	} elseif ($pl == "all") {
		$dstr = $sel_str;
	} else {
		$dstr = "";
	}
	if ($iz == 0) {
		$hstyle = 'style="font-weight: bold;"';
	} else {
		$hstyle = '';
	}
	$self2html .= '<option value="' . $pl . '"' . $hstyle . ' ' . $dstr . '>' . $pname . '</option>';
	$iz ++;
}
$self2html .= "</select>";*/

$self2html = '';


$titleBlock->addCell ();
// Let's see if this user has admin privileges
if (getPermission ( 'admin', 'view' )) {
	//$titleBlock->addCell ( $AppUI->_ ( 'Projects' ) . ':' );
	$proj = & new CProject ( );
	$deny = $proj->getDeniedRecords ( $AppUI->user_id );
	/*$sql = 'SELECT p.project_id, p.project_name FROM projects AS p';
	if ($deny) {
		$sql .= ' WHERE p.project_id NOT IN (' . implode ( ',', $deny ) . ')';
	}
	$sql .= ' ORDER BY p.project_name';
	$projects = db_loadHashList ( $sql, 'project_id' );
	$p [0] = $AppUI->_ ( 'All Projects' );
	foreach ( $projects as $proj ) {
		$p [$proj [0]] = $proj [1];
	}
	if ($project_id) {
		$p [$project_id] = $AppUI->_ ( '[same project]' );
	}*/
	
	//natsort ( $p );
	//$project_list = $p;
	//$project_list = $perms->getPermittedUsers('tasks');
	/*$titleBlock->addCell(arraySelect($project_list, 'selected_project_id[]', ('size="3" multiple class="text" style="width: 200px;"' )/*' onChange="document.projectIdForm.submit();"'),
	  /*                               $selected_project_id, false), '','');*/
	//'<form action="?m=tasks" method="post" name="projectIdForm">','</form>');
	

	//$selphtml = "<select name='selected_project_id[]' size=3 multiple class='text' style='width: 200px'>";
	/*if (isset ( $_POST ['selected_project_id'] )) {
		$tvar = $_POST ['selected_project_id'];
	}*/
	/*$iz = 0;
	foreach ( $project_list as $pl => $pname ) {
		if (isset ( $selected_project_id )) {
			if (in_array ( trim ( $pl ), $selected_project_id )) {
				if($pl != '-1'){
					$filshow = true;
				}
				$dstr = $sel_str;
			} else {
				$dstr = "";
			}
		} else {
			if ($pl == 0) {
				$dstr = $sel_str;
			} else {
				$dstr = "";
			}
		}
		if ($iz == 0) {
			$hstyle = 'style="font-weight: bold;"';
		} else {
			$hstyle = '';
		}
		$selphtml .= '<option value="' . $pl . '"'. $hstyle.' '.$dstr.'>' . $pname . '</option>';
		$iz++;
	}
	$selphtml .= '</select>';*/

	$selphtml ='';
}
if ($canEdit && $project_id) {
	/*	$titleBlock->addCell(('<input type="submit" class="button" value="' . $AppUI->_('new activity') 
	                      . '">'), '', 
						 ('<form action="?m=tasks&a=addedit&task_project=' . $project_id 
	                      . '" method="post">'), '</form>');
*/
}

//$titleBlock->show();


if (dPgetParam ( $_GET, 'inactive', '' ) == 'toggle')
	$AppUI->setState ( 'inactive', $AppUI->getState ( 'inactive' ) == - 1 ? 0 : - 1 );
$in = $AppUI->getState ( 'inactive' ) == - 1 ? '' : 'in';

$sector_list = arrayMerge ( array (- 1 => 'All Sectors' ), dPgetSysVal ( 'SectorType' ) );


unset ( $tvar );

$selshtml = "<select name='sector[]' size=3 multiple class='text' style='width: 200px'>";

$iz=0;
foreach ( $sector_list as $pl => $pname ) {
	if (isset ( $sector )) {
		if (in_array ( $pl, $sector )) {
			if($pl != '-1'){
				$filshow = true;
			}
			$dstr = $sel_str;
		} else {
			$dstr = "";
		}
	} else {
		if ($pl == - 1) {
			$dstr = $sel_str;
		} else {
			$dstr = "";
		}
	}
	if($iz == 0){
		$hstyle='style="font-weight: bold;"';
	}else{
		$hstyle='';
	}
	$selshtml .= '<option value="' . $pl . '"'.$hstyle.' '.$dstr.'>' . $pname . '</option>';
	$iz++;
}
$selshtml .= '</select>';


unset ( $tvar );

$country_id = $f;
$selchtml = ''; //buildProvinceList('f');
// use a new title block (a new row) to prevent from oversized sites
//$titleBlock = new CTitleBlock('', 'shim.gif');

//'<form action="?m=tasks" method="post" name="taskFilter">', '</form>');onChange="document.taskFilter.submit();"
/****************************************************************/

if($filshow){
	$tcolor='green';
	$ttext='ON';
}else{
	$tcolor='red';
	$ttext='OFF';
}
$titleBlock->addCell('<div style="color: '.$tcolor.';">Filters '.$ttext.'</div>');

$titleBlock->addCell ();
$titleBlock->addCell ( $self2html, '', '<form action="?m=tasks" method="post" name="companyFilter">', '' );

if($selphtml!= ''){
	$titleBlock->addCell ();
	$titleBlock->addCell ( $selphtml );
}

$titleBlock->addCell ();
$titleBlock->addCell ( $selshtml );

$titleBlock->addCell ();
$titleBlock->addCell ( $selchtml );

//'<form action="?m=tasks" method="post" name="sectorFilter">', '</form>');onChange="document.sectorFilter.submit();
$titleBlock->addCell ( "<input type='submit' value='show' class='button'><br>
						<input type='submit' value='clear' name='clear' class='button' style='margin-top: 5px;'>", '', '', '</form>' );

//$titleBlock->addCrumb('?m=tasks&inactive=toggle', 'show '.$in.'active activities');
//$titleBlock->addCrumb('?m=tasks&a=tasksperuser', 'activities per user');
//$titleBlock->addCrumb('?m=projects&a=reports', 'reports');


$titleBlock->show ();

$titleBlock = new CTitleBlock ( '', 'shim.gif' );
$search_text = $AppUI->getState ( 'searchtext' ) ? $AppUI->getState ( 'searchtext' ) : '';
$search_text = dPformSafe ( $search_text, true );
//. ' onChange="document.searchfilter.submit();" value="' . $search_text 
//$titleBlock->addCell ( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $AppUI->_ ( 'Search' ) . ':' );
$titleBlock->addCell ( ('<input type="text" class="text" SIZE="20" name="keyword"' . ' onChange="document.searchfilter.submit();" value="" title="' . $AppUI->_ ( 'Search in name and description fields' ) . '"/>&nbsp;&nbsp;<input type="submit" class="button" value="search" title="' . $AppUI->_ ( 'Search in name and description fields' ) . '"/>'), '', '<form action="?m=smartsearch" method="post" id="searchfilter">', '</form>' );

$titleBlock->show ();

// include the re-usable sub view
$min_view = false;
include (DP_BASE_DIR . '/modules/tasks/tasks.php');

?>
