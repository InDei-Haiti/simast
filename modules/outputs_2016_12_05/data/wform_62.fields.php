<?php
$partShow=true;
$selects = array("fld_9" => 'select contact_id as id, CONCAT_WS(" ",contact_first_name,contact_last_name) as name from contacts  where contact_id<>"13" and contact_active="1" order by name asc',
"fld_10" => 'select contact_id as id, CONCAT_WS(" ",contact_first_name,contact_last_name) as name from contacts  where contact_id<>"13" and contact_active="1" order by name asc');
$fields=array("fld_0" => array("title"=>"1.Registration Date","xtype"=>"date"),
"fld_1" => "2.Amount (HTG)",
"fld_2" => array("title"=>"3.Motive Type","value"=>"sysval","query"=>"44"),
"fld_3" => "4.Other approaches tried",
"fld_4" => "5.The reason for the request",
"fld_5" => "6.Other documents",
"fld_6" => "7.Recommandation",
"fld_7" => array("title"=>"8.Request statut","value"=>"sysval","query"=>"45"),
"fld_8" => array("title"=>"9.Social worker in charge","xtype"=>"date"),
"fld_9" => array('title'=>'10.Head of the social service','value'=>'preSQL','query'=>'staffName','rquery'=>'staffId'
				),
"fld_10" => array('title'=>'11.Director in charge','value'=>'preSQL','query'=>'staffName','rquery'=>'staffId'
				),
"fld_11" => array("title"=>"12.processing date","xtype"=>"date"));
?>