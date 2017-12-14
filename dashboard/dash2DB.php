<?php
/**
 * Created by PhpStorm.
 * User: rulxphilome.alexis
 * Date: 12/14/2017
 * Time: 12:21 PM
 */
if(isset($_POST['dt'])){
//    var_dump($_POST['dt']);
    $link = mysql_connect("localhost", "simastadmin", "fasil")
    or die("Impossible de se connecter : " . mysql_error());
    $db = mysql_select_db("simast", $link);
    $datazs = json_encode($_POST['dt']);
    $updt = mysql_query("UPDATE new_dashboard SET etat = 0");
    $rstl = mysql_query("INSERT INTO new_dashboard(titles,etat) VALUES('".$datazs."',1)");
    if($rstl AND $updt){
        echo "Yes";
    }else{
        echo "No ".mysql_error();
    }
    mysql_close($link);
}