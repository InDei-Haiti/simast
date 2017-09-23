<?php

require_once 'base.php';
require_once(DP_BASE_DIR.'/lib/mpdf/mpdf.php');
require_once(DP_BASE_DIR.'/modules/outputs/outputs.class.php');
if(isset($_GET['token']) && !empty($_GET['token'])){
	if (file_exists(DP_BASE_DIR . '/files/tmp/' .$_GET['token']. '.html')){
		$pdfhtml = file_get_contents(DP_BASE_DIR . '/files/tmp/' .$_GET['token']. '.html');
		$mpdf=new mPDF();
		$mpdf->WriteHTML($pdfhtml);
		$mpdf->Output($_GET['token'].'.pdf','D');
	}else{
		echo 'Pdf not exist';
	}
}else{
	echo 'The token is lost';
}

?>