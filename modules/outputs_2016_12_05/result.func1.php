<?php

function downlow($s)
{
	return strtolower($s);
}


function admFirst($arr,$onLVDs) {
	$pos = array_search ( 'client_adm_no', $arr );
	if ($pos > 0) {
		array_splice ( $arr, $pos, 1 );
		//$arr = array_merge ( array ('client_adm_no' ), $arr );
        array_unshift($arr,'client_adm_no');
	}
	if($onLVDs > 0){
		$arr[]='client_lvd_form';
		$arr[]='client_lvd';
	}
	return $arr;
}


function resultBuilder($qmode) {
	global $l, $f, $h, $u, $s, $r, $m1, $staterd, $final, $nfei, $y, $tab_src, $e, $p, $html, $rhtml, $fielder,
	$titles, $tkeys, $bigtar, $rqid, $lpost, $bigtar_cnt, $bigtar_keys, $starter, $ender, $show_start, $show_end,
	$sels, $clients, $uamode, $colsConst, $clients_cnt,$vis_mode,$thisCenter,$statusHistory,$rl,$preFils,$lvder,$lvd_sel,
	$dPconfig;

	$lvdForm = false;

	if (! $e || ! is_array ( $e )) {
		$e = array ();
	}
	/* foreach ($_POST as $ids => $val){
		echo $ids."=>";
		print_r($val);
		echo '<br/><br/>';
	} */
	if (( int ) $_POST ['qsid'] > 0 ) {
		$quid = ( int ) $_POST ['qsid'];
		$spost = getSaves ( $quid, $_POST ['stype'] );
		$vis_mode = $spost [0] ['visits'];
		$lpost = $spost [0] ['posts'];
		if(strlen($spost [0] ['sdate']) >= 8 ){
			$starter = prepareDate($spost [0] ['sdate']);
		}
		if(strlen($spost [0] ['edate']) >= 8){
			$ender = prepareDate($spost [0] ['edate']);
		}
		$dblvd=unserialize($spost[0]['lvdopt']);
		$lvd_sel = $dblvd[1];
		$lvder = $dblvd[0];
		$filters = $spost [0] ['fils'];
		$dfil = $spost[0]['dfilter'];
		$use_center= $spost[0]['center'];
		$spost [0] ['actives'] == 1 ? $uamode = false : $uamode = true;
		if (is_null($spost [0] ['id'])) {
			return false;
		}
		if ($_POST ['stype'] == 'Stats') {
			$rqid = $spost [0] ['id'];
		} else {
			$rqid = $quid;
		}
		if ($_POST ['beginner'] != '0' && strlen ( $_POST ['beginner'] ) > 4) {
			$starter = prepareDate( $_POST ['beginner'] );
		}
		if ($_POST ['finisher'] != '0' && strlen ( $_POST ['finisher'] ) > 4) {
			$ender = prepareDate($_POST ['finisher'] );
		}
		if($_POST['dfilter'] != '' ){
			$dfil=$_POST['dfilter'];
			if(!in_array($dfil,array('visit','doa'))){
				$dfil='visit';
			}
		}

	} else {
		if (in_array($_POST ['vis_sel'], array('last','all','first'))) {
			$vis_mode = $_POST ['vis_sel'];
		} else {
			$vis_mode = '';
		}
		if(!is_array($tkeys)){
			$tkeys = array();
		}
		foreach ( $_POST as $pkey => &$pval ) {
			if (in_array ( $pkey, $tkeys ) || $pkey == 'extra') {
				$lpost [$pkey] = $pval;
			}
		}
		if(isset($_POST['sta_his']) && (int)$_POST['sta_his']){
			$statusHistory=TRUE;
			if(!isset($_POST['clients'])){
				$_POST['clients']=array('client_id');
			}
		}
		if (isset($_POST['beginner']) && $_POST ['beginner'] != "" && $_POST ['beginner'] != '0' ) {
			$tdd = new CDate ( $_POST ['beginner'] ); // ( int ) $_POST ['filter_beginner'];
			$starter = $tdd->format ( FMT_DATE_MYSQL );
			unset ( $tdd );
		}
		if (isset($_POST['finisher']) &&  $_POST ['finisher'] != '' && $_POST ['finisher'] != '0') {
			$tdd = new CDate ( $_POST ['finisher'] ); //( int ) $_POST ['filter_finisher'];
			$ender = $tdd->format ( FMT_DATE_MYSQL );
			unset ( $tdd );
		}
		if($_POST['dfilter'] != '' ){
			$dfil=$_POST['dfilter'];
			if(!in_array($dfil,array('visit','doa'))){
				$dfil='visit';
			}
		}
		if(isset($_POST['lvd_cmp_mode']) && strlen($_POST['lvd_cmp_mode']) === 2) {
			$lvd_sel = $_POST['lvd_cmp_mode'];
			if(isset($_POST['lvd_date'])){
				$td = new CDate($_POST['lvd_date']);
				$lvder = $td->format(FMT_DATE_MYSQL);
				unset($td);
			}
		}
		if(isset($_POST['cur_center']) && $_POST['cur_center'] == 1){
			$use_center=1;
		}else{
			$use_center=0;
		}
		if (isset ( $_POST ['actives'] ) && $_POST ['actives'] == 'on') {
			$uamode = false;
		} else {
			$uamode = true;
		}
		//uamode means user any mode - ie without active filter
	}


	$lvd_sql = '';
	$lvdSQLCode = '';

	if($lvd_sel === 'gt'){
		$lvd_sql=' > ';
	}elseif ($lvd_sel === 'lt'){
		$lvd_sql = ' < ';
	}elseif ($lvd_sel === 'eq'){
		$lvd_sql = ' = ';
	}
	if($lvd_sql != ''){
		$lvdSQLCode = 'clients.client_lvd '.$lvd_sql.' "'.$lvder.'"';
	}

	$preFils=array(); // storage of prefiltered values - this is required for report work
	$locCase = false;
	$locCaseOne = false;
	if (array_key_exists ( 'admission', $lpost )) {
		if (in_array ( 'admission_location', $lpost ['admission'] )) {
			$locCase = true;
			if (count ( $lpost ['admission'] ) == 1) {
				$locCaseOne = true;
			}
		}
	}

	$rforms=array();
	foreach ($lpost as $tform => $tdata) {
		if(isset($titles[$tform]['form_type']) && $titles[$tform]['form_type'] === 'registry'){
			$rforms[]=$tform;
		}
	}

	$thisCenter=false;
	if($use_center == 1){
		$thisCenter=getThisCenter();
	}

	$header = array (0 => array (), 1 => array (), 2 => array () );
	$edcref = array ();
	$clientCase = false;
	/*dbHistory::setViewMode($uamode);
	dbHistory::types();*/
	//$sels= array('0'=>'plain','1'=>'plain');
	//$columns=array('client_id'=>'clients','client_adm_no'=>'clients');
	$decoder = array ();
	$sels = $columns = array ();
	$q = new DBQuery ();
	$activCase=false;

	$excludes = array('client_adm_no','client_name','client_lvd_form');

	if (array_key_exists ( 'activity', $lpost )) {
		$activCase=TRUE;
		$delayed = array ();
		$q->addQuery ( 'activity_id as rid ,activity_date as edate' );
		$tia = $titles ['activity'];
		$fia = $fielder ['activity'];
		$header [0] = array_merge ( $header [0], getTitles ( $lpost ['activity'], $fia ['list']->getList () ) );
		//$header [0] = my_array_merge ( $header [0], getTitles ( $lpost ['activity'], $fia ['list']->getList () ) );
		$q->addTable ( 'activity' );
		if ($starter != '' && strlen ( $starter ) == 10) {
			$q->addWhere ( 'activity_date >= "' . $starter . '"' );
		}
		if ($ender != '' && strlen ( $ender ) == 10) {
			$q->addWhere ( 'activity_date <= "' . $ender . '"' );
		}
		if($thisCenter !== FALSE){
			$q->addWhere('activity_clinic="'.$thisCenter.'"');
		}
		foreach ( $lpost ['activity'] as &$lf ) {
			$header [1] [] = array ('v' => 'Activities', 'r' => 'ACT' );
			$header [2] [] = $titles ['activity'] ['db'];
			$decoder [$lf] = 'activity';
			$sels [] = $fielder ['activity'] ['list']->getSelects ( $lf );
			$columns [$lf] = 'activity';
			if ($fia ['list']->instant ($lf) && !array_key_exists($lf, $tia['plurals'])) {
				$q->addQuery ( $lf );
			} else {
				$delayed [] = $lf;
			}
		}
		$bigtar = $q->loadList ();
		if(count($bigtar) > 0){
			$marker=true;
			$uamode=true;
		}

	}elseif (array_key_exists('chwcheck', $lpost)){
		$delayed = array ();
		$q->addQuery ( 'chw_id as rid ,chw_entry_date as edate' );
		$tia = $titles ['chwcheck'];
		$fia = $fielder ['chwcheck'];
		$header [0] = array_merge ( $header [0], getTitles ( $lpost ['chwcheck'], $fia ['list']->getList () ) );
		//$header [0] = my_array_merge ( $header [0], getTitles ( $lpost ['chwcheck'], $fia ['list']->getList () ) );
		$q->addTable ( 'chw_info' );
		if ($starter != '' && strlen ( $starter ) == 10) {
			$q->addWhere ( 'chw_entry_date >= "' . $starter . '"' );
		}
		if ($ender != '' && strlen ( $ender ) == 10) {
			$q->addWhere ( 'chw_entry_date <= "' . $ender . '"' );
		}
		if($thisCenter !== FALSE){
			$q->addWhere('chw_center_id="'.$thisCenter.'"');
		}
		foreach ( $lpost ['chwcheck'] as &$lf ) {
			$header [1] [] = array ('v' => 'CHW', 'r' => 'CHW' );
			$header [2] [] = 'chwcheck';//$titles ['chwcheck'] ['db'];
			$decoder [$lf] = 'chwcheck';
			$sels [] = $fielder ['chwcheck'] ['list']->getSelects ( $lf );
			$columns [$lf] = 'chwcheck';
			if ($fia ['list']->instant ($lf) && !array_key_exists($lf, $tia['plurals'])) {
				$q->addQuery ( $lf );
			} else {
				$delayed [] = $lf;
			}
		}
		$bigtar = $q->loadList ();
		if(count($bigtar) > 0){
			$marker=true;
			$uamode = true;
		}
	}elseif(count($rforms) > 0){
		$delayed = array ();
		$q->addQuery ( 'id as rid ,entry_date as edate' );
		$uwform=$rforms[0];
		$tia = $titles [$uwform];
		$fia = $fielder [$uwform];
		$header [0] = array_merge ( $header [0], getTitles ( $lpost [$uwform], $fia ['list']->getList () ) );
		//$header [0] = my_array_merge ( $header [0], getTitles ( $lpost ['chwcheck'], $fia ['list']->getList () ) );
		$q->addTable ( $tia['db'] );
		if ($starter != '' && strlen ( $starter ) == 10) {
			$q->addWhere ( 'entry_date >= "' . $starter . '"' );
		}
		if ($ender != '' && strlen ( $ender ) == 10) {
			$q->addWhere ( 'entry_date <= "' . $ender . '"' );
		}
		/*if($thisCenter !== FALSE){
			$q->addWhere('chw_center_id="'.$thisCenter.'"');
		}*/
		foreach ( $lpost [$uwform] as &$lf ) {
			$header [1] [] = array ('v' => $tia['abbr'], 'r' => $tia['abbr'] );
			$header [2] [] = $uwform;
			$decoder [$lf] = $uwform;
			$sels [] = $fielder [$uwform] ['list']->getSelects ( $lf );
			$columns [$lf] = $uwform;
			if ($fia ['list']->instant ($lf) && !array_key_exists($lf, $tia['plurals'])) {
				$q->addQuery ( $lf );
			} else {
				$delayed [] = $lf;
			}
		}
		$bigtar = $q->loadList ();
		if(count($bigtar) > 0){
			$marker=true;
			$uamode = true;
		}
	}else {
		//$q->addQuery ( "client_id,client_adm_no" );
		$q->addQuery ( "client_id" );
		$q->addTable ( 'clients' );
		if($thisCenter !== false){
			$q->addWhere('client_center = "'.$thisCenter.'"');
		}
		
		if (@count ( $lpost ['clients'] ) > 0) {
			$zccnt=count ( $lpost );
			$clientFieldsTotal = count($lpost['clients']);
			if(array_key_exists('extra',$lpost)){
				--$zccnt;
			}
			if(array_key_exists('admission',$lpost) && count($lpost['admission']) == 1 &&
				$lpost['admission'][0] == 'admission_location'){
					--$zccnt;
			}
			if($zccnt === 1 && array_key_exists('clients',$lpost)){
				$clientCase= true;
			}
			$upost = admFirst ( $lpost ['clients'],strlen($lvdSQLCode) );
			/*$lvdForm = array_search('client_lvd_form',$lpost['clients']);
			if(is_numeric($lvdForm)){
				array_splice($lpost['clients'],$lvdForm,1);
			}*/
			$header [0] = array_merge ( $header [0], getTitles ( $lpost ['clients'], $fielder ['clients'] ['list']->getList () ) );
			//$header [0] = my_array_merge ( $header [0], getTitles ( $lpost ['clients'], $fielder ['clients'] ['list']->getList () ) );
			$tl = count ( $lpost ['clients'] );
			for($i = 0; $i < $tl; $i ++) {
				//if ($lpost ['clients'] [$i] == 'client_adm_no' || $lpost ['clients'] [$i] == 'client_name' ) {
                if(in_array($lpost['clients'][$i],$excludes)){
					$colsConst [] = $i;
				}
                //if($lpost['clients'][$i] !== 'client_lvd_form'){
				    $header [1] [] = array ('v' => 'Clients', 'r' => 'CLI' );
				    $header [2] [] = $titles ['clients'] ['db'];
				    $decoder [$lpost ['clients'] [$i]] = 'clients';
                //}
			}
			//var_dump($header);echo '<br><br>';
			//$txtpost = mysql_real_escape_string ( join ( ",", $upost ) );
			$txtpost =  join ( ",", $upost ) ;
			$index = array_search ( 'client_name', $lpost ['clients'] );
			$upost_orig = $upost;
			if ($index !== false && $index >= 0) {
				$upost [$index] = $titles ['clients'] ['client_name'];
				$txtpost = str_replace ( 'client_name', $titles ['clients'] ['client_name'], $txtpost );
			}
			$q->addQuery ( $txtpost );
		}
		$clist = @array_search ( 'client_status', $lpost ['clients'] );
		if($uamode === false && $dPconfig['regular_definition'] != ''){
			//$q->addWhere('client_status in (1,11)');
			//$q->addWhere($dPconfig['regular_definition']);
		}
		
		for($i = 0; $i < $tl; $i ++) {
			$m1 [] = false;
			if (isset($filters) && is_array ( $filters ) && count ( $filters ) > 0) {
				if (array_key_exists ( $i, $filters ) && $filters [$i]->state === true) {
					if (count ( $filters [$i]->mvals ) > 0) {
						$ors = array ();
						foreach ( $filters [$i]->mvals as &$mv ) {

							$cn = 0;
							foreach ( $lpost ['clients'] as &$pcol ) {
								if ($i == $cn) {
									$ucol = $pcol;
									$mvpre=$mv;
									$lpoly=$fielder['clients']['list']->polyCase($ucol);
									$mv = $fielder ['clients'] ['list']->reverse ( $ucol, $mv );
									if($lpoly !== false ){
										if(!is_array($preFils['clients'.$ucol])){
											$preFils['clients.'.$ucol]=array('vars'=>array());
										}
										$preFils['clients.'.$ucol]['vars'][]=array('v'=>$mvpre,'r'=>$mv,'title'=>'clients','poly'=>$lpoly);
									}else{
										$preFils['clients.'.$ucol][]=array('v'=>$mvpre,'r'=>$mv,'title'=>'clients','poly'=>$lpoly);
									}
									break;
								} else {
									++$cn ;
								}
							}
							//}
							$ors [] = 'clients.' . $ucol . '="' . $mv . '"';
						}
						if (count ( $ors ) > 0) {
							$q->addWhere ( '( ' . join ( ' OR ', $ors ) . ' )' );
						}
					}
					$ors = array ();
					foreach ( $filters [$i]->methods as $mtd => &$mv ) {
						$cn = 0;
						foreach ( $lpost ['clients'] as &$pcol ) {
							if ($i == $cn) {
								$ucol = $pcol;
								$mv = $fielder ['clients'] ['list']->reverse ( $ucol, $mv );
								break;
							} else {
								++ $cn;
							}
						}
						if ($mtd == 'match'){
							$mtd = 'like ';
							$mv='%'.$mv.'%';
						}
						//}
						$ors [] = 'clients.' . $ucol . ' ' . $mtd . ' "' . $mv . '"';

		//$q->addWhere();
					}
					if (count ( $ors ) > 0) {
						$q->addWhere ( '( ' . join ( ' AND ', $ors ) . ' )' );
					}
				}
			}
		}
		
		if ($tl > 0) {
			foreach ( $lpost ['clients'] as &$cl ) {
				$sels [] = $fielder ['clients'] ['list']->getSelects ( $cl );
				$columns [$cl] = 'clients';
			}
		}
		/*if ($locCase) {
			$q->leftJoin('admission_info','ai','ai.admission_client_id=client_id');
			//$q->addTable ( 'admission_info', 'ai' );
			//$q->addWhere ( 'ai.admission_client_id=client_id' );
			$q->addQuery ( 'admission_location' );
			$columns ['admission_location'] = 'admission';
		}

		if($clientCase || $dfil === 'doa'){
			if ($starter != '' && strlen ( $starter ) == 10) {
				$q->addWhere ( 'client_doa >= "' . $starter . '"' );
			}
			if ($ender != '' && strlen ( $ender ) == 10) {
				$q->addWhere ( 'client_doa <= "' . $ender . '"' );
			}
		}
		if(is_null($dfil)){
			$dfil='visit';
		}

		if($lvdSQLCode != ''){
			$q->addWhere($lvdSQLCode);
		}*/
	
		$clients = $q->loadArrayList ();
		//echo '<br><br>';
		//var_dump($clients);
		$did = 0;
		//unset ( $q ,$ors,$ucol,$mv);
		$marker = false;
		$extraCase = false;
		if (isset($lpost['extra']) && is_array ( $lpost ['extra'] ) && count ( $lpost ['extra'] ) > 0) {
			$extraCase = true;
		}
		$offset = ($tl);
		$delayed = array ();
		$extras = array ();

		if(isset($lpoly['clients'])){
			$polys=array_fill(0,count($lpost['clients']),false);
		}

		$locCaseDel = false;
		if (count ( $clients ) > 0) {
			$q1 = new DBQuery ();
			$first = false;
			$queries = array ();
			if ($statusHistory === TRUE) {
				$q2=new DBQuery();
				$q2->addTable('status_client');
				$q2->addWhere('social_client_id in ('.join(',', array_keys($clients)).')');
				$q2->addWhere('mode="status"');
				$q2->addQuery('social_client_status,social_entry_date,social_client_id as clid,id as rid');
				$header[0] = array_merge($header[0],array('social_client_status'=>'Status (Log)','social_entry_date'=> 'Date of change'));

				$header[1] = array_merge($header[1],array(array ('v' => oneWord ( 'Client Status Log' ), 'r' => 'CSL' ),array ('v' => oneWord ( 'Client Status Log' ), 'r' => 'CSL' )));

				$header[2] = array_merge($header[2],array('client_status','client_status'));

				$decoder['social_client_status']='client_status';
				$decoder['social_entry_date']='client_status';
				$bigtar=$q2->loadList();
				$marker=TRUE;
				$tv=2;
			} else {
				foreach ( $lpost as $key => $var ) {
					if(count($var) > 0){
						$vname = findkey ( $key, $tkeys );
						$fv = $fielder [$vname];
						$tiv = $titles [$vname];
						if ($key == 'admission' && in_array ( 'admission_location', $var )) {
							$header [0] = array_merge ( $header [0], getTitles ( $var, $fv ['list']->getList () ) );

							$vpp = array_search ( 'admission_location', $var );
							array_splice ( $var, $vpp, 1 );
							$locCaseDel = true;
							$sels [] = $fielder [$key] ['list']->getSelects ( 'admission_location' );
							$header [1] [] = array ('v' => oneWord ( $fv ['title'] ), 'r' => $tiv ['abbr'] );
							$header [2] [] = $vname;
						}
						$q1 = new DBQuery ();

						if ($vname != '' && $key != 'clients' && $key != 'extra' && ! ($key == 'admission' && $locCaseOne)) {
							++$staterd;
							$pure_val = array ();
							if ($extraCase) {
								$textras=$lpost ['extra'];
								$forOut=array();
								foreach ($textras  as $pid => $pval ) {
									if($tiv[$pval] != ''){
										if (! array_key_exists ( $pval, $tiv ) && ! in_array ( $vname . '_' . $pval, $fv ['list']->getList () )) {
											$decoder [$vname . '_' . $pval] = $key;
											$edcref [$vname . '_' . $pval . '1'] = $vname . '_' . $pval;
										} else {
											$decoder [$tiv [$pval]] = $key;
											$edcref [$vname . '_' . $pval . '1'] = $tiv [$pval];
										}
										//$sels[]=$fielder
										$q1->addQuery ( $tiv [$pval] . ' as ' . $vname . '_' . $pval . '1' );
										$extras [] = $vname . '_' . $pval . '1';
										$sels [] = $fielder ['extra'] ['list']->getSelects ( $pval );
										$header [1] [] = array ('v' => oneWord ( $fv ['title'] ), 'r' => $tiv ['abbr'] );
										$header [2] [] = $vname;
									}else{
										$forOut[]=$pid;
									}
								}
								if(count($forOut) > 0){
									foreach ($forOut as $killval) {
										unset($textras[$killval]);
									}
									$textras=array_values($textras);
								}
								if(count($textras) > 0){
									$header [0] = array_merge ( $header [0], getTitles ( $textras, $fielder ['extra'] ['list']->getList (), $vname . '_', '1' ) );
								}
								//$header [0] = my_array_merge ( $header [0], getTitles ( $lpost ['extra'], $fielder ['extra'] ['list']->getList (), $vname . '_', '1' ) );
							}
							$tv = count ( $var );
							for($z = 0; $z < $tv; $z ++) {
								$socialCase = preg_match ( "/^social_services/", $var [$z] );
								if (! $socialCase && $fv ['list']->instant ( $var [$z] ) && ((isset ( $tiv ['plurals'] ) && ! array_key_exists ( $var [$z], $tiv ['plurals'] )) || ! isset ( $tiv ['plurals'] ))) {
									$bff = $fv ['list']->isComplex ( $var [$z] );
									if (! $bff) {
										$pure_val [] = $var [$z];
									} else {
										if (! in_array ( $bff, $pure_val )) {
											$pure_val [] = $bff;
										}
									}
									$bff = FALSE;
								} elseif (! $socialCase) {
									$delayed [] = $var [$z];
								}

								$decoder [$var [$z]] = $key;
								$fkey = $fielder [$key];
								$sels [] = $fkey ['list']->getSelects ( $var [$z] );
								//$m1[]=$fkey['list']->polyCase($var [$z]);
								$header [1] [] = array ('v' => oneWord ( $fv ['title'] ), 'r' => $tiv ['abbr'] );
								$header [2] [] = $vname;
							}
							$marker = true;
							$header [0] = array_merge ( $header [0], getTitles ( $var, $fv ['list']->getList () ) );
							//$header [0] = my_array_merge ( $header [0], getTitles ( $var, $fv ['list']->getList () ) );
							if (! $queries [$key]) {
								$first = true;
								if (count ( $pure_val ) > 0) {
									$tstr = join ( ',', $pure_val );
									$q1->addQuery ( $tstr );
								}
								$q1->addQuery ( $tiv ['did'] . ' as rid' );
								$q1->addQuery ( $tiv ['client'] . ' as clid' );
								$q1->addQuery ( $tiv ['date'] . ' as ed' );
								$ltn = $tiv ['db'];
								$ltr = explode ( ',', $ltn );
								if (count ( $ltr ) > 1) {
									foreach ( $ltr as &$tn ) {
										$q1->addTable ( trim ( $tn ) );
									}
								} else {
									$q1->addTable ( $tiv ['db'] );
								}
								if ($vis_mode !== 'all' && $vis_mode != '') {
									$q1->setLimit ( '1' );
									if ($vis_mode === 'last') {
										$q1->addOrder ( $tiv ['date'] . ' DESC' );
									} elseif ($vis_mode === 'first') {
										$q1->addOrder ( $tiv ['date'] . ' ASC' );
									}
								}

								if (! ($vname == 'admission' && $locCaseOne) && $dfil === 'visit') {
									if ($starter != '' && strlen ( $starter ) == 10) {
										$q1->addWhere ( $tiv ['date'] . '>= "' . $starter . '"' );
									}
									if ($ender != '' && strlen ( $ender ) == 10) {
										$q1->addWhere ( $tiv ['date'] . '<= "' . $ender . '"' );
									}
								}
								if ($titles [$vname] ['where'] != '') {
									$q1->addWhere ( $tiv ['where'] );
								}
								if ($thisCenter !== FALSE) {
									$q1->addWhere ( $tiv ['center'] . ' = "' . $thisCenter . '"' );
								}
								if (count ( $filters ) > 0) {
									for($i = $offset, $lil = ($offset + count ( $var )); $i < $lil; $i ++) {
										if (array_key_exists ( $i, $filters ) && $filters [$i]->state === true) {
											$tar = &$filters [$i];
											if (count ( $tar->mvals ) > 0) {
												$ors = array ();
												foreach ( $tar->mvals as &$obval ) {
													//$valName = $var [($i - ($offset+1))];
													$valName = $var [($i - $offset)];
													$pval = $fkey ['list']->reverse ( $valName, $obval );
													$lpoly=$fkey ['list']->polyCase ( $valName );
													$polys[]=$lpoly;
													$addReg = '';
													$fieldName = $tiv ['db'] . '.' . $valName;
													if ($lpoly !== false) {
														$addReg = 'OR ' . $fieldName . ' REGEXP "[[.comma.]]?(' . $pval . ')[[.comma.]]?"';
														if(!is_array($preFils[$fieldName])){
															$preFils[$fieldName]=array('vars'=>array());
														}
														$preFils[$fieldName]['vars'][]=array('v'=>$obval,'r'=>$pval,'poly'=>$lpoly,'title'=>$key);
													}else{
														$preFils[$fieldName]=array('v'=>$obval,'r'=>$pval,'poly'=>$lpoly,'title'=>$key);
													}
													$ors [] = '(' . $fieldName . ' = "' . $pval . '" ' . $addReg . ')';

												}
												if (count ( $ors ) > 0) {
													$q1->addWhere ( '( ' . join ( ' OR ', $ors ) . ' )' );
												}
											}
											$ors = array ();
											if (count ( $tar->methods ) > 0) {
												foreach ( $tar->methods as $mtd => &$mv ) {
													$valName = $var [($i - $offset)];
													$pval = $fkey ['list']->reverse ( $valName, $mv );
													if($mtd === 'match'){
														$pval='%'.$pval.'%';
														$mtd = ' LIKE ';
													}
													if (strlen ( $pval ) > 0) {
														$ors [] = $titles [$vname] ['db'] . '.' . $valName . ' ' . $mtd . ' "' . $pval . '"';
													}
												}
												if (count ( $ors ) > 0) {
													$q1->addWhere ( '( ' . join ( ' AND ', $ors ) . ' )' );
												}
											}
										}
									}
								}
								$queries [$key] = clone $q1;
							}
						}
						unset ( $q1, $fv, $tiv, $fkey );
						if ($key != 'clients') {
							$offset += count ( $var );
						}
					}
				}
			}//end of else for client status history log
			$columns = array_merge ( $columns, $decoder );
			//$columns = my_array_merge ( $columns, $decoder );
			$bti = 0;
			//dbHistory::prepareHistory();
			$chwnulls = false;

			$lastinOrder = (count ( current ( $clients ) ) - 1);

			if (count ( $queries ) > 0) {
				foreach ( $clients as $clid => &$clar ) {
					$lrow = array ();
					foreach ( $queries as $qkey => &$qreal ) {
						$q2 = clone $qreal;
						$atxt = '';

						$q2->addWhere ( $titles [$qkey] ['client'] . '="' . $clar [0] . '"' );
						//$q2->addWhere ($titles [$qkey] ['client'] .' IN ('.join(',',array_keys($clients)).' )');
						$tsql = $q2->prepare();
						$tvar = $q2->loadList ();
						/*if ($vis_mode != 'all') {
							if (is_array ( $tvar ) && count ( $tvar ) > 0) {
								$lrow = array_merge ( $lrow, $tvar [0] );
								foreach ( $lpost as $pcl => $pvars ) {
									if ($pcl != 'clients' && $pcl != 'extra' && $pcl != $qkey && ! ($pcl == 'admission' && $locCaseOne)) {
										foreach ( $pvars as $pvk ) {
											$lrow [$pvk] = 'AwSeDrFtG';
										}
									}
								}
								$bigtar [$bti] = $lrow;
								$bti ++;
							}*/
						//} else if ($vis_mode == 'all') {
							if (count ( $tvar ) > 0) {
								foreach ( $tvar as &$nrow ) {
									//if (dbHistory::checkStatus ( $nrow ['clid'], $nrow ['ed'] )) {
									//if ($uamode === true || ($uamode === FALSE && $clients [$nrow ['clid']] [1] == 1)) {
										$bigtar [$bti] = $nrow;

										foreach ( $lpost as $pcl => $pvars ) {
											if ($pcl != 'clients' && $pcl != 'extra' && $pcl != $qkey && ! ($pcl == 'admission' && $locCaseOne)) {
												foreach ( $pvars as &$pvk ) {
													if ($pvk != 'admission_location'/* && $pcl!= 'admission'*/){
														$bigtar [$bti] [$pvk] = 'AwSeDrFtG';
													}
												}
											}
										}
										//ksort($bigtar[$bti]);
										if (! ($locCaseOne && $qkey == 'admission')) {
											++$bti;
										} else if ($qkey == 'admission' && $locCaseOne) {
											//$clients [$clid] [$lastinOrder] = $nrow ['admission_location'];
											$clients [$nrow['clid']] [$lastinOrder] = $nrow ['admission_location'];
											unset ( $bigtar [$bti] );
										}


									//}
								}
							}
						//}
						unset ( $q2 );
					}

				 } // foreach clients end
			}
		}
	}
	unset ( $queries );
	diskFile::init ();
	$bigtar_cnt = count ( $bigtar );
	$clients_cnt = count ( $clients );

    if($lvdSQLCode !== ''){
	          $header [0] = array_merge($header[0],array('client_lvd_form'=>'LVD Form','client_lvd'=>'LVD'));
              $header [1] [] = array ('v' => 'Clients', 'r' => 'CLI' );
              $header [2] [] = $titles ['clients'] ['db'];
              $header [1] [] = array ('v' => 'Clients', 'r' => 'CLI' );
              $header [2] [] = $titles ['clients'] ['db'];
              $decoder ['client_lvd_form'] = 'clients';
              $decoder ['client_lvd'] = 'clients';
    }
	if ($bigtar_cnt > 0 || $clients_cnt > 0) {
		$bigtar_keys = (isset($bigtar) && count($bigtar) > 0) ?  array_keys ( $bigtar ) : array();
		$rhtml = '<div style="text-align: center;*text-align:none;padding-left:50px;padding-bottom:30px;">
		<form method="POST" action="/?m=outputs&suppressHeaders=1" name="saveme">
		<input type="hidden" name="list" id="stabbox">
		<input type="hidden" name="mode" value="save">
		<input type="hidden" name="fname" value="">
		<input type="button" onclick="popTable(\'mholder\',\'print\')" style="float:left; " value="Print" class="button">
		<input type="button" onclick="gpgr.saveTable()" style="float:left; " value="Export to Excel" class="button adcbutt">
		</form>';
		if ($qmode != 'mas') {
			$rhtml .= '<input type="button" class="button adcbutt" value="Save Table Query" onclick="qurer.saveDialog()">
			<input type="button" class="button adcbutt" value="Build Stats" onclick="gpgr.startss();">';
		}
		$rhtml .= '</div>';
		$tab_start = '<div id="mholder"><table class="rtable moretable" id="rtable" border="0" style="display:none;" cellpadding="2" cellspacing="1">';
		$tab_head = "\n<thead><tr>";
		$colz = '<colgroup>';
		$ind = 0;
		$addcl = '';
		$reportLinks=array();

		foreach ( $header [0] as $hcode => &$hname ) {
			$tab_head .= '<th id="head_' . $ind . '" data-thid="' . $ind . '" class="head ' . $addcl . '" data-part="' . $header [1] [$ind] ['r'] . '">' . $hname . '<div class="head_menu"></div></th>' . "\n";
			$tab_src .= '<th class="fsource">' . $header [1] [$ind] ['v'] . '</th>' . "\n";
			$colz .= '<col id=col_' . $ind . '></col>';
			$reportLinks[(array_key_exists($hcode,$edcref) ? $edcref[$hcode] : $hcode) ] = $header[2][$ind];
			$ind ++;
			$addcl = ' forsize';

		}
		$tab_head .= "</tr>\n<tr>" . $tab_src . '</tr></thead>' . "\n";
		$rhtml .= $tab_start . $colz . $tab_head;
		unset ( $tab_start, $colz );
		$rhtml .= '<tbody>';
		diskFile::putTXT ( $rhtml );
		$clIds = array ();
		$onceMulti = false;
		$dcname='';
		$wcind = 0;
		$passed = 0;
		if ($marker) {
			if (count($bigtar) > 0) {
				foreach ($bigtar as $big_id => &$rvals) {
					/*|| dbHistory::checkStatus ( $rvals ['clid'], $rvals ['ed'] )*/
					if ( /*$vis_mode == 'all' &&*/
						($uamode || ($uamode === false && $clients [$rvals ['clid']] [1] == 1))
					) {
						$row = '';
						$x = 0;
						$xpos = 0;
						$jsar [$y] ['fakes'] = array();
						$jsar [$y] ['hidden'] = false;
						$rid = $rvals ['clid'];
						if (($rid > 0 && is_array($clients [$rid])) || $marker === true) { //</td>\n\t\t<td class='txtit'>
							$firstTD = true;
							$row = "\n\t<tr id='row_" . $y . "'>\n\t\t";
							$clIds [$y] = $clients [$rid] [0];
							if ($tv > 0 && count($lpost ['clients']) > 0) {
								foreach ($lpost ['clients'] as $pid => &$pcl) {
									if (!strstr($pcl, 'client_lvd')) {
										/*if($pcl == 'client_status'){
											  $rt=dbHistory::checkStatus($rvals['clid'],$rvals['ed'],'actual');
											  }else{*/

										$rt = $fielder ['clients'] ['list']->value($pcl, $clients [$rid] [($pid + 2)]);

										//}

										$edCell = 'vcell ';
										$forStore = $rt;

										if (in_array($pcl, $excludes)) {
											$edCell = '';
										}
										$nfei->store($forStore);

										if ($firstTD) {
											if ($pcl === 'client_adm_no') {
												$item = "<div class='txtit fhref flink' onclick='shCl(" . $clients [$rid] [0] . ")'>" . $rt . "</div>";
											} else {
												$item = $rt;
											}
											$firstTD = false;
											$row .= "<td class='rowfdel'><div class='delbutt blind'></div><div #@QBC@# class='qeditor blind'></div>" . $item . "</td>\n";
										} else {
											$row .= "\t\t<td class='" . $edCell . $nfei->colType($xpos) . "'>" . $rt . "</td>\n";
										}
										++$xpos;
									}
								}
								if ($locCase) {
									$rt = $fielder ['admission'] ['list']->value('admission_location', $clients [$rid] [($lastinOrder)]);
									$nfei->store($rt);
									$row .= "\t\t<td class='vcell " . $nfei->colType($xpos) . "'>" . $rt . "</td>\n";
									++$xpos;
								}
							}
							//foreach ( $rvals as $colname => $zval ) {
							$part = '';
							$wclass = '';
							$wcind = 0;
							$passed = 0;
							if (!isset($extras)) {
								$extras = array();
							}
							foreach ($header [0] as $colname => &$cltit) {
								if ($colname != 'clid' && $colname != 'ed' && !preg_match("/^client/", $colname) && $colname != 'admission_location') {
									if (array_key_exists($colname, $edcref)) {
										$colname1 = $colname;
										$pcolname = $edcref [$colname];
										$colname = $edcref [$colname];

									} else {
										$colname1 = $colname;
										$pcolname = $colname;
									}
									$dcname = $decoder [$colname];
									$fv = $fielder [$dcname];

									$polyCase = $fv ['list']->polyCase($pcolname);
									$haveMode = $fv ['list']->extraMode($pcolname);
									$pluriCase = $fv ['list']->pluriCase($pcolname);
									$readOnly = $fv['list']->isReadOnly($pcolname);

									if (!$onceMulti) {
										$m1 [] = $polyCase;
									}
									$zval = $rvals [$colname1];
									if (is_null($zval) || trim($zval) == '') {
										$zval = '&nbsp;';
									}
									if ($zval === 'AwSeDrFtG') {
										$zclass = 'fake';
										$zval = '';
										$nfei->itFake();
									} else {
										$zclass = 'vcell';
										$part = $dcname;
										$wclass = $header [2] [($wcind + $passed)];
									}

									if ($zclass === 'vcell' && $readOnly === true) {
										$zclass = '';
									}
									if (preg_match("/^social_services/", $colname)) {
										$zval = array($rvals ['clid'], $rvals ['rid']);
									} elseif (in_array($colname, $delayed)) {
										if (strstr($colname, 'household')) {
											$zval = $rvals['clid'];
										} else {
											$zval = $rvals ['rid'];
										}
									}

									if (in_array($colname1, $extras)) {
										$localName = @array_reverse(explode('_', str_replace('1', '', $colname1)));
										$pval = $fielder ['extra'] ['list']->value($localName [0], $zval, $rvals ['rid']);
									} else {
										$pval = $fv ['list']->value($colname, $zval, $rvals ['rid']);
									}
									$nfei->store($pval, $polyCase, $haveMode, $pluriCase ? $fv ['list']->getPData($colname) : FALSE);
									if ($polyCase !== false) {
										if (is_array($pval) && count($pval) > 0) {
											$cellTxt = implode(', ', $pval);
										}
									} else {
										$cellTxt = $pval;
									}
									$st = trimView($cellTxt);
									if ($st ['show'] === true) {
										$zclass .= ' moreview ';
										$dct = ' data-text="' . $st ['orig'] . '" ';
									} else {
										$dct = '';
									}
									if ($firstTD === true) { //&& $activCase === true
										$firstTD = false;
										$row .= "<td class='rowfdel'><div class='delbutt blind'></div><div #@QBC@#  class='qeditor blind'></div><div class='vcell'>" . $st['str'] . "</div></td>\n";
									} else {
										$row .= "\t\t<td " . $dct . " class='$zclass " . $nfei->colType($xpos) . "'>" . $st ['str'] . "</td>\n";
									}

								} else {
									++$passed;
								}
							}
							if ($lvdSQLCode !== '') {
								$rt = $fielder ['clients'] ['list']->value('client_lvd_form', $clients [$rid] [$clientFieldsTotal + 2]);
								$nfei->store($rt);
								$row .= "\t\t<td class='text-left'>" . $rt . "</td>\n";
								++$xpos;
								++$x;
								++$wcind;
								$rt = $fielder ['clients'] ['list']->value('client_lvd', $clients [$rid] [$clientFieldsTotal + 3]);
								$nfei->store($rt);
								$row .= "\t\t<td class='text-left'>" . $rt . "</td>\n";
								++$xpos;
								++$x;
								++$wcind;
							}
						}
						if (isset($wclass) && $wclass !== '') {
							$row = str_replace("#@QBC@#", 'data-tbl="' . $wclass . '||' . $rvals ['clid'] . '||' . $rvals ['rid'] . '" ', $row);
							if (@!in_array($wclass, $e)) {
								$e [] = $wclass;
							}
						} else {
							$row = str_replace("#@QBC@#", '', $row);
						}
						$row .= '</tr>' . "\n";
						//$final [$y] = array ('row' => $row, 'id' => $rvals ['rid'], 'table' => $wclass );
						//$rhtml .= $row;
						diskFile::tableBody(array('row' => str_replace(array("\r\n", "\n", "\r"), '', $row), 'id' => $rvals ['rid'], 'table' => $wclass, 'client' => $rvals ['clid'], 'date' => DatetoInt($rvals['ed'])), $y);
						diskFile::putTXT($row);
						++$y;
						$nfei->nextRow();
					} //else {
					unset ($bigtar [$big_id]);
					//}
					unset ($row);
					$onceMulti = true;
				}
			}
		} else {
			foreach ( $clients as $cld => &$clar ) {
				$row = "\n\t<tr id='row_" . $y . "'>\n\t\t";
				$firstTD=true;
				$xpos=0;
				$rid=$cld;
				if (count ( $lpost ['clients'] ) > 0 ) {
					foreach ( $lpost ['clients'] as $pid => &$pcl ) {
						/*if($pcl == 'client_status'){
						$rt=dbHistory::checkStatus($rvals['clid'],$rvals['ed'],'actual');
						}else{*/
						$rt = $fielder ['clients'] ['list']->value ( $pcl, $clar [($pid + 1)] );
						//}
						$edCell = 'vcell ';
						$forStore = $rt;
						if ($pcl === 'client_name') {
							$edCell = '';
						}else {
							$zclass = 'vcell';
							$part = $dcname;
							$wclass = $header [2] [($wcind + $passed)];
						}
						$nfei->store ( $forStore/* ,$fielder [$decoder [$colname]] ['list']->polyCase($pcl)*/);
						
						if ($firstTD) {
							if ($pcl === 'client_name') {
								$item = "<div class='<!--txtit -->fhref flink' onclick='shCl(" . $clients [$rid] [0] . ")'>" . $rt . "</div>";
							} else {
								$item = $rt;
							}
							$firstTD = false;
							$row .= "<td class='rowfdel'><div class='delbutt blind'></div><div #@QBC@# class='qeditor blind'></div>" . $item . "</td>\n";
						} else {
							$row .= "\t\t<td class='" . $edCell . $nfei->colType ( $xpos ) . "'>" . $rt . "</td>\n";
						}
						++$xpos;
					}
					if ($locCase) {
						$rt = $fielder ['admission'] ['list']->value ( 'admission_location', $clients [$rid] [($lastinOrder)] );
						$nfei->store ( $rt );
						$row .= "\t\t<td class='vcell " . $nfei->colType ( $xpos ) . "'>" . $rt . "</td>\n";
						++$xpos;
					}
				}

				if (isset($wclass) &&  $wclass != '') {
					$row = str_replace ( "#@QBC@#", 'data-tbl="' . $wclass . '||' . $rid . '||' . $rid . '" ', $row );
					if (@! in_array ( $wclass, $e )) {
						$e [] = $wclass;
					}
				}else{
					$row = str_replace ( "#@QBC@#", '', $row );
				}
				if($lvdSQLCode !== '' ){
					$rt = $fielder ['clients'] ['list']->value ( 'client_lvd_form', $clients [$rid] [$clientFieldsTotal + 2] );
					$nfei->store ( $rt);
					$row .= "\t\t<td class='text-left'>" . $rt . "</td>\n";
					++$xpos;
					$rt = $fielder ['clients'] ['list']->value ( 'client_lvd', $clients [$rid] [$clientFieldsTotal + 3] );
					$nfei->store ( $rt);
					$row .= "\t\t<td class='text-left'>" . $rt . "</td>\n";
					++$xpos;
				}
				$nfei->nextRow ();
				$row .= '</tr>' . "\n";
				$rhtml .= $row;
				diskFile::putTXT ( $rhtml );
				diskFile::tableBody ( array ('row' => str_replace ( array ("\r\n", "\n", "\r" ), '', $row ), 'id' => $clar [0], 'table' => 'clients', 'client' => $rid ), $y );
				++$y;
				$row='';
			}
			unset ( $row, $td );
		}
		//unset($header);
		//dbHistory::purge();
		unset ( $clients );
		$tddd=$nfei->getForStat ();
		diskFile::tableBodyWrite ( $tddd );
		$rhtml .= '</tbody></table></div> <div id="pagebox"><span id="pgbs"></span>
		<span style="float:left;">&nbsp;&nbsp;&nbsp;&nbsp;Rows per page<select name="npp" onchange="gpgr.reorder(this)">
		<option value="10" >10</option>
		<option value="20">20</option>
		<option value="50" selected="selected">50</option>
		<option value="100">100</option>
		<option value="200">200</option>
		<option value="500">500</option>
		<option value="-1">All</option>
		</select></span>
		<div id="cleanbox" style="display:none;float:left;margin-right: 15px;">
			<span class="fmonitor" id="fmbox"></span>
			<input type="button" class="button" onclick="cleanAllF();" disabled="disabled" value="Clear Filters" id="fclean">
		</div></div>';
		diskFile::putTXT ( $rhtml );

		$l = $nfei->html ();
		$f = $nfei->getFakes ();
		$h = $nfei->getHeads ();
		$u = $nfei->getLects ();
		$r = $nfei->getRefs ();
		$p = $nfei->getPlurals ();
		$rl = $reportLinks;
		$nfei->purge ();
		unset ( $nfei, $fielder );
		$lpost['clients']=$upost_orig;
		$_SESSION ['table'] = array ("head" => $tab_head, "body" => $final, 'clids' => $clIds );
		$_SESSION ['query'] = array (
										"posts" => $lpost, "begin" => $starter, "end" => $ender,
										"visits" => $vis_mode, 'cols' => $columns, 'dfilter' => $dfil,
										'center' => $use_center, 'actives' => $uamode,
										'polys'=>array('marker'=>$m1,'values'=>$sels,'plurs'=>$p),
										'lvd' => array($lvder,$lvd_sel)
									);
		unset ( $final, $clIds, $lpost, $columns );

	}
}

