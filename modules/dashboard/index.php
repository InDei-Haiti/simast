<?php
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

$hashList = $q->loadList();
//var_dump($hashList);
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
                <select id="reportSelector" class="selectpicker">
                    <?php

                    if(count($projects) > 0) {
                        $i = 0;
                        foreach ($projects as $pid => $project_name) {
                            $i++;
                            echo '<option>Projet numero | '.$i.'</option>';
                        }
                    }
                    ?>
                </select>
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

                    if(count($sets) > 0) {
                        $i = 0;
                        foreach ($sets as $sid => $set_name) {
                            $i++;
                            $list = array();
                            foreach ($hashList as $index => $grapher){
                                if($grapher['set_id']==$sid){
                                    $list[] = $grapher;
                                }
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

                                    $graph_data = gzdecode($grapher['data_item']);
//                                    var_dump($graph_data);

                                    //echo '<br/>---------';
                                    $graph_data = json_decode(str_replace("\\","", str_replace("'","", $graph_data)),true);
                                    //var_dump($grapher);
                                   // echo $graph_data['type'].'<br/>';
                                    echo '<div id="chart-'.$i.'-'.$index.'-1" class="card resizable" style="width: 600px;height:400px;resize: both;"><div id="chart-'.$i.'-'.$index.'-1"></div></div>';
                                    ?>
                                    <script type="text/javascript">
                                        var graph_type = '<?php echo $graph_data['type']?>';
                                        if(graph_type=='bars'){
                                            bars('chart-<?php echo $i.'-'.$index?>-1',<?php echo json_encode($graph_data["categories"]); ?>,<?php echo json_encode($graph_data["series"]); ?>,'<?php echo $graph_data["title"]; ?>');
                                        }
                                        if(graph_type=='sbars'){
                                            sbars('chart-<?php echo $i.'-'.$index?>-1',<?php echo json_encode($graph_data["categories"]); ?>,<?php echo json_encode($graph_data["series"]); ?>,'<?php echo $graph_data["title"]; ?>');
                                        }
                                        if(graph_type=='pbars'){
                                            pbars('chart-<?php echo $i.'-'.$index?>-1',<?php echo json_encode($graph_data["categories"]); ?>,<?php echo json_encode($graph_data["series"]); ?>,'<?php echo $graph_data["title"]; ?>');
                                        }
                                        if(graph_type=='pie'){
                                            pie('chart-<?php echo $i.'-'.$index?>-1',<?php echo json_encode($graph_data["series"]); ?>,'<?php echo $graph_data["title"]; ?>');
                                        }
                                        if(graph_type=='lines'){
                                            lines('chart-<?php echo $i.'-'.$index?>-1',<?php echo json_encode($graph_data["categories"]); ?>,<?php echo json_encode($graph_data["series"]); ?>,'<?php echo $graph_data["title"]; ?>');
                                        }

                                    </script>
                                    <?php
                                }
                                if($grapher['type']=='TABLE'){
                                    $graph_data = gzdecode($grapher['data_item']);
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
