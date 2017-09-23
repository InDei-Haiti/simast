<?php
require_once($baseDir . '/modules/outputs/outputs.class.php');
buildTableDataDemand();
//check permissions for this record
$perms = & $AppUI->acl ();

$is_superAdmin = false;
$roles = $perms->getUserRoles($AppUI->user_id);
foreach ($roles as $role){
    if($role['value']=='super_admin'){
        $is_superAdmin = true;
    }
}
if($_POST['mode']=='save'){

	require_once($baseDir.'/modules/outputs/patch.func.php');

	exportResultExcel();
}
global $titles;
$tkeys = array_keys ( $titles );

?>
<link rel="stylesheet" type="text/css" href="./modules/outputs/outputs.module.css" />
<link rel="stylesheet" type="text/css" href="./modules/outputs/jquery-ui-1.7.2.custom.css" />
<?php
$staterd=0;
$l='""';
$f='""';
$h='""';
$u='""';
$s='""';
$p='""';
$e=array();
$m1=array();
$html='';
$rhtml='';
$thtml='';
$bigtar=array();
$rqid=0;
$ftabsel=0;
$sels=array();
$mode='simple';
$bigtar_cnt=0;
$clients_cnt=0;
$clients=array();
$vis_mode='';
$lcrows=0;
$colsConst=array();
$thisCenter=false;
$y=0;
$countall=0;
$moduleScripts[]="./modules/outputs/outputs.module.js";
$moduleScripts[]="./modules/outputs/stats.js";
//$moduleScripts[]="./modules/outputs/jquery-ui-1.7.2.custom.min.js";
if ($_SERVER ['CONTENT_LENGTH'] > 0 && count ( $_POST ) > 0) {
	unset($_SESSION['SAVEQUERYANA']);
}
if(isset($_GET['token']) && !empty($_GET['token'])){
	if(isset($_SESSION['SAVEQUERYANA'])){
		$_POST = $_SESSION['SAVEQUERYANA'][$_GET['token']];
	}
}
if (/* $_SERVER ['CONTENT_LENGTH'] > 0 &&  */count ( $_POST ) > 0) {
	require_once($baseDir . '/modules/outputs/result.func.php');
	/* $lpost = array ();
	$starter=0;
	$ender=0;
	$show_start='';
	$show_end='';
	$header[0] = array ('client_id' => 'Client ID', 'client_adm_no' => 'Client Adm No' );
	$header[1] = array(array('v'=>'Clients','r'=>'CLI'),array('v'=>'Clients','r'=>'CLI'));
	$final = array();
	$nfei= new evolver();
	$y=0;
	$tab_src='';
	echo 'ok';
	resultBuilder('mas');
	$ftabsel=2;
	$mode='result'; */
	$lpost = array ();
	$starter=0;
	$ender=0;
	$show_start='';
	$show_end='';
	$final = array();
	$nfei= new evolver();
	$y=0;
	$tab_src='';
	resultBuilder('mas');
	$ftabsel=2;
	$mode='result';
}else{
	$l='""';
	$f='""';
	$h='""';
	$u='""';
	$s='""';
	$m1=array();
}
// onsubmit="return false;"




$htmlpre = '<form method="POST" action="?m=manager&part=cleaner" id="sendAll" name="xform" >
	<input type="hidden" name="stype">
	<input type="hidden" name="pmode">
	<input type="hidden" name="faction">
';


$htmlpre .= '<div id="awrapper" style="width:95%">';
$htmlpre .= '<div id="bset" style="color:red">';

$q = new DBQuery();
$q->addTable("projects");
$q->addQuery("project_id,project_name");
$rows = $q->loadHashList();

$projects = '<div class="row">';
$perms =& $AppUI->acl();
foreach($rows as $k => $v){
    if(!$is_superAdmin) {
        if (!$perms->checkForm($AppUI->user_id, 'projects', $k, 'view')) {
            continue;
        }
    }
	$projects .= '<div class="col-sm-2"><label><input type="radio" name="projects[]" class="projects" value="'.$k.'">&nbsp;&nbsp;'.$v.'</label></div>';
}
$projects .= '</div>';
$htmlpre .= '<h3 style="padding: 5px;">&emsp;&emsp;'.$AppUI->_('Projects').'</h3>';
$htmlpre .= '<div id="bparts" style="height: 90px">
<p>';
$htmlpre .=  $projects;
$htmlpre .= '</p>
		</div>';

//echo '<h3 style="padding: 5px;">&emsp;&emsp;Activity</h3>';
//echo '<div class="list" id="tasks" style="height: 90px"></div>';

$htmlpre .= '<h3 style="padding: 5px;">&emsp;&emsp;'.$AppUI->_('Forms').'<img src="/modules/outputs/images/ajax-loader.gif" id="imgloader" style="display:none;width:20px;height:20px"/></h3>';
$htmlpre .= '<div  id="forms" style="height:300px">
		</div>';

$htmlpre .= '</div>';
$htmlpre .= '</div>';


