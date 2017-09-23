<?php
require_once $AppUI->getModuleClass ( 'clients' );
require_once ($AppUI->getModuleClass ( 'wizard' ));
require_once ($AppUI->getSystemClass ( "genericTable" ));

$show_all = dPgetParam ( $_REQUEST, 'show_all', 0 );
$fields_all = dPgetParam ( $_REQUEST, 'fields', null );
if ($fields_all != null) {
	$fields_all = explode ( ',', $fields_all );
	if (($key = array_search ( '', $fields_all )) !== false) {
		unset ( $fields_all [$key] );
	}
}
$task_id = dPgetParam ( $_REQUEST, 'task_id', 0 );
$client_id = dPgetParam ( $_POST, 'client_id', 0 );
$call_back = dPgetParam ( $_GET, 'call_back', null );
$clients_submited = dPgetParam ( $_POST, 'clients_submited', 0 );
$selected_clients_id = dPgetParam ( $_GET, 'selected_clients_id', '' );
if (dPgetParam ( $_POST, 'selected_clients_id' )) {
	$selected_clients_id = dPgetParam ( $_POST, 'selected_clients_id' );
}
$data_rel_id = $_GET ['client_id'];
$add = $_GET ['add'];
$filter = "";
$selected_columns = dPgetParam ( $_GET, 'selected_columns', array () );
$selected_columns = array_reverse ( $selected_columns );
$group_by = dPgetParam ( $_GET, 'group_by', null );
$fuid = ( int ) $_GET ['fid'];
$rfuid = ( int ) $_GET ['relfid'];
$dataid = ( int ) $_GET ['dataid'];
$filterJs = array ();
if (isset ( $_GET ['wform_' . $fuid] )) {
	foreach ( $_GET ['wform_' . $fuid] as $fld => $valfld ) {
		
		$and = '';
		if ($filter !== "") {
			$and = " AND";
		}
		if (count ( $valfld ['value'] ) > 0 && $valfld ['operator']) {
			$filterJs [$fld] ['operator'] = $valfld ['operator'];
			$filterJs [$fld] ['value'] = $valfld ['value'];
			$filter .= $and;
			$ci = 0;
			if (count ( $valfld ['value'] ) > 1)
				$filter .= ' (';
			foreach ( $valfld ['value'] as $k => $v ) {
				$ci += 1;
				$filter .= " " . 'wform_' . $fuid . '.' . $fld . $valfld ['operator'] . "'" . $v . "'";
				if ($ci < count ( $valfld ['value'] ))
					$filter .= " OR";
			}
			if (count ( $valfld ['value'] ) > 1 && $ci == count ( $valfld ['value'] ))
				$filter .= ') ';
		}
	}
}

$q = new DBQuery ();

$q->addTable ( 'form_master' );
$q->addWhere ( 'id=' . $rfuid );
$sql = $q->prepare ();
$res = mysql_query ( $sql );
$robj = null;
if ($res) {
	$robj = mysql_fetch_object ( $res );
}
if ($_GET ['fpart'] != "") {
	$part = $_GET ['fpart'];
	$partStr = '&fpart=' . $part;
	$partFunc = '"' . $part . '",';
} else {
	$part = '';
	$partStr = '';
	$partFunc = '';
}
$table = "wf_" . $rfuid . "_wf_" . $fuid;
if ($data_rel_id) {
	$data_rel_id = explode ( ',', $data_rel_id );
	$clients = array ();
	if(is_array($data_rel_id)){
		foreach ( $data_rel_id as $index => $value ) {
			$clients [] = $value;
		}
	}
	if (count ($clients)) {
		
		//db_exec ( 'TRUNCATE `' . $table . '`' );
		db_exec('DELETE FROM `' . $table . '` WHERE `id`='.$dataid);
		foreach ( $clients as $cid ) {
			
			$sql = "INSERT INTO `" . $table . "`(`id`, `rel`) VALUES ('" . $dataid . "'," . $cid . ")";
			db_exec ( $sql );
		}
		$AppUI->redirect ( 'm=wizard&a=form_use&fid=' . $rfuid . '&idIns=' . $dataid . '&todo=view&teaser=1&rtable=1&tab=0' );
	}
}

