<?php 
GLOBAL $AppUI, $projects;

$perms = & $AppUI->acl ();

$is_superAdmin = false;
$roles = $perms->getUserRoles($AppUI->user_id);
foreach ($roles as $role){
	if($role['value']=='super_admin'){
		$is_superAdmin = true;
	}
}
$q = new DBQuery();
$q->addTable('projects');
if(!$is_superAdmin){
	$q->addJoin('permission_form','pf', 'project_id=pf.form');
	$q->addWhere('pf.user_id='.$AppUI->user_id);
	$q->addWhere('pf.form=project_id');
	$q->addWhere('pf.module="projects"');
	$option = $perms->getAcoIdByValue('view');
	$q->addWhere('find_in_set("'.$option.'",replace(type, " ", ","))');
}
$q->order_by = 'project_name';
$projects = $q->loadList();//border-bottom: 1px solid #bbbbbb;
?>
<br/>
<div class="card">
    <!--<div class="block-header">
        <h2>My Page</h2>
    </div>-->
    <div class="mtab">
        <table border="0" cellpadding="3" cellspacing="1" style="width: 100%;" class="tbl ck">
            <thead>
            <tr><th nowrap="nowrap" class="head header" width="auto">Project Name</th><th nowrap="nowrap" class="head" width="auto">Activity Name</th><th nowrap="nowrap" class="head" width="auto">Form Name</th></tr>
            </thead>
            <tbody>
            <?php
            /* $q = new DBQuery();
            $q->addTable($table);
            $q->addQuery('COUNT(*)');
            $q->addWhere($fld.'="'.$value.'"');
            echo $q->prepare(); */
            if($projects && is_array($projects)){
                foreach ($projects as $row) {
                    echo '<tr><td><a class="qn" href="/index.php?m=projects&a=view&project_id='.$row['project_id'].'&tab=0">'.$row['project_name'].'</a></td>
                    
                            <td>';
                    $q = new DBQuery();
                    $q->addTable('tasks');
                    $q->addWhere('task_project='.$row['project_id']);
                    if(!$is_superAdmin){
                        $q->addJoin('permission_form','pf', 'task_id=pf.form');
                        $q->addWhere('pf.user_id='.$AppUI->user_id);
                        $q->addWhere('pf.form=task_id');
                        $q->addWhere('pf.module="activity"');
                        $option = $perms->getAcoIdByValue('view');
                        $q->addWhere('find_in_set("'.$option.'",replace(type, " ", ","))');
                    }

                    $q->order_by = 'task_name';
                    $res = $q->loadList();
                    $list = array();
                    foreach ($res as $r){
                        $list[] = '<a href="/index.php?m=tasks&a=view&task_id='.$r['task_id'].'&tab=0&user=me">'.$r['task_name'].'</a>';
                    }
                    echo implode(', ', $list);
                    echo '</td>';

                    echo '<td>';
                    $q = new DBQuery();
                    $q->addTable('form_master');
                    $q->addWhere('project_id='.$row['project_id']);
                    if(!$is_superAdmin){
                        $q->addJoin('permission_form','pf', 'id=pf.form');
                        $q->addWhere('pf.user_id='.$AppUI->user_id);
                        $q->addWhere('pf.form=id');
                        $q->addWhere('pf.module="wizard"');
                        $option = $perms->getAcoIdByValue('view');
                        $q->addWhere('find_in_set("'.$option.'",replace(type, " ", ","))');
                    }

                    $q->order_by = 'title';
                    $res = $q->loadList();

                    $list = array();
                    foreach ($res as $r){
                        if($r['alltask'] && $r['forregistration'])
                            $list[] = '<a href="/index.php?m=projects&a=view&project_id='.$r['project_id'].'&tab=2&user=me">'.$r['title'].'</a>';
                        elseif($r['alltask'])
                            $list[] = '<a href="/index.php?m=projects&a=view&project_id='.$r['project_id'].'&user=me&fidu='.$r['id'].'">'.$r['title'].'</a>';
                        else $list[] = '<a href="/index.php?m=tasks&a=view&task_id='.$r['task_id'].'&user=me">'.$r['title'].'</a>';
                    }
                    echo implode(', ', $list);
                    echo '</td>';

                    echo '</tr>';
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<!--</div>-->




