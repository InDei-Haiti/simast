<?php

//Migration donnees Dashboard Grapher vers report Items
//$q = new DBQuery();
//$q->addQuery("id, set_id, project_id, type, query_save, data_item,description");
//$q->addTable('dashboard_grapher');
//$result = $q->loadList();
///*echo '<pre>';
//var_dump($result);
//echo '</pre>';*/
//$counter = 0;
//foreach ($result as $row){
//    $title = '';
//    $itype = '';
//    $idata = '';
//    $html = '';
//    $data_item = '';
//    if($row['type']=='GRAPH') {
//        $graph_data = gzdecode($row['data_item']);
//        $graph_data = json_decode(str_replace("\\", "", str_replace("'", "", $graph_data)), true);
//        $data_item = $row['data_item'];
//        $title = $graph_data["title"];
//        $itype = strtolower($row['type']);
//        $idata = array(
//            'v' => ucfirst(strtolower($row['type'])),
//            'n' => $graph_data["title"],
//            'g' => $graph_data
//        );
//
////        echo '<pre>';
////        var_dump($idata);
////        echo '</pre>';
//    }else if($row['type']=='TABLE'){
//        $counter++;
//        $title = "Table #".$counter;
//        $itype = 'stat';
//        $graph_data = gzdecode($row['data_item']);
//        $html .='<span style="background-color: inherit;" id="tthome">';
//        $tmp_html = mysql_real_escape_string($graph_data);
////        if(substr($tmp_html,0,1) == "'" AND substr($tmp_html,strlen($tmp_html)-1) == "'"){
//        $tmp_html = substr($tmp_html,2,strlen($tmp_html)-3);
//
////        }
//        $html .= $tmp_html;
//        $html .= '</span>';
////        echo "<br /><br /><br /><br /><br /><br />".$html;exit;
////        echo "<pre>";
////        var_dump($html);
////        echo "</pre>";
//        $idata = array(
//            'v' => ucfirst(strtolower("Idata #".$counter)),
//            'n' => "Table #".$counter,
//            'g' => "",
//            'c'=> 'stat'
//        );
//        $data_item = mysql_real_escape_string(gzencode(var_export($html,true), 9, FORCE_GZIP));
//    }
//
//    $sql = "INSERT INTO report_items (title,itype,idata,html,data_item,project_id,actif,query_save)
//              VALUES('".$title."','".$itype."','".mysql_real_escape_string(json_encode($idata))."','".$html."','".$data_item."',".$row['project_id'].",'1','')
//             ";
//    //echo $sql.'<br/><br/>';
//    $res = mysql_query($sql);
////    echo '<br/><br/><br/><br/><br/>'.$sql;
////    echo mysql_errno().':'.mysql_error();
////    exit;
//    if ($res) {
//        $new_id = mysql_insert_id();
//        $sql_link = "INSERT INTO simast.set_report_items(setId,itemId)VALUES('".$row["set_id"]."','".$new_id."')";
//        $res = mysql_query($sql_link);
//    }
//
//
//}
//
//exit;
//Migration donnees Dashboard Grapher vers report Items

GLOBAL $AppUI;
$q = new DBQuery();
$q->addTable('projects');
$q->addQuery('project_id,project_name');
$projects = $q->loadHashList();

$q = new DBQuery();
$q->addTable('sets');
$q->addQuery('id,setname');
$sets = $q->loadHashList();


$q = new DBQuery();
$q->addQuery("id, set_id, project_id, type, query_save, data_item,description");
$q->addTable('dashboard_grapher');

//$hashList = $q->loadList();
//var_dump($hashList);exit;
//echo "<br /><br /><br /><br /><br /> Cogito Ergo Sum";
//echo "<pre>";
//var_dump($hashList);
//echo "</pre>";
$slctQUery = "SELECT C.id,B.setId AS set_id, C.project_id, C.idata,C.html,CASE C.itype WHEN 'graph' THEN 'GRAPH' WHEN 'stat' THEN 'TABLE' END AS type,C.query_save, C.data_item, 'none' AS description FROM sets AS A INNER JOIN set_report_items AS B ON A.id = B.setId INNER JOIN report_items AS C ON B.itemId = C.id";

