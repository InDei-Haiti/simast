var statTableHtmlData;
var itemClick;
var imgBase64;
var statsdesc;
var editorOptions = {
    width:240,
    height:35,
    controls:"bold italic underline | font size style | color highlight ",
    colors:// colors in the color popup
        ["FFF FCC FC9 FF9 FFC 9F9 9FF CFF CCF FCF ",
            "CCC F66 F96 FF6 FF3 6F9 3FF 6FF 99F F9F ",
            "BBB F00 F90 FC6 FF0 3F3 6CC 3CF 66C C6C ",
            "999 C00 F60 FC3 FC0 3C0 0CC 36F 63F C3C ",
            "666 900 C60 C93 990 090 399 33F 60C 939 ",
            "333 600 930 963 660 060 366 009 339 636 ",
            "000 300 630 633 330 030 033 006 309 303"].join(""),
    fonts:// font names in the font popup
        "Arial,Arial Black,Comic Sans MS,Courier New,Narrow,Garamond," +
            "Georgia,Impact,Sans Serif,Serif,Tahoma,Trebuchet MS,Verdana",
    sizes:// sizes in the font size popup
        "1,2,3,4,5,6,7"

};
function endRTE(obj, e) {
    var $editor = $j("#" + e),
        ntxt2 = obj.body.innerHTML,
        $par = $editor.parent();
    $editor.closest(".rte_fld").removeClass("rte_fld_mask rte_on")
        .attr("data-text", ntxt2)
        .addClass("moreview")
        .find(".cleditorMain").replaceWith(ntxt2).end()
        .prev().val(ntxt2);
    cleanSelection();
}

function uniqueID() {
    return  Math.round(new Date().getTime() / 10);
}

