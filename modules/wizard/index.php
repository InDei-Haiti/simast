<?php
$jmsg='';
$is_superAdmin = false;
$perms = & $AppUI->acl ();
$roles = $perms->getUserRoles($AppUI->user_id);
foreach ($roles as $role){
	if($role['value']=='super_admin'){
		$is_superAdmin = true;
	}
}
if($_GET['mode'] == 'loadcommunDept' && !empty($_GET['ccode'])){
	$code = $_GET['ccode'];
	$q = new DBQuery();
	$q->addTable("administration_com","com");
	$q->addQuery("com.administration_com_code,com.administration_com_name");
	$q->addWhere("com.administration_com_code_dep='".$code."'");
	$result = $q->loadHashList();
	if($result)
		echo json_encode($result);
	else echo 'fail';
	return;
}elseif($_GET['mode'] == 'loadsection' && !empty($_GET['ccode'])){
	$code = $_GET['ccode'];
	$q = new DBQuery();
	$q->addTable("administration_section","section");
	$q->addQuery("section.administration_section_code,section.administration_section_name");
	$q->addWhere("section.administration_section_code_com='".$code."'");
	$result = $q->loadHashList();
	if($result)
		echo json_encode($result);
	else echo 'fail';
	return;
}elseif ($_GET['mode'] == 'loadcommun' && !empty($_GET['ccode'])){
	$code = $_GET['ccode'];
	$q = new DBQuery();
	$q->addTable("administration_section","section");
	$q->addJoin('administration_com', 'com', 'com.administration_com_code = "'.$code.'"');
	$q->addQuery("com.administration_com_code,com.administration_com_name");
	$q->addWhere("section.administration_section_code='".$code."'");
	$result = $q->loadHashList();
	if($result)
		echo json_encode($result);
	else echo 'fail';
	return;
}elseif($_GET['todo'] === 'empty'){
	$fuid=(int)$_GET['fid'];
	if($fuid > 0){
		$sql='select count(*) as trows from wform_'.$fuid;
		$res=mysql_query($sql);
		if($res){
			//$fdata = mysql_fetch_object($res);
			$nowrows=mysql_fetch_row($res);
			if((int)$nowrows[0] > 0){
				$sql='select * from form_master where id="'.$fuid.'" limit 1';
				$res=mysql_query($sql);
				if($res){
					$fname=mysql_fetch_object($res);
				}
				$sql = 'truncate wform_'.$fuid;
				$dres = mysql_query($sql);
				
				if($dres){
					$many = "ManyToMany";
					if($fname->multiplicity==$many){
						mysql_query('truncate wf_'.$fuid.'_wf_'.$fname->parent_id);
					}
					if($fname->subs){
						$subs=explode(',',$fname->subs);
						foreach($subs as $st){
							mysql_query('truncate '.$st);
						}
					}
					mysql_query('DELETE FROM `beneficieries` WHERE form_id='.$fuid);
					echo "ok";
					return ;
				}
			}
			
		}
	}
	echo "fail";
	return ;
}
elseif($_GET['todo'] === 'del'){
	$fuid=(int)$_GET['fid'];
	$type = '';
	if($fuid > 0){
		$sql='select count(*) from wform_'.$fuid;
		$res=mysql_query($sql);
		if($res){
			$respar = 'select count(*) from form_master where parent_id="'.$fuid.'"';
			$nowpar = mysql_fetch_row(mysql_query($respar));
			if((int)$nowpar[0] === 0){
				$nowrows=mysql_fetch_row($res);
				if((int)$nowrows[0] === 0){
					$sql='select title,subs,typef from form_master where id="'.$fuid.'" limit 1';
					$res=mysql_query($sql);
					if($res){
						$fname=mysql_fetch_object($res);
						$type = $fname->typef;
						if($fname->subs){
							$subs=explode(',',$fname->subs);
							foreach($subs as $st){
								$sql='drop table '.$st;
								$dres=mysql_query($sql);
							}
							//return;
						}
					}

					$sql='delete from form_master where id="'.$fuid.'" limit 1';
					$res1=mysql_query($sql);
					$sql='DROP TABLE wform_'.$fuid;
					$res2=mysql_query($sql);
					if(file_exists($baseDir.'/modules/outputs/data/wform_'.$fuid.'.fields.php')){
						unlink($baseDir.'/modules/outputs/data/wform_'.$fuid.'.fields.php');
					}
					if(file_exists($baseDir.'/modules/outputs/titles/wform_'.$fuid.'.title.php')){
						unlink($baseDir.'/modules/outputs/titles/wform_'.$fuid.'.title.php');
					}
					if($res1 && $res2){
						//$jmsg="Form ".$fname->title.' deleted';
						if($type=='bigform'){
							mysql_query('delete from registre_bigtable where bgid='.$fuid);
						}
						echo 'ok';
					}
				}	
			}
		}elseif(!mysql_fetch_row(mysql_query('SHOW TABLES LIKE "wform_'.$fuid.'"'))){
			$res = mysql_query('delete from form_master where id="'.$fuid.'" limit 1');
			if($res)
				echo 'ok';
		}else{
			//var_dump(mysql_fetch_row(mysql_query('SHOW TABLES LIKE "wform_'.$fuid.'"')));
			//echo count(mysql_fetch_row(mysql_query('SHOW TABLES LIKE "wform_'.$fuid.'"')));
			echo 'fail';
		}
	}
	return false;
}elseif($_GET['mode'] == 'editf' && (int)$_GET['fid'] > 0){
	$sql='select title,task_id, fields,registry,multiplicity,forregistration,touch,project_id,task_id,alltask,multiplicity,parent_id,digestrel,typef from form_master WHERE id="'.(int)$_GET['fid'].'" limit 1';
	$res=mysql_query($sql);
	if($res && mysql_num_rows($res)  == 1){
		$trow=mysql_fetch_assoc($res);
		echo json_encode(
				array(
					'rows'=>unserialize(gzuncompress($trow['fields'])),
					'title'=>$trow['title'],'task_id'=>$trow['task_id'],
					'registry'=>$trow['registry'],
					'multiplicity'=>$trow['multiplicity'],
					'forregistration'=>$trow['forregistration'],
					'touch'=>$trow['touch'],
					'project_id'=>$trow['project_id'],
					'task_id'=>$trow['task_id'],
					'alltask'=>$trow['alltask'],
					'multiplicity'=>$trow['multiplicity'],
					'parent_id'=>$trow['parent_id'],
					'digestrel'=>explode(',', $trow['digestrel']),
					'typef'=>explode(',', $trow['typef'])
				)
			);
	}else{
		echo 'fail';
	}
	return false;
}elseif($_GET['mode'] == 'loadtask' && (int)$_GET['pid'] > 0){
	$sql='select task_id,task_name FROM tasks WHERE task_project='.(int)$_GET['pid'];
	$res=mysql_query($sql);
	if($res && mysql_num_rows($res)  > 0){
		while($trow=mysql_fetch_assoc($res)){
			$all[] = $trow;
		}
		echo json_encode($all);
	}else{
		echo ' fail';
	}
	return false;
}elseif(($_GET['mode'] == 'loadforms' && (int)$_GET['pid'] > 0)){
	$q = new DBQuery();
	$q->addTable('form_master');
	$q->addQuery("id,title");
	$q->addWhere('project_id='.$_GET['pid']);
	//$q->addWhere('forregistration=1');
	
	$sql=$q->prepare();
	$res=mysql_query($sql);
	if($res && mysql_num_rows($res)  > 0){
		while($trow=mysql_fetch_assoc($res)){
			$all[] = $trow;
		}
		echo json_encode($all);
	}else{
		echo ' fail';
	}
	return false;

}elseif(($_GET['mode'] == 'loadforms1' && (int)$_GET['tid'] > 0 && (int)$_GET['pid'] > 0)){
	$q = new DBQuery();
	$q->addTable('form_master');
	$q->addQuery("id,title");
	$q->addWhere('project_id='.$_GET['pid'].' AND forregistration=1 OR task_id='.$_GET['tid']);
	//$q->addWhere('forregistration=1');
	
	$sql=$q->prepare();
	$res=mysql_query($sql);
	if($res && mysql_num_rows($res)  > 0){
		while($trow=mysql_fetch_assoc($res)){
			$all[] = $trow;
		}
		echo json_encode($all);
	}else{
		echo ' fail';
	}
	return false;

}elseif(($_GET['mode'] == 'loadfields' && (int)$_GET['fid'] > 0)){
	$fuid = (int)$_GET['fid'];
	$wz = new Wizard('print');
	$wz->loadFormInfo($fuid);
	if(count($wz->fields)>0)
		echo json_encode($wz->fields);
	else echo ' fail';
	return false;

}elseif($_GET['mode'] == 'loadFldValDistinct' && (int)$_GET['fid'] > 0 && !empty($_GET['fld'])){
	$fuid = (int)$_GET['fid'];
	$fld = $_GET['fld'];
	$wz = new Wizard('print');
	$wz->loadFormInfo($fuid);
	$fldinfo = $wz->findFieldName($fld);
	$q = new DBQuery();
	$q->addTable('wform_'.$fuid);
	$q->addQuery('DISTINCT('.$fld.')');
	$list = $q->loadColumn();
	$finallist = array();
	if($fldinfo['sysv']=='SysCommunes'){
		foreach ($list as $value){
			$finallist[$value] = $wz->getValues($fldinfo['type'], $fldinfo['sysv'], $value);
		}
	}else{
		foreach ($list as $value){
			$finallist[$value] = $wz->getValues($fldinfo['type'], $fldinfo['sysv'], $value);
		}
	}
	echo json_encode($finallist);
	return false;
}elseif(($_GET['mode'] == 'loadHDVIVar' && (int)$_GET['pid'] > 0)){
	$household = array();
	$members = array();
	$pid = (int)$_GET['pid'];
	$q = new DBQuery();
	$q->addTable('form_master');
	$q->addQuery("id,title");
	$q->addWhere('project_id='.$_GET['pid'].'');
	$res = $q->loadHashList();
	foreach ($res as $id => $title){
		$fuid = $id;
		$household[$id]['name'] = $title;
		$wz = new Wizard('print');
		$wz->loadFormInfo($fuid);
		foreach ($wz->fields as $index => $fi) {
			if (is_array($fi['subs']) && count($fi['subs']) > 0) {
				if ($fi['otm'] === true){
					$household[$id]['members'][$index]['name'] = $fi['name'];
					foreach ($fi['subs'] as $fisub) {
						$household[$id]['members'][$index]['fields'][] = array('title' => $fisub['name'], 'fld' => $fisub['dbfld']);
					}
				}else{
					foreach ($fi['subs'] as $fisub) {
						$household[$id]['fields'][] = array('title' => $fisub['name'], 'fld' => $fisub['dbfld']);
					}
				}
			}else{
				$household[$id]['fields'][] = array('title' => $fi['name'], 'fld' => $fi['dbfld']);
			}
		}
	}
	
	
	echo json_encode($household);
	//else echo ' fail';
	return false;

}elseif($_GET['mode'] == 'printForm' && (int)$_GET['fid'] > 0){
	$fuid=(int)$_GET['fid'];
	$wz = new Wizard('print');
	$wz->loadFormInfo($fuid);
	$wz->tableWrap();

	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=form_".str_replace(' ','_',$wz->formName).".doc");

	echo "<html>";
	echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
	<!--<style type='text/css'>
	table{
		border-collapse:collapse;
	}
	table,td,th{
		font-family: arial, sans-serif;
		font-size: 10pt;
		border:1px solid #000;
	}
	</style>-->
	";
	echo "<body>
			<table cellpadding='2' cellspacing='1' width='99%' border='0'><tbody>";
	$sections = array();
	foreach($wz->fields as $fld_id => $fld){
		if(isset($fld['otm']) && count($fld['subs']) > 0){
			if($fld['otm'] === false){
				foreach ($fld['subs'] as $sid => &$fsub) {
					echo $wz->outputField($fld_id,str_replace('fld_','',$fsub['dbfld']),$fsub,$dvals[$fsub['dbfld']],false,$fld['tout']);
				}
			}else{
				//$sections[$fld_id] = $fld;
				//foreach($sections as $ids => $section){//$section
					echo "<tr><td><b>".$fld['name']."</b></td></tr>";
					echo "<tr><td align='center'>";
					echo "<table cellpadding='1' cellspacing='0' width='90%'";
					for($y =0 ; $y < 5; $y++){
						echo "<tr><td><b>".($y+1)."<b></td></tr>";
						foreach ($fld['subs'] as $sid => &$fsub) {
							/* echo "<tr><td>".$fsub['name']."</td></tr>";
							echo "<tr><td>&nbsp;</td></tr>"; */
							echo $wz->outputField($fld_id,str_replace('fld_','',$fsub['dbfld']),$fsub,$dvals[$fsub['dbfld']],true,false);
						}
					}
					echo "</table></td></tr>";
				//}
			}
		}else{
			echo $wz->outputField($fld_id,str_replace('fld_','',$fld['dbfld']),$fld, $dvals[$fld['dbfld']],false,false);
		}
	}
	
	/* foreach($sections as $ids => $section){//$section
		echo "<tr><td colspan='2'>";
		echo "<table cellpadding='1' cellspacing='0' width='90%' border='1'><tr><td><b>".$section['name']."</b></td></tr>";
		for($y =0 ; $y < 5; $y++){
			echo "<tr><td><b>".($y+1)."<b></td></tr>";
			foreach ($section['subs'] as $sid => &$fsub) {
				echo "<tr><td>".$fsub['name']."</td></tr>";
				echo "<tr><td>&nbsp;</td></tr>";
			}
		}
		echo "</table></td></tr>";
	} */
	
	echo "</tbody></body></html>";
	return ;
}elseif ($_GET['todo']==='onoff' && (int)$_GET['fid'] > 0){
	$fid=(int)$_GET['fid'];
	$sql='update form_master set valid=(select if(valid > 0,0,1) ),valid_change=now() where id="'.$fid.'"';
	$res=mysql_query($sql);
	if($res){
		echo 'ok';
	}else {
		echo 'fail';
	}
	return ;
}elseif($_GET['todo'] === 'exportf' && (int)$_GET['fid'] > 0 ){
	$fid=(int)$_GET['fid'];
	$sql='select * from form_master where id = "'.$fid.'" limit 1';
	$res=mysql_query($sql);
	if($res && mysql_num_rows($res) === 1){
		$fd=mysql_fetch_assoc($res);
		$fd['digest']=null;
		$fd['fields']=unserialize(stripslashes(gzuncompress($fd['fields'])));
		$fields = $fd['fields'];
		/*search for questions using sysvals and add those sysvals to package with form itself*/
		$togrift=array();
		foreach ($fields as &$fld) {
			if(!isset($fld['subs'])){
				if( in_array($fld['type'],array('select','radio','checkbox','select_multi')) &&
					is_numeric($fld['sysv']) ){
						$togrift[]=$fld['sysv'];
				}
			}else{
				foreach ($fld['subs'] as &$sfld) {
					if( in_array($sfld['type'],array('select','radio','checkbox','select_multi')) &&
						is_numeric($sfld['sysv']) ){
							$togrift[]=$sfld['sysv'];
					}
				}
			}
		}
		$topack=array();
		$togrift = array_unique($togrift);
		if(count($togrift) > 0){
			$sql = 'select * from svsets where id IN ('.join(",",$togrift).')';
			$res = mysql_query($sql);
			if($res){
				while($srow = mysql_fetch_assoc($res)){
					$topack[$srow['id']]=$srow;
				}
				mysql_free_result($res);
			}
		}

		$sql = 'select * from wform_'.$fid;
		$rdata = mysql_query($sql);
		$datas = array();
		if((int)$_GET['wdata'] === 1){
			if($rdata && mysql_num_rows($rdata) > 0){
				while ($drow = mysql_fetch_assoc($rdata) ){
					$datas[]=$drow;
				}
			}
			mysql_free_result($rdata);
			if($fd['subs']!= ''){
				$sparts = explode(",",$fd['subs']);
				$datas['subs']=array();
				foreach ($sparts as $sutab) {
					//wf_20_sub_3
					$sutab1 = preg_replace("/^wf_\d+_/","",$sutab);
					$sql = 'select * from '.$sutab;
					$rsdata = mysql_query($sql);
					if(!is_array($datas['subs'][$sutab1])){
						$datas['subs'][$sutab1] = array();
					}
					if($rsdata && mysql_num_rows($rsdata) > 0){
						while ($dsrow = mysql_fetch_assoc($rsdata) ){
							$datas['subs'][$sutab1][]=$dsrow;
						}
					}
				}
			}
		}
		unset($fd['digest'],$fd['subs']);
		$fd['rowData'] = $datas;
		$bfd = array("form"=>$fd,"sets"=>$topack);
		$str=base64_encode(addslashes(gzcompress(serialize($bfd),9)));
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment;Filename=form_".str_replace(' ','_',$fd['title']).".fbn");
		echo $str;
	}
	return;
}elseif ($_GET['todo'] === 'importf'){
	$res='fail';
	if(count($_FILES) === 1  && $_FILES['frfile']['size'] > 0 && $_FILES['frfile']['error'] == 0){
		$res = importForm();
	}
	echo $res;
	return ;
}elseif($_GET['mode'] == 'file1'){
	$keys = file_get_contents($baseDir . '/modules/manager/hdvi/date.json');
	$keys = json_decode($dates,true);
	for ($i=0;$i<count($keys);$i++){
		$q = new DBQuery();
		$q->addTable('wform_81');
		$q->addUpdate('key', $dates[$i]['key']);
		$q->addWhere('id='.($i+1));
		$q->exec();
	}
	return;
}


