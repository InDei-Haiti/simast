<?php
global  $baseDir;
require_once $baseDir.'/modules/outputs/result.func.php';

class genericTable
{
	private $tpl;
	private $nfei;
	private $header_types;
	private $body;

	private $tableRows= array();

	private $multy = array();

	private $decors = array();
	private $headerCount = 0;

	private $tmpvals;

    private $emptyTableText = 'No entries found';

	private $df;
	

	function __construct($isWide = false,$serverfiltre = false){
		global $baseDir, $moduleScripts,$AppUI;
		$moduleScripts[] = "./modules/outputs/outputs.module.js";
		if($serverfiltre)
			$this->tpl = new Templater($baseDir.'/style/default/generic_table_w_f.tpl');
		else
		    $this->tpl = new Templater($baseDir.'/style/default/generic_table.tpl');
		$this->nfei = new evolver();
		if($isWide !== true){
			$this->tpl->wide_start = '/*';
			$this->tpl->wide_end = '*/';
		}
		$this->df = $AppUI->getPref('SHDATEFORMAT');
	}

	function makeHeader($headers,$multies = false){
		$head_html = '';
		$col_html = '';
		$ind=0;
		$addcl = '';
		$this->headerCount = count($headers);
		foreach ($headers as $iname => $itype) {
			$head_html .= '<th style="width:'.(100/$this->headerCount).'%" id="head_' . $ind . '" data-thid="' . $ind . '" class="head ' . $addcl . ' header" ><center>' . $iname . '</center><div class="head_menu"></div></th>' . "\n";
			$col_html .= '<col id=col_' . $ind . '></col>';
			$addcl = 'forsize';
			++$ind;
		}
		$this->tpl->headers = $head_html;
		$this->tpl->colgroup = $col_html;
		$this->header_types = array_values($headers);
		$this->tpl->header_types = json_encode($this->header_types);
		if($multies !== false){
			$this->multy = $multies;
		}
	}

    function setEmptyText($text){
        $this->emptyTableText = $text;
    }

	function setDecorators($decs){
		$this->decors = $decs;
	}
	
	private function composeHTMLRow($rowValues,$color=''){
		global $df;
		$row = array('<tr id="row_'.$this->nfei->getCurrentRow() .'">');
		$position = 0;
		foreach ($rowValues as $column => &$value) {
			$cellContent = '&nbsp;';
			$addTitle = false;
			if(is_array($value))
				$value = join(", ",$value);
			if($column < $this->headerCount){
				$dval = $value;

				if(strlen($dval) > 30){
					$addTitle = $value;
					$value = substr($dval,0,30).'...';
				}

				if(isset($this->decors[$column])){
					if($this->decors[$column] === 'date'){
						$zdate = intval($value) ? new CDate($value) : null;
						if (isset($zdate)) {
							$cellContent = $zdate->format($this->df);
						}
					}else{
						$cellContent=$this->doDecors($this->decors[$column],$rowValues);
					}
				}else {
					$cellContent = $value;
				}
				/*if($position==0)
				    $row[] ='<td><input type="checkbox" class="itemcheck"/></td><td style="'.$color.';width:'.(100/$this->headerCount).'%"  '.($addTitle !== false ? ' class="moreview" data-text="'.$addTitle.'"' : '').'>'.$cellContent .'</td>'."\n\t";
				else */

				    $row[] ='<td style="'.$color.';width:'.(100/$this->headerCount).'%"  '.($addTitle !== false ? ' class="moreview" data-text="'.$addTitle.'"' : '').'>'.$cellContent .'</td>'."\n\t";

			}
            $position++;
		}
		$row[]='</tr>';
		$this->addTableHtmlRow(join("", $row));
	}

	private function doDecors($decStr,$vals){
		foreach($vals as $vid => $vv){			
			$decStr = str_replace('##'.$vid.'##',$vv,$decStr);
		}
		return $decStr;
	}

	function fillBody($row,$color=''){
		$pure = array();
		foreach ($row as $colid => $colv) {
			if($colid < $this->headerCount){
				//$colv = utf8_decode($colv);
				$colv = json_encode($colv);
				//$forStore = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $forStore);
				$colv = str_replace('"', '', $colv);
				$this->nfei->store($colv, (count($this->multy) > 0 && in_array($colid,$this->multy) ? 'multi' : false ), $this->header_types[$colid]);
			}
		}
		$this->composeHTMLRow($row,$color);
		
		$this->nfei->nextRow();
		
	}

	function setToolBar($code){
		$this->tpl->toolBar = $code;
	}

	function addTableHtmlRow($row){
		$this->tableRows[] = $row;
	}

	function setPageTitle($ttl){
		$this->tpl->pageTitle = $ttl;
	}

	function compile($returnText = false){
        if(count($this->tableRows) > 0){
            $this->tpl->tableBody = join("",$this->tableRows);
            $this->tpl->rows_data = json_encode($this->nfei->html() );
            $this->tpl->lects = json_encode($this->nfei->getLects());

        }else{
            $this->tpl->tableBody =  '<tr><td colspan="'.$this->headerCount.'">'.$this->emptyTableText.'</td></tr>';
        }
        //$this->tpl->tableBody =  '<tr><td colspan="'.$this->headerCount.'">'.$this->emptyTableText.'</td></tr>';
        if ($returnText === true) {
            return $this->tpl->output();
        } else {
            $this->tpl->output(true);
        }
	}
}
