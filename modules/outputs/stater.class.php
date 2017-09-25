<?php

$extracola=false;

function makeStat($bar,$svals){
	global $tcols,$trows,$baseDir;
	$row_levels = array ();
	//var_dump($bar);
	//echo '<br/><br/>';
	$firstr = $svals ['id'];
	$trows = count ( $svals ['rows'] );
	$tcols = count ( $svals ['cols'] );
	$prs = new Stater ( $bar, $svals ); //
	unset($bar);
	$end = $prs->getToplevs ();

	for($br = 0; $br < $end; $br ++) {
		$path = array ();
		for($i = 1; $i < $trows; $i ++) {
			$crowf = $svals ['rows'] [$i];
			$prs->validChilds ( $crowf ['id'], ($i - 1), $br );
			$path [] = $i;
		}
		$prs->countRows ( $br, $trows );
	}
	if ($trows == 0) {
		$prs->pureCols ( $tcols );
	}

	$prs->buildIt ();
	$prs->__destruct();
	unset($prs);
	$fname = $_SESSION ['fileNameCsh'];
	$fip = $baseDir . '/files/tmp/' . $fname.'.tss';
	if(file_exists($fip) && filesize($fip) > 0){
		$thtml = file_get_contents($fip);
	}
	return $thtml;
}

function unqsort($a,$b){
	if($a['r'] == $b['r']){
		return 0;
	}
	return ($a['r'] < $b['r']) ? -1 : 1;
}

function onlyR($a){
	return $a['r'];
}

function onlyArrs ($a){
	return is_array($a);
}

function preZero($id){
	$res=$id;
	if($id < 10){
		$res =  '0'.$id;
	}
	return $res;
}

function notNulls($a){
	return !is_null($a);
}

class Stater {
	private $uniques;
	private $refs;
	private $list;
	private $bits=array('rows'=>array(),'cols'=>array());
	private $allowed;
	protected $parent;
	protected $localuns;
	protected $localrefs;
	protected $localid;
	protected $depth;
	private $cols=array();
	protected $cy = 0;
	protected $cx = 0;
	protected $llist;
	protected $col_heads;
	protected $last_head;
	protected $grid=array();
	protected $rseen=array();
	protected $finalrow=array();
	protected $totals=array("x"=>0,"y"=>0);
	protected $bgrid=array();
	private $by=0;
	private $stots_rows;
	private $stots_cols;
	private $sperc_rows;
	private $sperc_cols;
	private $srecords;
	private $cgroup=array();
	private $gmetd=false;
	private $tcomp=array('rows'=>array(),'cols'=>array());
	private $sblanks;
	private $gtotal=0;
	private $levcols=array();
	private $allDates=array('rows'=>false,'cols'=>FALSE);
	private $rowCount=0;
	private $lowRowCount=array();
	private $acol_heads=array();
	private $titleBy=array('rows'=>array(),'cols'=>array());
	private $cellCount=0;
	private $lineCells = 0;
	private $colFakes=array();
	private $yearRow=array();
	private $countedFakes=array();
	private $ctshown = array();
	private $frPos=0;
	private $dateRowSpan=1;
	private $upRows=array();
	private $oldParent=false;
	private $hangArr = array();
	private $colNet=array();
	private $filters = false;
	private $rawFile;
	private $deltac = array();
	private $brokenCols = array();
	private $recordCount = array();
	private $shownRows =array();
	private $allShownRows =array();
    private $datapercent = false;