class dbHistory {
	private static $tar = array ();
	private static $needStatus = 1;
	private static $defaultStatus;
	private static $types = array ();
	private static $row;
	private static $last = array ('client' => '', 'date' => '' );

	public static function prepareHistory() {
		if (self::$defaultStatus === false) {
			$sql = 'select * from status_client order by social_client_id, social_entry_date ASC';
			$res = mysql_query ( $sql );
			if ($res) {
				while ( $row = mysql_fetch_object ( $res ) ) {
					if (! is_array ( self::$tar [$row->social_client_id] )) {
						self::$tar [$row->social_client_id] = array ();
					}
					self::$tar [$row->social_client_id] [] = array ('date' => $row->social_entry_date, 'status' => $row->social_client_status );
				}
				mysql_free_result ( $res );
			}
			unset ( $row );

		}
	}

	public static function types() {
		self::$types = dPgetSysVal ( 'ClientStatus' );
	}

	private static function cleanDate($date) {
		return ( int ) str_replace ( '-', '', $date );
	}

	private static function foundStatus($clid, $ed) {
		$ed = self::cleanDate ( $ed );
		$lset = self::$tar [$clid];
		if (is_array ( $lset )) {
			$actualStatus = false;
			foreach ( $lset as &$entry ) {
				$udate = self::cleanDate ( $entry ['date'] );
				if (! is_null ( $entry ['date'] ) && $ed >= $udate) {
					self::$row = $entry;
				}
			}
		}
	}

