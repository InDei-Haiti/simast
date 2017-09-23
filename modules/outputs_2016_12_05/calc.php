<?php
global $baseDir;
if($_POST['mode'] == 'query'){
	$dquery=array();
	$qid=(int)$_POST['qrid'];
	$statd = trim ( $_POST ['setsd'] );
	//$statd = json_decode ( stripslashes( $statd ) ,true);
	$statd = magic_json_decode($statd,true);
	$pures = trim ( $_POST ['filters'] );
	//$purar = json_decode ( stripslashes($pures ));
	$purar = magic_json_decode($pures);
	if($qid > 0 && count($purar) > 0){
		// prepare set of filters and compare them with rules in already saved query.
		// in case when sqved query is sans those rules, then save new query and attach it to stat query
		$utar = array ();
		foreach ( $purar as $pid => $par ) {
			if ($par['state'] === true) {
				$utar [$pid] = $par;
			}
		}
		if (count ( $utar ) > 0) {
			$wfils = true;
		} else {
			$wfils = false;
		}
		if($wfils === true){
			$sql = 'select 1 from queries where id="'.$qid.'" and dfils = "'.mysql_real_escape_string ( serialize ( $utar ) ).'"';
			$eres=mysql_query($sql);
			if(mysql_num_rows($eres) === 0){
				$qid = 0;
			}
		}
	}
	if($qid == 0){
		if (count ( $_SESSION ['query'] ) > 4) {
			$dquery['posts'] =  $_SESSION ['query'] ['posts'];
			$dquery['sdate'] = DatetoInt( $_SESSION ['query'] ['begin']);
			$dquery['edate'] = DatetoInt( $_SESSION ['query'] ['end']);
			$dquery['visits'] = $_SESSION ['query'] ['visits'];
			$dquery['dfilter'] = $_SESSION ['query'] ['dfilter'];
			$dquery['center'] = $_SESSION ['query'] ['center'];
			$dquery['actives'] = bool2bit(!$_SESSION ['query'] ['actives']);
			$dquery['visible'] = 0;
			$dquery['lvdopt'] = $_SESSION['query']['lvd'];

			$dquery['fils']=$utar;

			$qid=ExIm::intoQueries($dquery);
		}
	}elseif ($qid > 0){
		$sql ='select sdate,edate from queries where id='.$qid;
		$res=mysql_query($sql);
		if($res){
			$r=mysql_fetch_object($res);
			$dquery['sdate']=$r->sdate;
			$dquery['edate']=$r->edate;
		}
	}
	foreach ($statd as $sk=>$sv) {
		if(!is_bool($sv)){
			if(is_array($sv)){
				$sv=serialize($sv);
			}
		}else{
			$sv=bool2bit($sv);
			//$statd[$sk]=$sv;
		}
		$statd[$sk]=mysql_real_escape_string($sv);
	}

	if(isset($_POST['legacy']) && (int)$_POST['legacy'] > 0){
		$sql='select * from stat_queries  where id="'.(int)$_POST['legacy'].'"';
		$res=mysql_query($sql);
		if($res){
			$statd=mysql_fetch_assoc($res);
			$dquery['sdate']=$statd['sdate'];
			$dquery['edate']=$statd['edate'];
		}
	}else{
		$statd['turns'] = serialize(
			array(
				'sunqs'=>$statd['sunqs'],
				'sblanks'=>$statd['sblanks'],
				'stots_cols'=>$statd['stots_cols'],
				'stots_rows'=>$statd['stots_rows'],
				'sperc_cols'=>$statd['sperc_cols'],
				'sperc_rows'=>$statd['sperc_rows'],
				'delta_count'=>$statd['delta_count'],
				'records'=>$statd['records'],
			)
		);
		$statd['show_result']=$statd['brest'];
		$statd['query_id']=$qid;
		$statd['ranges']=$statd['range'];
	}
	$statd['qname']=trim($_POST['qname']);
	$statd['qdesc']=trim($_POST['qdesc']);

	unset($statd['range'],$statd['brest']);
	if(isset($_POST['graph_data']) && strlen($_POST['graph_data']) > 0){
		$statd['qmode']='graph';
		//$statd['chart_data']=serialize(json_decode(stripslashes($_POST['graph_data']),true));
		$statd['chart_data']=serialize(magic_json_decode($_POST['graph_data'],true));
	}else{
		$statd['qmode']='stat';
		$statd['chart_data']='';
	}

	$nqid=ExIm::intoStats($statd,$dquery);
	if($nqid > 0 && $qid > 0){
		echo $nqid;
	}else{
		echo "fail";
	}
	return ;

}elseif($_POST['mode']=='save'){
	$dlist=trim($_POST['list']);
	if(strlen($dlist) > 1){
		//$tdl=json_decode(stripslashes($dlist));
		$tdl = magic_json_decode($dlist);
	}
	// import stat table from file
	$table =  file_get_contents($baseDir.'/files/tmp/'.$_SESSION['fileNameCsh'].'.tss');
	if(strlen($table) > 1){
		$table=str_replace('\n',"",$table);
		$table=str_replace('\t',"",$table);
		$ps=stripslashes($table);
		$ps=str_replace('border="0"','border="1"',$ps);
		printForSave($ps,'application/vnd.ms-excel','stat-table.xls');
	}
	return ;
}
elseif($_POST['mode'] == 'btable' && trim($_POST['calcs']) != ''){
	require_once('stater.class.php');
	//$cl=preg_replace('/\\\{1,}"/','"',$_POST['calcs']);
	//$svals=json_decode(stripslashes($_POST['calcs']),true);
	$svals = magic_json_decode($_POST['calcs'],true);
	/*if(is_null($svals)){
		$svals=json_decode($_POST['calcs'],true);
		
	}*/
	var_dump($_POST['calcs']);
	$fip=$_SESSION ['fileNameCsh'];
	if ($fip  != '' && file_exists($baseDir.'/files/tmp/'.$fip.'.tst')) {
		$fpath=$baseDir.'/files/tmp/'.$fip.'.tst';
		$bar=unserialize(file_get_contents($fpath));
		@unlink($baseDir.'/files/tmp/'.$fip.'.tss');
		$allKeys = array_keys($bar['list']);
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
		/* echo '<pre>';
		var_dump($bar);
		echo '</pre>'; */
		makeStat($bar,$svals);

		//DiskStatCache($thtml);
		//echo $thtml;
		$fps=$baseDir.'/files/tmp/'.$fip.'.tss';
		$sfh=fopen($fps,'r');
		fpassthru($sfh);
		fclose($sfh);
		//unset($thtml);
		return;
	}
	//for numeric ranges start <= val && end > val
}
?>