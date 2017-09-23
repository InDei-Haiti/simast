<?php

function dig($parent){
	$sql = 'select region_id from administrative_regions where region_parent ="'.$parent.'"';
	$res = mysql_query($sql);
	$fresh = array();
	$tokill = array();
	if($res){
		while ($rp = mysql_fetch_assoc($res)) {
			$fresh[]= $rp['region_id'];
		}
		if(count($fresh) > 0){
			foreach ($fresh as $unf) {
				$tokill = array_merge($tokill,dig($unf));
			}
		}
	}
	$fresh = array_merge($fresh,$tokill);
	return $fresh;
}

/*$dbc = mysql_connect('localhost','root','root');
mysql_select_db('wdw',$dbc);*/

$tokill = array();

$sql = 'select region_id from administrative_regions where region_id > 1 and region_id < 6';
$res = mysql_query($sql);
$fresh = array();
if($res){
	while ($rp = mysql_fetch_assoc($res)) {
		$fresh[]= $rp['region_id'];
	}
	mysql_free_result($res);
	foreach ($fresh as $pr){
		$tokill = array_merge($tokill,dig($pr));
	}
	$fresh = array_merge($fresh,$tokill);
}

$sql= 'delete from administrative_regions where region_id IN ('.join(",",$fresh).' )';
$k = mysql_query($sql);
echo count($fresh).' killed';
?>