<?php
global $AppUI,$m, $db;
ini_set('max_execution_time', 900);
buildTableDataDemand();
/* if(isset($_POST['mode'])){
	var_dump($_POST);
	exit;
} */
//echo strpos('how are you?','are').'....';
/* if(isset($_GET['mode'])){
	$_POST['mode']= $_GET['mode'];
} */
if($_POST['mode']=='save'){
	//var_dump($_POST);
	//exit;
	require_once $AppUI->getFileInModule($m, 'patch.func');
	exportResultExcel();
	
	return ;
}elseif($_POST['mode']=='savefile'){
	require_once $AppUI->getModuleClass( 'files' ) ;
	$postdata = http_build_query(
			array(
					'list' => $_POST['list'],
					'mode' => 'save',
					'fname' => $_POST['fname'],
					'fdesc' => $_POST['fdesc'],
					'project_id' => $_POST['project_id']
			)
	);
	$opts = array( 
					'http' => array( 	
								'method'=>"POST",
								'content' => $postdata,
								'header'=>"Accept-language: en\r\n" .
								"Cookie: ".session_name()."=".session_id()."\r\n".
								"Content-type: application/x-www-form-urlencoded"."\r\n"
	 				) 
			
			);
			
	$context = stream_context_create($opts);
	session_write_close();
	$obj = new CFile();
	$obj->file_project = $_POST['project_id'];
	if (!is_dir(DP_BASE_DIR.'/files')) {
		$res = mkdir(DP_BASE_DIR.'/files', 0777);
		mkdir(DP_BASE_DIR.'/files/'.$obj->file_project, 0777);
	}else if(!is_dir(DP_BASE_DIR.'/files/'.$obj->file_project)){
		mkdir(DP_BASE_DIR.'/files/'.$obj->file_project, 0777);
	}
	if (is_dir(DP_BASE_DIR.'/files')) {
		$obj->is_shared_file = true;
		$obj->file_real_filename = uniqid(rand());
		$obj->file_nick = $_POST['fname'];
		$obj->file_folder = 0;
		$obj->file_task = 0;
		$obj->file_sector = -1;
		$obj->file_country = -1;
		$obj->file_activity_type = -1;
		$obj->file_name = 'shared-files.xls';
		$obj->file_parent = 0;
		$obj->file_type = 'application/x-msexcel';
		$obj->file_date = str_replace("'", '', $db->DBTimeStamp(time()));
		$obj->file_version = 1;
		$obj->file_icon = 'obj/';
		$obj->file_category = 1;
		$obj->file_version_id = getNextVersionID();
		$obj->beneficieries = $_POST['ftotal'];
		$obj->shared_organisation = $_POST['foname'];
		$obj->file_description = $_POST['fdesc'];
		
		$obj->_filepath = DP_BASE_DIR.'/files/'.$obj->file_project.'/'.$obj->file_real_filename;
		//echo $obj->_filepath;
		file_put_contents($obj->_filepath, fopen("http://simast.crowdlogic.org/index.php?m=outputs&suppressHeaders=1&mode=save", 'r', false, $context));
		$obj->file_size = filesize($obj->_filepath);
		if (($msg = $obj->store())) {
			$AppUI->setMsg($msg, UI_MSG_ERROR);
		} else {
			
			$AppUI->setMsg ( "Save successfuly", UI_MSG_OK );
		}
	}
	
	
	
	$link = explode('?', $_POST['flink']);
	$AppUI->redirect($link[1]);
	return ;
}elseif($_POST['faction'] == 'export'){
		$file=ExIm::makeFile((int)$_POST['qsid'],$_POST['stype']);
		if(count($file) == 2 && strlen($file[1]) > 1 ){
			printForSave($file[1],'application/octet-stream',$file[0].'.qbn');
			return ;
		}
}elseif ($_POST['mode'] == "importquery" && count($_FILES) == 1){
	$res='fail';
		if($_FILES['qfile']['size'] < 100000 && $_FILES['qfile']['error'] == 0){
			$res=ExIm::pickFile($_FILES['qfile']['tmp_name']);
			if($res !== false){
				$res= json_encode($res);
			}else{
				$res='fail';
			}
		}
		echo $res;
		return ;
}elseif($_POST ['mode'] == "query") {
	require_once $AppUI->getFileInModule($m, 'patch.func');
	proceedQueryStuff();
	return ;

}else if ($_POST ['mode'] == 'patch') {
	require_once $AppUI->getFileInModule($m, 'patch.func');
	proceedPatch();
	return ;
}elseif ($_GET['mode'] == 'rowkill'){
	$rid=(int)$_GET['row'];
	if($rid >=0 && is_numeric($_GET['row'])){
		//$fsaved=$_SESSION['table']['body']; 
		$fsaved=getFileBody('body');
		//var_dump($fsaved);
		if(count($fsaved) > 0){
			$ucase=unserialize($fsaved[$rid]);
			//var_dump($ucase);
			$sql='delete from '.$titles[$ucase['table']]['db'].' where '.$titles[$ucase['table']]['did'].' ="'.$ucase['id'].'" limit 1';
			$res=mysql_query($sql);
			if(mysql_affected_rows()){
				//unset($_SESSION['table']['body'][$rid]);
				$fsaved[$rid]=serialize('');
				saveFileBody($fsaved);
				echo "ok";
			}else{
				echo "fail";
			}
		}
	}
	return ;
}elseif ($_GET['mode'] == 'jsonMap'){
	//echo file_get_contents(diskFile::getJsonPath());
	//var_dump($_GET);
	//exit;
	require_once('result.func.php');
	$tempsession = $_SESSION ['fileNameCsh'];
	diskFile::init ();
	$query = array();
	$allfld = array();
	$querysaveaj = $_GET['querysave'];
	$latlng = false;
	if(isset($_GET['mapping_lon']) && !empty($_GET['mapping_lon'])){
		$tab = explode('_', $_GET['mapping_lon'],3);
		$query[] = $tab[0].'_'.$tab[1].'.'.$tab[2].' AS '.$_GET['mapping_lon'];
		$allfld[] = $tab[0].'_'.$tab[1].'.'.$tab[2];
		$latlng = true;
	}
	if(isset($_GET['mapping_lat']) && !empty($_GET['mapping_lat'])){
		$tab = explode('_', $_GET['mapping_lat'],3);
		$query[] = $tab[0].'_'.$tab[1].'.'.$tab[2].' AS '.$_GET['mapping_lat'];
		$allfld[] = $tab[0].'_'.$tab[1].'.'.$tab[2];
		$latlng = true;
	}else{
		$latlng = false;
	}
	if(isset($_GET['mapping_com']) && !empty($_GET['mapping_com'])){
		$tab = explode('_', $_GET['mapping_com'],3);
		$query[] = $tab[0].'_'.$tab[1].'.'.$tab[2].' AS '.$_GET['mapping_com'];
		$allfld[] = $tab[0].'_'.$tab[1].'.'.$tab[2];
	}
	if(isset($_GET['mapping_dep']) && !empty($_GET['mapping_dep'])){
		$tab = explode('_', $_GET['mapping_dep'],3);
		$query[] = $tab[0].'_'.$tab[1].'.'.$tab[2].' AS '.$_GET['mapping_dep'];
		$allfld[] = $tab[0].'_'.$tab[1].'.'.$tab[2];
	}
	if(isset($_GET['popup_field']) && !empty($_GET['popup_field'])){
		foreach ($_GET['popup_field'] as $popup_field){
			$tab = explode('_', $popup_field,3);
			$query[] = $tab[0].'_'.$tab[1].'.'.$tab[2].' AS '.$popup_field;
			$allfld[] = $tab[0].'_'.$tab[1].'.'.$tab[2];
		}
	}
	
	if($latlng){
		$query = join(',', $query);
		$sql = 'SELECT '.$query.' FROM '.$querysaveaj;
		//echo $sql;
		$res = db_exec($sql);
		$START = time();
		if($res){
			$_request = 0;
			$keys_request = array();
			$sql = 'SELECT COUNT(*) AS count FROM '.$querysaveaj;
			//echo $sql;
			//$res1 = db_exec($sql);
			//while ($row = mysql_fetch_assoc($result))
			$count_request = db_loadResult($sql);
			$allfldq = array();
			$wz = new Wizard('print');
			foreach($allfld as $field){
				$tab = explode('.', $field);
				$tabform = explode('_', $tab[0]);
				$fuid = $tabform[1];
				$wz->loadFormInfo($fuid);
				$fldinfo = $wz->findFieldName($tab[1]);
				$allfldq[str_replace('.', '_', $field)] = $fldinfo;
			}
			diskFile::startJsonArray();
			$nbrval = 0;
			while ( $rowdata = db_fetch_assoc( $res ) ) {
				$keys_request[] = $_request;
				$_request++ ;
				$nbrval += 1;
				//var_dump($rowdata);
				//break;
				$json_data = array();
				foreach($allfldq as $key => $fldinfo){
					$forStore = $rowdata[$key];
					$tempval = $forStore;
					if(isset($fldinfo['sysv']) && !empty($fldinfo['sysv'])){
						$forStore = $wz->getValues($fldinfo['type'],$fldinfo['sysv'],$forStore);
					}
						
					$forStore = strval($forStore);
					$forStore = json_encode($forStore);
					$forStore = str_replace('"', '', $forStore);
						
					//$nfei->store ($forStore);
					if(isset($fldinfo['sysv']) && !empty($fldinfo['sysv']) && ($fldinfo['sysv']=='SysDepartment' || $fldinfo['sysv']=='SysCommunes')){
						$lat;$lng;
						if($fldinfo['sysv']=='SysDepartment'){
							$ql = new DBQuery();
							$ql->addTable('administration_dep');
							$ql->addQuery('administration_dep_lat,administration_dep_lng');
							$ql->addWhere('administration_dep_code="'.$tempval.'"');
							$sqll = $ql->prepare();
							$resl = db_exec($sqll);
							while($rowl = db_fetch_row($resl)){
								
								$lat = $rowl['administration_dep_lat'];
								$lng = $rowl['administration_dep_lng'];
							}
						}
						if($fldinfo['sysv']=='SysCommunes'){
							$ql = new DBQuery();
							$ql->addTable('administration_com');
							$ql->addQuery('administration_com_lat,administration_com_lng');
							$ql->addWhere('administration_com_code="'.$tempval.'"');
							$sqll = $ql->prepare();
							$resl = db_exec($sqll);
							while($rowl = db_fetch_row($resl)){
								$lat = $rowl['administration_com_lat'];
								$lng = $rowl['administration_com_lng'];
							}
						}
						$json_data[$key]['lat'] = $lat;
						$json_data[$key]['lng'] = $lng;
						$json_data[$key]['name'] = $forStore;
					}else{
						$json_data[$key] = $forStore;
					}
				}
				
				diskFile::putJsonData(json_encode($json_data));
				if($nbrval<$count_request/*count($dataRequest)*/)
					diskFile::putSeparatorJsonData();
			}
			diskFile::endJsonArray();
			//$tddd=$nfei->getForStat ();
		}
	}else{
		
	}
	echo file_get_contents(diskFile::getJsonPath());
	$_SESSION ['fileNameCsh'] = $tempsession;
	return;
}elseif ($_GET['mode'] == 'geojsonMap'){
	//var_dump($_GET);
	//exit;
	//return;
}elseif($_POST['mode'] == 'btable'){
	$START = time();
	$calcs = magic_json_decode($_POST['calcs'],true);
	$rows = $calcs['row'];
	$cols = $calcs['col'];
	$querysaveaj = $calcs['querysave'];
	$svals = magic_json_decode($_POST['calcs2'],true);
	
	$allfld = arrayMerge($rows, $cols);
	$allfld = $rows;
	foreach ($cols as $val){
		$allfld[] = $val;
	}
	$colsvals = $svals['cols'];
	$rowsvals = $svals['rows'];
	foreach ($allfld as $i => $v){
		for ($x = 0;$x<count($colsvals);$x++){
			if($colsvals[$x]['field']==$v){
				$colsvals[$x]['id']= strval($i);
			}
		}
		for ($x = 0;$x<count($rowsvals);$x++){
			if($rowsvals[$x]['field']==$v){
				$rowsvals[$x]['id']= strval($i);
			}
		}
	}
	$svals['cols'] = $colsvals;
	$svals['rows'] = $rowsvals;
	//var_dump($allfld);
	require_once('result.func.php');
	$nfei= new evolver();
	$tempsession = $_SESSION ['fileNameCsh'];
	diskFile::init ();
	$query = array();
	foreach ($allfld as $fld){
		$query[] = $fld.' AS '.str_replace('.', '_', $fld);
	}
	$query = join(',', $query);
	$sql = 'SELECT '.$query.' FROM '.$querysaveaj;
	//echo $sql;
	$res = db_exec($sql);
	if($res){
		$_request = 0;
		$keys_request = array();
		$sql = 'SELECT COUNT(*) AS count FROM '.$querysaveaj;
		$res1 = db_exec($sql);
		$count_request = db_free_result($res1);
		$allfldq = array();
		$wz = new Wizard('print');
		foreach($allfld as $field){
			$tab = explode('.', $field);
			$tabform = explode('_', $tab[0]);
			$fuid = $tabform[1];
			$wz->loadFormInfo($fuid);
			$fldinfo = $wz->findFieldName($tab[1]);
			$keyn = str_replace('.', '_', $field);
			$allfldq[$keyn]['info'] = $fldinfo;
			if(isset($fldinfo['sysv']) && !empty($fldinfo['sysv'])){
				$sql = 'SELECT DISTINCT('.$field.') FROM '.$querysaveaj;
				$resdis = db_exec($sql);
				while ( $rowdatadis = db_fetch_assoc( $resdis ) ) {
					$allfldq[$keyn]['value'][$rowdatadis[$tab[1]]] = $wz->getValues($fldinfo['type'],$fldinfo['sysv'],$rowdatadis[$tab[1]]);
				}
				//$allfldq[$keyn]['value']
				//echo $sql;
			}
		}
		/*echo '<pre>';
		var_dump($allfldq);
		echo '</pre>';*/

		$tempsys = array();
		while ( $rowdata = db_fetch_assoc( $res ) ) {
			$keys_request[] = $_request;
			//echo $forStore.'....';
			$_request++ ;
			/*echo '<pre>';
			var_dump($rowdata);
			echo '</pre>';
			break;*/
			foreach($allfldq as $key => $fldinfo){
				$forStore = $rowdata[$key];
				if(isset($fldinfo['info']['sysv']) && !empty($fldinfo['info']['sysv'])){
					//$tval = $forStore;
					//$forStore = $wz->getValues($fldinfo['type'],$fldinfo['sysv'],$forStore);
					$forStore = $fldinfo['value'][$forStore];
				}
				$forStore = strval($forStore);
				$forStore = json_encode($forStore);
				$forStore = str_replace('"', '', $forStore);
				$nfei->store ($forStore);
			}
			$nfei->nextRow ();
		}
		$bigtar_keys = (/*isset($dataRequest) && */$count_request > 0) ?  $keys_request : array();
		$tddd=$nfei->getForStat ();
		
		diskFile::tableBodyWrite ( $tddd );
		$nfei->purge ();
	}
	
	require_once('stater.class.php');
	//$cl=preg_replace('/\\\{1,}"/','"',$_POST['calcs']);
	//$svals=json_decode(stripslashes($_POST['calcs']),true);
	
	//$_SESSION ['fileNameCsh']='8c4a5c4a7fe7d6bac9239b59d1818ed0';//759ee55ed403219e085c442c83be66e9';
	$fip=$_SESSION ['fileNameCsh'];
	//echo $fip;
	if ($fip  != '' && file_exists($baseDir.'/files/tmp/'.$fip.'.tst')) {
		$fpath=$baseDir.'/files/tmp/'.$fip.'.tst';
		$bar=unserialize(file_get_contents($fpath));
		@unlink($baseDir.'/files/tmp/'.$fip.'.tss');
		$allKeys = array_keys($bar['list']);
		//var_dump($svals['list']);
		if(is_array($svals['list'])){
			$rowRules = $svals['list'];
			if($rowRules[1] === 'hidden'){
				if(count($svals['list']) == 0){
					$svals['list']= $allKeys;
				}else{
					$svals['list'] = array_values (array_diff($allKeys, $rowRules[0]));
				}
			}elseif ($rowRules[1] === 'visible'){
				$svals['list'] = $rowRules[0];
			}
		}
		makeStat($bar,$svals);
	
		//DiskStatCache($thtml);
		//echo $thtml;
		$fps=$baseDir.'/files/tmp/'.$fip.'.tss';
		$sfh=fopen($fps,'r');
		fpassthru($sfh);
		fclose($sfh);
		//unset($thtml); 
		
	}
	$_SESSION ['fileNameCshBack'] = $_SESSION ['fileNameCsh'];
	$END = time() - $START;
	echo "<br/><br/>Process took $END seconds\n";
	$_SESSION ['fileNameCsh'] = $tempsession;
	return;
}


