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
$perms =& $AppUI->acl();
$is_superAdmin = false;
$roles = $perms->getUserRoles($AppUI->user_id);
foreach ($roles as $role){
    if($role['value']=='super_admin'){
        $is_superAdmin = true;
    }
}
if($_POST['mode']=='save'){
	//var_dump($_POST);
	//exit;
	require_once $AppUI->getFileInModule($m, 'patch.func');
	exportResultExcel();
	
	return ;
}elseif($_POST['mode']=='savefile'){
    $is_save=$_POST['is_save'];
    $qname=$_POST['qname'];
    $qdesc=$_POST['qdesc'];
    $activity_id=$_POST['activity_id'];
    $sector_id=$_POST['sector_id'];
    $st_area_id=12;//$_POST['st_area_id'];
    $beneficieries=$_POST['beneficieries'];
    $amount=$_POST['amount'];
    $prepare = $_POST['querysave'];
    $headers = $_SESSION['SAVEQUERYDATA'][$_POST['token']];

    //echo $_POST['querysave'];
    $tabsql = explode('FROM', $prepare);
    $querypart = $tabsql[1];
    //$res = mysql_query();
    $sql ="Select ".$headers['id']['table'].".id FROM ".$querypart;
    $sql = stripslashes($sql);
    $sql = stripslashes($sql);
    $sql = stripslashes($sql);
    $sql = stripslashes($sql);
    $sql = stripslashes($sql);
    $sql = stripslashes($sql);
    $headers = mysql_real_escape_string(gzencode(var_export($headers,true), 9, FORCE_GZIP));
    $prepare = mysql_real_escape_string(gzencode($prepare, 9, FORCE_GZIP));
    //echo $sql;
    //echo mysql_num_rows($res);
   // db_fetch_assoc();
    $res=db_loadColumn($sql);
    $list = mysql_real_escape_string(gzencode(implode(',',$res), 9, FORCE_GZIP));
    $stmpl = 'insert into `activity_queries`(qname, qdesc, created, activity_id, sector_id, st_area_id, beneficieries, amount, `prepare`, list, is_save, headers) 
                    values ("'.$qname.'","'.$qdesc.'",now(),'.$activity_id.','.$sector_id.','.$st_area_id.','.$beneficieries.','.$amount.',"'.$prepare.'","'.$list.'",'.$is_save.',"'.$headers.'")';

    //$res = mysql_query($sql);
    $zid = 0;
    $res = db_exec($stmpl);
    if($res)
        $zid = db_insert_id();
    if (!($zid > 0)) {
        $zid = 'fail';
    }
    echo $zid;
    return;
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
}elseif ($_POST['mode'] == "setquery"){
    $stmpl = 'insert into `sets`(setname) 
                    values ("'.$_POST['set'].'")';
    $zid = 0;
    $res = db_exec($stmpl);
    if($res)
        $zid = db_insert_id();
    //header("Location: /?m=outputs");
    if (!($zid > 0)) {
        $zid = 'fail';
    }else{
        echo json_encode(array('id'=>$zid,'set'=>$_POST['set']));
    }

    return;
}elseif ($_GET['mode'] == "getSet"){
    $sql='select id,setname FROM sets';
    $res=mysql_query($sql);
    if($res && mysql_num_rows($res)  > 0){
        while($trow=mysql_fetch_assoc($res)){
            $all[] = $trow;
        }
        echo json_encode($all);
    }else{
        echo ' fail';
    }

    return;
}elseif ($_GET['mode'] == "delSet"){
    $sql='DELETE FROM `sets` WHERE id='.$_GET['id'];
    $res=mysql_query($sql);
    if($res){
        echo 'ok';
    }else{
        echo ' fail';
    }

    return;
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
    ob_start();
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
	//var_dump($_GET);
	//return;
    $lat_field = null;
    $long_field = null;
    if(isset($_GET['mapping_lon']) && !empty($_GET['mapping_lon'])){
        $long_field = $_GET['mapping_lon'];
    }
    if(isset($_GET['mapping_lat']) && !empty($_GET['mapping_lat'])){
        $lat_field = $_GET['mapping_lat'];
    }
	if(isset($_GET['mapping_lon']) && !empty($_GET['mapping_lon'])){
		$tab = explode('_', $_GET['mapping_lon'],3);
		$query[$_GET['mapping_lon']] = $tab[0].'_'.$tab[1].'.'.$tab[2].' AS '.$_GET['mapping_lon'];
		$allfld[] = $tab[0].'_'.$tab[1].'.'.$tab[2];
		$latlng = true;
	}if(isset($_GET['mapping_lat']) && !empty($_GET['mapping_lat'])){
		$tab = explode('_', $_GET['mapping_lat'],3);
		$query[$_GET['mapping_lat']] = $tab[0].'_'.$tab[1].'.'.$tab[2].' AS '.$_GET['mapping_lat'];
		$allfld[] = $tab[0].'_'.$tab[1].'.'.$tab[2];
		$latlng = true;
	}else{
		$latlng = false;
	}
	/*if(isset($_GET['mapping_com']) && !empty($_GET['mapping_com'])){
		$tab = explode('_', $_GET['mapping_com'],3);
		$query[] = $tab[0].'_'.$tab[1].'.'.$tab[2].' AS '.$_GET['mapping_com'];
		$allfld[] = $tab[0].'_'.$tab[1].'.'.$tab[2];
	}
	if(isset($_GET['mapping_dep']) && !empty($_GET['mapping_dep'])){
		$tab = explode('_', $_GET['mapping_dep'],3);
		$query[] = $tab[0].'_'.$tab[1].'.'.$tab[2].' AS '.$_GET['mapping_dep'];
		$allfld[] = $tab[0].'_'.$tab[1].'.'.$tab[2];
	}*/

	if(isset($_GET['popup_field']) && !empty($_GET['popup_field'])){
		foreach ($_GET['popup_field'] as $popup_field){
			$tab = explode('_', $popup_field,3);
			$query[$popup_field] = $tab[0].'_'.$tab[1].'.'.$tab[2].' AS '.$popup_field;
			$allfld[] = $tab[0].'_'.$tab[1].'.'.$tab[2];
		}
	}

    $popup_fields = array();
    if(isset($_GET['popup_field']) && !empty($_GET['popup_field'])) {
        foreach ($_GET['popup_field'] as $popup_field) {
            $popup_fields[] = $popup_field;
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

                $keyn = str_replace('.', '_', $field);
                $allfldq[$keyn]['info'] = $fldinfo;
                if(isset($fldinfo['sysv']) && !empty($fldinfo['sysv'])){
                    $sql = 'SELECT DISTINCT('.$field.') FROM '.$querysaveaj;
                    $resdis = db_exec($sql);
                    while ( $rowdatadis = db_fetch_assoc( $resdis ) ) {
                        $allfldq[$keyn]['value'][$rowdatadis[$tab[1]]] = $wz->getValues($fldinfo['type'],$fldinfo['sysv'],$rowdatadis[$tab[1]]);
                    }
                }
			}

			//diskFile::startJsonArray();
			$nbrval = 0;
			//$features = array();
            $features['type'] = 'FeatureCollection';
            $i = 0;
            //diskFile::startJsonArray();
			while ( $rowdata = db_fetch_assoc( $res ) ) {
				//var_dump($rowdata);
				//break;
				$json_data = array();
				$feature = array();
                $feature['type'] = 'Feature';
                $feature['geometry']['type'] = 'Point';
                $lat = null;
                $lng = null;
                if($rowdata[$lat_field]){
                   $lat = floatval($rowdata[$lat_field]);
                }
                if($rowdata[$long_field]){
                    $lng = floatval($rowdata[$long_field]);
                }
                //$rowdata[$lat_field],$rowdata[$long_field]
                if($lat && $lng){
                    $feature['geometry']['coordinates'] = array($lng,$lat);
                    foreach($popup_fields as $popup_field){
                        $forStore = $rowdata[$popup_field];
                        if(isset($fldinfo['info']['sysv']) && !empty($fldinfo['info']['sysv'])){
                            $forStore = $allfldq[$popup_field]['value'][$forStore];
                        }
                        $feature['properties'][$popup_field] = $forStore;
                    }
                    $features['features'][] = $feature;
                }
                //diskFile::putJsonData(json_encode($feature));
                $i++;
                if($i==65000)
                    break;
			}
		}
		$response = json_encode($features);
		echo $response;
        $length = ob_get_length();
        header('Content-type: application/json');
        header('Content-Length: '.$length."\r\n");
        header('Accept-Ranges: bytes'."\r\n");
        ob_end_flush();
    }else{
		
	}
	//echo file_get_contents(diskFile::getJsonPath());
	//$_SESSION ['fileNameCsh'] = $tempsession;
	return;
}elseif ($_GET['mode'] == 'geojsonMap'){
	//var_dump($_GET);
	//exit;
	//return;
}elseif($_POST['mode'] == 'grapher_save'){
    /*echo '<pre>';
    var_dump($_POST);
    echo '</pre>';*/
    //calcs=' + JSON.stringify(data)+'&calcs2=' + JSON.stringify(self.collector());
    $urlData = 'calcs='.$_POST['calcs'].'&calcs2='.$_POST['calcs2'];
    //echo $urlData;
    if($_POST['type']=='TABLE') {
        $_POST['data_item'] = file_get_contents($baseDir . '/files/tmp/' . $_SESSION ['fileNameCshBack'] . '.tss');
    }
    //INSERT INTO `dashboard_grapher`(`id`, `set_id`, `project_id`, `type`, `query_save`, `data_item`)
    $sql = "INSERT INTO dashboard_grapher(`set_id`, project_id, `type`, query_save, data_item) VALUES 
    (".$_POST['setid'].",".$_POST['project'].",'".$_POST['type']."',null,'".mysql_real_escape_string(gzencode(var_export($_POST['data_item'],true), 9, FORCE_GZIP))."')";
    //echo $sql;
    $zid = 0;
    $res = db_exec($sql);
    if($res)
        $zid = db_insert_id();
    if (!($zid > 0)) {
        $zid = 'fail';
    }else{
        echo "insert successfully";
    }

    return;
}elseif($_POST['mode'] == 'btable'){
    //var_dump($_POST);
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
				/*if($key=='wform_81_fld_63')
				    echo strval($forStore).' ';*/
				$forStore = strval($forStore);
				if($forStore=='0')
                    $forStore = '-1';
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
/*$moduleScripts[]="./modules/outputs/highcharts.js";
$moduleScripts[]="http://code.highcharts.com/modules/exporting.js";*/
$moduleScripts[]="./modules/outputs/stats.js";
$moduleScripts[]="./modules/outputs/reporter.js";
$moduleScripts[]="./modules/outputs/jquery-ui.min.js";
$moduleScripts[]="./modules/outputs/CLEditor1_4_5/jquery.cleditor.min.js";
/*$moduleScripts[]="./modules/outputs/ckeditor/ckeditor.js";*/
$js_comm='false';
$mapRequest = false;
$project_id = 0;
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
//$q->addTable('activity_queries','act');
//$q->addOrder("created desc");
//$queriez['ActQr']=$q->loadList();
//$q->clearQuery();
$q->addQuery('id,title as qname');
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
echo '<br/>
<DIV id="tabs" class="bigtab card">
    <ul class="topnav tabs-nav" style="margin-left: 7px">
        <LI><A href="#tabs-1">'.$AppUI->_('Queries').'</A></LI>
        <LI><A href="#tabs-2">'.$AppUI->_('Forms').'</A></LI>
        <LI><A href="#tabs-3">'.$AppUI->_('Tables').'</A></LI>
        <LI class="tabs-disabled"><A href="#tabs-4">'.$AppUI->_('Stats').'</A></LI>
        <LI><A href="#tabs-5">'.$AppUI->_('Report').'</A></LI>
        <LI class="tabs-disabled"><A href="#tabs-6" id="mapstab">'.$AppUI->_('Maps').'</A></LI>
        <LI><A href="#tabs-7">'.$AppUI->_('Sets').'</A></LI>
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
                        <!--<th class="phead">&nbsp;</th>--><th class="phead">'.$AppUI->_('Name').'</th><th class="phead">'.$AppUI->_('Type').'</th><th class="phead">&nbsp;</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
    
            <!--<table cellspacing="1" cellpadding="2" border="0" class="tbl tablesorter moretable ck" id="qtable" style="width: 100%">-->
            <table cellspacing="1" cellpadding="2" border="0" class="tbl moretable ck" id="qtable" style="width: 95%">
                <thead>
                    <tr>
                        <!--<th class="phead">&nbsp;</th>-->
                        <th class="phead">'.$AppUI->_('Name').'</th>
                        <th class="phead">'.$AppUI->_('Type').'</th>
                        <th class="phead">'.$AppUI->_('Item Type').'</th>
                        <th class="phead">'.$AppUI->_('Description').'</th>
                        <!--<th class="phead">'.$AppUI->_('Start Date').'</th>
                        <th class="phead">'.$AppUI->_('End Date').'</th>-->
                        <th class="phead">&nbsp;</th>
                        <!--<th class="phead">&nbsp;</th>-->
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
                    $pnameOut='Table'.$pname;
                }
                $qsr.='<tr id="qsr_'.$trid.'" data-showr="'.$sr.'">
                <!--<td title="Edit" align="center"><div class="'.$edClass.' fa fa-pencil" data-id="'.$row['id'].'" style="color: blue;font-size: large"></div></td>-->';
                $st=trimView($row['qname']);
                $qsr.='<td data-text="'.$st['orig'].'" '.($st['show'] === true ? ' class="moreview"' : '').' data-id="'.$row['id'].'"><span class="fhref flink" onclick="qurer.run(\''.$trid.'\',\'run\');">'.$st['str'].'</span></td>
                <td align="center">'.$pnameOut.'</td>
                <td>&nbsp;</td>';
                $st=trimView($row['qdesc']);
                $qsr.='<td data-text="'.$st['orig'].'"'.($st['show'] === true ? ' class="moreview"' : '').' >'.$st['str'].'</td>';
                $sdateClean=viewDate($row['sdate']);
                $edateClean=viewDate($row['edate']);
                //if($pname == "Table"){
                //onclick = "popTCalendar(\'start_'.$trid.'\')"
                //onclick = "popTCalendar(\'end_'.$trid.'\')"
                /*$qsr.='
                    <td align="center">
                        <div class="tdw">
                        <div class="stdw" fsort="'.$sdateClean[1] .'">'.$sdateClean[0].'</div>
                        <!--<img width="16" height="16" border="0" alt="'.$AppUI->_('Calendar').'" src="/images/calendar.png" class="calpic" onclick = "popTCalendar(\'start_' . $trid . '\')">-->
                        </div>
                        <input type="hidden" id="start_'.$trid.'" value="'.$row['sdate'].'" >
                    </td>
                    <td align="center">
                        <div class="tdw">
                        <div class="stdw" fsort="'.$edateClean[1] .'">'.$edateClean[0].'</div>
                        <!--<img width="16" height="16" border="0" alt="Calendar" src="/images/calendar.png" class="calpic" onclick = "popTCalendar(\'end_' . $trid . '\')">-->
                        </div>
                        <input type="hidden" id="end_'.$trid.'" value="'.$row['edate'].'" >
                    </td>';*/
                /*}else{
                    $qsr.='<td >&nbsp;</td><td >&nbsp;</td>';
                }*/
                $qsr.='
                <!-- <td ><span title="Run" class="fhref"><img src="/images/run1.png" weight=22 height=22 border=0 alt="Run"></span></td> -->
                <td align="center"><span title="'.$AppUI->_('Delete').'" class="fhref fa fa-trash-o" style="color: blue;font-size: large" onclick="qurer.delq(\''.$trid.'\');" >
                <!-- <img src="/images/delete1.png" weight=16 height=16 border=0 alt="Delete"> -->
                </span>
                <span title="'.$AppUI->_('Export').'" style="color: blue;font-size: large" class="exportq fa fa-download" onclick="qurer.run(\''.$trid.'\',\'export\');" ></span>
                <a href="?m=outputs&rep='.$row['id'].'"><span title="'.$AppUI->_('Edit').'" style="color: blue;font-size: large" class="exportq fa fa-pencil" onclick="qurer.run(\''.$trid.'\',\'export\');" ></span></a>
                </td>
                <!--<td align="center"><div title="'.$AppUI->_('Export').'" style="color: blue;font-size: large" class="exportq fa fa-download" onclick="qurer.run(\''.$trid.'\',\'export\');" ></div></td>-->
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
        $projects = '<div class="row">';
        if(!$is_superAdmin){
            foreach($rows as $k => $v){
                if(!$perms->checkForm($AppUI->user_id,'projects',$k,'view')){
                    continue;
                }
                $projects .= '<div class="col-sm-2"><label><input type="radio" name="projects[]" class="projects" value="'.$k.'">&nbsp;&nbsp;'.$v.'</label></div>';
            }
        }else{
            foreach($rows as $k => $v){
                $projects .= '<div class="col-sm-2"><label><input type="radio" name="projects[]" class="projects" value="'.$k.'">&nbsp;&nbsp;'.$v.'</label></div>';
            }
        }

        $projects .= '</div><a href="#" class="" id="map_link_project" onclick="mapProjects()"></a>';
        echo       '<h3 style="padding: 5px;">&emsp;&emsp;'.$AppUI->_('Projects').'</h3>';
        echo '<div id="bparts" style="height: 90px">
        <p>';
            echo $projects;
            echo
        '</p>
		</div>';