if ($add==='request') {
	$sqlreq = $_SESSION['REQUESTSELECTOR'];
	
	/* $clients = array ();
	if(is_array($data_rel_id)){
		foreach ( $data_rel_id as $index => $value ) {
			$clients [] = $value;
		}
	} */
	$r = mysql_query($sqlreq);
	if($r){
		db_exec ( 'TRUNCATE `' . $table . '`' );
		while ($dr = mysql_fetch_assoc($r)) {
			$sql = "INSERT INTO `" . $table . "`(`id`, `rel`) VALUES ('" . $dataid . "'," . $dr['id'] . ")";
			db_exec ( $sql );
		}
		$_SESSION['REQUESTSELECTOR'] = '';
		$AppUI->redirect ( 'm=wizard&a=form_use&fid=' . $rfuid . '&idIns=' . $dataid . '&todo=view&teaser=1&rtable=1&tab=0' );
	}
}



$q1 = new DBQuery();
$q1->addTable($table);
$q1->addQuery('rel');
$q1->addWhere('id='.$dataid);
//$sql1 = $q1->prepare();
//echo $q1->prepare();
$existrecord = $q1->loadColumn();
//var_dump($inregistre);
$existrecord = array_values($existrecord);
?>


<script language="javascript">
server = true;
function setClientIDs (/* method,querystring */)
{
	var URL = 'index.php?m=wizard&a=selector&fid=<?php echo $fuid?>&relfid=<?php echo $rfuid?>&dataid=<?php echo $dataid?>';
    
	var field = document.getElementsByName('client_id[]');
	var selected_clients_id;// = document.frmClientSelect.selected_clients_id;
	var tmp = new Array();
	
	
	var count = 0;
	for (i = 0; i < field.length; i++) {
		if (field[i].checked) {
			tmp[count++] = field[i].value;
		}
	}
	selected_clients_id = tmp.join(',');
	if(selected_clients_id){
		URL +=  '&client_id=' + selected_clients_id;
		window.location.href = URL;
	}else{	
		alert('<?php echo $AppUI->_('error')?>');
	}
	return false;

}

function addRequestIDs (/* method,querystring */)
{
	var URL = 'index.php?m=wizard&a=selector&fid=<?php echo $fuid?>&relfid=<?php echo $rfuid?>&add=request&dataid=<?php echo $dataid?>';
    
	window.location.href = URL;
	return false;

}

</script>
<?php
function remove_invalid($arr) {
	$result = array ();
	foreach ( $arr as $val ) {
		if (! empty ( $val ) && trim ( $val ) !== '' && is_numeric ( $val )) {
			$result [] = $val;
		}
	}
	return $result;
}

?>
<!-- <script language="javascript">
	<?php //echo $call_back_string ?>
	self.close();
</script> -->

<?php
/*
 * return ;
 * }
 */

// Remove any empty elements
$clients_id = remove_invalid ( explode ( ',', $selected_clients_id ) );
$selected_clients_id = implode ( ',', $clients_id );

$q = new DBQuery ();

$moduleScripts [] = './modules/public/tsjq.js';
$wz = new Wizard ( null );
$wz->loadFormInfo ( $fuid );
$fields = $wz->showFieldsImport ();
//
$formsFlds = array ();
if (count ( $fields ['notms'] ) > 0) {
	foreach ( $fields ['notms'] as $nitem ) {
		// $formsFlds[$nitem['fld']] = $nitem['title'];
		if (isset ( $nitem ["raw"] ["sysv"] )) {
			$formsFlds [$nitem ["fld"]] = array (
					"title" => $nitem ["title"],
					"type" => $nitem ["raw"] ["type"],
					"sysv" => $nitem ["raw"] ["sysv"] 
			);
		} else {
			$formsFlds [$nitem ["fld"]] = array (
					"title" => $nitem ["title"],
					"type" => $nitem ["raw"] ["type"] 
			);
		}
	}
}
$digest = $wz->getDigest ();
$digestrel = $wz->getDigestRel ();
$q->clear ();

$pageSub = "";
$pageURL = 'http';
if ($_SERVER ["HTTPS"] == "on") {
	$pageURL .= "s";
}
$pageURL .= "://";
if ($_SERVER ["SERVER_PORT"] != "80") {
	$pageURL .= $_SERVER ["SERVER_NAME"] . ":" . $_SERVER ["SERVER_PORT"]; // .$_SERVER["REQUEST_URI"];
} else {
	$pageURL .= $_SERVER ["SERVER_NAME"]; // .$_SERVER["REQUEST_URI"];
}