?>
<br /><br />
<link rel="stylesheet" type="text/css" href="/modules/wizard/jquery-ui-1.8.12.custom.css" />
<?php
$task_id = intval ( dPgetParam ( $_GET, 'task_id', 0 ) );
$moduleScripts[]="/modules/wizard/jquery-ui-1.8.12.custom.min.js";
$perms = $AppUI->acl();
$q = new DBQuery();
$q->addTable('sysvals');
$q->addQuery('sysval_title,sysval_title as c');
$q->addOrder('sysval_title');
$q->addWhere('sysval_tport = "0"');
$q->addWhere("sysval_key_id='1'");
$svals = $q->loadHashList();

//$svals = arrayMerge(array('-1'=>'Select Answer set','SysCenters'=>'List of Centers','SysClients'=>'List of Clients','SysStaff'=>'List of Staff'),$svals);

$q = new DBQuery();
$q->addTable('svsets');
$q->addQuery('id,title,vtype,level,touch');
$q->addOrder('vtype,title');
$q->addWhere('status="1"');
$sets = $q->loadHashListMine();

$setlist = $touchlog = array();

foreach ($sets as $sd) {
	$setlist[$sd['id']]=$sd['title'];
	$touchlog[$sd['id']]=$sd['touch'];
}


$singles = array('-1'=>'Select Answer set',
                            /* 'SysCenters'=>'List of Centers',
                            'SysClients'=>'List of Clients', */
                           'SysStaff'=>'List of Staff',
						   'SysDepartment'=>'List of Department',
                           'SysCommunes'=>'List of Communes',
                           'SysCommunalSection'=>'List of Communal Section',
	                        /* 'SysPositions' => 'List of Positions' */
                         );