//echo '<h3 style="padding: 5px;">&emsp;&emsp;Activity</h3>';
//echo '<div class="list" id="tasks" style="height: 90px"></div>';

echo '<h3 style="padding: 5px;">&emsp;&emsp;'.$AppUI->_('Forms').'<div id="imgloader" style="display:none;width:25px;height:25px"><img src="/modules/outputs/images/ajax-loader.gif"/></div></h3>';
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
echo '</p>';
if($lpo === true) {
}else{
    $sector_list = dPgetSysVal ( 'SectorType' );
    AreasArrayList();
    ?>
    <div id="dbaddf" title="Save To File" style="display: none">
        <table>
            <tr>
                <td>Query Name</td>
                <td><input type='text' id='fnamefile' style='width:90%'/></td>
            </tr>
            <tr>
                <td>Activity</td>
                <td>
                    <select id="ftask" style='width:90%'>
                        <option value="-1">-- Select Activity --</option>
                    <?php
                    $sqlt='select task_id,task_name FROM tasks WHERE task_project='.$project_id;
                    $res=mysql_query($sqlt);
                    if($res){
                        while($trow=mysql_fetch_assoc($res)){
                            $point = '';
                            if(strlen($AppUI->_($trow['task_name']))>25)
                                $point = '...';
                            echo '<option value="'.$trow['task_id'].'">'.substr($AppUI->_($trow['task_name']), 0,24).$point.'</option>';
                        }
                    }
                    ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Strategic Areas</td>
                <td>
                    <?php echo htmlAreasSelectList("id='fst_area' style='width:90%'",true); ?>
                </td>
            </tr>
            <tr>
                <td>Sector</td>
                <td>
                    <?php echo arraySelect ( arrayMerge(array('-1'=>'-- Select Sector --'),$sector_list), "", "id ='fsector' style='width:90%' class=''", null, false ); ?>
                </td>
            </tr>
            <tr>
                <td>Beneficieries</td>
                <td><input type='number' value="<?php echo $countall?>" readonly='readonly' id="fcountall" style='width:90%'/></td>
            </tr>
            <tr>
                <td>Amount</td>
                <td><input type='number' value="" id="famount" style='width:90%'/></td>
            </tr>

            <tr>
                <td colspan="2">
                    Save list
                    <input type="checkbox" id="fsaveben" checked="checked"/>
                </td>
            </tr>
            <tr>
                <td>Description</td>
                <td><textarea id='fdesc' style='width:90%'></textarea></td>
            </tr>
        </table>
    </div>
    <?php
    function taskAddon(){
        global $task_locations;
        ?>

        <?php
    }
    taskAddon();
}
echo '</div>';

