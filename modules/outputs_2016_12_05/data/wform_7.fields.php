<?php
$partShow=true;
$selects = array("fld_1" => 'select clinic_id as id,clinic_name as name from clinics order by name asc',
"fld_4" => 'select id , title as name from positions order by name asc ',
"fld_5" => 'select clinic_location_id as id, clinic_location as name from clinic_location order by name asc ');
$fields=array("fld_0" => "1.Country",
"fld_1" => array('title'=>'2.Region','value'=>'preSQL','query'=>'clinicName','rquery'=>'clinicId'
				),
"fld_2" => array("title"=>"3.Market","value"=>"sysval","query"=>"3"),
"fld_3" => array("title"=>"4.Date","xtype"=>"date"),
"fld_4" => array('title'=>'5.Item','value'=>'preSQL','query'=>'positionName','rquery'=>'positionId'
				),
"fld_5" => array('title'=>'6.Unit','value'=>'preSQL','query'=>'locationName','rquery'=>'locationId'
				),
"fld_6" => "7.Price");
?>