	function __construct($li, &$pack){//  $fr,$all,$cols,$sts){ 'id',$svals['list'],$svals['cols'],$svals['stots']);
		global $_SESSION,$dateDetail,$tcols,$extracola,$baseDir;

		$dateDetail=array('rows'=>false,'cols'=>false);

		$possibleMethds=array('summ','merge');

		$cacheID = $_SESSION['fileNameCsh'];
		//$this->rawFile = fopen($baseDir.'/files/tmp/'.$cacheID.'.tbd','r');

		$this->uniques=$li['uniques'];
		$this->refs=$li['refs'];
		$this->list=$li['list'];
		$this->allowed=$pack['list'];
		$this->cols=$pack['cols'];
		$this->stots_rows=$pack['stots_rows'];
		$this->stots_cols=$pack['stots_cols'];
		$this->sperc_rows=$pack['sperc_rows'];
		$this->sperc_cols=$pack['sperc_cols'];
		$this->srecords=$pack['records'];
		$this->datapercent=$pack['datapercent'];
		$this->sunqs=$pack['sunqs'];
		$this->sblanks=$pack['sblanks'];
		$this->clids=$_SESSION['table']['clids'];
		$this->gmetd=$pack['gmetd'];
		$this->cgroup=$pack['cgroup'];

		if(count($this->allowed) < count($this->list)){
			$this->filters = true;
		}

		//reformat uniques so Blanks will be in the tail of the list, not head.

		$dob_found=false;
		foreach ($dateDetail as $key => &$vaaa) {
			if($vaaa !== false){
				foreach ($pack[$key] as $fid => &$fvar) {
					if(strstr(strtolower($fvar['title']),'dob')){
						$dob_found=$fvar['id'];
					}
				}
			}
		}

		$usedIds = $this->findUsedItem($pack);
		$bigRowsRefs = array();
		$bigRowID =array();

		if(is_array($pack['rows']) && count($pack['rows']) > 0){
			foreach ($pack['rows'] as $pr) {
				$bigRowID[] = $pr['id'];
			}
		}

		if(isset($this->uniques) && count($this->uniques) > 0 && is_array($this->uniques)){
			foreach ($this->uniques as $key =>$vals) {
				//if($this->findUsedItem(&$pack,$key) === false ){
				if(!in_array($key,$usedIds)){
					$this->uniques[$key]=false;
					$this->refs[$key]=false;
				}else {
					if($vals[0]['r'] === false || ($vals[0]['r'] === 0 && $vals[0]['v'] === '0000-00-00' )){
						array_shift($vals);
						//rebuild refs according to changed uniques
						$nrefs=array();

						$nrefs=array_slice($this->refs[$key],1);
						if($this->sblanks === true){
							$nrefs[]=$this->refs[$key][0];
							$vals[]=array('r'=>false,'v'=>'(blanks)');
						}
						$this->uniques[$key]=$vals;
						$this->refs[$key]=$nrefs;

						unset($nrefs);
					}
					if(isset($pack['pluralchoice']) && array_key_exists($key,$pack['pluralchoice']) && !is_null($pack['pluralchoice'][$key])){
						$vals=$vals[$pack['pluralchoice'][$key]];
					}
					$osvals=$vals;
					$vals=array_map('onlyR', $vals);
					//sort($vals);
					//usort($vals, 'unqsort');

					asort($vals);

					$res=array();
					$res2=array();

					//foreach ($osvals as $ckey => &$cval) {
					//$npp=array_search($cval['r'], $vals);
					$npp=0;
					foreach ( $vals as $ni => $nval ) {
						if (($this->sblanks === true && ($nval === FALSE  || $osvals[$ni]['v'] === '0000-00-00')) || ($nval  !== FALSE && $osvals[$ni]['v'] !== '0000-00-00')) {
							$res [$npp] = $osvals [$ni];
							if(isset($pack['pluralchoice']) && array_key_exists($key,$pack['pluralchoice']) && !is_null($pack['pluralchoice'][$key])){
								$orefs=&$this->refs [$key] [$pack['pluralchoice'][$key]][$ni];
							}else{
								$orefs=&$this->refs [$key] [$ni];
							}
							$res2 [$npp] = ($this->filters === true ?
							//array_values ( array_intersect ( $orefs, $this->allowed ) )
							$this->onlyCrossRefs($orefs)
							:
							$orefs );
							if (count ( $res2 [$npp] ) === 0) {
								unset ( $res2 [$npp], $res [$npp] );
							}else{
								++$npp;
							}
						}
					}

					/*ksort($res);
					ksort($res2);*/

					$this->uniques[$key]=$res;
					$this->refs[$key]=$res2;

					unset($res,$res2,$osvals,$orefs,$vals);
				}
			}
		}
		//sort($bigRowsRefs);

		if($this->gmetd !== false ){
			switch ($this->gmetd) {
				case 'merge':
					$newUniques=array();
					$newRefs=array();
					if(count($this->cgroup) > 1){
						$head=$this->cgroup[0];
						$newUniques=$this->uniques[$head];
						$newRefs=$this->refs[$head];
						for($ig=1;$ig < count($this->cgroup); $ig++){
							foreach ($this->uniques[$this->cgroup[$ig]] as $unikey => &$univar) {
								$newpos=count($newUniques);
								$newUniques[$newpos]=$univar;
								$newRefs[$newpos]=$this->refs[$this->cgroup[$ig]][$unikey];
							}
							/*array_splice($this->uniques,$this->cgroup[$ig],1);
							array_splice($this->refs,$this->cgroup[$ig],1);*/
							unset($this->uniques[$this->cgroup[$ig]]);
							unset($this->refs[$this->cgroup[$ig]]);
							foreach ($this->cols as $colID => &$colVal) {
								if($colVal['id'] == $this->cgroup[$ig]){
									array_splice($this->cols,$colID,1);
									$tcols--;
								}
							}

						}
						$this->uniques[$head]=$newUniques;
						$this->refs[$head]=$newRefs;
						foreach ($this->uniques as $key =>&$arr) {
							if($arr === null){
								array_splice($this->uniques,$key,1);
								array_splice($this->refs,$key,1);
							}
						}
					}
					break;

				case 'summ':
					if(count($pack['gsums']) > 2){
						$fields=array();
						$newUniques=array(0=>array('r'=>$pack['gsums']['title'],
						'v'=>$pack['gsums']['title']
						),
						1=>array('r'=>'others',
						'v'=>'Others'
						)
						);
						$newRefs=array(0=>array(),1=>array());
						$firstUniq=0;$once=false;
						foreach ($pack['gsums'] as $fieldId => &$fieldVal) {
							if(is_numeric($fieldId) && (int)$fieldId >= 0){
								if($once === false){
									$firstUniq=$fieldId;
									$once=true;
								}
								$fieldVal=(int)$fieldVal;
								$fields[$fieldId]=$fieldVal;
								if(end($this->uniques[$fieldId]) == array('r'=>false,'v'=>'(blanks)')){
									if($fieldVal > 0){
										$fieldVal--;
									}else{
										$fieldVal=count($this->uniques[$fieldId])-1;
									}
								}
								$newRefs[0][]=$this->refs[$fieldId][$fieldVal];
								unset($this->refs[$fieldId][$fieldVal]);
								unset($this->uniques[$fieldId]);
								foreach ($this->refs[$fieldId] as &$refer) {
									//$newRefs[1]=array_merge($newRefs[1],$refer);
									$newRefs[1]=my_array_merge($newRefs[1],$refer);
								}
								unset($this->refs[$fieldId]);
							}
						}
						sort($newRefs[1]);
						$newRefs[1]=array_unique($newRefs[1]);
						$cleanRefs=$this->findCommon($newRefs[0]);
						$newRefs[0]=$cleanRefs;
						$this->refs[$firstUniq]=$newRefs;
						$this->uniques[$firstUniq]=$newUniques;
						$firstInCols=false;
						foreach ($this->cols as $colId => &$colB) {
							if($colB['id']==$firstUniq){
								$firstInCols=$colId;
							}
							if(array_key_exists($colB['id'],$fields)){
								unset($this->cols[$colId]);
							}
						}
						if(count($this->cols) == 0){
							$firstInCols=0;
						}
						$this->cols[$firstInCols]=array('id'=>$firstUniq,'title'=>'Group','type'=>'string');
						//$tcols-=(count($fields)-1);
						$tcols=count($this->cols);
					}

					break;
				default:
					break;
			}
		}

		$dob_force=false;
		$denyperiods=array('All','none');
		$actGrp=false;
		$bigRows2 = array();
		foreach ( $this->tcomp as $kpart => &$value ) {
			if($kpart === 'cols' && count($pack['rows']) > 0 ){
				if(count($pack['rows']) == 1){
					if(is_array($this->refs[$bigRowID[0]]) && count($this->refs[$bigRowID[0]]) > 0){
						foreach ($this->refs[$bigRowID[0]] as $bforadd) {
							$bigRowsRefs = my_array_merge($bigRowsRefs,$bforadd);
						}
					}
				}else{
					foreach($bigRowID as $brid => $bid){
						if($brid === 0){
							foreach ($this->refs[$bid] as $ras) {
								$bigRowsRefs = my_array_merge($bigRowsRefs,$ras);
							}
						}else{
							foreach ($this->refs[$bid] as $ras) {
								$bigRows2 = my_array_merge($bigRows2,$ras);
							}
							sort($bigRows2);
							$bigRowsRefs = $this->my_array_intersect($bigRows2,$bigRowsRefs);
						}
					}
				}
			}
			sort($bigRowsRefs);
			$unid=0;
			$rind = 0;
			$cway=false;
			$firstfid=true;
			foreach ( $pack [$kpart] as $rid => &$rv ) {
				$dob_case = strstr ( strtolower($rv ['title']), 'dob' );
				$rran = $pack ['range'] [$rv ['id']];
				if (!is_null($rran) && is_array ( $rran ) || $dob_case || $kpart === 'cols') {
					if ($dob_case && ! $rran) {
						$rran = array ('type' => $rv ['type'], 'title' => $rv ['title'] );
						$dob_force = $rind;
					}

					if ($rran ['val'] != "All" ) {
						if ($rran['val'] !== false) {
							if ($rran ['type'] == 'date' && ! $dob_case ) {
								$dateDetail[$kpart] = $rid;
								if($kpart == 'cols'){
									$actGrp=$rran['val'];
								}
								$urs=((count($this->cols))-$rid);
								$this->dateRowSpan=$urs > 0 ? $urs : 1;
							}
							$this->tcomp [$kpart] [$rind] = new Ranger ( $rran ['type'], $rran ['val'], $rran ['title'], $kpart );
							if($rv['type'] === 'string' && is_array($rran['val']) && count($rran['val']) > 0
							&& count(array_unique($rran['val'])) === count($rran['val'])	){
								$this->deltac[]=$rv['id'];
								$origvals = $rran['val'];
								$blankPosition = array_search(-1,$rran['val']);
								if(is_numeric($blankPosition)){
									$rran['val'][$blankPosition] = 'Blank';
									$origvals[$blankPosition] = '(blanks)';
								}
								$nuniq = implode(' to ',$rran['val']);
								$nr = array();
								foreach ($this->uniques[$rv['id']] as $uid => $uvals) {
									$npos = array_search($uvals['v'],$origvals);
									if(is_numeric($npos)){
										$nr[$npos]=$uid;
									}
								}
								$nrefs=array();
								foreach ($nr as $nc =>  $nrid) {
									$nrefs[$nc] = $this->refs[$rv['id']][$nrid];
								}
								$nrefs = $this->lookDeltas($nrefs);
								$this->refs[$rv['id']]=array(0=>$nrefs);
								$pasteu = array('r'=>strtolower($nuniq),'v'=>$nuniq);
								$this->uniques[$rv['id']]=array(0=>$pasteu);
								/*$this->tcomp [$kpart] [$rind] = new Ranger ( 1,1,1, $kpart );*/
								$this->tcomp [$kpart] [$rind]->namer($pasteu,0);
							}
							/*if (! $this->tcomp [$kpart] [$rind]->isValid () && ! $dob_case && $dateDetail[$kpart] == 0) {
							//array_splice ( $this->tcomp [$kpart], $rind, 1 );
							}*/
						}
					} else {
						$this->allDates[$kpart] = true;
					}
				}
				if($dob_case){
					$this->uniques[$rv['id']]=array_reverse($this->uniques[$rv['id']]);
					$this->refs[$rv['id']]=array_reverse($this->refs[$rv['id']]);
				}
				++$rind;
			}
			$currentPart = &$this->tcomp [$kpart];
			if (count ( $currentPart ) > 0 && array_key_exists ( 0, $currentPart )) {
				$cway = false;
				$this->titleBy [$kpart] = 'visual';
			} else {
				$cway = true;
				$this->titleBy [$kpart] = 'id';
			}
			$ontc=false;
			$fid=$pack[$kpart][0]['id'];
			/*if(preg_match('/dob/i',$pack[$kpart][0]['title'])){
			$this->uniques[$fid]=array_reverse($this->uniques[$fid]);
			$this->refs[$fid]=array_reverse($this->refs[$fid]);
			}*/
			$nameCount = 0;
			$prevId=false;
			if(!is_null($fid )){
				$uflist=array(0=>$fid);
			}
			/*if(is_numeric($dateDetail[$kpart]) && $dateDetail[$kpart] > 0){
			$uflist[$dateDetail[$kpart]]=$pack[$kpart][$dateDetail[$kpart]]['id'];
			}*/
			if ($kpart == 'cols' ) {
				foreach ( $this->cols as &$colar ) {
					if (! in_array ( $colar ['id'], $uflist ) && $pack['range'][$colar['id']]['val'] != 'All') {
						$uflist [] = $colar ['id'];
					}
				}
			}else{
				if(is_numeric($dateDetail[$kpart]) && $dateDetail[$kpart] > 0){
					$uflist[$dateDetail[$kpart]]=$pack[$kpart][$dateDetail[$kpart]]['id'];
				}
			}
			$firstfid=true;
			$moveO=false;
			$move1=false;
			$blankArr=array();
			$currIA = array();
			if(!is_array($uflist)){
				$uflist=array();
			}

			$processed=array();
			foreach ( $uflist as $tcid => &$fid ) {
				if(!is_null($fid)){
					$unid=0;
					$link = false;
					$moveO=FALSE;
					$move1=false;
					$prevIA=array();
					$prevId=false;

					$currentComp= &$currentPart [$tcid];

					$pureI = 0;

					$realRanges = (!isset($currentComp) ? false : $currentComp->isRealValid());
					//for($i = 0, $l = count ( $this->uniques [$fid] ); $i < $l; $i ++) {
					$localBlanksCase = false;
					$currentRefs=&$this->refs[$fid];
					$uniqForDel=array();
					if (is_array($this->uniques[$fid]) && count($this->uniques[$fid]) > 0) {
						foreach ($this->uniques[$fid] as $i => &$ufiq) {
							$rfound = true;
							if ($kpart === 'cols' && !in_array($fid, $this->deltac)) {
								if (count($bigRowsRefs) > 0) {
									if (count($this->my_array_intersect($currentRefs[$i], $bigRowsRefs)) === 0) {
										$rfound = false;
									}
								}
							}
							if ($rfound === true || in_array($fid, $this->deltac)) {
								if ($i === 0 && $this->sblanks === true) {
									$localBlanksCase = ($ufiq['r'] === false);
								}
								//$ufiq = $this->uniques [$fid] [$i];
								if ($dateDetail[$kpart] > 0 && $tcid > 0 && $moveO === false) {
									$bobj = clone $currentComp;
									//$xps=$this->tcomp[$kpart][0]->getAllPeriods();
									$xps = $currentPart[0]->getAllPeriods();
									$tar = array();
									foreach ($xps as $xid => &$xval) {
										$tar[$xid] = clone $bobj;
									}
									$tar['test'] = clone $bobj;
									if (count($tar) > 0) {
										//unset($this->tcomp[$kpart][$tcid]);
										//$this->tcomp[$kpart][$tcid]=$tar;
										$currentComp = $tar;
									}
									$moveO = true;
									unset($bobj);
									$extracola = true;
								}
								if (($this->sblanks === true && ($ufiq ['r'] === false || $ufiq['v'] === '0000-00-00')) || ($ufiq ['r'] !== false && $ufiq['v'] !== '0000-00-00') /*&& in_array ( $i, $pack ['list'] )*/) {
									if (!$cway || ($fid == $pack[$kpart][$dateDetail[$kpart]]['id'] && is_numeric($dateDetail[$kpart]))) {
										if ($moveO === true && $tcid > 0) {
											if ($dateDetail[$kpart] != $tcid && $tcid > 0) {
												$founds = array();
												foreach ($this->bits [$kpart] as $bid => &$bar) {
													//$crossy=array_intersect($this->refs[$fid][$i], $bar['list']);
													$crossy = $this->my_array_intersect($this->refs[$fid][$i], $bar['list']);
													if (count($crossy) > 0) {
														$founds[$bid] = $crossy;
													}
												}
												if (count($founds) > 0) {
													foreach ($founds as $did => &$dar) {
														//$this->tcomp [$kpart] [$tcid][$did]->namer ( $ufiq ,$i);
														$currentComp[$did]->namer($ufiq, $i);
														//$this->tcomp [$kpart] [$tcid][$did]->listR ( $crossy);
														$currentComp[$did]->listR($crossy);
														//$this->uniques [$fid] [$i] ['parid'] = $did;
														++$nameCount;
													}
												}
												$r = false;
											} else {
												//$r = $this->tcomp [$kpart] [$tcid]['test']->cmp ( $ufiq, $i );
												$r = $currentComp ['test']->cmp($ufiq, $i);
											}
											if ($move1 === false) {
												//$actp=$this->tcomp [$kpart] [$tcid]['test']->tellPeriod();
												$actp = $currentComp['test']->tellPeriod();
												foreach ($xps as $xid => &$sss) {
													//$this->tcomp[$kpart][$tcid][$xid]->tellPeriod($actp);
													$currentComp[$xid]->tellPeriod($actp);
												}
												$move1 = true;
											}
										} else {
											//$r = $this->tcomp [$kpart] [$tcid]->cmp ( $ufiq, $i );
											$r = $currentComp->cmp($ufiq, $i);
										}

										if ((is_numeric($r) || $r === 'quos') && ($r !== false && ($firstfid === true || $kpart === 'cols'))) { ///*&& $r >= 0)*/
											//$ontc=true;
											if ($moveO === true && $tcid > 0) {
												//$forApprove = $this->addItems ( $tcid, $r, $this->refs [$fid] [$i], 'bits', $kpart );
												$letit = $this->mergeCheck($this->refs [$fid] [$i], $blankArr);
												if (count($letit) > 0) {
													$forApprove = array('add' => true);
												}
											} else {
												$forApprove = $this->landItems($tcid, $r, $this->refs [$fid] [$i], 'bits', $kpart);
											}
											if (!$forApprove['add']) {
												/*if($moveO === true && $tcid > 0){
												//$this->tcomp [$kpart] [$tcid]['test']->cancel ();
												$currentComp['test']->cancel ();
												}else{
												//$this->tcomp [$kpart] [$tcid]->cancel ();
												}*/
												$currentComp->cancel();
											} else {
												$processed[] = $tcid;
												if ($moveO === true && $tcid > 0) {
													$firstfid = false;
													$founds = array();
													if ($tcid > 0) {
														$trifs = $this->refs[$fid][$i];
														sort($trifs);
														foreach ($this->bits [$kpart] as $bid => &$bar) {
															$getin = false;
															if (count($this->my_array_intersect($this->refs[$fid][$i], $bar['list'])) > 0) {
																$founds[] = $bid;
															}

														}
														unset($trifs);
													}
													//$link = $this->tcomp [$kpart] [$tcid] ['test']->getLast ();
													$link = $currentComp ['test']->getLast();
													foreach ($founds as &$did) {
														//$newr = $this->tcomp [$kpart] [$tcid] [$did]->importNew ( $link );
														$newr = $currentComp [$did]->importNew($link);
														//$this->landItems ( $tcid, $newr, $this->refs [$fid] [$i], 'bits', $kpart );
														if ($kpart === 'cols' && $forApprove ['add'] === true && $link !== false) {
															$this->uniques [$fid] [$i] ['vid'] = $newr;
															$this->uniques [$fid] [$i] ['parid'] = $did;
															$this->uniques [$fid] [$i] ['ind'] = $unid++;
															if (is_numeric($prevIA[$did])) {
																$fakes = $this->tcomp ['cols'][$tcid] [$did]->injunker($prevIA[$did], $newr);
																if (!is_null($fakes)) {
																	if (!is_array($this->colFakes [$tcid])) {
																		$this->colFakes [$tcid] = array();
																	}
																	/*if(!is_array($this->colFakes [$tcid][$did])){
																	$this->colFakes [$tcid][$did]=array();
																	}*/
																	if (!is_array($this->colFakes [$tcid][$did])) {
																		$this->colFakes [$tcid][$did] = array();
																	}
																	$this->colFakes [$tcid][$did][$newr] = $fakes;
																}
															}
															$prevIA[$did] = $newr;
														}

													}
												}
											}
											if ($kpart === 'cols' && $forApprove['add'] === true && $link === false) {
												$this->uniques [$fid] [$i] ['vid'] = $r;
												$this->uniques [$fid] [$i] ['ind'] = $unid++;
												if (is_numeric($prevId)) {
													if ($moveO === true && $tcid > 0) {
														$fakes = $this->tcomp ['cols'] [$dateDetail ['cols']][$i]->injunker($prevId, $r);
													} else {
														$fakes = $this->tcomp ['cols'] [$dateDetail ['cols']]->injunker($prevId, $r);
													}
													if (!is_null($fakes) && $realRanges === true) {
														$this->colFakes [$tcid][$r] = $fakes;
													}
												}
												$prevId = $r;
											}
											$thisin = true;
										} else {
											$thisin = false;
											$forAdd['add'] = false;
										}
										//$firstfid = false;
										if ($forApprove['add'] === true) {
											$ontc = true;
										}
									}
									// else
									if ($firstfid === TRUE && !in_array($tcid, $processed)) {
										$forAdd = $this->landItems($tcid, $i, $this->refs [$fid] [$i], 'bits', $kpart);
										if ($forAdd['add'] === true) {
											if (!$ontc) {
												//$this->tcomp [$kpart] [0] = new Ranger ( 1, 1, 1, $kpart );
												$currentPart [0] = new Ranger (1, 1, 1, $kpart);
												$ontc = true;
											}
											//$this->tcomp [$kpart] [0]->namer ( $ufiq, $nameCount );
											$currentPart [0]->namer($ufiq, $nameCount);
											++$nameCount;
											if ($kpart === 'cols' && $forAdd['add'] === true) {
												//$this->uniques [$fid] [$i] ['vid'] = (($localBlanksCase === true ) ? $i : ($i > 0 ? $i : 0));
												$this->uniques [$fid] [$i] ['vid'] = (($localBlanksCase === true) ? $pureI : ($pureI > 0 ? $pureI : 0));
												$this->uniques [$fid] [$i] ['ind'] = $unid++;
												/*if($tcols > 2){

												}*/
											}
										}
									}
								}
								++$pureI;
							} else {
								if ($kpart === 'cols') {
									$uniqForDel[] = $i;
								}
							}
						}
					}
					if(in_array($fid,$this->deltac)){
						$uniqForDel=array();
					}
					if(count($uniqForDel) > 0){
						foreach ($uniqForDel as $udel) {
							unset($this->uniques[$fid][$udel],$this->refs[$fid][$udel]);

						}
						$this->uniques[$fid]=array_values($this->uniques[$fid]);
						$this->refs[$fid]=array_values($this->refs[$fid]);
					}
					$firstfid=false;
					$nameCount=0;
				}
			}
			//$this->bits[$kpart]=array_merge(array(),$this->bits[$kpart]);
			$this->bits[$kpart]=array_values($this->bits[$kpart]);
			ksort($this->tcomp[$kpart]);
		}

		unset($currentRefs);
		/** SORT UNIQUES -- **/
		$rsrefs=array();
		$killrefs=array();
		if(count($bigRowID) > 0){
			$bigRowID = array_reverse($bigRowID);
			foreach ($bigRowID as $brid => $brval) {
				if($brid === 0){
					if(is_array($this->refs[$brval]) && count($this->refs[$brval]) > 0){
						foreach ($this->refs[$brval] as $rind => $rvals) {
							$rsrefs = my_array_merge($rsrefs,$rvals);
						}
					}
				}else{
					foreach ($this->refs[$brval] as $rind => $rvals) {
						if(count($this->my_array_intersect($rvals,$rsrefs)) === 0){
							$this->refs[$brval][$rind]=null;
							$this->uniques[$brval][$rind]=null;
						}
					}
					$this->refs[$brval]=array_values(array_filter($this->refs[$brval],'notNulls'));
					$this->uniques[$brval]=array_values(array_filter($this->uniques[$brval],'notNulls'));
				}
			}
		}


		if(!in_array($actGrp,$denyperiods)  && ($this->titleBy['cols'] === 'visual' || $this->allDates['cols'] == true || is_numeric($dateDetail['cols']))){
			for($i = 0,$l = count ( $this->cols ); $i < $l; $i ++) {
				$newuniq= array();
				$newrfs=array();
				$pass = false;
				$zx=$this->cols[$i]['id'];
				$oldu=$this->uniques[$zx];
				$oldr=$this->refs[$zx];
				$polyMode = is_array($this->tcomp['cols'][$i]);
				if($this->allDates['cols'] === true  && $this->cols[$i]['type'] === 'date'){
					$masterId = 0;
				}else{
					$masterId =false;
				}
				$curuniq=array();
				if (isset($oldu) && is_array($oldu) && count($oldu) > 0) {
					foreach ($oldu as $kuid => &$kvl) {
						if (isset($kvl['parid']) && is_numeric($kvl['parid'])) {
							if (!is_array($curuniq[$kvl['parid']])) {
								$curuniq[$kvl['parid']] = array();
								$currfs[$kvl['parid']] = array();
							}
						}
						if ((isset($kvl['vid']) || $masterId === 0) && ($kvl['r'] != false || ($this->sblanks && $kvl['r'] === false))) {
							if ($masterId === 0 /*&& !isset($kvl['vid'])*/) {
								$xid = $masterId;
							} else {
								$xid = $kvl['vid'];
							}
							if (!isset($newuniq[$xid])) {
								$newuniq[$xid] = array();
							}
							if (!isset($newrfs[$xid])) {
								$newrfs[$xid] = array();
							}

							if ($masterId === false) {
								if ($polyMode && !$this->findStored($kvl['vid'], $kvl['parid'], $curuniq[$kvl['parid']])) {
									$curuniq[$kvl['parid']][$xid] = array(
									'v' => $this->tcomp['cols'][$i][$kvl['parid']]->getRName($xid),
									'r' => $xid,
									'redone' => true,
									'parid' => $kvl['parid'],
									'row' => $i,
									'ind' => $kvl['ind']
									);
									if (!is_array($currfs[$kvl['parid']][$xid])) {
										$currfs[$kvl['parid']][$xid] = array();
									}
									//$currfs[$kvl['parid']][$xid]=array_merge($currfs[$kvl['parid']][$xid],$oldr[$kuid]);
									$currfs[$kvl['parid']][$xid] = my_array_merge($currfs[$kvl['parid']][$xid], $oldr[$kuid]);
								} elseif (!$polyMode) {
									$newuniq[$xid] = array(
									'v' => $this->tcomp['cols'][$i]->getRName($xid),
									'r' => $xid,
									'redone' => true,
									'row' => $i,
									'ind' => $kvl['ind']
									);
								}
							} else {
								$newuniq[$xid] = array('v' => 'All', 'r' => $xid, 'redone' => true, 'row' => $i);
							}
							//$newrfs[$xid]=array_merge($newrfs[$xid],$oldr[$kuid]);
							$newrfs[$xid] = my_array_merge($newrfs[$xid], $oldr[$kuid]);

						}

					}
				}
				if(count($currfs) > 0 && count($curuniq) > 0 && isset($kvl['parid'])){
					$newrfs=array();
					$newuniq = array();
					$pass=true;
					foreach ($curuniq as $cid => &$unar){
						//$newuniq = array_merge($newuniq,$unar);
						$newuniq = my_array_merge($newuniq,$unar);
						//$newrfs = array_merge($newrfs,$currfs[$cid]);
						$newrfs = my_array_merge($newrfs,$currfs[$cid]);
					}
				}

				if(count($newuniq) > 0 ){
					$this->uniques[$zx]=$newuniq;
					$this->refs[$zx]=$newrfs;
				}
			}
		}

		$this->colConditions();

	}

	function onlyCrossRefs($orefs){
		$pref = $this->my_array_intersect ( $orefs, $this->allowed ) ;
		$tref=array_values($pref);
		return $tref;
	}

	function my_array_intersect($a1,$a2){
		if(count($a2) > count($a1)){
			sort($a2);
			$stog=$a2;
			$needles = $a1;
		}else{
			sort($a1);
			$stog = $a1;
			$needles = $a2;
		}
		$founds=array();
		if (is_array($needles) && count($needles) > 0){
			foreach ($needles as $item) {
				if($this->binsearch($item,$stog)){
					$founds[]=$item;
				}
			}
		}
		return $founds;
	}

	function my_array_diff($a1,$a2){
		if(count($a2) > count($a1)){
			sort($a2);
			$stog=$a2;
			$needles = $a1;
		}else{
			sort($a1);
			$stog = $a1;
			$needles = $a2;
		}
		$founds=array();
		if (is_array($needles) && count($needles) > 0){
			foreach ($needles as $item) {
				if($this->binsearch($item,$stog) === false){
					$founds[]=$item;
				}
			}
		}
		return $founds;
	}

