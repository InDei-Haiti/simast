<?php
global $myspeed;
if (! defined ( 'DP_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}
$rpath='images/maps/';
//$fpath=DP_WORK_DIR.$rpath;
$fpath=$rpath;
if($_POST['link']!='' ){
	$markers=explode('|',$_POST['points']);
	$url=trim($_POST['link']);
	
	$im = imagecreatefrompng($url.'&key='.$GMapKey.'&format='.$_POST['format'].'&size='.$_POST['size'].'&sensor=false&maptype='.$_POST['maptype'].'&center='.$_POST['center'].'&zoom='.$_POST['zoom']);
	//$red=imagecolorallocate($im,255,0,0);
	$marki= imagecreatefrompng(DP_BASE_DIR.'/images/marker.png');// 'http://maps.gstatic.com/intl/ru_ALL/mapfiles/marker.png');//);
	//$minfo=getimagesize(DP_WORK_DIR.'/images/mark.png');
	$imd=array('x'=>imagesx($marki),'y'=>imagesy($marki));
	imagealphablending($marki,true);
	imagealphablending($im,false);
	imagesavealpha($marki,false);
	$trans= imagecolorallocatealpha($im,0,0,0,127);
	imagealphablending($im,true);	
	$zx=0;
	foreach ($markers as $mark){
		$cpos=explode(',',$mark);
		imagecopy($im,$marki,$cpos[0]-(2*$imd['x']),$cpos[1]-(1.1*$imd['y']),0,0,imagesx($marki),imagesy($marki));

	}
	$time=time();
	$epath=md5($time);
	while(file_exists(DP_BASE_DIR.$rpath.$epath.'.png')){
		$epath=md5(++$time);
	}
	//imagealphablending($im,false);
	/*imagealphablending($im,false);*/
	imagealphablending($im,true);
	imagesavealpha($im,false);
	imagepng($im,DP_BASE_DIR."/".$rpath.$epath.'.png',8);
	imagedestroy($im);
	///echo 'slink="http://'.$_SERVER['HTTP_HOST'].'/'.$fpath.$epath.'.png";';
	echo 'slink="'.$fpath.$epath.'.png";';
	return ;
}else{
	return;
}
?>