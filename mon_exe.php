<?php
$baseDir = dirname(__FILE__);
$ikey=$_GET['key'];
$current=@file_get_contents($baseDir.'/files/flags/'.$ikey);
if(!is_numeric($current)){
	$current=0;
}
echo $current;
if($current == 100){
	@unlink($baseDir.'/files/flags/'.$ikey);
}
return ;
?>