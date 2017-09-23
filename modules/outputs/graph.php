<?php

 require_once("chart/pData.class");   
 require_once("chart/pChart.class");  
 
 function acron ($str){
 	if(strlen($str) > 6){
 		$words = explode(' ',$str);
 		if(count($words) == 1){
 			$final=substr($str,0,6);
 		}else {
 			$finar=array();
 			foreach ($words as $wd) {
 				$finar[]=substr($wd,0,3);
 			}
 			$final=implode(' ',$finar);
 		}
 	}else{
 		$final=$str;
 	}
 	return $final;
 }
 
 function denuller ($a){
 	if(is_null($a)){
 		unset($a);
 	}else{
 		return  $a;
 	}
 }
 
 function denuller1($a){
 	if(!is_null($a)){
 		return true;
 	}else{
 		return false;
 	}
 }

 function sub_rand(){
 	$t=dechex(mt_rand(0,255));
 	if(strlen($t) == 1){
 		$t='0'.$t;
 	}
 	return $t;
 }
 
 function rand_colorCode(){
 	$r = sub_rand();
 	$g = sub_rand();
 	$b = sub_rand();
 	$rgb = $r.$g.$b; 	
 	return $rgb;
 }
 
 $indset=trim($_POST['dset']);
 $fontPath=$baseDir.'/modules/outputs/tahoma.ttf';
 
 $seriesCounter=0;
 
 if(strlen($indset) == 0){
 	echo 'fail';
 	return ;
 }else{
 	$data = json_decode ( stripslashes($indset ) ,true);	
 }
  // Dataset definition    
 $DataSet = new pData;   
 
 if (count($data['data']) == 0) {
 	return false;
 }
 $ycs=false;
 if(isset($data['row_use']) && $data['row_use'] == 'ycall'){
 	unset($data['row_use']);
 	if(isset($data['boxes']['rows'][0])){
 		$brtitle=$data['boxes']['rows'][0]['title'];
 		$brow=0;
 	}
 	$ycs=true;
 }
 
 if(isset($data['row_use'])){
 	$bids=array();
 	foreach ($data['rowb'] as $rbc){
 		$bids[]=$rbc[0];
 	}
 	$brow=(int)$data['row_use'];
 	$len=$data['rows'][$brow][0];
 	$brtitle=$data['rows'][$brow][1];
 	$ndrows=array_splice($data['rows'],$brow+1,$len); 	
 	if($brow === 0){
 		$data['data']=array_splice($data['data'],0,$len);
 	}else{
 		$ib=0;
 		$ni=0;
 		reset($data['rows']);
 		while($ni < $brow){
 			$cur=current($data['rows']);
 			if(in_array($ni,$bids)){
 				$ib++; 				
 			}
 			$ni++;
 			next($data['rows']); 			
 		}
 		$cur=current($data['rows']); 		
 		$odata= $data['data'];
 		$nodata=array();
 		foreach ($odata as $dv){
 			if(!is_null($dv)){
 				$nodata[]=$dv;
 			}
 		}
 		$odata=$nodata;
 		unset($nodata); 		
 		$data['data']=array();
 		//$ni--;
 		for($i=0;$i < $cur[0];$i++){
 			$data['data'][]=$odata[(($ni-$ib)+$i)];
 		}
 	}
 	$oldrows=$data['rows'];
 	$data['rows']=$ndrows;
 }else{
 	for($i=0;$i < count($data['rows']); $i++){
 		if(count($data['rows'][$i]) > 0 ){
 			$urows=$data['rows'][$i];
 			$ir=count($data['rows']);
 		}
 	}
 }
 
 if(isset($data['col_use']) && $data['col_use'] == 'xcall'){
 	unset($data['col_use']);
 }
 
 if(isset($data['col_use'])){
 	$bcol=(int)$data['col_use'];
 	$len=$data['cols'][0][$bcol][0];
 	$bctitle=$data['cols'][0][$bcol][1];
 	$i=0;
 	$wtop=$data['cols'][1];
 	$rdtop=array();
 	$induse=array();
 	if($bcol == 0){
 		for($i=0; $i < $len; $i++){
 			$rdtop[]=$wtop[$i];
 			
 		}
 		$induse=array(0,$len);
 	}else{
 		$outnd=0;
 		for($z=0;$z < $bcol; $z++){
 			$zc=$data['cols'][0][$z][0];
			while ($zc > 0) {
				array_shift($wtop);
				$zc -- ;
				$outnd++;
			}
 		}
 		$rdtop=array_splice($wtop,0,$len);
 		$odata=$data['data'];
 		$induse=array($outnd,$len);
 		
 	}
 	//$ndcols=array_splice($data['cols'],$bcol+1,$len);
 	$oldcols=$data['cols'];
 	$data['cols']=$rdtop;
 	$ndata=array();
 	foreach ($data['data']  as $drow){
 		$ndata[]=array_splice($drow,$induse[0],$induse[1]);
 	}
 	$data['data']=$ndata;
 	$ucols=$data['cols'];
 }else{
 	for($i=0;$i < count($data['cols']); $i++){
 		if(count($data['cols'][$i]) > 0 ){
 			$ucols=$data['cols'][$i];
 			$i=count($data['cols']);
 		}
 	}
 }
 
 $mode=$_POST['cmode'];
 
 $margin=true;
 $exePart='';
 $yunits='';
 $scale=SCALE_START0;
 $chartSize=array(840,260);
 
 $titlePos=array(100,295);
 $titlePos2=array(800,480);
 
 $legend='$Test->drawLegend(720,20,$DataSet->GetDataDescription(),236,238,240,52,58,82);';
 $titlePos=array();
 switch ($mode) {
 	case 'pbars':
 		$exePart='$Test->drawStackedBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),TRUE);';
 		$yunits="%";
 		$scale=SCALE_ADDALLSTART0;
 		break;
 	case 'sbars':
 		$exePart='$Test->drawStackedBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),TRUE);';
 		$scale=SCALE_ADDALLSTART0;//SCALE_ADDALL;
 		break;
 		
 	case 'bars':
		var_dump($_POST);
 		$exePart='$Test->drawBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),TRUE);';
 		break;
 	case 'lines':
 		$margin=false; 		
 		$exePart='$Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());   
 				$Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2);';   //NO TRANSPARENCY 255,255,255'
 		//$titlePos=array(600, 244);
 		//$titlePos2=array(800,258);
 		$scale=SCALE_ADDALLSTART0;
 		break;
 	case 'pie':
 		$exePart=' $Test->drawPieGraph($DataSet->GetData(),$DataSet->GetDataDescription(),155,110,110,PIE_PERCENTAGE,TRUE,70,25,10);  ';
		$legend='$Test->drawPieLegend(305,15,$DataSet->GetData(),$DataSet->GetDataDescription(),220,220,220); ';
		$chartSize=array(400,260);
		$titlePos=array(20,240);
		$titlePos2=array(400,260);
 		break;
 	default:
 		break;
 }
 
 
 $precols=array(); 
 foreach ($ucols as $key => $col){
 	for($i=0;$i < $col[0]; $i++){
 		$precols[]=acron($col[1]);
 	}
 }
 
 /*
 	CAse of bars of percents & pie chart
 */
 
 $ctext='all rows';
 
 if($mode == 'pbars'){
 	$perc=array();
 	$dlength = count($data['data'][0]);
 	for($x=0;$x < $dlength;$x++ ){
 		$ctot=0;
 		for ($y=0; $y < count($data['data']); $y++) {
 			$ctot+=$data['data'][$y][$x];
 		}
 		if($ctot == 0){
 			$ctot=1;
 		}
 		for ($y=0; $y < count($data['data']); $y++) {
 			if(!is_array($perc[$y]) ){
 				$perc[$y]=array();
 			}
 			$perc[$y][$x]=(($data['data'][$y][$x] * 100)/$ctot);
 		}
 	}

 	foreach ($perc  as $key => $dset){
 		if(count($dset) > 0){
 			$DataSet->AddPoint($dset,$data['rows'][$key][1],$data['rows'][$key][1]);
 			++$seriesCounter;
 			//$DataSet->AddSerie($data['rows'][$key]);
 		}
 	}
 }else if($mode == 'pie' || ($mode == "lines" && (int)$_POST['urow']  >= 0 )){
 	$crow=(is_array($_POST['urow']) ? (int)$_POST['urow']['urow'] : 0);
 	if(($crow+1) > count($data['rows'])){
 		$crow=0;
 	}
 	$perc=array();
 	$dlength = count($data['data'][$crow]);
 	$ctot=array_sum($data['data'][$crow]);
 	if($ctot == 0){
 		$ctot=1;
 	}
 	for ($x=0; $x < $dlength; $x++) {
 		if(!is_array($perc[0]) ){
 			$perc[0]=array();
 		}
 		if($mode == 'pie'){
 			$perc[0][$x]=(($data['data'][$crow][$x] * 100)/$ctot);
 		}else{
 			$perc[0][$x]=$data['data'][$crow][$x];
 		}
 	}


 	foreach ($perc  as $key => $dset){
 		if($crow >= 0 ){
 			$ukey=$crow;
 		}else{
 			$ukey=$key;
 		}
 		if(count($dset) > 0){
 			$DataSet->AddPoint($dset,$data['rows'][$ukey][1],$data['rows'][$ukey][1]);
 			++$seriesCounter;
 			//$DataSet->AddSerie($data['rows'][$ukey]);
 		}
 	}
 	if($crow >= 0){
 		$ctext=$data['rows'][$crow][1]; 		 		
 	} 	
 }else{ // all other types of chars
 	foreach ($data['data']  as $key => $dset){
 		if(count($dset) > 0){
 			$DataSet->AddPoint($dset,$data['rows'][$key][1],$data['rows'][$key][1]);
 			++$seriesCounter;
 			//$DataSet->AddSerie($data['rows'][$key]);
 		}
 	} 	
 }
 
 
 $DataSet->AddPoint($precols,"Serie4");
 ++$seriesCounter;
 $DataSet->AddAllSeries();   
 $DataSet->SetAbsciseLabelSerie("Serie4");
 
 $ltar=array();
 if(isset($brow)){
 	if($mode != 'pie' ){
 		$ltar[]=$data['boxes']['cols'][0]['title'].' for '.$data['boxes']['rows'][0]['title'] . ($ycs === true ? '' : ' = '.$brtitle);
 	}else{
 		$ltar[]=$brtitle;
 	}
 }
 if($bctitle != ''){
 	if($mode != 'pie'){
 		$ltar[]=$data['boxes']['cols'][0]['title'].' = '.$bctitle;
 	}else{
 		$ltar[]=$bctitle;
 	}
 }