$otherlists= array("-1" => '-- Select --');

$nsets=array();
foreach($sets as $iset){
	if(!isset($nsets[$iset['vtype']])){
		$nsets[$iset['vtype']]=array();
	}
	$nsets[$iset['vtype']][$iset['id']]=$iset['title'];
}

$q=new DBQuery();
$q->addTable('form_master','form');
$q->addJoin('tasks','t', 't.task_id=form.task_id');
$q->addJoin('projects','p', 'p.project_id=form.project_id');
$q->addQuery('form.*, t.task_name, p.project_name');
if(!$is_superAdmin){
	$q->addJoin('permission_form','pf', 'p.project_id=pf.form');
	$q->addWhere('pf.user_id='.$AppUI->user_id);
	$q->addWhere('pf.form=p.project_id');
	$q->addWhere('pf.module="projects"');
	$option = $perms->getAcoIdByValue('view');
	$q->addWhere('find_in_set("'.$option.'",replace(type, " ", ","))');
}
$q->addOrder('title');
if($task_id){
	$q->addWhere('t.task_id='.$task_id);
}
$forms=$q->loadHashListMine();




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
/* $q->addJoin('tasks','t', 't.task_id=form.task_id');
$q->addWhere('t.task_id=form.task_id'); */

$formsjs='';

