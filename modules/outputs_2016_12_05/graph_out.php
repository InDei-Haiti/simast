<?php
if($_SESSION['fileNameCsh']!= ''){
	$filePath=$baseDir.'/files/tmp/'.$_SESSION['fileNameCsh'].'.png';
	$fh=fopen($filePath,'r');
	fpassthru($fh);
	return ;
}
?>