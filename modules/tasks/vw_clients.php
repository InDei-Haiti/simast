<?php
require_once($AppUI->getModuleClass('clients'));
global $contact_id;
global $currentTabId;
global $currentTabName;
global $tabbed;
global $limit;
global $dPconfig;
global $page;

global $task_name;
$search = false;
$task_id = intval( dPgetParam( $_GET, "task_id", 0 ) );

//$obj = new CContact();
/*
$obj->load($contact_id);
$offset = ($page - 1) * $limit;
$obj->setRoleViewLimits($limit,$offset);

//pager settings
$count = $obj->getRoleCount();
$qid  = $obj->getContactRoles();



$num_pages = ceil ($count / $limit);


if ($where != '%') $search=true;

if ($offset < 0)
{
	$limit = intval($count);
	$offset = 0;
}*/
//var_dump($count);
//var_dump($offset);
//var_dump($limit);
/*
$q = new DBQuery;
$q->setLimit($limit, $offset  );	
$q->addTable('clients', 'c');
$q->innerJoin('counselling_info', 'ci', 'ci.counselling_client_id = c.client_id');
$q->innerJoin('contacts', 'si', 'ci.counselling_staff_id = si.contact_id');
$q->addQuery('c.client_adm_no, c.client_first_name, c.client_last_name, "Intake Officer" AS role');
$q->addWhere("si.contact_id=$contact_id");
//$q->addQuery('');
$q->addTable('clients', 'c');
$q->innerJoin('admission_info', 'ai', 'ai.admission_client_id = c.client_id');
$q->innerJoin('contacts', 'sa', 'ai.admission_staff_id = sa.contact_id');
$q->addQuery('UNION c.client_adm_no, c.client_first_name, c.client_last_name, "Admission Officer" AS role');
$q->addWhere("sa.contact_id=$contact_id");
*/
//$sql = $q->prepare();
//var_dump($sql);

//$rows = $roles;

//echo ( printPageNavigation( "?m=contacts&a=view&contact_id=".(int)$contact_id, $page, $num_pages, $offset, $limit, $count, 'Clients Assigned to this staff member'));
$q = new DBQuery();
$q->addTable('tasks');
$q->addQuery('task_project');
$q->addWhere('task_id='.$task_id);

$project = $q->loadResult();

$q->clear();
$q->addTable('form_master');
$q->addQuery('id');
$q->addWhere('project_id='.$project.' AND forregistration=1');
$fuid = $q->loadResult();


$wz = new Wizard(null);
$wz->loadFormInfo($fuid);
$fields = $wz->showFieldsImport();
//
$formsFlds = array();
if (count($fields['notms']) > 0) {
	foreach ($fields['notms'] as $nitem) {
		$formsFlds[$nitem['fld']] = $nitem['title'];
	}
}
$digest = $wz->getDigest();



$page=1;
if (isset($_GET['p']) && is_numeric($_GET['p'])) {
	$page = intval($_GET['p']);
	/* if ($page >= 1 && $page <= $nbPages) {
		// cas normal
		$current=$page;
	} else  */
	if ($page < 1) {
		// cas où le numéro de page est inférieure 1 : on affecte 1 à la page courante
		$page=1;
	}/* else {
	//cas où le numéro de page est supérieur au nombre total de pages : on affecte le numéro de la dernière page à la page courante
	$current = $nbPages;
	}*/
}

$epp = (int)$_GET['epp'];

if(!$epp){
	$epp = 100;
}

$q->clear();
$q->addTable('wform_'.$fuid);
$q->addQuery("count(*)");
$q->addWhere('wform_'.$fuid.'.id IN (SELECT registry_id FROM beneficieries WHERE task_id='.$task_id.' AND form_id='.$fuid.')');
if(isset($_GET['user']) && $_GET['user']==='me'){
	$q->addWhere('wform_'.$fuid.'.user_creator='.$AppUI->user_id);
}
$countQuery = $q->loadResult();

$start = $page * $epp - $epp;
//$end = $start + $epp;
$nbPages = ceil($count/$epp);