//echo '<div id="msgbox" style="color:#288d28;font-weight:800;"></div>';
echo '<div class="card"><div id="tabs" style="visibility:hidden;" class="bigtab">
<ul class="topnav">
	<li><a href="#tabs-1"><span>'.$AppUI->_('Forms').'</span></a></li>
	<li id="editTab"><a href="#tabs-2"><span>'.$AppUI->_('Wizard').'<img src="/images/tab_load.gif" style="display:none;" border="0"></a></span></li>
	<li><a href="#tabs-3" onclick="svals.init();"><span>'.$AppUI->_('System Values').'<img src="/images/tab_load.gif" style="display:none;" border="0"></span></a></li>
</ul>
<div id="tabs-1" class="mtab">
<p>
	<span onclick="$j(\'#importbox\').toggle();" class="button ce pi ahr">'.$AppUI->_('Import form').'</span>
		<div id="importbox" class="myimporter">
			<form name="upq" action="/?m=wizard&suppressHeaders=1&todo=importf" enctype="multipart/form-data" method="POST" onsubmit="return AIM.submit(this, {onStart : startCallback, onComplete : wzrd.addIForm})">
				<input type="file" name="frfile" id="fultra">
				<input class="button ce pi ahr" type="submit" value="'.$AppUI->_('Import Form').'" class="button" disabled="disabled" >
			</form>
		</div>';

