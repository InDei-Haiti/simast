<?php
/**
 * Created by PhpStorm.
 * User: rulxphilome.alexis
 * Date: 11/8/2017
 * Time: 12:05 PM
 */
require_once '../base.php';
$link = mysql_connect("localhost", "simastadmin", "fasil")
or die("Impossible de se connecter : " . mysql_error());
$db = mysql_select_db("simast", $link);
$titres = mysql_query("SELECT * FROM new_dashboard WHERE etat = 1");
$rslt;
while($r = mysql_fetch_assoc ($titres)){
    $rslt =  $r['titles'];
}
$dataz = json_decode(stripslashes($rslt),true);
//foreach ($dataz as $m){
//    echo "<pre>";
//    var_dump($m);
//    echo "</pre>";
//}
//echo "<pre>";
//var_dump(json_decode(stripslashes($rslt),true));
//echo "</pre>";
//var_dump(json_decode($rslt[1], true));
//$ls = json_decode($rslt[1], true);
//$ls = json_decode(json_encode($lesTitres), true);
?>


<!DOCTYPE html>
<html>
    <head>
            <title>New - DASHBOARD</title>
            <link rel="stylesheet" href="<?php echo DP_BASE_URL?>/assets/css/bootstrap.min.css" />
            <link rel="stylesheet" href="<?php echo DP_BASE_URL?>/assets/css/main.css" />
            <link rel="stylesheet" href="<?php echo DP_BASE_URL?>/assets/css/global.css" />
            <link rel="stylesheet" href="<?php echo DP_BASE_URL?>/assets/css/jasny-bootstrap.min.css" />
            <link rel="stylesheet" href="<?php echo DP_BASE_URL?>/assets/css/font-awesome.min.css" />
            <link rel="stylesheet" href="<?php echo DP_BASE_URL?>/assets/editor/summernote.css" />
            <link rel="stylesheet" href="<?php echo DP_BASE_URL?>/assets/js/leaflet/leaflet.css" />
            <script src="<?php echo DP_BASE_URL?>/assets/js/leaflet/leaflet.js"></script>
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
    <div class="navmenu navmenu-default navmenu-fixed-left offcanvas" style="left: 0px; right: 1169px;">
        <!--- Off Canvas Side Menu -->
        <div class="close" data-toggle="offcanvas" data-target=".navmenu">
            <i class="fa fa-close"></i>
        </div>
        <h3 class="title-menu">Modifier</h3>
        <ul class="nav navmenu-nav"> <!--- Menu -->
            <li><a href="#" id="titre">Titres</a></li>
            <li><a href="#" id="boites">Boites</a></li>
            <li><a href="#" id="carte">Carte</a></li>
        </ul><!--- End Menu -->
    </div>
    <div class="tbtn wow pulse animated" id="menu" data-wow-iteration="infinite" data-wow-duration="500ms" data-toggle="offcanvas" data-target=".navmenu" style="visibility: visible;-webkit-animation-duration: 500ms; -moz-animation-duration: 500ms; animation-duration: 500ms;-webkit-animation-iteration-count: infinite; -moz-animation-iteration-count: infinite; animation-iteration-count: infinite;">
        <p><i class="fa fa-file-text-o"></i> Modifier</p>
    </div>
    <div id="setModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <span id="closeSetModal" class="close"><i class="fa fa-close"></i></span>
                <h2>Modification du Set : Education</h2>
            </div>
            <div class="modal-body">
                <div id="text_editor"></div>
                <button id="bb">Save Modification</button>
                <button id="bb2">Editer tous les titres</button>
            </div>
        </div>

    </div>
<!--    Dashboard Begin-->
    <div class="row">
            <div class="col-md-8 col-md-offset-1 cadre_1">
                <div class="row">
                    <div class="col-md-1 cadre_x">
                        <div class="row">
                            <div class="col-md-12 colored verticalText">
                                <span class="editable" id="title_1"><?php
                                    foreach($dataz as $val){
                                        if(isset($val['title_1'])){
                                            echo $val['title_1'];
                                        }
                                    }
                                    ?></span></div>
                            <div class="col-md-12 colored verticalText" style="margin-top: 1px"><span class="editable" id="title_2"><?php
                                    foreach($dataz as $val){
                                        if(isset($val['title_2'])){
                                            echo $val['title_2'];
                                        }
                                    }
                                    ?></span>
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
                        <h3 class="panel-title editable" id="boite_1"><?php
                            foreach($dataz as $val){
                                if(isset($val['boite_1'])){
                                    echo $val['boite_1'];
                                }
                            }
?></h3>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-12 blancBox"></div>
                        <div class="col-md-12 blancBox"></div>
                    </div>
                </div>


                <div class="panel panel-success dimens">
                    <div class="panel-heading">
                        <h3 class="panel-title editable" id="boite_2"><?php
                            foreach($dataz as $val){
                                if(isset($val['boite_2'])){
                                    echo $val['boite_2'];
                                }
                            }
?></h3>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-12 blancBox"></div>
                        <div class="col-md-12 blancBox"></div>
                    </div>
                </div>

                <div class="panel panel-danger dimens">
                    <div class="panel-heading">
                        <h3 class="panel-title editable" id="boite_3"><?php
                            foreach($dataz as $val){
                                if(isset($val['boite_3'])){
                                    echo $val['boite_3'];
                                }
                            }
?></h3>
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
                    <div id="box" class="col-md-1"><img src="<?php echo DP_BASE_URL?>/assets/img/orange.PNG" style="height: 70px;width: 62px;"> </div>
                    <div class="col-md-9 blanc"></div>
                </div>
                <div class="row">
                    <div id="box_2" class="col-md-1"><img src="<?php echo DP_BASE_URL?>/assets/img/pam_log.PNG" style="height: 70px;width: 62px;"></div>
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

                var div = L.DomUtil.create('div', 'info legend','moi');



                categories = ['STX','HHX','HF','STX/HF'];

                for (var i = 0; i < categories.length; i++) {
                    div.innerHTML +=
                        '<i style="background:' + getColor(categories[i]) + '"></i> ' +
                        (categories[i] ? categories[i] + '<br>' : '+');
                }

                return div;
            }
        </script>
        <script src="<?php echo DP_BASE_URL?>/assets/js/jquery-min.js"></script>
        <script src="<?php echo DP_BASE_URL?>/assets/js/bootstrap.min.js"></script>
        <script src="<?php echo DP_BASE_URL?>/assets/js/jasny-bootstrap.min.js"></script>
        <script src="<?php echo DP_BASE_URL?>/assets/editor/summernote.min.js"></script>
        <script src="<?php echo DP_BASE_URL?>/assets/js/main.js"></script>
    </body>
</html>
