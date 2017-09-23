<?php

function ti($s){
	return intval(trim($s));
}

$case='INSERT INTO `admission_info`';
$match=false;
$ind=0;
$ns=array(20,23,26);
if($argv[1] != ''){
	$fh=fopen($argv[1],'r');
	$fw=fopen($argv[1].'.edit',"a");
	while (!feof($fh)) {
		$prestr=fgets($fh);
		$bits=explode(',',$prestr);
		foreach ($ns as $n) {
			$v=$bits[$n];
			if($v != 'NULL'){
				$v=ti($v);
				if($v > $ind){
					$ind=$v;
				}else{
					$bits[$n]='NULL';
				}
			}
		}
		$nstr=implode(',',$bits);
		fputs($fw,$nstr);
	}
	fclose($fh);
	fclose($fw);
}
?>