global $titles;
?>
<link rel="stylesheet" type="text/css" href="/modules/outputs/jquery-ui.css" />
<?php
$l='""';
$f='""';
$h='""';
$u='""';
$s='""';
$r='""';
$p='""';
$jsonmap='';
$preFils=array();
$y=0;
$countall = 0;
$staterd=0;
$html='';
$rhtml='';
$thtml='';
$rl='';
$bigtar=array();
$rqid=0;
$btlen=0;
$ftabsel=0;
$sels=array();
$mode='simple';
$clients=array();
$bigtar_keys=array();
$lcrows=0;
$uamode=false;
$thisCenter=FALSE;
$statusHistory=false;
$vis_mode= '';
$where_query = "";
$join_query = "";
$querysave = "";
//$moduleScripts[]="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js";
$moduleScripts[]="./modules/outputs/highcharts.js";
$moduleScripts[]="./modules/outputs/stats.js";
$moduleScripts[]="./modules/outputs/reporter.js";
$moduleScripts[]="./modules/outputs/jquery-ui.min.js";
$moduleScripts[]="./modules/outputs/jquery.cleditor.min.js";
$js_comm='false';
$mapRequest = false;
$tpl = new Templater($baseDir. "/modules/outputs/outputs.main.tpl");
if ($_SERVER ['CONTENT_LENGTH'] > 0 && count ( $_POST ) > 0) {
	unset($_SESSION['SAVEQUERYANA']);
}
if(isset($_GET['token']) && !empty($_GET['token'])){
	if(isset($_SESSION['SAVEQUERYANA'])){
		$_POST = $_SESSION['SAVEQUERYANA'][$_GET['token']];
	}
}
if(isset($_GET['map'])){
	$ftabsel=5;
	$mapRequest = true;
	$project_id = intval($_GET['projects']);
	$task_id = intval($_GET['tasks']);
	//$moduleScripts[]="https://maps.googleapis.com/maps/api/js?key=AIzaSyAXBklWqmHO8dubF66h9VEBlRQnuLS9P_g&sensor=false";
	//echo "https://maps.googleapis.com/maps/api/js?key=AIzaSyAXBklWqmHO8dubF66h9VEBlRQnuLS9P_g&sensor=false";
	//exit;
	//$mode='result';
	$q = new DBQuery();
	$q->addTable('tasks');
	$q->addQuery('tasks.*,p.*');
	$q->addJoin('projects', 'p', 'p.project_id=task_project');
	if($project_id)
		$q->addWhere('task_project='.$project_id);
	elseif($task_id){
		$q->addWhere('task_id = '.$task_id);
	}
	/* if(is_array($task)){
		$q->addWhere('task_id in ('.$task.')');
	}else{
		$q->addWhere('task_id = '.$task);
	} */
	//echo $q->prepare();
	$tasks = $q->loadList();
	//var_dump($tasks);
	foreach($tasks as $ir => $row){
		foreach($row as $index => $value){
			if($index==='task_locations'){
				//$locations = explode(",", $value);
				/* foreach($locations as $i => $code){
					$q = new DBQuery();
					$q->addTable('administration_com');
				} */
				if($value){
					$q = new DBQuery();
					$q->addTable('administration_com');
					$q->addWhere('administration_com_code in ('.$value.')');
					$row[$index] = $q->loadList();
					$tasks[$ir] = $row;
				}
			}			
		}
	}
	//var_dump(json_encode($tasks));
	if(count($tasks)){
		$json = json_encode($tasks);
	}
	/*if($json){
		$script = ' 
				  var json = '.$json.';
				  for (var i = 0, length = json.length; i < length; i++) {
					  var data = json[i];
					  var locations = data.task_locations;
					  for (var j = 0, length1 = locations.length; j < length1; j++) {
						  var location = locations[j];
						  latLng = new google.maps.LatLng(parseFloat(location.administration_com_lat), parseFloat(location.administration_com_lng));
						  // Creating a marker and putting it on the map
						  var marker = new google.maps.Marker({
						    position: latLng,
						    map: map,
						    title: data.task_name
						  });
						  contentString = "<b>Project Name:</b> "+data.project_name+"<br/>";
						  contentString += "<b>Activity Name:</b> "+data.task_name+"<br/>";
		                  contentString += "<b>Location:</b> "+location.administration_com_name;
						  contentString += "";
						  ///var infowindow = new google.maps.InfoWindow({
						      content: contentString
						  });
						  google.maps.event.addListener(marker, "click", function() {
						    infowindow.open(map,marker);
						  });///
						 var infowindow = new google.maps.InfoWindow();
						 google.maps.event.addListener(marker,"click", (function(marker,content,infowindow){ 
						        return function() {
						           infowindow.setContent(contentString);
						           infowindow.open(map,marker);
						        };
						    })(marker,contentString,infowindow)); 
						
						  }
					}
		';
	}*/
	
}else if (/* $_SERVER ['CONTENT_LENGTH'] > 0 &&  */count ( $_POST ) > 0) {
	//$rustart = getrusage(null);
	$lpost = array ();
	$starter=0;
	$ender=0;
	$show_start='';
	$show_end='';
	$final = array();
	
	
	require_once('result.func.php');
	$nfei= new evolver();

	$tab_src='';
	/* if(count($_POST)>0){
		var_dump($_POST);
		exit;
	} */
	resultBuilder('out');
	$ftabsel=2;
	$mode='result';
	if($_POST['stype'] === 'Stats' || $_POST['stype'] === 'Chart'){
		$ftabsel=3;
		$js_comm='1';
		$qsid=(int)mysql_real_escape_string($_POST['qsid']);
		$q = new DBQuery();
		$q->addTable('stat_queries');
		$q->addWhere('id='.$qsid);
		$sdb=$q->loadList();
		$sdb=$sdb[0];
		$turns=unserialize($sdb['turns']);
		//echo '<pre>';
		//var_dump($turns);
		//echo '</pre>';
		//echo '<br/><br/><br/>';
		//var_dump($turns);
		$svals=array(
			"rows" => unserialize(stripslashes($sdb['rows'])),
			'cols' => unserialize(stripslashes($sdb['cols'])),
			'range'=> unserialize(stripslashes($sdb['ranges'])),
			'sunqs'=> (int)$turns['sunqs'],
			'stots-rows'=> (int)$turns['stots_rows'],
			'stots-cols'=> (int)$turns['stots_cols'],
			'sperc-rows'=> (int)$turns['sperc_rows'],
			'sperc-cols'=> (int)$turns['sperc_cols'],
			'delta-count'=> (int)$turns['delta_count'],
			'records'	=>	(int)$turns['records'],
			'sblanks'=> (int)$turns['sblanks'],
			'list' => array(),
		);
		//var_dump($svals);
		$do_show_result=(int)$sdb['show_result'];
		unset($turns);
		//$bar=getFileBody('stat');
		$trows=count($svals['rows']);
		$tcols=count($svals['cols']);

		if($do_show_result === 0){
			require_once('stater.class.php');
			$row_levels=array();
			$firstr=$svals['id'];
			$bar=getFileBody('stat');
			//var_dump($bar);
			$turns=unserialize($sdb['turns']);
			if(count($bigtar_keys) > 0){
				$ulines=$bigtar_keys;//array_keys($bigtar);
			}else{
				/*for($i=0;$i < count($clients);$i++ ) 	$ulines[]=$i;*/
				$ulines=range(0,count($clients));
			}
			$svals['list']=$ulines;

			$thtml = makeStat($bar,$svals);

			DiskStatCache($thtml);
			$rhtml='';
			unset($bigtar,$clients,$bigtar_keys,$l,$r,$u,$sels,$f);
			$y=0;
		}
		if($_POST['stype'] === 'Chart'){
			$js_comm=2;
			$gdata=unserialize($sdb['chart_data']);
			$dset=json_decode($gdata['dset'],true);
			if(isset($dset['col_use'])){
				if($dset['col_use'] != 'xcall'){
					foreach ($dset['colb'] as $cv) {
						if($cv[0] == $dset['col_use']){
							$use_col=$cv[1];
						}
					}
				}else{
					$use_col='xcall';
				}
			}else{
				$use_col=false;
			}
			if(isset($dset['row_use'])){
				foreach ($dset['rowb'] as $rv) {
					if($rv[0] == $dset['row_use']){
						$use_row=$rv[1];
					}
				}
			}else{
				$use_row=false;
			}
			$chartDerectives=array(
				'mode'=>$gdata['cmode'],
				'pie_row'=>(isset($gdata['urow']['uvrow']) ? $gdata['urow']['uvrow'] : false),
				'col_use'=>$use_col,
				'row_use'=>$use_row
			);
		}
	}
}
$htmlpre = '<form method="POST" action="?m=outputs" id="sendAll" name="xform" onsubmit="return false;">
	<input type="hidden" name="stype">
	<input type="hidden" name="pmode">
	<input type="hidden" name="faction">
	<input type="hidden" name="qsid">';