	function getRCD ($vals){
		global $baseDir;
		$fpath = $baseDir.'/files/tmp/'.$_SESSION['fileNameCsh'].'.tbd';
		$big_refs=array();
		if(file_exists($fpath) && filesize($fpath) > 0){
			$src = file($fpath);
			foreach ($vals as $vid => $list){
				$resar=array('client'=>array(),'row'=>array(),'date'=>array());
				foreach ($list as $lrow) {
					$row=$src[$lrow];
					$set=unserialize($row);
					$present = array_search($set['client'],$resar['client']);
					if($present === false ){
						$resar['client'][$lrow]=$set['client'];
						$resar['row'][$lrow]=$set['id'];
						if($set['table'] === 'clients'){
							$resar['date'][$lrow] = $set['id'];
						}else{
							//$resar['date'][$lrow]=$set['date'];
							if(!is_array($resar['date'][$lrow])){
								$resar['date'][$lrow]=array();
							}
							$resar['date'][$lrow][]=$set['date'];
						}
					}elseif($present >= 0 && $resar['date'][$present] != $set['date']){
						$resar['date'][$present][]=$set['date'];
					}
				}
				$big_refs[$vid]=$resar;
			}
		}
		$src=null;
		return $big_refs;
	}

	function lookDeltas ($refs){
		$vals=array();
		$nrefs=array();
		$t0=microtime(true);
		/*foreach ($refs as $rid => $reds) {
		$vals[$rid]=$this->getRCD($reds);
		}*/
		$deltaItems=count($refs);
		$vals = $this->getRCD($refs);
		$t1=microtime(true);$t2=$t1-$t0;
		if(count($refs) === 2){
			//$pclients = array_intersect($vals[0]['client'],$vals[1]['client']);
			$pclients = $this->my_array_intersect($vals[0]['client'],$vals[1]['client']);
		}else{
			foreach ($vals as $vid => $valar) {
				if($vid === 0){
					$pclients=$valar['client'];
				}else{
					//$pclients = array_intersect($pclients,$valar['client']);
					$pclients = $this->my_array_intersect($pclients,$valar['client']);
				}
			}
		}
		if(count($pclients) > 0){
			foreach ($pclients as $pcid){
				$idr=array();
				$zetas=array();
				foreach ($vals as $vid => &$vvals){
					$zplace = array_search($pcid,$vvals['client']);
					if($zplace !== false){
						$refid=$vid;
						$refar=$refs[$refid];
						//foreach ($refs as $refid => $refar) {
						if(count($refar) < 100){
							$rppos = array_search($zplace,$refar);
						}else{
							$rppos = $this->binsearch($zplace,$refar);
						}
						if(is_numeric($rppos)){
							$tar=$vvals['date'][$zplace];
							sort($tar);
							if($refid == 1 && $deltaItems === 3){
								$prev=$ids[($refid-1)];
								if(count($tar) > 1){
									$ready=false;
									foreach ($tar as $tval) {
										if($tval > $prev && $ready === false){
											$useDate=$tval;
											$ready = true;
										}
									}
								}else{
									$useDate=$tar[0];
								}
							}else{
								if($refid === 0){
									$useDate=$tar[0];
								}elseif (($refid == 1 && $deltaItems === 2) || ($refid == 2 && $deltaItems === 3)){
									$useDate=array_pop($tar);
								}
							}
							$idr[$refid]=$useDate;
							$zetas[$refid]=$zplace;
						}
						//}
					}
				}
				$oidr = $idr;
				sort($idr);
				if(count(array_diff_assoc($oidr,$idr)) == 0){
					//BINGO we have correct match
					$lastval=end($zetas);
					$nrefs[]=$lastval;
				}
			}
		}
		return $nrefs;
	}

	function localIntersect ($set){
		$num = count($set);
		for ($i=0;$i > $num; $i++){
			for($i=0;$i<sizeof($a);$i++){
				$m[]=$a[$i];
			}
			for($i=0;$i<sizeof($a);$i++){
				$m[]=$b[$i];
			}
			sort($m);
			$get=array();
			for($i=0;$i<sizeof($m);$i++){
				if($m[$i]==$m[$i+1])
				$get[]=$m[$i];
			}
			return $get;
		}
	}

	function binsearch($needle, $haystack){
		$high = count($haystack);
		$low = 0;
		while ($high - $low > 1){
			$probe = ($high + $low) / 2;
			if ($haystack[$probe] < $needle){
				$low = $probe;
			}else{
				$high = $probe;
			}
		}
		if ($high === count($haystack) || $haystack[$high] != $needle) {
			return false;
		}else {
			return $high;
		}
	}

	function findUsedItem(&$pack){
		$used=array();
		$result=false;
		$parts=array('rows','cols');
		foreach ($parts as &$pt) {
			foreach ($pack[$pt] as &$item) {
				/*if($item['id'] == $id){
				return  true;
				}*/
				$used[]=$item['id'];
			}
		}
		return $used;
	}

	function findStored($vid,$parid,&$arr){
		foreach ($arr as &$i) {
			if($i['vid'] == $vid && $i['parid'] == $parid){
				return TRUE;
			}
		}
		return false;
	}

	function transform($order,$fid){
		$res=array();
		foreach ($order as $old => &$new) {
			$res[$old]=$this->bits[$new];
			//$res2[$old]=$this->uniques[$fid][$new];
		}
		$this->bits = array_slice($res,0); // array_merge(array(),$res);
	}

	function findCommon(&$basket = array()){
		if(count($basket) > 0){
			$m=array();$get=array();
			foreach ($basket as &$arr) {
				if(is_array($arr) && count($arr) > 0){
					//$m=array_merge($m,$arr);
					$m=my_array_merge($m,$arr);
				}
			}
			sort($m);
			$get=array();
			$all=count($basket)-1;
			unset($basket);
			for($i=0,$l=sizeof($m);$i<$l;$i++){
				if((($i+1) < $l) && $m[$i] === $m[($i+1)] && (($i+$all) < $l && isset($m[($i+$all)]) && $m[$i] === $m[($i+$all)]) ){
					$get[]=$m[$i];
				}
			}
			unset($m);
			return $get;
		}
	}

	function colConditions() {
		$topcols = array ();
		$ccon = array ();
		for($i = 0,$l = count ( $this->cols );  $i < $l ;$i ++) {
			$this->cols[$i]['title']=preg_replace('/^.*:/','',$this->cols[$i]['title']);
			$this->llist = array ();
			$empty=array();
			if($i == 0 ){
				if($this->filters === true){
					$ccon=$this->privCol($i,$this->allowed);
				}else{
					$ccon=$this->privCol($i,$this->allowed,false,true);
				}
			}else{
				$levcon=0;
				$z=$i-1;
				$ccon=array();
				//while($z >= 0){
				foreach ($topcols[$z]['kids'] as $pid => &$pbl) {
					$tcon=$this->privCol($i,$pbl['list'],$pid);
					$tcAmoun = count($tcon);
					if($tcAmoun > 0){
						//$ccon=array_merge($ccon,$tcon);
						$ccon=my_array_merge($ccon,$tcon);
						//$ccon[]=$tcon;
						$pbl['kids']=$tcon;
						$pbl['cols']=$tcAmoun;  ///count($tcon); //(count($tcon)+1);
						$pbl['head']=$i;
						if($this->stots_cols){
							++$pbl['cols'];
						}
						$levcon+=$tcAmoun+1;
						$tps=array_search($pid,$empty);
						if(is_numeric($tps)){
							array_splice($empty,$tps,1);
						}
					}else{
						if(!in_array($pid,$empty)){
							$empty[]=$pid;
						}
						//unset($topcols[($i-1)]['kids'][$pid]);
					}
				}
				//	$z--;
				//}
				$topcols[($i-1)]['cols']=$levcon;
				foreach ($empty as &$evar) {
					//array_splice($topcols[($i-1)]['kids'],$evar,1);
					unset($topcols[($i-1)]['kids'][$evar]);
				}
				$newtop=array();
			}

			if (count ( $ccon ) > 0) {
				$topcols [$i] = array ('list' => $this->llist, 'kids' => $ccon,'cols'=>0 ,'head'=> $i);
			} else {
				$i = count ( $this->cols );
			}
		}
		$tupar=array();
		$ponce=false;
		$colz=array();
		for($z = count($topcols)-1;$z >= 0;$z--){
			$lcols=0;
			$colz[$z]=0;
			$levadd=array();
			$tupar[$z]=array();
			if(count($topcols[$z]['kids']) > 0){
				$ponce=true;
				$colcnt=0;
				foreach ($topcols[$z]['kids'] as $id=>&$kid){
					if(!is_array($tupar[$z][$kid['parent']])){
						$tupar[$z][$kid['parent']]=array();
					}
					$tupar[$z][$kid['parent']][$id]=&$kid;
					//$tupar[$z][$kid['parent']][$id]['cols']=0;
					$colz[$z]+=$kid['cols'];
					if($ponce && is_array($tupar[($z+1)])){
						if(array_key_exists($id,$tupar[($z+1)])){
							$tupar[$z][$kid['parent']][$id]['kids']=&$tupar[($z+1)][$id];
						}
					}
				}
			}
			//$this->countCols(&$tupar[$z]);
		}
		unset($levadd,$colz);
		$bcols=0;
		if(count($topcols) > 1){
			foreach ($tupar[1] as $tid => &$kids) {
				$topcols[0]['kids'][$tid]['kids']=$kids;
				$lcols=0;
				$tvcount = count($topcols);
				foreach ($kids as &$kvar) {
					if($tvcount > 2){
						$lcols+=$kvar['cols'];
					}else{
						++$lcols;
					}
				}
				if($lcols > 0){
					$topcols[0]['kids'][$tid]['cols']=$lcols;
					if($this->stots_cols === true){
						++$topcols[0]['kids'][$tid]['cols'];
					}
					$bcols+=$lcols;
				}else{
					array_splice($topcols[0]['kids'],$tid,1);
				}
			}
		}
		$topcols[0]['cols']=$bcols;
		/*foreach ($topcols as &$bl) {
		$this->countCols($bl);
		}*/
		if(count($topcols) ==  count ( $this->cols )){
			$this->countCols($topcols[0]);
			$this->col_heads=$topcols;
		}else{
			$this->col_heads=array();
			$this->levcols=array();
		}
		unset($topcols,$tupar);
		$this->acol_heads=array_reverse($this->col_heads);
		$this->llist=null;
	}

	function allCols($arr){
		$res=0;
		foreach ($arr as &$k) {
			$res+=$k['cols'];
		}
		if($this->stots_cols){
			++$res;
		}
		return $res;
	}

	function countCols(&$pbl,$level=0){
		global $dateDetail;
		$res=0;$added=false;
		$fakeIn=FALSE;
		if(count($pbl['kids']) > 0 && $level < (count($this->cols))){
			if(!is_array($this->levcols[$level])){
				$this->levcols[$level]=array();
			}
			if(!is_array($this->colFakes[$level])){
				$this->colFakes[$level]=array();
			}
			//$this->levcols[$level]=array_merge($this->levcols[$level],$pbl['kids']);
			$this->levcols[$level]=my_array_merge($this->levcols[$level],$pbl['kids']);
			$kidcount=0;
			if (isset ( $pbl ['val'] ['redone'] )) {
				if (array_key_exists ( $level, $this->colFakes ) && count ( $this->colFakes [$level] ) > 0) {
					$ulev = $level;
				} else {
					$ulev = $dateDetail ['cols'];
				}
				if (array_key_exists ( $pbl ['val'] ['r'], $this->colFakes [$ulev] ) && count ( $this->colFakes [$ulev] ) > 0) {
					$acvu=$this->colFakes [$ulev] [$pbl ['val'] ['r']];
					if (array_key_exists ( 'length', $acvu )) {
						if (isset ( $acvu ['xmode'] ) ) {
							foreach ( $acvu as $yi => &$yv ) {
								if(is_numeric($yi) && is_numeric($yv)){
									$this->finalrow[]='data';
								}
							}
						} else {
							foreach ( $acvu as $yi => &$yv ) {
								if (is_numeric ( $yi ) && is_array ( $yv ) && count ( $yv ) > 0) {
									//$this->finalrow = array_merge ( $this->finalrow, array_fill ( 0, count ( $yv ), 'data' ) );
									$this->finalrow = my_array_merge ( $this->finalrow, array_fill ( 0, count ( $yv ), 'data' ) );
									$fakeIn = true;
								}
							}
						}
					} else {
						foreach ( $acvu as &$prevs ){
							if (is_array ( $prevs ) > 0) {
								foreach ( $prevs as $yi => &$yv ) {
									if (is_numeric ( $yi ) && is_array ( $yv ) && count ( $yv ) > 0) {
										//$this->finalrow = array_merge ( $this->finalrow, array_fill ( 0, count ( $yv ), 'data' ) );
										$this->finalrow = my_array_merge ( $this->finalrow, array_fill ( 0, count ( $yv ), 'data' ) );
										$fakeIn = true;
									}
								}
							}
						}
					}

				}
			}
			foreach ( $pbl ['kids'] as &$pkid ) {
				$kidcount ++;
				$kval=$pkid['val'];
				if (count ( $pkid ['kids'] ) > 0) {
					$res += $this->countCols ( $pkid, ($level + 1) );
					if (count ( $pbl ['kids'] ) == $kidcount) {
						if (end ( $this->finalrow ) != $level) {
							$this->finalrow [] = $level;
							$added = true;
						}
					}
				} else {
					if (isset ( $kval ['redone'] )) {
						if(array_key_exists($level,$this->colFakes) && count($this->colFakes[$level]) > 0){
							$ulev=$level;
						}else {
							$ulev=$dateDetail['cols'];
						}
						//$ulev=$level;
						$lcolfakes = (isset ( $kval ['parid'] ) ? $this->colFakes [$ulev] [$kval ['parid']] : $this->colFakes[$ulev]);
						if (! is_array ( $lcolfakes )) {
							$lcolfakes = array ();
						}
						if (array_key_exists ( $kval['r'], $lcolfakes )) {
							if (isset ( $lcolfakes [$kval ['r']] ['xmode'] )) {
								foreach ( $lcolfakes [$kval ['r']] as $ii => &$iv ) {
									if (is_numeric ( $ii ) && is_numeric ( $iv )) {
										$this->finalrow [] = 'data';
										$fakeIn=true;
									}
								}
							} elseif($dateDetail['cols'] == 0) {
								foreach ( $lcolfakes [$kval ['r']] as $yi => &$yv ) {
									if (is_numeric ( $yi ) && is_array ( $yv ) && count ( $yv ) > 0) {
										//$this->finalrow = array_merge ( $this->finalrow, array_fill ( 0, count ( $yv ), 'data' ) );
										$this->finalrow = my_array_merge ( $this->finalrow, array_fill ( 0, count ( $yv ), 'data' ) );
										$fakeIn=TRUE;
									}
								}
							}
						}
					}
					$this->finalrow [] = 'data';
					++$res;
				}
			}
			if($kidcount > 0 && !$added && ($level -1) >= 0 /*&& $fakeIn === FALSE*/){
				$this->finalrow[]=($level-1);
			}
			//$this->finalrow[]=$level;
			++$res;
		}else{
			$res=1;
			$this->finalrow[]='data';
		}
		//$pbl['cols']=$res;
		return $res;
	}

	function pureCols($depth){
		$cdep=0;$rtot=0;
		$this->depth=($depth-1);
		if($depth  >= 1){
			for($bi=0,$bl = count($this->col_heads);$bi < $bl;$bi++){
				for($i=0,$il = count($this->col_heads[$bi]['kids']); $i < $il; $i++){
					$pblock=&$this->col_heads[$bi]['kids'][$i];
					$rows=$this->colSumm(1,$pblock);
					if($rows > 0){
						//$pblock['cols']=$rows;
						$rtot+=$rows;
					}else{
						//unset($this->col_heads[$bi]['kids'][$i]);
						array_splice($this->col_heads[$bi]['kids'],$i,1);
					}
				}
				//$this->col_heads[$bi]['cols']=$rtot;
			}
		}
	}

	function colSumm($lev, &$pbl) {
		$sum = 0;
		if ($lev < $this->depth) {
			if (count ( $pbl ['kids'] ) > 0) {
				for($i = 0,$l=count ( $pbl ['kids'] ); $i < $l; $i ++) {
					$dbl = &$pbl ['kids'] [$i];
					$sum += $this->colSumm ( ($lev + 1), $dbl );
				}
				//$pbl ['cols'] = ++$sum;
			} /*else {
			unset ( $pbl );
			}*/
		} else {
			$sum = 1;
			//perform building of final grid
			$this->toGrid($pbl['list']);
		}
		return $sum;
	}