	public static function checkStatus($clid, $ed, $mode = 'show') {
		if (self::$defaultStatus === true) {
			return true;
		}
		if ($clid != self::$last ['client'] || $ed != self::$last ['date']) {
			self::$last = array ('client' => $clid, 'date' => $ed );
			self::$row = false;
			self::foundStatus ( $clid, $ed );
		}
		$mode == 'actual' ? $res = '&nbsp;' : $res = self::$defaultStatus;
		if (is_array ( self::$row )) {
			if (self::$row ['status'] == self::$needStatus) {
				$res = true;
			}
			if ($mode == 'actual') {
				if (self::$row ['status'] >= 0) {
					$res = self::$types [self::$row ['status']];
				}
			}
		}
		return $res;
	}

	public static function setViewMode($res = false) {
		self::$defaultStatus = $res;
	}

	public static function purge() {
		self::$tar = null;
		self::$types = null;
		self::$last = null;
	}
}

class evolver {
	private $cols = array ();
	private $xpos=0;
	private $y=0;
	private $tar=array();
	private $fakes=array();
	private $unique=array();
	private $refer=array();
	private $treal=array();
	private $pldata = array();
	private $rcdata = array();
	private $pluralcols = array();
	private $plurallects = array();

	function __construct(){
		$this->fakes[0]=array();
	}