$mi = 0;
$block_count = 1;
$tchex=0;
$auto_open=array();
ksort ( $fielder );
//$html=buildForms($fielder);
unset($fielder);
$lasttext='';
$alltext='';
$firsttext='';
$curcentext=($thisCenter !== FALSE ? 'checked' : '');
$stahistext=($statusHistory !== FALSE ? 'checked' : '');
if ($vis_mode == 'last') {
	$lasttext = 'checked';
} elseif($vis_mode == 'first') {
	$firsttext = 'checked';
}else{
	$alltext='checked';
}

$df = $AppUI->getPref('SHDATEFORMAT');
if ($starter != '' && !is_null($starter)) {
	$tdd = new CDate($starter);
	$show_start = $tdd->format($df);
	unset($tdd);
} else {
	$starter = date ( "Ymd" );
}
if ($ender != '' && !is_null($ender)) {
	$tdd= new CDate($ender);
	$show_end = $tdd->format($df);
	unset($tdd);
} else {
	$ender = date ( "Ymd" );
}

if($lvder != '' && !is_null($lvder)){
	$tdd= new CDate($lvder);
	$show_lvd = $tdd->format($df);
	unset($tdd);
}else{
	$show_lvd='';
}



$queriez=array();
$q= new DBQuery();
$q->addTable("queries");
$q->addWhere('visible="1"');
$q->addOrder("created desc");
$queriez['Table'] = $q->loadList();
$q->clearQuery();
$q->addTable('stat_queries','sqs');
$q->addOrder("created desc");
$q->addOrder('qmode asc');
$queriez['Stats']=$q->loadList();
$q->clearQuery();
$q->addQuery('id,title as qname, replace(start_date,"-","") as sdate, replace(end_date,"-","") as edate');
$q->addTable('reports');
$queriez['Report']=$q->loadList();
unset($q);
flush_buffers();
//<label><input type=checkbox name="extra[]" '.addChecks($lpost,'extra',"location").'>Location</label>