	function privCol($colind, &$ucheck,$parent=false,$forceAll = false) {
		$cuniq = $this->uniques [$this->cols [$colind] ['id']];
		$crefs = $this->refs [$this->cols [$colind] ['id']];
		$ind = 0;
		$ccon=array();
		if(count($cuniq) > 0 && is_array($cuniq)){
			foreach ( $cuniq as $cid => &$cval ) {
				if(($this->sblanks && $cval['r'] === false) || $cval['r']!== false)	{
					$ccon [$ind] = array ('val' => $cval, 'list' => array (), 'cols' => 0,'kids'=>array() ,'parent'=> $parent,'head'=>$colind);
					if($forceAll === false){
						if(is_array($ucheck) && is_array($crefs[$cid])){
							//$cross=array_intersect($crefs[$cid],$ucheck);
							$cross=$this->my_array_intersect($crefs[$cid],$ucheck);
						}
						if(!is_array($cross)){
							$cross=array();
						}
					}else{
						$cross=$crefs[$cid];
					}

					$ccon[$ind]['list']=$cross;
					//$this->list = array_merge($this->list,$cross);
					//$this->list = my_array_merge($this->list,$cross); !!!!Commenteed since its not used
					/*foreach ( $crefs [$cid] as $crow ) {
					if (@in_array ( $crow, $ucheck )) {
					$ccon [$ind] ['list'][] = $crow;
					$this->llist [] = $crow;
					}
					}*/
					/*$met=array_values(array_intersect(array_values($crefs[$cid]), $ucheck));
					if(count($met) > 0){
					$ccon [$ind] ['list'] = array_merge($ccon[$ind]['list'],$met);
					$this->llist = array_merge($this->llist,$met); // changed list to llist
					}*/
					if (count ( $ccon [$ind] ['list'] ) > 0) {
						++$ind ;
					}
				}
			}
			if (isset($ccon[$ind]) && count ( $ccon [$ind] ['list'] ) == 0) {
				//unset($ccon [$ind]);
				array_splice($ccon,$ind,1);
			}
			return $ccon;
		}
	}

	function findCommonRows($a,$b){
		$t=array($a,$b);
		$final=$this->findCommon($t);
		if($this->srecords === true){
			$pures=array_diff($final,$this->allShownRows);
			if(is_array($this->recordCount[$this->cy])){
				$this->recordCount[$this->cy][$this->cx]=count($pures);
			}else {
				$this->recordCount[$this->cy]=count($pures);
			}
			$this->allShownRows = my_array_merge($this->allShownRows,$pures);
		}
		unset($t);
		return $this->clientCount($final);
	}

	function mergeCheck($arr, &$tarr){
		if(!$arr){
			$arr = array();
		}
		if(!$tarr){
			$tarr=array();
		}

		$xtar=array_diff($arr,$tarr);
		//$xtar=$this->my_array_diff($arr,$tarr);
		if(!is_array($xtar)){
			$xtar=array();
		}
		//$tarr=array_merge($tarr,$xtar);
		$tarr=my_array_merge($tarr,$xtar);
		//$tarr = array_unique($arr);

		return $tarr;//array_unique(array_values($arr));
	}

	function addItems($level,$part,$arr,$xparts,$rpart){
		$founds=array();
		$toadd=false;
		//hangArr add flag
		//1.Check for allowed rows matching in new set of rows
		if(!isset($this->bits[$rpart][$part]) || !is_array($this->bits[$rpart][$part])){
			$ztar0=array();
		}else{
			$ztar0=$this->bits[$rpart][$part]['list'];
		}
		$ztar=$ztar0;
		$res=$this->mergeCheck($arr, $ztar);
		if ($level > 0) {
			foreach ( $this->bits [$rpart] as $bid => &$bar ) {
				foreach ( $arr as $subs ) {
					if (in_array ( $subs, $bar['list'] )) {
						$founds [] = $bid;
					}
				}
			}
		}
		if(count($res) == 0){
			$ztar=false;
		}else{
			$toadd=true;
		}
		$this->hangArr=array('orig'=>$ztar0,'res'=>$ztar);
		return array('add'=>$toadd,'detect'=>$founds);
	}

	function landItems($level,$part,$arr,$xparts,$rpart){
		$toadd=false;

		if(!isset($this->bits[$rpart][$part]) || !is_array($this->bits[$rpart][$part])){
			$this->bits[$rpart][$part]=array('list'=>array(),'kids'=>array(),'rows'=>0,'visual'=>$part);
		}
		$res=$this->mergeCheck($arr,$this->bits[$rpart][$part]['list']);
		$founds=array();

		if ($level > 0) {
			foreach ( $this->bits [$rpart] as $bid => $bar ) {
				if(count($this->my_array_intersect($arr, $bar['list'])) > 0){
					$founds [] = $bid;
				}
			}
		}
		if(count($res) == 0){
			unset($this->bits[$rpart][$part]);
		}else{
			$toadd=true;
		}
		return array('add'=>$toadd,'detect'=>$founds);
	}

	function findParent(&$pblock,$curr,$depth){
		$found=false;
		$kidsCount = count($pblock['kids']);
		if($curr < $depth &&  $kidsCount > 0){
			for($i = 0 ;$i < $kidsCount ;$i++){
				$lpar=&$pblock['kids'][$i];
				$this->findParent($lpar,$curr+1,$depth);
			}
		}else{
			$nkids=$this->Kids($pblock['list']);
			if(count($nkids) > 0){
				$pblock['kids']=$nkids;
			}
		}

	}

	function Kids($parentList){
		$big=array();
		$xr=0;
		foreach ($this->localuns as $unid => &$unval) {
			if(($this->sblanks && $unval['r'] === false) || $unval['r'] !== false) {
				if(!array_key_exists($this->localid,$this->tcomp['rows'])){
					$carr=array('title'=>$unval['v'],'list'=>array(),'kids'=>array());
					$xr=count($big);
				}else{
					if(is_array($this->tcomp['rows'][$this->localid])){
						foreach ($this->tcomp['rows'][$this->localid] as $rowPart => &$rowkid){
							if(is_numeric($rowPart)){
								$r=$rowkid->cmp($unval,$unid);
								if(is_numeric($r) && $r >= 0){
									if(!array_key_exists($r,$big)){
										$carr=array('title'=>$rowkid->getRName($r),'list'=>array(),'kids'=>array(),'rowPart'=>$rowPart);
									}else{
										$carr=$big[$r];
									}
								}
							}
						}
					}else{
						$r=$this->tcomp['rows'][$this->localid]->cmp($unval,$unid);
						if(is_numeric($r) && $r >= 0){
							if(!array_key_exists($r,$big)){
								$carr=array('title'=>$this->tcomp['rows'][$this->localid]->getRName($r),'list'=>array(),'kids'=>array());
							}else{
								$carr=$big[$r];
							}
						}
					}
					$xr=$r;
				}
				if(is_numeric($xr) && $xr >= 0 ){
					foreach ($this->localrefs[$unid] as &$row) {
						//if(@in_array($row,$parentList) && !@in_array($row,$carr['list'])){
						if($this->binsearch($row,$parentList) && !$this->binsearch($row,$carr['list'])){
							$carr['list'][]=$row;
						}
					}
					if(count($carr['list']) > 0){
						$big[$xr]=$carr;
					}
				}
			}
		}
		return $big;
	}

	function validChilds($uid, $level, $block) {
		$this->localrefs = $this->refs [$uid];
		$this->localuns = $this->uniques [$uid];
		$this->localid=($level+1);
		if ($level == 0) {
			//$topkid = $this->bits [$block] ['list'];
			$nkids = $this->Kids ( $this->bits['rows'] [$block]['list'] );
			if (count ( $nkids ) > 0) {
				$this->bits['rows'] [$block] ['kids'] = $nkids;
			} /*else {
			//unset ( $this->bits['rows'] [$block] );

			}*/
		} else {
			for($i = 0,$l = count ( $this->bits['rows'] [$block] ['kids'] ); $i < $l; $i ++) {
				$topkid = &$this->bits['rows'] [$block] ['kids'] [$i];
				if ($level == 1) {
					if (count ( $topkid ['list'] ) > 0) {
						$nkids = $this->Kids ( $topkid ['list']);
						if (count ( $nkids ) > 0) {
							$topkid['kids']=$nkids;
						}/* else {
						unset ( $this->bits['rows'] [$block] );
						}*/
					}
				}elseif($level > 1){
					$this->findParent($topkid,1,$level);
				}
			}
		}
	}

	function rowSumm($lev, &$pbl) {
		$sum = 0;
		if ($lev < $this->depth) {
			if (count ( $pbl ['kids'] ) > 0) {
				foreach ($pbl['kids'] as &$pkid){
					//for($i = 0; $i < count ( $pbl ['kids'] ); $i ++) {
					if(count($pkid ) > 1){
						//$dbl = &$pbl ['kids'] [$i];
						$sum += $this->rowSumm ( ($lev + 1), $pkid );
					}
				}
				if($this->stots_rows){
					$pbl ['rows'] = ++$sum;
				}else{
					$pbl ['rows'] = $sum;
				}

			}/* else {
			unset ( $pbl );
			}*/
		} else {
			$sum = 1;
			//perform building of final grid
			$this->toGrid($pbl['list']);
			$pbl['rows']=$sum;
		}
		return $sum;
	}

	function findFakesUp(&$kr,$acl=0,$oldParent){
		global $dateDetail;
		$res=array();
		$valr=$kr['val']['r'];
		$row=$kr['val']['row'];
		if($kr['parent'] !== false && $kr['parent'] === $oldParent && $dateDetail['cols'] == 0){
			return ;
		}
		if(!is_array($this->colFakes[$row])){
			$this->colFakes[$row]=array();
		}
		if (isset ( $kr ['val'] ['redone'] ) && $kr ['val'] ['redone'] === true) {
			if (isset ( $kr ['val'] ['parid'] )) {
				$parid=$kr ['val'] ['parid'];
				if (@array_key_exists ( $valr, $this->colFakes[$row][$parid] )) {
					if (! $this->tcomp ['cols'] [$dateDetail ['cols']][$parid]->testYear ()) {
						$res = array_fill ( 0, $this->colFakes[$row][$parid] [$valr] ['length'], '-******-' );
					} elseif(is_array($this->colFakes [$row][$parid][$valr])) {
						$fks = $this->colFakes [$row][$parid][$valr];
						/*if (($fks ['finish'] - $fks ['init']) > 0) {
						$res = array_fill ( ($fks ['init'] + 1), ($fks ['finish'] - 1), 1 );
						}*/
						foreach ($fks as $ii => &$iv) {
							if(is_numeric($ii) && is_numeric($iv)){
								$res[]='-******-';
							}
						}
					}
				}
			} else {
				if (array_key_exists ( $valr, $this->colFakes[$row] )) {
					if (! $this->tcomp ['cols'] [$dateDetail ['cols']]->testYear ()) {
						$res = array_fill ( 0, $this->colFakes [$row][$valr] ['length'], '-******-' );
					} else {
						$fks = $this->colFakes [$row][$valr];
						/*if (($fks ['finish'] - $fks ['init']) > 0) {
						$res = array_fill ( ($fks ['init'] + 1), ($fks ['finish'] - 1), 1 );
						}*/
						foreach ($fks as $ii => &$iv) {
							if(is_numeric($ii) && is_numeric($iv)){
								$res[]='-******-';
							}
						}
					}
				} elseif (array_key_exists ( ($row - 1), $this->colFakes ) && count ( $this->colFakes [($row - 1)] )) {
					//$mrow = $row - 1;
					$mrow=$dateDetail['cols'];
					$valr = $kr ['parent'];
					if (array_key_exists ( $valr, $this->colFakes [$mrow] )) {
						if (! $this->tcomp ['cols'] [$dateDetail ['cols']]->testYear ()) {
							$res = array_fill ( 0, $this->colFakes [$mrow] [$valr] ['length'], '-******-' );
						} else {
							$fks = $this->colFakes [$mrow] [$valr];
							/*if (($fks ['finish'] - $fks ['init']) > 0) {
							$res = array_fill ( ($fks ['init'] + 1), ($fks ['finish'] - 1), 1 );
							}*/
							foreach ( $fks as $ii => &$iv ) {
								if (is_numeric ( $ii ) && is_numeric ( $iv )) {
									$res [] = '-******-';
								}
							}
						}
					}
				}
			}
		}else if(is_numeric($kr['parent']) && isset($this->acol_heads[($acl+1)])){
			$pid=$kr['parent'];
			$res=$this->findFakesUp($this->acol_heads[($acl+1)]['kids'][$pid],($acl+1),$oldParent);
		}
		return $res;
	}

	function toGrid($rlist){
		$pid=0;
		$oldParent=FALSE;
		if(count($this->acol_heads[$pid]) > 0 && count($this->acol_heads[$pid]['kids']) > 0){
			if(!isset($this->recordCount[$this->cy])){
				$this->recordCount[$this->cy]=array();
				$this->shownRows[$this->cy]=array();
			}
			$this->cx=0;
			foreach ($this->acol_heads[$pid]['kids'] as $kiid => &$krow){
				$tt=$this->findCommonRows($rlist,$krow['list'],0);
				$fcells=$this->findFakesUp($krow,0,$oldParent);
				$oldParent=$krow['parent'];
				if(!isset($this->grid[$this->cy])){
					$this->grid[$this->cy]=array();
				}
				if(isset($fcells) && count($fcells) > 0){
					//$this->grid[$this->cy]=array_merge($this->grid[$this->cy],$fcells);
					$this->grid[$this->cy]=my_array_merge($this->grid[$this->cy],$fcells);
					$temps=array_pop($this->recordCount[$this->cy]);
					$this->recordCount[$this->cy]=my_array_merge($this->recordCount[$this->cy],array_fill(0,count($fcells),0));
					$this->recordCount[$this->cy][]=$temps;
					unset($temps);
					$this->cx+=count($fcells);
				}
				if($tt === 0){
					$this->grid[$this->cy][]='&nbsp;';
					$this->recordCount[$this->cy][$this->cx]=0;
				}else{
					$this->grid[$this->cy][]=$tt;
					$this->gtotal+=$tt;
				}
				++$this->cx;
			}
		}else{
			if(count($rlist) == 0){
				$this->grid[$this->cy]='&nbsp;';
				$this->recordCount[$this->cy]=0;
			}else{
				$tt=$this->clientCount($rlist);
				$this->grid[$this->cy]=$tt;//count($rlist);
				$this->gtotal+=$tt;
				if($this->srecords === true){

					$pures=array_diff($rlist,$this->allShownRows);
					$this->recordCount[$this->cy]=count($pures);
					$this->allShownRows = my_array_merge($this->allShownRows,$pures);
				}
			}
		}

		++$this->cy;
	}

	function clientCount($arr){
		if(!$this->sunqs){
			return count($arr);
		}else{
			$pure=array();
			foreach ($arr as &$row){
				$id=$this->clids[$row];
				if(!in_array($id,$pure)){
					$pure[]=$id;
				}
			}
			return count($pure);
		}
	}

	function countRows($block,$depth){
		$cdep=0;$rtot=0;
		$this->depth=$depth-1;
		$once=false;
		$cleanset=array();
		if($depth  >= 1){
			$zpar=&$this->bits['rows'][$block];
			$zxkid=&$zpar['kids'];
			if(count($zxkid) > 0){
				foreach($zxkid as $kiid => &$pblock) {
					if(is_array($pblock) && count($pblock) > 1){
						$once=true;
						$rows=$this->rowSumm(1,$pblock);
						if($rows > 0){
							$pblock['rows']=$rows;
							$rtot+=$rows;
						}else{
							$cleanset[]=array($zxkid,$kiid,1);
						}
					}
				}
			}
			$zpar['rows']=$rtot;
		}
		if(!$once){
			$rows=$this->rowSumm(0,$zpar);
		}
		if(count($cleanset) > 0){
			foreach ($cleanset as &$cv) {
				array_splice($cv[0],$cv[1],$cv[2]);
			}
		}
	}

	function getToplevs(){
		return count($this->bits['rows']);
	}

	function liveKids($arr){
		$res=0;
		$a=array_filter($arr,'onlyArrs');
		$res=count($a);
		return $res;
	}

	function shortHeader($str){
		$length=15;
		if(strlen($str) > 15){
			$str=substr($str,0,15).'..';
		}
		return str_replace(' ','&nbsp;',$str);
	}

