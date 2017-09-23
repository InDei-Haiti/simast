<?php
/**
 * Created by JetBrains PhpStorm.
 * User: stig
 */
global $AppUI,$project_id;

//require_once ($AppUI->getModuleClass("clients"));
require_once($AppUI->getModuleClass("outputs"));
//echo  str_lreplace("Suy", "cadi", "Suy hello allo Suy Hi Suy");
$fuid = (int)$_GET['fid'];
$parent_id = (int)$_GET['ref'];
$idIns = (int)$_GET['idIns'];
$task_id = (int)$_GET['task_id'];
$xmode = 'view';
$useID = false;
$useID = (int)$_GET['itemid'];

$q = new DBQuery();
$q->addTable('form_master');
$q->addQuery('project_id');
$q->addWhere('id='.$fuid);
$project_id = $q->loadResult();


$q = new DBQuery();
$q->addTable('form_master');
$q->addQuery('task_id');
$q->addWhere('id='.$fuid);
$task_id = $q->loadResult();

$pdfstyle = '
			<style>
				div tr td{
					padding: 5px;
					font-family: "Times New Roman", Times, serif;
					font-size: 12px;
				}
			</style>
		';
$pdfhtml = '<html><head>'.$pdfstyle.'</head><body><div style="width:90%;margin:auto"><img src="/images/headerdoc/entete.png"/><br/><br/><table>';
$q->clear();

if (isset($_GET['todo']) && trim($_GET['todo']) == 'addedit') {
	$xmode = 'edit';
	if (!isset($_GET['itemid']) || $useID === 0) {
		$xmode = 'add';
	}
}
if (isset ( $_GET ['tab'] )) {
	$AppUI->setState ( 'WizFormVwTab', $_GET ['tab'] );
}
$tab = $AppUI->getState ( 'WizFormVwTab' ) !== NULL ? $AppUI->getState ( 'WizFormVwTab' ) : 0;
$teaser = false;
if(isset($_GET['teaser']) && $_GET['teaser'] == 1){
	$teaser = true;
}


if ($client_id === 0 && (int)$_POST['client_id'] > 0) {
	$client_id = (int)$_POST['client_id'];
}
$wz = new Wizard($xmode, $task_id, $client_id, $useID);

