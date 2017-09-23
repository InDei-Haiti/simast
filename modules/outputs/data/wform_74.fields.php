<?php
$partShow=true;
$selects = array();
$fields=array("fld_0" => "1.test",
"wform_sub_1" => array("title"=>"membre","value"=>"plural",
							"query"=>array(
									"set"=>"select * from wf_74_sub_1 where wf_id='%d'",
									"fields"=>array(
										"fld_0" => "1.nom",
"fld_1" => "2.prenom"
									)
								)
							));
?>