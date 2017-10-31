<?php
global $m;
global $AppUI;
if($m==='outputs' || $m==='manager')
	require_once($AppUI->getModuleClass('wizard'));
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

function getResult(){
	
}

function resultBuilder($qmode) {
	global $AppUI, $m, $l, $f, $h, $u, $s, $r, $m1, $staterd, $final, $nfei, $y, $tab_src, $e, $p, $html, $rhtml, $fielder,
	$titles, $tkeys, $bigtar, $rqid, $lpost, $bigtar_cnt, $bigtar_keys, $starter, $ender, $show_start, $show_end,
	$sels, $clients, $uamode, $colsConst, $clients_cnt,$vis_mode,$thisCenter,$statusHistory,$rl,$preFils,$lvder,$lvd_sel,
	$dPconfig,$jsonmap,$where_query,$join_query,$querysave,$countall,$project_id;
	$lvdForm = false;
	$project_id = null;
	//echo json_encode("Rodéo");
	//exit;
	//mysql_query('SET CHARACTER SET utf8');
	$q = new DBQuery();
	$qcount = new DBQuery();
	if (! $e || ! is_array ( $e )) {
		$e = array ();
	}
	
	if (( int ) $_POST ['qsid'] > 0 ) {
	    $quid = ( int ) $_POST ['qsid'];
		$spost = getSaves ( $quid, $_POST ['stype'] );
		//echo '<pre>';
        //var_dump($spost);
        //echo '<pre>';
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
	}else{
		
	}
    //var_dump($_POST);

	$dataRequest = array();
	// 	$formId = $_GET['idfw'];
	$wz = new Wizard('import');
	
	//$tablename = array();
	$headersotm = array();
	$headers = array();
	$sources = array();
	$where = false;
	if(!isset($_GET['p']) && !isset($_GET['epp'])){
		$token = md5($time);
		$_SESSION['SAVEQUERYANA'][$token] = $_POST;
	}elseif (isset($_GET['token'])){
		$token = $_GET['token'];
	}

    if(isset($_POST['form'])){
		if(isset($_POST['where']) && !empty($_POST['where'])){
			$where = json_decode(stripslashes($_POST['where']));
		}
		$sels = $columns = array();
		$tablechoose = '';
		$forms = array();
		$formsid = array();
		foreach ($_POST['form'] as $table => $arr_fields){
			$q  = new DBQuery();
			$qcount  = new DBQuery();
			$qcount->addTable($table);
			$q->addTable($table);
			if($where!==false && $where!=null){
				$wheresection = array();
				foreach ($where as $kw => $vw){
					if(strpos($kw, 'sec_') !==false){
						$kw = str_replace('sec_', '', $kw);
						$tab_sec = explode('.', $kw);
						$kw = $tab_sec[0];
						$wheresection[$kw][] = $vw;
					}else{
						$q->addWhere($vw);
						$qcount->addWhere($vw);
					}
				}
				if(count($wheresection)>0){
					foreach ($wheresection as $k=>$v){
						$q->addWhere($table.'.id IN( Select '.$kw.'.wf_id FROM '.$kw.' WHERE '.join(' AND ',$v).')');
						$qcount->addWhere($table.'.id IN( Select '.$kw.'.wf_id FROM '.$kw.' WHERE '.join(' AND ',$v).')');
					}
				}
			}
			//orderby
            if(isset($_POST['orderby']) && !empty($_POST['orderby']))
			    $q->addOrder($_POST['orderby']);

			//$tablename[] = $table;
            if($table != 'tasks'){
                $tableau = explode("_", $table);
                $forms[] = $table;
                $formId = $tableau[1];
                $wz->loadFormInfo($formId);
                $fields = $wz->showFieldsImport();
                $sources['id'] =  $wz->title;
                $headers['id']['title'] = '#';



                //if($nitem['raw']['type']==='string')
                  //  $nitem['raw']['type'] = 'string';

                $headers['id']['type'] = 'string';
                $sels[] = 'string';
                $headers['id']['table'] = $table;
                $headers['id']['form'] = $formId;
                $formsid[] = $table.'_id';
                //var_dump($arr_fields);
                foreach ($fields['notms'] as $nitem) {
                    //$nitem['title']$nitem['fld']
                    //$headers[$table.'_'.$nitem['fld']] = $table
                    if(in_array($nitem['fld'],$arr_fields)){

                        $sources[$table.'_'.$nitem['fld']] =  $wz->title;
                        $headers[$table.'_'.$nitem['fld']]['title'] = $nitem['title'];

                        if($nitem['raw']['type']==='entry_date')
                            $nitem['raw']['type'] = 'date';
                        $headers[$table.'_'.$nitem['fld']]['type'] = $nitem['raw']['type'];
                        if($nitem['raw']['type']==='radio' || $nitem['raw']['type']==='select'){
                            $alist = $wz->getValues($nitem['raw']['type'], $nitem['raw']['sysv'], false, false, $nitem['raw']['other']);
                            $ltemp = array();
                            //$ltemp[] = array('r'=>-1,'v'=>$AppUI->__('Select'));
                            foreach ($alist as $kle => $vkle){
                                if($kle!='rels')
                                    $ltemp[] = array('r'=>$kle,'v'=>$vkle);
                            }
                            $sels[] = $ltemp;
                        }else{
                            $sels[] = $nitem['raw']['type'];
                        }
                        $headers[$table.'_'.$nitem['fld']]['table'] = $table;
                        $headers[$table.'_'.$nitem['fld']]['isSection'] = false;
                        $headers[$table.'_'.$nitem['fld']]['form'] = $formId;
                        $headers[$table.'_'.$nitem['fld']]['fld'] = $nitem['fld'];
                        if($nitem['raw']['sysv'])
                            $headers[$table.'_'.$nitem['fld']]['sysv'] = $nitem['raw']['sysv'];
                        if($nitem['section'])
                            $headers[$table.'_'.$nitem['fld']]['section'] = $nitem['section'];
                    }
                }
                foreach ($fields['otms'] as $inx => $sectio) {
                    foreach ($sectio["fields"] as $iny => $infof) {
                        if(isset($_POST[$table]['fields'])){
                            if(in_array($infof['fld'],$_POST[$table]['fields'])){
                                $sourcesotm[$inx.'_'.$infof['fld']] = $sectio['name'];
                                $headersotm[$inx.'_'.$infof['fld']]['title'] = $infof['title'];
                                $headersotm[$inx.'_'.$infof['fld']]['type'] = $infof['type'];
                                if($infof['type']==='radio' || $infof['type']==='select'){
                                    $alist = $wz->getValues($infof['type'], $infof['sysv'], false, false, $infof['other']);
                                    $ltemp = array();
                                    foreach ($alist as $kle => $vkle){
                                        if($kle!='rels')
                                            $ltemp[] = array('r'=>$kle,'v'=>$vkle);
                                    }
                                    $sels[] = $ltemp;
                                }else{
                                    $sels[] = $infof['type'];
                                }
                                $headersotm[$inx.'_'.$infof['fld']]['table'] = $_POST[$table];
                                $headersotm[$inx.'_'.$infof['fld']]['isSection'] = true;
                                $headersotm[$inx.'_'.$infof['fld']]['form'] = $formId;
                                $headersotm[$inx.'_'.$infof['fld']]['fld'] = $infof['fld'];
                                if($infof['sysv'])
                                    $headersotm[$inx.'_'.$infof['fld']]['sysv'] = $infof['sysv'];
                            }
                        }

                    }
                }
            }else{
                $headers['id']['type'] = 'string';
                $sels[] = 'string';
                $headers['id']['table'] = $table;
                $headers['id']['form'] = 0;
                $formsid[] = $table.'_id';

                $sqlact='SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = "tasks"';
                $resact=mysql_query($sqlact);
                if($resact && mysql_num_rows($resact)  > 0) {
                    $activities = array();
                    while ($trowact = mysql_fetch_assoc($resact)) {
                        //$activities['fieldsv']['fields'][$trow['COLUMN_NAME']]['title'] = str_replace("_","",str_replace('task_', 'Activity ',$trow['COLUMN_NAME']));
                        /*if(preg_match('/int/',$trow['DATA_TYPE'])||preg_match('/decimal/',$trow['DATA_TYPE'])){
                            $activities['fieldsv']['fields'][$trow['COLUMN_NAME']]['type'] = 'numeric';
                        }elseif(preg_match('/char/',$trow['DATA_TYPE'])||preg_match('/text/',$trow['DATA_TYPE'])){
                            $activities['fieldsv']['fields'][$trow['COLUMN_NAME']]['type'] = 'plain';
                        }
                        $activities['fieldsv']['fields'][$trow['COLUMN_NAME']]['sysv'] = null;*/
                        if(in_array($trowact['COLUMN_NAME'],$arr_fields)){
                            $sources[$table.'_'.$trowact['COLUMN_NAME']] =  'Activity';
                            $headers[$table.'_'.$trowact['COLUMN_NAME']]['title'] = str_replace("_","",str_replace('task_', 'Activity ',$trowact['COLUMN_NAME']));
                            $headers[$table.'_'.$trowact['COLUMN_NAME']]['table'] = $table;
                            $headers[$table.'_'.$trowact['COLUMN_NAME']]['isSection'] = false;
                            $headers[$table.'_'.$trowact['COLUMN_NAME']]['form'] = 0;
                            $headers[$table.'_'.$trowact['COLUMN_NAME']]['fld'] = $trowact['COLUMN_NAME'];
                        }


                    }
                }

                /*$sources[$table.'_'.$nitem['fld']] =  $wz->title;
                $headers[$table.'_'.$nitem['fld']]['title'] = $nitem['title'];
                $headers[$table.'_'.$nitem['fld']]['table'] = $table;
                $headers[$table.'_'.$nitem['fld']]['isSection'] = false;
                $headers[$table.'_'.$nitem['fld']]['form'] = $formId;
                $headers[$table.'_'.$nitem['fld']]['fld'] = $nitem['fld'];*/
            }

			foreach ($arr_fields as $index => $field){
				if($index==="ref"){
				    $ref = array();
					$refkey = array();
					$tableref = '';// $columns
					foreach ($arr_fields[$index] as $tableref => $fieldsref){
						$forms[] = $tableref;
						$q->addJoin($tableref, null, $tableref.'.id = '.$table.'.ref');
						$qcount->addJoin($tableref, null, $tableref.'.id = '.$table.'.ref');
						$ref[$tableref.'.id'] = $tableref.'.id as '.$tableref.'_id';
						$formsid[] = $tableref.'_id';
						foreach ($fieldsref as $index2 => $field1){
							$ref[$tableref.'.'.$field1] = $tableref.'.'.$field1.' as '.$tableref.'_'.$field1;
							$refkey[] = $field1;
							$columns[$field1] = $tableref;
						}
					}
		
					$tableau1 = explode("_", $tableref);
					$formId1 = $tableau1[1];
		
					$wz1 = new Wizard('import');
					$wz1->loadFormInfo($formId1);
					$fields1 = $wz1->showFieldsImport();
					foreach ($fields1['notms'] as $nitem1) {
						if(in_array($nitem1['fld'],$refkey)){	
							$sources[$tableref.'_'.$nitem1['fld']] =  $wz1->title;
							$headers[$tableref.'_'.$nitem1['fld']]['title'] = $nitem1['title'];
							$headers[$tableref.'_'.$nitem1['fld']]['type'] = $nitem1['raw']['type'];
                            $headers[$tableref.'_'.$nitem1['fld']]['table'] = $tableref;
                            $headers[$tableref.'_'.$nitem1['fld']]['isSection'] = false;
                            $headers[$tableref.'_'.$nitem1['fld']]['form'] = $formId1;
                            $headers[$tableref.'_'.$nitem1['fld']]['fld'] = $nitem1['fld'];
							if($nitem1['raw']['sysv'])
								$headers[$tableref.'_'.$nitem1['fld']]['sysv'] = $nitem1['raw']['sysv'];
						}
					}
					$arr_fields = array_merge($arr_fields,$ref);
				}else{
					$columns[$field] = $table;
					$arr_fields[$index] = $table.'.'.$field.' as '.$table.'_'.$field;
				}

			}
			unset($arr_fields['ref']);
			$arr_fields = implode(',', $arr_fields);
			if($table=='tasks') $arr_fields = $table.'.task_id as '.$table.'_id,'.$arr_fields;
			else $arr_fields = $table.'.id as '.$table.'_id,'.$arr_fields;
			$q->addQuery($arr_fields);
			$qcount->addQuery('count(*)');
			$tablechoose = $table;
		}
		if($q->where)
			$where_query = join(' AND ',$q->where);
		if($q->join)
			$join_query = join(' AND ',$q->join);
		$sql = $q->prepare();
		$tabsql = explode('FROM', $sql);
        $querysaveall = $sql;
		$querysave = $tabsql[1];
			
		
		
		$pageSub = "";
		$pageURL = 'http';
		if ($_SERVER ["HTTPS"] == "on") {
			$pageURL .= "s";
		}
		$pageURL .= "://";
		if ($_SERVER ["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER ["SERVER_NAME"] . ":" . $_SERVER ["SERVER_PORT"]; // .$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER ["SERVER_NAME"]; // .$_SERVER["REQUEST_URI"];
		}
		
		$queryString = $_SERVER ["REQUEST_URI"];
		$queryString = explode ( "?", $queryString );
		$pageURL .= $queryString [0] . "?";
		$pageSub .= $queryString [0] . "?";
		if (isset ( $queryString [1] )) {
			$params = explode ( "&", $queryString [1] );
			foreach ( $params as $x => $param ) {
				$tabparam = explode ( "=", $param, 2 );
				if ($tabparam [0] != 'p') {
					if ($tabparam [0] != 'token') {
						if (isset ( $tabparam [1] )) {
							$pageURL .= "&" . $tabparam [0] . '=' . $tabparam [1];
							$pageSub .= "&" . $tabparam [0] . '=' . $tabparam [1];
						}
					}
				}
			}
		}
		
		$page = 1;
		if (isset ( $_GET ['p'] ) && is_numeric ( $_GET ['p'] )) {
			$page = intval ( $_GET ['p'] );
			
			if ($page < 1) {
				$page = 1;
			}
		}
		
		$epp = ( int ) $_GET ['epp'];
		
		if (! $epp) {
			$epp = 100;
		}
		//echo '<pre>';
		//var_dump($_POST);
        //echo '</pre>';
		if(isset($_POST['limit']) && !empty($_POST['limit']))
            $count_request = $_POST['limit'];
		else $count_request = $qcount->loadResult();
		$count = $count_request;
		$start = $page * $epp - $epp;
		// $end = $start + $epp;
		$nbPages = ceil ( $count / $epp );
		$sql = $sql .' limit '.$start.', '.$epp;
		$res = db_exec($sql);
		//echo $sql.' ';
		//exit;
	
	}else if(isset($_POST['activity'])){
        $q = new DBQuery();
        $q->addTable('activity_queries');
        $q->addQuery('id,qname,qdesc,activity_id as task_name,sector_id as sector,st_area_id as st_area_name,beneficieries,amount');
        $resActivities = $q->loadList();
    }



	$countall = $count;
	unset ( $queries );
	diskFile::init ();
	//$dataRequest_cnt = count ( $dataRequest );
	
	//if ($dataRequest_cnt > 0) {
	
	if($res){
		//$bigtar_keys = (isset($dataRequest) && count($dataRequest) > 0) ?  array_keys ( $dataRequest ) : array();
		$rhtml = '<div style="text-align: center;*text-align:none;padding-bottom:30px;">
		<form method="POST" action="/?m=outputs&suppressHeaders=1" name="saveme">
		<input type="hidden" name="list" id="stabbox">
		<input type="hidden" name="mode" value="save">
		<input type="hidden" name="fname" value="">
		<input type="button" onclick="popTable(\'mholder\',\'print\')" style="float:left; " value="Print" class="ce pi ahr button">
		<input type="button" onclick="gpgr.saveTable()" style="float:left; " value="Export to Excel" class="ce pi ahr button adcbutt">
		</form>';
		if ($qmode != 'mas') {
			$rhtml .= '<input type="button" class="ce pi ahr button adcbutt" value="Save Table Query" onclick="qurer.saveDialog()">
			<input type="button" class="ce pi ahr button adcbutt" value="Build Stats" onclick="gpgr.startss();">
			<input type="button" style="background-color: grey" class="ce pi ahr button adcbutt" value="Build Maps"> <!--onclick="gpgr.mapss();"-->';

		}
		//$rhtml .= '<input type="button" class="button adcbutt" value="Add to form" onclick="dialogForm()">';
        if($formId){
            $q = new DBQuery();
            $q->addTable("form_master");
            $q->addQuery("project_id");
            $q->addWhere('id='.$formId);
            $project_id = $q->loadResult();
        }

		
		$rhtml .= '<div style="text-align: center;*text-align:none;padding-left:50px;padding-bottom:30px;">
		<form method="POST" action="/?m=outputs&suppressHeaders=1" name="savemefile">
		<!--<input type="hidden" name="list" id="stabboxfile">
		<input type="hidden" name="mode" value="savefile">
		<input type="hidden" name="fname" value="">
		<input type="hidden" name="foname" value="">
		<input type="hidden" name="ftotal" value="">				
		<input type="hidden" name="flink" value="">
		<input type="hidden" name="fdesc" value="">		
		<input type="hidden" name="project_id" value="'.$project_id.'">-->		
		<input type="hidden" name="mode" value="savefile"/>
		<input type="hidden" name="is_save"/>
        <input type="hidden" name="qname"/>
        <input type="hidden" name="qdesc"/>
        <input type="hidden" name="activity_id"/>
        <input type="hidden" name="sector_id"/>
        <input type="hidden" name="st_area_id"/>
        <input type="hidden" name="beneficieries"/>
        <input type="hidden" name="amount"/>
        <input type="hidden" name="token" value="'.$token.'"/>
        <input type="text" name="querysave" value="'.$querysaveall.'">
		<input type="button" class="ce pi ahr button adcbutt" value="Save To File" onclick="gpgr.saveToFile()"  class="button adcbutt">
		</form></div>';
		$tfhtml = '<ul>';
		foreach ($forms as $form){
			$sform = split('_', $form);
			$idsf = $sform[1];
			$q = new DBQuery();
			$q->addTable('form_master');
			$q->addQuery('title');
			$q->addWhere('id='.$idsf);
			$r = $q->loadResult();
			
			$q = new DBQuery();
			$q->addTable('form_master');
			$q->addQuery('id,title');
			$q->addWhere('parent_id='.$idsf);
			$q->addWhere('multiplicity="ManyToMany"');
			$lsf = $q->loadHashList();
			$tfhtml .= '<li>'.$r.'<ul>';
			foreach ($lsf as $hc => $hv){
				$tfhtml .= '<li><strong><a href=\'javascript:popupForm('.$idsf.','.$hc.')\'>'.$hv.'</a></strong></li>';
			}
			$tfhtml .='</ul></li>';
		}
		$tfhtml .= '</ul>';
		 $rhtml .= '
		 <script type="text/javascript">
			 var $dbox;
			 function dialogForm(){
				 $dbox = $j("<div id=\'dbaddf\' title=\'Add\'></div>");
		 		 $dbox.append("'.$tfhtml.'");
				 $dbox.dialog({
					 modal: true,
					 width: "400",
					 resizable: false,
					 autoOpen: true,
				 }).prev(".ui-dialog-titlebar").css("background","#aed0ea").css("border","1px solid #aed0ea");
			
			 }
		 	 
		     function popupForm(pid,id){
		 		window.open(\'index.php?m=public&a=parent_form_selector&pid=\'+pid+\'&fid=\'+id+\'&dialog=1\', \'calwin\', \'top=250,left=250,width=auto,height=220,scollbars=false,resizable\' );
		 		$dbox.dialog("close");		
		 	 }
		 </script>
		
		 ';

		 if($page==1){
		 	$prev = '<span style="color:#c0c0c0">Prev</span>';
		 	if($page<$nbPages)
		 		$next = '<a href="'.$pageURL."&token=".$token."&p=".($page + 1).'&epp='.$epp.'"><h3>Next</h3></a>';
		 	else
		 		$next = '<span style="color:#c0c0c0">Next</span>';
		 }elseif($page>1){
		 	//$pageURL .= "&p=".($page - 1);
		 	$prev = '<a href="'.$pageURL."&token=".$token."&p=".($page - 1).'&epp='.$epp.'"><span>Prev</span></a>';
		 	if($page<$nbPages)
		 		$next = '<a href="'.$pageURL."&token=".$token."&p=".($page + 1).'&epp='.$epp.'"><span>Next</span></a>';
		 	else
		 		$next = '<span style="color:#c0c0c0">Next</span>';
		 }
		 //if($res)
		 //$currentNbr = $start + mysql_num_rows($res);
		 $currentNbr = 1;
		 
		 $selected1 = "";
		 $selected2 = "";
		 $selected3 = "";
		 $selected4 = "";
		 if($epp===100){
		 	$selected1 = " selected=selected";
		 }
		 if($epp===250){
		 	$selected2 = " selected=selected";
		 }
		 if($epp===500){
		 	$selected3 = " selected=selected";
		 }
		 /* if($epp===2000){
		  $selected4 = " selected=selected";
		  } */
		 if($count>$start+$epp){
		 	$val = $start+$epp;
		 }else{
		 	$val = $count;
		 }
		 $tokenUrl = '';
		 if($token){
             $tokenUrl = "&token=".$token;
         }
		 $info = "Total Records: ".$count." &emsp;&emsp;Viewing Records: ".($start+1)."-".$val." &emsp;&emsp;<span style='color: #08245b;'><b>Rows per page</b></span> <select name='epp' onchange='window.location.href = window.location.href+\"".$tokenUrl."\"+\"&epp=\"+this.value'><option ".$selected1.">100</option><option ".$selected2.">250</option><option ".$selected3.">500</option></select>";
		 
		 $rhp = '<span><table style=""><tr><td>'.$prev.'</td><td><!-- <div style="margin-top:35px;">{<b style="color:#008000">'.$currentNbr.'/'.$count.'</b>}</div>--></td><td>'.$next.'</td><td><div style="margin-top:-5px;">'.$info.'</div></td></tr></table></span>';
		 //$rh .= $rhp;
		 //$rh .= '</div>';
		 $rf = '<div align="left" style="background:white;">';
		 $rf .= $rhp;
		 $rf .= '</div>';
		 
		 
		$rhtml .= '</div>';
		$rhtml .= $rf;
		$tab_start = '<div id="mholder"><table class="rtable moretable" id="rtable" border="0" style="empty-cells:show;border:collapse:separate;display:block;" cellpadding="2" cellspacing="1">';
		$tab_head = "\n<thead><tr>";
		$colz = '<colgroup>';
		$ind = 0;
		$addcl = '';
		$reportLinks=array();
        foreach ( $headers as $hcode => $hcodeArray ) {
		    $source = $sources[$hcode];
			$source_cnt = strlen($source);
			$data_part = $source;

			if($source_cnt>4){
				$data_part = strrev($data_part);
				$data_part = substr($data_part, $source_cnt-4);
				$data_part = strrev($data_part);
			}
			if($hcodeArray['type']==='entry_date')
				$hcodeArray['type'] = 'date';
			$point = '';
			if(strlen($AppUI->_($hcodeArray['title']))>50)
				$point = '...';
			$tab_head .= '<th id="head_' . $ind . '" data-thid="' . $ind . '" data-form="'.$hcodeArray['form'].'" data-type="'.$hcodeArray['type'].'" data-field="'.$hcodeArray['fld'].'" data-title='.$source.' data-sys="'.$hcodeArray['sysv'].'"  class="head ' . $addcl . '" data-part="'.$data_part.'">' . substr($AppUI->_($hcodeArray['title']), 0,49).$point . '<div class="head_menu"></div></th>' . "\n";
			$tab_src .= '<th class="fsource">'.$source.'</th>' . "\n";
			$colz .= '<col id=col_' . $ind . '></col>';
			$ind ++;
			$addcl = ' forsize';
		
		}
		if($m==='manager'){
			foreach ( $headersotm as $hcode => $hcodeArray ) {
				$source = $sourcesotm[$hcode];
				$source_cnt = strlen($source);
				$data_part = $source;
				if($source_cnt>4){
					$data_part = strrev($data_part);
					$data_part = substr($data_part, $source_cnt-4);
					$data_part = strrev($data_part);
				}
				$point = '';
				if(strlen($AppUI->_($hcodeArray['title']))>50)
					$point = '...';
				$tab_head .= '<th id="head_' . $ind . '" data-thid="' . $ind . '" data-form="'.$hcodeArray['form'].'" data-type="'.$hcodeArray['type'].'" data-field="'.$hcodeArray['fld'].'" data-title='.$source.' data-sys="'.$hcodeArray['sysv'].'"  class="head ' . $addcl . '" data-part="'.$data_part.'">' . substr($AppUI->_($hcodeArray['title']), 0,49).$point . '<div class="head_menu"></div></th>' . "\n";
				$tab_src .= '<th class="fsource">'.$source.'</th>';
				$colz .= '<col id=col_' . $ind . '></col>';
				$ind ++;
				$addcl = ' forsize';
			
			}
		}
		//$tab_head .= "</tr></thead>" . "\n";
		$tab_head .= "</tr>\n<tr>" . $tab_src . '</tr></thead>' . "\n";
		$rhtml .= $tab_start . $colz . $tab_head;
		unset ( $tab_start, $colz );
		$rhtml .= '<tbody>';
		//$rhtml .= '\n\t<tr>\n\t\t'.$tab_src.'</tr>';
		diskFile::putTXT ( $rhtml );

		
		diskFile::startJsonArray();
		$nbrval = 0;
		$firstTD = true;
		/*echo '<pre>';
		var_dump($headers);
		echo '</pre>';*/
		$tempId = array();
			//$bigtar_keys = (isset($dataRequest) && count($dataRequest) > 0) ?  array_keys ( $dataRequest ) : array();
		//foreach($dataRequest as $rowdata){
		$_request = 0;
		$keys_request = array();
		//exit();
		$i = 0;
		//echo db_num_rows($res);
		//exit;
        $_SESSION['SAVEQUERYDATA'][$token] = $headers;
		while ( $rowdata = db_fetch_assoc ( $res ) ) {
			
			$keys_request[] = $_request;
			$_request++ ;
			$wclass = '';
			$nbrval += 1;
			$fornext = '';
			//var_dump($rowdata);
			$firstTD=true;
			$xpos=0;
			$json_data = array();
			$row = "\n\t<tr id='row_" . $y . "' data-id='#@QRI@#'>\n\t\t";
			$fornextcell = '';
			foreach ($formsid as $idf){
				$tempId[$idf][] = $rowdata[$idf];
			}
			//echo ($i + 1);
			//$i++;
			foreach ( $headers as $hcode => $hcodeArray ) {
				//$json_data['table'] = $hcodeArray['table'];
				$rid = $rowdata[$hcodeArray['table'].'_id'];
				//echo $hcode.' ';
				/* if($hcode=='key_ui')
					$hcode = $hcodeArray['table'].'_key_ui'; */
				$forStore = $rowdata[$hcode];
				$tempforStore = preg_replace('/\\\\/', '', $forStore);
                $sysvbool = false;
				$wclass = $hcodeArray['table'];
				if(isset($hcodeArray['sysv'])){
					$forStore = $wz->getValues($hcodeArray['type'],$hcodeArray['sysv'],$forStore);
				}
				$forStore = strval($forStore);
                $forStorewjson = $tempforStore = preg_replace('/\\\\/', '', $forStore);
				$forStore = json_encode($forStore);
				$forStore = str_replace('"', '', $forStore);
				/*if($hcode=='wform_81_fld_24')
				echo $forStorewjson;*/
				if($hcodeArray['sysv'] != 'SysCommunalSection'){
					$forStore = stripslashes($forStore);
					$forStore = stripslashes($forStore);
					$forStore = stripslashes($forStore);
					$forStore = stripslashes($forStore);
					$forStore = stripslashes($forStore);
					$forStore = stripslashes($forStore);
				}else{
				}
				//exit;
				
				$json_data[$hcode] = $forStore;
				
				if($hcodeArray['sysv']==='SysDepartment'){
					$func = '';
					$funcid = '';
					//$class = 'class_'.$hcodeArray['section'].'_'.$rid;
					$funcidchild = '';
					if($hcodeArray['section']){
						$funcidchild = 'commune_'.$hcodeArray['section'].'_'.$rid;
						$funcid = 'department_'.$hcodeArray['section'].'_'.$rid;
						$func = 'populateCommune("'.$funcid.'",'.'"'.$funcidchild.'")';
						$funcchild = 'populateSection("'.$funcidchild.'",'.'"section_'.$hcodeArray['section'].'_'.$rid.'")'; 
					}
				} 
				if($hcodeArray['sysv']==='SysCommunes'){
					$func = '';
					$funcid = '';
					$parentid = '';
					$mode = 'loadcommunDept';
					//$parentele = 
					if($hcodeArray['section']){
						$parentid = 'department_'.$hcodeArray['section'].'_'.$rid;
						$funcid = 'commune_'.$hcodeArray['section'].'_'.$rid;
						$func = 'populateSection("'.$funcid.'",'.'"section_'.$hcodeArray['section'].'_'.$rid.'")';
					}
					$q = new DBQuery();
					$q->addTable('administration_com');
					$q->addQuery('administration_com_lat,administration_com_lng');
					$q->addWhere('administration_com_code="'.$tempforStore.'"');
					$result = $q->loadList();
					
					if(is_numeric($result[0]['administration_com_lat'])){
						$json_data[$hcode.'_lat'] = (float)$result[0]['administration_com_lat'];
					}else{
						$json_data[$hcode.'_lat'] = null;
					}
					if(is_numeric($result[0]['administration_com_lng'])){
						$json_data[$hcode.'_lng'] = (float)$result[0]['administration_com_lng'];
					}else{
						$json_data[$hcode.'_lng'] = null;
					}
				}
				if($hcodeArray['sysv']==='SysCommunalSection'){
					$func = '';
					$funcid = '';
					$parentid = '';
					$mode = 'loadsection';
					if($hcodeArray['section']){
						$parentid = 'commune_'.$hcodeArray['section'].'_'.$rid;
						$funcid = 'section_'.$hcodeArray['section'].'_'.$rid;
					}
				}
				$nfei->store ($forStore);
				if($firstTD){
					$row .= "<td class='rowfdel'><div class='delbutt blind'></div><div #@QBC@# class='qeditor blind' title='Modify record' onclick='shEd(".$formId.',' . $rowdata[$hcodeArray['table'].'_id'] . ",1)'></div><div class='txtit fhref flink' title='View record' onclick='shCl(".$formId.',' . $rowdata[$hcodeArray['table'].'_id'] . ")'>NO." . $rowdata[$hcodeArray['table'].'_id'] . "</div></td>\n";
					if($m==='manager'){
						$fornextcell .= "<td class='rowfdel'><div class='delbutt blind'></div><div #@QBC@# class='qeditor blind' onclick='shEd(".$formId.',' . $rowdata[$hcodeArray['table'].'_id'] . ",1)'></div><div #@QBC@# class='fbutton qticon eye blind' onclick='shSh(".$formId.',' . $rowdata[$hcodeArray['table'].'_id'] . ",1)'>show</div><div class='txtit fhref flink' onclick='shCl(".$formId.',' . $rowdata[$hcodeArray['table'].'_id'] . ")'>NO." . $rowdata[$hcodeArray['table'].'_id'] . "</div></td>\n";
					}
					$firstTD = false;
					
				}else{ 
					if($forStore==null || $forStore==""){
						$forStore = '&nbsp';
					}
					$row .= "<td class='vcell text-left' func-id='".$funcid."' func-action='".$func."' data-value=\"".$tempforStore."\" parent-id='".$parentid."' mode='".$mode."'>" . strval($forStorewjson) . "</td>\n";
					if($m==='manager'){	
						$fornextcell .= "<td class='text-left' style='cursor:not-allowed;background:#c0c0c0'></td>\n";
					}
				}
				
			}
			$suiterow = '';
			if($m==='manager'){
				if(isset($_POST[$tablechoose]) && isset($_POST[$tablechoose]['fields']))
					foreach ($_POST[$tablechoose]['fields'] as $fld){
						$suiterow .= "<td class='text-left' style='cursor:not-allowed;background:#c0c0c0'>".$i."</td>\n";
					}
			}
			$row .= $suiterow;
			diskFile::putJsonData(json_encode($json_data));
			if($m==='outputs'){
				if($nbrval<$count_request)
					diskFile::putSeparatorJsonData();
			}
			if (isset($wclass) &&  $wclass != '') {
				$row = str_replace ( "#@QBC@#", 'data-tbl="' . $wclass . '||' . $rid . '" ', $row );
				if (@! in_array ( $wclass, $e )) {
					$e [] = $wclass;
				}
			}else{
				$row = str_replace ( "#@QBC@#", '', $row );
			}
			$row = str_replace ( "#@QRI@#", $rid, $row );
			$row .= '</tr>' . "\n";
			
			
			$rhtml .= $row;
			
			$nfei->nextRow ();
			diskFile::tableBody ( array ('row' => str_replace ( array ("\r\n", "\n", "\r" ), '', $row ), 'id' => $rid, 'table' => $tablechoose, 'client' => $rid ), $y );
			++$xpos;
			++$y;
			$row='';
			
			if($m==='manager'){
				if(count($_POST[$tablechoose]['fields'])>0){
					$q = new DBQuery();
					$q->addTable($_POST[$tablechoose]['name']);
					$query = array();
					foreach ($_POST[$tablechoose]['fields'] as $ik=>$iv){
						$query[] = $iv.' as '.$_POST[$tablechoose]['name'].'_'.$iv;
					}
					$q->addQuery(implode(',', $query));
					$q->addWhere('wf_id='.$rid);
					$sqlsubs = $q->prepare();
					$ressubs = db_exec($sqlsubs);
					$ressubs_cnt = db_num_rows($ressubs);
					
					if($ressubs){
						$json_data_subs = array();
						$nbrval_sb = 0;
						diskFile::putSeparatorJsonData();
						
						
						while ($rowsub = db_fetch_assoc ( $ressubs ) ) {
							$fornextrowtemp = "\n\t<tr id='row_" . $y . "' data-id='#@QRI@#'>\n\t\t";
							$nbrval_sb += 1;
							$nextrow = '';
							foreach ( $headersotm as $hcode => $hcodeArray ) {
								$forStore = $rowsub[$hcode];
								//$tempforStore = $forStore;
                                $tempforStore = preg_replace('/\\\\/', '', $forStore);
								$sysvbool = false;
								$wclass = $tablechoose;
								if(isset($hcodeArray['sysv'])){
									$forStore = $wz->getValues($hcodeArray['type'],$hcodeArray['sysv'],$forStore);
								}
                                $forStorewjson = $forStore;
								$forStore = json_encode($forStore);
								$forStore = str_replace('"', '', $forStore);
								//exit;
									
								$json_data_subs[$hcode] = $forStore;
								if($hcodeArray['sysv']==='SysCommunes'){
									$q = new DBQuery();
									$q->addTable('administration_com');
									$q->addQuery('administration_com_lat,administration_com_lng');
									$q->addWhere('administration_com_code="'.$tempforStore.'"');
									$result = $q->loadList();
										
									if(is_numeric($result[0]['administration_com_lat'])){
										$json_data_subs[$hcode.'_lat'] = (float)$result[0]['administration_com_lat'];
									}else{
										$json_data_subs[$hcode.'_lat'] = null;
									}
									if(is_numeric($result[0]['administration_com_lng'])){
										$json_data_subs[$hcode.'_lng'] = (float)$result[0]['administration_com_lng'];
									}else{
										$json_data_subs[$hcode.'_lng'] = null;
									}
								}
								$nfei->store ($forStore);
								$nextrow .= "<td class='text-left'>" . strval($forStore) . "</td>\n";
							}
							$nextrow = $fornextrowtemp.$fornextcell.$nextrow.'</tr>' . "\n";
				
							diskFile::putJsonData(json_encode($json_data_subs));
							if($nbrval_sb<$ressubs_cnt)
								diskFile::putSeparatorJsonData();
							if (isset($wclass) &&  $wclass != '') {
								$nextrow = str_replace ( "#@QBC@#", 'data-tbl="' . $wclass . '||' . $rid . '" ', $nextrow );
								if (@! in_array ( $wclass, $e )) {
									$e [] = $wclass;
								}
							}else{
								$nextrow = str_replace ( "#@QBC@#", '', $nextrow );
							}
							$nextrow = str_replace ( "#@QRI@#", $rid, $nextrow );
							$nfei->nextRow ();
								
							$rhtml .= $nextrow;
								
							diskFile::tableBody ( array ('row' => str_replace ( array ("\r\n", "\n", "\r" ), '', $nextrow ), 'id' => $clar [0], 'table' => $tablechoose, 'client' => $rid ), $y );
							
							++$xpos;
							++$y;
						}
					}
				}	
			}
			
			
			diskFile::putTXT ( $rhtml );
			//if($i>0)
				//break;
			
		}
		$bigtar_keys = ($count_request > 0) ?  $keys_request : array();
		diskFile::putId(json_encode($tempId));
		diskFile::endJsonArray();
		
		
		$tddd=$nfei->getForStat ();
		
		diskFile::tableBodyWrite ( $tddd );
		
		$rhtml .= '</tbody></table></div> <div id="pagebox"><span id="pgbs"></span>
		<!--<span style="float:left;">&nbsp;&nbsp;&nbsp;&nbsp;Rows per page<select name="npp" onchange="gpgr.reorder(this)">
		<option value="10" >10</option>
		<option value="20">20</option>
		<option value="50" selected="selected">50</option>
		<option value="100">100</option>
		<option value="200">200</option>
		<option value="500">500</option>
		<option value="-1">All</option>
		</select></span>-->
		<div id="cleanbox" style="display:none;float:left;margin-right: 15px;">
			<span class="fmonitor" id="fmbox"></span>
			<input type="button" class="button" onclick="cleanAllF();" disabled="disabled" value="Clear Filters" id="fclean">
		</div></div>';
		
		
		$rhtml .= $rf;
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
		$jsonmap = diskFile::getJsonPath();
    }else if($resActivities){
        $rhtml = '';
        $tab_start = '<div id="mholder"><table class="rtable moretable" id="rtable" border="0" style="empty-cells:show;border:collapse:separate;display:block;" cellpadding="2" cellspacing="1">';
        $tab_head = "\n<thead><tr>";
        $colz = '<colgroup>';
        $ind = 0;
        $addcl = '';
        $columnArray = array('qname','task_name','sector','st_area_name','beneficieries','amount');
        foreach ($columnArray as $column){
            $tab_head .= '<th id="head_' . $ind . '" data-thid="' . $ind . '" data-form="activity_queries" data-type="" data-field="" data-title="" data-sys=""  class="head ' . $addcl . '" data-part=""><div class="head_menu"></div></th>' . "\n";
            $tab_src .= '<th class="fsource">Test</th>' . "\n";
            $colz .= '<col id=col_' . $ind . '></col>';
            $ind ++;
            $addcl = ' forsize';
        }
        $tab_head .= "</tr>\n<tr>" . $tab_src . '</tr></thead>' . "\n";
        $rhtml .= $tab_start . $colz . $tab_head;
        unset ( $tab_start, $colz );
        $rhtml .= '<tbody>';
        diskFile::putTXT ( $rhtml );
        $nbrval = 0;
        foreach ($resActivities as $activity) {
            //$keys_request[] = $_request;
            //$_request++ ;
            $wclass = '';
            $nbrval += 1;
            $fornext = '';
            //var_dump($rowdata);
            $firstTD=true;
            $xpos=0;
            $row = "\n\t<tr id='row_" . $y . "' data-id='#@QRI@#'>\n\t\t";
            $fornextcell = '';
            //echo ($i + 1);
            //$i++;
            foreach ( $columnArray as $column ) {
                //$json_data['table'] = $hcodeArray['table'];
                //$rid = $rowdata[$hcodeArray['table'].'_id'];
                //echo $hcode.' ';
                /* if($hcode=='key_ui')
                    $hcode = $hcodeArray['table'].'_key_ui'; */
                $forStore = $activity[$column];
                $tempforStore = preg_replace('/\\\\/', '', $forStore);
                $sysvbool = false;
                //$wclass = $hcodeArray['table'];
                //if(isset($hcodeArray['sysv'])){
                //  $forStore = $wz->getValues($hcodeArray['type'],$hcodeArray['sysv'],$forStore);
                //}
                //exit;
                //$nfei->store ($forStore);
                if($firstTD){
                    $row .= "<td class='rowfdel'>NO.</td>\n";
                    $firstTD = false;
                }else{
                    if($forStore==null || $forStore==""){
                        $forStore = '&nbsp';
                    }
                    $row .= "<td class='vcell text-left' >" . $forStore . "</td>\n";
                }

            }
            //$suiterow = '';
            //$row .= $suiterow;
            //if (isset($wclass) &&  $wclass != '') {
            //$row = str_replace ( "#@QBC@#", 'data-tbl="' . $wclass . '||' . $rid . '" ', $row );
            //if (@! in_array ( $wclass, $e )) {
            //  $e [] = $wclass;
            //}
            //}else{
            //$row = str_replace ( "#@QBC@#", '', $row );
            //}
            //$row = str_replace ( "#@QRI@#", $rid, $row );
            $row .= '</tr>' . "\n";


            $rhtml .= $row;

            //$nfei->nextRow ();
            //diskFile::tableBody ( array ('row' => str_replace ( array ("\r\n", "\n", "\r" ), '', $row ), 'id' => $rid, 'table' => $tablechoose, 'client' => $rid ), $y );
            ++$xpos;
            ++$y;
            $row='';
            diskFile::putTXT ( $rhtml );
        }
        echo $y;
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
            $val = preg_replace('/\\\\/', '', $val);

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
        //echo $val.'...'.is_null($val).' ';
        //echo '('.$cxpos.','.$cypos.')';
		if(trim($val) === '' || is_null($val)){
			$val=false;
		}
		/*if($val===false){
		    echo $val.' ';
        }*/
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
		    //echo $val.' ';
			$found = false;
			$found=array_search($val,$this->treal[$cxpos],TRUE);
			/*if($val=='0')
                echo $val.':'.$found.' ';*/
			if ($found === false) {
				$this->inUniques ( $val, $vval );
			}else{
			    //echo $this->y.'&emsp;';
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