//echo "<br /><br /><br /><br /><br />Go";
$res = mysql_query($slctQUery);
$hashList = [];
if($res){
    while($row = mysql_fetch_assoc($res)){
        $hashList [] = $row;
    }
//    echo "<pre>";
//    var_dump($hashList);
//    echo "</pre>";
}
//exit;
?>
<!--<link rel="stylesheet" type="text/css" href="/modules/outputs/outputs.module.css" />-->

<!--Copying highCharts library in codes-->
<!--<script src="https://code.highcharts.com/highcharts.js"></script>-->
<style>
    .title[title]{
        position:relative;
    }
    .title[title]:after{
        content:attr(title);
        color:#fff;
        background:#333;
        background:rgba(51,51,51,0.75);
        padding:5px;
        position:absolute;
        left:-9999px;
        opacity:0;
        bottom:100%;
        white-space:nowrap;
        -webkit-transition:0.25s linear opacity;
    }
    .title[title]:hover:after{
        left:5px;
        opacity:1;
    }
</style>
<script src="<?php echo DP_BASE_URL?>/style/default/highcharts.js"></script>
<script src="<?php echo DP_BASE_URL?>/style/default/exporting.js"></script>
<!--<script src="https://code.highcharts.com/modules/exporting.js"></script>-->
<script type="text/javascript">
    var bars = function(chartid, categoriesv,seriesv,title){
        return Highcharts.chart(chartid, {
            chart: {
                type: 'bar'
            },
            title: {
                text: title
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories: categoriesv,
                title: {
                    text: null
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Population',
                    align: 'high'
                },
                labels: {
                    overflow: 'justify'
                }
            },
            plotOptions: {
                bar: {
                    dataLabels: {
                        enabled: true
                    }
                }
            },
            legend: {
                /*layout: 'vertical',
                 align: 'right',
                 verticalAlign: 'top',
                 x: -40,
                 y: 80,
                 floating: true,
                 borderWidth: 1,
                 backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
                 shadow: true*/
                /*reversed: true*/
            },
            credits: {
                enabled: false
            },
            series: seriesv
        });
    };

    var sbars = function(chartid, categoriesv,seriesv,title){
        return Highcharts.chart(chartid, {
            chart: {
                type: 'bar'
            },
            title: {
                text: title
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories: categoriesv,
                title: {
                    text: null
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Population (millions)',
                    align: 'high'
                },
                labels: {
                    overflow: 'justify'
                }
            },
            tooltip: {
                valueSuffix: ' millions'
            },
            plotOptions: {
                series: {
                    stacking: 'normal'
                }
            },
            legend: {
                reversed: true
            },
            credits: {
                enabled: false
            },
            series: seriesv
        });
    };

    var pbars = function(chartid, categoriesv,seriesv,title){
        return Highcharts.chart(chartid, {
            chart: {
                type: 'bar'
            },
            title: {
                text: title
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories: categoriesv,
                title: {
                    text: null
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Population (millions)',
                    align: 'high'
                },
                labels: {
                    overflow: 'justify'
                }
            },
            tooltip: {
                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
                shared: true
            },
            plotOptions: {
                series: {
                    stacking: 'percent'
                }
            },
            legend: {
                reversed: true
            },
            credits: {
                enabled: false
            },
            series: seriesv
        });
    };

    var lines = function(chartid, categoriesv,seriesv,title){
        return Highcharts.chart(chartid, {
            title: {
                text: title
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories: categoriesv,
                title: {
                    text: null
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Population (millions)',
                    align: 'high'
                },
                labels: {
                    overflow: 'justify'
                },
                plotLines: [{
                    value: 0,
                    width: 1
                }]
            },
            plotoptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    },
                    enableMousetracking: false
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            credits: {
                enabled: false
            },
            series: seriesv
        });
    };

    var line = function(chartid, categoriesv,seriesv,title){
        return Highcharts.chart(chartid, {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: title
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                }
            },
            series: seriesv
        });
    };

    var pie = function(chartid, seriesv,title){
        return Highcharts.chart(chartid, {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: title
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                }
            },
            series: seriesv
        });
    };

    var columns = function(chartid,categoriesv,seriesv,title,subTitle){
        /* Highcharts.setOptions({
         colors: colors
         });*/

        return Highcharts.chart(chartid,{
            chart: {
                type: 'column',
//                height: 500
            },
            title: {
                text: title
            },
            subtitle: {
                text: subTitle
            },
            xAxis: {
                categories: categoriesv,
                title: {
                    text: null
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: '',
                    align: 'high'
                },
                labels: {
                    overflow: 'justify'
                }
            },
            legend: {
                /*layout: 'vertical',
                 align: 'right',
                 verticalAlign: 'top',
                 */
                /*x: 0,
                 y: 30,*/
                floating: false,
                /*borderWidth: 1,*/
                backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
                shadow: true
                /*reversed: true*/
            },
            credits: {
                enabled: false
            },
            series: seriesv
        });
    };
