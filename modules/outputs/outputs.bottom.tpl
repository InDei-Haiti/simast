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
<div id="filbox" style="position: absolute; display: none;"	class="filter_box box1">
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
<div id="msg_note_box"><div class="note_msg ci_sprite"></div><span></span></div>
<div style="display: none;" id="secadder">
	<input type="button" class="text uniClone" onclick="reporter.newSectionPre(this,true)" value="Add Section" style="float:left;">
</div>
<script type="text/javascript">
 	var chartMode=false,img=document.createElement("img");img.src="modules/outputs/images/icns.png";img=document.createElement("img");img.src="modules/outputs/tab.png";img=document.createElement("img");img.src="images/icons/bg.gif";img=document.createElement("img");img.src="images/icons/desc.gif";img=document.createElement("img");img.src="images/icons/asc.gif";img=null;
	project=@@project@@;
 	chex=@@chex@@;
	rrr=@@rrr@@;
	var countall=@@countall@@;
	today=@@today@@;
	fakes=@@fakes@@;
	btr=@@btr@@;
	heads=@@heads@@;
	lets=@@lets@@;
	selects=@@selects@@;
	tgt=@@tgt@@;
	aopen=@@aopen@@;
	st_do=@@st_do@@;
	rqid=@@rqid@@;
	refs=@@refs@@;
	plus=@@plus@@;
	rels=@@rels@@ ;
	pf=@@pf@@;
	var multistart=@@mstart@@;
	extraCode=@@extraCode@@;
	function up(){
		@@extraCode@@
		prePage('out');
		tabPrepare();
	}
	window.onload=up;
</script>