$html = $htmlpre.buildSelectOptions().$html.'
<br><br>
<div style="width: 1000px;">
	<input type="button" value="Go" onclick="getData()" class="button">&nbsp;&nbsp;&nbsp;&nbsp;
	<!--<input type="button" value="Clear Forms" onclick="clearData()" id="fcleaner" class="button" '. (($tchex > 0) ? '': 'disabled="disabled"').'>-->
</div>
</form>';
cleanALoc($lpost);
//'.($ftabsel == 2 ? 'class="ui-tabs-selected"' : '').'
//echo '<a href="?m=outputs&a=gchart">Chart</a>';
echo '
<DIV id="tabs" class="bigtab" >
<UL class="topnav">
<LI><A href="#tabs-1"><span>'.$AppUI->_('Queries').'</span></A></LI>
<LI><A href="#tabs-2"><span>'.$AppUI->_('Forms').'</span></A></LI>
<LI><A href="#tabs-3"><span>'.$AppUI->_('Tables').'</span></A></LI>
<LI class="tabs-disabled"><A href="#tabs-4"><span>'.$AppUI->_('Stats').'</span></A></LI>
<LI><A href="#tabs-5"><span>'.$AppUI->_('Report').'</span></A></LI>
<LI class="tabs-disabled"><A href="#tabs-6" id="mapstab"><span>'.$AppUI->_('Maps').'</span></A></LI>
</ul>
<div id="tabs-1" class="mtab">
		<!-- <select onchange="rebootQTable(this);" data-items="">
			<option value="queries" selected>Queries</option>
			<option value="items">Report Items</option>
		</select> 
		<br> -->
	<p>
		<span onclick="$j(\'#importbox\').toggle();" class="fhref flink">'.$AppUI->_('Import query').'</span><span class="offwall msgs" id="msg_place"></span>
		<div id="importbox" class="myimporter">
			<form name="upq" action="/?m=outputs&suppressHeaders=1" enctype="multipart/form-data" method="POST" onsubmit="return AIM.submit(this, {\'onStart\' : startCallback, \'onComplete\' : qurer.extractRow})">
				<input type="file" name="qfile" id="fultra" data-ext="qbn|rbn|ibn">
				<input type="submit" value="Import query/item" class="button" disabled="disabled" >
				<input type="hidden" name="mode" value="importquery">
			</form>
		</div>
		<table cellspacing="1" cellpadding="2" border="0" class="tbl tablesorter moretable" id="ittable" style="display: none;">
			<thead>
				<tr>
					<th class="phead">&nbsp;</th><th class="phead">'.$AppUI->_('Name').'</th><th class="phead">'.$AppUI->_('Type').'</th><th class="phead">&nbsp;</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>

		<table cellspacing="1" cellpadding="2" border="0" class="tbl tablesorter moretable" id="qtable">
			<thead>
			<tr>
				<th class="phead">&nbsp;</th>
				<th class="phead">'.$AppUI->_('Name').'</th>
				<th class="phead">'.$AppUI->_('Type').'</th>
				<th class="phead">'.$AppUI->_('Item Type').'</th>
				<th class="phead">'.$AppUI->_('Description').'</th>
				<th class="phead">'.$AppUI->_('Start Date').'</th>
				<th class="phead">'.$AppUI->_('End Date').'</th>
				<th class="phead">&nbsp;</th>
				<th class="phead">&nbsp;</th>
			</tr>
		</thead>';
