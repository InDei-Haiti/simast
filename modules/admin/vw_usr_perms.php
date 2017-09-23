<?php /* ADMIN $Id: vw_usr_perms.php 5625 2008-02-17 09:47:57Z ajdonnison $ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

GLOBAL $AppUI, $user_id, $canEdit, $canDelete, $tab;

$perms =& $AppUI->acl();
$module_list = $perms->getModuleList();
$is_superAdmin = false;
$roles = $perms->getUserRoles($AppUI->user_id);
foreach ($roles as $role){
	if($role['value']=='super_admin'){
		$is_superAdmin = true;
	}
}
//var_dump($perms->getRole($perms->get_group_id('super_admin')));
//var_dump($perms->getUserRoles($AppUI->user_id));

//get list of 'real' modules
$pgos = array();
$q  = new DBQuery;
$q->addTable('modules', 'm');
$q->addQuery('mod_id, mod_name, permissions_item_table');
$q->addWhere('permissions_item_table is not null');
$q->addWhere("permissions_item_table <> ''");
$module_pgo_list = $q->loadHashList('mod_name');
$q->clear();

//list of additional 'pseudo-modules'
$pseudo_module_pgo_list = array('File Folders' => array('mod_id' => -1, 
                                                        'mod_name' => 'file_folders', 
                                                        'permissions_item_table' => 'file_folders')
                                );

//combine modules and 'pseudo-modules'
$pgo_list = arrayMerge($module_pgo_list, $pseudo_module_pgo_list);

// Build an intersection array for the modules and their listing
$modules = array();
$offset = 0;
foreach ($module_list as $module) {
	$modules[ $module['type'] . "," . $module['id']] = $module['name'];
	if ($module['type'] = 'mod' && isset($pgo_list[$module['name']])) {
		$pgos[$offset] = $pgo_list[$module['name']]['permissions_item_table'];
	} 
	$offset++;
}
$count = 0;

//Pull User perms
$user_acls = $perms->getUserACLs($user_id);
if (! is_array($user_acls))
  $user_acls = array(); // Stops foreach complaining.
$perm_list = $perms->getPermissionList();


$q = new DBQuery();
$q->addTable('permission_form');
$q->addWhere('user_id='.$user_id);
$q->exec();
$perm_form_list = array();
while ($row = $q->fetchRow()) {
	$perm_form_list[] = $row;
}

?>

<script language="javascript">
<?php
// security improvement:
// some javascript functions may not appear on client side in case of user not having write permissions
// else users would be able to arbitrarily run 'bad' functions
if ($canEdit) {
?>
function editPerm( id, gon, it, vl, nm ) {
/*
	id = Permission_id
	gon =permission_grant_on
	it =permission_item
	vl =permission_value
	nm = text representation of permission_value
*/
//alert( 'id='+id+'\ngon='+gon+'\nit='+it+'\nvalue='+vl+'\nnm='+nm);
	var f = document.frmPerms;

	f.sqlaction2.value = "<?php echo $AppUI->_('edit'); ?>";
	
	f.permission_id.value = id;
	f.permission_item.value = it;
	f.permission_item_name.value = nm;
	for(var i=0, n=f.permission_grant_on.options.length; i < n; i++) {
		if (f.permission_module.options[i].value == gon) {
			f.permission_module.selectedIndex = i;
			break;
		}
	}
	f.permission_value.selectedIndex = vl+1;
	f.permission_item_name.value = nm;
}

function clearIt(){
	var f = document.frmPerms;
	f.sqlaction2.value = "<?php echo $AppUI->_('add'); ?>";
	f.permission_id.value = 0;
	f.permission_grant_on.selectedIndex = 0;
}

function delIt(id) {
	if (confirm( 'Are you sure you want to delete this permission?' )) {
		var f = document.frmPerms;
		f.del.value = 1;
		f.permission_id.value = id;
		f.submit();
	}
}

