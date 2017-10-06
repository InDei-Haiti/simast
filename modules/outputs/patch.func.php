<?php

function realRole($arr){
	if($arr[1] == 'caregiver'){
		return $arr[1].'_'.$arr[2];
	}else{
		return $arr[1];
	}
}

function cleanField($fld,&$cstaff){
	$fedit = str_replace ( array('social_','admission_'), '', $fld );
	foreach ( $cstaff as $vac ) {
		$fedit = str_replace ( $vac . '_', '', $fedit );
	}
	return $fedit;
}

function addCaregiver($clid,$role,$field,$val){
	$res=false;
	$zsql='insert into admission_caregivers (client_id,role,'.$field.') values("'.$clid.'","'.$role.'","'.$val.'")';
	$zres=mysql_query($zsql);
	if($zres){
		$res=mysql_insert_id();
	}
	return $res;
}

function proceedPatch() {
	global $titles;
	$resex='';
	$zpatches = array ('educ', 'health', 'marital' );
	$rx = ( int ) $_POST ['x'];
	$ry = ( int ) $_POST ['y'];
	$rv = mysql_real_escape_string ( trim ( $_POST ['val'] ) );
	$vv = trim ( $_POST ['vval'] );
	$fid = trim ( $_POST ['fid'] );
	$fld = trim ( $_POST ['fld'] );
	$did = trim ( $_POST ['did'] );
	$table = 'wform_'.$fid;
	$blist = getFileBody ( 'body' );
	$crow = unserialize ( $blist [$ry] );
	if (is_array ( $crow )) {
		$rid = $crow ['id'];
		$rowh = $crow ['row'];
		$clid = $crow ['client'];
		preg_match_all ( "|<[^>]+>(.*)</[^>]+>|U", $rowh, $cells ); //|<td>([^<].*)<\\/td>|U
		$cols = $_SESSION ['query'] ['cols'];
		/* echo 'avant';
		var_dump($crow); */
		if ($rx >= 0) {
			$ind = 0;
			reset ( $cols );
			if (count ( $cells [0] ) > 0) {
				$nrow = "<tr id='row_$ry'>";
				for($v = 0; $v < count ( $cells [0] ); $v ++) {
					if ($v != $rx) {
						//echo ' if ';
						if ($v == 0) {
							$trow = str_replace ( $nrow, '', $cells [0] [$v] );
						} else {
							$trow = $cells [0] [$v];
						}
						$nrow .= $trow;
					} else {
						//echo ' else '.$vv;
						$nrow .= '<td>' . $vv . '</td>';
						$field = key ( $cols );
						$pfl = explode ( '_', $field );
					}
					next ( $cols );
				}
				reset ( $cols );
				/* echo 'apres';
				var_dump($crow); */
				$nrow .= '</tr>';
				$crow ['row'] = $nrow;
				$blist [$ry] = serialize ( $crow ) . "\n";
				saveFileBody ( $blist );
			}
			$sql = 'update '.$table.' set '.$fld.' = "' . $rv . '" where id="' . $did . '"';
			$res = mysql_query ( $sql );
			if ($res === true) {
				/* if ($psql != '') {
					mysql_query ( $psql );
				}
				if ($psql2 != '') {
					mysql_query ( $psql2 );
				} */
				echo "ok";
			}
			return;
			
			
			/*
			$clientDeps = array ('client_dob' => array ('tbl' => 'counselling_info', 'client' => 'counselling_client_id', 'col' => 'counselling_dob' ), 'client_doa' => array ('tbl' => 'counselling_info', 'client' => 'counselling_client_id', 'col' => 'counselling_admission_date' ), 'client_gender' => array ('tbl' => 'counselling_info', 'client' => 'counselling_client_id', 'col' => 'counselling_gender' ) );

			foreach ( $cols as $key => $db ) {
				if ($ind == $rx) {
					$dbname = $titles [$db] ['db'];
					$dbid = $titles [$db] ['did'];
					$cstaff = array ('father', 'mother', 'caregiver_pri', 'caregiver_sec' );
					$cout = array ('raising', 'relationship' ); //'status',
					$careps = array ('pri', 'sec' );
					$rv = trim ( $rv );
					$key = preg_replace ( '/1$/', '', $key );
					if ($_POST ['vmd'] === 'plural' || (isset ( $titles [$db] ['plurals'] ) && array_key_exists ( $key, $titles [$db] ['plurals'] ))) {
						$qset=array();
						$pdd = $titles [$db] ['plurals'] [$key];
						$edata = json_decode ( str_replace ( '\\"', '"', $_POST ['val'] ), true );
						if (! $edata ['rindex']) {
							$edata ['rindex'] = $rid;
						}
						if (( int ) $edata ['rindex'] > 0) {
							if (strstr ( $pdd ['index'], 'client_id' )) {
								$edata ['rindex'] = $clid;
							}
							$endsql = '';
							$clientUse = false;
							if (isset ( $pdd ['client'] ) && strlen ( $pdd ['client'] ) > 0) {
								$clientUse = true;
							}
							if (! isset ( $pdd ['keep'] )) {
								//1. Delete old entries
								$sql = 'delete from ' . $pdd ['table'] . ' where ' . $pdd ['index'] . '="' . (( int ) $edata ['rindex']) . '"';
								$delr = mysql_query ( $sql );
								$isql = 'insert into ' . $pdd ['table'] . ' (' . $pdd ['index'] . ($clientUse ? ',' . $pdd ['client'] : '') . ',' . join ( ', ', $pdd ['fields'] ) . ') VALUES ';
							} else {
								$isql = 'update ' . $pdd ['table'] . ' set ';
							}
							//2.Insert into table new entries


							$frows = array ();
							if (! isset ( $edata ['dset'] ) && strlen ( $rv ) > 0) {
								$xvs = explode ( ',', $rv );
								for($ix = 0, $lx = count ( $xvs ); $ix < $lx; $ix ++) {
									$edata ['dset'] [$ix] = array ($pdd ['fields'] [0] => $xvs [$ix] );
								}
							}
							foreach ( $edata ['dset'] as $row ) {
								! isset ( $pdd ['keep'] ) ? $inrows = array ('"' . $edata ['rindex'] . '"' ) : '';
								if ($clientUse) {
									$inrows [] = '"' . $clid . '"';
								}
								foreach ( $pdd ['fields'] as $fld ) {
									if (isset ( $pdd ['pparser'] ) && is_array ( $pdd ['pparser'] ) && array_key_exists ( $fld, $pdd ['pparser'] )) {
										$exstr = str_replace ( '#XYZ#', json_encode ( $row ), $pdd ['pparser'] [$fld] );
										eval ( $exstr );
										$dval = $resex;
									} else if (isset ( $pdd ['eparser'] ) && is_array ( $pdd ['eparser'] ) && array_key_exists ( $fld, $pdd ['eparser'] )) {
										$exstr = str_replace ( '#XYZ#', $row [$fld], $pdd ['eparser'] [$fld] );
										eval ( $exstr );
										$dval = $resex;
									} else {
										$dval = $row [$fld];
									}
									$dval == 'null' ? $prepost = '' : $prepost = '"';
									if (! isset ( $pdd ['keep'] )) {
										$inrows [] = $prepost . mysql_real_escape_string ( $dval ) . $prepost;
									} else {
										$inrows [] = $fld . ' = ' . $prepost . mysql_real_escape_string ( $dval ) . $prepost;
									}
								}
								! isset ( $pdd ['keep'] ) ? $frows [] = '( ' . join ( ',', $inrows ) . ' )' : $frows [] = join ( ', ', $inrows );
								if(isset($pdd['keep'])){
									$endsql = ' where ' . (isset($pdd['use_form_index']) ? $pdd['use_form_index']  : $pdd ['index'] ) . '="' .
										(isset($pdd['use_form_index']) ? $row[$pdd['use_form_index']] :  $edata ['rindex'])
										. '"';
									if (count ( $frows ) > 0) {
										$qset[]=$isql.join ( ', ', $frows ) . $endsql;
										$inrows=$frows=array();
									}
								}
							}
							if (count ( $frows ) > 0) {
								$isql .= join ( ', ', $frows ) . $endsql;
							}
							if(!isset($pdd['keep'])){
								$qset=$isql;
							}
							$onceError=false;
							foreach ($qset as $qsql) {
								$res = mysql_query ( $qsql );
								if(!$res){
									$onceError=true;
								}
							}
							if($onceError === true){
								$res=false;
							}

						}
					} else {
						$chadd = '';
						if ($dbname === 'chw_info' && $key === 'chw_adm_no' && strlen ( $rv ) >= 1) {
							$ires = mysql_query ( 'select client_id from clients where client_adm_no="' . mysql_real_escape_string ( $rv ) . '"' );
							if ($ires && mysql_num_rows ( $ires ) == 1) {
								$rclid = mysql_fetch_array ( $ires );
								$chadd = ' , chw_client_id="' . $rclid [0] . '" ';
							}
						}
						$sql = 'update ' . $dbname . ' set ' . $key . ' = "' . $rv . '" ' . $chadd . ' where ' . $dbid . ' = "' . $rid . '"';

						if ($dbname != 'admission_info' && $dbname != 'social_visit' && ! in_array ( $pfl [2], $titles [$db] ['defered'] )) {
							if ($dbname === 'clients' && $crow ['client'] != $crow ['id']) {
								$sql = 'update ' . $dbname . ' set ' . $key . ' = "' . $rv . '" where ' . $dbid . ' = "' . $clid . '"';
								$rid = $crow ['client'];
								$zpos = array_search ( $key, array_keys ( $clientDeps ) );
								if (is_numeric ( $zpos ) && $zpos >= 0) {
									$smarr = $clientDeps [$key];
									$psql = 'update ' . $smarr ['tbl'] . ' set ' . $smarr ['col'] . ' = "' . $rv . '" where ' . $smarr ['client'] . ' = "' . $rid . '"';
									if ($key == 'client_doa') {
										$psql2 = 'update clients set client_entry_date=client_doa where client_id="' . $rid . '"';
									} else {
										$psql2 = '';
									}
								} else {
									$psql = '';
								}
							}
						} elseif ($dbname == 'admission_info') {
							if ($key == 'admission_location') {
								$sql = 'update ' . $dbname . ' set ' . $key . ' = "' . $rv . '" where admission_client_id = "' . $clid . '"';
							}
							if ($pfl [1] == 'caregiver') {
								$pfl [1] = $pfl [1] . '_' . $pfl [2];
								array_splice ( $pfl, 2, 1 );
							}
							if ((! in_array ( $pfl [2], $careps ) && in_array ( $pfl [2], $titles [$db] ['defered'] )) || (in_array ( $pfl [2], $careps ) && in_array ( $pfl [3], $titles [$db] ['defered'] ))) {
								if (in_array ( $pfl [1], $cstaff ) && ($pfl [2] != '' && ! in_array ( $pfl [2], $cout ) && ! in_array ( $pfl [3], $careps ))) {
									$sql1 = 'select admission_' . $pfl [1] . (in_array ( $pfl [2], $careps ) ? '_' . $pfl [2] : '') . ' as oid from admission_info where admission_id="' . $rid . '" limit 1';
									$res1 = mysql_query ( $sql1 );
									$fedit = cleanField ( $field, $cstaff );

									if ($res1) {
										$info = mysql_fetch_object ( $res1 );
										if ($info->oid > 0) {
											$sql = 'update admission_caregivers set ' . $fedit . ' = "' . $rv . '" where id="' . $info->oid . '"';
										} else {
											$ncrid = addCaregiver ( $clid, $pfl [1] == 'caregiver' ? $pfl [2] : $pfl [1], $fedit, $rv );
											if ($ncrid > 0) {
												$sql = 'update admission_info set admission_' . realRole ( $pfl ) . ' = "' . $ncrid . '" where admission_id="' . $rid . '"';
											}

										}
									}
								}
							} ///additional if, for filtering caregivers
						} elseif ($dbname == 'social_visit' && in_array ( $pfl [1] . '_' . $pfl [2], $cstaff ) && in_array ( $pfl [3], $titles [$db] ['defered'] ) && count ( $pfl ) < 6 && in_array ( $pfl [2], $careps )) {
							$cname = 'social_' . $pfl [1] . (in_array ( $pfl [2], $careps ) ? '_' . $pfl [2] : '');
							$sql1 = 'select  ' . $cname . ' as oid from social_visit where social_id="' . $rid . '"  and ' . $cname . ' is not null limit 1';
							$res1 = mysql_query ( $sql1 );
							$fedit = cleanField ( $key, $cstaff );
							if ($res1 && mysql_num_rows ( $res1 ) == 1) {
								$data = mysql_fetch_assoc ( $res1 );
								if (is_null ( $data ['oid'] ) || ( int ) $data ['oid'] == 0) {
									$sql2 = 'select id from admission_caregivers where client_id="' . $clid . '" and datesoff is null and role="' . $pfl ['2'] . '" limit 1';
									$dres = mysql_query ( $sql2 );
									if ($dres) {
										$data1 = mysql_fetch_assoc ( $dres );
										$data ['oid'] = $data ['id'];
									}
								}
							} else {
								$newid = addCaregiver ( $clid, $pfl [1] == 'caregiver' ? $pfl [2] : $pfl [1], $fedit, $rv );
								if ($newid > 0) {
									$sql = 'update social_visit set social_' . realRole ( $pfl ) . ' = "' . $newid . '" where social_id="' . $rid . '"';
								}
							}

							if ($data ['oid'] > 0) {
								//$info = mysql_fetch_object ( $res1 );
								$sql = 'update admission_caregivers set ' . $fedit . ' = "' . $rv . '" where id="' . $data ['oid'] . '"';
							}
						}
						$res = mysql_query ( $sql );
					}
					if ($res === true) {
						if ($psql != '') {
							mysql_query ( $psql );
						}
						if ($psql2 != '') {
							mysql_query ( $psql2 );
						}
						echo "ok";
					}
					return;
				} else {
					$ind ++;
				}

			}*/
		}
	}
}