$trid=0;
$sr='';
$qsr='';
foreach ($queriez as $pname => $part) {
	foreach ($part as $row) {
		$edClass='qeditor';
		if($pname == 'Stats'){
			$row['show_result'] == 1 ? $sr ='true' : $sr='false';
			if($row['qmode'] === 'graph'){
				//$edClass='qreditor';
				$pnameOut='Chart';
			}else{
				$pnameOut='Stats';
			}
		}elseif($pname === 'Report'){
			$edClass='qreditor';
			$pnameOut='Report';
		}else{
			$pnameOut='Table';
		}
		$qsr.='<tr id="qsr_'.$trid.'" data-showr="'.$sr.'">
		<td title="Edit" align="center"><div class="'.$edClass.'" data-id="'.$row['id'].'"></td>';
		$st=trimView($row['qname']);
		$qsr.='<td data-text="'.$st['orig'].'" '.($st['show'] === true ? ' class="moreview"' : '').'><span class="fhref flink" onclick="qurer.run(\''.$trid.'\',\'run\');">'.$st['str'].'</span></td>
		<td align="center">'.$pnameOut.'</td>
		<td>&nbsp;</td>';
		$st=trimView($row['qdesc']);
		$qsr.='<td data-text="'.$st['orig'].'"'.($st['show'] === true ? ' class="moreview"' : '').' >'.$st['str'].'</td>';
		$sdateClean=viewDate($row['sdate']);
		$edateClean=viewDate($row['edate']);
		//if($pname == "Table"){
		//onclick = "popTCalendar(\'start_'.$trid.'\')"
		//onclick = "popTCalendar(\'end_'.$trid.'\')"
			$qsr.='
			<td >
				<div class="tdw">
				<div class="stdw" fsort="'.$sdateClean[1] .'">'.$sdateClean[0].'</div>
				<img width="16" height="16" border="0" alt="'.$AppUI->_('Calendar').'" src="/images/calendar.png" class="calpic" onclick = "popTCalendar(\'start_' . $trid . '\')">
				</div>
				<input type="hidden" id="start_'.$trid.'" value="'.$row['sdate'].'" >
			</td>
			<td >
				<div class="tdw">
				<div class="stdw" fsort="'.$edateClean[1] .'">'.$edateClean[0].'</div>
				<img width="16" height="16" border="0" alt="Calendar" src="/images/calendar.png" class="calpic" onclick = "popTCalendar(\'end_' . $trid . '\')">
				</div>
				<input type="hidden" id="end_'.$trid.'" value="'.$row['edate'].'" >
			</td>';
		/*}else{
			$qsr.='<td >&nbsp;</td><td >&nbsp;</td>';
		}*/
		$qsr.='
		<!-- <td ><span title="Run" class="fhref"><img src="/images/run1.png" weight=22 height=22 border=0 alt="Run"></span></td> -->
		<td align="center"><span title="'.$AppUI->_('Delete').'" class="fhref" onclick="qurer.delq(\''.$trid.'\');" ><img src="/images/delete1.png" weight=16 height=16 border=0 alt="Delete"></span></td>
		<td align="center"><div title="'.$AppUI->_('Export').'" class="exportq" onclick="qurer.run(\''.$trid.'\',\'export\');" ></div></td>
		</tr>';
		$trid++;
		echo $qsr;
		unset($qsr);
	}
}
unset($queriez);
flush_buffers();
//if(count($bigtar) == 0 &&  count ( $clients ) == 0){
$lpo=false;
if($y ==0 ){
	$rhtml='<span class="note">'.$AppUI->_('No data to display').'</span>';
	$lpo=true;
}
flush_buffers();
echo '</table></p></div>';