$queryString = $_SERVER ["REQUEST_URI"];
// echo $queryString.strpos("?",$queryString);
// if (strpos("?",$queryString) !== false) {
$queryString = explode ( "?", $queryString );
$pageURL .= $queryString [0] . "?";
$pageSub .= $queryString [0] . "?";
if (isset ( $queryString [1] )) {
	// if (strpos("&",$queryString[1]) !== false) {
	$params = explode ( "&", $queryString [1] );
	foreach ( $params as $x => $param ) {
		// if (strpos("=",$queryString[1]) !== false) {
		$tabparam = explode ( "=", $param, 2 );
		if ($tabparam [0] != 'p') {
			if (isset ( $tabparam [1] )) {
				$pageURL .= "&" . $tabparam [0] . '=' . $tabparam [1];
				$pageSub .= "&" . $tabparam [0] . '=' . $tabparam [1];
			}
		}
		// }
	}
	// }
}
// }

$page = 1;
if (isset ( $_GET ['p'] ) && is_numeric ( $_GET ['p'] )) {
	$page = intval ( $_GET ['p'] );
	/*
	 * if ($page >= 1 && $page <= $nbPages) {
	 * // cas normal
	 * $current=$page;
	 * } else
	 */
	if ($page < 1) {
		// cas où le numéro de page est inférieure 1 : on affecte 1 à la page courante
		$page = 1;
	} /*
	   * else {
	   * //cas où le numéro de page est supérieur au nombre total de pages : on affecte le numéro de la dernière page à la page courante
	   * $current = $nbPages;
	   * }
	   */
}

$epp = ( int ) $_GET ['epp'];

if (! $epp) {
	$epp = 100;
}

if ($filter) {
	$count = mysql_query ( 'SELECT count(*) as count FROM  ' . 'wform_' . $fuid . ' WHERE ' . $filter );
} else {
	$count = mysql_query ( 'SELECT count(*) as count FROM  ' . 'wform_' . $fuid );
}

$count = mysql_fetch_assoc ( $count );
$count = $count ['count'];
$start = $page * $epp - $epp;
// $end = $start + $epp;
$nbPages = ceil ( $count / $epp );
$isFilter = false;
$q->addTable ( 'wform_' . $fuid );
if ($filter) {
	$q->addWhere ( $filter );
	$isFilter = true;
}
if ($group_by) {
	$q->addGroup ( $group_by );
}
$q->offset = $start;
$q->limit = $epp;
// $q->addWhere($query);
if($isFilter){
	$_SESSION['REQUESTSELECTOR'] = $q->prepare();
}else{
	unset($_SESSION['REQUESTSELECTOR']);
}
$clients = $q->loadHashList ( 'id' );

if ($page == 1) {
	$prev = '<h3 style="color:#c0c0c0">Prev</h3>';
	if ($page < $nbPages) {
		
		$next = '<a href="' . $pageURL . "&p=" . ($page + 1) . '"><h3>Next</h3></a>';
	} else {
		$next = '<h3 style="color:#c0c0c0">Next</h3>';
	}
} elseif ($page > 1) {
	// $pageURL .= "&p=".($page - 1);
	
	$prev = '<a href="' . $pageURL . "&p=" . ($page - 1) . '"><h3>Prev</h3></a>';
	if ($page < $nbPages) {
		$next = '<a href="' . $pageURL . "&p=" . ($page + 1) . '"><h3>Next</h3></a>';
	} else {
		$next = '<h3 style="color:#c0c0c0">Next</h3>';
	}
}

$currentNbr = $start + count ( $clients );

// var_dump($fields['notms']);
function getSysval($array, $field, $val) {
	$result = false;
	if($val != null){
		if (count ( $array ) > 0) {
			foreach ( $array as $nitem ) {
				if ($nitem ['fld'] === $field) {
					$psv = $nitem ["raw"] ["sysv"];
		
					if ($psv === 'SysDepartment' || $psv === 'SysCommunes' || $psv === 'SysCommunalSection' || $psv === 'SysStaff') {
						if ($psv === 'SysDepartment') {
							if (strlen ( $val ) < 2) {
								$val = '0' . $val;
							}
							$q = new DBQuery ();
							$q->addTable ( "administration_dep" );
							$q->addQuery ( "administration_dep_name" );
							$q->addWhere ( 'administration_dep_code="' . $val . '"' );
							$result = $q->loadResult ();
						}
						if ($psv === 'SysCommunes') {
							if (strlen ( $val ) < 6) {
								$val = '0' . $val;
							}
							$q = new DBQuery ();
							$q->addTable ( "administration_com" );
							$q->addQuery ( "administration_com_name" );
							$q->addWhere ( 'administration_com_code="' . $val . '"' );
							$result = $q->loadResult ();
						}
							
						if ($psv === 'SysCommunalSection') {
							if (strlen ( $val ) < 8) {
								$val = '0' . $val;
							}
							$q = new DBQuery ();
							$q->addTable ( "administration_section" );
							$q->addQuery ( "administration_section_name" );
							$q->addWhere ( 'administration_section_code="' . $val . '"' );
							$result = $q->loadResult ();
						}
							
						if ($psv === 'SysStaff') {
							$q = new DBQuery ();
							$q->addTable ( 'contacts', 'con' );
							$q->leftJoin ( 'users', 'u', 'u.user_contact = con.contact_id' );
							$q->addQuery ( 'CONCAT_WS(" ",contact_first_name,contact_last_name) as name' );
							$q->addWhere ( 'con.contact_id=' . $val );
							$result = $q->loadResult ();
						}
					} else {
						$tab = dPgetSysValSet ( $psv );
						$result = $tab [$val];
					}
				}
			}
		}		
	}

	return $result;
}

