<?php
$partShow=true;
$selects = array("fld_6" => '',
"fld_8" => '',
"fld_9" => '');
$fields=array("fld_0" => array("title"=>"1.Date d'enregistrement","xtype"=>"date"),
"fld_1" => "2.First Name",
"fld_2" => "3.Last Name",
"fld_3" => "4.Nickname",
"fld_4" => array("title"=>"5.Gender","value"=>"sysval","query"=>"1"),
"fld_5" => array("title"=>"6.Date Of Birth","xtype"=>"date"),
"fld_6" => array('title'=>'7.Place Of Birth','value'=>'preSQL','query'=>'','rquery'=>''
				),
"fld_7" => "8.Adress",
"fld_8" => array('title'=>'9.Communes','value'=>'preSQL','query'=>'','rquery'=>''
				),
"fld_9" => array('title'=>'10.Communal Section','value'=>'preSQL','query'=>'','rquery'=>''
				),
"fld_10" => "11.Phone 1",
"fld_11" => "12.Phone 2",
"fld_12" => "13.Phone 3",
"fld_13" => array("title"=>"14.Marital Status","value"=>"sysval","query"=>"6"),
"fld_14" => "15.NIF",
"fld_15" => "16.CIN",
"fld_16" => "17.Other Identification",
"fld_17" => array("title"=>"18.Education Level","value"=>"sysval","query"=>"7"),
"fld_18" => array("title"=>"19.Health Status","value"=>"sysval","query"=>"8"),
"fld_19" => "20.Profession",
"fld_20" => "21.Occupation",
"fld_21" => "22.Status In The House",
"fld_22" => "23.How Many Rooms",
"wform_sub_23" => array("title"=>"Membre","value"=>"plural",
							"query"=>array(
									"set"=>"select * from wf_52_sub_23 where wf_id='%d'",
									"fields"=>array(
										"fld_0" => "1.First Name",
"fld_1" => "2.Last Name",
"fld_2" => array("title"=>"3.Date Of Birth","xtype"=>"date"),
"fld_3" => array("title"=>"4.Sex","value"=>"sysval","query"=>"1")
									)
								)
							));
?>