$htmlwhere = '';
$htmljoin = '';
$htmlquerysave = '';
if($where_query){
	$htmlwhere = '<input type="hidden" id="where_query" name="where_query" value="'.$where_query.'">';
}
if($join_query){
	$htmljoin = '<input type="hidden" id="join_query" name="join_query" value="'.$join_query.'">';
}
if($querysave){
	$htmlquerysave = '<input type="hidden" id="querysave" name="querysave" value="'.$querysave.'">';
}
echo '<div id="tabs-2" class="mtab" >';
echo '<form method="POST" action="?m=outputs" id="sendAll" name="xform" onsubmit="return false;">';
echo '<input type="hidden" name="stype">
	<input type="hidden" name="pmode">
	<input type="hidden" name="faction">
	<input type="hidden" name="qsid">'.$htmlwhere.$htmljoin.$htmlquerysave;
//echo '<link rel="stylesheet" type="text/css" href="/modules/outputs/gchart.css"/>';
//echo '<script type="text/javascript" src="/modules/outputs/gchart.js"></script>';
echo '<div id="awrapper" style="width:95%">';
echo 	'<div id="bset" style="color:red;">';

$q = new DBQuery();
$q->addTable("projects");
$q->addQuery("project_id,project_name");
$rows = $q->loadHashList();
$perms =& $AppUI->acl();
$projects = '';
foreach($rows as $k => $v){
	if(!$perms->checkForm($AppUI->user_id,'projects',$k,'view')){
		continue;
	}
	$projects .= '<label><input type="radio" name="projects[]" class="projects" value="'.$k.'">&nbsp;&nbsp;'.$v.'</label>';
}
$projects .= '<a href="#" class="" id="map_link_project" onclick="mapProjects()"></a>';
echo       '<h3 style="padding: 5px;">&emsp;&emsp;'.$AppUI->_('Projects').'</h3>';
echo '<div id="bparts" style="height: 90px">
<p>';
echo $projects;
echo '</p>
		</div>';

//echo '<h3 style="padding: 5px;">&emsp;&emsp;Activity</h3>';
//echo '<div class="list" id="tasks" style="height: 90px"></div>';

echo '<h3 style="padding: 5px;">&emsp;&emsp;'.$AppUI->_('Forms').'<img src="/modules/outputs/images/ajax-loader.gif" id="imgloader" style="display:none;width:20px;height:20px"/></h3>';
echo '<div  id="forms" style="height:300px;">
						
		</div>';

echo 	'</div>';
echo '</div>';
echo '<br><br>
<div style="width: 1000px;">
	<input type="button" value="'.$AppUI->_('Go').'" onclick="getData()" class="button">&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="button" value="'.$AppUI->_('Clear Forms').'" onclick="clearData()" id="fcleaner" class="button" '. (($tchex > 0) ? '': 'disabled="disabled"').'>
