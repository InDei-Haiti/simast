<?php /* STYLE/DEFAULT $Id: header.php,v 1.38.4.2 2006/04/18 15:11:10 pedroix Exp $ */
$dialog = dPgetParam( $_GET, 'dialog', 0 );
if ($dialog)
	$page_title = '';
else
	$page_title = ($dPconfig['page_title'] == 'dotProject') ? $dPconfig['page_title'] . '&nbsp;' . $AppUI->getVersion() : $dPconfig['page_title'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta name="Description" content="dotProject Default Style" />
	<meta name="Version" content="<?php echo @$AppUI->getVersion();?>" />
	<meta http-equiv="Content-Type" content="text/html;charset=<?php echo isset( $locale_char_set ) ? $locale_char_set : 'UTF-8';?>" />
	<title><?php echo @dPgetConfig( 'page_title' );?></title>
	<link rel="stylesheet" type="text/css" href="./style/<?php echo $uistyle;?>/main.css" media="all" />
	<style type="text/css" media="all">@import "./style/<?php echo $uistyle;?>/main.css";</style>
	<link rel="shortcut icon" href="./style/<?php echo $uistyle;?>/images/favicon.ico" type="image/ico" />
	<?php @$AppUI->loadCSS(); ?>
</head>

<body onload="this.focus();">
<div style="display: none;">
    <img id="calImg" src="images/calendar.png" alt="Popup" class="trigger">
</div>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td nowrap="nowrap"><table width='100%' cellpadding=3 cellspacing=0 border=0><tr>
	<th background="style/<?php echo $uistyle;?>/images/titlegrad.jpg" class="banner" align="left"><strong><?php
		echo "<a style='color: white' href='{$dPconfig['base_url']}'>$page_title</a>";
	?></strong></th>
	</tr></table></td>
</tr>
<?php if (!$dialog) {
	// top navigation menu
	$nav = $AppUI->getMenuModules();
	$perms =& $AppUI->acl();
?>
<tr>
	<td class="nav" align="left" nowrap="nowrap">
	<table width="100%" cellpadding="3" cellspacing="0" width="100%">
	<tr>
		<td>
		<?php
		$links = array();
		foreach ($nav as $module) {
			if ($perms->checkModule($module['mod_directory'], 'access')) {
				$links[] = '<a href="?m='.$module['mod_directory'].'">'.$AppUI->_($module['mod_ui_name']).'</a>';
			}
		}
		echo implode( ' | ', $links );
		echo "\n";
		?>
		<form name="frm_new" method=GET action="./index.php">
		<input type="hidden" name="a" value="add" />
		</td>

<?php
	echo '        <td nowrap="nowrap" align="right">';
	$newItem = array( ""=>'- New Item -' );
	if ($perms->checkModule( 'clients', 'add' )) $newItem["clients"] = "New VCT/Intake";
	if ($perms->checkModule( 'clinics', 'add' )) $newItem["clinics"] = "New Center";
	if ($perms->checkModule( 'activity', 'add' )) $newItem["activity"] = "New Activity";
	if ($perms->checkModule( 'training', 'add' )) $newItem["training"] = "New Training";
	$newItem['followup'] = 'New Counseling Follow Up';
	$newItem['cbccheck'] = 'New CBC List';
	$newItem['chwcheck'] = 'New CHW List';
	$newItem['discharge'] = 'New Discharge/Transfer';
	$q = new DBQuery();
	$q->addTable('form_master');
	$q->addWhere('registry="1"');
	$q->addWhere('valid="1"');
	$q->addQuery('id,title');
	$regforms= $q->loadList();
	foreach ($regforms as $rname) {
		$newItem['wizard&amp;todo=addedit&amp;fid='.$rname['id']]='New '.$rname['title'];
	}

	//if ($perms->checkModule( 'files', 'add' )) $newItem["files"] = "File";
	//if ($perms->checkModule( 'projects', 'add' )) $newItem["projects"] = "Project";

	echo arraySelect( $newItem, 'm', 'style="font-size:10px" class="text" onChange="f=document.frm_new;mod=f.m.options[f.m.selectedIndex].value;if(f.m.options[f.m.selectedIndex].text == \'New Activity\') {f.a.value = \'add_activity\';  f.m.options[f.m.selectedIndex].value = \'training\';}if(mod) f.submit();"', '', true);



//build URI string
		if (isset( $company_id )) {
		echo '<input type="hidden" name="company_id" value="'.$company_id.'" />';
	}
	if (isset( $task_id )) {
		echo '<input type="hidden" name="task_parent" value="'.$task_id.'" />';
	}
	if (isset( $file_id )) {
		echo '<input type="hidden" name="file_id" value="'.$file_id.'" />';
	}
?>
		</form>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td>
		<table cellspacing="0" cellpadding="3" border="0" width="100%">
		<tr>
			<td width="100%"><?php echo $AppUI->_('Welcome')." $AppUI->user_first_name $AppUI->user_last_name"; ?></td>
			<td nowrap="nowrap">
<?php
	if ($perms->checkModule('calendar', 'access')) {
		$now = new CDate();
?>
		<a href="./index.php?m=calendar"><?php echo $AppUI->_('Calendar');?></a> |
                        <b><a href="./index.php?m=tasks&a=todo"><?php echo $AppUI->_('Todo');?></a></b> |

<?php } ?>
				<?php echo dPcontextHelp( 'Help' );?> |
				<a href="./index.php?logout=-1"><?php echo $AppUI->_('Logout');?></a>
			</td>
		</tr>
		</table>
	</td>
</tr>
<?php } // END showMenu ?>
</table>

<table width="100%" cellspacing="0" cellpadding="4" border="0">
<tr>
<td valign="top" align="left" width="98%">
<?php
	echo $AppUI->getMsg();
?>
