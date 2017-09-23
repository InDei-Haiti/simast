<?php
/**
 * Created by JetBrains PhpStorm.
 * User: stig
 * Date: 10.04.11
 * Time: 15:04
 */

global $AppUI,$fileSels,$baseDir,$newd;

function dataEntry(&$fld,$name,$uid){
	global $fileSels;
	$code='"'.$name.'" => ';
	$vname=$uid.'.'.$fld['name'];
	if($fld['type'] === 'date' || $fld['type'] === 'entry_date'){
		$code.='array("title"=>"'.$vname.'","xtype"=>"date")';
	}elseif($fld['sysv'] != '' &&  !strstr($fld['sysv'],'Sys')){
		$code.='array("title"=>"'.$vname.'","value"=>"sysval","query"=>"'.$fld['sysv'].'"'.(($fld['type'] === 'select_multi' || $fld['type'] === "checkbox") ? ',"mode"=>"multi"' : '' ).')';
	}elseif($fld['type'] === 'select' && strstr($fld['sysv'],'Sys')){
		$list=array(
			'SysStaff'=>array('n'=>'staffName','i'=>'staffId','s'=>'select contact_id as id, CONCAT_WS(" ",contact_first_name,contact_last_name) as name from contacts  where contact_id<>"13" and contact_active="1" order by name asc'),
			'SysClients'=>array('n'=>'clientName','i'=>'clientId','s'=> 'select client_id as id, CONCAT_WS(" ",client_first_name,client_last_name) as name from clients  order by name asc'),
			'SysCenters'=>array('n'=>'clinicName','i'=>'clinicId','s'=>'select clinic_id as id,clinic_name as name from clinics order by name asc'),
			'SysLocations'=>array('n'=>'locationName','i'=>'locationId','s'=>'select clinic_location_id as id, clinic_location as name from clinic_location order by name asc '),
			'SysPositions'=>array('n'=>'positionName','i'=>'positionId','s'=>'select id , title as name from positions order by name asc '),
		);
		$lcrit=$fld['sysv'];
		$fileSels[]=$code."'".$list[$lcrit]['s']."'";
		$code.="array('title'=>'".$vname."','value'=>'preSQL','query'=>'".$list[$lcrit]['n']."','rquery'=>'".$list[$lcrit]['i']."'
				".(($fld['smult'] === true ) ? ',"mode"=>"multi"' : '' ).")";
	}else{
		$code.='"'.$vname.'"';
	}
	return $code;
}

