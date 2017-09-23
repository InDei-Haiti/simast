<?php

$show_all = dPgetParam ( $_REQUEST, 'show_all', 0 );
$activity_id = dPgetParam ( $_REQUEST, 'activity_id', 0 );
$caregiver_id = dPgetParam ( $_POST, 'caregiver_id', 0 );
$call_back = dPgetParam ( $_GET, 'call_back', null );
$caregivers_submited = (isset($_POST['caregiver_id']) && count($_POST['caregiver_id'] ) > 0);//dPgetParam ( $_POST, 'caregivers_submited', 0 );
$pcarez=$_POST['caregiver_id'];
if(count($pcarez) > 0){
	$selected_caregivers_id=array();
	foreach ($pcarez as $carid) {
		if(strstr($carid,'other_')){
			$inadm=str_replace('other_','',$carid);
			$otxt=trim(mysql_real_escape_string($_POST['other_text_'.$inadm]));
			if(strlen($otxt) > 0 ){
				$selected_caregivers_id[]=$inadm.'#@#'.$otxt;
			}
		}else{
			$selected_caregivers_id[]=$carid;
		}
	}
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

?>
<script language="javascript">
function setCaregiverIDs (method,querystring)
{
	var URL = 'index.php?m=public&a=caregiver_selector';
    
	var field = document.getElementsByName('caregiver_id[]');
	var selected_caregivers_id = document.frmCaregiverSelect.selected_caregivers_id;
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
	selected_caregivers_id.value = tmp.join(',');
    
	if (method == 'GET') {
		URL +=  '&selected_caregivers_id=' + selected_caregivers_id.value;
		return URL;
	} else {
		return selected_caregivers_id;
	}

}
</script>
<?php

function remove_invalid($arr) {
	$result = array ();
	foreach ( $arr as $val ) {
		if (! empty ( $val ) && trim ( $val ) !== '' /*&& is_numeric ( $val )*/) {
			$result [] = $val;
		}
	}
	return $result;
}
//var_dump($selected_clients_id);
//var_dump($clients_submited);
//var_dump($call_back);
if ($caregivers_submited === true) {
	$call_back_string = ! is_null ( $call_back ) ? "window.opener.$call_back(".$partFunc."'".urlencode(implode(',',$selected_caregivers_id))." ');" : '';
	?>
<script language="javascript">
	<?php
	echo $call_back_string?>
	self.close();
</script>
<?php
	return ;
}

// Remove any empty elements

$selected_caregivers_id = dPgetParam ( $_GET, 'selected_caregivers_id', '' );
if (dPgetParam ( $_POST, 'selected_caregivers_id' )) {
	$selected_caregivers_id = dPgetParam ( $_POST, 'selected_caregivers_id' );
}

$caregivers_id = remove_invalid ( explode ( ',', urldecode($selected_caregivers_id )) );
$others=array();
$txtNames=array();
foreach ($caregivers_id as $ids) {
	if(preg_match("/#@#/",$ids)){
		$rn=explode('#@#',$ids);
		$others[]=$rn[0];
		$txtNames[]=$rn[1];
	}
}
$selected_caregivers_id = implode ( ',', $caregivers_id );

require_once ($AppUI->getModuleClass ( 'caregivers' ));
$oCaregiver = new CCaregiver ();
$aCaregivers = $oCaregiver->getAllowedRecords ( $AppUI->user_id, 'caregiver_id', 'caregiver_id' );
$aCaregivers_esc = array ();
foreach ( $aCaregivers as $key => $caregiver ) {
	$aCaregivers_esc [$key] = db_escape ( $caregiver );
}

$q = new DBQuery ();

/*if (strlen ( $selected_caregivers_id ) > 0 && ! $show_all && ! $caregiver_id) {
	$q->addTable ( 'caregiver_client' );
	$q->addQuery ( 'caregiver_id' );
	$q->addWhere ( 'caregiver_id IN (' . $selected_caregivers_id . ')' );
	$where = implode ( ',', $q->loadColumn () );
	$q->clear ();
	if (substr ( $where, 0, 1 ) == ',' && $where != ',') {
		$where = '0' . $where;
	} else if ($where == ',') {
		$where = '0';
	}
	$where = (($where) ? ('caregiver_id IN(' . $where . ')') : '');
}*/
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
//$q->leftJoin('companies', 'b', 'company_id = contact_company');
//$q->leftJoin('departments', 'c', 'dept_id = contact_department');
//$q->addQuery('company_name');
//$q->addQuery('dept_name');


/**
 * 
 $q->addTable('caregiver_client', 'a');
 $q->addQuery('a.caregiver_id, a.caregiver_fname,  a.caregiver_lname');
$q->addQuery('c.client_adm_no, c.client_first_name,  c.client_last_name,client_id');
$q->addQuery('concat_ws(" ",c.client_adm_no, c.client_first_name,  c.client_last_name) as client_name');
$q->innerJoin('clients', 'c', 'a.caregiver_client_id = c.client_id');
$q->addWhere('a.caregiver_client_caregiver_type = 1');
$q->addWhere('LENGTH(a.caregiver_fname) > 2');

 */
//$q->addTable ( 'admission_caregivers', 'ac' );


$carez = array ('father' => array (), 'mother' => array (), 'pri' => array (), 'sec' => array (), 'other' => '' );

$q->addTable ( 'clients', 'c' );
$q->addQuery ( 'c.client_adm_no, c.client_first_name,  c.client_last_name,c.client_id' );
$q->addQuery ( 'concat_ws(" ", c.client_first_name,  c.client_last_name) as client_name' );
$clients = $q->loadHashList ( 'client_id' );

$q = new DBQuery ();
$q->addTable ( 'admission_caregivers', 'ac' );
foreach ( $clients as $clid => $clar ) {
	$q1 = clone $q;
	$q1->addWhere ( 'client_id=' . $clid );
	foreach ( $carez as $cname => $cinfo ) {
		$q2 = clone $q1;
		$q2->addQuery ( 'ac.id as id,concat_ws(" ",ac.fname,ac.lname) as name,ac.lname as lname,ac.role' );
		$q2->addWhere ( 'ac.role="' . $cname . '"' );
		$q2->addWhere ( 'ac.datesoff is null' );
		$tt = $q2->loadList ();
		$carez [$cname] = $tt [0];
		unset ( $q2, $tt );
	}
	unset ( $q1 );
	$clients [$clid] ['carez'] = $carez;
	$carez = array ('father' => array (), 'mother' => array (), 'pri' => array (), 'sec' => array (), 'other' => '' );
}

/*
$q->addQuery ( 'acp.id as caregiver_id_pri,concat_ws(" ",acp.fname,acp.lname) as carename_pri,acp.lname as plname,acp.role' );
$q->addQuery ( 'acs.id as caregiver_id_sec,concat_ws(" ",acs.fname,acs.lname) as carename_sec,acs.lname as slanem,acs.role' );
$q->addQuery ( 'acf.id as father_id,concat_ws(" ",acf.fname,acf.lname) as father_name,acf.lname as flname,acf.role' );
$q->addQuery ( 'acm.id as mother_id,concat_ws(" ",acm.fname,acm.lname) as mother_name_sec,acm.lname as mlanem,acm.role' );

//$q->innerJoin ( 'clients', 'c', 'ac.client_id = c.client_id' );
$q->leftJoin ( 'admission_caregivers', 'acp', 'acp.client_id=c.client_id' );
$q->leftJoin ( 'admission_caregivers', 'acs', 'acs.client_id=c.client_id' );
$q->leftJoin ( 'admission_caregivers', 'acf', 'acf.client_id=c.client_id' );
$q->leftJoin ( 'admission_caregivers', 'acm', 'acm.client_id=c.client_id' );
$q->addWhere ( 'LENGTH(acp.fname) > 2 or LENGTH(acs.fname) > 2' );
$q->addWhere ( 'acp.role="pri"' );
$q->addWhere ( 'acs.role="sec"' );
$q->addWhere ( 'acf.role="father"' );
$q->addWhere ( 'acm.role="mother"' );
//$q->addWhere ( 'ac.role <> "mother" and ac.role <> "father"' );


$q->addWhere ( 'acp.datesoff is null' );
$q->addWhere ( 'acs.datesoff is null' );

if ($where) { // Don't assume where is set. Change needed to fix Mantis Bug 0002056
//	$q->addWhere($where);
}
//$q->addWhere("(contact_owner = '".$AppUI->user_id."' OR contact_private = '0')");
//$q->addOrder ( 'ac.fname' ); // May need to review this.


$caregivers = $q->loadHashList ('client_id' );//'caregiver_id'
*/
$moduleScripts [] = './modules/public/tsjq.js';
?>

<form action="index.php?m=public&a=caregiver_selector&dialog=1&<?php if (! is_null ( $call_back )) 	echo 'call_back=' . $call_back . '&';?>activity_id=<?php echo $activity_id.$partStr?>"	
	method='post' name='frmCaregiverSelect'>
<input type="submit" value="<?php echo $AppUI->_ ( 'Continue' ); ?>" onClick="setCaregiverIDs()" class="button" />
<?php
$actual_department = '';
$actual_company = '';
$actual_client = '';
//$companies_names = array(0 => $AppUI->_('Select a company')) + $aCpies;
/*echo arraySelect($companies_names, 'company_id', 
				 'onchange="document.frmContactSelect.contacts_submited.value=0; '
				 .'setContactIDs(); document.frmContactSelect.submit();"', 
				 0);*/
?>

<br>
<h4><a href="#"
	onClick="window.location.href=setCaregiverIDs('GET','dialog=1&<?php
	if (! is_null ( $call_back ))
		echo 'call_back=' . $call_back . '&';
	?>show_all=1');"><?php
	echo $AppUI->_ ( 'Click to view all caregivers' );
	?></a></h4>
<hr />
<h2><?php
echo $AppUI->_ ( 'Caregivers' );
?> <?php
echo $activity_name?></h2>
<table class="tablesorter tbl" cellpadding="2" cellspacing="1" border="0" id="qtable">
	<thead>
		<tr>
			<th>Adm #</th>
			<th>Client name</th>
			<th>Father</th>
			<th>Mother</th>
			<th>Primary Caregiver</th>
			<th>Secondary Caregiver</th>
			<th>Other person</th>
		</tr>
	</thead>
	<tbody>
<?php
$ind=0;
foreach ( $clients as $client_id => $cadata ) {
	$caregiver_client = $cadata ['client_name'];
	/*if ($caregiver_client && $caregiver_client != $actual_client) {
		echo '<h4>' . $caregiver_client . '</h4>';
		$actual_client = $caregiver_client;
	}*/
	
	$localCarez = $cadata ['carez'];
	$is_other=array_search($cadata['client_adm_no'],$others);
	$checked_pri = (! is_null ( $localCarez ['pri'] ) && in_array ( $localCarez ['pri'] ['id'], $caregivers_id )) ? 'checked="checked"' : '';
	$checked_sec = (! is_null ( $localCarez ['sec'] ) && in_array ( $localCarez ['sec'] ['id'], $caregivers_id )) ? 'checked="checked"' : '';
	$checked_father = (! is_null ( $localCarez ['father'] ) && in_array ( $localCarez ['father'] ['id'], $caregivers_id )) ? 'checked="checked"' : '';
	$checked_mother = (! is_null ( $localCarez ['mother'] ) && in_array ( $localCarez ['mother'] ['id'], $caregivers_id )) ? 'checked="checked"' : '';
	//	echo $AppUI->_("Caregivers for " . $caregiver_data['client_first_name'].' '.$caregiver_data['client_last_name']);
	/*echo '<input type="checkbox" name="caregiver_id[]" id="caregiver_'.$caregiver_id.'" value="'.$caregiver_id.'" '.$checked.' />';
		echo '<label for="caregiver_'.$caregiver_id.'">'.$caregiver_data['fname'].' '.$caregiver_data['lname'].'</label>';
		//var_dump($caregivers[$client_id]);
		echo "<br />\n";*/
	echo '<tr><td>' . $cadata ['client_adm_no'] . '</td><td><span data-skort="' . $cadata ['client_last_name'] . '" style="display:none;"></span>' . $cadata ['client_name'] . '</td>
	<td>' . (@trim ( $localCarez ['father'] ['name'] ) != '' ? '<label ><input  data-skort="' . $localCarez ['father'] ['lname'] . '" type="checkbox" name="caregiver_id[]" id="caregiver_' . $localCarez ['father'] ['id'] . '" value="' . $localCarez ['father'] ['id'] . '" ' . $checked_father . '>' . $localCarez ['father'] ['name'] . '</label>' : '&nbsp;') . '</td>
	<td>' . (@trim ( $localCarez ['mother'] ['name'] ) != '' ? '<label ><input data-skort="' . $localCarez ['mother'] ['lname'] . '" type="checkbox" name="caregiver_id[]" id="caregiver_' . $localCarez ['mother'] ['id'] . '" value="' . $localCarez ['mother'] ['id'] . '" ' . $checked_mother . '>' . $localCarez ['mother'] ['name'] . '</label>' : '&nbsp;') . '</td>
	<td>' . (@trim ( $localCarez ['pri'] ['name'] ) != '' ? '<label ><input  data-skort="' . $localCarez ['pri'] ['lname'] . '" type="checkbox" name="caregiver_id[]" id="caregiver_' . $localCarez ['pri'] ['id'] . '" value="' . $localCarez ['pri'] ['id'] . '" ' . $checked_pri . '>' . $localCarez ['pri'] ['name'] . '</label>' : '&nbsp;') . '</td>
	<td>' . (@trim ( $localCarez ['sec'] ['name'] ) != '' ? '<label ><input data-skort="' . $localCarez ['sec'] ['lname'] . '" type="checkbox" name="caregiver_id[]" id="caregiver_' . $localCarez ['sec'] ['id'] . '" value="' . $localCarez ['sec'] ['id'] . '" ' . $checked_sec . '>' . $localCarez ['sec'] ['name'] . '</label>' : '&nbsp;') . '</td>	
	<td><input  type="checkbox" name="caregiver_id[]" value="other_'.$cadata['client_adm_no'].'" '.((is_numeric($is_other) && $is_other >= 0) ? 'checked="checked"' : '').' >
		<input type="text" class="text live_edit" name="other_text_'.$cadata['client_adm_no'].'" value="'.((is_numeric($is_other) && $is_other >= 0) ? $txtNames[$is_other] : '').'">' . 
	'</td></tr>' . "\n";
	++$ind;
	flush_buffers();
}
?>
</tbody>
</table>
<hr />
<input name="caregivers_submited" type="hidden" value="1" /> <input
	name="selected_caregivers_id" type="hidden"
	value="<?php
	echo $selected_caregivers_id;
	?>"> <input type="submit"
	value="<?php
	echo $AppUI->_ ( 'Continue' );
	?>"
	onClick="setCaregiverIDs()" class="button" /></form>
<script type="text/javascript">
window.onload=boost;function boost(){
	$j("#qtable").tablesorter({headers:{1:{sorter: false},2:{sorter: false},3:{sorter: false},4:{sorter:false},5:{sorter:false},6:{sorter:false}},widgets:['fixHead']});
	$j(".live_edit").live("keyup", function(e){
		var $bblock=$j(this).closest("div"),nv=trim($j(this).val()),flag=false;
		if(nv.length > 0){
			flag=true;
		}
		$j(this).parent().find("input[type='checkbox']:last").attr("checked",flag);				
	});
}
</script>
