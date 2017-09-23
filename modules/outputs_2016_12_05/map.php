<?php
global $myspeed;
/* comment 1 if (! defined ( 'DP_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
//require_once DP_BASE_DIR.'/modules/outputs/fields.class.php';
$possibles = array ('project_type', 'task_sector', 'project_status', 'task_areas' );
$multiples = array('task_sector','task_areas');
$addTables = array('project_type'=>'projects','project_status'=>'projects');
$wheresp = array (
				'project_type'=>'projects.project_id =  tasks.task_project',
				'project_status'=>'projects.project_id =  tasks.task_project'
				);
$ttt = $wheres = array();
if ($_POST ['getlist'] == 'tasks') {
	$scons = json_decode ( stripslashes( $_POST ['needs'] ),true );
	foreach ( $scons as $key => $var ) {
		if (in_array ( $key, $possibles )) {
			if(!in_array($key,$multiples)){
				$wheres [] =  $key . ' in (' . join ( ',', $var ) . ')';
			}else{
				$toadd=array();
				foreach ($var as $vw) {
					$toadd[] = '('.$key.' REGEXP "[[:<:]]' . $vw . '[[:>:]]")';
				}
				$wheres[] = '('.join(" OR ",$toadd).' )';
			}
			if(isset($addTables[$key])){
				$ttt[]=$addTables[$key];
			}
		}
	}
	
	//if(strlen($scons) > 0){
	$q = new DBQuery ( );
	//,task_gps_x,task_gps_y,task_country,task_admin2,task_admin3,task_admin4,task_admin5,task_admin6
	$q->addQuery ( 'task_id,task_name,task_description,task_locations' );
	if(count($wheres) > 0){
		$q->addWhere ( join ( ' AND ', $wheres ) );
	}
	$q->addTable ( 'tasks' );
	if(count($ttt) > 0){
		foreach ($ttt as $tadd){
			$q->addTable($tadd);
		}
	}
	$tasks = $q->loadArray ();
	$newts = array ();
	$seen_levs = array ();
	$backt = array ();
	$gpx = array ();
	$country_cache = array ();
	$tx = 0;
	$tb = 0;
	if(count($tasks) > 0){
		require_once DP_BASE_DIR.'/templates/location_dump.php';
	}*/
	/* comment 2 foreach ( $tasks as $task ) {
		$seen=false;
		if ($task [3] == '' || $task [4] == '') {
			$found = false;
			$tlox  = explode(",",$task[3]);
			$dval=null;
			foreach ($tlox as $tl){*/
			/*for($x = 10; $x > 5; $x --) {
				$tv = ( int ) $task [$x];
				if ($tv > 0) {*/
					/*if (! is_array ( $seen_levs [$x] )) {
						$seen_levs [$x] = array ();
					}
					if (! array_key_exists ( $tv, $seen_levs [$x] )) {*/
						/*$q1 = new DBQuery ( );
						$q1->addQuery ( "gps" );
						$q1->addTable ( 'administrative_regions' );
						$q1->addWhere ( 'region_id=' . $tv );
						$q1->addWhere ( "gps <> ''" );
						$dval = $q1->loadResult ();*/
						/* comment 3 if(isset($locData[$tl]) && $locData[$tl]['gps'] !=''){
							$dval = @$locData[$tl]['gps'];
						}
						if ($dval) {
							$x = 1;
							$backt [$tb] = $task;
							$gpsc = explode ( '|', $dval );
							$backt [$tb] [3] = $gpsc [0];
							$backt [$tb] [4] = $gpsc [1];
							$backt [$tb] [5] = $seen;
                            //$backt [$tb] [6] = $tl;
							if (! array_key_exists ( $dval, $gpx )) {
								$gpx [$dval] = array ();
							}							
							$gpx [$dval] [] = $tb++;
							//++$tb;
							$found = true;
						}
					$dval=null;		
				$seen = false;
			}*/
			/*}*/
			/*if (! $found && $task [5] > 0) {
				if (! array_key_exists ( $task [5], $country_cache )) {
					$q1 = new DBQuery ( );
					$q1->addQuery ( 'gps' );
					$q1->addTable ( 'administrative_regions' );
					$q1->addWhere ( 'region_id=' . $task [5] );
					$ccon = $q1->loadResult ();
					if ($ccon) {
						$country_cache [$task [5]] = $ccon;
					}
				} else {
					$ccon = $country_cache [$task [5]];
				}
				$backt [$tb] = $task;
				$gpsc = explode ( '|', $ccon );
				$backt [$tb] [3] = $gpsc [0];
				$backt [$tb] [4] = $gpsc [1];
				if (! array_key_exists ( $ccon, $gpx )) {
					$gpx [$ccon] = array ();
				}
				$gpx [$ccon] [] = $tb;
				$tb++;
				
			}*/
		
		/* comment 4 } else {
			$newts [$tx] = $task;
		}
		$tx ++;
	}
	$reorder = array();
	$reind = 0;
	$pure=array();
	foreach ($gpx as $coord => $tar){		
		$reorder[$reind]=$backt[$tar[0]];
		$descs_init= '<ol style="height: 150px;width: 170px;overflow: auto;">';
		$inlist = array();
		if(count($tar) > 1){
			foreach ($tar as $tin){
				if(!in_array($backt[$tin][1],$pure)){
					$inlist[]='<li>'.$backt[$tin][1].'<br><i>'.$backt[$tin][2].'</i></li>';
					$pure[] = $backt[$tin][1];
				}
			}
		}else{
			$tin = current($tar);
			$descs.='<i>'.$backt[$tin][2].'</i>';
			$pure[] = $backt[$tin][1];
		}
		$reorder[$reind][2]=$descs_init.join("\n",$inlist).'</ol>';
		$ttasks= count($tar);
		if(count($pure) > 1){
			$reorder[$reind][1]='Group of '.count($pure).' activities';
		}else{
			$vt = explode("<br>",$reorder[$reind][2]);
			$vt1 = str_replace("</li>","",$vt[1]);
			$reorder[$reind][2] = $descs_init.$vt1.'</ol>';
		}
		++$reind;
		$pure=array();
	}
	$alltasks= array_merge($newts,$reorder);
	$resp = json_encode ( $alltasks );
	echo $resp;
	return;
	//}
}

function selFromlist($ar, $name) {
	$shtml = '<ul class="maplist" data-name="' . $name . '">';
	foreach ( $ar as $key => $sec ) {
		if ($key == - 1) {
			$cht = 'checked="cheked"';
		} else {
			$cht = '';
		}
		$shtml .= '<li><input type="checkbox" name="' . $name . '" value="' . $key . '" ' . $cht . '>' . $sec . '</li>';
	}
	$shtml .= '</ul>';
	return $shtml;
}

$tabs = array ();*/