/* $html = '<form method="POST" action="?m=masteredit" id="sendAll" name="xform" onsubmit="return false;">';
$mi = 0;
$block_count = 1;
ksort ( $fielder );

//$html=buildForms(&$fielder);

$lasttext='';
$alltext='';
$firsttext='';
if ($vis_mode == 'last') {
	$lasttext = 'checked';
} elseif($vis_mode == 'first') {
	$firsttext = 'checked';
}else{
	$alltext='checked';
}

$curcentext=($thisCenter !== FALSE ? 'checked' : '');

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

$lcrows=0;
if($bigtar_cnt == 0 &&  $clients_cnt  == 0){
//	$rhtml='<span class="note">No data to display</span>';
}else{
	if($bigtar_cnt > 0){
		$lcrows=$bigtar_cnt;
	}elseif (count($clients) > 0){
		$lcrows=$clients_cnt;
	}
}

unset($bigtar,$header,$clients,$fielder);
$html = $htmlpre.buildSelectOptions().$html;
 */

$html = $htmlpre;

$html .= '<br><br>
<div style="width: 1000px;">
	<input type="button" value="'.$AppUI->_('Go').'" onclick="getData()" class="button">&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="button" value="'.$AppUI->_('Clear Forms').'" onclick="clearData()" id="fcleaner" class="button" '. (($tchex > 0) ? '': 'disabled="disabled"').'>
</div>
</form>';

echo $dst;
unset($dst);
//cleanALoc(&$lpost);
if ( class_exists(diskFile) && diskFile::calls() > 0 ) {
	$pstr=array('<div id="folder" class="suprcov">',$html,'</div>',"LLDCALLLL");
	//$pstr='$dst <div id="folder" class="suprcov"> $html </div> $rhtml';
} else {
	$pstr=array($html);
	//$pstr='$dst  $html';
}
flush_buffers();
unset($html);
ob_end_flush();
//eval('echo "'.$pstr.'";');
foreach ($pstr as $vss) {
	if($vss === 'LLDCALLLL'){
		diskFile::printOut();
	}else{
		echo $vss;
	}
	unset($vss);
}
ob_start();
unset($pstr);
//echo '</p></div>';
//$html.='</form>';
//echo $html;
$newe=array();
foreach ($e as $pt){
	$newe[$pt]=$titles[$pt]['link'];
}
/*
style="border-top: 1px solid #BFC3D9; position: relative; height: 20px; display: block; top: -4px"
*/
?>
<div id='stip'></div>
<div id="shadow" style="display:none"></div>
<div id='mbox'></div>
<div id="filbox" style="position: absolute; display: none;" class="filter_box box1">
	<div id="menu">
		<ul id="toplevel">
		<li>
			<div class="sib asci"></div>
			<span class="fhref" onclick="gpgr.ifsort('desc');">Sort Asc</span>
		</li>
		<li>
			<div class="sib desci"></div>
			<span class="fhref" onclick="gpgr.ifsort('asc');">Sort Desc</span>
		</li>
		<li>
			<div class="sib coli"></div>
			<span class="fhref" onclick="filmter.lects(this);">Values</span>
		</li>
		<li id="lbl">
	    	<span class="fillink" onclick="filmter.showfils(this);">Filters</span>
    	    <div class="sib"><input type="checkbox" id="fil_on" data-area="" value="1" onchange="filmter.checkFilter(this);" disabled="disabled" class="superbox"></div>
		</li>
		</ul>
	</div>
</div>
<div id="fil_list"  class="filter_box box2"></div>
<div id="filin_list"  class="filter_box box3"></div>
<div id="fil_stats" class="filter_box box4">
	<ul class="tobs">
		<li class="ffbb fil_line"><input type="checkbox" class="row_check"><?php echo $AppUI->_('Add to rows area')?></li>
		<li class="ffbb fil_line"><input type="checkbox" class="col_check"><?php echo $AppUI->_('Add to columns area')?></li>
	</ul>
</div>
<div id='stip'></div>
<script>
chex=<?php
	echo ($mi - 1);
	?>;
	var countall = <?php echo $countall;?>;
	rrr=<?php echo $y;//$lcrows;?>;
	today=<?php echo date("Ymd");?>;
	fakes=<?php echo json_encode($f);?>;
	btr=<?php echo json_encode($l); ?>;
	heads=<?php echo json_encode($h); ?>;
	lets=<?php echo json_encode($u); ?>;
	selects=<?php echo json_encode($sels); ?>;
	var multies = <?php echo json_encode($m1)?>;
	aopen=<?php echo json_encode($auto_open);?>;
	st_do=<?php echo $staterd;?>;
	cols_const=<?php echo json_encode($colsConst);?>;
	var editArr = <?php echo json_encode($newe);?>;
	var plur =<?php echo json_encode($p);?>;
	var multistart=false;

//	window.onload=up;
	//function up (){
	var tabLaunch="prePage();";
	//}


</script>
