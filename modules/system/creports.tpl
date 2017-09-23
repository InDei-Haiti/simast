<link rel="stylesheet" type="text/css" href="/modules/system/creports.css">
<span class="fhref" onclick="$j('#im_block').toggle()">
    Import new report
</span>
<div id="im_block">
    <form action="/?m=system&a=creports&mode=inreport&suppressHeaders=1" method="post" enctype="multipart/form-data"
          onsubmit="return AIM.submit(this, {onStart: bulkspace, onComplete: uploadFinished})" id="frn">
        <table class="swrap">
            <!-- <tr>
                <td>Report template</td>
                <td data-cont="Report">@@report@@</td>
            </tr> -->

            <tr>
                <td>Report file</td>
                <td>
                    <input type="file" name="cname"  style="display: none;" id="fup_report">
                    <button class="text" onclick="atuFile(this);return false;">Select File</button>
                    <span class="nf_name"></span>
                </td>
            </tr>
        </table>
        <input type="button" value="Upload" onclick="asyncUp(this)" class="text" >
        <input type="submit" id="hlaunch" style="display: none;">
    </form>
</div>

<div id="preview">
    <span class="swblock">Uploaded report</span><br>
    <div class="vbox"></div>
</div>

<div>
    @@rtable@@
</div>
<div id="chg_view"></div>

<script type="text/javascript" src="/modules/system/creports.js"></script>
<script type="text/javascript">
    window.onload = up;

    function up (){
        bstrap();
    }
</script>