function proceedQueryStuff(){

	$imode = trim ( $_POST ['imode'] );
	$sname = sqlstr( $_POST ['qname'] );
	$sdesc = sqlstr( $_POST ['qdesc'] );
	$qsid=(int)$_POST['sid'];
	$qrstype=strtolower($_POST['stype']);
	//check if we are dealing with report items
    //echo $qsid.' |||| '.$qrstype.' ||||';
	/*if(strstr($qrstype,'report')){
		if($imode === 'del'){
			$sql = 'delete from reports where id='.$qsid.'';
			$res = mysql_query($sql);
			echo $sql;
			echo $res === false ? 'fail' : 'ok';
		}
		return;
	}*/
	//echo $imode.' ||||';
	switch (trim($imode)) {	
		case 'save' :
			$pures = trim ( $_POST ['filters'] );
			$purar = json_decode ( stripslashes($pures ) );
			if (count ( $_SESSION ['query'] ) > 5) {
				$slpost = $_SESSION ['query'] ['posts'];
				$sstart =  str_replace('-','',$_SESSION ['query'] ['begin']);
				$send = str_replace('-','',$_SESSION ['query'] ['end']);
				$svisit = $_SESSION ['query'] ['visits'];
				$sdfil=$_SESSION['dfilter'];
				$scentre=$_SESSION['query']['center'];
				$actvs=bool2bit($_POST['actvs']);
				$lvdo = serialize($_SESSION['query']['lvd']);
				$utar = array ();
				foreach ( $purar as $pid => $par ) {
					if ($par->state === true) {
						$utar [$pid] = $par;
					}
				}
				if (count ( $utar ) > 0) {
					$wfils = true;
				} else {
					$wfils = false;
				}

				$sql = 'insert into queries (posts,qname,qdesc,sdate,edate,visits,fils,created,actives,dfilter,center,lvdopt)
				values ("' . mysql_real_escape_string ( serialize ( $slpost ) ) . '","' . mysql_real_escape_string($sname) . '","' .
				 mysql_real_escape_string($sdesc) . '",	"' . mysql_real_escape_string($sstart) . '","' .
				  mysql_real_escape_string($send) . '","' . mysql_real_escape_string($svisit) . '","' .
				  mysql_real_escape_string ( serialize ( $utar ) ) . '",now(),"'.mysql_real_escape_string($actvs).'","'.
				  mysql_real_escape_string($sdfil).'","'.mysql_real_escape_string($scentre).'",
				  "'.mysql_real_escape_string($lvdo).'")';
				$res=mysql_query( $sql );
				if($res){
					$nid=mysql_insert_id();
					echo $nid;
				}else{
					echo 'fail';
				}
			} else {
				echo 'fail';
			}

			break;
		case 'edit':
			if($qsid > 0){
				$sstart =  DatetoInt($_POST ['sdate']);
				$send = DatetoInt($_POST ['edate']);
				if($qrstype == 'table'){
					$sql='update queries set qname="'.$sname.'", qdesc="'.$sdesc.'",sdate="'.$sstart.'",edate="'.$send.'" where id="'.$qsid.'"';
					$res=mysql_query($sql);
				}elseif ($qrstype == 'stats'){
					$showr=$_POST['showr'];
					$sql='update stat_queries set qname="'.$sname.'", qdesc="'.$sdesc.'",sdate="'.$sstart.'",edate="'.$send.'",show_result="'.bool2bit($showr).'" where id="'.$qsid.'"';
					$res=mysql_query($sql);
				}
				if(!$res){
					echo 'fail';
				}else{
					echo 'ok';
				}
			}
			break;
		case 'del':


			if($qsid > 0){
				if($qrstype === 'table'){
					$sql='delete from queries where id="'.$qsid.'" limit 1';
					echo $sql;
					$res=db_exec($sql);
				}elseif ($qrstype === 'stats' || $qrstype === 'chart'){
					$q = new DBQuery();
					$q->addQuery('qs.id,qs.visible');
					$q->addTable('stat_queries','sqs');
					$q->addJoin('queries','qs','sqs.query_id=qs.id');
					$q->addWhere('sqs.id="'.$qsid.'"');
					$bqid=$q->loadList();
					$bqid=$bqid[0];
					if($bqid['visible'] == '0'){
						$sql='delete from queries where id='.$bqid['id'].' limit 1';
						$res = db_exec($sql);
					}
					$sql='delete from stat_queries where id="'.$qsid.'" limit 1';
					$res = db_exec($sql);
				}elseif($qrstype === 'report'){
					if(is_numeric($qsid)){
						$sql='delete from reports where id='.$qsid.' LIMIT 1';
						$res=db_exec($sql);
					}
				}elseif($qrstype === 'report item'){
                    if(is_numeric($qsid)){
                        $sql='delete from report_items where id='.$qsid.' LIMIT 1';
                        $res=db_exec($sql);
                    }
                }


				if(!$res){
					echo 'fail';
				}else{
					echo 'ok';
				}
			}
			break;
		default :
			break;
	}
	//echo json_encode ( getSaves () );
	return;
}

