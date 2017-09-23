<?php 
GLOBAL $AppUI;
$q = new DBQuery();
$q->addTable('projects');
$q->addQuery('project_id,project_name');
$projects = $q->loadHashList();
$q = new DBQuery();
$q->addQuery("id, name, project_id, type, query_save, stat, graph_data");
$q->addTable('dashboard_grapher');

$hashList = $q->loadList();
//var_dump($hashList);
?>
<!--<link rel="stylesheet" type="text/css" href="/modules/outputs/outputs.module.css" />-->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
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
<?php /*$moduleScripts[]="./modules/outputs/outputs.module.js"; */?>
<h2 style="font-size: 20px;
  padding: 2px 10px 1px 0px;
  margin: 0 0 10px 0;
  border-bottom: 1px solid #bbbbbb;">Dashboard</h2>

<DIV id="tabs" class="bigtab" style="background: transparent !important;">
    <br/>
    <UL class="topnav tabs-nav" style="background: transparent !important;border-color: transparent">

        <?php

        if(count($projects) > 0) {
            $i = 0;
            foreach ($projects as $pid => $project_name) {
                $i++;
                echo '<LI><A href="#tabs-'.$i.'">'.$project_name.'</A></LI>';
            }
        }
        ?>
    </ul>
    <?php

    if(count($projects) > 0) {
        $i = 0;
        foreach ($projects as $pid => $project_name) {
            $i++;
            $list = array();
            foreach ($hashList as $index => $grapher){
                if($grapher['project_id']==$pid){
                    $list[] = $grapher;
                }
            }
            //var_dump($list);
            echo '<div id="tabs-' . $i . '" class="mtab" style="margin-left: 4px">';

            echo '<div class="row">';
            echo '<div class="col-lg-3">
                    <ul class="nav nav-bordered nav-stacked flex-md-column" style="display: block;padding:10px;background: #f6f6f6;color: #1997c6;">
                      <li class="nav-header" style="color: #a0a0a0;font-weight: 500;margin-bottom: 20px;">Stats</li>
                      ';
            /*for($z=0;$z<4;$z++){
                echo '<li class="nav-item" style="padding: 5px;">
                        <a class="nav-link" href="#tab-'.$z.'" style="color: #1997c6;">View page '.$z.'</a>
                      </li>';
            }*/
            $z = 0;
            foreach($list as $index => $grapher){
                $z++;
                echo '<li class="nav-item" style="padding: 5px;">
                        <a class="nav-link" href="#tab-'.$i."_".$z.'" style="color: #1997c6;">'.$grapher["name"].'</a>
                      </li>';
            }


            echo '</ul>
                  </div>';

            echo '<div class="col-lg-9">';
            /*for($z=0;$z<4;$z++) {*/
            $z = 0;
            foreach ($hashList as $index => $grapher){
                $z++;
                $graph_data = gzdecode($grapher['graph_data']);

                /*var_dump($graph_data);
                echo '<pre>';
                var_dump(json_decode(str_replace("'","", $graph_data),true));
                echo '</pre>';*/
                $graph_data = json_decode(str_replace("'","", $graph_data),true);
                echo '<div style = "background: #f6f6f6;min-height: 100px;display: none;" id="tab-'.$i."_".$z.'" class="tabContent">
                        <DIV class="sstabs" class="bigtab" style = "background: transparent !important;border:none" >
                            <UL class="topnav tabs-nav" style = "background: transparent !important;border-color: transparent" >
                                <li ><A href = "#stabs-'.$i.'-'.$z.'-1" > Graphic</A ></li >
                                <li ><A href = "#stabs-'.$i.'-'.$z.'-2" > Table</A ></li >
                            </UL >
                            <div id = "stabs-'.$i.'-'.$z.'-1" class="mtab" style="" >';
                                ?>
                                <div id="chart-<?php echo $i.'-'.$z?>-1" style="width:800px; margin: 0 auto"></div>
                                <script type="text/javascript">
                                    var graph_type = '<?php echo $graph_data['type']?>';
                                    if(graph_type=='bars'){
                                        bars('chart-<?php echo $i.'-'.$z?>-1',<?php echo json_encode($graph_data["categories"]); ?>,<?php echo json_encode($graph_data["series"]); ?>,'<?php echo $graph_data["title"]; ?>')
                                    }
                                    if(graph_type=='sbars'){
                                        sbars('chart-<?php echo $i.'-'.$z?>-1',<?php echo json_encode($graph_data["categories"]); ?>,<?php echo json_encode($graph_data["series"]); ?>,'<?php echo $graph_data["title"]; ?>')
                                    }
                                    if(graph_type=='pbars'){
                                        pbars('chart-<?php echo $i.'-'.$z?>-1',<?php echo json_encode($graph_data["categories"]); ?>,<?php echo json_encode($graph_data["series"]); ?>,'<?php echo $graph_data["title"]; ?>')
                                    }
                                    if(graph_type=='pie'){
                                        pie('chart-<?php echo $i.'-'.$z?>-1',<?php echo json_encode($graph_data["series"]); ?>,'<?php echo $graph_data["title"]; ?>');
                                    }
                                </script>
                        <?php
                        echo '</div>';
                echo '<div id = "stabs-'.$i.'-'.$z.'-2" class="mtab" style = "margin-left: 4px" >';
                    echo gzdecode($grapher['stat']);
                echo '</div >
                        </DIV >
                    </div>';
            }


            echo '</div></div></div>';
            ?>


    <?php
        }
    }
    ?>
</DIV>