function fDelIt(id) {
	if (confirm( 'Are you sure you want to delete this permission?' )) {
		var f = document.frmPerms1;
		f.delf.value = 1;
		f.pf_id.value = id;
		f.submit();
	}
}

var tables = new Array;
<?php
	foreach ($pgos as $key => $value){
		// Find the module id in the modules array
		echo "tables['$key'] = '$value';\n";
	}
?>

function popPermItem() {
	var f = document.frmPerms;console.log(f);
	var pgo = f.permission_module.selectedIndex;

	if (!(pgo in tables)) {
		alert( '<?php echo $AppUI->_('No list associated with this Module.', UI_OUTPUT_JS); ?>' );
		return;
	}
	f.permission_table.value = tables[pgo];
	window.open('./index.php?m=public&a=selector&dialog=1&callback=setPermItem&table=' + tables[pgo], 'selector', 'left=50,top=50,height=250,width=400,resizable')
}

// Callback function for the generic selector
function setPermItem( key, val ) {
	var f = document.frmPerms;
	if (val != '') {
		f.permission_item.value = key;
		f.permission_item_name.value = val;
		f.permission_name.value = val;
	} else {
		f.permission_item.value = '0';
		f.permission_item_name.value = 'all';
		f.permission_table.value = '';
	}
}
var project;
function populatetask(project_id){
	project = project_id;
	$j("#imgtasks").show();
	$j.get("/?m=admin&suppressHeaders=1", {mode: "loadtask", pid: project_id}, function (msg) {
//		alert(msg);
		if (msg && msg !== 'fail') {
			project = project_id;
			msg = $j.parseJSON(msg);
			$j("#tasks").empty();
			$j("#tasks").append("");
			$j("#tasks").append('<option value="-1"><?php echo '-- '.$AppUI->_('Select Activity').' --'?></option>');
			$j("#tasks").append('<option value="all">All</option>');
			$.each(msg, function(i,e){	
				$j("#tasks").append('<option value="'+msg[i].task_id+'">'+msg[i].task_name+'</option>');
			});
		} else {		
			$j("#msgbox").text("Invalid form selected!").show().delay(2000).fadeOut(3000);			
		}
		$j("#imgtasks").hide();
	});
}

function populateform(task_id){
	$j("#imgforms").show();
	$j.get("/?m=admin&suppressHeaders=1", {mode: "loadform", tid: task_id, pid: project}, function (msg) {
//		alert(msg);
		if (msg && msg !== 'fail') {
			msg = $j.parseJSON(msg);
			$j("#forms").empty();
			$j("#forms").append("");
			$j("#forms").append('<option value="-1"><?php echo '-- '.$AppUI->_('Select Form').' --'?></option>');
			$j("#forms").append('<option value="all">All</option>');
			$.each(msg, function(i,e){	
				$j("#forms").append('<option value="'+msg[i].id+'">'+msg[i].title+'</option>');
			});
		} else {		
			$j("#msgbox").text("Invalid form selected!").show().delay(2000).fadeOut(3000);			
		}
		$j("#imgforms").hide();
	});
}
<?php } ?>
</script>

<table width="100%" border="0" cellpadding="2" cellspacing="0" class="mtab">
<tr><td width="50%" valign="top">

<table width="100%" border="0" cellpadding="2" cellspacing="1" class=>
<tr>
	<th width="50%"><?php echo $AppUI->_('Module');?></th>
	<th width="50%"><?php echo $AppUI->_('Item');?></th>
	<th nowrap><?php echo $AppUI->_('Type');?></th>
	<th nowrap><?php echo $AppUI->_('Status');?></th>
	<th>&nbsp;</th>
</tr>