unset($rhtml);
//flush_buffers();
//<!-- <div class="fbutton sec_type sec_table" title="Custom section"></div> -->
/*Report to be here*/
echo '<div  id="tabs-6" class="mtab" style="100%">';
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
	  width:75%;
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
      padding: 5px;
	  /*height:300px;*/
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

#loader{
		    position:fixed;
  top:0px;
  right:0px;
  width:100%;
  height:100%;
  display:none;
  /*background-color:#666;*/
  background-color:rgba(0,0,0,.4);
  background-image:url('/modules/outputs/images/loader.gif');
  background-repeat:no-repeat;
  background-position:center;
  z-index:10000000;
  /*opacity: 0.4;*/
  filter: alpha(opacity=40); /* For IE8 and earlier */
  text-align: center;
vertical-align: middle;
line-height: 800px;
			 /*position: fixed;
			  left: 0px;
			  top: 0px;
			  width: 100%;
			  height: 100%;
			  z-index: 9999;
			  opacity: 0.4;
			  display:none;
			  background: url('/modules/outputs/images/loader.gif') 50% 50% no-repeat rgb(0,0,0);*/
			}
			#percentloader{
			 position: fixed;
			  left: 0px;
			  top: 0px;
			  width: 1%;
			  height: 1%;
			  z-index: 99999;
			  background: red
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
/*echo '<div style="100%">';
echo '<div class="left">
            <form name="mapform" id="mapform">
         <!--<div class="mapfieldbox" style="overflow:auto"><center>Fields</center>
              <ul id="box-home" style="list-style: none;">
              </ul>
         </div>-->
         <!--<div>

              <table>
                  <tr><td><label><input type="checkbox" checked="checked" id="markergroup">'.$AppUI->_('Marker group by administrative location').'</label></td></tr>
              </table>
       </div>-->
       <div class="row">
            <div >
                <label><input type="checkbox" checked="checked" id="markergroup">'.$AppUI->_('Marker group by administrative location').'</label>
            </div>
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
       <!--*******<div class="mapdatamappingbox">
           <table>
             <tr><td><h3 style="margin-top: 0px;float:left">'.$AppUI->_('Data mapping').'</h3></td></tr>
             <tr><td><table style="margin-top: 0px;float:left" id="mappingfield_table">
             </table></td></tr>
           </table>
         </div>******************-->
       <div class="mappopupinfogbox">
           <table>
             <tr><td><h3 style="margin-top: 0px;float:left">'.$AppUI->_('Popup info').'</h3></td></tr>
             <tr><td><table style="margin-top: 0px;float:left" id="popupfield_table">
             </table></td></tr>
           </table>
         </div>
       <!--************<div class="mappopupinfogbox11">
           <table>
             <tr><td><h3 style="margin-top: 0px;float:left">'.$AppUI->_('Typography marker').'</h3></td></tr>
             <tr><td><table style="margin-top: 0px;float:left" id="mtype_table">
             </table></td></tr>
           </table>
         </div>*******************-->
        <div class="mappopupinfogbox11">
           <table>
             <tr><td><h3 style="margin-top: 0px;float:left">'.$AppUI->_('Filter').'</h3></td></tr>
             <tr><td><table style="margin-top: 0px;float:left" id="mfilter_table">
             </table></td></tr>
           </table>
         </div>
         <div class="mappopupinfogbox">
           <table>
             <tr><td><a href="#" class="button" id="savemaps">'.$AppUI->_('Save maps').'</a> <a href="#" class="button" id="btngomap">'.$AppUI->_('Go').'</a></td><td><button class="button">Clear</button></td></tr>
           </table>
         </div></form>
      </div>';
echo '</div>';
echo '<div id="loader">

</div>';

//echo 'Hello World';*/
echo '<div class="row">';
echo '<div class="col-md-3">';
?>
    <div class="row">
        <div class="col-md-12">
            <?php echo '<label><input type="checkbox" checked="checked" id="markergroup">'.$AppUI->_('Marker group by administrative location').'</label>';?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="maplonlatbox">
                <?php echo '<h2>'.$AppUI->_('Geographical coordinates').'</h2>';?>
                <div class="form-group">
                    <label for="maplon_select">Longitude field</label>
                    <select id="maplon_select" name="mapping_lon" style="width:200px;" class="form-control">
                        <option></option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="maplat_select">Latitude field</label>
                    <select id="maplat_select" name="mapping_lat" style="width:200px;" class="form-control">
                        <option></option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="maplonlatbox">
                <?php echo '<h2>'.$AppUI->_('Administrative location').'</h2>';?>
                <div class="form-group">
                    <label for="mapdep_select"><?php echo $AppUI->_('Department field')?></label>
                    <select id="mapdep_select" name="mapping_dep" style="width:150px;" class="form-control">
                        <option></option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="mapcom_select"><?php echo $AppUI->_('Commun field')?></label>
                    <select id="mapcom_select" name="mapping_com" style="width:150px;" class="form-control">
                        <option></option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="maplonlatbox">
                <?php echo '<h2>'.$AppUI->_('Data mapping').'</h2>';?>
                <div class="form-group">
                    <label for="mapdep_select"><?php echo $AppUI->_('Department field')?></label>
                    <select id="mapdep_select" name="mapping_dep" style="width:150px;" class="form-control">
                        <option></option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="mapcom_select"><?php echo $AppUI->_('Commun field')?></label>
                    <select id="mapcom_select" name="mapping_com" style="width:150px;" class="form-control">
                        <option></option>
                    </select>
                </div>
            </div>
        </div>
    </div>
