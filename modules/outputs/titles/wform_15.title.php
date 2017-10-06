<?php
$titles["wform_15"]=
		array(
			"title" => "Etudiant Form",
			"db"=>"wform_15",
			"client"=>"client_id",
			"uid"=>"tbw15",
			"date"=>"entry_date",
			"client_name"=> "concat(client_first_name,' ',client_last_name) as client_name",
			"did"=>"id",
			"defered"=>array(),
			"abbr"=>"WF15",
			"link"=>array("href"=>"?m=wizard&a=form_use&client_id=#client_id#&itemid=#did#&fid=15&todo=addedit","vals"=>array("client_id","did")),
			"plurals"=>array(),
			"referral"=>"",
			"next_visit"=>"",
			"form_type"=>"contus"
		);
?>