</script>
<?php /*$moduleScripts[]="./modules/outputs/outputs.module.js"; */ /*margin-right: -300px*/ ?>
<br/>
<div class="card">
    <!--<div class="block-header">
        <h2 style="border-bottom: 1px solid #d0d0d0;padding-bottom: 10px">Dashboard</h2>
    </div>-->
    <div class="body">
        <div class="row">
            <div class="col-md-3">
                <select class="form-control" style="width: 200px;height:30px">
                    <?php

                    if(count($projects) > 0) {
                        $i = 0;
                        foreach ($projects as $pid => $project_name) {
                            $i++;
                            echo '<option value="'.$pid.'">'.$project_name.'</option>';
                        }
                    }
                    ?>
                </select>
            </div>

<!--            <div class="col-md-3 col-md-offset-6">-->
<!--                <select id="reportSelector" class="selectpicker">-->
<!--                    --><?php
//
//                    if(count($projects) > 0) {
//                        $i = 0;
//                        foreach ($projects as $pid => $project_name) {
//                            $i++;
//                            echo '<option>Projet numero | '.$i.'</option>';
//                        }
//                    }
//                    ?>
<!--                </select>-->
<!--                <div class="col-lg-12">-->
<!--                    <div class="button-group">-->
<!--                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-cog"></span> <span class="caret"></span></button>-->
<!--                        <ul class="dropdown-menu">-->
<!--                            <li><a href="https://silviomoreto.github.io/bootstrap-select/examples/" target="_blank" class="small" data-value="option1" tabIndex="-1"><input type="checkbox"/>&nbsp;&nbsp;&nbsp;&nbsp;Rapport mois de janvier</a></li>-->
<!---->
<!--                        </ul>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
        </div>
        <div class="row">
            <div class="col-md-12">
                <DIV id="tabs" class="bigtab" style="background: transparent !important;">
                    <!--<div style="float: right;">
                        <select class="form-control" style="width: 200px">
                            <?php
/*
                            if(count($projects) > 0) {
                                $i = 0;
                                foreach ($projects as $pid => $project_name) {
                                    $i++;
                                    echo '<option value="'.$pid.'">'.$project_name.'</option>';
                                }
                            }
                            */?>
                        </select>
                    </div>-->
                    <ul class="topnav tabs-nav" style="width: 100% !important;background: transparent !important;border-color: transparent;">

                        <?php

                        if(count($sets) > 0) {
                            $i = 0;
                            foreach ($sets as $sid => $set_name) {
                                $i++;
                                echo '<LI><A href="#tabs-'.$i.'"><b>'.trim($set_name).'</b></A></LI>';
                            }
                        }
                        ?>
                    </ul>


                    <?php