	function treatOne($col,$val,$xtype = FALSE){
		$res=false;
		$val=str_replace('&nbsp;','',$val);
		if (strlen ( trim ( $val ) ) > 0) {
			if(!array_key_exists($col,$this->cols)){
				if (preg_match ( "/\d{4}-\d{2}-\d{2}/", $val )) {
					$this->cols [$col] = "date";
				} elseif (is_numeric ( $val )) {
					$this->cols [$col] = "float";
				} else {
					$this->cols [$col] = 'string';
				}
			}
			$res = $this->parseVal ( $val, $col );
		}elseif ($xtype !== false){
			$this->cols[$col]=$xtype;
		}
		return $res;
	}

	function treatOneOfPlurals($col,$val,$xtype = FALSE){
		$res=false;
		$val=str_replace('&nbsp;','',$val);
		if (strlen ( trim ( $val ) ) > 0) {
			if(!array_key_exists($col,$this->pluralcols)){
				if (preg_match ( "/\d{4}-\d{2}-\d{2}/", $val )) {
					$this->cols [$col] = "date";
				} elseif (is_numeric ( $val )) {
					$this->pluralcols [$col] = "float";
				} else {
					$this->pluralcols [$col] = 'string';
				}
			}

			if(array_key_exists($col,$this->pluralcols)){
				$xtype = $this->pluralcols [$col];
			}
			$res = '';
			if ($xtype === "int" || $xtype === 'date') {
				$res = ( int ) preg_replace ( "/[\s-]/", '', $val );
			} elseif ($xtype ===  'float') {
					$res = ( float ) $val;
			} else {
				$res = trim ( strtolower ( str_replace('&nbsp;','',$val) ) );
			}

		}elseif ($xtype !== false){
			$this->pluralcols[$col]=$xtype;
		}
		return $res;
	}

