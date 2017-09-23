
<?php /* $Id: index.php 5773 2008-07-21 15:04:07Z merlinyoda $ */
if(isset($_GET['mode']) && $_GET['mode']==='loadtask'){
	$pid = $_GET['pid'];
	$sql='select task_id,task_name FROM tasks WHERE task_project='.$pid;
	$res=mysql_query($sql);
	if($res && mysql_num_rows($res)  > 0){
		while($trow=mysql_fetch_assoc($res)){
			$all[] = $trow;
		}
		echo json_encode($all);
	}else{
		echo 'fail';
	}
	return;
}

if(isset($_GET['mode']) && $_GET['mode']==='loadform'){
	$pid = $_GET['pid'];
	$tid = $_GET['tid'];
	if($tid==='all'){
		$q = new DBQuery();
		$q->addTable('form_master');
		$q->addQuery("id,title");
		$q->addWhere('project_id='.$pid);

		$sql=$q->prepare();
		$res=mysql_query($sql);
		if($res && mysql_num_rows($res)  > 0){
			while($trow=mysql_fetch_assoc($res)){
				$all[] = $trow;
			}
			echo json_encode($all);
		}else{
			echo 'fail';
		}
	}else{
		$q = new DBQuery();
		$q->addTable('form_master');
		$q->addQuery("id,title");
		$q->addWhere('task_id='.$tid);

		$sql=$q->prepare();
		$res=mysql_query($sql);
		if($res && mysql_num_rows($res)  > 0){
			while($trow=mysql_fetch_assoc($res)){
				$all[] = $trow;
			}
			echo json_encode($all);
		}else{
			echo 'fail';
		}
	}

	return;
}

echo "<br/>
<div class=\"card\">";
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}

$perms =& $AppUI->acl();

$is_superAdmin = false;
$roles = $perms->getUserRoles($AppUI->user_id);
foreach ($roles as $role){
	if($role['value']=='super_admin'){
		$is_superAdmin = true;
	}
}
if(!$is_superAdmin){
	if (! $perms->checkModule($m, 'view')) {
		$AppUI->redirect('m=public&a=access_denied');
	}
	if (! $perms->checkModule('users', 'view')) {
		$AppUI->redirect('m=public&a=access_denied');
	}
}


$AppUI->savePlace();

if (isset($_GET['tab'])) {
    $AppUI->setState('UserIdxTab', $_GET['tab']);
}
$tab = (($AppUI->getState('UserIdxTab') !== NULL) ? $AppUI->getState('UserIdxTab') : 0);

if (isset($_GET['stub'])) {
    $AppUI->setState('UserIdxStub', $_GET['stub']);
    $AppUI->setState('UserIdxWhere', '');
} else if (isset($_POST['where'])) {
    $AppUI->setState('UserIdxWhere', $_POST['where']);
    $AppUI->setState('UserIdxStub', '');
}
$stub = $AppUI->getState('UserIdxStub');
$where = $AppUI->getState('UserIdxWhere');

$valid_ordering = array(
	'user_username',
	'contact_last_name',
	'contact_company',
	'date_time_in',
	'user_ip',
);
if (isset($_GET['orderby']) && in_array($_GET['orderby'], $valid_ordering)) {
    $AppUI->setState('UserIdxOrderby', $_GET['orderby']);
}
$orderby = (($AppUI->getState('UserIdxOrderby')) ? $AppUI->getState('UserIdxOrderby')
            : 'user_username');
$orderby = (($tab == 3 || ($orderby != 'date_time_in' && $orderby != 'user_ip'))
            ? $orderby : 'user_username');

$q = new DBQuery;

// Pull First Letters
$let = ":";
$q->addTable('users','u');
$q->addQuery('DISTINCT UPPER(SUBSTRING(user_username, 1, 1)) AS L');
$arr = $q->loadList();
foreach($arr as $L) {
	$let .= $L['L'];
}
$q->clear();

$q->addTable('users','u');
$q->addQuery('DISTINCT UPPER(SUBSTRING(contact_first_name, 1, 1)) AS L');
$q->addJoin('contacts', 'con', 'contact_id = user_contact');
$arr = $q->loadList();
foreach($arr as $L) {
	if ($L['L']) {
		$let .= strpos($let, $L['L']) ? '' : $L['L'];
	}
}
$q->clear();