if ($fuid > 0) {
	$dvals = array();
	$wz->loadFormInfo($fuid);

	
	if ($_GET['todo'] === 'save') {
		$rid = (int)$_POST['id'];
		//if($parent_id)
			//$wz->parent = $parent_id;
		$ref = $_GET['ref'];
		if(!$ref)
			$ref = 0;
		//var_dump($_POST);
		//exit;
		$res = $wz->saveFormData($_POST,$ref,$rid);
		if($res && $res !==true ){
			$redirect = 'm=wizard&a=form_use&fid='.$fuid.'&idIns='.$res.'&todo=view&teaser=1&rtable=1&tab=0';
			$res = true;
		}
		
		if ($res === true) {
			$AppUI->setMsg($rid>0 ? ' added' : ' edited', UI_MSG_OK, true);
		} else {
			if(count($wz->dberrors)>0)
				$AppUI->setMsg(join(', ', $wz->dberrors), UI_MSG_ERROR, true);
			else
				$AppUI->setMsg(' error during saving', UI_MSG_ERROR, true);
			//exit;
		}
		$wz->dberrors = array();
		//m=wizard&a=form_use&fid=39&idIns=1&todo=view&teaser=1&rtable=1&tab=0
		/* if($redirect)
			$AppUI->redirect($redirect);
		else $AppUI->redirect(); */
		$AppUI->redirect();
		/* if ($wz->registry === 0) {
			$itab = array_search($_SESSION['selected_tab'], $_SESSION['wiz_tab']);
			//$AppUI->setMsg(' error during saving', UI_MSG_ERROR, true);
			$AppUI->redirect('m=tasks&a=view&tab=' . $itab . '&task_id=' . $task_id);
		} else {
			$AppUI->redirect('m=tasks');
		} */
	}

	$wz->tableWrap();
	$blist = '';
	if ($xmode != 'view') {

		//if ($clientObj->getFullname()) {
			//$ttl = $useID > 0 ? "Edit Visit : " . $clientObj->getFullName() : "New Visit: " . $clientObj->getFullName();
		//} else {
			$ttl = $useID > 0 ? "Edit" : "New";
		//}
		if($teaser === false){
			$titleBlock = new CTitleBlock($ttl, '', $m, "$m.$a");
			
			//$titleBlock->addCrumb("?m=clients", "Clients");
			//$titleBlock->addCrumbRight2("clearSelection(document.forms['changeClinical'])", "Clear All Selections");
			//if ($clientObj->client_id > 0)
				//$titleBlock->addCrumb("?m=clients&a=view&client_id=$clientObj->client_id", $clientObj->getFullName());
			$titleBlock->addCrumb("?m=projects&a=view&project_id=".$project_id."&tab=2", $AppUI->_('view project'));
			$titleBlock->addCrumb("?m=projects", $AppUI->_('list projects'));
			$titleBlock->show();
		}
		$ref = "";
		if($idIns)
			$ref = '&ref='.$idIns;
		$blist .= '<form action="/?m=wizard&a=form_use&todo=save&fid=' . $fuid .$ref. '" method="POST" id="wform" name="wform">';
		$blist .= '<table width="100%" cellspacing="1" cellpadding="1" border="0" class="std">
					<tbody><tr>
						<td width="100%" valign="top">
						<table>';
	} else {
		$wd = $wz->drawDigest($fuid,0,1,false,false);
		$useID = ($useID > 0 ? $useID : $wd[2]);
		//$drows = $wd[1];
		//$blist = $wd[0];
	}

	if ($useID > 0) {
		$idIns = dPgetParam($_GET, 'idIns',0);
		$sql = 'select * from ' . $wz->form_prefix . $fuid . ' WHERE id='.$idIns;
		$res = mysql_query($sql);
		if ($res) {
			$dvals = mysql_fetch_assoc($res);
		} else {
			$dvals = array();
		}
	}
	$titleBlock = new CTitleBlock($ttl, '', $m, "$m.$a");
		
	//$titleBlock->addCrumb("?m=clients", "Clients");
	//$titleBlock->addCrumbRight2("clearSelection(document.forms['changeClinical'])", "Clear All Selections");
	//if ($clientObj->client_id > 0)
	//$titleBlock->addCrumb("?m=clients&a=view&client_id=$clientObj->client_id", $clientObj->getFullName());
	
	if ($xmode === 'view' && $useID > 0) {
		diskFile::init ();
		$titleBlock = new CTitleBlock('Details on ' .$wz->formName/*printDate($dvals['entry_date'])*/ , '', $m, "$m.$a");
		$titleBlock->addCrumb("?m=projects", $AppUI->_('list projects'));
		$titleBlock->addCrumb("?m=projects&a=view&project_id=".$project_id."&tab=2", $AppUI->_('view project'));
		if($task_id){
			$titleBlock->addCrumb("?m=tasks&a=view&task_id=".$task_id, $AppUI->_('view activity'));
		}
		$titleBlock->addCrumb('?m=wizard&a=form_use&fid=' . $fuid . '&todo=addedit&itemid=' . $useID.'&idIns='.$idIns, $AppUI->_('edit record'));
		$titleBlock->addCrumb('/pdf.php?token='.$_SESSION ['fileNameCsh'], $AppUI->_('PDF'));
		$crumb = $titleBlock->show(true);
		
		$blist .= '
		<!--<table>
			<tbody>
				<tr>
					<td width="100%"><h1>Details on form ' .$wz->formName/*printDate($dvals['entry_date'])*/ . '</h1>'.$crumb.'</td>
				</tr>
				<tr>
					<td width="100%"><a href="?m=wizard&a=form_use&fid=' . $fuid . '&todo=addedit&itemid=' . $useID.'&idIns='.$idIns.'">Edit record</a></td>
				</tr>
			</tbody>
		</table>-->';
				
		$blist .= '<!--cellpadding="4"  cellpadding="2"--><table width="100%" cellspacing="0"  border="0" class="std">
			<tbody><tr>
				<td width="100%" valign="top">
					<table cellspacing="1" cellpadding="4">
						<tbody>';
	}
	if($xmode === 'add'){
		$blist .= '<h1>'.$AppUI->_($wz->formName).'</h1>';
		
	}
	$subCnt = 0;

	$subTables = explode(",", $wz->formSubs);
	$subRowSet = array();
	$multiplicity = $wz->getMultiplicity($fuid);
	//$countdata = $wz->countByClientId($wz->getTableName(),$client_id);
	if ($useID > 0 || $xmode === 'add') {
		if (isset($_GET['todo']) && trim($_GET['todo']) == 'addedit')
			if($multiplicity=="One" && (int)$countdata>0){
				echo "<h1>".$AppUI->_("The data is already registry for this beneficiery")."</h1>";
				return;
			}
			
		$blist .= $wz->getDefaultFields(false, $dvals);
		/*$q = new DBQuery();
		$q->addTable("form_master");
		$q->addQuery("parent_id");
		$q->addWhere("id=".$fuid);
		$parent_id = $q->loadResult();*/
		$blist .= '<input type="hidden" name="id" value="'.$useID.'"/>';
		if($parent_id){
			/*$blist .= "<tr><td>Inscription"."</td>";
			
			$blist .= "<td>"."</td></tr>";*/
			$blist .= $wz->getDefaultFields(false, $dvals);
			/*$q = new DBQuery();
			$q->addTable("wform_".$parent_id);
			$q->addQuery("parent_id");
			$q->addWhere("id=".$fuid);*/
			$blist .= '<input type="hidden" name="id" value="'.$useID.'"/><input type="hidden" name="ref" value="'.$parent_id.'"/>';
		}
		/* echo '<pre>';
		var_dump($wz->fields);
		echo '</pre>'; */
		$classv = ' class="" ';
		//$classv = '';
		$compter = 0;
		$compterp = 0;
		$arehidden = false;
		$temp = '';
		$sections = array();
		foreach ($wz->fields as $fld_id => $fld) {
			/* if($compter >= 15){
				$classv = ' class="hidefp" ';
				$arehidden = true;
			} */
			$compter++;
			if (isset($fld['otm']) && count($fld['subs']) > 0) {
				
				$subRowSet = array();
				if ($fld['otm'] === true) {
					$compter--;
					 $blist = "<tr ".$classv.">
						<td><h3>" . $fld['name'] . "</h3></td>
						<td>
							<table border='0' cellpadding='2' cellspacing='2' class='usub " . ($xmode === 'view' || $wz->registry ? 'tbl' : '') . "'>
					<thead><tr>"; 
					/* $blistsec = "<table border='0' cellpadding='2' cellspacing='2' class='usub " . ($xmode === 'view' || $wz->registry ? 'tbl' : '') . "'>
					<thead><tr>"; */
					
					/* if($xmode==='view')
						$pdfhtml .= "<tr>
						<td><h3>" . $fld['name'] . "</h3></td>
						<td>
							<table border='0' cellpadding='2' cellspacing='2' class='usub " . ($xmode === 'view' || $wz->registry ? 'tbl' : '') . "'>
					<thead><tr>"; */
					
					$headsind = 0;
					foreach ($fld['subs'] as &$fsub) {
						$headsind++;
						$blist .= "<th>" . $fsub['name'] . "</th>";
						/* if($xmode==='view')
							$pdfhtml .= "<th>" . $fsub['name'] . "</th>"; */
					}
					$blist .= ($xmode !== 'view' ? '<th>&nbsp;</th>' : '') . '</tr></thead><tbody>';
					//$pdfhtml .= '</tr></thead><tbody>';
					if ($useID > 0) {
						$fld['table'] = $subTables[$subCnt];
						$sql = 'select * from ' . $subTables[$subCnt] . ' where wf_id="' . $idIns . '"';
						$res = mysql_query($sql);
						if ($res && mysql_num_rows($res) > 0) {
							while ($srow = mysql_fetch_assoc($res)) {
								$subRowSet[] = $srow;
							}
						}
					}
					if($xmode !== 'view'){
						if (count($subRowSet) === 0) {
							$subRowSet[0] = array_fill(0, count($fld['subs']), null);
						}
					}
					
					$fldprefix = str_replace('fld_', '', $fld['dbfld']);
					$tlist = '';
					if (count($subRowSet) > 0) {
						$preI = $wz->preIndex();
						foreach ($subRowSet as $sy => &$srset) {
							$tlist .= '<tr>';
							$wz->postIndex($preI);
							foreach ($fld['subs'] as $sid => &$fsub) {
								$tlist .= $wz->outputField($fld_id,$fldprefix . '[' . $sy . '][' . $fsub['dbfld'] . ']', $fsub, $srset[$fsub['dbfld']], true);
								
								/* if($xmode==='view')
									$pdfhtml .= $wz->outputField($fldprefix . '[' . $sy . '][' . $fsub['dbfld'] . ']', $fsub, $srset[$fsub['dbfld']], true); */
							}
							$tlist .= ($xmode !== 'view' ? '<td><div class="fbutton delRow"></div></td>' : '') . '</tr>';
							//$pdfhtml .= $tlist;
						}
					}else{
						$tlist .= '<tr><td colspan="'.$headsind.'" align="center">'.$AppUI->_('No Entries').'</td>';
					}
					++$subCnt;
					/* $blist .= $tlist . '</tbody></table>' .
							($xmode != 'view' ? '<br>
									<input type="button" onclick="frm.addSubRow(this);" value="new entry" class="text">'
									: ''); */
					 $blist .= $tlist . '</tbody></table>' .
						($xmode != 'view' ? '<br>
									<input type="button" onclick="frm.addSubRow(this);" value="new entry" class="text">
										</td></tr>'
							: '');
					/* if($xmode==='view')
						$pdfhtml .= '</tbody></table></td></tr>'; */
					$sections[] = $fld;

				} elseif ($fld['tout'] === true) {
					$blist .= "<tr ".$classv.">
							<td colspan='2'>
							<table border='0' cellpadding='2' cellspacing='2' class='usub " . ($xmode === 'view' || $wz->registry ? 'tbl' : '') . "'>
								<thead>
									<tr><th>&nbsp;</th>";
					/* if($xmode==='view')
						$pdfhtml .= "<tr>
							<td colspan='2'>
							<table border='0' cellpadding='2' cellspacing='2' class='usub " . ($xmode === 'view' || $wz->registry ? 'tbl' : '') . "'>
								<thead>
									<tr><th>&nbsp;</th>"; */
					
					$firsttab = $fld['subs'][0];
					if ($firsttab['type'] === 'checkbox' || $firsttab['type'] === 'radio') {
						$columns = $wz->getValues($firsttab['type'], $firsttab['sysv'], false, true, $firsttab['other']);
						$tcols = 0;
						foreach ($columns as $vid => $vcol) {
							if (!is_array($vcol)) {
								$blist .= '<th>' . $vcol . '</th>';
								/* if($xmode==='view')
									$pdfhtml .= '<th>' . $vcol . '</th>'; */
								++$tcols;
							}
						}
						$blist .= '</tr>
							</thead>
							<tbody>';
						/* if($xmode==='view')
							$pdfhtml .= '</tr>
							</thead>
							<tbody>'; */
						foreach ($fld['subs'] as $sy => &$fsub) {
							$blist .= $wz->outputField($fld_id,str_replace('fld_', '', $fsub['dbfld']), $fsub, $dvals[$fsub['dbfld']], false, true, $tcols);
							/* if($xmode==='view')
								$pdfhtml .= $wz->outputField(str_replace('fld_', '', $fsub['dbfld']), $fsub, $dvals[$fsub['dbfld']], false, true, $tcols); */
						}
						$blist .= '</tbody>
							</table>';
						/* if($xmode==='view')
							$pdfhtml .= '</tbody>
							</table>'; */

					}
				} else {
					foreach ($fld['subs'] as $sid => &$fsub) {
						$sendVal = $dvals[$fsub['dbfld']];
						if($fsub['type'] == 'entry_date' ){
							$sendVal = $dvals['entry_date'];//str_replace($search, $replace, $subject)
						}
						//echo $fld_id.' ';
						//$blist .= $wz->outputField($fld_id,str_replace('fld_', '', $fsub['dbfld']), $fsub, $sendVal);
						$row = $wz->outputField($fld_id,str_replace('fld_', '', $fsub['dbfld']), $fsub, $sendVal);
						$blist .= $row;//str_replace("##CLASS##", $classv, $row);
						/* if($xmode==='view')
							$pdfhtml .= $wz->outputField(str_replace('fld_', '', $fsub['dbfld']), $fsub, $sendVal); */
					}
					//++$subCnt;
					$subRowSet = array();
				}
			} else {
				if (($xmode === 'view' && $fld['type'] !== 'entry_date') || $xmode !== 'view') {
					$row = $wz->outputField($fld_id,str_replace('fld_', '', $fld['dbfld']), $fld, $dvals[$fld['dbfld']]);
					// && $fld['type'] != 'note'  && !$fld['otm']  && (isset($temp['otm']) && !$temp['otm'])
					/* if(($compterp % 2) === 1 && $fld['type'] != 'note' &&  (isset($temp['type']) && $temp['type'] != 'note')){
						//if(isset($temp['otm']))
							//echo $temp['otm'].'...';
						//var_dump($temp);
						
						$row = str_replace('<tr##CLASS##>', '', $row);
						$row = str_replace("</tr>", '', $row);
						//$row = str_replace("</tr>", '', $row);
						//$row = str_replace(">", '', $row);
						//echo '<script>console.log("'.$row.'");</script>';
						$blist = str_lreplace("</tr>", "", $blist);
						$blist .= $row.'</tr>';
						$compterp++;
						$compter--;
					}else{  */
						$blist .= $row;//str_replace("##CLASS##", $classv, $row);
						$pdfhtml .= $wz->outputField($fld_id,str_replace('fld_', '', $fld['dbfld']), $fld, $dvals[$fld['dbfld']]);
						/* if($fld['type'] != 'note' || (isset($temp['type']) && $temp['type'] != 'note') && (isset($temp['otm']) && !$temp['otm']))
							$compterp++;
					} */
					
					
					/* if( $compter > 1 &&  $compter % 2 == 1){
						$row
					}else{
						$blist .= str_replace("##CLASS##", $classv, $row);
						$pdfhtml .= $wz->outputField($fld_id,str_replace('fld_', '', $fld['dbfld']), $fld, $dvals[$fld['dbfld']]);
					} */
				}
				
			}
			if(isset($fld['otm']) && $fld['otm']){
				//var_dump($fld);
			}
			
			$temp = $fld;
		}
		if ($xmode === 'view'){
			if($wz->isRegisterForm($fuid)){
				$blist .= '<tr><td align="left" style="width:25%">'.$AppUI->_('Activity').'</td>';
				//$pdfhtml .= '<tr><td align="left">'.$AppUI->_('Activity').'</td>';
				$q = new DBQuery();
				$q->addTable('beneficieries');
				$q->addQuery('task_name');
				$q->addJoin('tasks', 't', 't.task_id=beneficieries.task_id');
				$q->addWhere('registry_id='.$idIns);
				$q->addWhere('form_id='.$fuid);
				$result = $q->loadColumn();
				$result = implode(', ',$result);
				if(!$result)
					$result = 'Nothing';
				$blist .= '<td style="width:50%" class="hilite" align="left">'.$result.'<td></tr>';
				//$pdfhtml .= '<td align="left">'.$result.'<td></tr>';
			}
		}
		/* if($arehidden){
			$more = $AppUI->_('More').'...';
			$less = $AppUI->_('Less');
			$blist .= '<tr><td colspan="4" align="right"><b><a href="javascript:viewMore()" id="moreless" class="moreAction">'.$more.'</b></td></tr>';
		} */
		$blist .= '</tbody></table>';
		if ($xmode !== 'view' && $teaser === false) {
			$blist .= '
				<tr>
					<td>
						<input type="button" onclick="frm.checkForm()" class="button" value="submit">
					</td>
					<td align="right">
						<input type="button" onclick="history.back(-1);" class="button" value="back">
					</td>
				</tr>
				 </table>
				 </td>
				 </tr>
				 </tbody>
				 </table>
				</form>
				<script type="text/javascript">
				window.onload = up;
				function up(){
					frm.init(' . $useID . ',' . $wz->registry . ');
				}
				</script>
				';
		} else {
			$blist .= "</tbody>
				</table>
				</td>
				</tr>
				</tbody>
				</table>";
		}
	}


	echo $blist;
	/* if ($xmode === 'view'){
		echo '<style>
					.showfp{}
					.hidefp{display:none;}
				</style>';
		echo '<script type="text/javascript">
					function viewMore(){
						ele = document.getElementById("moreless");
						classv = ele.getAttribute("class");
						if(classv === "moreAction"){
							ele.setAttribute("class","lessAction");
							ele.innerHTML = "'.$less.'";
							elementArray = document.getElementsByClassName("hidefp");
							elementArray = [].slice.call(elementArray, 0);
							for (var i = 0; i < elementArray.length; ++i)
							    elementArray[i].className = "showfp";
						}else{
							ele.setAttribute("class","moreAction");
							ele.innerHTML = "'.$more.'";
							elementArray = document.getElementsByClassName("showfp");
							elementArray = [].slice.call(elementArray, 0);
							for (var i = 0; i < elementArray.length; ++i)
							    elementArray[i].className = "hidefp";
						} 
					}
				
			  </script>';
	} */
	if ($xmode === 'view'){
		$pdfhtml .= '</table></div></body></html>';
		diskFile::putHtml($pdfhtml);
		$tabBox = new CTabBox ( ('?m=wizard&a=form_use&fid='.$fuid.'&idIns='.$idIns.'&todo=view&teaser=1&rtable=1'), '', $tab );
		//m=wizard&a=form_use&fid=29&idIns=1&todo=view&teaser=1&rtable=1
		
		$q = new DBQuery();
		$q->addTable('form_master');
		$q->addQuery('id,title,task_id,alltask');
		//$q->addWhere('registry = "0"');
		$q->addWhere('valid="1"');
		$q->addWhere('parent_id='.$fuid);
		$newforms = $q->loadList();
		//var_dump($newforms);
		$_SESSION['wiz_tab']=array();
		/* if(count($sections)>0){
			foreach ($sections as $section_id => $section){
				
				$tpos=$tabBox->add ( $dPconfig ['root_dir'] .'/modules/wizard/section_form', $section['name'].'',true,$section_id);
			}
		} */
		if(count($newforms) > 0){
			$_GET['wiz_ref'] = $idIns;
			foreach($newforms as $nid => $nform){
				$q = new DBQuery();//beneficieries
				$q->addTable('beneficieries');
				$q->addQuery("count(*)");
				$q->addWhere("form_id=".$fuid);
				$q->addWhere("task_id=".$nform['task_id']);
				$q->addWhere("registry_id=".$idIns);
				$valcount = true;//$q->loadResult();
				
				if(!$nform['alltask'] && $valcount){
					$q = new DBQuery();
					$q->addTable('wform_'.$nform['id']);
					$q->addQuery("count(*)");
					$q->addWhere("ref=".$idIns);
					$fentries=(int) $q->loadResult();
					
					$tpos=$tabBox->add ( $dPconfig ['root_dir'] .'/modules/wizard/vw_wizard', $nform['title'].' ('.$fentries.')' );
					$_SESSION['wiz_tab'][$tpos]=$nform['id'];
				}elseif($nform['alltask']){
					$q = new DBQuery();
					$q->addTable('wform_'.$nform['id']);
					$q->addQuery("count(*)");
					$q->addWhere("ref=".$idIns);
					$fentries=(int) $q->loadResult();
						
					$tpos=$tabBox->add ( $dPconfig ['root_dir'] .'/modules/wizard/vw_wizard', $nform['title'].' ('.$fentries.')' );
					$_SESSION['wiz_tab'][$tpos]=$nform['id'];
				}
				
			}
		}
		$tabBox->show('',0);
	}
	$AppUI->plainJS($wz->formJSsupport());
}