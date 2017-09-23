<?php
$pcen=(int)$_GET['cid'];
if($pcen > 0){
	$q=new DBQuery();
	$q->addTable('clinic_location');
	$q->addWhere('clinic_location_clinic_id="'.$pcen.'"');
	$q->addQuery('clinic_location_id as id, clinic_location as name');
	$locs=$q->loadHashListMine();
	if(count($locs) > 0){
		echo json_encode($locs);
	}else{
		echo 'fail';
	}
}else{
	echo 'fail';
}
return ;
?>