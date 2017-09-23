<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html"
      xml:lang="en" lang="en">
<head>
    <title>Leatoto Centers' Report Viewer</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <link rel="stylesheet" type="text/css" href="/front/creports.css">
    <link rel="stylesheet" type="text/css" href="/modules/outputs/jquery-ui.css"/>
    <link rel="stylesheet" type="text/css" href="/front/ui.dropdownchecklist.themeroller.css"/>
    <link rel="stylesheet" type="text/css" href="/front/style.css"/>
</head>
<body>

<div id="dialog" title="Select reports for view">
    <p>
    <table class="ptbl" cellpadding="0" cellspacing="1">
        <thead>
        <tr>
            <th>&nbsp;</th>
            <th>Center</th>
            <th>Start date</th>
            <th>End date</th>
            <th>Report Query</th>
        </tr>
        </thead>
        <tbody class="body_flow"></tbody>
    </table>
    </p>
</div>

<div id="all-wrap">
    <div class="pdf_wrap">
        <div class="pdf_but icns pdf_on" title="Save as PDF"></div>
        <div class="pdf_loading"></div>
    </div>
    <div id="loading"><img src="images/loading.gif"></div>
    <div id="rempty">Result is empty</div>
    <div class="top_sels center-place">@@centers_selector@@</div>
    <div class="top_sels dept-place">@@dept_selector@@</div>

    <div id="rqvals">
        <select id="rep_selector" class="text " multiple="multiple" size="1">
            @@all_reports@@
        </select>
        <button class="icns rborder ico_but" id="rep_but">Reports</button>
        <div class="period_sel" data-type="start">
            <span class="repdt_lims" data-type="mon">@@month@@</span>
            <span class="repdt_lims" data-type="year">@@year@@</span>
        </div>
        <!-- <button class="icns rborder ico_but" id="show_but">Search</button> -->
        <!-- <button class="icns rborder ico_but" id="clear_but">Clear</button> -->
        <button class="icns rborder ico_but" id="view_but">Go</button>
    </div>

    <div id="tabs">
        <div id="left" style="display:none;"></div>
        <div id="right" style="display:none;"></div>
        <div id="brdata"></div>
        <ul id="tabUl"></ul>
    </div>
</div>


<div id="fail">
    <p>Server request failed.<br>Please try again later.</p>
</div>



<script type="text/html" id="item_tmpl">
    <tr>
        <td><input type="checkbox" value="<%=id%>"></td>
        <td><%=center%></td>
        <td><%=sdate%></td>
        <td><%=edate%></td>
        <td><%=tpl%></td>
    </tr>
</script>

<form action="/front/index.php" method="post" style="display: none;" id="pdf_form"
      onsubmit="return AIM.submit(this, {onStart : fake , onComplete : rbr.pdfFin})">
    <input type="hidden" name="mode" value="printpdf">
    <input type="hidden" name="pdata" id="pdata_bnk">
    <input type="submit" id="startPDF">
</form>


<script type="text/javascript" src="/js/1jquery.best.js.re"></script>
<script type="text/javascript" src="/js/base.js"></script>
<script type="text/javascript" src="/front/creports.js"></script>
<script type="text/javascript" src="/front/js/jquery-ui-1.8.9.custom.min.js"></script>
<script type="text/javascript" src="/front/ui.dropdownchecklist-1.3-min.js"></script>
<script type="text/javascript" src="/front/jquery.scrolltab.js"></script>
<script type="text/javascript">
    window.onload = up;

    function up() {
        bstrap();
    }
</script>
</body>
</html>