<?php
echo '</div>';
echo '<div class="col-md-9"><div class="right" id="map" style="display:none"></div></div>';
echo '</div>';

echo '</div>';

echo '<div id="tabs-7">';
echo '<span onclick="$j(\'#setbox\').toggle();" class="fhref flink">'.$AppUI->_('Create Set').'</span><span class="offwall msgs" id="msg_place"></span>';
echo '<div id="setbox" class="myset" style="display: none">
        <form name="setuqp" action="/?m=outputs&suppressHeaders=1" method="POST" onsubmit="return AIM.submit(this, {\'onStart\' : startCallback, \'onComplete\' : qurer.extractRowSet})">
            <input type="text" name="set">
            <input type="submit" value="'.$AppUI->_('Create').'" class="button ce pi ahr">
            <input type="hidden" name="mode" value="setquery">
        </form>
	  </div>';
$sql='select id,setname FROM sets';
$res=mysql_query($sql);
$all = array();
if($res && mysql_num_rows($res)  > 0) {
    while ($trow = mysql_fetch_assoc($res)) {
        $all[] = $trow;
    }
}
echo '<table cellspacing="1" cellpadding="2" border="0" class="tbl tablesorter moretable ck" id="settable" style="width: auto">
			<thead>
			<tr>
				<th class="phead">'.$AppUI->_('Name').'</th>
				<th class="phead">&nbsp;</th>
			</tr></thead>';