</div>';
echo '</form></div>';




/* unset($html);
flush_buffers();
ob_end_clean(); */
//echo '<div id="tabs-3" class="mtab"><p>',$rhtml,'</p></div>';
echo '<div id="tabs-3" class="mtab"><p>';
//,$rhtml,
if($lpo === true){
	echo $rhtml ;
}else{
	diskFile::printOut();
	//$ru = getrusage();
	//echo "This process used " . rutime($ru, $rustart, "utime") ." ms for its computations\n";
}
echo '</p></div>';
unset($rhtml);
//flush_buffers();
//<!-- <div class="fbutton sec_type sec_table" title="Custom section"></div> -->
/*Report to be here*/
echo '<div  id="tabs-6" class="mtab" style="100%">';

//if($mapRequest)
	/*echo '<style>
      #map-canvas {
        height: 1000px;
		width: 80%;
        margin: 0px;
        padding: 0px;
		float: left;
		  text-align: left;
		  margin: 2px 10px;
		  display: inline
		height: 1000px;
	    width: 80%;
		float: right;
	    border: solid 1px #000000;
	    background-color: #66CC00;
      }
	#leftmap{
  height: 200px;
  width: 20%;
  border: solid 1px #000000;
  background-color: #0066CC;
  float: left;
}

    </style>';*/

echo '<style>
		
#div1{
  height: 30px;
  width: 20%;
  border: solid 1px #000000;
  background-color: transparent;
  float: left;
}
#div2{
  height: 30px;
  width: 80%;
  border: solid 1px #000000;
  background-color: #66CC00;
}
		
.left {
	  float: left;
	  width: 125px;
	  text-align: right;
	  height: 800px;
	  width:20%; 
	  padding: 2px;
	  margin: 3px;
	  border: 2px solid #DCDBD8;
	  display: inline;
}
.right {
	  float: left;
	  text-align: left;
	  margin: 2px 10px;
	  margin: 3px;
	  width:74%;
	  height:1000px;
	  border: 2px solid #DCDBD8;
	  display: inline;    
}
.mapfieldbox{
	  margin: 1px;
	  min-height:100px;
	  height: auto;
	  border: 2px solid #DCDBD8;
      		
}
.mapmarkergroup{
      margin: 1px;
	  height:100px;
	  border: 2px solid #DCDBD8;		
}
.maplonlatbox{
      margin: 1px;
	  height:100px;
	  border: 2px solid #DCDBD8;
			
}
.mapadminlocbox{
      margin: 1px;
	  height:100px;
	  border: 2px solid #DCDBD8;		
}
.mapdatamappingbox{
      margin: 1px;
	  height:100px;
	  border: 2px solid #DCDBD8;		
}
.mappopupinfogbox{
      margin: 1px;
	  border: 2px solid #DCDBD8;		
}
		
</style>';
	

echo "<style>
<!--
		
-->
		#loader{
			 position: fixed;
			  left: 0px;
			  top: 0px;
			  width: 100%;
			  height: 100%;
			  z-index: 9999;
			  opacity: 0.4;
			  display:none;
			  background: url('/modules/outputs/images/loader.gif') 50% 50% no-repeat rgb(0,0,0);
			}
	</style>";
	/* echo ' <p>

    <div id="shome">
        <div class="bbox">
            <div id="fsrc" class="dgetter wider">
                <span class="areaName" style="float:left;">Fields</span>
                <ul id="box-home" style="list-style: none; float: left;"></ul>
            </div>
        </div>
        <div class="bbox">
            <div id="fsrcr" class="dgetter"><span class="areaName">Rows</span>
                <ul id="rbox" class="accepter rcgetter"></ul>
            </div>
            <div class="box22">
                <div id="fsrcc" class="dgetter wsdiv"><span class="areaName">Columns</span>
                    <ul id="cbox" class="accepter rcgetter wsels"></ul>
                </div>
                <div class="bigger">
                    <span class="areaName">Data</span>

                    <div id="gbox" class="gsmall"></div>
                </div>
            </div>
        </div>
        </form>
    </div>
    </p> '; */
	
	//echo '<div style="width: 20%;color:red;">';
	echo '<div style="100%">';
	echo '<div class="left">
				<form name="mapform" id="mapform">
  		   <!--<div class="mapfieldbox" style="overflow:auto"><center>Fields</center>
  				<ul id="box-home" style="list-style: none;">
  		        </ul>
  		   </div>-->
  		   <div>
  		        <table>
  					<tr><td><label><input type="checkbox" checked="checked" id="markergroup">'.$AppUI->_('Marker group by administrative location').'</label></td></tr>
	  		    </table>
		   </div>
  		   <div class="maplonlatbox">
  		        <table>
	  		    <tr><td><h3 style="margin-top: 0px;float:left">'.$AppUI->_('Geographical coordinates').'</h3></td></tr>
  				<tr><td><table>
  		           <tr><td>Longitude field</td><td><select id="maplon_select" name="mapping_lon" style="width:150px;">
		         	<option></option>
		        </select></td></tr>
  		        </table></td></tr>
		       <tr><td><table>
  		           <tr><td>'.$AppUI->_('Latitude field').'</td><td><select id="maplat_select" name="mapping_lat" style="width:150px;">
		         	<option></option>
		        </select></td></tr>
  		        </table></td></tr>
		        </table>
		   </div>
  		   <div class="mapadminlocbox">
		       <table>
	  		   <tr><td><h3 style="margin-top: 0px;float:left">'.$AppUI->_('Administrative location').'</h3></td></tr>
  		       <tr><td><table>
  		           <tr><td align="left">'.$AppUI->_('Department field').'</td><td align="right"><select id="mapdep_select" name="mapping_dep" style="width:150px;">
		         	<option></option>
		        </select></td></tr>
  		       </table></td></tr>
  		       <tr><td><table>
  		           <tr><td align="left">'.$AppUI->_('Commune field').'</td><td align="right"><select id="mapcom_select" name="mapping_com" style="width:150px;">
		         	<option></option>
		        </select></td></tr>
  		       </table></td></tr>
		       </table>
  		   </div>
		   <!--<div class="mapdatamappingbox">
		       <table>
	  		   <tr><td><h3 style="margin-top: 0px;float:left">'.$AppUI->_('Data mapping').'</h3></td></tr>
  		       <tr><td><table style="margin-top: 0px;float:left" id="mappingfield_table">
  		       </table></td></tr>
		       </table>
  		   </div>-->
		   <div class="mappopupinfogbox">
		       <table>
	  		   <tr><td><h3 style="margin-top: 0px;float:left">'.$AppUI->_('Popup info').'</h3></td></tr>
  		       <tr><td><table style="margin-top: 0px;float:left" id="popupfield_table">
  		       </table></td></tr>
		       </table>
  		   </div>
		   <!--<div class="mappopupinfogbox11">
		       <table>
	  		   <tr><td><h3 style="margin-top: 0px;float:left">'.$AppUI->_('Typography marker').'</h3></td></tr>
  		       <tr><td><table style="margin-top: 0px;float:left" id="mtype_table">
  		       </table></td></tr>
		       </table>
  		   </div>-->
			<div class="mappopupinfogbox11">
		       <table>
	  		   <tr><td><h3 style="margin-top: 0px;float:left">'.$AppUI->_('Filter').'</h3></td></tr>
  		       <tr><td><table style="margin-top: 0px;float:left" id="mfilter_table">
  		       </table></td></tr>
		       </table>
  		   </div>						
	  	   <div class="mappopupinfogbox">
		       <table>
	  		   <tr><td><a href="#" class="button" id="btngomap">'.$AppUI->_('Go').'</a></td><td><button class="button">Clear</button></td></tr>
		       </table>
  		   </div></form>
  		</div>';
	//echo '<div class="right" id="map"></div>';
	echo '</div>';
	echo '<div id="loader"></div>';
	