function exportResultExcel($returnPath = false){
	global $baseDir,$headerCache,$msheet,$xlRow,$bigparts,$defered,$pluralKeys;
	$headerCache = array();
	$defered=array();
	$pluralKeys = array();
	require_once( "$baseDir/lib/Spreadsheet/Excel/Writer.php" ) ;

	$xlRow=0;
	$after = '';
	$bigparts=array();
	$mwxl= new Spreadsheet_Excel_Writer();
	$msheet = $mwxl->addWorksheet("table");
	$format_bold =& $mwxl->addFormat();
	$format_bold->setBold();
	$dlist=magic_json_decode( trim($_POST['list']));
	
	$fip=$_SESSION ['fileNameCsh'];
	if ($fip  != '' && file_exists($baseDir.'/files/tmp/'.$fip.'.tst')) {
		$fpath=$baseDir.'/files/tmp/'.$fip.'.tst';
		$bar=unserialize(file_get_contents($fpath));		
		$allKeys = array_keys($bar['list']);
		if(is_array($dlist)){
			$rowRules = $dlist;
			if($rowRules[1] === 'hidden'){
				if(count($rowRules[0]) == 0){
					$tdl= $allKeys;
				}else{
					$tdl = array_values (array_diff($allKeys, $rowRules[0]));
				}
			}elseif ($rowRules[1] === 'visible'){
				$tdl = $rowRules[0];
			}
		}
		unset($bar,$rowRules,$allKeys);
			
	}
	
	/*if(strlen($dlist) > 1){
		$tdl=json_decode(str_replace ( '\\"', '"', $dlist));
	}*/
	$table=$_SESSION['table']['head'];
	//$blist=$_SESSION['table']['body'];
	$fsname=str_replace(' ','_',trim($_POST['fname']));
	if($fsname == ''){
		$fsname='table';
	}
	if(!preg_match("/\.xls$/",$fsname)){
		$fsname.='.xls';
	}
	$table='<table border="1">'.$table.'<tbody>';
	$mylen=0;
	if(count($tdl) > 0){
		$fname=$_SESSION['fileNameCsh'];
		$fip=$baseDir.'/files/tmp/'.$fname;
		$fpath=$fip.'.tbd';

		$polys=&$_SESSION['query']['polys']['marker'];
		$sels=&$_SESSION['query']['polys']['values'];
		$table=str_replace(array('\n','\t'),"",$table);
		$plurs = &$_SESSION['query']['polys']['plurs'];

		//$mylen+=(strlen($ps)+ strlen($after));
		$tvar = false;
		//if(!$returnPath)
		printForSave($tvar,'application/vnd.ms-excel',$fsname,true,true);
		//$after = $fsname;
		//echo $ps;
		$fpart=false;

		if(strlen($fpath)  > 0 && file_exists($fpath)){
			$fh=fopen($fpath,"r");
			$fhf=fopen($fip.'.tch','r');
			$tab_head='';
			$begin=false;
			$enough = false;

			//echo $tab_head."\n";

			//$xlRow=0;
          	++$xlRow;
			$vrow=0;
			rewind($fh);
			while($fstr = fgets($fh)) {
				//$buffer = fread($fh, 2048);
				$far=unserialize($fstr);
				$buffer=$far['row'];
				if(strstr($buffer,'moreview')){
					preg_match_all("/data-text='([^']*)'[^>]*>([^<]*\.{3})<\/td>/",$buffer,$rtext);
					if(count($rtext) == 3){
						if(count($rtext[2] ) > 0) {
							foreach($rtext[2] as $key => $val){
								$buffer=str_replace($val,$rtext[1][$key],$buffer);
							}
						}
					}
				}
				if((count($tdl) > 0 && in_array($vrow,$tdl)) || count($tdl) == 0){
					xlsPure($buffer,true);
				}
				++$vrow;
			}

			$xlRow=0;


			while(!$enough){
				if(!isset($headerCache[$xlRow])){
					$headerCache[$xlRow]=array();
				}
				$fstr=fgets($fhf);
				if($begin === false){
					if(strstr($fstr,'<thead>')){
						$buar=explode('<thead>',$fstr);
						$tab_head=xlsPure($buar[1]);
						$begin = true;
					}
				}else{
					if(strstr($fstr,'</thead>')){
						$buar=explode('</thead>',$fstr);
						$tab_head.=xlsPure($buar[0]);
						$tab_head=preg_replace("/\@#@,@#@$/",'',$tab_head);
						$enough=true;
					}else{
						if(strstr($fstr,"</tr>")){
							$tab_head.="\n";
							$tab_head=preg_replace("/\@#@,@#@$/",'',$tab_head);
							$cells=explode('@#@,@#@',$tab_head);
							$key=0;
							foreach ($cells as $clean_key => $cell){
								if($polys[$clean_key] !== false && !is_null($polys[$clean_key]) && is_array($sels[$clean_key])){
									for($icell =0, $il = count($sels[$clean_key]); $icell < $il; $icell++){
										$msheet->write($xlRow,$key++,$cell.' - '.$sels[$clean_key][$icell]['v'],$format_bold);
									}
								}elseif (!is_null($plurs[$clean_key]) && $plurs[$clean_key]!== false){
									$pluralKeys[]=$clean_key;
									$vcell = uberAcro($cell);
									$showntimes = ($bigparts[$clean_key] > 1 ? $bigparts[$clean_key] : 1);									
									for ($i=0;$i < $showntimes; $i++){
										foreach ($plurs[$clean_key]['header'] as $hid => $hname){
											if($plurs[$clean_key]['visibility'][$hid] === true){
												$msheet->write($xlRow,$key++,$vcell.'-'.$hname.' '.($i+1),$format_bold);
											}
										}
									}
								}else {
									$msheet->write($xlRow,$key++,$cell,$format_bold);
								}
							}
							++$xlRow;
							$tab_head='';
						}
						$tab_head.=xlsPure($fstr);

					}
				}


			}
			fclose($fhf);

			$cells=explode('@#@,@#@',$tab_head);
			$plurals_add=array();
			$key=0;
			$borders =array();
			foreach ($cells as $clean_key => $cell){
				if($polys[$clean_key] !== false){
					for($icell =0, $il = count($sels[$clean_key]); $icell < $il; $icell++){
            			$msheet->write($xlRow,$key++,$cell,$format_bold);
					}
				}elseif ($plurs[$clean_key] !== false && !is_null($plurs[$clean_key])){
					//$key=$borders[($clean_key-1)];
					$showntimes = ($bigparts[$clean_key] > 1 ? $bigparts[$clean_key] : 1);
					for ($i=0;$i < $showntimes;$i++){
						for ($c=0,$l = count($plurs[$clean_key]['header']); $c < $l; $c++){
							if($plurs[$clean_key]['visibility'][$c] === true){
								$msheet->write($xlRow,$key++,$cell,$format_bold);
							}
						}
					}
				}else {
					$msheet->write($xlRow,$key++,$cell,$format_bold);					
				}
				$borders[$clean_key]=$key;
			}

			if(count($defered) > 0){
				foreach ($defered as $xrow => $vals) {

					foreach ($cells as $clean_key => $ddd) {
						if(array_key_exists($clean_key,$vals)){
							$xkey=$borders[($clean_key-1)];

							$toPrint =&$defered[$xrow][$clean_key];
							if(is_array($toPrint)){
								foreach ($toPrint as $tpv) {
									$msheet->write($xrow,$xkey++,$tpv);
								}
							}else{
								$msheet->write($xrow,$xkey++,$toPrint);
							}
						}
					}
				}
			}

          	fclose ($fh);

          	$mwxl->close();
			flush_buffers();
		}
		echo $after;
	}
	return ;
}