<?php
foreach ($user_acls as $acl){
	$buf = '';
	$permission = $perms->get_acl($acl);

	$style = '';
	// TODO: Do we want to make the colour depend on the allow/deny/inherit flag?
	// Module information.
	if (is_array($permission)) {
		$buf .= "<td $style>";
		$modlist = array();
		$itemlist = array();
		if (is_array($permission['axo_groups'])) {
			foreach ($permission['axo_groups'] as $group_id) {
				$group_data = $perms->get_group_data($group_id, 'axo');
				$modlist[] = $AppUI->_($group_data[3]);
				$itemlist[] = $AppUI->_('ALL');
			}
		}
		if (is_array($permission['axo'])) {
			foreach ($permission['axo'] as $key => $section) {
				// Find the module based on the key
				$mod_info = $perms->get_object_full($key, 'app', 1, 'axo');
				if ($mod_info['name']) {
					$modlist[] = $AppUI->_($mod_info['name']);
				} else {
					$itemlist[] = $AppUI->_('ALL');
				}
				foreach ($section as $id) {
					$mod_data = $perms->get_object_full($id, $key, 1, 'axo');
					if ($mod_info['name']) {
						$itemlist[] = $AppUI->_($mod_data['name']);
					} else {
						$modlist[] = $AppUI->_($mod_data['name']);
					}
				}
			}
		}
		$buf .= implode("<br />", $modlist);
		$buf .= "</td><td>";
		$buf .= implode("<br />", $itemlist);
		$buf .= "</td>";
		// Item information TODO:  need to figure this one out.
	// 	$buf .= "<td></td>";
		// Type information.
		$buf .= "<td>";
		$perm_type = array();
		if (is_array($permission['aco'])) {
			foreach ($permission['aco'] as $key => $section) {
				foreach ($section as $value) {
					$perm = $perms->get_object_full($value, $key, 1, 'aco');
					$perm_type[] = $AppUI->_($perm['name']);
				}
			}
		}
		$buf .= implode("<br />", $perm_type);
		$buf .= "</td>";

		// Allow or deny
		$buf .= "<td>" . $AppUI->_( $permission['allow'] ? 'allow' : 'deny' ) . "</td>";
		$buf .= '<td nowrap>';
		if ($canDelete) {
			$buf .= "<a href=\"javascript:delIt({$acl});\" title=\"".$AppUI->_('delete')."\">"
				. dPshowImage( './images/icons/stock_delete-16.png', 16, 16, '' )
				. "</a>";
		}
		$buf .= '</td>';
		
		echo "<tr>$buf</tr>";
	}
}
?>
</table>

</td>
<td width="50%" valign="top">

<?php if ($canEdit) {?>

<table cellspacing="1" cellpadding="2" border="0" class="mtab" width="100%">
<form name="frmPerms" method="post" action="?m=admin">
	<input type="hidden" name="del" value="0" />
	<input type="hidden" name="dosql" value="do_perms_aed" />
	<input type="hidden" name="user_id" value="<?php echo $user_id;?>" />
	<input type="hidden" name="permission_user" value="<?php echo $perms->get_object_id("user", $user_id, "aro");?>" />
	<input type="hidden" name="permission_id" value="0" />
	<input type="hidden" name="permission_item" value="0" />
	<input type="hidden" name="permission_table" value="" />
	<input type="hidden" name="permission_name" value="" />
<tr>
	<th colspan="2"><?php echo $AppUI->_('Add Module Permission');?></th>
</tr>
<tr>
	<td nowrap align="right"><?php echo $AppUI->_('Module');?>:</td>
	<td width="100%"><?php echo arraySelect($modules, 'permission_module', 'size="1" class="form-control"', 'grp,all', true);?></td><br />
</tr>
<tr>
	<td nowrap align="right"><?php echo $AppUI->_('Item');?>:</td>
	<td>
		<input type="text" name="permission_item_name" class="form-control" style="width: 80px;" size="30" value="all" disabled>
		<input type="button" name="" class="button ce pi ahr" value="..." onclick="popPermItem();">
	</td>
</tr>
<tr>
	<td nowrap align="right"><?php echo $AppUI->_('Access');?>:</td>
	<td>
		<select name="permission_access" class="text form-control">
			<option value='1'><?php echo $AppUI->_('allow');?></option>
			<option value='0'><?php echo $AppUI->_('deny');?></option>
		</select>
	</td>
</tr>
<?php
	foreach ($perm_list as $perm_id => $perm_name) {
?>
<tr>
	<td nowrap="nowrap" align="right"><label for="permission_type_<?php echo $perm_id; ?>"><?php echo $AppUI->_($perm_name);?>:</label></td>
	<td>
	  <input type="checkbox" name="permission_type[]" id="permission_type_<?php echo $perm_id; ?>" value="<?php echo $perm_id;?>" />
	</td>
</tr>
<?php
	}
?>
<tr>
	<td>
		<input type="reset" value="<?php echo $AppUI->_('clear');?>" class="button ce pi ahr" name="sqlaction" onClick="clearIt();">
	</td>
	<td align="right">
		<input type="submit" value="<?php echo $AppUI->_('add');?>" class="button ce pi ahr" name="sqlaction2">
	</td>
</tr>
</form>
</table>











<?php } ?>

