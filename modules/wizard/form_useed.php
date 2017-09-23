<?php
/**
 * Created by JetBrains PhpStorm.
 * User: stig
 */
global $AppUI,$project_id;

echo '<br /><br /> <div class="card">';
require_once ($AppUI->getModuleClass("clients"));

$fuid = (int)$_GET['fid'];
$parent_id = (int)$_GET['parent_id'];
$dialog = (int)$_GET['dialog'];
$idIns = (int)$_GET['idIns'];
$task_id = (int)$_GET['task_id'];
$xmode = 'view';
$useID = false;
$useID = $idIns;

$q = new DBQuery();
$q->addTable('form_master');
$q->addQuery('project_id');
$q->addWhere('id='.$fuid);
$project_id = $q->loadResult();

$q->clear();

if (isset($_GET['todo']) && trim($_GET['todo']) == 'addedit') {
	$xmode = 'edit';
	if (!isset($_GET['itemid']) || $useID === 0) {
		$xmode = 'add';
	}
}
//echo $xmode." ".$project_id;
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
//	echo "<pre>";
//	echo json_encode($wz->fields);die();
	$clientObj = new CClient();
	$clientObj->load($client_id);

	if ($_GET['todo'] === 'save') {
		$rid = (int)$_POST['id'];
		//if($parent_id)
			//$wz->parent = $parent_id;
		$ref = $_GET['ref'];
		if(!$ref)
			$ref = 0;
		$res = $wz->saveFormData($_POST,$ref,$rid);
		if($res && $res !==true ){
			$redirect = 'm=wizard&a=form_use&fid='.$fuid.'&idIns='.$res.'&todo=view&teaser=1&rtable=1&tab=0';
			$res = true;
		}
		if ($res === true) {
			$AppUI->setMsg(' added', UI_MSG_OK, true);
		} else {
			$AppUI->setMsg(' error during saving', UI_MSG_ERROR, true);
		}
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
		$blist .= '<table cellspacing="1" cellpadding="1" border="0"  width="auto" class="mtab">
					<tbody><tr>
						<td width="100%" valign="top">
						<table class="mtab">';
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
	if($project_id == 362){
		if($dialog && $parent_id){
			$datepay = dPgetParam($_GET, 'datepay',null);
			//$datepay = explode('-',$datepay);
			$from = date("Y-m-01", strtotime($datepay));
			$to = date("Y-m-t", strtotime($datepay));
			$sql = 'select * from ' . $wz->form_prefix . $fuid . ' WHERE ref='.$parent_id.' AND fld_0 BETWEEN  "'.$from.'" AND "'.$to.'"  limit 1';
			$res = mysql_query($sql);
			if ($res) {
				$dvals = mysql_fetch_assoc($res);
			} else {
				$dvals = array();
			}
			$useID = $dvals['id'];
			$idIns = $useID;
		}
	}

	$titleBlock = new CTitleBlock($ttl, '', $m, "$m.$a");
		
	//$titleBlock->addCrumb("?m=clients", "Clients");
	//$titleBlock->addCrumbRight2("clearSelection(document.forms['changeClinical'])", "Clear All Selections");
	//if ($clientObj->client_id > 0)
	//$titleBlock->addCrumb("?m=clients&a=view&client_id=$clientObj->client_id", $clientObj->getFullName());

	if ($xmode === 'view' && $useID > 0) {
		$titleBlock = new CTitleBlock('Details on visit ' . printDate($dvals['entry_date']), '', $m, "$m.$a");
		$titleBlock->addCrumb("?m=projects&a=view&project_id=".$project_id."&tab=2", $AppUI->_('view project'));
		$titleBlock->addCrumb("?m=projects", $AppUI->_('list projects'));
		$titleBlock->addCrumb('?m=wizard&a=form_useed&fid=' . $fuid . '&todo=addedit&itemid=' . $useID.'&idIns='.$idIns, $AppUI->_('edit record'));
		$crumb = $titleBlock->show(true);
		$blist .= '
		<!--<table>
			<tbody>
				<tr>
					<td width="100%"><h1>Details on visit ' . printDate($dvals['entry_date']) . '</h1>'.$crumb.'</td>
				</tr>
				<tr>
					<td width="100%"><a href="?m=wizard&a=form_useed&fid=' . $fuid . '&todo=addedit&itemid=' . $useID.'&idIns='.$idIns.'">Edit record</a></td>
				</tr>
			</tbody>
		</table>-->
		<table width="75%" cellspacing="0" cellpadding="4" border="0" class="std">
			<tbody><tr>
				<td width="100%" valign="top">
					<table cellspacing="1" cellpadding="2">
						<tbody>';
	}

	$subCnt = 0;
	$subTables = explode(",", $wz->formSubs);
	$subRowSet = array();
	$multiplicity = $wz->getMultiplicity($fuid);
	//$countdata = $wz->countByClientId($wz->getTableName(),$client_id);
	
	if ($useID > 0 || $xmode === 'add') {
		$blist .= '<input type="hidden" name="rid" value="'.$idIns.'"/>';
		if (isset($_GET['todo']) && trim($_GET['todo']) == 'addedit')
			if($multiplicity=="One" && (int)$countdata>0){
				echo "<h1>The data is already registry for this beneficiery</h1>";
				return;
			}

		$blist .= $wz->getDefaultFields(false, $dvals);
		/*$q = new DBQuery();
		$q->addTable("form_master");
		$q->addQuery("parent_id");
		$q->addWhere("id=".$fuid);
		$parent_id = $q->loadResult();*/
		if($parent_id){
			/*$blist .= "<tr><td>Inscription"."</td>";
			
			$blist .= "<td>"."</td></tr>";*/
			$blist .= $wz->getDefaultFields(false, $dvals);
			/*$q = new DBQuery();
			$q->addTable("wform_".$parent_id);
			$q->addQuery("parent_id");
			$q->addWhere("id=".$fuid);*/
			$blist .= '<input type="hidden" name="ref" value="'.$parent_id.'"/>';
		}
//		echo '<pre>';
//				var_dump($wz->fields);
//		echo '</pre>'; die();
		foreach ($wz->fields as $fld_id => $fld) {
			/* if($fld_id==217)
				var_dump($fld); */
//			echo "<pre>";
//			var_dump($fld);echo "</pre>";die();
			if (isset($fld['otm']) && count($fld['subs']) > 0) {
				$subRowSet = array();
				if ($fld['otm'] === true) {
					$blist .= "<tr>
						<td>" . $fld['name'] . "</td>
						<td>
							<table border='0' cellpadding='2' cellspacing='2' class='usub " . ($xmode === 'view' || $wz->registry ? 'tbl' : '') . "'>
					<thead><tr>";
					foreach ($fld['subs'] as &$fsub) {
//						Declaration des entete
						$blist .= "<th>" . $fsub['name'] . "</th>";
					}
					$blist .= ($xmode !== 'view' ? '<th>&nbsp;</th>' : '') . '</tr></thead><tbody>';
					if ($useID > 0) {
						$sql = 'select * from ' . $subTables[$subCnt] . ' where wf_id="' . $useID . '"';
						$res = mysql_query($sql);
						if ($res && mysql_num_rows($res) > 0) {
							while ($srow = mysql_fetch_assoc($res)) {
								$subRowSet[] = $srow;
							}
						}
					}
					if (count($subRowSet) === 0) {
						$subRowSet[0] = array_fill(0, count($fld['subs']), null);
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
							}
							$tlist .= ($xmode !== 'view' ? '<td><div class="fbutton delRow"></div></td>' : '') . '</tr>';
						}
					}
					++$subCnt;
					$blist .= $tlist . '</tbody></table>' .
						($xmode != 'view' ? '<br>
									<input type="button" onclick="frm.addSubRow(this);" value="new entry" class="text">
										</td></tr>'
							: '');

				} elseif ($fld['tout'] === true) {
					$blist .= "<tr>
							<td colspan='2'>
							<table border='0' cellpadding='2' cellspacing='2' class='usub " . ($xmode === 'view' || $wz->registry ? 'tbl' : '') . "'>
								<thead>
									<tr><th>&nbsp;</th>";
					$firsttab = $fld['subs'][0];
					if ($firsttab['type'] === 'checkbox' || $firsttab['type'] === 'radio') {
						$columns = $wz->getValues($firsttab['type'], $firsttab['sysv'], false, true, $firsttab['other']);
						$tcols = 0;
						foreach ($columns as $vid => $vcol) {
							if (!is_array($vcol)) {
								$blist .= '<th>' . $vcol . '</th>';
								++$tcols;
							}
						}
						$blist .= '</tr>
							</thead>
							<tbody>';
						foreach ($fld['subs'] as $sy => &$fsub) {
							$blist .= $wz->outputField($fld_id,str_replace('fld_', '', $fsub['dbfld']), $fsub, $dvals[$fsub['dbfld']], false, true, $tcols);
						}
						$blist .= '</tbody>
							</table>';

					}
				} else {
					foreach ($fld['subs'] as $sid => &$fsub) {
//						print_r($fsub);
						$sendVal = $dvals[$fsub['dbfld']];
						if($fsub['type'] == 'entry_date' ){
							$sendVal = $dvals['entry_date'];//str_replace($search, $replace, $subject)
						}
						$blist .= $wz->outputField($fld_id,str_replace('fld_', '', $fsub['dbfld']), $fsub, $sendVal);
					}
					$subRowSet = array();
				}
			} else {
				if (($xmode === 'view' && $fld['type'] !== 'entry_date') || $xmode !== 'view') {
					$blist .= $wz->outputField($fld_id,str_replace('fld_', '', $fld['dbfld']), $fld, $dvals[$fld['dbfld']]);
				}
			}
		}
		$blist .= '</tbody></table>';
		if ($xmode !== 'view' && $teaser === false) {
			$blist .= '
				<tr>
					<td>
						<input type="button" onclick="frm.checkForm()" class="button" value="'.$AppUI->_('submit').'">
					</td>
					<td align="right">
						<input type="button" onclick="history.back(-1);" class="button" value="'.$AppUI->_('back').'">
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
	if ($xmode === 'view'){
		$tabBox = new CTabBox ( ('?m=wizard&a=form_use&fid='.$fuid.'&idIns='.$idIns.'&todo=view&teaser=1&rtable=1'), '', $tab );
		//m=wizard&a=form_use&fid=29&idIns=1&todo=view&teaser=1&rtable=1
		
		$q = new DBQuery();
		$q->addTable('form_master');
		$q->addQuery('id,title');
		//$q->addWhere('registry = "0"');
		$q->addWhere('valid="1"');
		$q->addWhere('parent_id='.$fuid);
		$newforms = $q->loadHashList();
		$_SESSION['wiz_tab']=array();
		if(count($newforms) > 0){
			foreach($newforms as $nid => $nform){
				$q = new DBQuery();
				$q->addTable('wform_'.$nid);
				$q->addQuery("count(*)");
				$q->addWhere("ref=".$idIns);
				$fentries=(int) $q->loadResult();
		
				$tpos=$tabBox->add ( $dPconfig ['root_dir'] .'/modules/wizard/vw_wizard', $nform.' ('.$fentries.')' );
				$_SESSION['wiz_tab'][$tpos]=$nid;
			}
		}
		$tabBox->show('',0);
	}
	$AppUI->plainJS($wz->formJSsupport());
}

echo "</div>";
?>