if($_POST['formName'] != ''){
	/*echo '<pre>';
	var_dump($_POST);
	echo '</pre>';*/
	//exit;
	$arraybg = array();
	$formType = trim($_POST['bigform']);
	$eform=(int)$_POST['form_id'];
	//$eform = 0;
	$fparent_id=(int)$_POST['parent_id'];
	$presql=array();
	//need to create/save new form
	$fproject=mysql_real_escape_string(trim($_POST['project_id']));
	//if()
	//$alltask = dPgetParam($_POST, 'alltask',0+"");
	
	$forregistration = dPgetParam($_POST, 'forregistration',0);
	
	$forregistration=mysql_real_escape_string(trim($forregistration.""));
	//echo $forregistration.'...';
	
	$task = dPgetParam($_POST, 'task_id',0+"");
	$relfields = dPgetParam($_POST, 'relfields',null);
	if($relfields)
		$relfields = implode(',', $relfields);
	$ftask=mysql_real_escape_string(trim($task));
	if($forregistration){
		$alltask = 1;
	}elseif($task=='all'){
		$alltask = 1;
	}else{
		$alltask = 0;
	}
	
	//$fcardinality = dPgetParam($_POST, 'fcardinality',null);
	
	$falltask=mysql_real_escape_string(trim($alltask));
	$fname=mysql_real_escape_string(trim($_POST['formName']));
	$fmultiplicity=trim($_POST['multiplicity']);
	$plainFields=json_decode(stripslashes($_POST['formsum']),true);
	$registry=(int)$_POST['regForm'];

	$fileData=array();

	$fileSels = array();
	
	$subTables=array();
	if(!$eform){
		
		//$sql = 'SELECT COUNT(id) FROM form_master WHERE '."project_id=".$fproject." AND forregistration=1";
		$q = new DBQuery();
		$q->addTable('form_master');
		$q->addQuery('COUNT(id)');
		$q->addWhere("project_id=".$fproject." AND forregistration='1'");
		//$restesf = db_exec($sql);
		$restesf = $q->loadResult();
		if($forregistration==='1' && $restesf>0){
			$AppUI->setMsg("The resgistration form for the project choosing is already exist",UI_MSG_ERROR);
			$AppUI->redirect("m=wizard");
		}
		
	}
	

	$typeSQL=array(
	'date' => 'date DEFAULT NULL',
	'time' => 'varchar(10) DEFAULT NULL',
	'datetime'=>'datetime DEFAULT NULL',
	'plain'=>' varchar(100)  DEFAULT NULL',
	'bigText'=>' text default NULL',
	'select'=>'varchar(100)  DEFAULT NULL',
	'select-multi'=>'varchar(100)  DEFAULT NULL',
	'select_multi'=>'varchar(100)  DEFAULT NULL',
	'radio'=>'int(11) unsigned DEFAULT NULL',
	'checkbox'=>'varchar(100)  DEFAULT NULL',
	'note'=>' text default NULL',
	'numeric' => 'varchar(100)  DEFAULT NULL',
	'unique' => 'varchar(100)  DEFAULT NULL',
	'positive' => 'varchar(100)  DEFAULT NULL',
	'range'  => 'varchar(100)  DEFAULT NULL',
	'calculateText'  => 'varchar(100)  DEFAULT NULL',
	'calculateNumeric'  => 'varchar(100)  DEFAULT NULL',
	'calculateChoice'  => 'varchar(100)  DEFAULT NULL',
	'calculateChoiceMult' => 'varchar(100)  DEFAULT NULL',
	
	);
	$setunicode = 'ALTER TABLE `wform_#@ID@#` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci';
	$bcsql='CREATE TABLE IF NOT EXISTS `wform_#@ID@#` ';
	$sqlappix=',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1';

	if($eform > 0){
		$presql[]='DROP TABLE IF EXISTS `wform_'.$eform.'`';
		$presql[]='DROP TABLE IF EXISTS `wf_'.$eform.'_wf_'.$fparent_id.'`';
		$sql='select subs from form_master where id ="'.$eform.'"';
		$res=mysql_query($sql);
		if($res && mysql_num_rows($res) > 0){
			$rsb = mysql_fetch_array($res);
			$pts=explode(',',$rsb[0]);
			foreach($pts as $ps){
				$presql[]='DROP TABLE IF EXISTS `'.$ps.'`';
			}
		}
	}
	$sfids=array(
				
				'id int(11) unsigned NOT NULL AUTO_INCREMENT',
				'ref int(11) unsigned NOT NULL',
				'entry_date DATE NOT NULL',
				'last_update_date date DEFAULT NULL',
				'user_creator varchar(100) DEFAULT NULL',
				'user_last_update varchar(100) DEFAULT NULL',
				'valid TINYINT(1) NOT NULL DEFAULT "0"'
	);
	if($formType=='bigform'){
		$sfids[] = 'dataid int(11) unsigned NOT NULL';
		$sfids[] = 'fid int(11) unsigned NOT NULL';
		$fmultiplicity = 'Many';
 	}

	$many = "ManyToMany";
	if(strcasecmp($fmultiplicity, $many)==0){
		$mtable = '
					CREATE TABLE IF NOT EXISTS `wf_#@ID@#_wf_'.$fparent_id.'` (
					  `id` int(11) NOT NULL,
					  `rel` int(11) NOT NULL
					) ENGINE=InnoDB DEFAULT CHARSET=latin1;
				';
	}
	
	
	$rcn=0;
	$digests=array();
	$subCnt=0;
	$uniques = array();
	/*echo '<pre>';
	var_dump($plainFields);
	echo '</pre>';
	exit;*/
	
	foreach($plainFields as $pid => &$pfdata){
		if($pfdata ){
			if($pfdata['type'] !== 'entry_date') {
				
				if(isset($pfdata['subs']) && is_array($pfdata['subs']) && count($pfdata['subs']) > 0){
					$subrcn=0;
					$lpos=false;
					
					foreach ($pfdata['subs'] as $sbid => &$spfdata) {
						if($pfdata['otm'] === false || $registry === 1){
							if($pfdata['type']==='dbtype'){
								$ffmmappingid = $pfdata['mapping'][0]['form'];
								$ffdmappingid = $pfdata['mapping'][0]['field'];
								foreach($pfdata['mapping'] as $mping){
									$arraybg[] = $mping['form'];
								}
								$wz11 = new Wizard('print');
								$wz11->loadFormInfo($ffmmappingid);
								$fldInfo = $wz11->findFieldName($ffdmappingid);
								$pfdata['type'] = $fldInfo['type'];
								if(isset($fldInfo['sysv']))
									$pfdata['sysv'] = $fldInfo['sysv'];
							}
							$unique = '';
							if($spfdata['type']==='unique')
								$unique = ',UNIQUE (fld_'.$rcn.')';
							$sfids[]='`fld_'.$rcn.'` '.$typeSQL[$spfdata['type']].$unique;
							
							
							if($spfdata['dgst'] === true){
								$digests[]='fld_'.$rcn;
							}
							$spfdata['dbfld']='fld_'.$rcn;
							$fileData[]=dataEntry($spfdata,'fld_'.$rcn,++$rcn);
						}elseif($pfdata['otm'] === true && $registry === 0){
							
							if($lpos === false){
								$lpos=count($fileData);
								$pfdata['dbfld']='fld_'.$rcn.'_subs';
								$pfdata['dbsub']=$subCnt;
								$pfdata['pid'] = $pid;
								++$subCnt;
							}
							
							if(!is_array($subTables[$pid])){
								$subTables[$pid]=array('sql'=>array(
								'id int(11) unsigned NOT NULL AUTO_INCREMENT',
								'wf_id int(11) unsigned NOT NULL'
								),
								'name'=>'wf_#@ID@#_sub_'.$pid,
								'title'=>$pfdata['name'],
								'fdid'=>$lpos,
								'list'=>array(),
								'dates'=>array()
								);
							}
							$unique = '';
							if($spfdata['type']==='unique')
								$unique = ',UNIQUE (fld_'.$subrcn.')';
							$subTables[$pid]['sql'][]='`fld_'.$subrcn.'` '.$typeSQL[$spfdata['type']].$unique;
							
							if($spfdata['type'] == 'date'){
								$subTables[$pid]['dates'][]='fld_'.$subrcn;
							}
							$subTables[$pid]['list'][]='fld_'.$subrcn;
							if(!is_array($fileData[$lpos])){
								$fileData[$lpos]=array('name'=>'wf_#@ID@#_sub_'.$pid,'fields'=>array(),'id'=>$pid);
							}
							$spfdata['dbfld']='fld_'.$subrcn;
							$fileData[$lpos]['fields'][]=dataEntry($spfdata,'fld_'.$subrcn,++$subrcn);

						}
					}
				}else{
					/*echo '<pre>';
					var_dump($pfdata);
					echo '</pre>';
					exit;*/
					if($pfdata['type']==='dbtype'){
						$ffmmappingid = $pfdata['mapping'][0]['form'];
						$ffdmappingid = $pfdata['mapping'][0]['field'];
						foreach($pfdata['mapping'] as $mping){
							$arraybg[] = $mping['form'];
						}

						$wz11 = new Wizard('print');
						$wz11->loadFormInfo($ffmmappingid);
						$fldInfo = $wz11->findFieldName($ffdmappingid);
						$pfdata['type'] = $fldInfo['type'];
						if(isset($fldInfo['sysv']))
							$pfdata['sysv'] = $fldInfo['sysv'];
					}

					$unique = '';
					if($pfdata['type']==='unique')
						$unique = ',UNIQUE (fld_'.$rcn.')';
					$sfids[]='`fld_'.$rcn.'` '.$typeSQL[$pfdata['type']].$unique;
					if($pfdata['dgst'] === true){
						$digests[]='fld_'.$rcn;
					}
					
					$pfdata['dbfld']='fld_'.$rcn;
					$fileData[]=dataEntry($pfdata,'fld_'.$rcn,++$rcn);
				}
			}else{
				$pfdata['dbfld']='entry_date';
			}
		}
	}
	//exit;
	$fields=mysql_real_escape_string(gzcompress(serialize($plainFields),9));
	if($eform === 0){
		$sql='insert into form_master (typef,project_id,alltask,task_id,forregistration,title,multiplicity,parent_id,fields,registry,touch,digestrel) values ("'.$formType.'","'.$fproject.'","'.$falltask.'","'.$ftask.'","'.$forregistration.'","'.$fname.'","'.$fmultiplicity.'","'.$fparent_id.'","'.$fields.'","'.$registry.'",now(),"'.$relfields.'")';
		$res=mysql_query($sql);
		$newd=mysql_insert_id();
		mysql_error();
	}else{
		//'project_id="'.$fproject.'", alltask="'.$falltask.'", task_id="'.$ftask.'", parent_id="'.$fparent_id.'", digestrel="'.$relfields.'"';
		$sql='update form_master set title="'.$fname.'", multiplicity="'.$fmultiplicity.'", forregistration="'.$forregistration.'", project_id="'.$fproject.'", alltask="'.$falltask.'", task_id="'.$ftask.'", parent_id="'.$fparent_id.'", digestrel="'.$relfields.'", fields="'.$fields.'",touch = now()  where id="'.$eform.'"';
		$res=mysql_query($sql);
		$newd=$eform;
	}
	if($newd) {
		$bcsql = str_replace('#@ID@#', $newd, $bcsql);
		$setunicode = str_replace('#@ID@#', $newd, $setunicode);
		if ($mtable) {
			$mtable = str_replace('#@ID@#', $newd, $mtable);
		}
		$plurals = array();

		if (count($sfids) > 0) {
			$bcsql .= '( ' . join(",\n", $sfids) . $sqlappix;
			foreach ($presql as $psql) {
				$psql = str_replace('#@ID@#', $newd, $psql);
				mysql_query($psql);
			}
			$wres = mysql_query($bcsql);//
			/*echo $bcsql;
			exit;*/
			//mysql_query($bcsql);

			if ($wres) {
				mysql_query($setunicode);
				$updates = array();
				if (count($digests) > 0) {
					$updates['digest'] = join(',', $digests);
				}
				if (count($subTables) > 0) {
					$tar = array();
					$parseDAtes = array();
					foreach ($subTables as $sid => &$sval) {
						$tabname = str_replace('#@ID@#', $newd, $sval['name']);
						$fdata =& $fileData[$sval['fdid']];
						if (is_array($fdata) && $fdata['id'] === $sid) {
							foreach ($sval['dates'] as $sfd) {
								$parseDates[] = '"' . $sfd . '"=>"$resex=turnDateSQL(\"#XYZ#\");"';
							}
							$fdata['name'] = str_replace('#@ID@#', $newd, $fdata['name']);
							$tmp = '"wform_sub_' . $sid . '" => array("title"=>"' . $sval['title'] . '","value"=>"plural",
							"query"=>array(
									"set"=>"select * from ' . $tabname . ' where wf_id=\'%d\'",
									"fields"=>array(
										' . join(",\n", $fdata['fields']) . '
									)
								)
							)';
							//'client'=>'client_id',
							$plurals[] = "'wform_sub_" . $sid . "'=>array(
			'table'=>'" . $tabname . "',
			'index'=>'wf_id',
			'fields'=>array(\"" . join('","', $sval['list']) . "\"),
			'eparser'=>" . (count($parseDates) > 0 ? 'array(' . join(",", $parseDates) . ')' : 'false') . "
				)";
						}

						$tar[] = $tabname;
						$fsql = 'CREATE TABLE IF NOT EXISTS ' . $tabname . ' (' . join("\n,", $sval['sql']) . $sqlappix;

						$tres = mysql_query($fsql);
						$fdata = $tmp;
					}

					$updates['subs'] = join(",", $tar);
					unset($tar);
				}
				if (count($updates) > 0) {
					$q = new DBQuery();
					$q->addWhere('id="' . $newd . '"');
					$q->addTable('form_master');
					foreach ($updates as $ukey => $uval) {
						$q->addUpdate($ukey, $uval);
					}
					$sql = $q->prepare();
					$dres = mysql_query($sql);
				}
				$fh = fopen($baseDir . '/modules/outputs/data/wform_' . $newd . '.fields.php', "w+");
				fputs($fh, '<?php
$partShow=true;
$selects = array(' . join(",\n", $fileSels) . ');' . "\n");
				fputs($fh, '$fields=array(' . join(",\n", $fileData) . ");\n?>");
				fclose($fh);
				$fh = fopen($baseDir . '/modules/outputs/titles/wform_' . $newd . '.title.php', "w+");
				//"client"=>"client_id","client_name"=> "concat(client_first_name,\' \',client_last_name) as client_name",
				//"link"=>array("href"=>"?m=wizard&a=form_use&'.($registry === 1 ? '' : 'client_id=#client_id#&').'itemid=#did#&fid='.$newd.'&todo=addedit","vals"=>array("'.($registry === 1 ? '' : 'client_id').'","did")),
				fputs($fh, '<?php
$titles["wform_' . $newd . '"]=
		array(
			"title" => "' . $fname . '",
			"db"=>"wform_' . $newd . '",
			"uid"=>"tbw' . $newd . '",
			"date"=>"entry_date",
			"did"=>"id",
			"defered"=>array(),
			"abbr"=>"WF' . $newd . '",
			"plurals"=>array(' . join(',', $plurals) . '),
			"referral"=>"",
			"next_visit"=>"",
			"form_type"=>"' . ($registry === 1 ? 'registry' : 'contus') . '"
		);
?>');
				fclose($fh);
			}
		}

		if ($mtable) {
			mysql_query($mtable);
		}


		foreach ($arraybg as $idd) {
			$sql = 'insert into registre_bigtable (bgid,fid) values (' . $newd . ',' . $idd . ')';
			mysql_query($sql);
		}
//.implode("...",$arraybg).'....'
		if (!$_POST['fakereturn']) {
			if ($wres) {
				$AppUI->setMsg("Form " . $fname . ' saved ', UI_MSG_OK);
				$AppUI->redirect("m=wizard");
			} elseif (mysql_error() != '') {
				echo mysql_error();
			}
		} else {
			if ($wres) {
				return 'ok';
			}
		}
	}else{
		$AppUI->setMsg("Form " . $fname . ' can\'t ceate ', UI_MSG_OK);
		$AppUI->redirect("m=wizard");
	}

}
?>