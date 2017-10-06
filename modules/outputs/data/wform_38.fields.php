<?php
$partShow=true;
$selects = array("fld_1" => 'select id , title as name from positions order by name asc ');
$fields=array("fld_0" => array("title"=>"1.status","value"=>"sysval","query"=>"8"),
"fld_1" => array('title'=>'2.position','value'=>'preSQL','query'=>'positionName','rquery'=>'positionId'
				));
?>