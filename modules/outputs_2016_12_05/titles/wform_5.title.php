<?php
$titles["wform_5"]=
		array(
			"title" => "Test Form",
			"db"=>"wform_5",
			"client"=>"client_id",
			"uid"=>"tbw5",
			"date"=>"entry_date",
			"client_name"=> "concat(client_first_name,' ',client_last_name) as client_name",
			"did"=>"id",
			"defered"=>array(),
			"abbr"=>"WF5",
			"link"=>array("href"=>"?m=wizard&a=form_use&client_id=#client_id#&itemid=#did#&fid=5&todo=addedit","vals"=>array("client_id","did")),
			"plurals"=>array(),
			"referral"=>"",
			"next_visit"=>"",
			"form_type"=>"contus"
		);
?>