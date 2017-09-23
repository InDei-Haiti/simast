<h1>Anmweyyy</h1>
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
	chex=@@chex@@;
	rrr=@@rrr@@;
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
	function up(){
		@@extraCode@@
		prePage('out');
		tabPrepare();
	}
	window.onload=up;
</script>
