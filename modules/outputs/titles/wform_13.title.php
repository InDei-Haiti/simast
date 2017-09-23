<?php
$titles["wform_13"]=
		array(
			"title" => "Medical entry",
			"db"=>"wform_13",
			"client"=>"client_id",
			"uid"=>"tbw13",
			"date"=>"entry_date",
			"client_name"=> "concat(client_first_name,' ',client_last_name) as client_name",
			"did"=>"id",
			"defered"=>array(),
			"abbr"=>"WF13",
			"link"=>array("href"=>"?m=wizard&a=form_use&client_id=#client_id#&itemid=#did#&fid=13&todo=addedit","vals"=>array("client_id","did")),
			"plurals"=>array(),
			"referral"=>"",
			"next_visit"=>"",
			"form_type"=>"contus"
		);
?>