</td>

</tr>

<tr>

<td width="50%" valign="top">

	<table width="100%" border="0" cellpadding="2" cellspacing="1" class="mtab">
		<tr>
			<th width="50%"><?php echo $AppUI->_('Projects').'/'.$AppUI->_('Activity').'/'.$AppUI->_('Form');?></th>
			<!-- <th width="50%"><?php echo $AppUI->_('Modules');?></th> -->
			<th nowrap><?php echo $AppUI->_('Type');?></th>
			<th nowrap><?php echo $AppUI->_('Status');?></th>
			<th>&nbsp;</th>
		</tr>
		<?php
		foreach ($perm_form_list as $perm) {
		?>
			<tr>
				<td nowrap="nowrap" align="center"><?php 
				if($perm['module']==='wizard'){
					$q = new DBQuery();
					$q->addTable('form_master','f');
					$q->addJoin('projects', 'p', 'p.project_id=f.project_id');
					$q->addQuery("f.title,f.alltask,f.task_id,p.project_name");
					$q->addWhere('id='.$perm['form']);
					$res = $q->loadList();
					foreach ($res as $r){
						$task = '';
						if($r['alltask'])
							$task = 'All';
						else{
							$q = new DBQuery();
							$q->addTable('tasks');
							$q->addQuery('task_name');
							$q->addWhere('task_id='.$r['task_id']);
							$task = $q->loadResult();
						}
						echo '<h4>'.$r['project_name'].'<span style="color:red">/</span>'.$task.'<span style="color:red">/</span>'.$r['title'].'</h4>';
					}
				}elseif($perm['module']==='activity'){
					$q = new DBQuery();
					$q->addTable('tasks');
					$q->addJoin('projects', 'p', 'p.project_id=task_project');
					$q->addQuery("task_name,p.project_name");
					$q->addWhere('task_id='.$perm['form']);
					$res = $q->loadList();
					foreach ($res as $r){
						echo '<h4>'.$r['project_name'].'<span style="color:red">/</span>'.$r['task_name'].'</h4>';
					}
				}elseif($perm['module']==='projects'){
					$q = new DBQuery();
					$q->addTable('projects');
					$q->addQuery("project_name");
					$q->addWhere('project_id='.$perm['form']);
					echo '<h4>'.$q->loadResult().'</h4>';
				}
				?></td>
				<!-- <td nowrap="nowrap" align="center"><?php echo $perm['module']?></td> -->
				<td nowrap="nowrap" align="center"><?php 
					$perm_type = array();
					$permf = explode(',', $perm['type']);
					foreach ($permf as $key => $section) {
						$qp = new DBQuery();
						$qp->addTable('gacl_aco');
						$qp->addWhere('id='.$section);
						$qp->addQuery('name');
						$res = $qp->loadResult();
						$perm_type[] = $AppUI->_($res);
					}
					$perm_type = implode("<br />", $perm_type);
					echo $perm_type;
				?></td>
				<td nowrap="nowrap" align="center"><?php echo $AppUI->_( $perm['status'] ? 'allow' : 'deny' ) ?></td>
				<td nowrap="nowrap" align="center">
					<?php 
					$buf = "<a href=\"javascript:fDelIt({$perm['pf_id']});\" title=\"".$AppUI->_('delete')."\">"
				. dPshowImage( './images/icons/stock_delete-16.png', 16, 16, '' )
				. "</a>";
					echo $buf;
					?>
				</td>
			</tr>
		<?php
			}
		?>
	</table>