//echo 'Hello World';
echo '</div>';



$tpl = new Templater($baseDir.'/modules/outputs/report.tpl');
$tpl->cal_start=drawDateCalendar('rep_start','',false,'id="rep_start"',false,10);
$tpl->cal_end=drawDateCalendar('rep_end','',false,'id="rep_end"',false,10);
$tpl->thtml = $thtml;
$tpl->dept_selector = arraySelect(dPgetSysVal("ClinicalDepartments"),'rep_dept',"id='rep_dept' class='text'",1);
$tpl->output(true);

echo '<div class="right" id="map" style="display:none"></div>';




if($thtml !=''){
	$grinit=true;
}else{
	$grinit=false;
}
unset($html,$thtml,$rhtml);
flush_buffers();

$tpl->reboot($baseDir.'/modules/outputs/outputs.bottom.tpl');
$tpl->chex = ($mi - 1);
$tpl->rrr = $y;
if($countall) $tpl->countall = $countall;
else $tpl->countall = 0;//$countall;
$tpl->today =  date("Ymd");
$tpl->fakes = json_encode($f);
/* var_dump($l);
echo '<br/>';
for($i=0;$i<count($l);$i++){
	echo $i.': ';
	echo json_encode($l[$i]);
	echo '<br/><br/><br/>';
}  */
$tpl->btr = json_encode($l,JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
$tpl->heads = json_encode($h);
$tpl->lets = json_encode($u);
$tpl->selects = json_encode($sels);
$tpl->tgt = $ftabsel;
$tpl->aopen = json_encode($auto_open);
$tpl->st_do =  $staterd;
$tpl->rqid = $rqid;
$tpl->refs =  json_encode($r);
$tpl->plus = json_encode($p);
$tpl->rels = json_encode($rl);
$tpl->pf = json_encode($preFils);
$tpl->mstart = $js_comm;
$tpl->extraCode = 'jstrert="";';
//if($jsonmap){
	//echo $jsonmap;
	//$tpl->append('extraCode','jsonmap="'.$jsonmap.'";');
//}
if(strlen($thtml) > 0){
	$tpl->append('extraCode','$j("#tthome").show();');
}
if($_POST['stype'] ===  'Stats' || $_POST['stype'] ===  'Chart'){
	echo '<pre>';
	//var_dump($svals);
	echo '</pre>';
	unset($svals['list']);
	$svals['rbox']=$svals['rows'];
	unset($svals['rows']);
	$svals['cbox']=$svals['cols'];
	unset($svals['cols']);
	/* echo '<pre>';
	var_dump($svals);
	echo '</pre>'; */
	//echo json_encode($svals);
	$tpl->append('extraCode','fstatp='.json_encode($svals).';');
	
	/* echo 'extraCode: '.$tpl->extraCode;
	exit; */
}
if(is_array($chartDerectives) && count($chartDerectives) > 0){
	$tpl->append('extraCode','chartMode='.json_encode($chartDerectives).';');
}

$tpl->output(true);
if (count ( $_POST ) > 0) {

echo '  <link rel="stylesheet" type="text/css" href="http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.css" />
		<link rel="stylesheet" type="text/css" href="http://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/0.4.0/MarkerCluster.css" />
    <link rel="stylesheet" type="text/css" href="http://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/0.4.0/MarkerCluster.Default.css" />

      
      <script type="text/javascript" src="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js?2"></script>
      <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/0.4.0/leaflet.markercluster.js"></script>
  
	  		
      <script type="text/javascript" src="/modules/outputs/maps/leaf-demo.js"></script>';
}
?>

	<script type="text/javascript">
	
	function mapProjects(){
		checkedValue = $('.projects:checked');
		projects = "";
		for(l=0;l<checkedValue.length;l++){
			projects += '&';
			projects += 'projects='+checkedValue[l].value;
		}
		//console.log(projects);
		if(projects)
			window.location.href = '?m=outputs&map=1'+projects;
	}
	</script>
<?php if($mapRequest){ ?>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true">
	</script>
	<script type="text/javascript">
		//function initialize() {
			//var mapOptions = {
				//zoom: 9,
				//center: new google.maps.LatLng(18.783058748403047, -73.26694868164064)
			//};
			//var map;
			//map = new google.maps.Map(document.getElementById('map-canvas'),
					//mapOptions);
			<?php //if($script){
				  	//echo $script;
				//  }
			?>
			 //google.maps.event.addListener(map, 'zoom_changed', function() {
			    //zoomLevel = map.getZoom();
			  //  console.log(map.getCenter());
			//}); 
		//}
		//initialize();
		</script>
	
	
	
	
	<!-- <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&callback=initialize">
	</script> -->
	<?php }?>
	