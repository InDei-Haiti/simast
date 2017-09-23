
<link rel="stylesheet" type="text/css" href="/modules/outputs/jquery-ui.css" />
<div id="msg_note_box"><div class="note_msg ci_sprite"></div><span></span></div>
@@htmlpre@@

<DIV id="tabs" class="bigtab">
<UL class="topnav">
<LI><A href="#tabs-1"><span>Queries</span></A></LI>
<LI><A href="#tabs-2"><span>Forms</span></A></LI>
<LI><A href="#tabs-3"><span>Tables</span></A></LI>
<LI class="tabs-disabled"><A href="#tabs-4"><span>Stats</span></A></LI>
<LI><A href="#tabs-5"><span>Report</span></A></LI>
</ul>
<div id="tabs-1" class="mtab">
	<p>
		<span onclick="$j('#importbox').toggle();" class="fhref flink">
            Import query</span><span class="offwall msgs" id="msg_place">
		</span>
		<div id="importbox" class="myimporter">
			<form name="upq" action="/?m=outputs&suppressHeaders=1" enctype="multipart/form-data" method="POST"
                  onsubmit="return AIM.submit(this, {onStart: startCallback, onComplete: qurer.extractRow})">
				<input type="file" name="qfile" id="fultra" data-ext="qbn">
				<input type="submit" value="Import query" class="button" disabled="disabled" >
				<input type="hidden" name="mode" value="importquery">
			</form>
		</div>
		<table cellspacing="1" cellpadding="2" border="0" class="tbl tablesorter moretable" id="qtable">
		<thead>
		<tr>
            {*<th class="phead">&nbsp;</th>*}
            <th class="phead">Name</th>
            <th class="phead">Type</th>
	    <th class="phead">Item Type</th>
            <th class="phead">Description</th>
            <!--<th class="phead">Start Date</th>
            <th class="phead">End Date</th>-->
            <th class="phead">&nbsp;</th>
            <th class="phead">&nbsp;</th>
		</tr></thead>
        <tbody>
        @@queries@@
        </tbody>
        </table>

echo '<div id="tabs-5" class="mtab">
<div style="width:1300px;">
<div id="reportMSG" class="msgs"></div>

<div style="float:left; width: 1280px;">
<ul id="pbay" class="moretable">
 </ul>
 </div>
 <div class="tpbag">
 <div id="reportBag">
 <div id="load_ps" class="chrt_load"></div>
 <form id="datareport">
 <p>Name of report &nbsp;<input type="text" name="rep_name" class="text" id="rep_name" size="50">
    <span style="width: 480px;"> Start&nbsp;'.drawDateCalendar('rep_start','',false,'id="rep_start"',false,10).'&nbsp;&nbsp;
        End&nbsp;'.drawDateCalendar('rep_end','',false,'id="rep_end"',false,10).'&nbsp; Second Column&nbsp;<input type="checkbox" id="scol_view">
    </span>
 </p>
 <table id="reportHouse" border=0 width="95%">
    <tbody>
    <tr>
	    <td style="width: 50%; vertical-align: top;">
			 <input type="button" class="text uniClone" onclick="reporter.newSectionPre(this)" value="Add Section" style="float:left;">
			 <div style="width:0px;height: 30px;overflow: hidden;" id="candidset">
			    <div class="fbutton sec_type sec_text" title="Text section"></div>
				<div class="fbutton sec_type sec_chart" title="Chart or statistic table section"></div>
			 </div>
			 <br>
			 <table class="breport rowslist moretable">
			    <tbody></tbody>
			 </table>
		</td>
		<td id="second-column" style="display:none; width: 50%; vertical-align: top;">
			 <input type="button" class="text uniClone" onclick="reporter.newSectionPre(this)" value="Add Section" style="float:left;">
			 <div style="width:0px;height: 30px;overflow: hidden;" id="candidset1">
                <div class="fbutton sec_type sec_text" title="Text section"></div>
                <div class="fbutton sec_type sec_chart" title="Chart or statistic table section"></div>
             </div>
			 <br>
			 <table class="breport rowslist moretable">
			    <tbody></tbody>
			 </table>
		</td>
	</tr>
	</tbody>
</table>
 <input type="button" class="text" value="Save" onclick="reporter.saveReport(this,false)">
 <input type="button" class="text" value="Review" onclick="reporter.saveReport(this,true)">
 <div id="rep_ps" class="chrt_load"></div>
 </form>
 </div>
 </div>
 </div>
