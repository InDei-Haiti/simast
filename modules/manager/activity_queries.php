<?php /* PROJECTS $Id: vw_files.php 4800 2007-03-06 00:34:46Z merlinyoda $ */
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}

GLOBAL $AppUI, $project_id, $deny, $canRead, $canEdit, $dPconfig;
//global $TView;
//$showProject = false;

$TView = "manager";
$q = new DBQuery();
$q->addTable('activity_queries','act');
$q->addQuery('act.id,act.qname,act.qdesc,act.created,act.activity_id,act.st_area_id,act.beneficieries,act.amount,st.prex,st.title,t.task_name');
$q->addJoin('st_area','st','act.st_area_id=st.id');
$q->addJoin('tasks','t','act.activity_id=t.task_id');
$activity_queries = $q->loadList();
?>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl tablesorter" id="tbl">
	<thead>

		<tr>
			<th nowrap="nowrap">&nbsp;</th>
			<th>Query Name</th>
			<th>Activity</th>
            <th>Strategic Areas</th>
			<th>Beneficieries</th>
			<th>Amount</th>
			<th>Description</th>
		</tr>
    <?php foreach($activity_queries as $activity_querie){?>
            <tr>
                <td></td>
                <td><?php echo$activity_querie['qname'] ?></td>
                <td>
                    <?php
                        echo $activity_querie['task_name']
                    ?>
                </td>
                <td title="<?php echo $activity_querie['title']?>">
                    <?php
                    echo $activity_querie['prex']
                    ?>
                </td>
                <td><?php echo$activity_querie['beneficieries'] ?></td>
                <td><?php echo$activity_querie['amount'] ?></td>
                <td><?php echo$activity_querie['qdesc'] ?></td>
            </tr>
    <?php }?>
</thead>
<tbody>
</tbody>
</table>