//                    echo "<br /><br /><br /><br /><pre>";
//                    var_dump($hashList);
//                    echo "</pre>";
                    if(count($sets) > 0) {
                        $i = 0;
                        foreach ($sets as $sid => $set_name) {
                            $i++;
                            $list = array();
                                    echo "<script>console.log('".count($hashList)."')</script>";
                            foreach ($hashList as $index => $grapher){
//                                if($grapher){
////                                    echo "<script>console.log('".$index."')</script>";
//
//                                }
                                if($grapher['set_id']==$sid){
                                    $list[] = $grapher;
                                }
//                                  echo "<script>console.log('".$grapher['set_id']."  <->  ".$sid."')</script>";
                            }
                            echo '<div id="tabs-' . $i . '" class="mtab" style="margin-left: 0px">';
                            echo '<div class="row">';
                            foreach ($list as $index=>$grapher) {
                                echo '<div class="col-md-6'.$offset.'">';
                                // echo "<span class='getInfo'><i class=\"fa fa-info-circle\" aria-hidden=\"true\" style=\"color: blue;\"></i></span>";
//                                echo "<div class='descrip' style='display:none;'>".$grapher['description']."</div>";
                                if($grapher['description'] == ''){
                                    echo "<div class='descrip' style='display:none;'>Description indisponible</div>";
                                }else{
                                    echo "<div class='descrip' style='display:none;'>".$grapher['description']."</div>";
                                }
                                if($grapher['type']=='GRAPH'){
//                                    echo "<br /><br /><br /><br /><br />Je suis<br />";

//                                    $graph_data = gzdecode($grapher['data_item']);
//                                    var_dump($graph_data);
//                                    echo "<pre>";
//                                    var_dump($grapher['data_item']);
//                                    var_dump($graph_data);
//                                    echo "</pre>";

                                    $newGraphhandler = json_decode($grapher["idata"],true);
//                                    echo "<pre>";
//                                    var_dump($newGraphhandler["g"]["categories"]);
//                                    echo "</pre>";
//                                    echo "<pre>";
//                                    var_dump($grapher["idata"]);
////                                    var_dump($grapher['data_item']);
//                                    var_dump($newGraphhandler);
//                                    echo "</pre>";
                                    //echo '<br/>---------';
//                                    $graph_data = json_decode(str_replace("\\","", str_replace("'","", $graph_data)),true);
                                    //var_dump($grapher);
                                   // echo $graph_data['type'].'<br/>';
                                    echo '<div id="chart-'.$i.'-'.$index.'-1" class="card resizable" style="width: 600px;height:400px;resize: both;"><div id="chart-'.$i.'-'.$index.'-1"></div></div>';
                                    ?>
                                    <script type="text/javascript">
                                        var graph_type = '<?php  echo $newGraphhandler["g"]['type'];?>';
                                        if(graph_type=='bars'){
                                            bars('chart-<?php echo $i.'-'.$index?>-1',<?php echo json_encode($newGraphhandler["g"]["categories"]); ?>,<?php echo json_encode($newGraphhandler["g"]["series"]); ?>,'<?php echo $newGraphhandler["g"]["title"]; ?>');
                                        }
                                        if(graph_type=='sbars'){
                                            sbars('chart-<?php echo $i.'-'.$index?>-1',<?php echo json_encode($newGraphhandler["g"]["categories"]); ?>,<?php echo json_encode($newGraphhandler["g"]["series"]); ?>,'<?php echo $newGraphhandler["g"]["title"]; ?>');
                                        }
                                        if(graph_type=='pbars'){
                                            pbars('chart-<?php echo $i.'-'.$index?>-1',<?php echo json_encode($newGraphhandler["g"]["categories"]); ?>,<?php echo json_encode($newGraphhandler["g"]["series"]); ?>,'<?php echo $newGraphhandler["g"]["title"]; ?>');
                                        }
                                        if(graph_type=='pie'){
                                            pie('chart-<?php echo $i.'-'.$index?>-1',<?php echo json_encode($newGraphhandler["g"]["series"]); ?>,'<?php echo $newGraphhandler["g"]["title"]; ?>');
                                        }
                                        if(graph_type=='lines'){
                                            lines('chart-<?php echo $i.'-'.$index?>-1',<?php echo json_encode($newGraphhandler["g"]["categories"]); ?>,<?php echo json_encode($newGraphhandler["g"]["series"]); ?>,'<?php echo $newGraphhandler["g"]["title"]; ?>');
                                        }
                                        if(graph_type=='columns'){
//                                            console.log('<?php //echo json_encode($newGraphhandler["g"]["categories"]); ?>//');
//                                            console.log('<?php //echo json_encode($newGraphhandler["g"]["series"]); ?>//');
//                                            console.log('<?php //echo $newGraphhandler["g"]["title"]; ?>//');
                                            columns('chart-<?php echo $i.'-'.$index?>-1',<?php echo json_encode($newGraphhandler["g"]["categories"]); ?>,<?php echo json_encode($newGraphhandler["g"]["series"]); ?>,'<?php echo $newGraphhandler["n"];/*($newGraphhandler["g"]["title"] == '') ? $newGraphhandler["g"]["title"] : $newGraphhandler["n"] ;*/?>','subTitle');
                                        }

                                    </script>
                                    <?php
                                }
                                if($grapher['type']=='TABLE'){
                                    $graph_data = gzdecode($grapher['data_item']);
                                    $graph_data = str_replace("\\n","",$graph_data);
                                    $graph_data = str_replace('\\',"",$graph_data);
                                    echo '<div id="table-'.$i.'-'.$index.'-1" class="card resizable title" style="width: 600px;height:400px;resize: both;overflow-y: scroll;" title="'.$grapher['describe'].'">';echo $grapher['describe'];
                                    eval("echo $graph_data;");
                                    echo '</div>';

                                }
                                echo '</div>';
                                /*if ($index%2 == 1)
                                    echo '</div><div class="row">';
                                else
                                    $offset = '';*/
                            }
                            echo '</div>';
                            echo '</div>';
                            ?>


                            <?php
                        }
                    }
                    ?>
                </DIV>
            </div>
        </div>
        &nbsp;&nbsp;&nbsp;&nbsp;
    </div>
    &nbsp;
