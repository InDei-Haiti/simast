<?php
/**
 * Created by PhpStorm.
 * User: rulxphilome.alexis
 * Date: 11/8/2017
 * Time: 12:05 PM
 */
require_once 'base.php';
?>


<!DOCTYPE html>
<html>
    <head>
            <title>New - DASHBOARD</title>
            <link rel="stylesheet" href="<?php DP_BASE_URL?>/style/default/bootstrap_new.min.css" />
            <link rel="stylesheet" href="<?php DP_BASE_URL?>/style/default/leaflet/leaflet.css" />
            <script src="<?php echo DP_BASE_URL?>/style/default/leaflet/leaflet.js"></script>
            <style type="text/css">
                .cadre_1{
                    /*border: 1px solid red;*/
                    height: 610px;
                }

                .cadre_2 {
                    /*border: 1px solid blue;*/
                    height: 610px;
                }

                .cadre_x{
                    /*border: 1px solid green;*/
                    height: 610px;
                }

                .cadre_y{
                    /*border: 1px solid violet;*/
                    height: 610px;
                }
                .dimens{
                    /*margin-top:1px;*/
                    width: 325px;
                }
                .colored{
                    background-color: blue;
                    height:305px;

                }

                .yellow{
                    background-color: blue;
                    height:140px;
                }

                .blancBox{
                    height: 60px;
                }
                .verticalText{
                    writing-mode: vertical-rl;
                    mixed: 180;
                    text-orientation: sideways-left;
                    color: white;
                    font-weight: bolder;
                    padding-top: 15px;
                    font-size: 15px;
                }
                #map{
                    height: 610px;
                }

                #box{
                    background-color: #ff6a03;
                    display: inline-block;
                    height: 70px;
                    width: 76px;
                }

                #box_2{
                    background-color: #0000ff;
                    display: inline-block;
                    height: 70px;
                    width: 76px;
                }

                .blanc{
                    background-color: white;
                    height: 70px;
                    border-top:1px solid black;
                }

            </style>
    </head>
    <body>
    <br />
        <div class="row">
            <div class="col-md-8 col-md-offset-1 cadre_1">
                <div class="row">
                    <div class="col-md-1 cadre_x">
                        <div class="row">
                            <div class="col-md-12 colored verticalText">Emergency Dashboard <br />Updated: 09 December 2016</div>
                            <div class="col-md-12 colored verticalText" style="margin-top: 1px"><span>Haiti - Hurricane Matthew</span>
                            </div>
                        </div>

                        <div class="row">
                        </div>
                    </div>

                    <div class="col-md-11 cadre_y">
                        <div class="row">
                            <div id="map" class="col-md-12">

                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-md-2 cadre_2">
                <div class="panel panel-primary dimens">
                    <div class="panel-heading">
                        <h3 class="panel-title">People Assisted</h3>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-12 blancBox"></div>
                        <div class="col-md-12 blancBox"></div>
                    </div>
                </div>


                <div class="panel panel-success dimens">
                    <div class="panel-heading">
                        <h3 class="panel-title">FUNDING</h3>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-12 blancBox"></div>
                        <div class="col-md-12 blancBox"></div>
                    </div>
                </div>

                <div class="panel panel-danger dimens">
                    <div class="panel-heading">
                        <h3 class="panel-title">COMMON SERVICES</h3>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-12 blancBox"></div>
                        <div class="col-md-12 blancBox"></div>
                    </div>
                </div>

            </div>

        </div>
        <div class="row">
            <div class="col-md-11 col-md-offset-1 yellow">
                <div class="row">
                    <div id="box" class="col-md-1"><img src="<?php echo DP_BASE_URL?>/style/default/images/orange.PNG" style="height: 70px;width: 62px;"> </div>
                    <div class="col-md-9 blanc"></div>
                </div>
                <div class="row">
                    <div id="box_2" class="col-md-1"><img src="<?php echo DP_BASE_URL?>/style/default/images/pam_log.PNG" style="height: 70px;width: 62px;"></div>
                    <div class="col-md-9 blanc" id="moi"></div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            var mymap = L.map('map').setView([18.538381, -72.331454], 13);
            L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(mymap);

            var legend = L.control({position: 'bottomleft'});

            legend.onAdd = function (map) {

                var div = L.DomUtil.create('#moi', 'info legend');



                categories = ['STX','HHX','HF','STX/HF'];

                for (var i = 0; i < categories.length; i++) {
                    div.innerHTML +=
                        '<i style="background:' + getColor(categories[i]) + '"></i> ' +
                        (categories[i] ? categories[i] + '<br>' : '+');
                }

                return div;
            }
        </script>
    </body>
</html>