/*$q = new DBQuery ( );
$q->addTable ( 'administrative_regions' );
$q->addQuery ( '*' );
$q->addWhere ( 'region_parent="0"' );
$countries = $q->loadArray ();*/

/* comment 5 $cnt = new sarea();
$chtml = selFromlist ( $cnt->valuesForMap (), "task_areas" );
$tabs ['Strategy Areas'] = $chtml;

$sector_list = arrayMerge ( array (- 1 => 'all sectors' ), dPgetSysVal ( 'SectorType' ) );
$seclist = selFromlist ( $sector_list, 'task_sector' );
$tabs ['Sectors'] = $seclist;

$task_types = arrayMerge ( array ("-1" => 'all types' ), dPgetSysVal ( 'ProjectType' ) );
$ttypes = selFromlist ( $task_types, 'project_type' );
$tabs ['Project type'] = $ttypes;

$pr_st = arrayMerge ( array ("-1" => 'all types' ), dPgetSysVal ( 'ProjectStatus' ) );
$ttypes = selFromlist ( $pr_st, 'project_status' );
$tabs ['Project Status'] = $ttypes;*/


/*$prjc = new project ( );
$prhtml = selFromlist ( $prjc->valuesForMap (), 'project' );
$tabs ['Projects'] = $prhtml;*/