var reporter = (function (my) {
    var rows = [], rcell = 0, $visualRowSet, rowIndex = 1, $statHead, $statBody, $statTable, dBox, curRow, self = this, $rform = $j("#datareport"), $breport = $j("#breport", $rform),
        $brbody = $j("tbody", $breport), sections = [], sIndex = 0, lastdbox = 0, rowsPart = 0, lastFoundItem, anyColumnitem = false, $cbrbody, $rhouse = $j("#reportHouse"),
        delLink = '<span onclick="reporter.delr(this);" class="fhref" title="Delete"><img height="16" border="0" alt="Delete" width="16" src="/images/delete1.png"></span>',
        $replist = $j("#reportList"), $rlbody = $replist.find("tbody") , sdbox = [], loadSrc, curChoice = false, choice_det, ndtitle, tempAreaText = '', sitemSelector = '',
        activated = false, $rnote = $j("#rep_note"), inBays = [], startAddPoint = '', sectionsCount = 0, lastPicked, buildingSample = false, textStore = '', prevPicked = false,
        dropDOptions = {
            accept:'#box-home li, li.head-field',
            activeClass:'ui-state-hover',
            hoverClass:'ui-state-active',
            addClasses:"head-field hfc",
            greedy:true,
            tolerance:'pointer',
            drop:function (ev, $ui) {
                pasteToRowPlace($ui, ev, this);
                return true;
            }
        }, sortableOptions = {
            scroll:true,
            tolerance:'pointer',
            cursorAt:{
                top:-2,
                left:-2
            },
            stop:function (event, ui) {
                $j(this).find(".moreview").trigger("mouseout");
            },
            deactivate:function (event, ui) {
                $j(".moreview").trigger("mouseout");
            }
        },
        $sectionTypes = $j("<select/>", {"class":"section_typer text form-control","style":"width:310px"})
            .append("<option value='-1'>----</option>")
            .append("<option value='text'>Text</option>")
            .append("<option value='stat'>Stat table</option>")
            .append("<option value='graph'>Chart</option> ")
            .change(function () {
                var tob = $j(this).val();
                applyType(this, tob);
            });

    function applyType(obj, type) {
        if (type != '-1') {
            SectionAddRow($j(obj).closest(".zxrow").attr("data-sid"), type, $j(obj).closest(".table_edit_cell"));
        } else {
            return false;
        }
        return true;
    }

    function findIndex(obj) {
        var $ro = $j(obj).closest("li");
        return [$ro, $ro.attr("data-rid")];
    }

    function rowActions(method, rcell, wipeit) {
        if (wipeit === null || wipeit === undefined) {
            wipeit = false;
        }
        var row = findIndex(rcell)[1];
        if (method === 'add') {
            rowIndex = $visualRowSet.length + 1;
            var $rowClone = $j($visualRowSet[0]).clone();
            $rowClone
                .attr("data-rid", rowIndex)
                .find("input:not([type='button'])").val("").end().html(function (ixh, code) {
                    return code.replace(/rep\[\d{1,}\]/gi, "rep[" + rowIndex + "]");
                })
                .find(".demobox").text("").end()
                .find(".rtit").val("").end()
                .find(".indata").val(amnt(+sdbox) - 1);

            $rowClone.appendTo("ol", $j("#reportBag")[0]);

        }
        else if (method === 'del') {
            var $zr = $j(rcell).closest("tr"), xclass = $zr.attr("class"), pclass = xclass.match(/secsub_\d+/g),
            //sibles=$j("." + pclass[0], $brbody).length;
                sibles = $j("." + pclass[0], $j(rcell).closest(".breport").find("tbody")).length;
            if (isArray(pclass) && pclass[0] && ( (sibles > 1 && wipeit === false) || wipeit === true)) {
                $zr.slideUp("fast", function () {
                    $j(this).find(".moreview").each(
                        function () {
                            if (wipeit === false) {
                                $j(this).trigger("dblclick");
                            } else {
                                setFree(this, true);
                            }
                        }).end().remove();
                });
                delete rows[row];
            }
        }
        refreshRowSet();
    }

    function getColumn(obj, within) {
        return within ? $j(obj).closest(".breport").find("tbody") : $j(obj).parent().find(".breport > tbody");
    }


    function killSection(id, force) {
        if (force === null || force === undefined) {
            force = false;
        }
        if (force === true || confirm("Do you really want to remove this section?")) {
            var $fsection = $j("#sec_" + id),
                $lbrbody = getColumn($fsection, true),
                zmode = $fsection.attr("data-stype"), //zmode = $j("#sec_" + id, $lbrbody).attr("data-stype"),
                pretxt = '', secAct = '';
            if (zmode === 'table') {
                pretxt = "All rows ";
                secAct = 'rowActions("del",$j(this).find("td:eq(0)"),' + force + ');';
            }
            else if (zmode === 'text') {
                pretxt = "Text entered";
                secAct = '$j(this).remove();'
            }
            else if (zmode === 'graph') {
                pretxt = 'Content';
                secAct = 'rowActions("del",$j(this).find("td")[0],' + force + ');';
            }
            if (force === true || confirm(pretxt + " inside this section also will be removed")) {
                // launch killer section and rows inside
                $j("#sec_" + id, $lbrbody).next("tr.herow").andSelf().remove();
                $j(".secsub_" + id, $lbrbody).map(function () {
                    eval(secAct);
                });
            }
        }
    }

    function buildSection2(isNew) {
        var id = sIndex, postAction = '',
            xtxt = ["<tr id='sec_", sIndex, "' data-stype='' data-sid='", sIndex, "' class='slink zxrow'><td colspan='2' class='sec_ware'>",
                "<div style='float: left;'>Section name&nbsp;</div><div style='float:left;width: 350px;'>",
                "<input type='hidden' class='text offview  longwrite ichsection'  name='sec[", id, "][name]'><div class='rte_fld'/>",
                "<div style='float: right;'>",
                "<span class='fbutton delbutt' onclick='reporter.delSection(", sIndex, ",true)' title='Remove Section'></span>",
                "<span class='fbutton sceditor' onclick='reporter.editSection(", sIndex, ",this)' title='Edit Section'></span>",
                "<span class='section_move section_move_up' title='Move UP'></span>",
                "<span class='section_move section_move_down' title='Move DOWN'/></span>",
                "</div><div class='sec_cont_view'/><input type='hidden' name='sec[", sIndex, "][content]' class='sec_cont_all'>",
                "<input type='hidden' name='sec[", sIndex, "][type]' class='sec_cont_type'></td>" +
                "<td style='width: 120px;'><span class='fbutton delbutt fhref fa fa-trash-o' onclick='reporter.delSection(",sIndex,",true)' title='Remove Section'></span><span class='fbutton sceditor fa fa-pencil' onclick='reporter.editSection(",sIndex,",this)' title='Edit Section'></span><span class='section_move section_move_up fa fa-arrow-up' title='Move UP'></span><span class='section_move section_move_down fa fa-arrow-down' title='Move DOWN'></span>"+
                "</td></tr>"];
        ++sIndex;

        xtxt = xtxt.join("");
        sections[id] = 0;

        $j(xtxt).hide();
        if (startAddPoint === 'origin') {
            $j(xtxt).appendTo($cbrbody);
        } else {
            $j(xtxt).insertAfter(["tr.", (startAddPoint.replace("_", "sub_")), ":last"].join(""), $cbrbody);//.slideDown("fast");
        }

        $j("<tr/>", {"class":"secsub_" + (sIndex - 1)})
            .append("<td/>").find("td")
            .html($j("#secadder > input").clone(true)).end().insertAfter($j(".secsub_" + (sIndex - 1) + ":last", $cbrbody));

        if (isNew) {
            reporter.editSection(id, $j(".sceditor", $j("#sec_" + id)));
        }
    }

    function proceedEdit(id, obj) {
        var $sec = $j(obj).closest(".slink"), $sec_info_block = $sec.find(".sec_ware");
        var backup = $sec.html();
        $sec_info_block.empty();
        var sctype = $sec.attr("data-stype"), sname = $j(backup).find(".longwrite").val();
        sname = sname ? sname : "";
        sctype = sctype ? sctype : "-1";
        var $new_data = $j("<table/>", {"class":"table_edit_cell"})
            .append("<tr><td>Section Name:</td> <td><input type='text' class='text form-control' value='" + sname + "'></td></tr>")
            .append("<tr><td>Section Type:</td> <td></td></tr>");
        $new_data.find("tr:eq(1) > td:eq(1)").append($sectionTypes.clone(true).val(sctype));
        $sec.data("old-content", backup);
        $sec_info_block.append($new_data);
        if (sctype && sctype.length > 2) {
            if (sctype == 'text') {
                textStore = $j(backup).find(".sec_cont_all").val();
            }
            applyType($sec.find(".section_typer"), sctype);
            if (sctype != 'text') {
                $sec.find(".repits").val($j(backup).find(".sec_cont_all").val());
            }
        }
    }


    function SectionAddRow(sid, xmode, parentCell) {
        var curId = sections[sid], srid = ['[', sid, '][', curId, ']'].join(""), rtxt = [],
            $srows = $j("tr#sec_" + sid, $cbrbody).next(),
            apresent = $srows.find(".head_unit").length, afterSector = '', zclass = 'zxrow';

        $j(".sec_cont, .sec_ctrl", parentCell).remove();

        if (xmode == 'text') {
            var localtxt = 'loc_' + uniqueID();
            rtxt = rtxt.concat(["<td></td><td><textarea id='" , localtxt , "' name='rows", srid, "[title]' class='rte_box' cols=60 rows=2>", textStore, "</textarea></td></tr>"]);
        }
        else if (xmode == 'graph' || xmode == 'stat') {
            var localitems = "<tr class='sec_cont'><td>Section Element</td><td>" + sitemSelector + "</td></tr>";
            /*$j(localitems).appendTo(parentCell).find("optgroup").each(function () {
             var $tgt = $j(this).add("option", this);
             if ($j(this).attr("label").toLowerCase() == xmode) {
             $tgt.show();
             } else {
             $tgt.hide();
             }
             });*/
            $j(localitems).appendTo(parentCell).find("option").show().filter(":not(." + xmode + ")").hide();
        }

        rtxt = ["<tr class='secsub_", sid, " ", zclass, " sec_cont' data-rsid='", curId, "'>"].concat(rtxt);

        ++sections[sid];
        var $secrows = $cbrbody.find(".secsub_" + sid + ":last");

        $j(rtxt.join("")).appendTo($j(parentCell));
        if (localtxt) {
            var lopts = cloneThis(editorOptions);
            delete lopts.width;
            delete lopts.height;
            /*$j("#" + localtxt).val(textStore).cleditor(lopts);*/
            $j("#" + localtxt).val(textStore).cleditor();
            /*$j("#" + localtxt).val(textStore);
            CKEDITOR.replace( localtxt );*/
        }
        $j(["<tr class='sec_ctrl'><td><input type='button' class='text sec_app do_save button ce pi ahr' value='Apply'></td>",
            "<td style='text-align: right;'><input type='button' class='text sec_app do_cancel button ce pi ahr' value='Cancel'></td></tr>"].join(""))
            .appendTo(parentCell);
    }

    function inOrder() {
        $statTable = $j("#tthome");
        $statHead = $j("thead", $statTable);
        $statBody = $j("tbody", $statTable);
        $j("#qtable")
            .undelegate(".qreditor", "click")
            .delegate(".qreditor", "click", function (e) {
                loadRepData(e);
            });
        dBox = stater.collector();
        delete dBox.list;
        dBox.selects = selects;
        lastdbox = findMaxKey(sdbox);
        sdbox[lastdbox] = dBox;
        $j(".stabh_vis", $statTable).hover(
            function () {
                $statTable.css("background-color", "#B8B5B5");
            },
            function () {
                $statTable.css("background-color", "inherit");
            }).live("dblclick", function (e) {
            	
            	statTableHtmlData = 'table';//$j('#tthome table').html();
            	itemClick = 'stat';
            	//console.log(statTableHtmlData);
                pickerAct(e);
            });

    }

    function refreshRowSet() {
    }

    function loadRepData(e) {
        var $xrow = $j(e.target).closest("tr"), rid = $j(e.target).attr("data-id");
        loadSrc = $xrow.parent().find("tr").index($xrow);
        e.stopPropagation();
        $j("#tabs").toTab(4);
        $j.get("?m=outputs&a=reports&mode=loadinfo&suppressHeaders=1", {
            "dbrid":rid
        }, function (rdata,status,xhr) {
            // console.log(status);
            console.log(rdata);
            rdata = JSON.parse(rdata);
            console.log(rdata);
            cleanTabStat(true);
            sIndex = 0;
            inBays = [];
            $j("#reportHouse").data("fromdb", rid);
            for (var u in rdata.backdoor.bdata) {
                if (rdata.backdoor.bdata.hasOwnProperty(u)) {
                    sdbox[u] = rdata.backdoor.bdata[u];
                }
            }
            //sdbox = rdata.backdoor.bdata;

            rows = rdata.backdoor.rows.concat(rows);

            startAddPoint = "origin";
            var entries = rdata.entries, secs = entries.sec, bdata = rdata.backdoor, $srow, second = false;
            for (var colm in bdata.columns) {
                if (bdata.columns.hasOwnProperty(colm)) {
                    $cbrbody = $j(".breport:eq(" + colm + ") > tbody");
                    console.log(bdata.columns[colm]);
                    for (var i = 0, l = bdata.columns[colm].length; i < l; i++) {
                        var cursec = bdata.columns[colm][i],
                            pprow = secs[cursec], vcont;
                        if(pprow.type === -1){
                            continue;
                        }
                        pprow_name = "";
                        if(pprow.hasOwnProperty('name')) {
                            pprow_name = pprow.name;
                        }
                        if (bdata.types[i] === 'text') {
                            tempAreaText = pprow_name;
                            vcont = pprow.content;
                        } else {
                            //vcont = sdbox[pprow.content].n;
                            var pd = getSDBoxItem(pprow.content);
                            vcont = pd.n;
                        }
                        buildSection2();
                        var $pcell = $j(".slink:last", $cbrbody).attr("data-stype", pprow.type);
                        console.log(cursec);
                        console.log(pprow_name);
                        console.log(pprow);

                        /*console.log($pcell.find(".rte_fld").html()
                            .prev("input:hidden"));*/
                        $pcell.find(".rte_fld").html(pprow_name)
                            .prev("input:hidden").val(pprow_name).end()
                            .end()
                            .find(".sec_cont_view").html(vcont)
                            .next("input").val(pprow.content).end()
                            .end()
                            .find(".sec_cont_type").val(pprow.type);
                        --sIndex;
                        $srow = $j("#sec_" + sIndex, $cbrbody);

                        ++sIndex;
                    }
                }
            }

            for (var i in rdata.entries) {
                if (!isNaN(i) && rdata.entries.hasOwnProperty(i)) {
                    var crow = rdata.entries[i], $rcln = $rowClone.clone(true),
                        $vinput = $j(["<input type='text' class='rtit text' name='rep[1].title' value='", crow.title, "'>"].join(""));

                    $rcln
                        .find(".rtit").replaceWith($vinput).end()
                        .find(".hibox").val(crow.rid).end()
                        .find(".indata").val(crow.bdid).end()
                        .find(".demobox").text(rdata.backdoor.rows[crow.rid].n).end()
                        .html(
                        function (inx, code) {
                            var rcode = code.replace(/rep\[\d{1,}\]/gi, "rep[" + (parseInt(i) + 1) + "]");
                            $j($j(rcode)[0]).val(crow.title);
                            return rcode;
                        }).attr('data-rid', crow.rid)
                        .appendTo("ol", $j("#reportBag"));
                }
            }
            $rowClone = null;
            //$j("#tabs").toTab(4);
            $j("#rep_name").val(rdata.title);
            $j("#rep_dept").val(rdata.rep_dept);
            $j("#rep_desc").val(rdata.rep_desc);
            if(rdata.start_date == ""){
                $j("#rep_start").val("0000-00-00");
            }else{
                $j("#rep_start").val(rdata.start_date);
            }

            if(rdata.end_date == ""){
                $j("#rep_end").val("0000-00-00");
            }else{
                $j("#rep_end").val(rdata.end_date);
            }


            refreshRowSet();
            $j("tr", $cbrbody).show();
        });
        e.stopPropagation();
        return false;
    }

    function findMaxKey(obj) {
        var i, res, tcase = (isArray(obj) === true ? 'array' : 'object');
        for (i in obj) {
            if (tcase === 'array') {
                if (i != 'indexOf' && i != 'length' && i !== null) {
                    res = i;
                }
            }
            else if (tcase === 'object') {
                if (typeof obj === 'object' && obj.hasOwnProperty(i)) {
                    res = i;
                }
            }
        }
        return (res === undefined ? 0 : parseInt(res) + 1);
    }

    function extractDB(ind, arr) {
        var zind = 0;
        for (var i in arr) {
            if (ind == zind) {
                return {
                    field:i,
                    table:arr[i]
                };
            }
            ++zind;
        }
    }

    function pickerOff(mode) {
        rcvField(rcell);
    }
    function returnMapPicture(err,canvas){
        imgBase64 = canvas.toDataURL();
        statTableHtmlData = 'maps';
        itemClick = 'maps';
        pickerAct(e);
    }
    function pickerAct(e) {
        var lthis = e.target;
        if (lthis === lastPicked) {
            //if(!confirm("This item was already selected as report item, do u want repeat?")){
            return false;
            //}
        } else {
            prevPicked = lastPicked;
            lastPicked = lthis;
        }
        if (ndtitle = prompt("Enter name for selected item")) {
            if (!ndtitle || trim(ndtitle).length == 0) {
                info("You should enter name of item before save it", 0);
                return false;
            }
            curChoice = lthis.tagName.toLowerCase();
            choice_det = $j(lthis).attr("data-rep_item");
            if (curChoice == "td") {
                choice_det = 'cell';
                $j(lthis).addClass("cseled");
                $j("#tthome").css("cursor", "auto");
                reporter.detectParents(lthis);
            }
            else {
                var rtabd = form2object("sendAll"); // result table form data
                rcell = findMaxKey(rows);//amnt(rows);
                if (curChoice === "div" && choice_det === 'stat') {
                    // we got selected stat table
                    var spiv = ['cols', 'rows'], stxt = [];
                    for (var si = 0, sl = spiv.length; si < sl; si++) {
                        var ssi = spiv[si];
                        stxt.push(si);
                        stxt[si] = [];
                        for (var aitem in dBox[ssi]) {
                            if (typeof dBox[ssi][aitem].title === 'string') {
                                stxt[si].push(dBox[ssi][aitem].title);
                            }
                        }
                        stxt[si] = stxt[si].join(",");
                    }
                    var $ztab = $j(lthis).parent().children("table");
                    rows[rcell] = {
                        "v":stxt.join("; "),
                        "r":rtabd,
                        "n":ndtitle,
                        "d":(findMaxKey(sdbox) - 1),
                        "c":choice_det,
                        "s":{
                            'width':$ztab.width(),
                            'height':$ztab.height()
                        },
                        "t":uniqueID()
                    };

                }
                else if (curChoice === 'div' && choice_det === 'graph') {
                    //we have selected graph
                    var graphData = grapher.emulSend(true);
                    rows[rcell] = {
                        "v":"Graph",
                        "n":ndtitle,
                        "r":[rtabd, graphData],
                        "d":(findMaxKey(sdbox) - 1),
                        'c':choice_det,
                        's':{
                            'width':$j(lthis).width(),
                            'height':$j(lthis).height()
                        },
                        "t":uniqueID()
                    };
                }
                else if (curChoice === 'div' && choice_det === 'maps') {
                    //we have selected graph
                    var graphData = grapher.emulSend(true);
                    rows[rcell] = {
                        "v":"Maps",
                        "n":ndtitle,
                        "r":[rtabd, graphData],
                        "d":(findMaxKey(sdbox) - 1),
                        'c':choice_det,
                        's':{
                            'width':$j(lthis).width(),
                            'height':$j(lthis).height()
                        },
                        "t":uniqueID()
                    };
                }
                //rcvField(rcell);
                saveFieldItem(rcell);

            }
        } else {
            lastPicked = prevPicked;
        }
        return false;
    }

    function saveFieldItem(rcid) {
        datatt = ["itemfo=", JSON.stringify(rows[rcid]), "&html="+statTableHtmlData, "&sddata=", JSON.stringify(stater.collector())].join("");
        if(statTableHtmlData=='img'){
            $j.ajax({
                url:"/?m=outputs&a=reports&mode=save_item&suppressHeaders=1",
               /* data:["itemfo=", JSON.stringify(rows[rcid]), "&html="+statTableHtmlData, "&sddata=", JSON.stringify(stater.collector())].join(""),*/
                data: {
                    itemfo: JSON.stringify(rows[rcid]),
                    html: statTableHtmlData,
                    sddata: JSON.stringify(stater.collector()),
                    imgBase64: imgBase64
                },
                type:'post',
                success:function (msg) {
                    if (msg && msg.length > 0) {
                        if (parseInt(msg) > 0) {
                            //request fresh list of items
                            refreshItemsList();
                            info("Report item saved", 1);
                        } else {
                            info("Failed to save report item", 0);
                        }
                    } else {
                        info("Failed to save report item", 0);
                    }
                }
            });
        }else{
            $j.ajax({
                url:"/?m=outputs&a=reports&mode=save_item&suppressHeaders=1",
                data:["itemfo=", JSON.stringify(rows[rcid]), "&html="+statTableHtmlData, "&sddata=", JSON.stringify(stater.collector())].join(""),
                type:'post',
                success:function (msg) {
                    console.log(msg);
                    if (msg && msg.length > 0) {
                        if (parseInt(msg) > 0) {
                            //request fresh list of items
                            refreshItemsList();
                            info("Report item saved", 1);
                        } else {
                            info("Failed to save report item", 0);
                        }
                    } else {
                        info("Failed to save report item", 0);
                    }
                }
            });
        }

    }

    function refreshItemsList() {
        var localselects = [], optClass = '';
        $j.getJSON("/?m=outputs&a=reports&mode=get_item_list&suppressHeaders=1", function (data) {
            if (data) {
                localselects.push('<select class="repits text form-control">');
                localselects.push('<option value="-1">-----</option>');
                sdbox = [];
                for (var ic in data) {
                    if (data.hasOwnProperty(ic)) {
                        //localselects.push("<optgroup label='" + ic + "'>");
                        optClass = ic;
                        var ttype = data[ic], tval;
                        for (var ti in ttype) {
                            if (ttype.hasOwnProperty(ti)) {
                                //tval = JSON.parse(ttype[ti]); //$j.parseJSON(ttype[ti]);
                                //sdbox[ti] = eval(''tval + '');
                                tval = eval('(' + ttype[ti] + ')');
                                sdbox[ti] = tval;
                                localselects.push(["<option class='", optClass, "' value='", tval.t , "'>", tval.n, "</option>"].join(""));
                            }
                        }
                        //localselects.push("</optgroup>");
                    }
                }
                localselects.push("</select>");
                sitemSelector = localselects.join("");
            }
            refillSavedItems();
        });
    }

    function delReport(obj) {
        var $rid = $j(obj).closest("tr"), dbrid;
        $j("a", $rid).attr("href", function (i, xhr) {
            dbrid = xhr.match(/\d{1,}$/);
            return xhr;
        });
        $j.ajax({
            type:'get',
            url:'?m=outputs&a=reports&mode=delete&suppressHeaders=1&dbrid=' + dbrid[0],
            success:function (msg) {
                if (msg && msg.length > 0) {
                    if (msg === 'ok') {
                        //popMsg('Report template deleted.', 'ok');
                        info('Report template deleted.', 1);
                        $rid.remove();
                    }
                }
            }
        });
    }

    function cleanTabStat(leaveRows) {
        //$brbody.empty();
        $j(".breport tbody").empty();
        if (leaveRows !== true) {
            rows = [];
            $j("#tthome").find(".cseled").removeClass("cseled");
        }
        //remove all item areas
        $j(".sub_port").remove();
    }

    function prettyCamelCases(text) {
        return text.charAt(0).toUpperCase() + text.slice(1).toLowerCase();
    }

    function getSDBoxItem(rid, onlyID) {
        if (amnt(sdbox) === 0) {
            return 0;
        }
        rid = parseInt(rid);
        for (var i in sdbox) {
            if (sdbox[i] !== null) {
                if (sdbox[i].t === rid) {
                    lastFoundItem = i;
                    return onlyID ? i : sdbox[i];
                }
            }
        }
        return false;
    }

    function getRowItem(rid, onlyID) {
        if (amnt(rows) === 0) {
            return 0;
        }
        rid = parseInt(rid);
        for (var i in rows) {
            if (rows[i] !== null) {
                if (rows[i].t === rid) {
                    lastFoundItem = i;
                    return onlyID ? i : rows[i];
                }
            }
        }
        return false;
    }

    function cleanBData() {
        var used = [], ndbox = [];
        for (var r in rows) {
            if (rows[r] !== null && $j.inArray(rows[r].d, used) < 0 && !isNaN(r)) {
                used.push(rows[r].d);
            }
        }
        if (used.length > 0) {
            for (var i = 0, l = used.length; i < l; i++) {
                ndbox[used[i]] = sdbox[used[i]];
            }
            sdbox = cloneThis(ndbox);
            ndbox = null;
        }
    }

    function cleanUnUsedItems() {
        //close all sections in edit to keep all data
        $j(".do_save").each(function () {
            $j(this).trigger("click");
        });

        var used = [], nsdbox = [];
        $j(".sec_cont_type").each(function () {
            if ($j(this).val() !== 'text') {
                used.push(parseInt($j(this).closest(".sec_ware").find(".sec_cont_all").val()));
            }
        });
        if (used.length > 0 && used.length < sdbox.length) {
            /// not all items were used and we need clear report from trash data
            for (var u in sdbox) {
                if (sdbox.hasOwnProperty(u) && sdbox[u]) {
                    var tv = sdbox[ u ];
                    if ($j.inArray(parseInt(tv.t), used) >= 0) {
                        if (tv && tv.filters) {
                            tv.filters = $j.parseJSON(tv.filters);
                        }
                        nsdbox.push(tv);
                    }
                }
            }
            return nsdbox;
        } else if (used.length === 0) {
            return [];
        } else {
            return sdbox;
        }

    }

    function endAllEdits() {
        $j(".rte_fld", $j("#reportHouse")).each(function () {
            var tframe = $j(".cleditorMain iframe", this);
            if (tframe && tframe.length > 0) {
                $j(tframe[0].contentWindow.document).trigger("end_edit");
            }
        });

    }

    return {
        init:function () {
            if (activated === false) {
                inOrder();

                $j(".bpick", $j("#reportBag")[0]).live("click", reporter.pickerOn);
                $j("#rep_start").val($j("#start_date").val());
                $j("#rep_end").val($j("#end_date").val());
                refreshRowSet();
                //$j(".longwrite").live("keyup", writeOn);

                /******** Provide support for section edit buttons : SAVE AND CANCEL ********/
                $j(".sec_app").live("click", function () {
                    var $xbox = $j(this).closest(".zxrow"), back = $xbox.data("old-content");
                    if ($j(this).hasClass("do_cancel")) {
                        var test = $(".table_edit_cell tbody tr:eq(0) td:eq(1)").html();
                        console.log(test);
                        $xbox.html(back);
                    } else {
                        var sok = true, sltype, content, vcontent, sname;
                        //We go save this section
                        $j(this).closest(".table_edit_cell").find("tr").each(function (row) {
                            var $tdcell = $j("td:eq(1)", this);
                            if (row === 0) {// name of section
                                sname = $j("input", $tdcell).val();
                            } else if (row === 1) {// type of section
                                sltype = $j("select", $tdcell).val();
                            } else if (row === 2) { // for text is text-content, for others - id of picked item
                                if (sltype === 'text') {

                                    /*content = CKEDITOR.instances[$tdcell.find('textarea').attr('id')].getData();
                                    vcontent = content;*/
                                    //CKEDITOR.replace($tdcell.find('textarea').attr('id'));
                                    //console.log(CKEDITOR.instances[$tdcell.find('textarea').attr('id')].getData());
                                    //content = $tdcell.find('textarea');
                                    console.log($tdcell.find('textarea').attr('id'));
                                    var $tobj = $j(".cleditorMain > iframe", $tdcell);
                                    content = $tobj[0].contentDocument.body.innerHTML;
                                    vcontent = content;
                                } else {
                                    content = $j(".repits", this).val();
                                    vcontent = $j(".repits option:selected").text();
                                    if (content == '-1') {
                                        alert("Please select report item, or remove section");
                                        sok = false;
                                    }
                                }
                            }
                        });
                        if (sok === true) {
                            var $pcell = $j(this).closest(".slink").attr("data-stype", sltype),
                                pdata = $pcell.data("old-content");
                            $pcell.empty().append(pdata)
                                .find(".rte_fld").html(sname)
                                .prev("input:hidden").val(sname).end()
                                .end()
                                .find(".sec_cont_view").html(vcontent)
                                .next("input").val(content).end()
                                .end()
                                .find(".sec_cont_type").val(sltype);
                            textStore = '';
                        }
                    }
                });
                $j(".head_unit > input", $rhouse).live('focusin',
                    function () {
                        $j(this).removeClass("head_hint");
                    }).live('focusout', function () {
                        var tval = $j(this).val();
                        if (tval.length == 0) {
                            $j(this).addClass("head_hint");
                        }
                    });
                activated = true;

                $j(".section_move").live("click", function () {
                    endAllEdits();
                    var areas = 0;
                    var $lbrbody = getColumn(this, true);
                    $j(".rte_box", $lbrbody).each(function () {
                        var $ta = $j(this),
                            $t = $j("<input/>")
                                .attr("name", $ta.attr("name"))
                                .val($ta.val())
                                .attr("id", $ta.attr("id"))
                                .addClass("area_subst");
                        //.insertAfter($ta);
                        $ta.closest(".cleditorMain").replaceWith($t);
                        ++areas;
                    });
                    var $mysec = $j(this).closest("tr"),
                        drct = $j(this).hasClass("section_move_up") ? "up" : "down",
                        $step,
                        mypos = $j("tr.slink", $lbrbody).index($mysec);
                    if (drct == 'up') {
                        $step = $j("tr.slink:lt(" + mypos + "):last", $lbrbody);
                    } else {
                        $step = $mysec;
                        $mysec = $j("tr.slink:gt(" + mypos + "):first", $lbrbody);
                    }
                    var $bunch = $mysec.nextUntil("tr.slink");
                    if ($step && $step.length > 0) {
                        $mysec.insertBefore($step);
                        $bunch.each(function () {
                            $j(this).insertBefore($step);
                        });
                    }
                    if (areas > 0) {
                        var lopts = cloneThis(editorOptions);
                        delete lopts.width;
                        delete lopts.height;
                        $j(".area_subst").each(function () {
                            var $ti = $j(this), nid = $ti.attr("id");
                            var $n = $j(["<textarea id='", nid , "' name='", $ti.attr("name") , "' class='rte_box' cols=60 rows=3>", $ti.val(), "</textarea>"].join(""));
                            $ti.replaceWith($n);

                            $j("#" + nid).cleditor(lopts);
                            $j("#" + nid).cleditor();
                            //CKEDITOR.replace( nid );
                        });
                    }
                });
                $j("#rep_selector > li").click(function () {
                    $j(".rpparts").hide();
                    $j(this).parent().find("li").removeClass("rep_link_on").end().end().addClass("rep_link_on").attr("data-mode", function (i, x) {
                        $j("#rpdata_" + x).show();
                        if (x === 'preview') {
                            reporter.saveReport(this, true);
                        }
                    });
                });
                $j(".rpparts:eq(0)").show();

                //get recent list of picked items
                refreshItemsList();

            }
            else {
                inOrder();
            }
        },
        pickerOn:function (e) {
            curRow = findIndex(this)[0];
            $j("#tthome").css("cursor", "crosshair");
            tabPrepare(3);

            $j(window).bind("keypress", function (e) {
                var code = (e.keyCode ? e.keyCode : e.which);
                if (code == 27) { // user pressed Esc button
                    pickerOff(false);
                }
            });
        },
        reget:function () {
            if (activated === false) {
                reporter.init();
            }
            else {
                inOrder();
            }
        },
        addRow:function (obj) {
            rowActions("add", obj);
        },
        delRow:function (obj) {
            rowActions("del", obj);
        },
        saveReport:function (but, tryCase) {
            if (buildingSample === true) {
                return false;
            } else {
                buildingSample = true;
            }
            endAllEdits();
            //$j(but).attr("disabled", true);
            var $rep_name = $j("#datareport").find("input:eq(0)");
            if ($rep_name.val() == '') {
                alert("Please enter name of report");
                $rep_name.focus();
                //$j(but).attr("disabled", false);
                buildingSample = false;
                return false;
            }
            $j("#rep_ps").show("fast");
            //cleanBData();
            var psdbox = cleanUnUsedItems();
            var order = [], stypez = [], columnItems = [];
            $j(".breport").each(function (ci) {
                //$j(".slink",$brbody).each(function(){
                columnItems[ci] = [];
                $j(".slink", $j(this)).each(function () {
                    var sid = $j(this).attr("id").replace("sec_", "");
                    order.push(sid);
                    stypez.push($j(this).attr("data-stype"));
                    columnItems[ci].push(sid);
                });
            });

            var entries = $j("#datareport").formParams(), strpre = [], //form2object("datareport"),
                startD = $j("#rep_start").val(), endD = $j("#rep_end").val(), res = false, indb = $j("#reportHouse").data("fromdb"), //indb = $breport.data("fromdb"),
                postAction,
                dfp = {
                    entries:entries,
                    start:startD,
                    end:endD,
                    bdata:psdbox,
                    rows:rows,
                    order:order,
                    types:stypez,
                    columns:columnItems,
                    second:1//$j("#scol_view:checked").length
                };
            if (isNaN(indb) || indb === null || tryCase === true) {
                postAction = 'save';
            }
            else {
                postAction = 'update';
                //dfp.push("&indb=" + indb);
                strpre.push("indb=" + indb + "&");
            }
            strpre.unshift("mode=" + postAction + "&");				//sdbox.push(dBox);
            // alert(postAction);
            $j.ajax({
                url:'?m=outputs&a=reports&suppressHeaders=1',
                data:strpre.join("") + "bps=" + encodeURIComponent(JSON.stringify(dfp)), //dfp.join(""),
                type:'post',
                success:function (msg) {
                    if (msg && msg.length > 0) {
                        if (msg != 'fail' && !isNaN(parseInt(msg))) {
                            if (tryCase === false) {
                                buildingSample = false;
                                $rlbody.find("tr.emptydb").remove().end();
                                msg = (postAction == 'update' ? indb : msg);
                                console.log(entries);
                                chface.add2Table({
                                    id:(msg ? parseInt(msg) : 0),
                                    name:entries.rep_name,
                                    desc:'',
                                    type:'Report',
                                    sdate:startD,
                                    edate:endD,
                                    brest:true,
                                    eaction:postAction
                                });
                                res = true;
                                inBays = [];
                                cleanTabStat();
                                //popMsg("Report template saved", "ok");
                                info("Report template saved", 1);
                                $j(but).attr("disabled", false);
                                $rep_name.val("");
                                $j("#rep_dept").val("1");
                                qurer.reporter(startD, endD, msg, false);
                            } else {
                                if (parseInt(msg) > 0) {
                                    $j("#rpdata_preview")
                                        .html("<img src='/modules/outputs/images/report-load.gif'>")
                                        .load(['/?m=outputs&a=reports&mode=wfrm&itid=' , msg , "&ds=", startD, "&de=", endD, "&kadze=kami&suppressHeaders=1" ].join(""), function () {
                                            buildingSample = false;
                                        });
                                }
                            }
                        } else {
                            buildingSample = false;
                            alert("Report is not saved");
                        }

                    }
                    $j("#rep_ps").hide();
                }
            });


        },
        delr:function (obj) {
            delReport(obj);
        },
        newSection:function () {
            buildSection2();
        },
        freshRow:function (x, y) {
            SectionAddRow(x, y);
        },
        delSection:function (x, tforce) {
            killSection(x, tforce);
        },
        editSection:function (i, x) {
            proceedEdit(i, x);
        },
        getItemsList:function () {
            return sdbox;
        },
        newSectionPre:function (obj, rightAfter, justNew) {
            $cbrbody = getColumn(obj, rightAfter);
            if (rightAfter && rightAfter === true) {
                startAddPoint = $j(obj).closest("tr").attr("class").replace("zxrow", "").replace("sub", "");
                ++sectionsCount;
            } else {
                startAddPoint = 'origin';
            }
            buildSection2(justNew);
        },
        colFrm:function (x, y) {
            columnWork(x, y);
        },
        initGraph:function () {
            $j("#graph_tab_holder").live("dblclick", function(e){
                var element = $('#graph_home');
               /* html2canvas(element, {
                    onrendered: function (canvas) {*/
                        statTableHtmlData = 'img';
                        //chart.exportChart({type: 'image/png', filename: 'my-png'});
                        //imgBase64 = chart.getSVG();//.getImageURI();
                        canvas = document.createElement('canvas'); // Create an empty canvas
                         canvg(canvas, chart.getSVG()); // Render the SVG on the canvas
                        imgBase64 = canvas.toDataURL('image/png');
                        //window.location.href = imgBase64;
                        itemClick = 'graph';
                        pickerAct(e);
                    /*}
                });*/
            });
        },
        initMaps:function () {
            $j("#savemaps").live("click", function(e){
                //var element = $('#map');
                leafletImage(map,returnMapPicture);
            });
        }

    }
}(reporter));
function popupDescStats(){
    $j('#tabdesc').val(statsdesc);
    $dbtabdesc = $j("#dbtabdesc");
    $dbtabdesc.dialog({
        modal: true,
        resize: "auto",
        resizable: false,
        autoOpen: true,
        buttons: {
            "Save": function(){
                statsdesc = $j('#tabdesc').val();
                $dbtabdesc.dialog( "close" );
            },
            "Cancel": function() {
                $dbtabdesc.dialog( "close" );
            }
        },
        close: function() {
            $dbtabdesc.dialog( "close" );
        }
    }).prev(".ui-dialog-titlebar").css("background","#aed0ea").css("border","1px solid #aed0ea");
}
reporter.initMaps();

