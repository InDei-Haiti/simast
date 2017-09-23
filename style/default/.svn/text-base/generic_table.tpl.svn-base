<link rel="stylesheet" type="text/css" href="/modules/outputs/outputs.module.css"/>
<style type="text/css">
    #pagebox{
        margin-bottom: 10px !important;
    }
    @@wide_start@@
    #rtable{
        width: 100% !important;
    }
    @@wide_end@@

</style>
<div class="toolbar">
    <div class="toleft"><h1>@@pageTitle@@</h1></div>
    <div class="toright">@@toolBar@@</div>
</div>
<div id="mholder" class="toleft">
    <table class="rtable moretable" id="rtable" border="0" style="display:none;" cellpadding="2" cellspacing="1">
        <colgroup>@@colgroup@@</colgroup>
        <thead><tr>@@headers@@</tr></thead>
        <tbody>@@tableBody@@</tbody>
    </table>
</div>
<div id="pagebox"><span id="pgbs"></span>
		<span style="float:left;">&nbsp;&nbsp;&nbsp;&nbsp;Rows per page<select name="npp" onchange="gpgr.reorder(this)">
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50" selected="selected">50</option>
            <option value="100">100</option>
            <option value="200">200</option>
            <option value="500">500</option>
            <option value="-1">All</option>
        </select></span>

    <div id="cleanbox" style="display:none;float:left;margin-right: 15px;">
        <span class="fmonitor" id="fmbox"></span>
        <input type="button" class="button" onclick="cleanAllF();" disabled="disabled" value="Clear Filters"
               id="fclean">
    </div>
</div>
<div id="filbox" style="position: absolute; display: none;" class="filter_box box1">
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

                <div class="sib"><input type="checkbox" id="fil_on" data-area="" value="1"
                                        onchange="filmter.checkFilter(this);" disabled="disabled" class="superbox">
                </div>
            </li>
        </ul>
    </div>
</div>
<div id="fil_list" class="filter_box box2"></div>
<div id="filin_list" class="filter_box box3"></div>
<div id="fil_stats" class="filter_box box4"></div>
<div id='stip'></div>
<div id="shadow" style="display: none"></div>
<script type="text/javascript">
    window.onload = up;

    heads = @@header_types@@;
    btr = @@rows_data@@;
    lets = @@lects@@;
    function up(){
        $j.fn.disableSelection = function (){return this.attr("unselectable","on").css("MozUserSelect","none").bind("selectstart.ui",function(){return false})};
        prePage("site");
    }

</script>