if(count($forms) > 0){
	echo '<table cellspacing="1" cellpadding="2" border="0" id="qtable" class="tbl tablesorter moretable">
	<thead>
		<tr>
			<th>'.$AppUI->_('Project').'</th>
			<th>'.$AppUI->_('Activity').'</th>
			<th>'.$AppUI->_('Form Name').'</th>
			<th>'.$AppUI->_('Multiplicity').'</th>
			<th>'.$AppUI->_('Type').'</th>
			<th>'.$AppUI->_('Status').'</th>
			<th>'.$AppUI->_('Date Of Status Change').'</th>
			<th>'.$AppUI->_('Entries').'</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>';
	foreach($forms as &$fvals){
		$q = new DBQuery();
		$q->addTable('wform_'.$fvals['id']);
		$q->addQuery("count(*)");
		$fentries=(int) $q->loadResult();
		$fvals['rows']=$fentries;
		unset($fvals['fields']);
		$fvals['valid_change']=$fvals['valid_change'];//printDate($fvals['valid_change']);
	}
	$formjs=json_encode($forms);
	echo '</tbody></table>';
}
echo '</p></div>
	<div id="tabs-2" class="mtab">
		<p>';

?>

<form action="?m=wizard&a=saveform" method="post" name="formform" onsubmit="return false;">
	<div class="row">
		<div class="col-md-12">
			<label><input type="radio" name="bigform" value="formwizard" checked="checked" onclick="wzrd.processFormWizard()"><?php echo $AppUI->_('Creation Form Wizard');?></label>
			<label><input type="radio" id="bigform" name="bigform" value="bigform" onclick="wzrd.processFormBigData()"><?php echo $AppUI->_('Creation Big Data Form');?></label>

		</div>
	</div>
	<div class="row">
		<br/><br/>
		<div class="col-md-12">
			<label id="lcheckinscription">
				<input type="checkbox" name="forregistration" value="1" id="checkinscription"><?php echo $AppUI->_('For Inscription')?>
			</label>
		</div>
	</div>
	<div class="row">
		<br/><br/>
		<div class="col-md-12">
			<label>
				<?php echo $AppUI->_('Form Name')?>&nbsp;
				<input type="text" class="form-control" size="30" name="formName" id="fname" class="form-control"/>
				<!--<input type="checkbox" value="1" id="regForm" name="regForm"/>--><!--<label id="regFormCap" for="regForm"> Registry</label> -->
			</label>
		</div>
	</div>
	<div class="row">
		<br/><br/>
		<div class="col-md-1">
			<label  id="lfprojects"><?php echo $AppUI->_('Projects')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<select id="fprojects" name="project_id" class="form-control">
					<option value=""></option>
					<?php
					foreach ($projects as $key=>$val)
						echo '<option value="'.$key.'">'.$val.'</option>';
					?>
				</select>
			</label>
		</div>
		<div class="col-md-1">
			<label id="lftasks"><?php echo $AppUI->_('Activities')?>&nbsp;&nbsp;&nbsp;&nbsp;
				<select id="ftasks" name="task_id" class="form-control">

				</select>
			</label>
		</div>
		<div class="col-md-10">
			<label  id="lfforms"><?php echo $AppUI->_('Relate Form')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<select id="fforms" name="parent_id" class="form-control">
					<option value="0"></option>
				</select>
			</label>
		</div>
	</div>

	<div class="row">
		<br/><br/><br/><br/>
		<div class="col-md-1">
			<label id="lfmultiplicity"><?php echo $AppUI->_('Multiplicity')?>&nbsp;
				<select name="multiplicity"  id="fmultiplicity" class="form-control">
					<option value="One"><?php echo $AppUI->_('One')?></option>
					<option value="Many"><?php echo $AppUI->_('Many')?></option>
					<option value="ManyToMany"><?php echo $AppUI->_('Many To Many')?></option>
				</select>
			</label>
		</div>
		<div class="col-md-11">
			<label id="lreference"><?php echo $AppUI->_('Reference field relate form')?></label>
			<table id="reference">
				<tbody>

				<tr>
					<td style="width:1%;margin: 0;padding: 0;border: 0;outline: 0;font-size: 100%;vertical-align: baseline;background: transparent;">
						<select id="available_columns" multiple="multiple" size="10" style="min-width:150px;max-width:300px;overflow-x:scroll"><option value="last_update_date"></option></select>
					</td>
					<td style="width:1%;margin: 0;padding: 5px;border: 0;outline: 0;font-size: 100%;background: transparent;" align="center">
						<input type="button" value="→" onclick="addRemoveOption('available_columns','selected_columns')"><br><br>
						<input type="button" value="←" onclick="addRemoveOption('selected_columns','available_columns')">
					</td>
					<td style="width:1%;margin: 0;padding: 0;border: 0;outline: 0;font-size: 100%;vertical-align: baseline;background: transparent;">
						<select id="selected_columns" multiple="multiple" name="relfields[]" size="10" style="min-width:150px;max-width:300px;overflow-x:scroll"><option></option></select>
					</td>
					<td style="margin: 0;padding: 5px;border: 0;outline: 0;font-size: 100%;background: transparent;">
						<input type="button" id="btns_up" value="↑" onclick="moveUpDown('selected_columns','Up')"><br><br>
						<input type="button" value="↓" onclick="moveUpDown('selected_columns','Down')">
					</td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<br/><br/>
			<button type="button" class="button ce pi ahr sectionbtn" onclick="wzrd.sectionWork('add');"><?php echo $AppUI->_('Add New Section');?></button>&nbsp;&nbsp;
			<button type="button" class="button ce pi ahr" onclick="wzrd.rowWork('add');"><?php echo $AppUI->_('Add New Question');?></button>
		</div>
	</div>
	<div class="row">
		<br/><br/><br/><br/>
		<div class="col-md-12">
			<div id="hugeStore" style="    margin-top: 20px;margin-bottom: 20px;!important;">
				<ul id="mainList" class="initlist">

				</ul>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<button type="button" class="button ce pi ahr sectionbtn" onclick="wzrd.sectionWork('add');"><?php echo $AppUI->_('Add New Section');?></button>&nbsp;&nbsp;
			<button type="button" class="button ce pi ahr" onclick="wzrd.rowWork('add');"><?php echo $AppUI->_('Add New Question');?></button>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">&nbsp;</div>
	</div>
	<div class="row">

		<div class="col-md-12">
			<input type="button" value="Clear" class="button ce pi ahr" onclick="wzrd.clean();">&nbsp;&nbsp;&nbsp;
			<input type="button" value="Save" class="button ce pi ahr" onclick="wzrd.collect();">&nbsp;&nbsp;&nbsp;
			<input type="button" value="View" class="button ce pi ahr" onclick="wzrd.preView();" style="display:inline;" id="viewbut">

			<input type="hidden" id="forSend" name="formsum" value="">
			<input type="hidden" name="form_id" value="" id="fid">
		</div>
	</div>
	<!--<button type="button" class="text sectionbtn" onclick="wzrd.sectionWork('add');"><?php /*echo $AppUI->_('Add New Section')*/?></button>&nbsp;&nbsp;
			    <button type="button" class="text" onclick="wzrd.rowWork('add');"><?php /*/*echo $AppUI->_('Add New Question')*/?></button><br/><br/><br/><br/>
				<div id="hugeStore">
					<ul id="mainList" class="initlist">
					</ul>
				</div><br/><br/>
				<button type="button" class="text sectionbtn" onclick="wzrd.sectionWork('add');"><?php /*/*echo $AppUI->_('Add New Section')*/?></button>&nbsp;&nbsp;
			    <button type="button" class="text" onclick="wzrd.rowWork('add');"><?php /*/*echo $AppUI->_('Add New Question')*/?></button>
				<br/><br/><br/>
				<input type="button" value="Clear" class="text" onclick="wzrd.clean();">&nbsp;&nbsp;&nbsp;
				<input type="button" value="Save" class="text" onclick="wzrd.collect();">&nbsp;&nbsp;&nbsp;
				<input type="button" value="View" class="text" onclick="wzrd.preView();" style="display:none;" id="viewbut">

				<input type="hidden" id="forSend" name="formsum" value="">
				<input type="hidden" name="form_id" value="" id="fid">-->