if($page==1){
	$prev = '<h3 style="color:#c0c0c0;margin-top:-5px;">Prev</h3>';
	if($page<$nbPages){
		$next = '<a href="'.$pageURL."&p=".($page + 1).'"><h3>Next</h3></a>';
	}else{
		$next = '<h3 style="color:#c0c0c0;margin-top:-5px;">Next</h3>';
	}
}elseif($page>1){
	//$pageURL .= "&p=".($page - 1);
	$prev = '<a href="'.$pageURL."&p=".($page - 1).'"><h3>Prev</h3></a>';
	if($page<$nbPages){
		$next = '<a href="'.$pageURL."&p=".($page + 1).'"><h3>Next</h3></a>';
	}else{
		$next = '<h3 style="color:#c0c0c0;margin-top:-5px;">Next</h3>';
	}
}



$q->clear();
$q->addTable('wform_'.$fuid);
$q->addQuery('DISTINCT(wform_'.$fuid.'.id),wform_'.$fuid.'.*');
$q->addJoin('beneficieries', 'b', 'b.registry_id='.'wform_'.$fuid.'.id');
if(isset($_GET['user']) && $_GET['user']==='me'){
	$q->addWhere('wform_'.$fuid.'.user_creator='.$AppUI->user_id);
}
$q->addWhere('wform_'.$fuid.'.id IN (SELECT registry_id FROM beneficieries WHERE task_id='.$task_id.' AND form_id='.$fuid.') ORDER BY b.date_entry DESC');

$q->offset = $start;
$q->limit = $epp;
$clients = $q->loadHashList('id');
$currentNbr = $start + count($clients);


//var_dump($fields['notms']);
function getSysval($array,$field,$val){
	$result = false;
	if (count($array) > 0) {
		foreach ($array as $nitem) {
			if($nitem['fld']===$field){
				$psv = $nitem["raw"]["sysv"];
				if($psv==='SysCommunes' || $psv==='SysCommunalSection'){
					if($psv==='SysCommunes'){
						$q = new DBQuery();
						$q->addTable("administration_com");
						$q->addQuery("administration_com_name");
						$q->addWhere('administration_com_code="'.$val.'"');
						$result = $q->loadResult();
					}
						
					if($psv==='SysCommunalSection'){
						$q = new DBQuery();
						$q->addTable("administration_section");
						$q->addQuery("administration_section_name");
						$q->addWhere('administration_section_code="'.$val.'"');
						$result = $q->loadResult();
					}
				}else{
					$tab = dPgetSysValSet($psv);
					$result = $tab[$val];
				}
			}
		}
	}
	return $result;
}
?>

<!-- <table id="qtable" class="tablesorter tbl" cellpadding=2 cellspacing=1 border=0 style="width: 100%"> -->
<?php
$count = count($digest)+2;
//echo '<tr><th colspan="'.$count.'" align="right" valign="top" style="background-color:#ffffff">';
//echo '<div>';
//echo '<input type="button" class=button style="float:right" value="'.$AppUI->_( 'Add New Beneficiery' ).'" onClick="javascript:window.location=\'./index.php?a=addedit&m=clients&activity_id='.$activity_id.'\'">';
//echo '<input type="button" class=button style="float:right" value="'.$AppUI->_( 'Add Beneficiaries' ).'" onClick="window.location=\'/index.php?m=tasks&a=selector&task_id='.$task_id.'\'/*popSelects(\'client\','.$task_id.')*/">';