	function drawYRT($year,$parent,$parid = FALSE){
		global $extracola;
		$res='<th rowspan="'.($this->dateRowSpan).'" data-ptitle="'.$year.'">Total - '.($year).'</th>'."\n\t";
		if(!is_numeric($parid)){
			$this->yearRow[$year]++;
			$this->ctshown []=$year;
		}else{
			if(!is_array($this->yearRow[$parid])){
				$this->yearRow[$parid]=array();
			}
			$this->yearRow[$parid][$year]++;
			if(!is_array($this->ctshown[$parid])){
				$this->ctshown[$parid]=array();
			}
			$this->ctshown[$parid] []=$year;
		}
		$xpos=$this->frPos;
		$cur=$this->finalrow;
		$cur0=array_slice($cur, 0,$xpos);
		$cur1=array_slice($cur, $xpos);
		//$this->finalrow=array_merge($cur0,array('100'),$cur1);
		$this->finalrow=my_array_merge($cur0,array('100'),$cur1);

		$this->frPos++;
		if($parent !== false){
			$this->upRows[$parent]++;
		}
		return $res;
	}

	function finalHeadFakes(&$khead,$alt,$fresh,$parentId,$levID) {
		$res='';
		$oldyear=null;
		$khval=&$khead['val'];
		$fparid=$khval['parid'];
		if(isset($fparid)){
			$lcolFakes =  &$this->colFakes[$levID][$fparid];//[$levID][$fparid];
			$lctshown = &$this->ctshown[$fparid];
			$lcountedFakes = &$this->countedFakes[$fparid];
			$lyearRow=&$this->yearRow[$fparid];
		}else{
			$lcolFakes =   &$this->colFakes[$khval['row']];
			$lctshown = &$this->ctshown;
			$lcountedFakes = &$this->countedFakes;
			$lyearRow=&$this->yearRow;
		}
		if(!is_array($lcolFakes)){
			$lcolFakes=array();
		}
		if(!is_array($lctshown)){
			$lctshown=array();
		}
		if(!is_array($lcountedFakes)){
			$lcountedFakes=array();
		}
		if(!is_array($lyearRow)){
			$lyearRow=array();
		}
		$valr=$khval['r'];
		$fks=$lcolFakes[$valr];

		if (isset ( $khval ['redone'] ) && array_key_exists ( $valr, $lcolFakes )
		&& ((!in_array($valr, $lcountedFakes) && $fresh === TRUE ) || ($fresh === FALSE  && !in_array($valr,$lcountedFakes)))) {
			foreach ( $lcolFakes [$valr] as $item => &$vali ) {
				if(isset($fks['xmode']) && $fks['xmode'] == 'yrz'){
					if(is_numeric($item) && is_numeric($vali)){
						$res .= '<th>' . ($fresh === true ? $vali : '&nbsp;') . '</th>' . "\n\t";
						$this->frPos++;
						$this->upRows[$parentId]++;
					}
				}
				if (is_numeric ( $item ) && is_array ( $vali )) {
					if(!in_array(($item-1), $lctshown) && array_key_exists(($item-1), $lyearRow) && count($lctshown) > 0 && $this->stots_cols){
						$res.=$this->drawYRT(($item-1),$parentId,$fparid);
					}
					if($this->stots_cols && $fresh == true){
						if($oldyear != $item && !in_array($oldyear, $lctshown) && is_numeric($oldyear)){
							$res.=$this->drawYRT($oldyear,$parentId,$fparid);
							$oldyear=$item;
						}
						else if(is_null($oldyear) && count($vali) == $fks['max'] &&  !in_array(($item-1), $lctshown) ){
							$res.=$this->drawYRT(($item-1),$parentId,$fparid);

						}
						//if(!is_numeric($oldyear))
						$oldyear=$item;
					}
					if($fresh === TRUE){
						$lyearRow[$item] +=count ( $vali );
					}
					if(in_array($khead['val']['r'],$lcountedFakes) && $fresh === FALSE){
						return ;
					}
					foreach ( $vali as &$sv ) {
						$res .= '<th>' . ($fresh === true ? $sv : '&nbsp;') . '</th>' . "\n\t";
						$this->frPos++;
						$this->upRows[$parentId]++;
					}
					if($this->stots_cols && $fresh == true){
						if($oldyear != $item && !in_array($oldyear, $lctshown) && is_numeric($oldyear)){
							$res.=$this->drawYRT($oldyear,$parentId,$fparid);
							$oldyear=$item;
						}
						//if(!is_numeric($oldyear))
						$oldyear=$item;
					}
					/// Case when fake year is containing complete set of sub year items
					if(count($vali) == ($fks['max']) && !in_array($item, $lctshown) && $this->stots_cols){
						$res.=$this->drawYRT($item,$parentId,$fparid);
					}
					
				}
			}
			if($fresh === FALSE){
				$lcountedFakes[]=$valr;
			}
		}elseif (is_numeric($khead['parent']) && $alt > 0){
			$res=$this->finalHeadFakes($this->levcols[($alt-1)][$khead['parent']], ($alt-1),false,$parentId,($levID-1));
		}
		return $res;
	}