	function treat($col, $val,$xtype = FALSE) {
		if(is_array($val) ){
			if( count($val) > 0){
			$vstock=array();
			foreach ($val as $vpart) {
				if(is_array($vpart) && isset($vpart['title'])){
					$vstock[]=$vpart['title'];
				}else{
					$vstock[]=$vpart;
				}
			}
			$valStr=implode(', ',$vstock);
			$res=$this->treatOne($col,$valStr,$xtype);
			$res=array(array_map("downlow",/*$val*/$vstock),$res);
			}else{
				$res=$this->treatOne($col,'',$xtype);
			}
		}else{
			$res=$this->treatOne($col,$val,$xtype);
		}
		return $res;
	}

	function colType($x){
		$res='';
		if(array_key_exists($x,$this->cols) && $this->cols[$x] === 'string'){
			$res='text-left';
		}
		return $res;
	}

	function parseVal($val, $col) {
		$type=false;
		if(array_key_exists($col,$this->cols)){
			$type = $this->cols [$col];
		}
		$res = '';
		if ($type === "int" || $type === 'date') {
			$res = ( int ) preg_replace ( "/[\s-]/", '', $val );
		} elseif ($type ===  'float') {
			if(strstr($val,'-')){
				$this->cols[$col]='string';
				$res = $this->parseVal($val,$col);
			}else{
				$res = ( float ) $val;
			}
		} else {
			$res = trim ( strtolower ( str_replace('&nbsp;','',$val) ) );
		}
		return $res;
	}

