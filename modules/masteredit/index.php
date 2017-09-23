<?php

require_once($baseDir . '/modules/outputs/outputs.class.php');

if($_POST['mode']=='save'){
	$dlist=trim($_POST['list']);
	if(strlen($dlist) > 1){
		$tdl=json_decode(str_replace ( '\\"', '"', $dlist));
	}
	$table=$_SESSION['table']['head'];
	$blist=$_SESSION['table']['body'];
	$table.='<tbody>';
	if(count($tdl) > 0){
		foreach ($tdl as $pl) {
			$table.=$blist[$pl]['row'];
		}
		$table.='</tbody></table>';
		$table=str_replace('\n',"",$table);
		$table=str_replace('\t',"",$table);
		$ps=stripslashes($table);
		$ps= '
  			<style type="text/css">
  				table {border :2px solid #000;}
  				td{border:2px solid #000;padding: 2px;}
  			</style>'.$ps;
		header("Content-disposition: attachment; filename=table.xls");
		header("Content-type: application/vnd.ms-excel");
		header( "Pragma: no-cache" );
		header( "Expires: 0" );
		header("Content-Length: " . strlen($ps) ."; ");
		echo $ps;

	}
	return ;
}elseif($_POST ['mode'] == "query") {
	$imode = trim ( $_POST ['imode'] );
	$sname = mysql_real_escape_string ( trim ( $_POST ['qname'] ) );
	$sdesc = mysql_real_escape_string ( trim ( $_POST ['qdesc'] ) );
	$qsid=(int)$_POST['sid'];
	switch ($imode) {
		case 'save' :
			$pures = trim ( $_POST ['filters'] );
			$purar = json_decode ( str_replace ( '\\"', '"', $pures ) );
			if (count ( $_SESSION ['query'] ) == 5) {
				$slpost = $_SESSION ['query'] ['posts'];
				$sstart = ( int ) $_SESSION ['query'] ['begin'];
				$send = ( int ) $_SESSION ['query'] ['end'];
				$svisit = $_SESSION ['query'] ['visits'];
				$utar = array ();
				foreach ( $purar as $pid => $par ) {
					if ($par->state) {
						$utar [$pid] = $par;
					}
				}
				if (count ( $utar ) > 0) {
					$wfils = true;
				} else {
					$wfils = false;
				}

				$sql = 'insert into queries (posts,qname,qdesc,sdate,edate,visits,fils,created)
				values ("' . mysql_real_escape_string ( serialize ( $slpost ) ) . '","' . $sname . '","' . $sdesc . '",
				"' . $sstart . '","' . $send . '","' . $svisit . '","' . mysql_real_escape_string ( serialize ( $utar ) ) . '",now())';
				db_exec ( $sql );

			} else {
				echo 'fail';
			}

			break;
		case 'edit':
			if($qsid > 0){
				$sql='update queries set qname="'.$sname.'", qdesc="'.$sdesc.'" where id="'.$qsid.'"';
				$res=mysql_query($sql);
				if(!$res){
					echo 'fail';
				}
			}
			break;
		case 'del':
			if($qsid > 0){
				$sql='delete from queries where id="'.$qsid.'"';
				$res=mysql_query($sql);
				if(!$res){
					echo 'fail';
				}
			}
			break;
		default :
			break;
	}
	echo json_encode ( getSaves () );
	return;

}
global $titles;
$tkeys = array_keys ( $titles );

$fieldsPath = $baseDir . '/modules/outputs/data';

$fielder = array ();
$ddir = opendir ( $fieldsPath );
if (is_dir ( $fieldsPath )) {
	if ($dh = opendir ( $fieldsPath )) {
		while ( ($file = readdir ( $dh )) !== false ) {
			if ($file != '.' && $file != '..') {
				$selects=array();
				$vname = findkey ( str_replace ( ".fields.php", "", $file ), $tkeys );
				if ($vname != '') {
					require $fieldsPath . '/' . $file;
					$fielder [str_replace ( ".fields.php", "", $file )] = array ('list' => new wallE ( $fields,$selects ), 'title' => $titles [$vname] ['title'] ,'visible'=>$partShow);
					unset ( $fields );
				}
			}
			unset ( $vname );
		}
		closedir ( $dh );
	}
}

if ($_GET['mode'] == 'patch'){
	$rx=(int)$_GET['x'];
	$ry=(int)$_GET['y'];
	$rv=mysql_real_escape_string(trim($_GET['val']));
	//$blist=$_SESSION['table']['body'];
	$fbf=$_SESSION['fileNameCsh'];
	$fbpath=$baseDir.'/files/tmp/'.$fbf.'.tbd';
	if(file_exists($fbpath)){
		$blist=unserialize(file_get_contents($fbpath));		
	}
	$rid=$blist[$ry]['id'];
	$rowh=$blist[$ry]['row'];
	preg_match_all("|<[^>]+>(.*)</[^>]+>|U",$rowh,$cells);//|<td>([^<].*)<\\/td>|U
	$cols=$_SESSION['query']['cols'];
	if($rx > 0){
		$ind=0;
		if(count($cells[0]) > 0){
			$nrow="<tr id='row_$ry'>";
			for($v=0;$v  <count($cells[0]);$v++){
				if($v != $rx){
					$nrow.=$cells[0][$v];
				}else{
					$nrow.='<td>'.$rv.'</td>';
				}
			}
			$nrow.='</tr>';
			$blist[$ry]['row']=$nrow;
			$_SESSION['table']['body']=$blist;
		}
	foreach ($cols as $key=> $db) {
		if($ind == $rx){
			$dbname=$titles[$db]['db'];
			$dbid=$titles[$db]['did'];
			$sql='update '.$dbname.' set '.$key.' = "'.$rv.'" where '.$dbid.' = "'.$rid.'"';
			$res=mysql_query($sql);
			if(mysql_affected_rows() == 1){
				echo "ok";
			}
			return ;
		}else{
			$ind++;
		}

	}
	}
}
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
$moduleScripts[]="./modules/outputs/outputs.module.js";
$moduleScripts[]="./modules/outputs/stats.js";
$moduleScripts[]="./modules/outputs/jquery-ui-1.7.2.custom.min.js";
if ($_SERVER ['CONTENT_LENGTH'] > 0 && count ( $_POST ) > 0) {
	require_once($baseDir . '/modules/outputs/result.func.php');
	$lpost = array ();
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
$htmlpre = '<form method="POST" action="?m=masteredit" id="sendAll" name="xform" >
	<input type="hidden" name="stype">
	<input type="hidden" name="pmode">
	<input type="hidden" name="faction">
';
$html = '<form method="POST" action="?m=masteredit" id="sendAll" name="xform" onsubmit="return false;">';
$mi = 0;
$block_count = 1;
ksort ( $fielder );

$html=buildForms(&$fielder);

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
/*  &nbsp;&nbsp;<input class="datepicker" id="start_date" name="beginner" disabled="disabled" value="' . $show_start . '">
			<a href="#" onclick="popCalendar(\'beginner\')">
			<img src="/images/calendar.png" width="16" height="16" alt="Calendar" 	border="0"></a>
			<input type="hidden" class="datepicker" name="filter_beginner" value="' . $starter . '" />
			&nbsp;<input class="datepicker" id="end_date" name="finisher" disabled="disabled" value="' . $show_end . '">
			<a href="#" onclick="popCalendar(\'finisher\');"><img src="/images/calendar.png" width="16" height="16" alt="Calendar" border="0"></a>
			<input type="hidden" class="datepicker" name="filter_finisher" value="' . $ender . '" />
			<input type="button" class="button" value="Clear dates" onclick="datesoff()">&nbsp;&nbsp;&nbsp;&nbsp;
			*/
$html = $htmlpre.'
<div style="float: none; margin: 10px;">&nbsp;&nbsp;&nbsp;
	Start &nbsp;'.drawDateCalendar('beginner',$show_start,false,'id="start_date"').'
	&nbsp;&nbsp;&nbsp;End '.drawDateCalendar('finisher',$show_end,false,'id="end_date"').' &nbsp;&nbsp;&nbsp;
	<label><input checked="checked" type="radio" name="dfilter" value="visit">Visit&nbsp;</label>&nbsp;&nbsp;
	<label><input type="radio" name="dfilter" value="doa">DOA&nbsp;</label><br>	
	<label ><input type="radio" name="vis_sel" value="all" id="allv" ' . $alltext . '>All visits</label> &nbsp;&nbsp;
	<label ><input type="radio" name="vis_sel" value="first" id="firstv" ' . $firsttext . '>First visit</label>
	<label ><input type="radio" name="vis_sel" value="last" id="lastv" ' . $lasttext . '>Last visit</label>
	<label ><input type="checkbox" name="cur_center" value="1" id="curcen" ' . $curcentext . '>Only this center</label>
	<label><input type="checkbox" name="actives" id="ashow" '.($uamode === false ? 'checked="checked"' : '' ).'>Active clients only</label><br>
	
	<div id="cboxes">
		<label><input type=checkbox onclick="markAll(this);">ALL</label><ul class="mflt">'.
		topRowFields($lpost).'<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.topRowFields($lpost,true).'		
		</ul>
	</div>
	<input type="hidden" name="qsid" value="">
	<br>

</div>
'.$html.'
<br><br>
<div style="width: 1000px;">
	<input type="button" value="Go" onclick="getData()" class="button">&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="button" value="Clear Forms" onclick="clearData()" id="fcleaner" class="button" '. (($tchex > 0) ? '': 'disabled="disabled"').'>
</div>
</form>';

echo $dst;
unset($dst);
cleanALoc(&$lpost);
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
echo '</p></div>';
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
<div id="filbox" style="position: absolute; display: none;"
	class="filter_box box1">
<div id="menu">
<ul id="toplevel">
	<li>
		<div class="sib asci"></div>
		<span class="fhref" onclick="gpgr.ifsort('desc');">Sort Asc</span></li>
	<li>
		<div class="sib desci"></div>
		<span class="fhref" onclick="gpgr.ifsort('asc');">Sort Desc</a>
	</li>
	<li>
		<div class="sib coli"></div>
		<span class="fhref" onclick="filmter.lects(this);">Values</span>
	</li>
	<li id="lbl">
	    <span class="fillink" onclick="filmter.showfils(this);">Filters</span>
    	    <div class="sib"><input type="checkbox" id="fil_on" data-area="" value="1" onchange="filmter.checkFilter(this);" disabled="disabled" class="superbox">
    	</div>
	</li>
</ul>
</div>
</div>
<div id="fil_list"  class="filter_box box2"></div>
<div id="filin_list"  class="filter_box box3"></div>
<div id="fil_stats" class="filter_box box4">
<ul class="tobs">
<li class="ffbb fil_line"><input type="checkbox" class="row_check">Add to rows area</li>
<li class="ffbb fil_line"><input type="checkbox" class="col_check">Add to columns area</li>
</ul>
</div>
<div id='stip'></div>
<script>
chex=<?php
	echo ($mi - 1);
	?>;
	rrr=<?php echo $lcrows;?>;
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
	
	window.onload=up;
	function up (){
		prePage();
	}


</script>