/*$q = new DBQuery();
$q->addTable('st_area');
$q->addWhere('parent_id ="0"');
$q->addOrder('prex');
$areas = $q->loadArray();
$ahtml = selFromlist($areas,'task_areas');*/

/*$agc = new agency ( );
$aghtm = selFromlist ( $agc->valuesForMap (), 'agency' );
$tabs ['Agencies'] = $aghtm;*/

//$myspeed->addJs ( 'http://maps.google.com/maps?file=api&v=2&key=' . $GMapKey, 'file' );
/* $myspeed->addJs(' http://maps.googleapis.com/maps/api/js?sensor=false&libraries=geometry');
$myspeed->addJs ( 'modules/outputs/jquery-ui.js', 'file' );
$myspeed->addJs ( 'modules/outputs/maps2.js', 'file' );
$myspeed->addJs ( ' $j(document).ready(function(){
	xMap.initz();
	$j("#accordion").accordion({ autoHeight: false,animated: false, collapsible: true,navigation: true });
	watchsels(); 
	$j("#rendp").center();});
	$j("#tfilter").show()', 'code' );
$myspeed->addJs ( 'modules/outputs/progressBar.js','file');*/
//$myspeed->addJs ( 'modules/outputs/smartinfowindow.js','file');
//$myspeed->addJs ('http://www.acme.com/javascript/Clusterer2.js','file');
if(dPgetParam($_GET, 'type', 0)){
	if(dPgetParam($_GET, 'type', 0)=='activity'){
		if(dPgetParam($_GET, 'query', 0)=='all'){
			$q = new DBQuery();
			$q->addTable ('activity', 'ac' );
			$q->addQuery ( 'ac.activity_administration_section' );
			$activity_section = $q->loadColumns();
			foreach ($activity_section as $index => $code){
				$q->clear();
				$q->addTable('administration_section', 'section');
				$q->addJoin('administration_com', 'com', 'com.administration_com_code = section.administration_section_code_com');
				$q->addQuery ( 'DISTINCT(section.administration_section_name),com.administration_com_name' );
				$q->addWhere('section.administration_section_code in ('.$code.')');
				$data[] = $q->loadList();
			}
			$data = array_unique($data);
			foreach ($data as $row){
				foreach ($row as $ele){
					$str = explode(" ", $ele['administration_section_name']);
					array_splice($str, 0, 1);
					$str = implode(" ", $str);
					$zones[] = Array(
								'zone'=> $str.', '.$ele['administration_com_name'].', Haiti'
							   );
				}
			}
			
			$zones = json_encode($zones);
			//var_dump($zones);
		}
	}
}
?>

	<div id='tfilter' style='width: 350px;display:none;' class='bblock'>
		<div id="accordion" style='height: 500px;'>
			<?php
			/*foreach ( $tabs as $key => $var ) {
				echo '<h3><a href="#">' . $key . '</a></h3>
					<div>' . $var . '</div>';
			}*/
		?>
		</div>
	</div> 
	<div id='map' style='width: 550px;'  class="bblock"></div>
	<div id='sbar' style='width: 200px; overflow: auto' class="bblock"></div>
	<div style='clear: both; padding-top: 20px;'>
		<input type='button' class='button' value='Apply' onclick='xMap.refresh();'>&nbsp;&nbsp; 
		
		<!-- <input type='button' class='button' value='toggle' onclick='xMap.blinder();'>&nbsp;&nbsp; -->
		<input type='button' class='button' value='Clear' onclick='xMap.clean();'>&nbsp;&nbsp; 
		<input type='button' class='button' value='Snapshot' onclick='xMap.saveStatic();' id='save_but'>&nbsp;&nbsp;
		<span id='load_text' class='fblock button'>Loading...&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
		<input type='button' class='button' value='HOME' onclick='xMap.home();'>
	</div>
	
	<div id='rendp' style="display:none;">Loading ...</div>
	<div id="imger" style="display:none;"><div id='nest'></div><a href='#' onclick='$j("#imger").hide()'>Close</a></div>
	
