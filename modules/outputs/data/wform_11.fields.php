<?php
$partShow=true;
$selects = array("fld_1" => 'select client_id as id, CONCAT_WS(" ",client_first_name,client_last_name) as name from clients  order by name asc');
$fields=array("fld_0" => "1.name",
"fld_1" => array('title'=>'2.list','value'=>'preSQL','query'=>'clientName','rquery'=>'clientId'
				));
?>