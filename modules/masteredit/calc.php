<?php

if($_POST['mode'] == 'btable' && trim($_POST['calcs']) != ''){
	$cl=preg_replace('/\\\{1,}"/','"',$_POST['calcs']);	
	$svals=json_decode($cl,true);	
	
	$bar=$_SESSION['stat'];
	
	$row_levels=array();
	$firstr=$svals['id'];	
	$tcomp = new Ranger($svals['type'],$svals['range'],$svals['title']);	
	$prs = new Stater($bar,$firstr,$svals['list'],$svals['cols'],$svals['stots']);
 	$trows=count($svals['rows']); 	
 	$tcols=count($svals['cols']); 	
	for($br=0;$br < $prs->getToplevs();$br++){
		$path=array();	
		for($i=1; $i < $trows;$i++){
			$crowf=$svals['rows'][$i];			
			$prs->validChilds($crowf['id'],($i-1),$br);
			$path[]=$i;
		}
		$prs->countRows($br,$trows);
	}
	if($trows == 0){
		$prs->pureCols($tcols);
	}
	
	$thtml=$prs->buildIt();
	echo $thtml;
	return ;
	
	//for numeric ranges start <= val && end > val
}
?>