	function buildIt() {
		global $trows, $svals,$tcols,$dateDetail,$extracola;
		$this->list=$this->uniques=$this->allowed=$this->localuns=$this->localrefs=$this->refs=null;
		unset($this->list,$this->uniques,$this->allowed,$this->localuns,$this->localrefs,$this->refs);
		$this->cy=0;
		$head_bonus=1;
		$colDateGrouped=false;
		$colYear=false;
		$oldcolYear=false;
		$yearCase=null;
		$extracola=false;
		$yearUPcell=array();
		$prevhead = false;
		if($trows ==0 ){
			$ntrows=$tcols;
		}else{
		($tcols-1) > 0 ? $ntrows=($tcols-1) : $ntrows=1;
		}
		if($dateDetail['cols'] !== false && $this->allDates['cols'] !== true){
			if(is_array($this->tcomp['cols'][$dateDetail['cols']])){
				$colGroupMode=$this->tcomp['cols'][$dateDetail['cols']]['test']->testYear();
				$extracola=true;
			}else{
				$colGroupMode=$this->tcomp['cols'][$dateDetail['cols']]->testYear();
			}
			if(!$colGroupMode){
				++$ntrows;
			}
			$colDateGrouped=true;
		}
		if($tcols == 0 && $trows == 1){
			$ntcols=($trows+1);
			$head_bonus=2;
		}else{
			$ntcols=$trows;
		}
		if($tcols == 1 && $trows >= 1){
			$addnum=(count($this->finalrow)+2);
			$addd='<th colspan="#@!NTCOLS!@#" >&nbsp;</th></tr><tr>';
		}else{
			$addd='';
		}
		if(is_object($this->tcomp['rows'][$dateDetail['rows']])){
			$checkTestYear = $this->tcomp['rows'][$dateDetail['rows']]->testYear();
		}elseif (is_array($this->tcomp['rows'][$dateDetail['rows']])){
			$checkTestYear = $this->tcomp['rows'][$dateDetail['rows']][0]->testYear();

		}
		if($dateDetail['rows'] >= 0 && $dateDetail['rows'] !== false && !$checkTestYear ){
			$ntcols++;
			$utrc=1+$trows;
		}else{
			$utrc=$trows;
		}
		
		if(count($this->levcols) < count($this->cols)){//if(count($this->levcols) < $tcols ){
			$html='<table class="empty"><tr><td style="font-weight:bolder;font-size: 14pt;">No data to display '.count($this->cols).'</td></tr></table>';
			DiskStatCache($html);
			return ;
		}
		$html = '<table cellpadding=2 cellspacing=1 border="0" class="tbl sttable">'."\n";
		//$html.='###XCOLSCODE###';
		$html.='<thead><tr class="'.($addd != '' ? ' decor ': '').'"><th class="sblk " colspan="'.$ntcols.'" rowspan="' . $ntrows. '">Amount</th>'."\n\t".$addd.
		($dateDetail['cols'] == 0 ? '#@!XCODE!@#': '');
		$lc=0;
		$sprevs=array();$onprev=false;$gtshow=false;$levax=array();$finalrow=array();$allLevels=count($this->levcols)-1;
		$yearRow=array();
		$colsRows = array();
		if ($tcols > 0) {
			for ( $ci=0,$cl=count($this->levcols);$ci < $cl; $ci++) {
				$colsRows[$ci]=array();
				$shownColHeads=array();
				$levax[$ci]=array();
				$rx=0;$fx=0;$zx=0;$first=false;
				$ht=$this->levcols[$ci];
				if($lc > 0){
					$html.='<tr>'."\n\t";
				}
				if($ci == ($tcols-($dateDetail['cols'] == $ci ? 0 :1))){
					foreach ( $svals ['rows'] as $rid => &$rhead ) {
					($dateDetail['rows'] === $rid &&  !$this->tcomp['rows'][$dateDetail['rows']]->testYear()) ? $caddd=1: $caddd=0;
					$html .= '<th colspan="'.($head_bonus+$caddd).'" rowspan="'.($colGroupMode && $dateDetail['cols'] == $ci ? 2 : 1).'" class="sblk">' . $rhead ['title'] . '</th>'."\n\t";
					}
					$rshown=true;
				}
				if($dateDetail['cols'] > 0 && ($cl-1) == $ci){
					$html.='#@!XCODE!@#';
				}
				$ni=0;
				$cntc=$levax[($ci-1)][$rx];

				foreach ( $ht  as $hit => &$khead ) {
					if(isset($khead['val']['parid'])){
						$lcolFakes =  &$this->colFakes[$ci][$khead['val']['parid']];
						$lctshown = &$this->ctshown[$khead['val']['parid']];
						$vparid=$khead['val']['parid'];
					}else{
						$lcolFakes =   &$this->colFakes[$ci];
						$lctshown = &$this->ctshown;
					}
					if(!is_array($lcolFakes)){
						$lcolFakes=array();
					}
					if(!is_array($lctshown)){
						$lctshown=array();
					}

					$colcode=$ci.'_'.$hit;
					if ($dateDetail['cols'] > 0  ){
						$parentName=($ci-1).'_'.$khead['parent'];
						if(!isset($this->upRows[$parentName])){
							$this->upRows[$parentName]=$khead['cols'];
						}
					}else{
						$parentName = $khead['parent'];
						$colcode=($khead ['cols']  > 0 ? $khead['cols'] : 1);
					}



					if(($allLevels > 0 && $ci < $allLevels && count($khead['kids']) > 0) ||
					($allLevels > 0 && $ci == $allLevels && count($khead['kids']) == 0)	||
					$allLevels == 0  ){
						$vcolhead=$khead ['val'] ['v'];
                        if($vcolhead==-1)
                            $vcolhead = '0';
						$addColTotal=false;
						if($dateDetail['cols'] == $ci && isset($khead['val'])){
							if(isset($vparid)){
								$colYear=$this->tcomp['cols'][$ci][$vparid]->getYCell($khead['val']['r']);
								if(is_null($yearCase)){
									$yearCase=$this->tcomp['cols'][$ci][$vparid]->testYear();
								}
							}else{
								$colYear=$this->tcomp['cols'][$ci]->getYCell($khead['val']['r']);
								if(is_null($yearCase)){
									$yearCase=$this->tcomp['cols'][$ci]->testYear();
								}
							}
							if ($dateDetail ['cols'] == $ci) {
								if (isset ( $vparid )) {
									$this->yearRow [$vparid] [$colYear] += ($khead ['cols'] > 0 ? $khead ['cols'] : 1);
								} else {
									$this->yearRow [$colYear] += ($khead ['cols'] > 0 ? $khead ['cols'] : 1);
								}
							}

							if ($yearCase === false && $this->stots_cols) {
								if (! array_key_exists ( $khead ['val'] ['r'], $lcolFakes )) {
									if (is_numeric ( $oldcolYear ) && $oldcolYear != $colYear && ! in_array ( $oldcolYear, $lctshown ) && $khead['val']['parid'] == $prevhead['val']['parid']) {
										$html.=$this->drawYRT($oldcolYear,$parentName,$vparid);
										$addColTotal = true;
									}
								}
								$oldcolYear = $colYear;
							}

						}
						$html.=$this->finalHeadFakes($khead,$ci,true,(is_numeric($khead['parent']) ? $parentName : FALSE),$ci);  // in false case value was FALSE

						if(strstr($colcode,'_')){
							$colcode='#$%'.$colcode.'%$#';
						}

						//data-ocols  ($khead['cols'] > 1 && $this->stots_cols ) ? ($khead['cols']-1) : $khead['cols']
						//data-pid="'.($ci > 0 ? ($ci-1).'_'.$khead['parent'] : '').'"
						$html .= '<th data-ptitle="'.$vcolhead.'"  data-oid="'.$ci."_".$hit.'"  data-ocols="'.($colcode).
						'" colspan="' . $colcode . '" >' .$this->shortHeader($vcolhead.'-'. $this->cols[$ci]['title'] ). '</th>'."\n\t";
						$oldcolYear=$colYear;
						if($dateDetail['cols'] == $ci && $this->stots_cols === true){
							$this->frPos+= ($khead ['cols']  > 0 ? $khead['cols'] : 1);
						}
						if($khead['parent'] !== false){
							$this->upRows[$parentName]++;
						}

                        $aff = $khead ['val'] ['v'];
						if($aff==-1)
                            $aff = '0';
						$levax[$ci][$fx]=array('title'=>$this->shortHeader($aff.'-'.$this->cols[$ci]['title']),'amnt'=>$this->liveKids($khead ['kids']));
						if(($khead ['cols']-1) > 0 && $ci < (count($this->col_heads)-1) ){
							if(!$first){
								$lpos=0;
								$first=true;
							}else{
								$lpos=count($finalrow);
							}
							/*for($d=0;$d < ($khead ['cols']-1);$d++){
							$finalrow[($lpos+$d)]='data';
							}*/
							//$finalrow=array_merge($finalrow,array_fill(0, $khead['cols'], 'data'));
							$finalrow=my_array_merge($finalrow,array_fill(0, $khead['cols'], 'data'));
							//$finalrow[($lpos+$d)]=$ci;
							$finalrow[]=$ci;
						}
						$fx++;
						$zx++;
						$lc++;
						$ni++;
						if($ni == $cntc['amnt']){
							$nrc=($tcols - (is_numeric($dateDetail['cols']) && !$yearCase ?  $ci-1 : $ci));
							if($nrc == 0){
								$nrc=1;
							}
							$finalrow[$zx]=($ci-1);
							if ($this->stots_cols) {
								if ($yearCase === false && $dateDetail['cols'] > 0) {
									//if (! array_key_exists ( $khead ['val'] ['r'], $lcolFakes )) {
									if (is_numeric ( $colYear ) &&  ! in_array ( $colYear, $lctshown )) {
										$html .= $this->drawYRT ( $colYear, $parentName, $vparid );
										$addColTotal = true;
									}
									//}
									//$oldcolYear = $colYear;
									$yearUPcell[$vparid.'_'.$colYear]='<th rowspan="' . $nrc . '" class="head_tot">' . $cntc ['title'] . ' Total</th>' . "\n\t";
									$this->frPos++;
								}else{
									$html .= '<th rowspan="' . $nrc . '" class="head_tot">' . $cntc ['title'] . ' Total</th>' . "\n\t";
								}
							}
							$ni=0;
							$rx++;
							$zx++;
							$cntc=$levax[($ci-1)][$rx];
						}
					}
					$prevhead=$khead;
				}
				if(!$gtshow){
					$html.='<th rowspan="'.$tcols.'">Grand Total</th>'
					.($this->sperc_rows === true ? '<th rowspan="'.$tcols.'" class="jkdata">Percents</th>':'')
					.($this->srecords === true ? '<th rowspan="'.$tcols.'" class="jkdata">Records</th>':'')
					;
					$gtshow=true;
				}
				$html .= '</tr>'."\n";
			}

			//$xhcode=$rht;
			if(!$rshown){
				$rht='####@@@@@@####';
			}else{
				$rht='';
			}

			$leval_add = 1;
			krsort($this->upRows);
			$lups=array();
			foreach ($this->upRows as $levit => &$leval) {
				$parts=explode('_', $levit);
				if(strlen($parts[0]) > 0 && strlen($parts[1])){
					$lups[(($parts[0]-1).'_'.$parts[0])]+=$leval;
				}
				//$html=str_replace('#$%'.$parts[0].'_'.$parts[1].'%$#', $leval, $html);
				if(array_key_exists($levit, $lups)){
					$leval=$lups[$levit];
				}
				$html=str_replace('#$%'.$levit.'%$#', ($this->stots_cols ? ($leval+$leval_add) : $leval), $html);
			}
			$html=preg_replace('/#\$%\d{1,}_\d{1,}%\$#/', '1', $html);
			$fshown=current($lctshown);
			if(is_array($fshown)){

			}else{
				$addnum+=count($lctshown);
			}

			$html=str_replace('#@!NTCOLS!@#', $addnum, $html);
			if(count($this->yearRow) > 0 && $colGroupMode === false){
				if(is_array($this->tcomp['cols'][$dateDetail['cols']])){
					foreach ($this->yearRow as $ykey => &$yrow) {
						if (is_array ( $yrow )) {
							ksort ( $yrow );
							foreach ( $yrow as $yid => &$ycnt ) {
								$xhcode .= '<th colspan="' . $ycnt . '" data-ptitle="'.$yid.'">' . $yid . '</th>';
								if(array_key_exists($ykey . '_'.$yid, $yearUPcell)){
									$xhcode.=$yearUPcell[$ykey . '_'.$yid];
									unset($yearUPcell[$ykey . '_'.$yid]);
								}
							}
						}
					}
				}else{
					ksort($this->yearRow);
					foreach ($this->yearRow as $yid => &$ycnt) {
						$xhcode.='<th colspan="'. $ycnt.'" data-ptitle="'.$yid.'">'.$yid.'</th>';
						if(array_key_exists($ykey . '_'.$yid, $yearUPcell)){
							$xhcode.=$yearUPcell[$ykey . '_'.$yid];
							unset($yearUPcell[$ykey . '_'.$yid]);
						}
					}
				}
				if($yearCase || $dateDetail['cols'] ==  0){
					$xhcode.='<th>&nbsp;</th>';
				}elseif($dateDetail['cols'] > 0){
					$html=preg_replace('/<\/tr>$/' ,"<th>&nbsp;</th></tr>\n",trim($html));
				}
				$lastcols=0;
				if($this->sperc_cols === true){
					++$lastcols;
				}
				if($this->srecords === true){
					++$lastcols;
				}
				if($lastcols > 0){
					$xhcode.='<th colspan="'.$lastcols.'">&nbsp;</th>';
				}
				$xhcode.='</tr><tr>';
			}

			$html.='';
		} else {
			$html .= '<th rowspan="2">Grand Total</th>'.
			($this->sperc_rows === true ? '<th rowspan="2" class="jkdata">Percents</th>' : '').
			($this->srecords === true ? '<th rowspan="2" class="jkdata">Records</th>' : '')
			.'</tr><tr>';
		}
		$html=str_replace('#@!XCODE!@#', $xhcode.$rht, $html);
		if(!$rshown){
			if($tcols == 0 && $trows > 0){
				$html.='</tr><tr>';
				$htmladd='</tr>';
			}
			foreach ( $svals ['rows'] as $rid => &$rhead ) {  /// $this->tcomp['rows'][$dateDetail['rows']]->testYear()
			($dateDetail['rows'] === $rid && !$checkTestYear) ? $caddd=1: $caddd=0;
			$htmlx .= '<th data-ptitle="'.$rhead ['title'].'" colspan="'.($head_bonus+$caddd).'" class="missgr">' . $rhead ['title'] . '</th>';
			}
			$html=str_replace('####@@@@@@####', $htmlx, $html);
			$html.=$htmladd;
		}
		if($this->gtotal == 0){
			$this->gtotal = 1;
		}
		$html.='</thead><tbody>';
		//DiskStatCachePartial($html);
		$todeep=($trows-1);
		$rows='';
		$rowcount=array();
		$dateRowCount=array();
		$rti=0;
		$yearCode='';
		$lsumm=0;
		$rowsInYear=array();
		$childCount=0;
		$shownYear=0;
		$yearCode=array();
		$yearShown=array();
		$this->levcols=null;
		unset($ht);
		$prevId ='';
		$oytd = FALSE;
		$usekids = false;
		$foffset='all';
		foreach ($this->bits['rows'] as $bi => &$bblock) {
			$row='';
			if((count($bblock) > 1) && ((count($bblock['kids']) > 0 && $trows > 1 ) || $trows == 1)){
				$frs=$bblock['rows'];
				if(is_numeric($prevId)){
					$fakes = $this->tcomp['rows'][0]->injunker($prevId,$bi);
				}
				if($dateDetail['rows'] === 0){
					$yearTD=$this->tcomp['rows'][0]->getYCell($bi);
					$blankCase = strstr(strtolower($yearTD),'blanks');
					if($yearTD != $oytd && $shownYear != $yearTD){
						if(strlen($oytd) > 0 || strtolower($shownYear) === 'blanks'){
							if(!is_null($fakes)){
								$fcode=$this->drawFakeRow($fakes,$oytd);
								$row.=$fcode['code'];
								$foffset='sibling';
							}
							if($this->stots_rows){
								//$rowcount[$rti]+=count($rowsInYear);
								$rowcount[$rti]+=$childCount;

								$row.='<tr class="itog jkdata"><td class="rowhead sblk" colspan="'.$utrc.'" data-ptitle="'.$oytd.'">'.$oytd.' Total</td>';
								if(!$usekids){
									$row.='<td>'.$lsumm.'</td><td>'.$lsumm.'</td>';
									$lsumm=0;
								}else{
									//$row.=$this->drawVTRow($rowsInYear).'</tr>'."\n";
									$vtres=$this->drawVTRow($rowsInYear).'</tr>'."\n";
									$row.=$vtres[0];
									$rowsInYear=array();
								}
								$childCount=0;
							}
							$rti++;
						}
						$rowcount[$rti]=0;
						$oytd=$yearTD;
						if($blankCase){
							$colspans=' colspan="2"';
						}else{
							$colspans='';
						}
						$yearCode[$yearTD]= '<td class="rowhead sblk" '.$colspans.' data-ptitle="'.$yearTD.'" rowspan="#@#'.$rti.'#@#">'.$yearTD.'</td>';
					}
					if($blankCase){
						$bcolspans = ' colspan="'.($this->tcomp['rows'][0]->testYear() === true ?  1 : 2 ).'"';
					}else{
						$bcolspans='';
					}
				}
				if(!is_null($fakes)){
					if($fakes['init'] == $yearTD /*&& $fakes['begin'] != $fakes['end']*/){
						$rowcount[$rti]+=count($fakes[$fakes['init']]);
					}/*elseif($fakes['finish'] == $yearTD ){
					$rowcount[$rti]+=count($fakes[$fakes['finish']]);
					}*/
					else if($fakes['init'] == $shownYear  ){
						if(/*$fakes['begin'] != 'full' &&*/ $rti > 0){
							$rowcount[($rti-1)]+=count($fakes[$fakes['init']]);
						}
						/*if($fakes['end'] == 'hemi' && $fakes['finish'] == $yearTD){
						$rowcount[$rti]+=count($fakes[$fakes['finish']]);
						}*/
					}
					if($fakes['finish'] == $yearTD && $fakes['end'] != 'full' && $fakes['init'] != $fakes['finish']){
						$rowcount[$rti]+=count($fakes[$fakes['finish']]);
					}
				}
				if($this->titleBy['rows'] == 'id'){
					$param=$bi;
				}else{
					$param=$bblock['visual'];
				}
				if(!is_null($fakes)){
					if($fakes['finish'] == $yearTD && $fakes['end'] != 'full'){
						if($fakes['init'] == $fakes['finish']){
							$foffset='all';
							if($fakes['end'] == 'onset'){
								$incode=$this->drawFakeRow($fakes,'end');
								$rowcode=$incode['code'];
							}
						}else{
							$foffset = ($foffset == 'all' ? 'zero-middle' : 'middle');
						}

						$rcode=$this->drawFakeRow($fakes,$foffset);
						$row.=$rcode['code'];
						if($fakes['end'] == 'onset'){
							$incode=$this->drawFakeRow($fakes,'end');
							strlen($rowcode) == 0 ? $rowcode=$incode['code'] : '';
						}
					}
				}
				$row.='<tr>'.(!$yearShown[$yearTD]  ?  $yearCode[$yearTD] : '').$rowcode;
				if($colspans == ''){
					$xtmp=$this->tcomp['rows'][0]->getRName($param);
					if($xtmp==-1)
                        $xtmp = '0';
					$row.='<td class="rowhead ysub" data-ptitle="'.$xtmp.'" '.$bcolspans.' data-rtitle="'.$xtmp.'" rowspan="@!@'.$rti .'@!@">'.$xtmp.'</td>'."\n";
				}
				$yearShown[$yearTD]=true;
				$rowcode='';
				if(!is_null($fakes)){
					$rcode=$this->drawFakeRow($fakes,'end');
					$row.=$rcode['code'];
				}
				//$yearCode='';
				if(count($bblock['kids']) > 0 || count($this->finalrow) > 0){
					$th=$this->iterate($bblock,$todeep,0,$bi);
					if($th['text'] ==''){
						break;
					}
					if($th['total'] > 0 || $dateDetail['rows'] === 0){
						$childCount++;
						$shownYear=$yearTD;
						unset($yearCode[$yearTD]);
					}
					$usekids=true;
				}else{
					$uval=count($bblock['list']);
					$th=array(
					'text' => '<td class="vdata">'.$uval.'</td><td class="summr">'.$uval.'</td>'."\n"
					.($this->sperc_rows == true ? '<td class="perc">'.round((($uval*100)/$this->gtotal)).'%</td>' : '')
					.($this->srecords == true ? '<td class="summr">'.$this->recordCount[$bi].'</td>' : ''),
					'total'=> $uval,
					'pureRows'=>1);
					$usekids=false;
				}
				if($th['total'] > 0  || $dateDetail['rows'] === 0){
					/*$dateRowCount[$rti]+=$frs;
					$rowcount[$rti]+=$frs;*/
					$dateRowCount[$rti]+=$th['pureRows'];
					$rowcount[$rti]+=$th['pureRows'];
					$row.=$th['text']."\n\t";//</tr>
					if(!is_array($rowsInYear)){
						$rowsInYear = array();
					}
					if(is_array($th['row_id'])){
						//$rowsInYear=array_merge($rowsInYear,$th['row_id']);
						$rowsInYear=my_array_merge($rowsInYear,$th['row_id']);
					}
					$shownYear=$yearTD;
				}elseif($dateDetail['rows'] != 0){
					$row='';
				}elseif ($th['total'] === 0){
					$row='';
				}else{
					for ( $i=0,$l=($this->cellCount-1);$i < $l; $i++ ) {
						$row .= '<td class="vdata fk">'.(($i +1) > ($trows-1) ?  '0' : '&nbsp;').'</td>'."\n";
						$cnt ++;
					}
					$row .= '<td class="vdata fk summr">0</td>'."\n";
					$rowcount[$rti]++;
				}
				if($this->stots_rows && $row != '' && !strstr(strtolower($yearTD),'blanks')){
					$xtmp=$this->tcomp['rows'][0]->getRName($bi);
					$row.='<tr class=" jkdata itog"><td class="rowhead" colspan="'.($dateDetail['rows'] > 0 ? $utrc : $trows ).'" data-ptitle="'.$xtmp.'">'.$xtmp.' Total</td>';
					if(!$usekids){
						$row.='<td>'.count($bblock['list']).'</td><td>'.count($bblock['list']).'</td>'."\n";
						$lsumm+=count($bblock['list']);
					}else{
						//$row.=$this->drawVTRow($th['row_id'])."\n"; //'</tr>'.
						$vtres = $this->drawVTRow($th['row_id'])."\n"; //'</tr>'.
						$row.=$vtres[0];
					}
				}
				$this->rseen=array();
			}
			if($dateDetail['rows'] > 0 && $th['total'] > 0){
				$dateRowCount[$rti]+=$this->rowCount;
			}
			/*if($dateDetail === false){
			$dateRowCount[$rti]=1;
			}*/
			if($dateRowCount[$rti] > 2){
				preg_match('/@!@'.$rti.'@!@">([^<]*)</',$row,$umatch);
				if(count($umatch) >= 1){
					$dateRowCount[$rti]=$dateRowCount[$rti].'" title="'.$umatch[1];
				}
			}
			$row=str_replace('@!@'.$rti.'@!@',($dateRowCount[$rti] > 0 ? $dateRowCount[$rti] : 1),$row);
			$this->rowCount=0;
			$dateRowCount[$rti]=0;

			//$rows.=$row;
			DiskStatCachePartial($row);
			$prevId = $bi;

		}//end of bits iteration
		if(count($rowcount) > 0 && ($dateDetail['rows'] == 0 && $dateDetail['rows'] !== false)){
			if($this->stots_rows){
				//$rowcount[$rti]+=count($rowsInYear);
				$rowcount[$rti]+=$childCount;
				$row='<tr class="jkdata itog"><td class="rowhead" colspan="'.($utrc).'" data-ptitle="'.$oytd.'">'.$oytd.' Total</td>'."\n";
				if(!$usekids){
					$row.='<td>'.$lsumm.'</td><td>'.$lsumm.'</td>'."\n";
					$lsumm=0;
				}else{
					//$row.=$this->drawVTRow($rowsInYear).'</tr>'."\n";
					$vtres =$this->drawVTRow($rowsInYear).'</tr>'."\n";
					$row.=$vtres[0];
					$rowsInYear=array();
				}
				//$rows.=$row;
				DiskStatCachePartial($row);
			}
			DiskStatCacheSubstitute('#@#',$rowcount,$rti);
		}
		if(!$this->allDates['rows']){
			DiskStatCachePartial($html,true);
			//$html.=$rows;
		}else{
			DiskStatCache($html);
		}
		$html='';
		$lrow=array();
		$false = false;
		$lastTotal=$this->drawVTRow(array_keys($this->grid),true);
		if(count($this->brokenCols) > 0){
			DiskStatCacheSubstitute($this->brokenCols, $false , $false);
		}
		$html.='<tr class="jkdata itog"><td class="rowhead" colspan="'.$utrc.'"><b>Grand Total</b></td>'."\n";
		$html.=$lastTotal[1]."</tr>\n\t";
		if($this->sperc_cols == true){
			$html.='<tr class="jkdata"><td class="rowhead" colspan="'.$utrc.'" style="font-weight: normal !important;">Percents</td>'."\n";
			$html.=$lastTotal[0]."</tr>\n\t";
		}
		if($this->srecords === true){
			$html.='<tr class="jkdata itog"><td class="rowhead" colspan="'.$utrc.'"><b>Records</b></td>'."\n";
			$summm=array(0=>0);
			foreach ($this->recordCount as &$row_level) {
				if(is_array($row_level)){
					foreach ($row_level as $rcid => $rvalue) {
						$summm[$rcid]+=$rvalue;
					}
				}else{
					$summm[0]+=$row_level;
				}
			}
			$summRecords =0;
			foreach ($summm as $scell) {
				$html.='<td>'.$scell.'</td>';
				$summRecords+=$scell;
			}
			if($tcols> 0 || $trows != 2){
				$html.='<td>&nbsp;</td>'; // for grand total last columns
			}
			if($this->sperc_cols === true){
				$html.='<td>&nbsp;</td>';
			}
			$html.='<td>'.$summRecords.'</td>';
			$html.='</tr>';
		}
		$html.='</tbody></table>'."\n";
		DiskStatCachePartial($html);

		return ;//$html
	}

	function __destruct(){
		unset($this->finalrow);
	}