</div>';
echo '<div id="tabs-4" class="mtab">
<p>
		<div id="shome">
			<div class="bbox">
				<div id="fsrc" class="dgetter wider">
					<span class="areaName" style="float:left;">Fields 1</span>
					<ul id="box-home" style="list-style: none; float: left;"></ul>
				</div>
			</div>
			<div class="bbox">
				<div id="fsrcr" class="dgetter"><span class="areaName">Rows</span><ul id="rbox" class="accepter rcgetter"></ul></div>
				<div class="box22">
					<div id="fsrcc" class="dgetter wsdiv"><span class="areaName">Columns</span><ul id="cbox" class="accepter rcgetter wsels"></ul></div>
					<div class="bigger">
						<span class="areaName">Data</span>
						<div id="gbox" class="gsmall"></div>
					</div>
				</div>
				<div id="bbbox">
					<table border=0 cellpadding=2 cellspacing=1>
						<tr>
							<td><label for="sblanks">Blanks</label></td><td><input type="checkbox" id="sblanks" ></td>
							<td><label for="sunqs">Unique</label></td><td><input type="checkbox" id="sunqs" ></td>
						</tr>
						<tr>
							<td>Row&nbsp;&nbsp;<label for="stots-rows">Subtotals</label></td><td><input type="checkbox" id="stots-rows" ></td>
							<td><label for="sperc-rows">Percent</label></td><td><input type="checkbox" id="sperc-rows" ></td>
						</tr>
						<tr>
							<td>Col&nbsp;&nbsp;&nbsp;<label for="stots-cols">Subtotals</label></td><td><input type="checkbox" id="stots-cols" ></td>
							<td><label for="sperc-cols">Percent</label></td><td><input type="checkbox" id="sperc-cols" ></td>
						</tr>
						<tr>
							<td colspan="2">
								<label for="delta-count">Count CHANGE
									<input type="checkbox" id="delta-count" value="1">
								</label>
							</td>
							<td colspan="2">
								<label for="records">Records
									<input type="checkbox" id="records" value="1">
								</label>
							</td>
						</tr>
						<tr id="colgroupz">
							<td>Fields&nbsp;&nbsp;<label for="retile">Tile</label></td><td><input type="radio" value="merge" name="wayofg" id="retile" ></td>
							<td><label for="regrp">Regroup</label></td><td><input type="radio" value="summ" name="wayofg" checked="checked" id="regrp"></td>
						</tr>
					</table>
				<ul class="statcolx">
					<li><input type="button" class="button stab_let" value="Go" disabled="disabled" onclick="stater.run();" id="launchbut">&nbsp;&nbsp;&nbsp;</li>
					<li><input type="button" class="button stab_let purestat" value="Pop Out" onclick="popTable(\'tthome\');" disabled="disabled" ></li>
					<li><input type="submit" class="button stab_let purestat" value="Export " disabled="disabled" onclick="document.stsave.submit();"></li>
					<li><input type="submit" class="button stab_let" value="Save Query" disabled="disabled" onclick="stater.saveDialog();"></li>
					<li><input type="button" class="button stab_let" value="Clear" onclick="stater.pclean();" id="bclean"></li>
					<li><input type="button" class="button stab_let" value="Chart" onclick="grapher.start();" id="gr_but"></li>
					<li>
					<div id="chart_pref"><div id="dx_kill" onclick="grapher.hideOpts();">X</div>
					<select id="chart_type" class="text" onchange="grapher.pieOpts()">
						<option value="bars">Bars</option>
						<option value="pbars">Percent Bars</option>
						<option value="sbars">Stocked Bars</option>
						<option value="lines">Lines</option>
						<option value="pie">Pie</option>
					</select><br>
					<span style="width: 100%;float:left;"><input type="button" value="Show" class="text" onclick="grapher.build()" >
					<div class="chrt_load"></div></span>
				</div></li>

				</ul>


					<div id="load_progress"></div>
				</div>
			</div>

			</form>
		</div><br>
		<div id="stat_tab_holder" title="Pick whole Statistic table" data-rep_item="stat" class="ianchor"></div>
		<span id="tthome">
		'.$thtml.'
		</span>
		<div id="graph_home"></div>
	</p>
</div>
</div>
<form method="post" action="/?m=outputs&suppressHeaders=1&a=calc" style="width: 50px;float:left;" name="stsave">
<input type="hidden" name="mode" value="save">
</form>';

if($thtml !=''){
	$grinit=true;
}else{
	$grinit=false;
}
unset($html,$thtml,$rhtml);
flush_buffers();
?>
<div id="debox" title="Edit saved query">Name:&nbsp;<input type='text'
	style='border: 1px solid black; width: 150px;' id='qname' class='qnsvd'
	value=''><br>