$q->addTable('users','u');
$q->addQuery('DISTINCT UPPER(SUBSTRING(contact_last_name, 1, 1)) AS L');
$q->addJoin('contacts', 'con', 'contact_id = user_contact');
$arr = $q->loadList();
foreach($arr as $L) {
	if ($L['L']) {
		$let .= strpos($let, $L['L']) ? '' : $L['L'];
	}
}
$q->clear();


// $a2z = "\n" . '<table cellpadding="2" cellspacing="1" border="0">';
// $a2z .= "\n<tr>";
// $a2z .= '<td width="100%" align="right">' . $AppUI->_('Show'). ': </td>';
// $a2z .= '<td><a href="./index.php?m=admin&stub=0">' . $AppUI->_('All') . '</a></td>';
// for ($c=65; $c < 91; $c++) {
// 	$cu = chr($c);
// 	$cell = ((strpos($let, $cu) > 0)
// 	         ? '<a href="?m=admin&stub=' . $cu . '">' . $cu . '</a>'
// 	         : '<font color="#999999">' . $cu . '</font>');
// 	$a2z .= "\n\t<td>" . $cell . '</td>';
// }
// $a2z .= "\n</tr>\n</table>";


// Changing online stub to Select
/*
* Previous format was lines 147 to 158
*/
$a2z = "\n" . '<table cellpadding="2" cellspacing="1" border="0">';
// $a2z.= "<label>".$AppUI->_('Show').":</label>";
$a2z.='<select class="form-control" style="width: 200px;height:30px" onchange="location = this.value;"><option value="./index.php?m=admin&stub=0">-----------</option>
<option value="./index.php?m=admin&stub=0">'.$AppUI->_('All').'</option>';


for ($c=65; $c < 91; $c++) {
	$cu = chr($c);
	$cell = ((strpos($let, $cu) > 0)
	         ? '<option value="./index.php?m=admin&stub=' . $cu . '">' . $cu . '</option>'
	         : '<option  disabled color="#999999">' . $cu . '</option>');
	$a2z .= $cell ;
}
$a2z .= "</select>\n</table>";
// Changing online stub to Select

// setup the title block
$titleBlock = new CTitleBlock('User Management', '', $m, "$m.$a");

$where = dPformSafe($where, true);

$titleBlock->addCell(('<form class="form-inline" action="index.php?m=admin" method="post">'
                      . '<div class="form-group"><input type="text" name="where" class="form-control" size="10" value="' . $where
                      . '" /> <input type="submit" value="' . $AppUI->_('search')
                      . '" class="button ce pi ahr" /></div><label class="pi" style="margin-left:20px;">   '.$AppUI->_('Show').':</label>
											</form>'),	'',	'', '');

$titleBlock->addCell($a2z);
$titleBlock->show();

?>
<script language="javascript">
<?php
// security improvement:
// some javascript functions may not appear on client side in case of user not having write permissions
// else users would be able to arbitrarily run 'bad' functions
if ($canDelete) {
?>
function delMe(x, y) {
	if (confirm("<?php echo $AppUI->_('doDelete', UI_OUTPUT_JS).' '.$AppUI->_('User', UI_OUTPUT_JS);?> " + y + "?")) {
		document.frmDelete.user_id.value = x;
		document.frmDelete.submit();
	}
}
<?php } ?>
</script>

<?php
$extra = '<td align="right" width="100%"><input type="button" class="button ce pi ahr" value="'.$AppUI->_('add user').'" onClick="javascript:window.location=\'./index.php?m=admin&a=addedituser\';" /></td>';

// tabbed information boxes
$tabBox = new CTabBox('?m=admin', (DP_BASE_DIR . '/modules/admin/'), $tab);
$tabBox->add('vw_active_usr', 'Active Users');
$tabBox->add('vw_inactive_usr', 'Inactive Users');
$tabBox->add('vw_usr_log', 'User Log');
if ($canEdit && $canDelete) {
	//$tabBox->add('vw_usr_sessions', 'Active Sessions');
}
$tabBox->show($extra);

?>

<form name="frmDelete" action="./index.php?m=admin" method="post">
	<input type="hidden" name="dosql" value="do_user_aed" />
	<input type="hidden" name="del" value="1" />
	<input type="hidden" name="user_id" value="0" />
</form>
</div>