function xlsPure($str,$rbody=false){
	global $msheet,$xlRow,$_SESSION,$headerCache,$bigparts,$defered,$pluralKeys;
	$polys=&$_SESSION['query']['polys']['marker'];
	$sels=&$_SESSION['query']['polys']['values'];
	$plurs=&$_SESSION['query']['polys']['plurs'];

	$objs=array(",","\t","\n","<\tr>","</td>","</th>","&nbsp;");
	$ress=array( " ","","" , '' , "@#@,@#@","@#@,@#@",'');
	$str=str_replace($objs,$ress,$str);
	$objs=array("/<tr[^>]*>/","/<th[^>]*>/",'/<td\s{0,}data-text="([^"]*)"[^@]*/'); //,"/<td[^>]*>/"
	$ress=array( "" , "",'$1');
	$str=preg_replace($objs,$ress,$str);

	$str=strip_tags($str);
	$key=0;
	$pluralsMet =false;
	if($rbody === true){
		$str=preg_replace("/\@#@,@#@$/",'',$str);
		$cells=explode('@#@,@#@',$str);
		foreach ($cells as $clean_key => &$cell){
			$cell = htmlspecialchars_decode($cell);
            if($polys[$clean_key]!== false && !is_null($polys[$clean_key])){
            	for($icell =0, $il = count($sels[$clean_key]); $icell < $il; $icell++){
            		$vtext = 'No';
            		if(is_array($sels[$clean_key]) && is_array($sels[$clean_key][$icell]) 
            			&& array_key_exists('v',$sels[$clean_key][$icell]) && strstr($cell,$sels[$clean_key][$icell]['v'])){
            			$vtext = 'Yes';
            		}
            		//$vtext=(strstr($cell,$sels[$clean_key][$icell]['v']) ? 'Yes' : 'No' );
            		if($pluralsMet === false){
            			$msheet->write($xlRow,$key++,$vtext);
            		}else{
            			if(!is_array($defered[$xlRow][$clean_key])){
            				$defered[$xlRow][$clean_key]=array();
            			}
            			$defered[$xlRow][$clean_key][]= $vtext;
            		}
            	}
            }elseif (!is_null($plurs[$clean_key]) && $plurs[$clean_key] !== false ){
            	if( strlen($cell) > 1){
            		$prows=explode(';',$cell);
            		if(count($prows) > 0 ){
            			if(count($prows) > $bigparts[$clean_key]){
            				$bigparts[$clean_key]=count($prows);
            			}
            			//$xstart=$key;
            			if(!is_array($defered[$xlRow][$clean_key])){
            				$defered[$xlRow][$clean_key]=array();
            			}
            			foreach ($prows as $omrow) {
            				$pcells = explode('|',$omrow);
            				foreach ($pcells as $tcell) {
            					if($pluralsMet === false){
            						$msheet->write($xlRow,$key++,$tcell);
            					}else{
            						$defered[$xlRow][$clean_key][]=$tcell;
            					}
            				}
            			}
            		}
            	}
            	$pluralsMet = true;
            }else{
            	if($pluralsMet === false){
            		$msheet->write($xlRow,$key++,$cell);
            	}else{
            		$defered[$xlRow][$clean_key]=$cell;
            	}
            }
		}
		++$xlRow;
	}
	return $str;
}

function uberAcro($text){
	$bparts=explode('.',$text);
	$prefix=$bparts[0].'.';
	$words=explode(" ",$bparts[1]);

	foreach ($words as $word) {
		$prefix.=strtoupper($word{0});
	}
	return $prefix;
}
?>