Description: <textarea cols='34' rows='2' id='qdesc' class='qdsvd'></textarea><br>
<input type='hidden' id='quid' value=''> <label><input type="checkbox"
	id="brest" style="display: none;">Build result table</label><br>
<table class="dates">
	<tr>
		<td>Start date</td>
		<td><input class="datepicker" id="qstart_date" name="beginner"
			disabled="disabled" value=""> <a href="#"
			onclick="popRCalendar('qstart')"> <img src="/images/calendar.png"
			width="16" height="16" alt="Calendar" border="0"> </a> <input
			type="hidden" class="datepicker" name="filter_qstart" value="" /></td>
	</tr>
	<tr>
		<td>End date</td>
		<td><input class="datepicker" id="qend_date" name="qend_date" disabled="disabled" value="">
			<a href="#" onclick="popRCalendar('qend');">
				<img src="/images/calendar.png" width="16" height="16" alt="Calendar" border="0">
			</a>
			<input type="hidden" class="datepicker" name="filter_qend" value="" /></td>
	</tr>
</table>
<input type='button' class='button' value='Save' onclick='qurer.editQuery();'> &nbsp;&nbsp;
<input type='button' class='button' id='dbox-kill' value='Cancel' onclick='qurer.closeEdit();'>
<div id='slogo' class='saving'></div>
</div>

<div id='stip'></div>
<div id='mbox'></div>
<div id="filbox" style="position: absolute; display: none;"
	class="filter_box box1">
<div id="menu">
<ul id="toplevel">
	<li>
		<div class="sib asci"></div>
		<span class="fhref" onclick="gpgr.ifsort('desc');">Sort Asc</span>
	</li>
	<li>
		<div class="sib desci"></div>
		<span class="fhref" onclick="gpgr.ifsort('asc');">Sort Desc</span>
	</li>
	<li>
		<div class="sib coli"></div>
		<span class="fhref" onclick="filmter.lects(this);">Values</span>
	</li>
	<li id="lbl">
		<span class="fillink" onclick="filmter.showfils(this);">Filters</span>
		<div class="sib"><input type="checkbox" id="fil_on" data-area="" value="1" onchange="filmter.checkFilter(this);" disabled="disabled" class="superbox"></div>
	</li>
</ul>
</div>
</div>
<div id="fil_list" class="filter_box box2"></div>
<div id="filin_list" class="filter_box box3"></div>
<div id="fil_stats" class="filter_box box4"></div>
<div id="shadow" style="display: none"></div>
<div id="selected-result"></div>
<div id="rep_note"></div>
<div style="display: none;" id="secadder">
	<input type="button" class="text uniClone" onclick="reporter.newSectionPre(this,true)" value="Add Section" style="float:left;">
</div>
<script type="text/javascript">
 	var chartMode=false,img=document.createElement("img");img.src="modules/outputs/images/icns.png";img=document.createElement("img");img.src="modules/outputs/tab.png";img=document.createElement("img");img.src="images/icons/bg.gif";img=document.createElement("img");img.src="images/icons/desc.gif";img=document.createElement("img");img.src="images/icons/asc.gif";img=null;
	chex=<?php
	echo ($mi - 1);
	?>;
	rrr=<?php echo $y;?>;
	today=<?php echo date("Ymd");?>;
	fakes=<?php echo json_encode($f);?>;
	btr=<?php echo json_encode($l); ?>;
	heads=<?php echo json_encode($h); ?>;
	lets=<?php echo json_encode($u); ?>;
	selects=<?php echo json_encode($sels); ?>;
	tgt=<?php echo $ftabsel;?>;
	aopen=<?php echo json_encode($auto_open);?>;
	st_do=<?php echo $staterd;?>;
	rqid=<?php echo $rqid;?>;
	refs=<?php echo json_encode($r);?>;
	plus=<?php echo json_encode($p);?>;
	rels=<?php echo json_encode($rl);?>;
	pf=<?php echo json_encode($preFils);?>;
	<?php echo 'var multistart='.$js_comm.';'; ?>
	function up(){
	<?php
	if(strlen($thtml) > 0){
		echo '$j("#tthome").show();';
	}
	if($_POST['stype'] ===  'Stats' || $_POST['stype'] ===  'Chart'){
		unset($svals['list']);
		$svals['rbox']=$svals['rows'];
		unset($svals['rows']);
		$svals['cbox']=$svals['cols'];
		unset($svals['cols']);
		echo 'fstatp='.json_encode($svals).';';
		
	}
	if(is_array($chartDerectives) && count($chartDerectives) > 0){
		echo 'chartMode='.json_encode($chartDerectives).';';
	}
	?>
	prePage('out');
	tabPrepare();

	}
	//window.onload=up;
</script>