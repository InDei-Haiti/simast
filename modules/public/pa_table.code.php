<div id='stip'></div>
<div id='mbox'></div>
<div id="filbox" style="position: absolute; display: none;"
	class="filter_box box1">
<div id="menu">
<ul id="toplevel">
	<!-- <li>
		<div class="sib asci"></div>
		<span class="fhref" onclick="pf.ifsort('desc');">Sort Asc</span>
	</li>
	<li>
		<div class="sib desci"></div>
		<span class="fhref" onclick="pf.ifsort('asc');">Sort Desc</span>
	</li> -->
	<li>
		<div class="sib coli"></div>
		<span class="fhref" onclick="filmter.columnsList(this);">Columns</span>
	</li>
	<li>
		<div class="sib mfilter"></div>
		<span class="fhref" onclick="filmter.lects(this);">Filter</span>
	</li>
	<li id="lbl">
		<span class="fillink" onclick="filmter.showfils(this);">Search</span>
		<div class="sib"><input type="checkbox" id="fil_on" data-area="" value="1" onchange="filmter.checkFilter(this);" disabled="disabled" class="superbox"></div>
	</li>
</ul>
</div>
</div>
<div id="fil_list" class="filter_box box2"></div>
<div id="filin_list" class="filter_box box3"></div>
<div id="fil_stats" class="filter_box box4"></div>
<div id="col_list" class="filter_box box5"></div>
<div id="shadow" style="display: none"></div>
<div id="selected-result"></div>
<div id="rep_note"></div>