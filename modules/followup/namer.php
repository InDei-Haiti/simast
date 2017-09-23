<?php

require_once $AppUI->getModuleClass ( 'followup' );

global $answer;
$result='fail';
$answer = array ();
$ualone = 0;
$gallone = intval($_GET['alone']);
if (isset ( $_GET ['nadm'] ) && trim ( $_GET ['nadm'] ) != '') {
	$admno = trim ( $_GET ['nadm'] );
	
	$answer = makeListPerson ( $admno, $gallone );
	
	if(!is_null($answer)){
		$result = json_encode ( $answer );
	}
} 
echo $result;

return;
?>