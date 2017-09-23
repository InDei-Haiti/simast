<?php /* PUBLIC $Id: calendar.php,v 1.6 2005/04/03 19:33:48 gregorerhardt Exp $ */
require_once( "$baseDir/classes/ui.class.php" );
require_once($baseDir . '/modules/outputs/outputs.class.php');
require_once $AppUI->getModuleClass('wizard');

if($_GET['mode'] == 'affect' && !empty($_GET['rid']) && !empty($_GET['pid']) && !empty($_GET['fid'])){
	$resultat = file_get_contents(diskFile::getBidPath());
	$resultat = json_decode($resultat,true);
	$resultat = $resultat['wform_'.$_GET['pid'].'_id'];
	if(is_array($resultat) && count($resultat)>0){
		$table = 'wf_'.$_GET['fid'].'_wf_'.$_GET['pid'];
		db_exec('TRUNCATE `'.$table.'`');
		foreach ($resultat as $id){
			$sql = "INSERT INTO `".$table."`(`id`, `rel`) VALUES ('".$_GET['rid']."',".$id.")";
			db_exec($sql);
		}
		echo 'Successfully';
	}else{
		echo 'Fail to affect list';
	}
	return;
}


$callback = isset( $_GET['callback'] ) ? $_GET['callback'] : 0;

$fid = isset( $_GET['fid'] ) ? $_GET['fid'] : 0;

$pid = isset( $_GET['pid'] ) ? $_GET['pid'] : 0;

$this_month = new CDate( $date );

$uistyle = $AppUI->getPref( 'UISTYLE' ) ? $AppUI->getPref( 'UISTYLE' ) : $dPconfig['host_style'];

/* $cal = new CMonthCalendar( $this_month );
$cal->setStyles( 'poptitle', 'popcal' );
$cal->showWeek = false;
$cal->callback = $callback;
$cal->setLinkFunctions( 'clickDay' );

if(isset($prev_date)){
	$highlights=array(
		$prev_date => "#FF8888"
	);
	$cal->setHighlightedDays($highlights);
	$cal->showHighlightedDays = true;
}

echo $cal->show(); */
$wz = new Wizard('print');
$wz->loadFormInfo($fid);
$fields = $wz->showFieldsImport();
//
$formsFlds = array();
if (count($fields['notms']) > 0) {
	foreach ($fields['notms'] as $nitem) {
		//$formsFlds[$nitem['fld']] = $nitem['title'];
		if(isset($nitem["raw"]["sysv"])){
			$formsFlds[$nitem["fld"]] = array("title"=>$nitem["title"],"type"=>$nitem["raw"]["type"],"sysv"=>$nitem["raw"]["sysv"]);
		}else{
			$formsFlds[$nitem["fld"]] = array("title"=>$nitem["title"],"type"=>$nitem["raw"]["type"]);
		}


	}
}
$digest = $wz->getDigest();


$q = new DBQuery();
$q->addTable('wform_'.$fid);
$resultat = $q->loadList();
?>
<script language="javascript">
/**
 *	@param string Input date in the format YYYYMMDD
 *	@param string Formatted date
 */
	/*function clickDay( idate, fdate ) {
		window.opener.<?php echo $callback;?>(idate,fdate);
		window.close();
		 /* border: 1px solid #a5cbf7; 
		padding: 2px;
		text-align: center;
	}*/
	function affect(pid,fid,rid){
		$j.get("?m=public&a=parent_form_selector&suppressHeaders=1&mode=affect&rid="+rid+"&fid="+fid+"&pid="+pid, function (msg) {
			if(msg != 'Fail to affect list')
				info(msg, 1);
			else info(msg, 0);
			setTimeout(function(){return true;},10000);
			//window.close();
		});
	} 
</script>
 
<table border="0" cellspacing="0" cellpadding="3" width="100%" class="std">
	
	<?php 
		echo '<tr>';
		echo '<th></th>';
		echo '<th>*</th>';
		foreach ($digest as $fld){
			echo '<th>'.$formsFlds[$fld]['title'].'</th>';
		}
		echo '</tr>';
		foreach ($resultat as $hc => $hv){
			echo '<tr>';
			echo '<td style="border: 1px solid #a5cbf7;">'.($hc + 1).'</td>';
			echo '<td style="border: 1px solid #a5cbf7;"><a href="javascript:affect('.$pid.','.$fid.','.$hv['id'].')">'.$AppUI->_('Add').'</a></td>';
			foreach ($digest as $fld){
				echo '<td style="border: 1px solid #a5cbf7;">'.$hv[$fld].'</td>';
			}
			echo '</tr>';
		}
	 ?>
</table>