</form>
</p>
</div>
<div id="tabs-3">
	<p>
	<table cellspacing="1" cellpadding="2" border="0" id="stable" class="tbl tablesorter moretable" style="display:none;clear:both;">
		<thead>
		<tr>
			<th class="header"><?php echo $AppUI->_('Name')?></th><th class="header"><?php echo $AppUI->_('Type')?></th><th class="header"><?php echo $AppUI->_('Level')?></th><th class="header"><?php echo $AppUI->_('Status')?></th><th class="header"><?php echo $AppUI->_('Created')?>/<?php echo $AppUI->_('Changed')?></th><th><?php echo $AppUI->_('Options')?></th>
		</tr>
		</thead>
		<tbody></tbody>
	</table>
	</p>
</div>
</div>
<div id="msg_note_box"><div class="note_msg ci_sprite"></div><span></span></div>
<div id="stock" style="display:none;">
	<?php echo arraySelect($svals,'sysval_use-old','class="sysval_use-old text"',-1);
	/* <div class="fbutton newsval qticon"></div> */
	foreach ($nsets as $nkey => $net){
		$net=arrayMerge( ($nkey === 'select' ? $singles : $otherlists) ,$net);
		echo arraySelect($net,'sysval_use','class="sysval_use text '.$nkey.'"',-1);
	}

	?>
</div>
<div id="stip" style="display: none;"></div>
<div class="mctrl" style="display:none;">
	<div class="fbutton inc">+</div>
	<div class="fbutton del">-</div>
</div>
<script type="text/javascript">
	window.onload=up;
	export_all = '<?php echo $AppUI->_('Export All')?>';
	export_all = '<?php echo $AppUI->_('Export All')?>';
	add_new_value_set = '<?php echo $AppUI->_('Add new value set')?>';
	import_ = '<?php echo $AppUI->_('Import')?>';
	search_ = '<?php echo $AppUI->_('Search')?>';
	var tmsg="<?php echo $jmsg;?>",
		pforms=<?php echo ($formjs != '' ? $formjs : 'false')?>;

	function up(){
		if(tmsg != ''){
			info(tmsg,"#msgbox");
		}
		wzrd.init();
	}
	var valtail = <?php	echo (count($touchlog) > 0 ? json_encode($touchlog) : "{}"); ?>;
</script>
<?php echo "</div>"?>