</div>

<!--Added by RPA-->
<script src="<?php echo DP_BASE_URL?>/style/default/jquery-1.10.2.min.js"></script>
<!-- <script src="<?php echo DP_BASE_URL?>/style/default/bootstrap-select/js/bootstrap-select.min.js"></script> -->
<!-- <script type="text/javascript">
    $('.selectpicker').selectpicker();

</script> -->
<script>
//    var =
    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires="+d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function getCookie(cname) {
        var name = cname + "=";

        var ca = document.cookie.split(';');
//      return ca
//        return ca.length
        var totalc ="";
        for(var i = 0; i < ca.length; i++) {
            var c = ca[i];
//            while (c.charAt(0) == ' ') {
//                c = c.substring(1);
//            }
            if(c.search(name) !=-1){
                return name;
            }
//            totalc +=c
//            return c.indexOf(name)
//            if (c.indexOf(name) == 0) {
//                return c.substring(name.length, c.length);
//            }
        }
        return "merde"
//        return totalc;
    }

    function checkCookie() {
        var user = getCookie("username");
        if (user != "") {
            alert("Welcome again " + user);
        } else {
            user = prompt("Please enter your name:", "");
            if (user != "" && user != null) {
                setCookie("username", user, 365);
            }
        }
    }
    $(document).ready(function(){
//        Loading css informations in cookies
        $("div.card.resizable").each(function(){
//            setCookie($(this).attr("id")+"_width",$(this).css("width"),200)
            setTimeout(setCookie($(this).attr("id")+"_width",$(this).css("width"),200),500);
//            setTimeout(alert($(this).css("width")),500);
        });
        $("div.card.resizable").click(function(){
            $("div.card.resizable").each(function(){
//                alert($(this).attr("id"));
//                setTimeout(alert(getCookie($(this).attr("id"))),500);
            });
        });
        $("div.card.resizable").click(function(){
//            alert("Je suis Alexis");
//            alert($("div.card.resizable").css("height").height);

//            setTimeout(alert($(this).width()),1000);
        });
//        $("div.card.resizable").click(function(){
//            alert("On a modifie mon size");
//        })
    });

</script>
<!--Added by RPA-->
