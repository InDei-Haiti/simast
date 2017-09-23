<?php
if(isset($_GET['mode']) && $_GET['mode'] !== ''){
	switch (trim($_GET['mode'])) {
		case 'dropex':
			require_once $AppUI->getModuleClass( 'files' );
			$fid=intval($_GET['dfid']);
			if($fid > 0){
				$obj = new CFile();
				$obj->load($fid);
				$res=$obj->delete();
				if(is_null($res)){
					echo 'ok';
				}
			}
			break;
		case 'transfer_back':
			$clinic_id=(int)$_GET['clinic'];
			$client_id=intval($_GET['client_id']);
			$sql='update ltp_transfers set clinic_id=null,status="0",ondate=null where client_id="'.intval($client_id).'" ';
			$res=mysql_query($sql);
			/*$q = new DBQuery();
			$q->addQuery('social_client_status');
			$q->addTable('status_client');
			$q->addWhere('social_client_id="'.$client_id.'"');
			$q->addOrder('social_entry_date desc');
			$q->addWhere('mode="status"');
			$q->setLimit(1);
			$prevState=$q->loadResult();
			if(!$prevState)$prevState=1;
			
			//$sql='update clients set client_status="'.$prevState.' where client_id="'.$client_id.'"';
			$res2=mysql_query($sql);*/
			if($res === true){
				echo "ok";
			}
			break;

		case 'makecenters':
			require_once('transfer-out.php');			
			break;			
		
		default:
			break;
	}
	return ;
}

/* $sql='select count(*) from clients c left join ltp_transfers l on c.client_id = l.client_id  
		where client_status=7 and (l.id > 0 AND l.status="0") ';
$res=mysql_query($sql);
if($res){
	$lfor=mysql_fetch_array($res);
	$lfort=$lfor[0];
} */

$needTab=1;

?>
<br/>
<DIV id="tabs" class="bigtab card">
<UL class="topnav tabs-nav" style="margin-left: 7px">
    <LI><A href="#tabs-1"><?php echo $AppUI->_('Cleaning')?></A></LI>
    <LI><A href="#tabs-2"><?php echo $AppUI->_('Queries')?></A></LI>
    <?php
          if($dPconfig['hdvi']){?>
        <LI><A href="#tabs-3"><?php echo 'HDVI v1.0'?></A></LI>
          <?php
          }
          ?>
<!-- <LI><A href="#tabs-2"><span>LTP In</span></A></LI>
<LI><A href="#tabs-3"><span>LTP Out <b><?php echo ($lfort > 0 ? ' ( '.$lfort.' )' : '');?></b></span></A></LI>
<LI><A href="#tabs-4"><span>Import</span></A></LI>
<LI><A href="#tabs-5"><span>Export</span></A></LI> -->
</ul>
<!-- start of container for cleaninng -->
<div id="tabs-1" class="mtab">
<?php
require_once('cleaning.inc.php');

?>

</div>
<div id="tabs-2" class="mtab">
 list files

</div>
<?php if($dPconfig['hdvi']){?>
<div id="tabs-3" class="mtab">
<?php

	require_once('hdvi_form.php');
?>
</div>
<?php 
	}
?>

<!-- end of container for cleaninng -->

<!-- end of container for cleaninng -->

<!-- start of container for Transfer IN -->
<!-- <div id="tabs-2" class="mtab">
<div class="mandat">
	<span onclick="$j('#tinbox').toggle();" class="fhref flink">Accept new Transfer-In</span><span class="offwall msgs" id="msg_place"></span>
	<div id="tinbox" class="myimporter">
	<form name="uptin" action="/?m=manager&suppressHeaders=1&a=parse_tin&mode=importfile" enctype="multipart/form-data" method="POST" onsubmit="return AIM.submit(this, {'onStart' : startCallback, 'onComplete' : manager.tinDone})">
			<input type="file" name="formfile" id="fultra" data-ext="tbn">
			<input type="submit" value="Import File" class="button" disabled="disabled" >			
		</form>
	</div>
</div>
<?php

?>
</div> -->
<!-- end of container for Transfer IN -->

<!-- start of container for Transfer OUT -->
<!--<div id="tabs-3" class="mtab">
<?php

?>
</div> -->
<!-- end of container for Transfer OUT -->


<!-- start of container for Import -->
<!--<div id="tabs-4" class="mtab">
	<div class="mandat">
		<span onclick="$j('#importbox').toggle();" class="fhref flink">Upload file for Import</span><span class="offwall msgs" id="msg_place"></span>
		<div id="importbox" class="myimporter">
			<form name="upq" action="/?m=manager&suppressHeaders=1&a=do_file_imp_aed" enctype="multipart/form-data" method="POST" onsubmit="return AIM.submit(this, {'onStart' : startCallback, 'onComplete' : manager.importDone})">
				<input type="file" name="formfile" id="fultra" data-ext="sbn">
				<input type="submit" value="Upload File" class="button" disabled="disabled" >
				<input type="hidden" name="mode" value="importfile">
			</form>
		</div>
	</div>
<?php
	/* if($_GET['part'] === 'importer'){
		require_once('importer.php');
		$needTab=4;
	} */
?>
</div> -->
<!-- end of container for Import -->

<!-- start of container for Export -->
<!-- <div id="tabs-5" class="mtab">
<div class="mandat">
	<form action="/?m=manager&suppressHeaders=1&a=do_system_export" onsubmit="return AIM.submit(this, {'onStart' : manager.initExport, 'onComplete' : manager.exportDone})" name='doEx' method="POST">
		<input type="submit" class="text" value="Go" id="start_ex">
		<input type="hidden" name='skey' value="">
	</form>
</div>
<?php
/* if($_GET['part'] === 'exporter'){
	require_once('exported.php');
	$needTab=5;
} */
?>
</div> -->
<!-- end of container for expport -->

</div>
<?php

$moduleScripts[]="./modules/outputs/jquery-ui.min.js";
?>
<script type="text/javascript">

window.onload=up;
var openTab=<?php echo $needTab?>;
function up(){
	xstart();
}

</script>