if(count($ltar) == 0){
 		$ltar[]=$data['boxes']['cols'][0]['title'];
 } 	
 
 if($mode != 'pie'){ 	
 	///////////////////////// PERC BARS //////////////////
 	$DataSet->SetYAxisUnit($yunits);
 	///////////////////////// END PERC BARS ////////////////
 	
 	//$DataSet->SetXAxisName($ltitle);
 	
 	//$DataSet->SetYAxisName($data['boxes']['rows'][0]['title']);
 	$DataSet->SetYAxisName('Amount');
 }
  
 
 // Initialise the graph   
 $Test = new pChart($chartSize[0],$chartSize[1]);
 //$Test->drawGraphAreaGradient(251,251,251/*132,153,172*/,20,TARGET_BACKGROUND); 
 //$Test->loadColorPalette($baseDir.'/modules/outputs/chart/tones.db',',');
 
 if(isset($_POST['palette'])){
	$currentPalette=( is_string($_POST['palette']) ? json_decode(stripslashes($_POST['palette']),true) : $_POST['palette']);
	if(count($currentPalette) < $seriesCounter)	{
		for($ic=(count($currentPalette)-1),$ie=$seriesCounter; $ic < $ie; $ic++){					
			$currentPalette[] = rand_colorCode();
		}
	}
 }else{
 	$currentPalette=array();
 	for ($ic=0; $ic < $seriesCounter; $ic++ ){ 		
 		$currentPalette[]=rand_colorCode();
 	}
 }

 $ic=0;
 foreach ($currentPalette as $colour){
 	preg_match("/(\w{2})(\w{2})(\w{2})/",$colour,$rgbs);
 	$Test->setColorPalette($ic++,hexdec($rgbs[1]),hexdec($rgbs[2]),hexdec($rgbs[3])); 		 	
 }
 
 if($mode !='pie'){
 	$Test->drawFilledRoundedRectangle(3,3,833,253,5,248,248,248);     
	$Test->drawRoundedRectangle(1,1,835,255,5,110,110,110); 
 	$Test->setFontProperties($fontPath,8);   
 	$Test->setGraphArea(60,20,710,215);    
 	//$Test->drawGraphAreaGradient(162,183,202,50);  
 	$Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),$scale,125,125,125,/*213,217,221,*/TRUE,0,2,$margin);   
 	$Test->drawGrid(4,TRUE,203,203,203,/*230,230,230,*/20);
 }else{
 	$Test->drawFilledRoundedRectangle(3,3,395,253,5,248,248,248);     
	$Test->drawRoundedRectangle(1,1,397,258,5,110,110,110); 
 }
   
 $DataSet->RemoveSerie("Serie4");
 // Draw the 0 line   
 $Test->setFontProperties($fontPath,8);   
 if($mode != 'pie'){
 	$Test->drawTreshold(0,143,55,72,TRUE,TRUE);   
 }
  
 eval($exePart);
  
 // Finish the graph   
 $Test->setFontProperties($fontPath,8);   
 eval ($legend);
 
 if($mode == 'pie' || $mode == 'lines'){
 	$ltar[]=$ctext; 	
 }
 
 $ltar=array_filter($ltar,'denuller1');
 reset($ltar);
 $Test->setFontProperties($fontPath,10);   
 $Test->drawTitle($titlePos[0],$titlePos[1],(count($ltar) > 1 ? implode(' and ',$ltar) : current($ltar)),50,50,50,$titlePos2[0],$titlePos2[1]);   
 
 $filePath=$baseDir.'/files/tmp/'.$_SESSION['fileNameCsh'].'.png';
 //$Test->AddBorder(1);
 $Test->Render($filePath);
 if($_GET['a']!= 'reports' ){
 	$_SESSION ['fileNameCshfBack'] = $filePath;
 	if(filesize($filePath) > 10){
 	//echo $_SESSION['fileNameCsh'];
 		$fh=fopen($filePath,'r');
 		echo json_encode($currentPalette), '\\c',base64_encode(fread($fh,filesize($filePath)));
 		fclose($fh);
 	}else{
 		echo 'fail';
 	}
 }else{
 	return $filePath; 
 }
?>