</td>
<td width="50%" valign="top">
	<table cellspacing="1" cellpadding="2" border="0" class="mtab" width="100%">
<form name="frmPerms1" method="post" action="?m=admin">
	<input type="hidden" name="delf" value="0" />
	<input type="hidden" name="dosql" value="do_perms_aed" />
	<input type="hidden" name="user_id" value="<?php echo $user_id;?>" />
	<input type="hidden" name="pf_id" value="0" />
	<input type="hidden" name="perms_form" value="1" />
<tr>
	<th colspan="2"><?php echo $AppUI->_('Add Project/Activity/Form Permission');?></th>
</tr>
<tr><td nowrap align="right"><?php echo $AppUI->_('Projects');?>:</td>
	
	<td width="100%"><?php 
	$q = new DBQuery();
	$q->addTable('projects');
	
	$q->addQuery("project_id,project_name");
	if(!$is_superAdmin){
		$q->addJoin('permission_form','pf', 'project_id=pf.form');
		$q->addWhere('pf.user_id='.$AppUI->user_id);
		$q->addWhere('pf.form=project_id');
		$q->addWhere('pf.module="projects"');
		$option = $perms->getAcoIdByValue('view');
		$q->addWhere('find_in_set("'.$option.'",replace(type, " ", ","))');
	}
	$projects = $q->loadHashList();
	
	echo arraySelect(arrayMerge(array('-1'=>'-- '.$AppUI->_('Select Project').' --'), $projects), 'projects', 'size="1" class="form-control" onchange="populatetask(this.value);"', 'grp,all', true);
	?></td></tr>

<tr><td nowrap align="right"><?php echo $AppUI->_('Activities');?>:</td>
	
	<td width="100%">
		<select name="tasks" id="tasks" onchange="populateform(this.value);" class="form-control">
			<option value="-1">-- <?php echo $AppUI->_('Select Activity');?> --</option>
		</select>
		<img id="imgtasks" style="display: none" alt="" src="/images/progress.gif">
	</td></tr>

<tr><td nowrap align="right"><?php echo $AppUI->_('Form wizard');?>:</td>
	
	<td width="100%">
		<select name="forms" id="forms" class="form-control">
			<option value="-1">-- <?php echo $AppUI->_('Select Forms');?> --</option>
		</select>
		<img id="imgforms" style="display: none" alt="" src="/images/progress.gif">
	</td></tr>
	
<tr>
	<td nowrap align="right"><?php echo $AppUI->_('Access');?>:</td>
	<td>
		<select name="permission_access" class="form-control">
			<option value='1'><?php echo $AppUI->_('allow');?></option>
			<option value='0'><?php echo $AppUI->_('deny');?></option>
		</select>
	</td>
</tr>
<?php
	foreach ($perm_list as $perm_id => $perm_name) {
?>
<tr>
	<td nowrap="nowrap" align="right"><label for="permission_type_<?php echo $perm_id; ?>"><?php echo $AppUI->_($perm_name);?>:</label></td>
	<td>
	  <input type="checkbox" name="permission_type[]" id="permission_type_<?php echo $perm_id; ?>" value="<?php echo $perm_id;?>" />
	</td>
</tr>
<?php
	}
?>
<tr>
	<td>
		<input type="reset" value="<?php echo $AppUI->_('clear');?>" class="button ce pi ahr" name="sqlaction" onClick="clearIt();">
	</td>
	<td align="right">
		<input type="submit" value="<?php echo $AppUI->_('add');?>" class="button  ce pi ahr" name="sqlaction2">
	</td>
</tr>
</form>
</table>
</td>
</tr>





</table>