	function storeRCID ($client_id,$row_id){}

	function store($val,$polyCase = false,$xtype = FALSE,$pldata = FALSE){
		$cxpos=&$this->xpos;
		$val = utf8_decode($val);
		if($pldata !== false ){
			if(is_array($pldata) && $val!='&nbsp;'){
				if(!is_array($this->pldata[$cxpos])){
					$this->pldata[$cxpos]=$pldata;
				}else {
					$this->pldata[$cxpos]['data'] = $pldata['data'];
				}
				$zpdata = end($pldata['data']);
				$pltypes = &$pldata['columns'];
				$tdatas=explode(';',$val);
				foreach ($zpdata as $pipd =>  $pt) {
					$sidatas = explode('|',$tdatas[$pipd]);
					foreach ($pt as $sid => $svalue) {
						$cleanval=$this->treatOneOfPlurals($sid,$svalue,$pltypes[$sid]);
						$this->pluralLect($svalue,(is_array($pltypes[$sid]) ? $pltypes[$sid][$svalue] : $sidatas[$sid]),$sid);
					}
				}
				$this->tar[$this->y][$cxpos]=$val;
			}
		}else{
			$nv=$this->treat($cxpos,$val,$xtype);
			$this->tar[$this->y][$cxpos]=$nv;
			$polyCase === false ?  $unv=$nv : $unv=$nv[1];
			unset($nv);
			$this->lects($unv,$val,$polyCase);
		} 
		++$cxpos;
	}


