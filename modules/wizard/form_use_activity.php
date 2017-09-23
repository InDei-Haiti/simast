<?php
global $AppUI;
global $l;
//require_once ($AppUI->getModuleClass("clients"));
$fuid = (int)$_GET['fid'];
$task_id = (int)$_GET['task_id'];
$parent_id = (int)$_GET['idIns'];
$xmode = 'view';
$useID = false;
$useID = (int)$_GET['itemid'];
$page=1;
if (isset($_GET['p']) && is_numeric($_GET['p'])) {
	$page = intval($_GET['p']);
	/* if ($page >= 1 && $page <= $nbPages) {
		// cas normal
		$current=$page;
	} else  */
	if ($page < 1) {
		// cas o� le num�ro de page est inf�rieure 1 : on affecte 1 � la page courante
		$page=1;
	}/* else {
		//cas o� le num�ro de page est sup�rieur au nombre total de pages : on affecte le num�ro de la derni�re page � la page courante
		$current = $nbPages;
	}*/
}

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
$epp = (int)$_GET['epp'];

if ($task_id === 0 && (int)$_POST['task_id'] > 0) {
	$task_id = (int)$_POST['task_id'];
}
$ref_id = 0;
//if(isset($_GET['ref']))
//$wz = new Wizard($xmode, $task_id, $useID);
$wz = new Wizard($xmode, $task_id, $client_id = 0, $useID);
//echo date("Y-m-d");
if ($fuid > 0) {
	$dvals = array();
	$wz->loadFormInfo($fuid);


	$wz->tableWrap();
	$blist = '';
	if($parent_id){	
		//$wz->parent = (int)$parent_id;
		if($epp)
			$wd = $wz->drawDigest($fuid,$parent_id,$page,$epp,true);
		else
			$wd = $wz->drawDigest($fuid,$parent_id,$page,false,true);
	}else{
		if($epp)
			$wd = $wz->drawDigest($fuid,0,$page,$epp,true);
		else
			$wd = $wz->drawDigest($fuid,0,$page,false,true);
	}
	$useID = ($useID > 0 ? $useID : $wd[2]);
	$drows = $wd[1];
	$blist = $wd[0];
	if ($useID > 0) {
		$sql = 'select * from ' . $wz->form_prefix . $fuid . ' where id="' . $useID;
		$res = mysql_query($sql);
		if ($res) {
			$dvals = mysql_fetch_assoc($res);
		} else {
			$dvals = array();
		}
	}else{
		
		$sql = 'select * from ' . $wz->form_prefix . $fuid;// . ' where id="' . $useID;
		if($parent_id){
			$sql = 'select * from ' . $wz->form_prefix . $fuid . ' where ref="' . $parent_id;
		}
		$res = mysql_query($sql);
		if ($res) {
			$dvals = mysql_fetch_assoc($res);
		} else {
			$dvals = array();
		}
	}
	$subCnt = 0;
	$subTables = explode(",", $wz->formSubs);
	$subRowSet = array();
	
	
	$blist .= $wz->getDefaultFields(false, $dvals);
	$blist .= '</tbody></table>';
	$blist .= $wd[8];
	//if(isset($drows[3]))
		//$blist .= $drows[3];
	echo $blist;
	//echo $wd[3];
	echo '
			<script type="text/javascript">';
	echo 'count = '.$wd[4];
	echo '; 
			currentNbr = '.$wd[5];
	echo ';
			server = true';
	echo ';
			trRows = '.json_encode($wd[6]);
	echo ';
			filterJs = '.json_encode($wd[7]);
	echo '  ;console.log(filterJs);
			if(filterJs.length === undefined){
				$.each(filterJs, function( index, value ) {
					console.log(index);
					console.log(value);
					var filterstab = document.getElementById(\'filterstab\');
						//filterstab.innerHTML += trRows[val]; 
					$(\'#filterstab\').append(trRows[index]);
					document.getElementById(index+\'_operator\').value = value.operator;
					if(value.value.length>1){
						document.getElementById(index+\'_value\').setAttribute(\'multiple\',true);
						console.log(value.value);
					}else{
						value.value = value.value.join("");
					}
			        console.log(value.value);
					$("#"+index+\'_value\').val(value.value);
				});
				
			}
			';
	echo '</script>';
	$AppUI->plainJS($wz->formJSsupport());
	/* echo '<script type="text/javascript">';
	echo '$(".pagebox").css("width",30);';
	echo '</script>'; */
}?>
<script type="text/javascript">
function dialogNewClient(idfw) {
	
	//$j("#dbnewsv").dialog("destroy").remove();
	var $dbox = $j('<div id="dbnewsv" title="Import Form"></div>')
	$j("<form id='formfileid'  action='/?m=wizard&a=form_import&mode=read&idfw="+idfw+"' enctype='multipart/form-data' method='POST'><table><tr><td><input type='file' id='fileimp' name='qfile'  data-ext='xls|xlsx'/></td></tr></table></form>")
		.appendTo($dbox);
	
	$dbox.dialog({
		modal: true,
		width: "400px",
		resizable: false,
		autoOpen: true,
		buttons: {
			Cancel: function () {
				$j(this).dialog("close");
				$j("#dbnewsv").remove();
			},
			Process: function () {
					 	$("#formfileid").submit();
					 }
		}
	}).prev(".ui-dialog-titlebar").css("background","#aed0ea").css("border","1px solid #aed0ea");
	
}
</script>
<script language="javascript" src="/modules/wizard/filter.js"></script>