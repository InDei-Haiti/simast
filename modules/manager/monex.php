<?php
$found=false;
$ipath=$baseDir.'/modules/manager/eflag/'.$_GET['ekey'];
$i=0;
while ($found === false || $i == 300) {
	if(file_exists($ipath)){
		$found=true;
	}else{
		sleep(2);
		++$i;
	}
}
@unlink($ipath);
echo "ok";
return ;
?>