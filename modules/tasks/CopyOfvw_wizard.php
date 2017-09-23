<?php
/* global $AppUI, $client_id, $obj,$baseDir;

require_once $AppUI->getModuleClass('wizard');*/
if(isset($_GET['tab'])){
	$_SESSION['selected_tab']=$_GET['fid']=$_SESSION['wiz_tab'][(int)$_GET['tab']];
}else{
	$_GET['tab']=array_search($_SESSION['selected_tab'],$_SESSION['wiz_tab']);
	$_GET['fid']=$_SESSION['wiz_tab'][(int)$_GET['tab']]=$_SESSION['selected_tab'];
} 

//include_once ($baseDir.'/modules/wizard/form_use.php');
require_once $AppUI->getModuleClass('wizard');
global $AppUI;

require_once ($AppUI->getModuleClass("clients"));

$fuid = (int)$_GET['fid'];
$client_id = (int)$_GET['client_id'];
$xmode = 'view';
$useID = false;
$useID = (int)$_GET['itemid'];
if (isset($_GET['todo']) && trim($_GET['todo']) == 'addedit') {
	$xmode = 'edit';
	if (!isset($_GET['itemid']) || $useID === 0) {
		$xmode = 'add';
	}
}
$teaser = false;
if(isset($_GET['teaser']) && $_GET['teaser'] == 1){
	$teaser = true;
}


if ($client_id === 0 && (int)$_POST['client_id'] > 0) {
	$client_id = (int)$_POST['client_id'];
}

$wz = new Wizard($xmode, $client_id, $useID);

if ($fuid > 0) {
	$dvals = array();
	$wz->loadFormInfo($fuid);

	$clientObj = new CClient();
	$clientObj->load($client_id);

	if ($_GET['todo'] === 'save') {
		$rid = (int)$_POST['id'];
		$res = $wz->saveFormData($rid);
		if ($res === true) {
			$AppUI->setMsg(' added', UI_MSG_OK, true);
		} else {
			$AppUI->setMsg(' error during saving', UI_MSG_ERROR, true);
		}
		if ($wz->registry === 0) {
			$itab = array_search($_SESSION['selected_tab'], $_SESSION['wiz_tab']);
			$AppUI->redirect('m=clients&a=view&tab=' . $itab . '&client_id=' . $clientObj->client_id);
		} else {
			$AppUI->redirect('m=clients');
		}
	}

	$wz->tableWrap();
	$blist = '';
	if ($xmode != 'view') {

		if ($clientObj->getFullname()) {
			$ttl = $useID > 0 ? "Edit Visit : " . $clientObj->getFullName() : "New Visit: " . $clientObj->getFullName();
		} else {
			$ttl = $useID > 0 ? "Edit Visit " : "New Visit ";
		}
		if($teaser === false){
			$titleBlock = new CTitleBlock($ttl, '', $m, "$m.$a");
			$titleBlock->addCrumb("?m=clients", "Clients");
			$titleBlock->addCrumbRight2("clearSelection(document.forms['changeClinical'])", "Clear All Selections");
			if ($clientObj->client_id > 0)
				$titleBlock->addCrumb("?m=clients&a=view&client_id=$clientObj->client_id", $clientObj->getFullName());
			$titleBlock->show();
		}
		$blist .= '<form action="/?m=wizard&a=form_use&todo=save&fid=' . $fuid . '" method="POST" id="wform" name="wform">';
		$blist .= '<table width="100%" cellspacing="1" cellpadding="1" border="0" class="std">
					<tbody><tr>
						<td width="100%" valign="top">
						<table>';
	} else {
		$wd = $wz->drawDigest();
		$useID = ($useID > 0 ? $useID : $wd[2]);
		$drows = $wd[1];
		$blist = $wd[0];
	}

	if ($useID > 0) {
		$sql = 'select * from ' . $wz->form_prefix . $fuid . ' where id="' . $useID . '" and client_id="' . $client_id . '" limit 1';
		$res = mysql_query($sql);
		if ($res) {
			$dvals = mysql_fetch_assoc($res);
		} else {
			$dvals = array();
		}
	}

	if ($xmode === 'view' && $useID > 0) {
		$blist .= '
		<table>
			<tbody>
				<tr>
					<td width="100%"><h1>Details on visit ' . printDate($dvals['entry_date']) . '</h1></td>
				</tr>
				<tr>
					<td width="100%"><a href="?m=wizard&a=form_use&fid=' . $fuid . '&todo=addedit&itemid=' . $useID . '&client_id=' . $client_id . '">Edit record</a></td>
				</tr>
			</tbody>
		</table>
		<table width="75%" cellspacing="0" cellpadding="4" border="0" class="std">
			<tbody><tr>
				<td width="100%" valign="top">
					<table cellspacing="1" cellpadding="2">
						<tbody>';
	}

	$subCnt = 0;

	$subTables = explode(",", $wz->formSubs);
	$subRowSet = array();

	if ($useID > 0 || $xmode === 'add') {
		$blist .= $wz->getDefaultFields(false, $dvals);
		foreach ($wz->fields as $fld_id => $fld) {
			if (isset($fld['otm']) && count($fld['subs']) > 0) {
				$subRowSet = array();
				if ($fld['otm'] === true) {
					$blist .= "<tr>
						<td>" . $fld['name'] . "</td>
						<td>
							<table border='0' cellpadding='2' cellspacing='2' class='usub " . ($xmode === 'view' || $wz->registry ? 'tbl' : '') . "'>
					<thead><tr>";
					foreach ($fld['subs'] as &$fsub) {
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
								$tlist .= $wz->outputField($fldprefix . '[' . $sy . '][' . $fsub['dbfld'] . ']', $fsub, $srset[$fsub['dbfld']], true);
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
							$blist .= $wz->outputField(str_replace('fld_', '', $fsub['dbfld']), $fsub, $dvals[$fsub['dbfld']], false, true, $tcols);
						}
						$blist .= '</tbody>
							</table>';

					}
				} else {
					foreach ($fld['subs'] as $sid => &$fsub) {
						$sendVal = $dvals[$fsub['dbfld']];
						if($fsub['type'] == 'entry_date' ){
							$sendVal = $dvals['entry_date'];
						}
						$blist .= $wz->outputField(str_replace('fld_', '', $fsub['dbfld']), $fsub, $sendVal);
					}
					++$subCnt;
					$subRowSet = array();
				}
			} else {
				if (($xmode === 'view' && $fld['type'] !== 'entry_date') || $xmode !== 'view') {
					$blist .= $wz->outputField(str_replace('fld_', '', $fld['dbfld']), $fld, $dvals[$fld['dbfld']]);
				}
			}
		}
		$blist .= '</tbody></table>';
		if ($xmode !== 'view' && $teaser === false) {
			$blist .= '
				<tr>
					<td>
						<input type="button" onclick="history.back(-1);" class="button" value="back">
					</td>
					<td align="right">
						<input type="button" onclick="frm.checkForm()" class="button" value="submit">
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
	$AppUI->plainJS($wz->formJSsupport());
}
?>
