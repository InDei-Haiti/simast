<script type="text/javascript" src="http://code.highcharts.com/highcharts.js"></script>
<script type="text/javascript" src="http://code.highcharts.com/modules/exporting.js"></script>
<script type="text/javascript" src="http://canvg.github.io/canvg/rgbcolor.js"></script>
<script type="text/javascript" src="http://canvg.github.io/canvg/StackBlur.js"></script>
<script type="text/javascript" src="http://canvg.github.io/canvg/canvg.js"></script>


<div id="tabs-5" class="mtab">
    <div style="width:1280px;">
        <div id="reportMSG" class="msgs"></div>

        <div style="float:left; vertical-align: top;">
            <!-- <ul id="pbay" class="moretable"> </ul> -->
            <ul id="rep_selector">
                <li class="rep_link rep_link_on" data-mode="details">Report Details</li>
                <li class="rep_link" data-mode="builder">Report Builder</li>
                <li class="rep_link" data-mode="preview">Report Preview</li>
            </ul>
            <div class="tpbag">
                <div id="reportBag">
                    <div id="load_ps" class="chrt_load"></div>
                    <form id="datareport">
                        <div id="rpdata_details" class="rpparts">
                            <table>
                                <tr>
                                    <td>Name:</td>
                                    <td><input type="text" name="rep_name" class="text" id="rep_name" size="50"></td>
                                </tr>
                                <tr>
                                    <td>Description:</td>
                                    <td><input type="text" name="rep_desc" class="text" id="rep_desc" size="50"></td>
                                </tr>
                                <tr>
				    <td>Department:</td>
				    <td>@@dept_selector@@</td>
                                </tr>
                                
                                <tr>
                                    <td>Start Date:</td>
                                    <td>@@cal_start@@</td>
                                </tr>
                                <tr>
                                    <td>End Date:</td>
                                    <td>@@cal_end@@</td>
                                </tr>
                            </table>
                            <!-- Second Column&nbsp;<input type="checkbox" id="scol_view"> -->
                        </div>
                        <div id="rpdata_builder" class="rpparts">
                            <table id="reportHouse" border=0 width="95%">
                                <tbody>
                                <tr>
                                    <td style="width: 95%; vertical-align: top;">
                                        <input id="vrai" type="button" class="text uniClone button ce pi ahr"
                                               onclick="reporter.newSectionPre(this,false,true)"
                                               value="Add Section" style="float:left;">

                                        <div style="width:0px;height: 30px;overflow: hidden;" id="candidset">
                                            <div class="fbutton sec_type sec_text" title="Text section"></div>
                                            <div class="fbutton sec_type sec_chart"
                                                 title="Chart or statistic table section"></div>
                                        </div>
                                        <br>
                                        <table class="breport rowslist moretable">
                                            <tbody></tbody>
                                        </table>
                                    </td>
                                    <td id="second-column" style="width: 50%; vertical-align: top;">
                                        <!--<input type="button" class="text uniClone"
                                               onclick="reporter.newSectionPre(this,false,true)"
                                               value="Add Section" style="float:left;">-->

                                        <div style="width:0px;height: 30px;overflow: hidden;" id="candidset1">
                                            <div class="fbutton sec_type sec_text" title="Text section"></div>
                                            <div class="fbutton sec_type sec_chart"
                                                 title="Chart or statistic table section"></div>
                                        </div>
                                        <br>
                                        <table class="breport rowslist moretable">
                                            <tbody></tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>

                            </table>
                            <input type="button" class="text button ce pi ahr"  value="Save" onclick="reporter.saveReport(this,false)">
                            <input type="button" class="text uniClone button ce pi ahr" style="float: right;"
                              onclick="reporter.newSectionPre($('#vrai'),false,true)"
                              value="Add Section" style="float:left;">

                            <!-- <input type="button" class="text" value="Review" onclick=""> -->
                            <div id="rep_ps" class="chrt_load"></div>
                        </div>
                    </form>
                    <div id="rpdata_preview" class="rpparts"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="tabs-4" class="mtab">
    <p>

    <div id="shome">
        <div class="bbox">
            <div id="fsrc" class="dgetter wider">
                <span class="areaName" style="float:left;">Fields</span>
                <ul id="box-home" style="list-style: none; float: left;"></ul>
            </div>
        </div>
        <div class="bbox" id="boxz">
            <div id="fsrcr" class="dgetter"><span class="areaName">Rows</span>
                <ul id="rbox" class="accepter rcgetter"></ul>
            </div>
            <div class="box22">
                <div id="fsrcc" class="dgetter wsdiv"><span class="areaName">Columns</span>
                    <ul id="cbox" class="accepter rcgetter wsels"></ul>
                </div>
                <div class="bigger">
                    <span class="areaName">Data</span>

                    <div id="gbox" class="gsmall"></div>
                </div>
            </div>
            <div id="bbbox">
                <table border=0 cellpadding=2 cellspacing=1>
                    <tr>
                        <td><label for="sblanks">Blanks</label></td>
                        <td><input type="checkbox" id="sblanks"></td>
                        <td><label for="sunqs">Unique</label></td>
                        <td><input type="checkbox" id="sunqs"></td>
                    </tr>
                    <tr>
                        <td>Row&nbsp;&nbsp;<label for="stots-rows">Subtotals</label></td>
                        <td><input type="checkbox" id="stots-rows"></td>
                        <td><label for="sperc-rows">Percent</label></td>
                        <td><input type="checkbox" id="sperc-rows"></td>
                    </tr>
                    <tr>
                        <td>Col&nbsp;&nbsp;&nbsp;<label for="stots-cols">Subtotals</label></td>
                        <td><input type="checkbox" id="stots-cols"></td>
                        <td><label for="sperc-cols">Percent</label></td>
                        <td><input type="checkbox" id="sperc-cols"></td>
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
                    <tr>
                        <td colspan="4">
                            <label for="records">Data records in percent
                                <input type="checkbox" id="datapercent" value="1">
                            </label>
                        </td>
                    </tr>
                    <tr id="colgroupz">
                        <td>Fields&nbsp;&nbsp;<label for="retile">Tile</label></td>
                        <td><input type="radio" value="merge" name="wayofg" id="retile"></td>
                        <td><label for="regrp">Regroup</label></td>
                        <td><input type="radio" value="summ" name="wayofg" checked="checked" id="regrp"></td>
                    </tr>
                </table>
                <ul class="statcolx">
                    <li><input type="button" class="button ce pi ahr stab_let" value="Go" disabled="disabled"
                               onclick="stater.run();" id="launchbut">&nbsp;&nbsp;&nbsp;</li>
                    <li><input type="button" class="button ce pi ahr stab_let purestat" value="Pop Out"
                               onclick="popTable('tthome');" disabled="disabled"></li>
                    <li><input type="submit" class="button ce pi ahr stab_let purestat" value="Export " disabled="disabled"
                               onclick="document.stsave.submit();"></li>
                    <li><input type="submit" class="button ce pi ahr stab_let" value="Save Query" disabled="disabled"
                               onclick="stater.saveDialog();"></li>
                    <li><input type="button" class="button ce pi ahr stab_let" value="Clear" onclick="stater.pclean();"
                               id="bclean"></li>
                    <li><input type="button" class="button ce pi ahr stab_let" value="Chart" onclick="grapher.start();"
                               id="gr_but"></li>
                    <li>
                        <div id="chart_pref">
                            <div id="dx_kill" onclick="grapher.hideOpts();">X</div>
                            <select id="chart_type" class="form-control" style="width:300px" onchange="grapher.pieOpts()">
                                <option value="bars">Bars</option>
                                <option value="pbars">Percent Bars</option>
                                <option value="sbars">Stocked Bars</option>
                                <option value="lines">Lines</option>
                                <option value="pie">Pie</option>
                                <option value="geoChart">Geo Chart</option>
                            </select><br>
                            <span style="width: 100%;float:left;">
                                <input type="button" value="Show" class="button ce pi ahr text" onclick="grapher.build()">
                                <div class="chrt_load"></div>
                            </span>
                        </div>
                    </li>
                </ul>
                <div id="load_progress"></div>
            </div>
        </div>
        </form>
    </div>
    <br/>
    <div id="stat_tab_holder" data-rep_item="stat" class="ianchor">
        <ul style="list-style-type: none;margin: 0px !important;    -webkit-padding-start: 0px !important;">
            <li style="display: inline-block;margin:0px;border: 1px solid #eceeef !important;padding:5px;width: 50px;height: 30px"><span id="pick_table" class="fa fa-table" style="color: #354c8c" title="Pick whole Statistic table"></span></li>
            <!--<li style="display: inline-block;margin-left:-3px;border: 1px solid #eceeef;border-left:none;padding:5px;width: 50px;height: 30px"><span class="fa fa-file-text" style="color: #354c8c" onclick="popupDescStats()" title="Description"></span></li>-->
            <li style="display: inline-block;margin-left:-3px;border: 1px solid #eceeef;border-left:none;padding:5px;width: 50px;height: 30px" onclick="addTableToDashboard()"><span class="fa fa-dashboard" style="color: #354c8c" title="Add to Dashboard"></span></li>
        </ul>
    </div>
    <span id="tthome">
    @@thtml@@
    </span>
    <div id="graph_tab_holder" title="Pick whole Statistic graph" data-rep_item="graph" class="ianchor">
        <ul style="list-style-type: none;margin: 0px !important;    -webkit-padding-start: 0px !important;">
            <li style="display: inline-block;margin:0px;border: 1px solid #eceeef !important;padding:5px;width: 50px;height: 30px"><span id="pick_graph" class="fa fa-bar-chart" style="color: #354c8c" title="Pick whole Statistic table"></span></li>
            <!--<li style="display: inline-block;margin-left:-3px;border: 1px solid #eceeef;border-left:none;padding:5px;width: 50px;height: 30px"><span class="fa fa-file-text" style="color: #354c8c" onclick="popupDescStats()" title="Description"></span></li>-->
            <li style="display: inline-block;margin-left:-3px;border: 1px solid #eceeef;border-left:none;padding:5px;width: 50px;height: 30px" onclick="addGraphToDashboard()"><span class="fa fa-dashboard" style="color: #354c8c" title="Add to Dashboard"></span></li>
        </ul>
    </div>
    <div id="graph_home">

    </div>
    </p>
</div>
</div>
<form method="post" action="/?m=outputs&suppressHeaders=1&a=calc" style="width: 50px;float:left;" name="stsave">
    <input type="hidden" name="mode" value="save">
</form>
