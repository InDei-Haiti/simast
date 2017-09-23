<?php
// get GETPARAMETER for client_id
$client_id = 1;

$canRead = !getDenyRead( 'clients' );
if (!$canRead) {
	$AppUI->redirect( "m=public&a=access_denied" );
}
if (1 == 1)
	 {
		//export clients
		// Fields 1 - 5
		$text = sprintf("%s\r\n","\"First Name\",\"Middle Name\",\"Last Name\",\"Entry Date\"");
		$q  = new DBQuery;
		$q->addTable('clients', 'cl');
		$q->addQuery('cl.*');
		$clients = $q->loadList();
		foreach ($clients as $row) 
		{
			// Fields 1- 10
			$text .= sprintf("\"%s\",\"%s\",\"%s\",\"%s\",",$row['client_first_name'],$row['client_other_name'],$row['client_last_name'],$row['client_entry_date']);
			$text .= sprintf("\r\n");
		}
	//send http-output in csv format

	// BEGIN extra headers to resolve IE caching bug (JRP 9 Feb 2003)
	// [http://bugs.php.net/bug.php?id=16173]
		header("Pragma: ");
		header("Cache-Control: ");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");  //HTTP/1.1
		header("Cache-Control: post-check=0, pre-check=0", false);
	// END extra headers to resolve IE caching bug

	header("MIME-Version: 1.0");
	header("Content-Type: text/x-csv");
	header("Content-Disposition: attachment; filename=\"{$dPconfig['company_name']}_Clients.csv\"");
	print_r($text);
} else {
$AppUI->setMsg( "clientIdError", UI_MSG_ERROR );
	$AppUI->redirect();
}
?>