echo '<tbody>';
foreach ($all as $i=>$set){
    echo '<tr>
            <td>'.$set['setname'].'</td>
            <td>
            <span id="set_d_'.$set['id'].'" onclick="qurer.delSetRow('.$set['id'].',\''.$set['setname'].'\')"  class="fhref fa fa-trash-o" style="color: blue;font-size: large"></span>
            <span id="set_e_'.$set['id']. '" onclick="qurer.edit()"  class="fhref fa fa-edit" style="color: blue;font-size: large"></span>
            </td>
          </tr>';
}
echo '</tbody>';
echo '</table>';
echo '</div>';

echo '<div id="dbset" title="Choose set" style="display: none">
    <select id="chooseset" style="width: 50%">
        <option value=""></option>';
        foreach ($all as $i=>$set){
            echo '<option value="'.$set['id'].'">'.$set['setname'].'</option>';
        }
echo '</select>
</div>';


$tpl = new Templater($baseDir.'/modules/outputs/report.tpl');
$tpl->cal_start=drawDateCalendar('rep_start','',false,'id="rep_start"',false,10);
$tpl->cal_end=drawDateCalendar('rep_end','',false,'id="rep_end"',false,10);
$tpl->thtml = $thtml;
$tpl->dept_selector = arraySelect(dPgetSysVal("ClinicalDepartments"),'rep_dept',"id='rep_dept' class='text'",1);
$tpl->output(true);






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
$tpl->project = $project_id;
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
$tpl->btr = json_encode($l);
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
	//echo '<pre>';
	//var_dump($svals);
	//echo '</pre>';
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
  
	  		
      ';
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
&nbsp;&nbsp;&nbsp;&nbsp;

<script src="<?php echo DP_BASE_URL?>/style/default/jquery-1.10.2.min.js"></script>
<script>
	$(document).ready(function(){
		$($("#qtable")[0]).live("click",function(e){
//			alert($(this).closest("tr").html());
		});
//		alert($($("#qtable")[0]).html());
//		alert("Je susis");
	});
</script>