	function iterate(&$pbl,$depth,$level,$parent,$pstr=''){
		global $tcols,$trows,$dateDetail;
		$roundTotal = 0;
		$dcase=false;
		$html='';
		$utrc=$trows+1;
		$rowsInYear=array();
		$lsumm=0;
		$rti=0;
		$oytd = '';
		//$pstr=implode(':',$path);
		$lev_rows=array();
		$missed=0;
		$this->lowRowCount[$level]=array();
		$addTotalShown=false;
		if($level <= $depth ){
			++$missed;
			if(count($pbl['kids']) > 0){
				++$missed;
				$kk=0;
				foreach ($pbl['kids'] as $pid => &$pkid){
					$html_pre='';
					if(count($pkid) > 3){
						$tstr=$pstr.':'.$pid;
						if(!in_array($tstr,$this->rseen)){
							if($pkid['rows'] > 0){
								$frs=$pkid['rows'];
								$this->lowRowCount[$level][$pid]=$frs;
								$rstr=' rowspan="@!@'.$pid.'@!@"';
							}
							if($kk > 0 ){
								$nr='</tr><tr>';
							}/*elseif ($pid > 0 && $level == 0){
							$nr='<tr>';
							}*/else{
							$nr='';
							}
							if(($level+1) === $dateDetail['rows']){
								$letYearIn=false;
								if(is_object($this->tcomp['rows'][$dateDetail['rows']])){
									$yearTD=$this->tcomp['rows'][$dateDetail['rows']]->getYCell($pid,$parent.$pstr);
								}else{
									$yearTD=$this->tcomp['rows'][$dateDetail['rows']][$parent]->getYCell($pid,$parent.$pstr);
								}
								//$yearTD=$this->tcomp['rows'][$dateDetail['rows']]->getYCell($pid,$parent.$pstr);
								if($yearTD != ''){
									$row='';
									if(strlen($oytd) > 0 && $oytd != $yearTD){
										++$rti;
										$letYearIn = true;
										if($this->stots_rows){
											if($nr != ''){
												$nr='<tr>';
											}
											$row='<tr class="jkdata itog"><td  data-ptitle="'.$oytd.'"colspan="'.(($depth-$level)+1).'">'.$oytd.' Total</td>'."\n";
											//$row.=$this->drawVTRow($rowsInYear).'</tr><tr>'."\n";
											$vtres = $this->drawVTRow($rowsInYear).'</tr><tr>'."\n";
											$row.= $vtres[0];
											$rowsInYear=array();
											$this->rowCount++;
											$zlev=$level;
											//while ($zlev >= 0) {
											preg_match("/:(\d?)$/",$pstr,$clearPart);
											$this->lowRowCount[($level-1)][$clearPart[1]]++;
											//	$zlev--;
											//}
											//$this->lowRowCount[$level]++;
										}

									}
									if(strlen($oytd) === 0 || $letYearIn === true){
										$rowcount[$rti]=0;
										$oytd=$yearTD;
										$nr.=$row.'<td class="rowhead" rowspan="#@#'.$rti.'#@#">'.$yearTD.'</td>'."\n";
										$letYearIn=false;
									}
								}
							}
							$rowcount[$rti]+=$frs;
							$html_pre.=$nr/*.($level === 0 ? '##blks##' : '')*/;
							/*if($missed > 1 && $trows >= 1 && $kk >= 1 && $depth > 1 ){
							for ($tdc=1;$tdc < $missed; $tdc++){
							$html.='<td>&nbsp;</td>';
							}
							}*/
							$html_pre.='<td '.$rstr.' class="rowhead" data-ptitle="'.$pkid['title'].'">'.$pkid['title'].'</td>'."\n\t";
							$this->rseen[]=$tstr;
							if($level > $this->cellCount){
								$this->cellCount=$level;
							}
						}
						if($this->stots_rows){
							$vc=1;
						}else{
							$vc=0;
						}
						if(count($pkid['kids']) > 0 && $pkid['rows'] > $vc){
							$th=$this->iterate($pkid,$depth,($level+1),$parent,$tstr);
							if($th['text'] != ''){
								$html.=$html_pre./*'<tr>'.*/$th['text'].'</tr>'."\n";
								//$lev_rows=array_merge($lev_rows,$th['row_id']);
								$lev_rows=my_array_merge($lev_rows,$th['row_id']);
								//$rowsInYear=array_merge($rowsInYear,$th['row_id']);
								$rowsInYear=my_array_merge($rowsInYear,$th['row_id']);
								$roundTotal+=$th['total'];
								$kk+=$th['pureRows'];
								$this->lowRowCount[$level][$pid] = $th['pureRows'];
								$dcase=false;
							}else{
								$html_pre='';
								$dcase=true;
								$rowcount[$rti]-=$frs;
								$this->lowRowCount[$level][$pid]-=$frs;
							}


						}else{
							if($level < $depth){
								if($tcols > 0){
									$utr=(($depth-1) + ($tcols -1));
									if($trows > 0){
										$utr=0;
									}
								}else{
									$utr=($depth-1);
								}

								for($tdf=$level; $tdf < $utr;$tdf ++){
									$html.='<td>&nbsp;</td>';
								}
							}
							$th=$this->drawGData();
							if($th['text'] != '' ){
								if($th['total'] > 0){
									$roundTotal+=$th['total'];
									$html.=$html_pre.$th['text'];
									$lev_rows[]=$th['row_id'];
									$rowsInYear[]=$th['row_id'];
									++$kk;
									$dcase=false;
								}else {
									$html_pre='';
									$rowcount[$rti]-=$frs;
									$this->lowRowCount[$level][$pid]-=$frs;
								}
							}else{
								$html='';
								$dcase=true;
							}
						}
					}
				}
				if($dateDetail['rows'] === ($level+1)){
					if($this->stots_rows && $oytd != ''){
						if($nr != ''){
							$nr='</tr>';
						}
						$row='<tr class="jkdata itog"><td colspan="'.(($depth-$level)+1).'">'.$oytd.' Total</td>'."\n";
						//$row.=$this->drawVTRow($rowsInYear).'</tr>'."\n";
						$vtres = $this->drawVTRow($rowsInYear).'</tr>'."\n";
						$row.= $vtres[0];
						$rowsInYear=array();
						$this->rowCount++;
						//$rowcount[$rti]++;
						$html.=$row;
					}
				}
				if($this->stots_rows && !$dcase){
					if($level > 0){
						$ncl=$depth-($level);
						/*if($ncl < 1){
						$ncl=1;
						}*/
						if(($level+1) <= $dateDetail['rows']){
							$ncl+=2;
						}
						$html.="<tr>\n".'<td colspan="'.($ncl > 0 ? $ncl : 1 ) . '">'.$pbl['title'].' Total</td>';
						//$html.=$this->drawVTRow($lev_rows).'</tr>'."\n";
						$vtres = $this->drawVTRow($lev_rows).'</tr>'."\n";
						$html.=$vtres[0];
					}
				}
			}
		}
		if($missed < 2){
			if($pbl['title']!=''){
				$html.='<td>'.$pbl['title'].'</td>'."\n";
			}
			$th=$this->drawGData();
			++$kk;
			if($level > 0){
				$nr='</tr><tr>."\n"';
			}else{
				$nr='';
			}
			$html.=$nr.$th['text']."</tr>\n";
			$lev_rows[]=$th['row_id'];
			$rowsInYear[]=$th['row_id'];
			$roundTotal+=$th['total'];

		}
		if($dcase && $roundTotal === 0){
			$html='';
		}
		$ttx='';
		if(strlen($html) > 0){
			for($ix=0,$il=($rti +1);$ix < $il; $ix++){
				if($rowcount[$ix] > 2){
					preg_match('/#@#'.$ix.'#@#">([^<]*)</',$html,$umatch);
					if(count($umatch) >= 1){
						$rowcount[$ix]=$rowcount[$ix].'" title="'.$umatch[1];
					}
				}
				$html=str_replace('#@#'.$ix.'#@#',$rowcount[$ix],$html);

			}
			foreach ($this->lowRowCount[$level] as $lkey => &$lval){
				if($lval > 2){
					preg_match('/@!@'.$lkey.'@!@">([^<]*)</',$html,$umatch);
					if(count($umatch) >= 1){
						$lval=$lval.'" title="'.$umatch[1];
					}
				}
				$html=str_replace('@!@'.$lkey.'@!@',$lval,$html);
			}
			if($level < $this->cellCount){
				for ($icn=$level,$l = $this->cellCount;$icn < $l;$icn++){
					$ttx.='<td>&nbsp;</td>'."\n";
				}
			}
			$html=str_replace('##blks##',$ttx,$html);
		}
		$this->lowRowCount[$level]=array();
		return array('text'=>$html,'row_id'=>$lev_rows,'total'=>$roundTotal /*$th['total']*/,'pureRows' => $kk);
	}

	function drawFakeRow(&$arr, $mode = 'all') {
		global $trows,$tcols;
		$html='';
		$cellset= array('head'=>'','body'=>array(),'tail'=>'');
		$cnt = 0;
		$inside = false;
		if ($arr ['init'] === $arr ['finish']) {
			$inside = true;
			if (is_numeric ( $mode ) && $arr ['init'] != $mode) {
				$inside = false;
			}
		}
		if($trows > 1){
			$ucolcnt=(($this->cellCount-1) + ($trows-1));
		}else{
			$ucolcnt = ($this->cellCount-1);
		}
		if($ucolcnt < 0 ){
			$ucolcnt = 1;
		}

		if ($arr ['xmode'] == 'yrz') {
			foreach ($arr as &$val) {
				if(is_numeric($val) && !in_array($val, $arr['seen']) && $val > 0){
					$tcode="<tr>\n\t".'<td  class="fk rowhead" data-ptitle="'.$val.'">'.$val.'</td>'."\n";
					$html.=$tcode;
					$cellset['head']=$tcode;

					for ( $i=0;$i < ($ucolcnt); $i++ ) {
						$html .= '<td class="vdata fk">0</td>'."\n";
						$cellset['body'][]='<td class="vdata fk">0</td>'."\n";
						++$cnt ;
					}
					$html .= '<td class="vdata fk summr">0</td>'."\n";
					$cellset['body'][]='<td class="vdata fk summr">0</td>'."\n";
					if($this->srecords === true && $trows === 1 && $tcols === 0){
						$html.='<td>&nbsp;</td>';
					}
					$html .= "</tr>\n";
					$arr['seen'][]=$val;
				}
			}
		} else {
			foreach ( $arr as $yr => &$item ) {
				if (is_array ( $item ) && ! in_array ( $yr, $arr ['seen'] ) && is_numeric ( $yr ) && (($mode == 'all' && $inside === true) || $mode == 'end' || ($mode == $yr && is_numeric ( $mode )) || ($mode === 'sibling' && $yr > $arr ['init']) || ($mode === 'middle' && $yr < $arr ['finish'] && $yr >= $arr ['init']) || ($mode === 'zero-middle' && $yr < $arr ['finish']))) {
					$yearCode = (count ( $item ) == $arr ['max'] ? ' <td rowspan="' . $arr ['max'] . '" class="rowhead" data-ptitle="'.$yr.'">' . $yr . '</td>' : '');
					foreach ( $item as &$sub ) {
						$tcode = ($mode === 'end' ? '' : '<tr>') . $yearCode . '<td class="fk rowhead" data-ptitle="'.$sub.'">' . $sub . '</td>'."\n";
						$html .= $tcode;
						$cellset['body'][]=$tcode;
						$yearCode = '';
						$trhd=' rowhead ';
						for ( $i=0;$i < ($ucolcnt); $i++ ) {
							$tcode = '<td class="'.$trhd.' vdata fk">'.(($i +1) > ($trows-1) ?  '0' : '&nbsp;').'</td>'."\n";
							$html .= $tcode;
							$cellset['body'][]=$tcode;
							$trhd='';
							++$cnt ;
						}
						$tcode = '<td class="vdata fk summr">0</td>'."\n";
						$html .= $tcode;
						$cellset['body'][] = $tcode;
						if($this->srecords === true && $trows === 1 && $tcols === 0){
							$html.='<td>&nbsp;</td>';
						}
						$html .= '</tr>'."\n";
						$cellset['tail']='</tr>'."\n";
					}
					//unset($arr[$yr]);
					$arr ['seen'] [] = $yr;
				}
			}
		}
		return array ('code' => $html, 'total' => $cnt ,'vset'=>$cellset,'pureRows'=>$cnt);
	}

	function drawVTRow($tar,$last=false){
		global $trows,$tcols;
		$res=array();
		$html=array(0=>'',1=>'');
		$cellset = array('head'=>'','body'=>array(),'tail'=>'');
		$row_tot=0;
		$rcn=count($this->cols);
		$ltots=array();
		$ax=0;
		foreach ($tar as &$row) {
			if(is_array($this->grid[$row])){
				foreach ($this->grid[$row] as $xp => &$cell) {
					if($trows > 0){
						$res[$xp]+=$cell;
					}else{
						if(is_numeric($cell) && !is_numeric($res[$xp]) || is_null($res[$xp])){
							$res[$xp]=$cell;
						}
					}
				}
			}else{
				$res[0]+=$this->grid[$row];
			}
		}

		foreach ($res as $x => &$td) {
			$finax=$this->finalrow[$ax];
			if($finax == 'data' || count($this->finalrow) == 0){
				if(is_null($td) || strlen($td) == 0 || $td == 0){
					$td='&nbsp;';
					if($last === true){
						$this->brokenCols[]=$ax;
					}
				}
				$html[1].='<td>'.$td.'</td>'."\n\t";
				$cellset ['body'][] ='<td>'.$td.'</td>'."\n\t";
				if(is_numeric($td) && $td > 0 ){
					$localPercent=round((($td*100)/$this->gtotal));
					$row_tot+=$td;
					for($v=0;$v < $rcn;$v++){
						$ltots[$v]+=$td;
					}
					if($this->stots_cols){
						$ltots['100']+=$td;
					}
				}else{
					$localPercent=$td;
					$td=0;
				}
				$tcode='<td class="perc">'.$localPercent.'%</td>'."\n\t";
				$html[0].=$tcode;
				$cellset['body'][]=$tcode;

				$this->bgrid[$this->by][]=$td;
				++$ax;

				while (is_numeric($this->finalrow[$ax])) {
					$finax=$this->finalrow[$ax];
					$tht=$ltots[$finax];
					if(!$tht || strlen($tht) == 0 || $tht == 0){
						$tht='&nbsp;';
						if($last === true){
							$this->brokenCols[]=$ax;
						}
					}else{
						$localPercent=round((($tht*100)/$this->gtotal));
					}
					if($this->stots_cols){
						$html[1].='<td>'.$tht.'</td>'."\n\t";
						$cellset['body'][]='<td>'.$tht.'</td>'."\n\t";
						$html[0].='<td class="perc">'.$localPercent.'%</td>'."\n\t";
					}
					$this->bgrid[$this->by][]=$td;
					$ltots[$finax]=0;
					++$ax;
				}


			}
		}

		if(!($tcols == 0 && $trows > 1)){
			$html[1].='<td class="summr">'.$row_tot.'</td>';
			$cellset['body'][]='<td class="summr">'.$row_tot.'</td>';
			$html[0].=($this->sperc_rows === true ? '<td class="perc">'.(round((($row_tot*100)/$this->gtotal))).'%</td>': '')
			.($this->srecords === true ? '<td class="summr">&nbsp;</td>': '');
		}
		if($this->gtotal == 0){
			$this->gtotal=1;
		}
		if ($tcols > 0 && $trows ==0) {
			$percent='100';
		}else{
			$percent=round((($row_tot*100)/$this->gtotal));
		}
		$tcode = ($this->sperc_rows === true ? '<td class="perc">'.$percent.'%</td>' : '')
		.($this->srecords === true ? '<td class="summr">&nbsp;</td>' : '')
		."\n\t";
		$html[1].= $tcode;
		$cellset['body'][] = $tcode;
		$html[0].='<td>&nbsp;</td>'."\n\t";
		if($last === true){
			return $html;
		}else{
			return array($html[1],$cellset);
		}
	}


	function drawGData($last=false){
		global $tcols,$trows;
		$cellset = array();
		$html='';
		$row_tot=0;
		$cells=0;
		$rcn=count($this->cols);
		$ltots=array();
		$ax=0;
		if(!$last){
			$utar=$this->grid[$this->cy];
		}else{
			$utar=$last;
		}
		if(!is_null($utar) && !is_array($utar)){
			$utar=array($utar);
		}
		if(is_array($utar)){
		    foreach ($utar as $x => &$td){
				$fk=FALSE;
				if($td === '-******-'){
					$td='0';
					$fk=true;
				}
				else if(is_null($td) || strlen($td) == 0 || $td == 0){
					$td='&nbsp;';
				}
				$finax=$this->finalrow[$ax];
				if($finax == 'data' || count($this->finalrow) == 0){
				    $perscsym = '';
				    $temptd = $td;
				    if(($this->sperc_cols || $this->sperc_rows) && is_numeric($td)) {
                        $td = round((($td * 100) / array_sum($utar)));
                        $perscsym = '%';
                    }
					$html.='<td class="vdata '.($fk === true ? 'fk' : '').'">'.$td.$perscsym.'</td>'."\n\t";
					$cellset[]='<td class="vdata '.($fk === true ? 'fk' : '').'">'.$td.$perscsym.'</td>'."\n\t";
					$td = $temptd;
					$cells++;
					if(is_numeric($td) && $td > 0){
						$row_tot+=$td;
						for($v=0;$v < $rcn;$v++){
							$ltots[$v]+=$td;
						}
						if($this->stots_cols){
							$ltots['100']+=$td;
						}
					}else{
						$td=0;
					}
					$this->bgrid[$this->by][]=$td;
					++$ax;

					while (is_numeric($this->finalrow[$ax])) {
						$finax=$this->finalrow[$ax];
						$tht=$ltots[$finax];
						if(!$tht || strlen($tht) == 0 || $tht == 0){
							$tht='&nbsp;';
						}
						if($this->stots_cols){
							$html.='<td class="tcol">'.$tht.'</td>'."\n\t";
							$cellset[]='<td class="tcol">'.$tht.'</td>'."\n\t";
							++$cells;
						}
						$this->bgrid[$this->by][]=$td;
						$ltots[$finax]=0;

						++$ax;
					}
				}
			}
		}else{
			$html='<td>&nbsp;</td>';
			$cellset[]='<td>&nbsp;</td>';
			++$cells;
			if($tcols > 0){
				return array('text'=> '','row_id'=>false);
			}
		}
		if(!($tcols == 0 && $trows > 1)){
			$html.='<td class="summr">'.$row_tot.'</td>';
			$cellset[]='<td class="summr">'.$row_tot.'</td>';
			++$cells;
		}
		if($this->gtotal == 0){
			$this->gtotal=1;
		}
		if($this->sperc_rows === TRUE){
			$dnum=round((($row_tot*100)/array_sum($utar)));
			$html.='<td class="perc">'.$dnum.'%</td>' ;
			$cellset[]='<td class="perc">'.$dnum.'%</td>';
			++$cells;
		}
		if($this->srecords === TRUE){
			$html.='<td class="summr">'.(is_array($this->recordCount[$this->cy]) ? array_sum($this->recordCount[$this->cy]) : (int)$this->recordCount[$this->cy]).'</td>' ;
			$cellset[]='<td class="summr">'.(is_array($this->recordCount[$this->cy]) ? array_sum($this->recordCount[$this->cy]) : (int)$this->recordCount[$this->cy]).'</td>' ;
			++$cells;
		}
		$html.="\n\t";
		$this->cellCount=$cells;
		$this->bgrid[$this->by][]=$row_tot;
		++$this->cy;
		++$this->by;
		return array('text'=>$html,'row_id'=>($this->cy-1),'total'=>$row_tot,'cells'=>$cellset);
	}
}