?>

<!-- <form action="index.php?m=public&a=client_selector&dialog=1&<?php if(!is_null($call_back)) echo 'call_back='.$call_back.'&'; ?>task_id=<?php echo $task__id.$partStr ?>" method='post' name='frmClientSelect'>
 -->
<form
	action="index.php?m=wizard&a=selector&fid=<?php echo $fuid?>&relfid=<?php echo $rfuid?>&dataid=<?php echo $dataid?>"
	method='post' name='frmClientSelect' id="formSelect">


<?php
$actual_department = '';
$actual_company = '';

?>

<br>
	<!-- <h4><a href="#" onClick="window.location.href=setClientIDs('GET','dialog=1&<?php if(!is_null($call_back)) echo 'call_back='.$call_back.'&'; ?>show_all=1<?php echo  $partStr;?>');"><?php echo $AppUI->_('Click to view all clients'); ?></a>
 
 </h4> -->
	<hr />
<?php $fieldsJs = json_encode($formsFlds); //var_dump($formsFlds);?>


<?php
/*
 * echo '<strong style="margin-left:5px;"><a href="/index.php?'.$AppUI->getState('SAVEDPLACE').'">'.$AppUI->_('Back').'</a>
 * <a href="/index.php?m=projects&a=view&project_id='.$task_id.'">'.$AppUI->_('view project').'</a>
 * <a href="/index.php?m=wizard&a=form_use&idIns='.$dataid.'&todo=view&teaser=1&rtable=1&tab=0">'.$AppUI->_($robj->title).'</a> :
 *
 * </strong>';
 */
$titleBlock = new CTitleBlock ( $AppUI->_ ( 'Add' ), '', $m, "$m.$a" );

// $titleBlock->addCrumb("?m=clients", "Clients");
// $titleBlock->addCrumbRight2("clearSelection(document.forms['changeClinical'])", "Clear All Selections");
// if ($clientObj->client_id > 0)
// $titleBlock->addCrumb("?m=clients&a=view&client_id=$clientObj->client_id", $clientObj->getFullName());
// $titleBlock->addCrumb("?".$AppUI->getState('SAVEDPLACE'), $AppUI->_('return'));
$titleBlock->addCrumb ( "?m=projects&a=view&project_id=" . $robj->project_id, $AppUI->_ ( 'view project' ) );
if ($robj->task_id)
	$titleBlock->addCrumb ( "?m=tasks&a=view&task_id=" . $robj->task_id, $AppUI->_ ( 'view activity' ) );
$titleBlock->addCrumb ( '?m=wizard&a=form_use&fid=' . $robj->id . '&idIns=' . $dataid . '&todo=view&teaser=1&rtable=1&tab=0', $AppUI->_ ( $robj->title ) );
$titleBlock->show ();

?>

<?php
$gt = new genericTable ( false, true );
$headers = array ();
?>
 <!-- <table id="qtable" class="tablesorter tbl" cellpadding=2 cellspacing=1 border=0>
