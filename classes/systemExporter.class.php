<?php
class systemExporter {

	protected $way;
	protected $workbook;
	protected $file_name;
	protected $bold;
	protected $big_tar=array();
	protected $worksheet;
	protected $fh;
	protected $last=false;
	protected $final;
	protected $dir;

	function __construct($mode,$name,$appendix=false,$case="system") {
		global $baseDir;
		$this->dir=$baseDir.'/files/tmp/';
		if($appendix !== false){
			$this->dir.=$appendix.'/';
			if(!file_exists($this->dir)){
				mkdir($this->dir);
			}
		}		
		$this->way = $mode;
		$this->file_name = $name.'-export-'.date("dmY");
		if ($mode === 'excel') {
			require_once ("$baseDir/lib/Spreadsheet/Excel/Writer.php");
			$this->workbook = new Spreadsheet_Excel_Writer ();
			//$this->file_name = str_replace(' ','_',$dPconfig ['company_name']).'-'.$dPconfig['current_center'] .'-export-'.date("dmY"). ".xls";
			$this->file_name.=".xls";
			$this->workbook->send ( $this->file_name );
			$this->bold= & $this->workbook->addFormat ();
			$this->bold->setBold();
		}else{
			$this->file_name.=($case === 'system' ? ".sbn" : '.tbn');
			$this->fh=fopen($this->dir.$this->file_name.'.t',"w+");			
			$this->final = fopen($this->dir.$this->file_name,"w+");
		}
	}



	/*function store($title,&$data,$zkeys=array(),$headers,$keys,$multi = false,$iter = 0) {
		global $outs;
		if($this->way === 'excel'){
			$this->worksheet = & $this->workbook->addWorksheet ( $title );
			$this->writeWorksheet(&$data,$headers,$keys);
		}elseif($this->way === 'plain'){
			global $prefix;
			reset($data);
			$oars=$outs=$nkeys=array();
			foreach ($zkeys as $kl => $zsk) {
				if(!in_array($zsk,$headers)){
					$outs[]=$kl;
					$oars[]=$zsk;
				}else{
					$nkeys[]=$zsk;
				}
			}
			$data=array_map('cleaner',$data);
			$outs=array();
			if($iter === 0){
				$this->putstr('$arr["'.$title.'"]=array("keys"=>\''.serialize($nkeys).'\',"data"=>array(');
			}else{
				$this->putstr(',');
			}
			$tcnt=count($data);
			$ind=0;
			foreach ($data  as $key=> $item){
				$pkey=$key+($iter*500);
				$this->putstr($pkey.'=>'.var_export($item,true).($ind + 1  === $tcnt ? '' : ','));
				$data[$key]=null;
				++$ind;
			}
			if($multi === false){
				$this->putstr('));');
				$data=null;
				unset($nkeys,$zkeys);
				$this->move();
			}
		}
		$data=null;
	}*/

	function putstr($txt){
		fprintf($this->fh,'%s',$txt) ;
	}

	function move(){
		fclose($this->fh);
		$xc=file_get_contents($this->dir.$this->file_name.'.t');
		fwrite($this->final, gzencode($xc,9,FORCE_GZIP));
		unset($xc);
		fwrite($this->final,'===###===');
		$this->fh=fopen($this->dir.$this->file_name.'.t',"w+");

	}

	function writeWorksheet( &$data,$headers,  $keys) {
		for($rowcount = 0, $hcnt = count ( $headers ); $rowcount < $hcnt; $rowcount ++) {
			$this->worksheet->write ( 0, $rowcount, $headers [$rowcount], $this->bold );
		}
		for($datacount = 0, $dcnt = count ( $data ); $datacount < $dcnt; $datacount ++) {
			$colcount = 0;
			foreach ( $keys as $key ) {
				$this->worksheet->write ( $datacount + 1, $colcount, $data [$datacount] [$key] );
				//echo $data[$datacount][$key];
				$colcount ++;
			}
		}
	}

	function close($keepFile = false,$printOut=true){
		global $baseDir;
		if($this->way == 'excel'){
			$this->workbook->close ();
		}else{
			global $baseDir;
			fclose($this->fh);
			@unlink($this->dir.$this->file_name.'.t');
			if($printOut === true){
				rewind($this->final);
				printForSave('','application/octet-stream',$this->file_name,false);
				rewind($this->final);
				while (!feof($this->final)){
					echo fread($this->final,2048);
				}
				fclose($this->final);
				if($keepFile === false){
					@unlink($this->dir.$this->file_name);
				}else{
					return $this->dir.$this->file_name;
				}
			}
		}
	}
}