class Ranger {

	private $type;
	private $periods;
	private $vperiods=array();
	private $once=false;
	private $name;
	private $cnt=0;
	private $valid;
	private $numberSet=array();
	private $levels=array();
	private $actualPeriod;
	private $lshown=array();
	private $ind=0;
	private $lstore=array();
	private $temp=array();
	private $dob_case;
	private $impart='';
	private $rowList = array();
	public  $intCount=0;

	function __construct($type,$rngs,$name,$bpart){
		$this->type=$type;
		//$this->periods=$rngs;
		$this->name=$name;
		$test=true;
		if(!$rngs){
			$test=false;
		}
		else if(is_array($rngs) && count($rngs) > 0){
			$allStrings=false;
			foreach ($rngs as &$value) {
				if(!(!is_null($value) || is_array($value) || $value != '')){
					1;
				}else{
					$this->periods[]=$value;
					if(is_string($value) && !is_numeric($value)){
						$allStrings = true;
					}
				}
			}
			if($allStrings === true){
				$this->periods=array(0=>join(" to ",$this->periods));
			}
		}elseif (is_string($rngs)){
			$this->periods=$rngs;
		}
		if(count($this->periods) == 0 || (!is_array($this->periods) && $this->periods == '')){
			$test=false;
		}
		$this->valid=$test;
		if(strstr(strtolower($this->name),'dob')){
			$this->dob_case=true;
		}else{
			$this->dob_case=false;
		}
		$this->impart=$bpart;
	}

	function isValid(){
		return $this->valid;
	}

	function isRealValid(){
		$res=false;
		if($this->periods != 'none'){
			$res = true;
		}
		return $res;
	}

	function testYear() {
		return ($this->actualPeriod === 'annually' ? true : false);
	}

	function cancel(){
		if($this->temp['kpos'] !== false){
			--$this->cnt;
		}else{
			--$this->cnt;
			unset($this->lstore[$this->cnt]);//=$this->temp['ltstore'];//$ltstore;
			unset($this->vperiods[$this->cnt]);//=$this->temp['res'];//$res;
			if($this->actualPeriod != 'annually'){
				unset($this->levels[$this->cnt]);//=$this->temp['resy'];//$resy;
			}
		}
	}

	function importNew($arr){
		if($arr!== false){
			$kpos=array_search($arr['lstore'], $this->lstore);
			if($kpos === false){
				$this->lstore[$this->cnt]=$arr['lstore'];
				$this->vperiods[$this->cnt]=$arr['vperiods'];
				if($this->actualPeriod != 'annually'){
					$this->levels[$this->cnt]=$arr['levels'];
				}
				++$this->cnt;
			}
			return ($kpos !== false ? $kpos : ($this->cnt-1));
		}
	}

	function getLast(){
		$res=FALSE;
		if($this->temp['kpos'] !== false){
			--$this->cnt;
		}else{
			--$this->cnt;
			$res=array('lstore'=>$this->lstore[$this->cnt],'vperiods'=>$this->vperiods[$this->cnt],'levels'=>$this->levels[$this->cnt]);

			unset($this->lstore[$this->cnt]);//=$this->temp['ltstore'];//$ltstore;
			unset($this->vperiods[$this->cnt]);//=$this->temp['res'];//$res;
			if($this->actualPeriod != 'annually'){
				unset($this->levels[$this->cnt]);//=$this->temp['resy'];//$resy;
			}
		}
		return $res;
	}

	function cmp($val,$ind){
		global  $dateDetail;
		$res=false;
		if($this->type == 'number' || $this->dob_case){
			if($this->dob_case){
				$val=calcIt($val['v']);
			}
			if (count ( $this->periods ) > 0 && $this->valid && $val >= 0) {
				for($i = 0; $i < count ( $this->periods ); $i ++) {
					$tper = $this->periods [$i];
					//for numeric ranges start <= val && end > val
					if (is_numeric ( $val ['r'] )) {
						//if ($tper ['s'] <= $val ['r'] && $tper ['e'] > $val ['r']) {
						//now more complicated periods with possible endless loop limit
						if(
						((is_numeric($tper['s']) && $tper ['s'] <= $val ['r']) || (trim((string)$tper['s']) === '' || $tper['s'] === 0)) &&
						((is_numeric($tper ['e']) && $tper ['e'] > $val ['r']) || (trim((string)$tper['e']) === '' || $tper['e'] === 0))
						){
							if (trim ( $tper ['n'] ) == '') {
								if((int)$tper['e'] > 0 && (string)$tper['s'] != ''){
									$this->vperiods [$i] = $tper ['s'] . ' - ' . $tper ['e'];
								}elseif((int)$tper['e'] === 0){
									$this->vperiods [$i] = ' > '.$tper['s'];
								}elseif ((string)$tper['s'] == ''){
									$this->vperiods [$i] = ' < '.$tper['e'];
								}
							} else {
								$this->vperiods [$i] = $tper ['n'];
							}
							return $i;
						}
					} else {
						return false;
					}
				}
			}else{
				if($this->dob_case){
					$xpos=array_search($val['r'],$this->numberSet);
					if($xpos === false){
						$this->numberSet[]=$val['r'];
						$this->vperiods[]=$val['v'];
						$xpos=(count($this->vperiods)-1);
					}
					$dateDetail[$this->impart]=false;
					return $xpos;
				}else{
					return false;
				}
			}

		}elseif ($this->type == 'date'){
			if($val['r'] === false){
				$this->vperiods[-1]=$val['v'];
				$this->lstore[$this->cnt]=0;
				$this->levels[$this->cnt]=$val['v'];
				++$this->cnt;
				$res=-1;
			}else if((int)$val['r'] > 0){
				$tt=explode('-',$val['v']);
				if((int)$tt[0] > 0){
					$ztime=mktime(0,0,0,$tt[1],$tt[2],$tt[0]);
				}else{
					$ztime=0;
				}
				if(strlen($this->periods[0]) >= 4){
					$ustr=$this->periods[0];
				}else{
					$ustr=$this->periods;
				}
				$item=false;
				$this->actualPeriod=$ustr;
				if(/*$ustr == 'annually' ||*/ $ustr=='none'){
					$dateDetail[$this->impart]=false;
				}
				switch ($ustr) {
					case 'weekly':
						$resw=date('W',$ztime);
						if($tt[1] == 12 && $resw < 45){
							$resw = date("W",mktime(1,1,1,$tt[1],($tt[2]-7),$tt[0]));
						}
						if($tt[1] == 1 && $resw > 1 && $tt[2] < 7){
							$resw = date("W",mktime(1,1,1,$tt[1],($tt[2]+7),$tt[0]));
						}
						$resy=date('Y',$ztime);
						$ltstore=$resy.$resw;
						//$res='Week '.$resw.' of '.$resy;
						$res='Week '.$resw;
						$item=$resw;
						break;
					case 'monthly':
						$resm=date('m',$ztime);
						$resy=date('Y',$ztime);
						$ltstore=$resy.$resm;
						//$res=date('M  Y',$ztime);
						$res=date('M',$ztime);
						$item=$resm;
						break;
					case 'quarterly':
						$md=date('n',$ztime);
						$resy=date('Y',$ztime);
						if($md > 0 && $md < 4){
							$rs='1st';
							$ladd="1";
						}elseif ($md >=4 && $md < 7){
							$rs='2nd';
							$ladd="2";
						}elseif ($md >=7 && $md < 10){
							$rs='3rd';
							$ladd="3";
						}elseif ($md >=10 && $md <= 12){
							$rs='4th';
							$ladd="4";
						}
						$res=$rs.'&nbsp;quarter';
						$item=$ladd;
						$ltstore=$resy.$ladd;
						break;
					case 'annually':
						$ltstore=$res=date('Y',$ztime);
						break;
					case 'none':
						$ltstore=$val['r'];
						$res=$val['v'];
					default:
						break;
				}
				$kpos=array_search($ltstore,$this->lstore);
				$this->temp=array('kpos'=>$kpos);
				if($kpos === false){
					$this->lstore[$this->cnt]=$ltstore;
					$this->vperiods[$this->cnt]=$res;
					if($ustr != 'annually'){
						$this->levels[$this->cnt]=$resy;
					}
					$res=$this->cnt++;
				}else{
					$res=$kpos;
					//$this->levels[]++;
				}
				/*if(count($rowcount) > 0 && $dateDetail){
				for($ix=0; $ix < ($rti +1);$ix++){
				$rows=str_replace('#@#'.$ix.'#@#',$rowcount[$ix],$rows);
				}
				}*/
			}
		}else if ($this->type ==='string'){
			//place to add ranging on CHANGE COUNT
			if(is_array($this->periods) && count($this->periods) > 0){
				if(in_array($val['v'],$this->periods)){
					$res=0;
				}else{
					$res=false;
				}
			}else{
				$this->vperiods[]=$val['v'];
				$res=$ind;
			}
		}
		return $res;
	}

	function listR($l){
		$this->rowList=$l;
	}

	function getRName($id){
		return $this->vperiods[$id];
	}

	function getAllPeriods (){
		return $this->vperiods;
	}

	function tellPeriod($vp = NULL){
		if(is_null($vp)){
			return $this->actualPeriod;
		}else{
			$this->actualPeriod=$vp;
		}
	}

	function getYCell($id,$block=false){
		$res='';
		$ystr=0;
		if(count($this->levels) > 0){
			$ystr=$this->levels[$id];
			if($block !== false){
				if(!is_array($this->lshown[$block])){
					$this->lshown[$block]=array();
				}
				$utar=&$this->lshown[$block];
			}else{
				$utar=&$this->lshown;
			}
			if(!in_array($ystr,$utar)){
				$utar[]=$ystr;
				++$this->ind;
				$res=$ystr;
			}
		}else{
			$res='';
		}
		return $ystr;//$res;
	}

	function injunker($oldid, $curid){
		if($this->type === 'date' && !in_array($this->actualPeriod, array('none','All')) && $oldid != $curid){
			switch ($this->actualPeriod) {
				case 'weekly':
					$wsub_txt='$wsub=date("W",mktime(1,1,1,12,31,$year2));if($wsub == 1){$wsub=date("W",mktime(1,1,1,12,24,$year2));}$wsub++;';
					$itxt = '$iname="Week ".preZero($i);';
					break;
				case 'monthly':
					$wsub_txt='$wsub=13;';
					$itxt = '$iname = date("M",mktime(1,1,1,$i,1,1));';
					break;
				case 'quarterly':
					$wsub_txt='$wsub=5;';
					$itxt = '$iname = date("jS",mktime(0,0,0,1,$i,1))."&nbsp;quarter ";';
					break;

				default:
					break;
			}
			$iname='';
			$wsub='';
			$fakes=array('seen'=>array(),'length'=>0);
			$years= $this->yearJunkFill($oldid,$curid);
			if($this->actualPeriod != 'annually'){
				$year1=$this->levels[$oldid];
				$year2=$this->levels[$curid];
				$startsub=str_replace($year1, '', $this->lstore[$oldid]);
				$endsub=str_replace($year2, '', $this->lstore[$curid]);
				if($year1 === $year2 && ($endsub - $startsub) === 1){
					return;
				}
				eval($wsub_txt);
				$fakes['max']=($wsub-1);
				if(count($years) == 0){
					$years=array($year1);
				}
				$first=FALSE;
				$initf=false;
				$once=FALSE;
				foreach ($years as &$cyear) {
					if($first === FALSE){
						if($startsub > 1 && $startsub <= ($wsub -1 )){
							$fakes['begin']='hemi';
						}elseif($startsub == 1 && $year2 > $year1) {
							$fakes['begin']= 'full';
						}
						if($endsub < ($wsub-1)){
							$fakes['end']='hemi';
						}elseif ($endsub == ($wsub-1)){
							$fakes['end']='full';
						}
						$first = TRUE;
						if($year1 === $year2){
							if($startsub == ($wsub-1) && $endsub == 1){
								$fakes['begin'] = 'full';
								$fakes['end']	= 'full';
							}else{
								$fakes['begin'] = 'hemi';
								$fakes['end']	= 'hemi';
							}
						}
					}
					for($i = ($cyear == $year1 ? ((int)$startsub+1) : 1); $i < ($cyear == $year2 ? (int)$endsub : (int)$wsub); $i++){
						eval($itxt);
						if(!array_key_exists($cyear,$fakes)){
							$fakes[$cyear]=array();
							if($initf === false){
								$fakes['init']=$cyear;
								$initf=true;
							}
						}
						if($i == 1){
							if($cyear == $year1){
								$fakes['begin']='onset';
							}
							else if($cyear == $year2){
								$fakes['end'] = 'onset';
							}
						}
						$fakes[$cyear][]=$iname;
						++$fakes['length'];
						$once=true;
					}
				}
				$fakes['finish']=$cyear;
			}else {
				$year1=$this->lstore[$oldid];
				$year2=$this->lstore[$curid];
				if($year1 != $year2 && ($year2 -$year1 ) > 1 && is_array($years)){
					//$fakes=array_merge($years,$fakes);
					$fakes=my_array_merge($years,$fakes);
					$fakes['xmode']='yrz';
					$once=true;
				}
			}
			if($once === true){
				return $fakes;
			}
		}
	}

	function yearJunkFill($oldid, $curid) {
		$annCase=FALSE;
		if($this->actualPeriod != 'annually'){
			$new= is_numeric($this->levels [$curid]) ? (int)$this->levels [$curid] : null;
			$old= is_numeric($this->levels[$oldid]) ? ( int ) $this->levels [$oldid] : null;
		}else{
			$new= (is_numeric($this->lstore [$curid]) && $this->lstore[$curid] > 0 ) ? (int)$this->lstore [$curid] : null;
			$old= (is_numeric($this->lstore[$oldid])  && $this->lstore[$oldid] > 0) ? ( int ) $this->lstore [$oldid] : null;
			$annCase=true;
		}
		if(is_null($new) || is_null($old)){
			return ;
		}
		$diff = $new - $old;
		if ($diff >= 1) {
			if($annCase && $diff >= 2){
				$res=range(($old+1), ($new-1),1);
			}else{
				$res = range (  $old, $new, 1 );
			}
		}else{
			$res = array($new);
		}
		return $res;
	}

	function namer ($val,$id = null){
		if(isset($this->vperiods[$id]) || (in_array($val,$this->vperiods) && is_null($id))){
			return  false;
		}
		if(!is_null($id)){
			$this->vperiods[$id]=$val['v'];
		}else{
			$this->vperiods[]=$val['v'];
		}
	}

	function sameYear($id){
		$res=false;
		if($this->levels[$id] == $this->levels[($id+1)]){
			$res=true;
		}
		return $res;
	}

	function nameVerse(){
		$this->vperiods=array_reverse($this->vperiods);
	}

	function nSort(){
		$oldvperds= $this->vperiods;
		$nvps=$this->vperiods;
		sort($nvps,SORT_STRING);
		$rbt=array();
		foreach ($oldvperds as $key => &$value) {
			$npos=array_search($value, $nvps);
			$rbt[$key]=$npos;
		}
		$this->vperiods=$nvps;
		return $rbt;
	}

	function cleaner (&$list){
		if(count($list) > 0){
			$npers=array();
			foreach ($list as $vt) {
				$npers[]=$this->vperiods[$vt];
			}
			$this->vperiods=$npers;
		}
	}
}