	function inUniques ($r,$v){
		$cxpos=$this->xpos;
		if(isset($r) && $r === false || is_string($r) && trim($r) === ''){
			if( !in_array($cxpos,$this->fakes[$this->y])){
				$v="Blanks";
				$vtr=true;
				$r=false;
			}else{
				$vtr=false;
			}
		}else{
			$vtr=true;
		}
		if($vtr === true){
			//$npos=array_push($this->unique[$this->xpos],array('r'=>$r,'v'=>$v));
			$this->unique[$cxpos][]=array('r'=>$r,'v'=>$v);
			$this->treal[$cxpos][]=$r;
			$this->refer[$cxpos][(count($this->unique[$cxpos])-1)]=array($this->y);
		}
	}

	function inUniquesPl ($r,$v,$pxpos){
		$cxpos=$this->xpos;
		if(isset($r) && $r === false || is_string($r) && trim($r) === ''){
			if( !in_array($cxpos,$this->fakes[$this->y])){
				$v="Blanks";
				$vtr=true;
				$r=false;
			}else{
				$vtr=false;
			}
		}else{
			$vtr=true;
		}
		if($vtr === true){
			//$npos=array_push($this->unique[$this->xpos],array('r'=>$r,'v'=>$v));
			$this->unique[$cxpos][$pxpos][]=array('r'=>$r,'v'=>$v);
			$this->treal[$cxpos][$pxpos][]=$r;
			$this->refer[$cxpos][$pxpos][(count($this->unique[$cxpos][$pxpos])-1)]=array($this->y);
		}
	}

	function pluralLect ($val,$vval,$pxpos){
		$cxpos = $this->xpos;$cypos=$this->y;
		if(trim($val) === '' || is_null($val)){
			$val=false;
		}
		if(!is_array($this->unique[$cxpos])){
			$this->unique[$cxpos]=array();
			$this->treal [$cxpos]=array();
			$this->refer[$cxpos] = array();
		}
		if (!array_key_exists($pxpos,$this->unique[$cxpos])) {
			$this->unique [$cxpos][$pxpos] = array();
			$this->treal [$cxpos][$pxpos] = array();
			$this->refer [$cxpos][$pxpos] = array();

			if (isset ( $val )) {
				//$this->unique[$this->xpos][]=array('r'=>$val,'v'=>$vval);
				$this->inUniquesPl( $val, $vval ,$pxpos);
				$found=0;
			}
		} else if (isset ( $val ) ) {
			$found = false;
			$found=array_search($val,$this->treal[$cxpos][$pxpos],TRUE);

			if ($found === false) {
				$this->inUniquesPl( $val, $vval,$pxpos );
			}else{
				//if(!in_array($this->y,$this->refer [$cxpos] [$pxpos] [$found])){
				$this->refer [$cxpos] [$pxpos] [$found] [] = $this->y;
				//}
			}
		}
	}