//echo '</div>';
//echo '</th></tr>';
/*?>

	<tr>
		<th><?php echo $AppUI->_( 'Date')?></th>
		<?php foreach ($digest as $fld){?>
				<th><?php echo $formsFlds[$fld]?></th>
		<?php }?>
		<th></th>
	</tr>
<tbody>
<?php */

    $gt = new genericTable();
    $headers = array();
    $i = 0;
    $headers['*'] = 'string';
    $i += 1;
    $headers['Date'] = 'string';
    $i += 1;
    foreach ($digest as $fld){
    	if($formsFlds[$fld])
    		$headers[$formsFlds[$fld]] = 'string';
    	$i += 1;
    }
	$gt->makeHeader($headers);
	$decs = array(//0=>'<a href="/index.php?m=clients&a=view&client_id=##4##">##0##</a>'//,1=>'date',2=>'date'
				0 => '<a href="?m=wizard&a=form_use&fid='.$fuid.'&idIns=##'.(0).'##&todo=view&teaser=1&rtable=1&tab=0">View</a>'
		);
	$gt->setDecorators($decs); 
	
	foreach($clients as $client_id => $client_data)
	{
		//$checked = in_array($client_id, $clients_id) ? 'checked="checked"' : '';
		/*echo "<tr>\n\t".'<td style="width: 30px"><input type="checkbox" name="client_id[]" id="client_'.$client_id.'" value="'.$client_id.'" '.$checked.' /></td>
			<td>'.$client_data['client_cin'].'</td>';
		echo '<td >
				<label for="client_'.$client_id.'" data-skort="'.$client_data['client_last_name'].'">'.$client_data['client_first_name'].' '.$client_data['client_last_name'].'</label>
			</td>';*/
		/* echo "<tr>\n\t";
		echo '<td >'.$client_data['date_entry'].'
			</td>'; */
		$row_data = array();
		if(!$client_data['entry_date']){
			$client_data['entry_date'] = '';
		}
		$row_data[] = $client_data['id'];
		$row_data[] = $client_data['entry_date'].'';
		foreach ($digest as $fld){
			if($client_data[$fld]=='-1')
				$client_data[$fld] = '';
			$test =getSysval($fields['notms'],$fld,strval($client_data[$fld]));
			if($test){
				$client_data[$fld] = $test;
				
			}
			$row_data[] = $client_data[$fld];
			/* echo '<td >'.$client_data[$fld].'</label>
			</td>'; */
			
		}
		
		/* echo '<pre>';
		echo $client_id.' => ';
		var_dump($row_data);
		echo '<pre>';
		echo '<br/><br/><br/>';  */
		/* echo count($row_data);
		echo '<br/><br/><br/>'; */
		$gt->fillBody($row_data);
		/* echo '<td><a href="?m=wizard&a=form_use&fid='.$fuid.'&idIns='.$client_id.'&todo=view&teaser=1&rtable=1&tab=0">'.$AppUI->_( 'View').'</a></td>';
		echo "\n</tr>\n"; */
	}
	
	
	
	$selected1 = "";
	$selected2 = "";
	$selected3 = "";
	$selected4 = "";
	if($epp===100){
		$selected1 = " selected=selected";
	}
	if($epp===500){
		$selected2 = " selected=selected";
	}
	if($epp===1000){
		$selected3 = " selected=selected";
	}
	if($epp===2000){
		$selected4 = " selected=selected";
	}
	if($countQuery>$start+$epp){
		$val = $start+$epp;
	}else{
		$val = $countQuery;
	}
	$info = "Total Records: ".$countQuery." Viewing Records: ".($start+1)."-".$val." <span style='color: #08245b;'><b>Rows per page</b></span> <select name='epp' onchange='window.location.href = window.location.href+\"&epp=\"+this.value'><option ".$selected1.">100</option><option ".$selected2.">250</option><option ".$selected3.">500</option></select>";
	
	//$code .= '<td class="alt" style="width: 200px"><a href="?m=wizard&a=form_use&fid='.$idfw.'&idIns='.$drow["id"].'&todo=view&teaser=1&rtable=1&tab=0">View</a>
								//</td>';
    ?>
    <table style="width:100%;background-color:#ffffff;margin-top:3px">
    	<tr><td colspan="3" align="left"  style="width:100%;">
        <?php echo '<input type="button" style="" value="'.$AppUI->_( 'Add Beneficiaries' ).'" onClick="window.location=\'/index.php?m=tasks&a=selector&task_id='.$task_id.'\'/*popSelects(\'client\','.$task_id.')*/">';
        ?>
        </td></tr>
    </table>
<script language="javascript">
   server = true;
</script>
<?php 	$gt->compile();
?>
<table style="width:100%;background-color:#ffffff;margin-top:3px">
        <tr>
			<td style="width:4%;"><?php echo $prev;?></td><td style="width:4%;"><?php echo $next;?></td><td><div style=""><?php echo $info;?></div></td>
		</tr>
    </table>
<!-- </tbody>
</table> -->

<script type="text/javascript" src="/modules/tasks/view.js"></script>
