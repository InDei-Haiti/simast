<?php /* PROJECTS $Id: vw_forums.php 5629 2008-02-29 16:26:48Z TheIdeaMan $ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

GLOBAL $AppUI, $project_id,$canEdit,$bobj;
// Forums mini-table in project view action
$q  = new DBQuery;
$q->addTable('forums');
$q->addQuery("forums.*,	DATE_FORMAT(forum_last_date, '%d-%b-%Y %H:%i' ) forum_last_date,
	project_name, project_color_identifier, project_id");
$q->addJoin('projects', 'p', 'project_id = forum_project');
$q->addWhere("forum_project = $project_id");
$q->addOrder('forum_project, forum_name');
$rc = $q->exec();

//$category_types = dPgetSysVal("FileType");
$sector_types = dPgetSysVal ( 'SectorType' );
$activity_types = dPgetSysVal('TaskType');

$q = new DBQuery ( );
$q->addTable ( 'administrative_regions' );
$q->addQuery ( 'region_id, region_name' );
$q->addWhere ( 'region_parent = 0' );
$country_list =  $q->loadHashList () ;

$adm_rights = $AppUI->isAdmin();

$letEdit = $bobj->localCheck($AppUI->user_id,$project_id);

?>
<form method="get" action="./index.php" name='filink'>
<input type="hidden" name="m" value="forums" />
<input type="hidden" name="a" value="addedit" />
<input type="hidden" name="forum_project" value="<?php echo $project_id; ?>" />
<!--   <input type="submit" value="new forum" class="button" /> -->
<?php if($canEdit && ($letEdit || $adm_rights)){ ?>
<a href='#' onclick="document.filink.submit();"><b>New Forum</b></a>
<?php }?>
</form><br>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl tablesorter" id='tfor_sort'>
<thead>
<tr>
	<th nowrap>&nbsp;</th>
	<th nowrap ><?php echo $AppUI->_('Forum Name');?></th>
	<!-- <th nowrap ><?php echo $AppUI->_('Category');?></th> -->
	<?php if($project_id == 0) { ?> ><th nowrap ><?php echo $AppUI->_('Project');?></th><?php }?>
	<th nowrap ><?php echo $AppUI->_('Country');?></th>
	<th nowrap ><?php echo $AppUI->_('Sector');?></th>
	<th nowrap ><?php echo $AppUI->_('Activity type');?></th>
	<th nowrap><?php echo $AppUI->_('Messages');?></th>
	<th nowrap><?php echo $AppUI->_('Last Post');?></th>
</tr>
</thead>
<tbody>
<?php
while ($row = db_fetch_assoc( $rc )) { ?>
<tr>
	<td nowrap align=center>
<?php
	if ($row["forum_owner"] == $AppUI->user_id || $adm_rights || $letEdit) { ?>
		<A href="./index.php?m=forums&a=addedit&forum_id=<?php echo $row["forum_id"];?>"><img src="./images/icons/pencil.gif" alt="expand forum" border="0" width=12 height=12></a>
<?php } ?>
	</td>
	<td nowrap>
		<a id='for_<?php echo $row['forum_id'];?>' href="./index.php?m=forums&a=viewer&forum_id=<?php echo $row["forum_id"];?>" 
			onmouseout="nd();" onmouseover="return overlib('<div><p><?php echo mysql_real_escape_string($row["forum_description"]);?></p></div>', CAPTION, 'Description', CENTER);" ><?php echo $row["forum_name"];?></a></td>
	<!-- <td nowrap><?php echo $category_types[$row["forum_category"]];?></td> -->
	<?php if($project_id  == 0) { ?><td nowrap><?php echo $row["project_name"];?></td><?php  } ?>
	<td nowrap><?php echo $country_list[$row["forum_country"]];?></td>
	<td nowrap><?php echo $sector_types[$row["forum_sector"]];?></td>
	<td nowrap><?php echo $activity_types[$row["forum_activity_type"]];?></td>
	<td nowrap><?php echo $row["forum_message_count"];?></td>
	<td nowrap>
		<?php echo (intval( $row["forum_last_date"] ) > 0) ? $row["forum_last_date"] : 'n/a'; ?>
	</td>
</tr>
<?php }
$q->clear();
?>
</tbody>
</table>
<?php  
global $myspeed;
$myspeed->addJs('$j(document).ready(function() {jQuery("#tfor_sort").tablesorter();});','code');
?>