	function oneLect($val,$vval){
		$cxpos=$this->xpos;$cypos=$this->y;
		if(trim($val) === '' || is_null($val)){
			$val=false;
		}
		if (!array_key_exists($cxpos,$this->unique)) {
			$this->unique [$cxpos] = array ();
			$this->treal [$cxpos] = array();
			$this->refer [$cxpos] = array ();
			if (isset ( $val )) {
				//$this->unique[$this->xpos][]=array('r'=>$val,'v'=>$vval);
				$this->inUniques ( $val, $vval );
				$found=0;
			}
		} else if (isset ( $val )) {
			$found = false;
			$found=array_search($val,$this->treal[$cxpos],TRUE);

			if ($found === false) {
				$this->inUniques ( $val, $vval );
			}else{
				$this->refer [$cxpos] [$found] [] = $this->y;
			}
		}
	}

	function lects($val, $vval,$polyCase) {
		if($polyCase === 'multi'){
			$vals=explode(', ',$val);
			for($i=0,$l=count($vals);$i < $l; $i++){
			//foreach ($vals as $vid => $vv) {
				//$this->oneLect($vv,$vval[$vid]);
				$this->oneLect($vals[$i],$vval[$i]);
			}
		}else{
			$this->oneLect($val,$vval);
		}
	}

	function itFake(){
		$this->fakes[$this->y][]=$this->xpos;
	}

	function nextRow(){
		++$this->y;
		$this->xpos=0;
		$this->fakes[$this->y]=array();
	}

	function getCurrentRow(){
		return $this->y;
	}

	function html(){
		return $this->tar;
	}

	function getFakes(){
		return $this->fakes;
	}

	function getHeads(){
		return $this->cols;
	}

	function getPlurals(){
		return $this->pldata;
	}

	function getRefs(){
		return $this->refer;
	}

	function smooth($part){
		$utar=$this->unique[$part];
		$rtar=$this->refer[$part];
		$ar1=array_keys($utar);
		$ar2=array();
		foreach ($utar as &$uval) {
			$ar2[]=$uval['r'];
		}
		$rs=array_multisort($ar2,$ar1);
		$nuq=array();
		$nref=array();
		foreach ($ar2 as $id=>&$val) {
			$opos=$ar1[$id];
			$nuq[]=$utar[$opos];
			$nref[]=$rtar[$opos];
		}
		$this->unique[$part]=$nuq;
		$this->refer[$part]=$nref;
	}

	function getLects(){
		$nar=array();
		foreach ($this->unique as $cid=>&$col) {
			$this->smooth($cid);
		}
		//return json_encode($this->unique);
		return $this->unique;
	}

	function getForStat(){
		return array("uniques"=> $this->unique,'refs'=>$this->refer,'list'=>$this->tar);
	}

	function purge(){
		$this->cols = null;
		$this->tar=null;
		$this->fakes=null;
		$this->unique=null;
		$this->refer=null;
		$this->treal=null;
	}
}

class Validate {

	static $staff= array (
			'data'	=>	'select contact_id as id, CONCAT_WS(" ",contact_first_name,contact_last_name) as name from contacts  where contact_id<>"13" and contact_active="1" order by name asc',
			'name'	=> 'select CONCAT(contact_first_name," ",contact_last_name) from contacts where contact_id="%d" limit 1',
			'id'	=> 'select contact_id from contacts where lower(CONCAT(contact_first_name," ",contact_last_name))="%s" limit 1'
	);

	static $clinic = array(
			'data'	=> 'select * from clinics',
			'id' 	=> 'select clinic_name from clinics where clinic_id="%d" limit 1',
			'name'	=> 'select clinic_id  from clinics where clinic_name="%s" limit 1'
	);

	static $client= array (
			'data'	=> 'select client_id,CONCAT(client_first_name," ",client_last_name) as name from clients ',
			'name'	=> 'select CONCAT(client_first_name," ",client_last_name) from clients where client_id="%d" limit 1',
			'id'	=> 'select client_id from clients where lower(CONCAT(client_first_name," ",client_last_name))="%s" limit 1'
	);

	static $pcarez = array(
			'data'		=> 'select * from admission_caregivers where id="%d"',
			'row'		=> 'select %s  as oid from admission_info where admission_id="%d"',
			'null_case'	=> 'select * from admission_caregivers where client_id="%d" and datesoff is null and role="%s"'
	);

	static $carez = array(
			'data'		=> 'select * from admission_caregivers where id="%d"',
			'row'		=> 'select %s  as oid from social_visit where social_id="%d"',
			'null_case'	=> 'select * from admission_caregivers where client_id="%d" and datesoff is null and role="%s"'
	);

	static $postn = array(
			'data'      => 'select id, title as name from positions',
			'id'        => 'select id from positions where title="%s" limit 1',
			'name'      => 'select title from positions where id = "%d" limit 1'
	);

	static $loctn = array(
			'data'      => 'select * from clinic_location',
			'id'        => 'select clinic_location_id from clinic_location where clinic_location = "%s" limit 1',
			'name'      => 'select clinic_location from clinic_location where clinic_location_id="%d" limit 1'
	);

	static protected $cache = array('staff'=>array('name'=>array(),'id'=>array()),'clinic'=>array('name'=>array(),'id'=>array()),'carez'=>array());

	static function staffName($id){
		if(is_numeric($id)){
			return self::query('staff',$id,'name');
		}elseif(is_null($id)){
			return self::query('staff',$id,'data');
		}else{
			return $id;
		}
	}

	static function clientName($id){
		if(is_numeric($id)){
			return self::query('client',$id,'name');
		}else{
			return $id;
		}
	}

	static function locationName ($id){
		if(is_numeric($id)){
			return self::query('loctn',$id,'name');
		}else{
			return $id;
		}
	}

	static function positionName ($id){
		if(is_numeric($id)){
			return self::query('postn',$id,'name');
		}else{
			return $id;
		}
	}

	static function clinicName($id){
		if(is_numeric($id)){
			return self::query('clinic',$id,'id');
		}elseif(is_null($id)){
			return self::query('clinic',$id,'data');
		}else{
			return $id;
		}
	}

	static function locationId($name){
		return self::query('loctn',$name,'id');
	}

	static function positionId($name){
		return self::query('postn',$name,'id');
	}

	static function staffId($name){
		return self::query('staff',$name,'id');
	}

	static function clinicId($name){
		return self::query('clinic',$name,'name');
	}

	static function clientId($name){
		return self::query('client',$name,'name');
	}

	static function careStuff($id,$field,$bfield,$row_id,$ff=FALSE){
		$arr=self::queryCare('pcarez',$id,$row_id,$bfield);
		return $arr[$field];
	}

	static function careStuff2($id,$field,$bfield,$row_id,$xrole,$ff=FALSE){
		$arr=self::queryCare('carez',$id,$row_id,$bfield,$xrole);
		return $arr[$field];
	}

	static function queryCare($part, $val, $row_id, $fpart,$xrole = false) {
		if((int)$row_id > 0){
			$sqls = sprintf ( self::${$part} ["row"], $fpart, $row_id );
		}else{
			$sqls = sprintf ( self::${$part} ["null_case"], $client_id,$xrole );
		}
		$res = mysql_query ( $sqls );
		if ($res) {
			$info = mysql_fetch_object ( $res );
			$sqls1 = sprintf ( self::${$part} ["data"], $info->oid );
			if ( ! @array_key_exists ( $info->oid, self::$cache [$part] )) {
				$res1 = mysql_query ( $sqls1 );
				if ($res1) {
					$vc = mysql_fetch_array ( $res1 );
					self::$cache [$part] [$info->oid] = $vc;
					return $vc;
				}
			} else {
				return self::$cache [$part] [$info->oid];
			}
		}
	}


	static function query($part,$val,$sql){
		//if (! array_key_exists ( $val, self::$cache [$part][$sql] )) {
			//eval('$sqls = sprintf ( self::$'.$part.' [$sql], $val );');
			$sqls = sprintf ( self::${$part} [$sql], $val );
			$res = mysql_query ( $sqls );
			if ($res) {
				$vc = mysql_fetch_array ( $res );
				//self::$cache[$part][$sql][$val]=$vc[0];
				return $vc [0];
			}
		/*}else{
			return self::$cache[$part][$sql][$val];
		}*/
	}
}

