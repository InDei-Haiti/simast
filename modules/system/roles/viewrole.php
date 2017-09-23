<?php
$AppUI->savePlace();
$perms =& $AppUI->acl();
$role_id = $_GET['role_id'];
$role = $perms->getRole($role_id);
$canDelete = $perms->checkModule('roles', "delete");
if (isset($_GET['tab'])) {
	$AppUI->setState('RoleVwTab', $_GET['tab']);
}
$tab = $AppUI->getState('RoleVwTab') !== NULL ? $AppUI->getState('RoleVwTab') : 0;
echo '<br /><br /><div class="card">';//ajout card
if (! is_array($role)) {
	$titleBlock = new CTitleBlock('Invalid Role', 'main-settings.png', $m, "$m.$a");
	$titleBlock->addCrumb("?m=system&u=roles", "role list");
	$titleBlock->show();
} else {
	$titleBlock = new CTitleBlock('View Role', 'main-settings.png', $m, "$m.$a");
	$titleBlock->addCrumb("?m=system&u=roles", "role list");
	if ($canDelete)
    {
		$titleBlock->addCrumbDelete( 'Delete role', $canDelete, $msg );
	}	
	$titleBlock->show();
	// Now onto the display of the user.
?>
<script language="javascript">
<?php
if ($canDelete)
{
?>
function delIt()
{
	if (confirm( "<?php echo $AppUI->_('doDelete').' '.$AppUI->_('Role').'?';?>" ))
    {
		document.frmDelete.submit();
	}
}
<?php } ?>
</script>
<?php if ($canDelete)
{
?>

<form name="frmDelete" action="./index.php?m=system&a=roles" method="post">
	<input type="hidden" name="dosql" value="do_role_aed" />
	<input type="hidden" name="del" value="1" />
	<input type="hidden" name="role_id" value="<?php echo $role_id;?>" />
</form>
<?php } ?>
	<div class="mtab">
<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Role ID');?>:</td>
			<td class="hilite" width="100%"><?php echo $role["value"];?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Description');?>:</td>
			<td class="hilite" width="100%"><?php echo $AppUI->_($role["name"]);?></td>
		</tr>
</table>
</div>
	<br /><br /><br /><br />
<?php
	$tabBox = new CTabBox("?m=system&u=roles&a=viewrole&role_id=$role_id", "./modules/system/roles/", $tab );
	$tabBox->add( 'vw_role_perms', 'Permissions');
	$tabBox->show();
} // End of check for valid role
?>
</div>