<thead>
	<tr>
		<th>&nbsp;</th>
 -->		<?php
	
	/*
	 * $headers['<a href="#" style="color:white" onclick="var table = $(\'#rtable\');
	 * if($(\'td input:checkbox\', table).attr(\'checked\')!=true)
	 * $(\'td input:checkbox\', table).attr(\'checked\', true);
	 * else{$(\'td input:checkbox\', table).attr(\'checked\', false);
	 * }return true;">'.$AppUI->_('Select all').'</a>&emsp;&emsp;&emsp;'] = 'string';
	 */
	
	$headers ['<a href="#" style="color:white" onclick="var $table = $(\'#rtable\');
					var rows = $table.children(\'tbody\').children(\'tr\');
					
			if($(\'td input:checkbox\', $table).attr(\'checked\')!=true){
				$(\'td input:checkbox\', $table).attr(\'checked\', true);
				$(\'#btn_add_s\').show();
				$.each(rows,function(){
					$(this).css(\'background-color\',\'#58ACFA\');
				});
			}else{
					$(\'td input:checkbox\', $table).attr(\'checked\', false);$(\'#btn_add_s\').hide();
					$.each(rows,function(){
					$(this).css(\'background-color\',\'#fff\');
				});
			}"><img alt="check" src="/images/icons/toggle_check.png" ></a>'] = 'string';
	if (count ( $selected_columns ) > 0) {
		foreach ( $selected_columns as $fld ) {
			if ($formsFlds [$fld] ['title'] != "")
				$headers [$formsFlds [$fld] ['title']] = 'string';
		}
	} else {
		if($wz->parent && count($digestrel)>0){
			$wzr = new Wizard('print');
			$wzr->loadFormInfo($wz->parent);
			foreach ( $digestrel as $fld ) {
				$tname = $wzr->findFieldName($fld);
				$headers[$tname['name']] = 'string';
			}
		}
		
		foreach ( $digest as $fld ) {
			
			if ($formsFlds [$fld] ['title'] != "")
				$headers [$formsFlds [$fld] ['title']] = 'string';
		}
	}
	
	/*
	 * if($fields_all) foreach($fields_all as $i => $val){ if(!in_array($val, $digest)){?>
	 * <!-- <th><?php $headers[$formsFlds[$val]['title']] = 'string'; //echo $formsFlds[$val]?></th> -->
	 * <?php }}
	 */
	
	?>
	<!-- </tr>
</thead>
<tbody> -->
<?php
$gt->makeHeader ( $headers );
$headers_cnt = count ( $headers );
$decs = array (
		0 => '<input type="checkbox" name="client_id[]" id="client_##' . ($headers_cnt) . '##" value="##' . ($headers_cnt) . '##" ##' . ($headers_cnt + 1) . '## onclick="if(hasCheckedOnTable(\'rtable\')){$(\'#btn_add_s\').show();$(this).closest(\'tr\').css(\'background-color\',\'#58ACFA\');}else{$(\'#btn_add_s\').hide();$(this).closest(\'tr\').css(\'background-color\',\'#fff\');}"/>' 
)
;
$gt->setDecorators ( $decs );
// var_dump($clients);

foreach ( $clients as $cliid => $client_data ) {
	//echo in_array($cliid, $existrecord)." ";
	$row_data = array ();
	$checked = "";
	$checked = in_array ( $cliid, $clients_id ) || in_array($cliid, $existrecord)? 'checked="checked"' : '';
	
	$row_data [] = '';
	if (count ( $selected_columns ) > 0) {
		foreach ( $selected_columns as $fld ) {
			if ($client_data [$fld] == '-1')
				$client_data [$fld] = '';
			$client_data [$fld] = trim ( $client_data [$fld] );
			$test = getSysval ( $fields ['notms'], $fld, strval ( $client_data [$fld] ) );
			
			if ($test) {
				$client_data [$fld] = $test;
			}
			if ($client_data [$fld] === '01') {
				$client_data [$fld] = strval ( $client_data [$fld] );
			}
			
			$row_data [] = $client_data [$fld];
		}
	} else {
		if($wz->parent && count($digestrel)>0){
			$wzr = new Wizard('print');
			$wzr->loadFormInfo($wz->parent);
			$fieldsrel = $wzr->showFieldsImport();
			foreach ( $digestrel as $fld ) {
				if($fld){
					$q = new DBQuery();
					$q->addTable('wform_'.$wz->parent);
					$q->addQuery($fld);
					$q->addWhere('id='.$client_data['ref']);
					$re = $q->loadResult();
						
					$test = getSysval ( $fieldsrel ['notms'], $fld, strval ( $re ) );
			
					if ($test) {
						$re = $test;
					}
					
					$row_data [] = $re;
				}
			}
		}
		foreach ( $digest as $fld ) {
			if ($client_data [$fld] == '-1')
				$client_data [$fld] = '';
			$client_data [$fld] = trim ( $client_data [$fld] );
			$test = getSysval ( $fields ['notms'], $fld, strval ( $client_data [$fld] ) );
			
			if ($test) {
				$client_data [$fld] = $test;
			}
			
			$row_data [] = $client_data [$fld];
		}
	}
	
	$row_data [] = $cliid;
	$row_data [] = $checked;
	
	$gt->fillBody ( $row_data );
}
$selected1 = "";
$selected2 = "";
$selected3 = "";
$selected4 = "";
if ($epp === 100) {
	$selected1 = " selected=selected";
}
if ($epp === 250) {
	$selected2 = " selected=selected";
}
if ($epp === 500) {
	$selected3 = " selected=selected";
}
if ($count > $start + $epp) {
	$val = $start + $epp;
} else {
	$val = $count;
}
$info = "Total Records: " . $count . " Viewing Records: " . ($start + 1) . "-" . $val . " <span style='color: #08245b;'><b>Rows per page</b></span> <select name='epp' onchange='window.location.href = window.location.href+\"&epp=\"+this.value'><option " . $selected1 . ">100</option><option " . $selected2 . ">250</option><option " . $selected3 . ">500</option></select>";

?>
<!-- </tbody>
</table> -->

<?php
$trRows = array ();
// $formsFldsFilter
foreach ( $formsFlds as $index => $field ) {
	$alist = $wz->getValues ( $field ['type'], $field ['sysv'], false, true, false, false, 'wform_' . $fuid, $index );
	$select = "";
	$function = "";
	if ($field ['type'] === 'numeric') {
		$select = '<input type="number" id="' . $index . '_value" name="' . 'wform_' . $fuid . '[' . $index . '][value][]" value="' . $_GET ['wform_' . $fuid] [$index] ['value'] . '"/> ';
		$function = '<select  id="' . $index . '_operator" name="' . 'wform_' . $fuid . '[' . $index . '][operator]"><option value="=">equal to</option><option  value="<">less than</option><option value=">">more than</option></select>';
	} elseif ($field ['type'] === 'date' || $field ['type'] === 'entry_date') {
		/*
		 * $dateS = null;
		 * if(count($filterJs)>0)
		 * $dateS = $filterJs[$index]['value'][0];
		 * $select = drawDateCalendar(''.'wform_'.$fuid.'['.$index.'][value][]',$dateS,false,'class="mandat"',false,10,false,'$j(this).trigger("focusout");');
		 */
		$select = '<input type="text" class="classflddate" id="' . $index . '_value" name="' . 'wform_' . $fuid . '[' . $index . '][value][]" value="' . $_GET ['wform_' . $fuid] [$index] ['value'] . '"/> ';
		$function = '<select  id="' . $index . '_operator" name="' . 'wform_' . $fuid . '[' . $index . '][operator]"><option value="=">equal to</option><option  value="<="><=</option><option value=">=">>=</option></select>';
	} elseif (isset ( $field ['sysv'] )) {
		$select = '<select id="' . $index . '_value" name="' . 'wform_' . $fuid . '[' . $index . '][value][]">';
		$select .= '<option value="---"></option>';
		foreach ( $alist as $key => $val ) {
			if (trim ( $key ) !== "" && trim ( $key ) !== "rels") {
				$selected = '';
				if ($_GET ['wform_' . $fuid] [$index] ['value'] == $key)
					$selected = 'selected=selected';
				$select .= '<option value="' . $key . '" ' . $selected . '>' . $alist [$key] . '</option>';
			}
		}
		$select .= '</select><a href="javascript:crAndRmSelectMultiple(\'' . $index . '_value\')" style="text-decoration:none">&emsp;<img src="/images/icons/stock_new.png" width="10px" height="10px"></a>';
		$function = '<select  id="' . $index . '_operator" name="' . 'wform_' . $fuid . '[' . $index . '][operator]"><option value="=">is</option><option value="<>">is not</option></select>';
	} elseif ($field ['type'] === 'date') {
	} elseif ($field ['type'] === 'plain') {
		$select = '<input type="text" id="' . $index . '_value" name="' . 'wform_' . $fuid . '[' . $index . '][value]">';
		$function = '<select  id="' . $index . '_operator" name="' . 'wform_' . $fuid . '[' . $index . '][operator]"><option value="=">is</option><option value="<>">is not</option></select>';
	}
	if ($select && $function)
		$trRows [$index] = utf8_encode ( '<tr><td><span onclick="delRowFilter(this)" style="width: 16px;height: 16px;padding: 1px;cursor: pointer;font-weight: 800;float: left;background-color: #B0B0B0;margin: 2px;text-align: center;background: url(\'/modules/wizard/images/icns.png\') no-repeat;background-position: -18px 1px;">&nbsp;&nbsp;</span></td><td style="padding:3px;width:20%">' . $field ["title"] . '</td><td>' . $function . '</td><td style="padding:3px">' . $select . '</td></tr>' );
}
if (isset ( $_GET ['wform_' . $fuid] )) {
	$filcollapse = '';
	$filvis = 'display: block';
} else {
	$filcollapse = 'collapsed';
	$filvis = 'display: none';
}

if (isset ( $_GET ['wform_' . $fuid] )) {
	$opcollapse = '';
	$opvis = 'display: block';
} else {
	$opcollapse = 'collapsed';
	$opvis = 'display: none';
}

?>
<div align="left"
		style="background: white; margin-top: 2px; padding: 10px">
		<fieldset id="filters" style="margin-left: -7px;"
			class="collapsible <?php echo $filcollapse;?> header_collapsible">
			<legend
				onclick="if($('#filterstab').is(':hidden')){
					$('#filterstab').show();document.getElementById('filters').classList.remove('collapsed');
                 }else{
		           $('#filterstab').hide();document.getElementById('filters').classList.add('collapsed');
                 }">Filtres</legend>
			<table id="filterstab" style="<?php echo $opvis;?>; width: 100%">
				<tbody>
				<?php
				
				echo '<tr><td colspan="4">
				&nbsp;Add filter: <select id="select_field" onchange="setTableFilter()">';
				echo '<option></option>';
				foreach ( $formsFlds as $index => $field ) {
					echo '<option value="' . $index . '">' . $field ["title"] . '</option>';
				}
				echo '</select></td></tr>';
				?>
			</tbody>
			</table>
		</fieldset>
		<fieldset id="options" style="margin-left: -7px;"
			class="collapsible <?php echo $opcollapse;?> header_collapsible">
			<legend
				onclick="if($('#optionstab').is(':hidden')){
					$('#optionstab').show();document.getElementById('options').classList.remove('collapsed');
                 }else{
		           $('#optionstab').hide();document.getElementById('options').classList.add('collapsed');
                 }">Options</legend>
			<table id="optionstab" style="<?php echo $opvis;?>; width: 100%">
				<tbody>
				<?php
				
				/*
				 * echo '<tr><td colspan="4">
				 * &nbsp;Add filter: <select id="select_field" onchange="setTableFilter()">';
				 * echo '<option></option>';
				 * foreach ($formsFlds as $index => $field){
				 * echo '<option value="'.$index.'">'.$field["title"].'</option>';
				 * }
				 * echo '</select></td></tr>';
				 */
				?>
				<tr>
						<td
							style="width: 1%; margin: 0; padding: 0; border: 0; outline: 0; font-size: 100%; vertical-align: baseline; background: transparent;">
							<label for="available_columns"><?php echo $AppUI->_('Available Columns');?></label>
							<br /> <select id="available_columns" multiple="multiple"
							size="10" style="width: 150px">
							<?php
							if (count ( $selected_columns ) > 0) {
								foreach ( $formsFlds as $index => $field ) {
									if (! in_array ( $index, $selected_columns ))
										echo '<option value="' . $index . '">' . $field ["title"] . '</option>';
								}
							} else {
								foreach ( $formsFlds as $index => $field ) {
									if (! in_array ( $index, $digest ))
										echo '<option value="' . $index . '">' . $field ["title"] . '</option>';
								}
							}
							
							?>
						</select>
						</td>
						<td
							style="width: 1%; margin: 0; padding: 5px; border: 0; outline: 0; font-size: 100%; background: transparent;"
							align="center"><input type="button" value="→"
							onclick="addRemoveOption('available_columns','selected_columns')" /><br />
						<br /> <input type="button" value="←"
							onclick="addRemoveOption('selected_columns','available_columns')" />
						</td>
						<td
							style="width: 1%; margin: 0; padding: 0; border: 0; outline: 0; font-size: 100%; vertical-align: baseline; background: transparent;">
							<label for="selected_columns"><?php echo $AppUI->_('Selected Columns');?></label>
							<br /> <select id="selected_columns" multiple="multiple"
							name="selected_columns[]" size="10" style="width: 150px">
							<?php
							// var_dump($digest);
							// echo in_array('fld_0', $digest);
							if (count ( $selected_columns ) > 0) {
								foreach ( $selected_columns as $index => $field ) {
									echo '<option value="' . $field . '">' . $formsFlds [$field] ["title"] . '</option>';
								}
							} else {
								foreach ( $digest as $index => $field ) {
									echo '<option value="' . $field . '">' . $formsFlds [$field] ["title"] . '</option>';
								}
							}
							
							?>
						</select>
						</td>
						<td
							style="margin: 0; padding: 5px; border: 0; outline: 0; font-size: 100%; background: transparent;">
							<input type="button" id="btns_up" value="↑"
							onclick="moveUpDown('selected_columns','Up')"><br />
						<br /> <input type="button" value="↓"
							onclick="moveUpDown('selected_columns','Down')">
						</td>
					</tr>
					<!-- <tr>
					<td colspan="3">
						<label for="group_by">Group results by</label>
						<br/>
						<select id="group_by" name="group_by">
							<?php
							foreach ( $formsFlds as $index => $field ) {
								echo '<option value="' . $index . '">' . $field ["title"] . '</option>';
							}
							?>
							
						</select>
					</td>				
				</tr> -->
				</tbody>
			</table>
		</fieldset>
<?php
// frmClientSelect
// $formdata = 'joinList(serialize(createForm(\'filterform\',\'filter1\')))';
$formdata = 'joinList(serialize(document.frmClientSelect))';
$link = '\'/index.php?m=wizard&a=selector&fid=' . $fuid . '&relfid=' . $rfuid . '&dataid=' . $dataid . '\'';
/* $searchscpt = ''; */
$searchscpt = 'window.location.href = ' . $link . '+\'&\'+' . $formdata;
if(isset($_GET['clients_submited']) && $_GET['clients_submited']==1){
	$clients_submited = '<input type="button" value="' . $AppUI->_ ( 'Add Request Beneficiarie(s)' ) . '" onClick="addRequestIDs()"/>';
}else{
	$clients_submited = '';
}

echo '
			
			<!--<input type="button" value="' . $AppUI->_ ( 'Add Field(s) To Table' ) . '"' . " onclick='dialogNewFields(" . $task_id . "," . $fieldsJs . ");'" . '>&emsp;&emsp;-->
	  	'.$clients_submited.'
		<input type="button" id="btn_add_s" value="' . $AppUI->_ ( 'Add Selected Beneficiarie(s)' ) . '" onClick="setClientIDs()" style="display:none"/>
	  <input id="search" type="hidden" name="search" style="margin-left:10px;" placeholder="' . $AppUI->_ ( 'Search' ) . '"/>
      <input type="button" value="' . $AppUI->_ ( 'Clear' ) . '" onClick="window.location=' . $link . '"/>
      <input type="button" value="' . $AppUI->_ ( 'Apply' ) . '" id="submitButton" onclick="' . $searchscpt . '"/>';
?>

				
</div>

<?php
/*
 * echo '<a href="#" onclick="var table = $(\'#rtable\');
 * if($(\'td input:checkbox\', table).attr(\'checked\')!=true)
 * $(\'td input:checkbox\', table).attr(\'checked\', true);
 * else{$(\'td input:checkbox\', table).attr(\'checked\', false);
 * }">'.$AppUI->_('Select all').'</a>&emsp;&emsp;&emsp;';
 */
$gt->compile ();

?>


<input name="clients_submited" type="hidden" value="1" /> <input
		name="selected_clients_id" type="hidden"
		value="<?php echo $selected_clients_id; ?>"> <br />
	<table style="margin-left: -3px;">
		<tr>
			<td><?php echo $prev;?></td>
			<td><?php echo $next;?></td>
			<td><div style="margin-top: 35px;"><?php echo $info;?></div></td>
		</tr>
	</table>

	<!-- <input type="submit" value="<?php echo $AppUI->_('Continue'); ?>" onClick="setClientIDs()" class="button" /> -->
</form>
<script language="javascript">
<?php
echo ';trRows = ' . json_encode ( $trRows ) . ';';
echo ';filterJs = ' . json_encode ( $filterJs ) . ';';
?>
	if(filterJs.length === undefined){
		$.each(filterJs, function( index, value ) {
			console.log(index);
			console.log(value);
			var filterstab = document.getElementById('filterstab');
				//filterstab.innerHTML += trRows[val]; 
			$('#filterstab').append(trRows[index]);
			document.getElementById(index+'_operator').value = value.operator;
			if(value.value.length>1){
				document.getElementById(index+'_value').setAttribute('multiple',true);
			}else{
				value.value = value.value.join("");
			}
			$("#"+index+'_value').val(value.value);
			
		});
		
	}

</script>
<script language="javascript" src="/modules/wizard/filter.js"></script>
<script type="text/javascript" src="/modules/public/add_fields.js"></script>
<script type="text/javascript">
//window.onload=boost;function boost(){$j("#qtable").tablesorter({headers:{0:{sorter:false},2:{sorter: "soname"}},widgets:['fixHead']});}
//createForm('filterform','filter');
//console.log($('#rtable').children('tr:first').find('th:first'));

	//$('#rtable').children('tr:first').find('th:first').attr('class', 'head');​
</script>
