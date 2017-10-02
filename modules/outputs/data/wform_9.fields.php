<?php
$partShow=true;
$selects = array("fld_2" => 'select clinic_location_id as id, clinic_location as name from clinic_location order by name asc ');
$fields=array("fld_0" => "1.name",
"fld_1" => array("title"=>"2.age","xtype"=>"date"),
"fld_2" => array('title'=>'3.list','value'=>'preSQL','query'=>'locationName','rquery'=>'locationId'
				),
"fld_3" => array("title"=>"4.visit date","xtype"=>"date"),
"fld_4" => "5.client name");
?>