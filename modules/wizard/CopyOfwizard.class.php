<?php
require_once($AppUI->getSystemClass("genericTable"));
class Wizard {

	private $mandatoryCode = '<div class="fcontrol"></div>';

	private $rowDataPrefix = '<li>';
	private $rowDataAppix = '</li>';
	private $rowDataComma = '';

	private $mode;
	public $formName;
	public $fields;
	private $digest = array();
	public $form_prefix = "wform_";
	public $formSubs;
	private $tableName;
	private $refTableName;
	private $tableID;
	private $formID;

	private $rowRef = array();
	private $icount = 0;
	private $jsActions = array();
	private $client_id;
	private $task_id;
	private $project_id;
	private $alltask;
	private $multiplicity;
	private $entryId;
	private $radioID = 0;
	private $textRowId = 0;
	private $valid;
	private $vrow = 0;
	private $forregistration;
	public $parent;
	public $parent_wizard;
	

	private $rels = array();
	public $registry;
	public $title;
	public $dberrors  = array();
	private $texts = array('numeric' => array(), 'positive' => array(), 'email' => array(), 'range' => array());

	/**
	 * Class construction for view/edit constructed form
	 * @param string $mode
	 * @param int $client_id
	 * @param int $rid
	 */
	function __construct($mode = 'edit', $task_id = 0, $client_id = 0, $rid = 0) {
		$this->mode = $mode;
		$this->task_id = $task_id;
		$this->client_id = $client_id;
		$this->entryId = (int)$rid;
	}

	function loadFormInfo($id) {
		$this->formID = $id;
		$sql = 'select title,fields,parent_id,digest,valid,subs,registry,task_id,multiplicity,forregistration from form_master where id="' . $this->formID . '" limit 1';
		$res = mysql_query($sql);
		if ($res) {
			$fdata = mysql_fetch_object($res);
			$this->formName = $fdata->title;
			$this->digest = explode(',', $fdata->digest);
			
			
			$this->parent = $fdata->parent_id;
			if($this->parent){
				$this->parent_wizard = new Wizard(null, null, null, null);;
			}
			$this->fields = unserialize(stripslashes(gzuncompress($fdata->fields)));
			$this->valid = $fdata->valid;
			$this->formSubs = $fdata->subs;
			$this->registry = (int)$fdata->registry;
			$this->multiplicity = $fdata->multiplicity;
			$this->forregistration = $fdata->forregistration;
			$this->title = $fdata->title;
			if($fdata->task_id)
				$this->task_id = (int)$fdata->task_id;
		}
		$this->tableName = $this->form_prefix . $id;
		$this->refTableName = $this->form_prefix.$this->parent;
		$this->tableID = $id;
	}
	
	function  loadchildForm($id){
		$sql = "select id from form_master where parent_id=".$id;
		$res = mysql_query($sql);
		$childformId = array();
		if ($res) {
			while($row = mysql_fetch_array($res)){
				$childformId[] = $row['id'];
			}
		}
		return $childformId;
	}
	
	function getDigest(){
		return $this->digest;
	}
		
	
	function getTableName(){
		return $this->tableName;
	}

	function getDefaultFields($client_id = 0, $dvals = array()) {
		if ($this->mode !== 'view') {
			/*$code = $this->rowDataPrefix.' Visit Date '.$this->rowDataComma
			//.drawDateCalendar('entry_date',printDate($dvals['entry_date']),false,'class="mandat"',false,10,false,'$j(this).trigger("focusout");')
			.'<input type="hidden" name="client_id" value="'.$this->client_id.'">'
			.'<input type="hidden" name="id" value="'.$this->entryId.'">'
			.$this->mandatoryCode
			.$this->rowDataAppix;
}else*/
			$code = '';
		}
		return $code;
	}

	function tableWrap() {
		$this->rowDataPrefix = '<tr##CLASS##><td align="left">';
		$this->rowDataComma = '</td><td ' . ($this->mode === 'view' ? " class='hilite' " : '') . ' align="left">';
		$this->rowDataAppix = '</td></tr>';
	}
	
	function addClassToRow($class){
		str_replace("##CLASS##", $class, $this->rowDataPrefix);
	}
	
	function outputField($index,$fld_id, $fld, $dvalue, $otm = false, $tabout = false, $prevCols = 1) {
		global $AppUI;
		
		$blist = '';
		$ftype = $fld['type'];
		if (isset($fld['otm']) && count($fld['subs']) > 0) {
			
			if($this->mode === 'view' || ($ftype != 'calculateText' && $ftype != 'calculateNumeric' && $ftype != 'calculateChoice' && $ftype != 'calculateChoiceMult'))
				$blist = str_replace('<td ', '<td colspan="2"', $this->rowDataPrefix) .
					'<strong>' . $fld['name'] . '</strong><br>' .
					'<hr width="500" size="1" align="left">' .
					$this->rowDataAppix;
			return $blist;
		} elseif ($otm === false) {
			//$blist=$this->rowDataPrefix.++$this->icount.'.'.$fld['name'].$this->rowDataComma;
			if($this->mode === 'view' || ($ftype != 'calculateText' && $ftype != 'calculateNumeric' && $ftype != 'calculateChoice' && $ftype != 'calculateChoiceMult'))
				$blist = $this->rowDataPrefix /*. $fld['vname']*/ . $fld['name'].''. ($tabout === false ? $this->rowDataComma : '</td>');
			//var_dump($this->rowDataPrefix);
		} else {
			$blist = '<td>';
		}
		$this->rowRef[$fld['vid']] = array($fld_id, $dvalue, $fld['type'], $this->vrow);

		if ($fld['type'] === 'note') {
			$blist = str_replace('<td ', '<td colspan="4"', $this->rowDataPrefix) .
				'<h3 style="margin-top:0px">' . $fld['name'] . '...</h3>' .
				$this->rowDataAppix;
			return $blist;
		}
		$fldClass = 'fcl_' . $this->vrow;
		$alist = $this->getValues($fld['type'], $fld['sysv'], false, false, $fld['other']);
		unset($alist['rels']);
		$code = '';
		
		if ($this->mode === 'edit' || $this->mode === 'add') {
			if (preg_match('/^select/', $ftype)) {
				$idel = null;
				$code = $this->buildSelectList($index,$alist, $fld, $fld_id, $dvalue, $fldClass);
			} else {
				if ($ftype === 'time' || $ftype === 'datetime' ) {
					$pftype = 'times';
				} else {
					$pftype = $ftype;
				}
				$obligate = $fld['mand'] === true ? 'mandat' : '';
				switch ($pftype) {
					case 'date':
						//$name,$value,$hidden=false,$tags='',$yearCase = false,$length=20,$hvalue=false,$extraEvent=''
						/*$code = DateCalendar('fld_' . $fld_id, printDate($dvalue), false, 'class=" ' . $fldClass . ' ' . $obligate . '"', false, 10, false,
							($obligate != '' ? '$j(this).trigger("focusout");' : ''), $fld['range']['start'], $fld['range']['end']);*/
						$code = drawDateCalendar('fld_' . $fld_id, printDate($dvalue), false, 'class=" ' . $fldClass . ' ' . $obligate . '"', false, 10, false,
								($obligate != '' ? '$j(this).trigger("focusout");' : ''), $fld['range']['start'], $fld['range']['end'])
								. '<input type="hidden" name="client_id" value="' . $this->client_id . '">'
										. '<input type="hidden" name="id" value="' . $this->entryId . '">';
						break;
					case 'entry_date':
						//$name,$value,$hidden=false,$tags='',$yearCase = false,$length=20,$hvalue=false,$extraEvent=''
						$code = drawDateCalendar('entry_date', printDate($dvalue), false, 'class=" ' . $fldClass . ' ' . $obligate . '"', false, 10, false,
							($obligate != '' ? '$j(this).trigger("focusout");' : ''), $fld['range']['start'], $fld['range']['end'])
							. '<input type="hidden" name="client_id" value="' . $this->client_id . '">'
							. '<input type="hidden" name="id" value="' . $this->entryId . '">';
							
						break;
					case 'times':
						$code = drawTimePicker('fld_' . $fld_id, $dvalue, ($ftype === 'datetime' ? true : false), $obligate);
						break;
					case 'radio':
						if ($tabout === false) {
							$code = arraySelectRadio($alist, 'fld_' . $fld_id, 'class="' . $fldClass . ' ' . $obligate . '" ', $dvalue);
						} else {
							$code = arraySelectRadioMultiCol($alist, 'fld_' . $fld_id, 'class="' . $fldClass . ' ' . $obligate . '" ', $dvalue, false, 1, false);
						}
						if ($obligate !== '')
							$blist = str_replace('</td><td', '</td><td class="radioMandat" ', $blist);
						++$this->radioID;
						break;
					case 'checkbox':
						if ($tabout === false) {
							$code = arraySelectCheckbox($alist, 'fld_' . $fld_id . '[]', 'class="' . $fldClass . ' ' . $obligate . '" ', $dvalue);
						} else {
							$code = arraySelectCheckboxMultiCol($alist, 'fld_' . $fld_id . '[]', 'class="' . $fldClass . ' ' . $obligate . '" ', $dvalue, false, 1, false);
						}
						break;
					case 'bigText':
						$code = '<textarea name="fld_' . $fld_id . '" cols=40 rows=5 class="text ' . $fldClass . ' ' . $obligate . '">' . nl2br(stripslashes($dvalue)) . '</textarea>';
						break;
					/*case 'current_user':
						$code = '<input class="text ' . $obligate . ' ' . $fldClass . ' " name="fld_' . $fld_id . '" id="trow_' . $this->textRowId++ . '" size="30" value="' . $AppUI->user_id . '" readonly="readonly">';
						break;
					case 'current_user_fix':
						$code = '<input class="text ' . $obligate . ' ' . $fldClass . ' " name="fld_' . $fld_id . '" id="trow_' . $this->textRowId++ . '" size="30" value="' . $AppUI->user_id . '"  readonly="readonly">';
						break;*/
					default:
						if ($pftype != 'plain') {
							if (is_array($fld['range'])) {
								$obligate .= "numeric inrange\" data-rng='" . ($fld['range']['start'] . '|' . $fld['range']['end']) . "' ";
							} else {
								$obligate .= ' strictz numeric';
							}
						}
						if($pftype != 'calculateText' && $pftype != 'calculateNumeric' && $pftype != 'calculateChoice' && $pftype != 'calculateChoiceMult')
							$code = '<input class="text ' . $obligate . ' ' . $fldClass . ' " name="fld_' . $fld_id . '" id="trow_' . $this->textRowId++ . '" size="30" value="' . $dvalue . '" >';
						break;
				}
			}
		} elseif ($this->mode === 'view') {
			$code = $this->printFieldValue($fld, $dvalue, $tabout);
		} elseif ($this->mode === 'print') {
			$ft = $fld['type'];
			$code = '<tr><td>' . $fld['vname'] . '&nbsp;</td><td>' . $fld['name'] . '&nbsp;&nbsp;';
			if (in_array($ft, array('SysCenters', 'SysStaff', 'SysClients', 'plain', 'numeric'))) {
				$code .= '...............................................';
			} else {
				if ($ft === 'date') {
					$code .= '. . . /. . . /. . . . . .';
				} elseif ($ft === 'bigText') {
					$code .= '<br><br><br>';
				} else {
					if ($tabout === false) {
						$arr = $this->getValues($ft, $fld['sysv'], false, true);
						$code .= join(" - ", $arr);
					} else {
						$pres = $this->getValues($ft, $fld['sysv'], false, true);
						$code = '<tr><td>' . $fld['vname'] . $fld['name'] . '</td><td>' . showValuesMultiCol(array(), count($pres) - 1, array_keys($pres));
						$code = preg_replace("/<td>$/", "", $code);
					}
				}
			}
			return $code . '</td></tr>';
		}

		if ($this->mode !== 'view') {
			
			if($ftype != 'calculateText' && $ftype != 'calculateNumeric' && $ftype != 'calculateChoice')
				$code .= ($fld['mand'] === true ? $this->mandatoryCode : '');

			if (is_array($fld['child']) && count($fld['child']) > 0) {
				$tableParentRow = $this->rowRef[$fld['child']['parent']];
				if ($this->mode === 'add' || ($this->mode === 'edit' && $tableParentRow[1] != $fld['child']['trigger'])) {
					if ($otm === false) {
						$this->jsActions[] = 'var tv = $j("#row_' . $fld_id . '").val(); if(tv === undefined ||  tv == "-1" || tv === ""){$j("#row_' . $fld_id . '").hide();}';

						$this->appendTag("#row_" . $tableParentRow[0] . ' :input', '#row_' . $fld_id, $fld['child']['trigger'], ($fld['chain'] === true ? $fld['sysv'] : false));
					} else { //if($this->registry === 1){
						$this->jsActions[] = '$j(".fcl_' . ($this->vrow) . '").attr("disabled",true);';

						$this->appendTag(".fcl_" . $tableParentRow[3], '.' . $fldClass, $fld['child']['trigger'], ($fld['chain'] === true ? $fld['sysv'] : false));
					}
				}
			}
		}
		++$this->vrow;
		if ($otm === false) {
			$blist .= $code . $this->rowDataAppix;
			$blist = str_replace("<tr>", "<tr id='row_" . $fld_id . "'>", $blist);
		} else {
			$blist .= $code . '</td>';
		}
		return $blist;
	}

	function appendTag($tRow, $selector, $tval, $dig) {
		$found = false;
		if (!isset($this->rels[$tRow])) {
			$this->rels[$tRow] = array();
		}
		foreach ($this->rels[$tRow] as &$tpart) {
			if ($tpart[1] == $tval) {
				$tpart[0] .= ', ' . $selector;
				$found = true;
			}
		}
		if ($found === false) {
			$this->rels[$tRow][] = array($selector, $tval, $dig);
		}
	}

	function preIndex() {
		return $this->vrow;
	}

	function postIndex($i) {
		$this->vrow = $i;
	}

	function formJSsupport() {
		if ($this->mode !== 'view') {
			if ($this->radioID > 0) {
				$this->jsActions[] = '$j(".radioMandat").find("input").click(function(e){$j(this).parent().find(".fcontrol").addClass("rowDone");})';
			}
			foreach ($this->texts as $type => $fields) {
				//if(count($fields) > 0){
				//$pre='$j("'.join(',',$fields).'").';
				$pre = '$j(".' . $type . '").live("mouseover",function(){$j(this).';
				switch ($type) {
					case 'numeric':
						$this->jsActions[] = $pre . 'liveStrict("format({autofix:true})");});';
						break;
					/*case 'range':
					$this->jsActions[]=$pre.'liveStrict("numeric()");});';
					break;
					case 'email':
					$this->jsActions[]=$pre.'liveStrict("format({type:\"email\"},function(){alert(\"Wrong Email format!\")})")});';
					break;
					case 'positive':
					$this->jsActions[]=$pre.'liveStrict("format({precision: 0,allow_negative:false,autofix:true})");});';
					break;*/
				}

				//}
			}
			$this->jsActions[] = 'frm.brels(' . json_encode($this->rels) . ');';
		}
		return join("\n", $this->jsActions);
	}

	function buildSelectList($index,$arr, $fld, $fld_id, $value, $xtraClass = '') {
		if ($fld['ftype'] === 'select-multi') {
			$vals = explode(',', $value);
			$addName = array('[]', 'multiple="multiple" size="3"');
		} else {
			$vals = array($value);
			//var_dump($vals);
			$addName = array('', '');
		}
		if ($fld['mand'] === true) {
			$addClass = ' mandat ';
		} else {
			$addClass = '';
		}
		$change = '';
		$datav = '';
		$section = false;
		$idel = '';
		$function = '';
		if($fld['sysv']==='SysCommunalSection'){
 			$idel = ' id="communalSection_'.$index.'"';
 		}
 		if($fld['sysv']==='SysCommunes'){
 			$idel = ' id="commune_'.$index.'"';
 			$function = ' onchange="populateSection('.'\'commune_'.$index.'\','.'\'communalSection_'.$index.'\');"';
 		}
 		if($fld['sysv']==='SysDepartment'){
 			$idel = ' id="department_'.$index.'"';
 			$function = ' onchange="populateCommune('.'\'department_'.$index.'\','.'\'commune_'.$index.'\');"';
 		}
		//$function = ' onchange="populateLocal('.'\'department_'.$index.'\','.'\'commune_'.$index.'\','.'\'communalSection_'.$index.'\');"';
		/*if($idelement){
			if($idelement==='communalSection'){
				$idelement = ' id="'.$idelement.'"';
				$section = true;
			}else if($idelement==='commune'){
				$idelement = ' id="'.$idelement.'"';
				//$change = ' onChange="loadSection(this.value)"';
			}else if($idelement==='department'){
				$idelement = ' id="'.$idelement.'"';
				//$change = ' onChange="loadSection(this.value)"';
			}else{
			
				$idelement = '';
			}
		}else{
				$idelement = '';
		}*/
		//if($fld['sysv']==='SysCommunalSection'){
		
		$psel = '<select name="fld_' . $fld_id . $addName[0] . '" class="text ' . $addClass . ' ' . $xtraClass . '" ' . ($addName[1]).$idel .$function. '>';
		foreach ($arr as $id => $ci) {
			$psel .= "<option value='" . $id . "' " . (in_array($id, $vals) ? 'selected="selected" ' : '') . ">" . $ci . "</option>\n";
		}
		$psel .= '</select>';
		if($section){
			$psel .= '<input type="hidden" id="'.$hiddenval.'" value="'.$value.'">';
		}
		
		return $psel;
	}
	
	public static function COUNT($query){
		$result = db_exec($query);
		return $result;
	}
	
	public function getValues($type, $psv = false, $pvalue = false, $nosubz = false, $withOther = false, $parentValue = false, $form = false,$fieldform = false) {
		$result = false;
		switch ($psv) {
			/* case 'SysClients':
				$q = new DBQuery();
				$q->addTable('clients');
				$q->addQuery('client_id as id, concat(client_first_name," ",client_last_name) as client_name');
				$result = arrayMerge(array(-1 => '- Select Client -'), $q->loadHashList());
				break;

			case 'SysCenters':
				$q = new DBQuery();
				$q->addTable('clinics', 'c');
				$q->addQuery('c.clinic_id as id, c.clinic_name as name');
				$q->addOrder('c.clinic_name');
				$result = arrayMerge(array(-1 => '- Select Center -'), $q->loadHashList());
				break; */

			case 'SysStaff':
				$q = new DBQuery;
				$q->addTable('contacts', 'con');
				$q->leftJoin('users', 'u', 'u.user_contact = con.contact_id');
				$q->addQuery('con.contact_id as id');
				$q->addQuery('CONCAT_WS(" ",contact_first_name,contact_last_name) as name');
				$q->addOrder('contact_last_name');
				$q->addWhere('contact_active="1"');
				if ($parentValue !== false && (int)$parentValue > 0) {
					$q->addTable('staff_position', 'sf');
					$q->addWhere('position_id="' . $parentValue . '"');
					$q->addWhere("sf.contact_id = con.contact_id");
				}
				$result = arrayMerge(array(-1 => '- Select Person -'), $q->loadHashList());
				/* if($this->mode==='view'){
					if($pvalue){
						
						$result =  $q->loadHashList();
					}
				} */
				break;
			case 'SysDepartment':
				$q = new DBQuery();
				
				
				$q->addTable("administration_dep");
				/*if ($parentValue !== false && (int)$parentValue > 0) {
					$q->addWhere('clinic_location_clinic_id = "' . (int)$parentValue . '"');
				}*/
				$q->addQuery("administration_dep_code, administration_dep_name");
				if($form && $fieldform){
					$q->addWhere('administration_dep_code in ( Select `'.$fieldform.'` from `'.$form.'`)');
				}
				$q->order_by = 'administration_dep_name';
				
				$result = arrayMerge(array(-1 => '- Select Department -'), $q->loadHashList());
				if($this->mode==='view'){
					if($pvalue){
						$q = new DBQuery();
						$q->addTable("administration_dep");
						$q->addQuery("administration_dep_code,administration_dep_name");
						$q->addWhere('administration_dep_code = "' . $pvalue . '"');
						$result =  $q->loadHashList();
					}
				}
				break;
			case 'SysCommunes':
				$q = new DBQuery();
				
				$q->addTable("administration_com");
				/*if ($parentValue !== false && (int)$parentValue > 0) {
					$q->addWhere('clinic_location_clinic_id = "' . (int)$parentValue . '"');
				}*/
				$q->addQuery("administration_com_code, administration_com_name");
				//echo 'SysCommunes: '.$form . $fieldform;
				if($form && $fieldform){
					$q->addWhere('administration_com_code in ( Select `'.$fieldform.'` from `'.$form.'`)');
				}
				$q->order_by = 'administration_com_name';
				$result = arrayMerge(array(-1 => '- Select Commun -'), $q->loadHashList());
				if($this->mode==='view'){
					if($pvalue){
						$q = new DBQuery();
						$q->addTable("administration_com");
						$q->addQuery("administration_com_code,administration_com_name");
						$q->addWhere('administration_com_code = "' . $pvalue . '"');
						$result =  $q->loadHashList();
					}
				}
				break;
			
			case 'SysCommunalSection':
				$q = new DBQuery();
				$q->addTable("administration_section");
				$q->addQuery("administration_section_code,administration_section_name");
				if($form && $fieldform){
					$q->addWhere('administration_section_code in ( Select `'.$fieldform.'` from `'.$form.'`)');
				}
				$result =  $q->loadHashList();
				$result = arrayMerge(array(-1 => '- Select Communal Section -'), $result);
				if($this->mode==='view'){
					if($pvalue){
						$q = new DBQuery();
						$q->addTable("administration_section");
						$q->addQuery("administration_section_code,administration_section_name");
						$q->addWhere('administration_section_code = "' . $pvalue . '"');
						$result =  $q->loadHashList();
					}
				}
				break;
			
			

			/* case 'SysPositions':
				$q = new DBQuery();
				$q->addTable('positions', 'c');
				$q->addQuery('id, title');
				$q->addOrder('title');
				$result = arrayMerge(array(-1 => '- Select Position -'), $q->loadHashList());
				break; */

			default:
				if (in_array($type, array('select', 'radio', 'checkbox')) && $psv != '') {
					$result = dPgetSysValSet($psv);
					if ($type === 'select') {
						$result = arrayMerge(array(-1 => '-- Select --'), $result);
					}

				}
				break;
		}
		
		if ($pvalue !== false) {
			$str = array();
			if (is_array($pvalue) && count($pvalue) > 0) {
				foreach ($pvalue as $pv) {
					$str[] = $result[$pv];
				}
			} else {
				$str[] = $result[$pvalue];
			}
			$result = join(",", $str);
		}
		if ($nosubz === true) {
			unset($result[-1]);
		}
		if ($withOther === true) {
			$result['other'] = "Other";
		}
		
		return $result;
	}

	function inFieldValueParse($key, $tf, $value) {
		if ($key === 'entry_date' || $tf['type'] === 'date') {
			$value = storeDate($value);
		} elseif ($tf['type'] === 'select-multi' || $tf['type'] === 'checkbox') {
			if (is_array($value)) {
				$value = join(',', $value);
			}
		}
		return mysql_real_escape_string($value);
	}
	
	function isExistingField($table,$fld,$value){
		$q = new DBQuery();
		$q->addTable($table);
		$q->addQuery('COUNT(*)');
		$q->addWhere($fld=$value);
		$res = $q->loadResult();
		if($res>0)
			return true;
		else 
			return false;
	}

	function saveFormData($post_array,$ref,$id = 0) {
		global $AppUI;
		$q = new DBQuery();
		$q->addTable($this->tableName);
		$action = '';
		/* echo '<pre>';
		var_dump($post_array);
		echo '</pre>'; */ 
		
		if(isset($post_array['ref'])){
			$ref = $post_array['ref']; 
		}
		//$pclient_id = (int)$_POST['client_id'];
		if ($id > 0) {
			$q->addWhere('id="' . (int)$id . '"');
			$action = 'Update';
			
			if(!isset($post_array['last_update_date']) || empty($post_array['last_update_date']))
				$q->{"add" . $action}('last_update_date', date("Y-m-d"));
			else
				$q->{"add" . $action}('last_update_date', $post_array['last_update_date']);
			
			
			if(!isset($post_array['user_last_update']) || empty($post_array['user_last_update']))
				$q->{"add" . $action}('user_last_update',  $AppUI->user_id);
			else
				$q->{"add" . $action}('user_last_update',  $post_array['user_last_update']);
			
		} else {
			//$q->addInsert('client_id', $pclient_id);
			$action = 'Insert';
			if($ref)
				$q->{"add" . $action}('ref', $ref);
			
			if(!isset($post_array['entry_date']) || empty($post_array['entry_date']))
				$q->{"add" . $action}('entry_date', date("Y-m-d"));
			else
				$q->{"add" . $action}('entry_date', $post_array['entry_date']);
			
			if(!isset($post_array['user_creator']) || empty($post_array['user_creator']))
				$q->{"add" . $action}('user_creator', $AppUI->user_id);
			else 
				$q->{"add" . $action}('user_creator', $post_array['user_creator']);
			
			if(!isset($post_array['last_update_date']) || empty($post_array['last_update_date']))
				$q->{"add" . $action}('last_update_date', date("Y-m-d"));
			else 
				$q->{"add" . $action}('last_update_date', $post_array['last_update_date']);
			
			if(!isset($post_array['user_last_update']) || empty($post_array['user_last_update']))
				$q->{"add" . $action}('user_last_update',  $AppUI->user_id);
			else 
				$q->{"add" . $action}('user_last_update',  $post_array['user_last_update']);
		}
		$afterSave = array();
		$lsubs = explode(',', $this->formSubs);
		/* if(!isset($post_array['entry_date']))
			$post_array['entry_date'] = ""; */
		
		/* foreach ($post_array as $key => $value) {
				
				
			if (preg_match("/_subs$/", $key) || preg_match('/^fld_$/', $key)) {
				$is_otm = 1;
				echo 'otm'.$key;
			} else {
				$is_otm = 0;
			}isExistingField($table,$fld,$value)
		}
		exit; */
		
		foreach ($post_array as $key => $value) {
			if (preg_match("/_subs$/", $key) || preg_match('/^fld_$/', $key)) {
				$is_otmc = 1;
			
			} else {
				$is_otmc = 0;
			}
			if ((strstr($key, 'fld_') && $is_otmc === 0)) {
				$tfc = $this->findFieldName($key);
				$value = trim($value);
				if($tfc['type']==='unique' && $action==='Insert'){
					if($this->isExistingField($this->tableName, $key, $value)){
						$this->dberrors[$key] = $action.$AppUI->_('Duplicate value for').' '.$AppUI->_($tfc['name']);
					}
				}
					
			}elseif ($is_otmc === 1) {
				
				$tfc = $this->findFieldName($key);
				
				foreach ($value as $rid => $mrow) {
					foreach ($mrow as $field => $svalue) {
						$stfc = $this->findFieldName($field, $tfc);
						$svalue = trim($svalue);
						echo '<pre>';
						var_dump($stfc);
						echo '</pre>';
						if($stfc['type']==='unique' && $action==='Insert'){
							if($this->isExistingField($lsubs[$tfc['dbsub']], $field, $svalue)){
								$this->dberrors[$key.'_'.$field] = $AppUI->_('Duplicate value for').' '.$AppUI->_($tfc['name'].'::'.$stfc['name']);
							}
						}
					}
				}
				
				/* echo '<pre>';
				var_dump($tfc);
				echo '</pre>';
				exit; */
			}
		}
		if(count($this->dberrors)>0){
			return false;
		}
		foreach ($post_array as $key => $value) {
			
			
			if (preg_match("/_subs$/", $key) || preg_match('/^fld_$/', $key)) {
				$is_otm = 1;
				
			} else {
				$is_otm = 0;
			}
			
			if ((strstr($key, 'fld_') && $is_otm === 0) /*|| $key === 'entry_date'*/) {
				$tf = $this->findFieldName($key);
				//if((strstr($key, 'fld_') && $is_otm === 0))
				$value = trim($value);
				/* if($key==='entry_date' && $action==='Insert'){
					if(!$value)
						$value = date("Y-m-d");
				}
				if($key==='current_user_fix' && $action==='Insert'){
					if(!$value)
						$value = date("Y-m-d");
				} */
				
				$value = $this->inFieldValueParse($key, $tf, $value);
				$q->{"add" . $action}($key, $value);
				/* if($action==='Insert')
					$q->{"add" . $action}($key, $value);
				if($action==='Update'){
					if($tf != 'current_user_fix')
						
				} */
				
			} elseif ($is_otm === 1) {
				//echo $key;
				$onetype = array();
				$values = array();
				$tf = $this->findFieldName($key, 1);
				if ($this->registry === 0) {
					if ($id > 0) {
						$sql = 'delete from ' . $lsubs[$tf['dbsub']] . ' where wf_id="' . $id . '"';
						$rdel = mysql_query($sql);
					}
					
					foreach ($value as $rid => $mrow) {
						foreach ($mrow as $field => $svalue) {
							if (!array_key_exists($field, $onetype)) {
								$stf = $this->findFieldName($field, $tf);
								$onetype[$field] = $stf;
							} else {
								$stf = $onetype[$field];
							}
							$values[$rid][] = $this->inFieldValueParse($field, $stf, $svalue);
						}
						$values[$rid][] = '#@WFID@#';
						//$values[$rid][] = $pclient_id;
						$values[$rid] = '("' . join('","', $values[$rid]) . '")';
					}
					$onetype['wf_id'] = '';
					//$onetype['client_id'] = '';

					$sql = 'insert into ' . $lsubs[$tf['dbsub']] . '(' . join(",", array_keys($onetype)) . ') VALUES ' .
						join(',', $values);
					unset($values);
					$afterSave[] = $sql;
					
				} elseif ($this->registry === 1) {
					//echo 'registry 1';
					foreach ($value as $rid => $mrow) {
						foreach ($mrow as $field => $svalue) {
							if (!array_key_exists($field, $onetype)) {
								$stf = $this->findFieldName($field, $tf);
								$onetype[$field] = $stf;
							} else {
								$stf = $onetype[$field];
							}
							if (!is_array($values[$rid])) {
								$value[$rid] = array();
							}
							$values[$rid][$field] = $this->inFieldValueParse($field, $stf, $svalue);
						}
					}
				}
			}
		}
		
		if (is_array($values) && count($values) > 0) {
			
			foreach ($values as $kv) {
				$q2 = clone $q;
				foreach ($kv as $key => $cval) {
					$q2->addInsert($key, $cval);
				}
				$sql = $q2->prepare();
				//echo $sql;
				//exit;
				$res = mysql_query($sql);
			}
		} else {
			$sql = $q->prepare();
			//echo $sql;
			//exit;
			$res = mysql_query($sql);
			if(!$id){
				if($res){
					$id = mysql_insert_id();
				}
			}
		}
		
		if (count($afterSave) > 0) {
			if ($id === 0) {
				$id = mysql_insert_id();
			}
			foreach ($afterSave as &$sob) {
				$sql = str_replace('#@WFID@#', $id, $sob);
				//echo $sql;
				//exit;
				$ires = mysql_query($sql);
			}
		}
		if($id){
			$res = $id;
		}
		return $res;
	}

	function findFieldName($dfld, $forceSub = false) {
		if ($forceSub !== false && $forceSub !== 1) {
			$useit =& $forceSub['subs'];
		} else {
			$useit = $this->fields;
		}
		//echo '<pre>';
		//var_dump($useit);
		//echo '<pre>';
		//echo '<br/><br/><br/>';
		foreach ($useit as &$sfl) {
			if (!isset($sfl['otm']) || $forceSub === 1) {
				if ($sfl['dbfld'] === $dfld) {
					return $sfl;
				}
			} elseif (isset($sfl['subs'])) {
				
				if (isset($sfl['otm']) && preg_match("/_subs$/", $dfld)) {
					if ($sfl['dbfld'] === $sfl['dbfld']) {
						//echo $dfld.' ';
						//var_dump($sfl);
						return $sfl;
					}
				}
				foreach ($sfl['subs'] as &$subfl) {
					//if($subfl['dbfld']==='entry_date')
						//echo $subfl['dbfld'];
					if ($subfl['dbfld'] === $dfld) {
						//echo $dfld.' ';
						//var_dump($subfl);
						return $subfl;
					}
				}
			}
		}
		return false;
	}

	function printFieldValue($fld, $val, $tabout = false) {
		$res = '';
		if ($fld['type'] === 'date' || $fld['type'] === 'entry_date') {
			$res = printDate($val);
		} elseif (in_array($fld['type'], array('select', 'radio', 'checkbox', 'centers', 'clients', 'staff'))) {
			//echo $val.' ';
			if ($fld['smult'] === true || $fld['type'] === 'checkbox') {
				$val = explode(',', $val);
			}
			if ($val >= 0) {
				
				if ($tabout === false) {
					$res = $this->getValues($fld['type'], $fld['sysv'], $val);
				} else {
					$pres = $val;//$this->getValues($fld['type'], $fld['sysv']);
					unset($pres['rels']);
					$res = showValuesMultiCol($val, count($pres), array_keys($pres));
				}
			} else {
				$res = '&nbsp;';
			}
		}elseif(in_array($fld['type'], array('current_user', 'current_user_fix'))){
			$q = new DBQuery();
			$q->addTable('users');
			$q->addQuery('CONCAT(c.contact_last_name," ",c.contact_first_name)');
			$q->addJoin('contacts', 'c', 'c.contact_id=user_contact');
			$q->addWhere('user_id='.$val);
			$res = $q->loadResult();
		} else {
			$res = nl2br(stripslashes($val));
		}
		return $res;
	}
	
	function getMultiplicity($id){
		$q = new DBQuery();
		$q->addTable('form_master');
		$q->addQuery("multiplicity");
		$q->addWhere("id=".$id);
		return $q->loadResult();
	}
	
	function countByClientId($formtable,$client_id2){
		$q = new DBQuery();
		$q->addTable($formtable);
		$q->addQuery("COUNT(id)");
		$q->addWhere("client_id=".$client_id2);
		return $q->loadResult();
	}
	
	function isRegisterForm($idfw){
		$q = new DBQuery();
		$q->addTable("form_master");
		$q->addQuery("forregistration");
		$q->addWhere("id=".$idfw);
		return $q->loadResult();
	}
	
	function drawDigest($idfw,$ref,$page,$epp=false,$view_table=true) {
		global $m,$a,$tab,$AppUI;
	    /* echo '<pre>';
		var_dump($_GET);
		echo '</pre>';
		exit; */
		global $project_id;
		$selected_columns = dPgetParam($_GET, 'selected_columns', array());
		$selected_columns = array_reverse($selected_columns);
		$link = '/index.php?m='.$m.'&a='.$a.'&project_id='.$project_id.'&tab='.$tab;
		//echo $project_id.$m.$a.$tab.'....';
		$filter = "";
		//var_dump($_GET[$this->tableName]);echo '<br/>';
		$filterJs = array();
		if(isset($_GET[$this->tableName])){
			foreach ($_GET[$this->tableName] as $fld => $valfld){
				$and = '';
				if($filter!==""){
					$and = " AND";
				}
				if(count($valfld['value'])>0 && $valfld['operator']){
					$filterJs[$fld]['operator'] = $valfld['operator'];
					$filterJs[$fld]['value'] = $valfld['value'];
					$filter .= $and;
					$ci = 0;
					if(count($valfld['value'])>1)
						$filter .= ' (';
					foreach ($valfld['value'] as $k=>$v){
						$ci += 1;
						$filter .= " ".$this->tableName.'.'.$fld.$valfld['operator']."'".$v."'";
						if($ci<count($valfld['value']))
							$filter .= " OR";
					}
					if(count($valfld['value'])>1 && $ci==count($valfld['value']))
						$filter .= ') ';
				}
			}
		}
		$pageSub = "";
		$pageURL = 'http';
		if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];//.$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"];//.$_SERVER["REQUEST_URI"];
		}
		
		$queryString = $_SERVER["REQUEST_URI"];
		//echo $queryString.strpos("?",$queryString);
		//if (strpos("?",$queryString) !== false) {
		$queryString = explode("?", $queryString);
		$pageURL .= $queryString[0]."?";
		$pageSub .= $queryString[0]."?";
		if(isset($queryString[1])){
			//if (strpos("&",$queryString[1]) !== false) {
			$params = explode("&", $queryString[1]);
			foreach ($params as $x => $param){
				//if (strpos("=",$queryString[1]) !== false) {
				$tabparam = explode("=", $param,2);
				if($tabparam[0]!='p'){
					if(isset($tabparam[1])){
						$pageURL .= "&".$tabparam[0].'='.$tabparam[1];
						$pageSub .= "&".$tabparam[0].'='.$tabparam[1];
					}
				}
				//}
			}
			//}
		}
		//}
		
		$search = '';
		$searchlike = '';
		if(isset($_GET['search']) && !empty($_GET['search'])){
			$search = $_GET['search'];
		}
			
		if(!$epp){
			$epp = 100;
		}
		$gt = new genericTable(false,true);
		$code = '';
		if (!$this->digest) {
			$this->digest = array();
		}
		$headers = array();
		if($search){
			$sqltabn = 'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = "crowdl5_simast" AND TABLE_NAME ="' .$this->tableName.'"';
			$r = mysql_query($sqltabn);
			while ($dr = mysql_fetch_assoc($r)) {
				if($searchlike){
					$searchlike .= ' OR ';
				}
				$column = $dr['COLUMN_NAME'];
				$searchlike .= $column. ' LIKE "%'.$search.'%"';
			}
		}
		//if(!$this->client_id)
		//$code .='<th>Beneficiery name</th><th>Identification</th>';
		$i = 0;
		$headers['*'] = 'string';
		$i = 1;
		/* if($this->isRegisterForm($this->parent)){
			/* $code .='<th>LastName</th>';
			 $code .='<th>FirstName</th>'; ///
			$headers['LastName'] = 'string';
			$headers['FirstName'] = 'string';
			$i += 2;
		}
		$headers['Date'] = 'date';
		$i += 1; */
		//$tname = $this->findFieldName('entry_date');
		//var_dump($tname);
		//$headers[$tname['name']] = 'date';
		//$i += 1;
		//$code .='<th>'.$this->findFieldName('entry_date').'</th>';
		
		$localflds = array();
		if(count($selected_columns)>0){
			
			foreach ($selected_columns as $dfld) {
				if ($dfld != '') {
					$tname = $this->findFieldName($dfld);
					//$code .= '<th>' . $tname['name'] . '</th>';
					if($tname){
						$localflds[$dfld] = $tname;
						$headers[$tname['name']] = 'string';
					}elseif($dfld==='entry_date'){
						$headers[$AppUI->_('Creation Date')] = 'date';
					}elseif($dfld==='last_update_date'){
						$headers[$AppUI->_('Last Update')] = 'date';
					}elseif($dfld==='user_creator'){
						$headers[$AppUI->_('Creation User')] = 'string';
					}elseif($dfld==='user_last_update'){
						$headers[$AppUI->_('Last User Update')] = 'date';
					}
					$i += 1;
				}
				
				
			}
		}else{
			$headers[$AppUI->_('Creation Date')] = 'date';
			$headers[$AppUI->_('Last Update')] = 'date';
			$headers[$AppUI->_('Creation User')] = 'string';
			$headers[$AppUI->_('Last User Update')] = 'string';
			$i += 4;
			
			foreach ($this->digest as $dfld) {
				if ($dfld != '') {
					$tname = $this->findFieldName($dfld);
					//$code .= '<th>' . $tname['name'] . '</th>';
					$localflds[$dfld] = $tname;
					$headers[$tname['name']] = 'string';
					$i += 1;
				}
			}	
		}
		
		//$code .= '<th></th>';
		//$code .= '</tr></thead><tbody>';
		//$gt->addTableHtmlRow($rh);
		$gt->makeHeader($headers);
		$dpicks = join(',', array_merge(array('id', 'entry_date', 'client_id'), $this->digest));
		$dpicks = preg_replace("/\,$/", "", $dpicks);
		//if($task)
		//$sql = 'SELECT * FROM ' . $this->tableName . ' WHERE task_id="' . $this->task_id . ' INNER JOIN clients c ON c.client_id='.$this->tableName.'.client_id  order by entry_date ASC';
		
		//$sql = 'SELECT ' . $this->tableName . '.*,CONCAT_WS(" ", c.client_last_name, c.client_first_name) AS client_name, c.client_cin AS identify,c.client_id FROM  ' . $this->tableName . ' LEFT JOIN `clients` AS c ON c.client_id=' . $this->tableName . '.client_id';
		if($this->client_id){
			//$sql = 'SELECT ' . $this->tableName . '.* FROM  ' . $this->tableName ." WHERE " . $this->tableName . ".client_id=".$this->client_id;
			//$sql .= " WHERE " . $this->tableName . ".client_id=".$this->client_id;
		}
			//else
			//$sql = 'select ' . $dpicks . ' from ' . $this->tableName . ' where client_id="' . $this->client_id . '" order by entry_date ASC limit 1';
		$isRegister = false;
		if($this->isRegisterForm($this->parent))
			$isRegister = true;
		
		$this->loadFormInfo($idfw);
		$fields = $this->showFieldsImport();
		
		/* echo '<pre>';
			var_dump($fields);
		echo '</pre>';  */
		$fieldsFilter = array();
		$fieldsFilter['entry_date'] = array("title"=>$AppUI->_('Creation date'),"type"=>'date');
		$fieldsFilter['last_update_date'] = array("title"=>$AppUI->_('Last Update'),"type"=>'date');
		$fieldsFilter['user_creator'] = array("title"=>$AppUI->_('Creation User'),"type"=>'date');
		$fieldsFilter['user_last_update'] = array("title"=>$AppUI->_('Last User Update'),"type"=>'date');
		foreach ($fields["notms"] as $nitem) {
			//$psv = $nitem["raw"]["type"];
			if(isset($nitem["raw"]["sysv"])){
				$fieldsFilter[$nitem["fld"]] = array("title"=>$nitem["title"],"type"=>$nitem["raw"]["type"],"sysv"=>$nitem["raw"]["sysv"]);
			}else{
				$fieldsFilter[$nitem["fld"]] = array("title"=>$nitem["title"],"type"=>$nitem["raw"]["type"],"sysv"=>$nitem["raw"]["sysv"]);
			}
		}
		//$rh = '<form id="filterform" name="filterform" action="'.$pageSub.'" method="get">';
		
		
		
		
		
		$trRows = array();
		foreach ($fieldsFilter as $index => $field){
			$alist = $this->getValues($field['type'], $field['sysv'], false, true,false,false,$this->tableName,$index);
			$select = "";
			$function = "";
			if($field['type']==='numeric'){
				$select = '<input id="'.$index.'_value" type="number" name="'.$this->tableName.'['.$index.'][value][]" value="'.$_GET[$this->tableName][$index]['value'].'"/> ';
				$function = '<select id="'.$index.'_operator" name="'.$this->tableName.'['.$index.'][operator]"><option value="=">'.$AppUI->__('equal to').'</option><option  value="<">'.$AppUI->__('less than').'</option><option value=">">'.$AppUI->__('more than').'</option></select>';
			}elseif ($field['type']==='date' || $field['type']==='entry_date'){
				$dateS = null;
				/* if(count($filterJs)>0)
					$dateS = $filterJs[$index]['value'][0];
				echo drawDateCalendar(''.'wform_'.$fuid.'['.$index.'][value][]',$dateS,false,'class="mandat"',false,10,false,'$j(this).trigger("focusout");');
				*/
				//$select = drawDateCalendar(''.'wform_'.$fuid.'['.$index.'][value][]',$_GET['wform_'.$fuid][$index]['value'],false,'class="mandat"',false,10,false,'$j(this).trigger("focusout");').'pouchon';		
				 
				$select = '<input type="text" class="classflddate" id="'.$index.'_value" name="'.'wform_'.$fuid.'['.$index.'][value][]" value="'.$_GET['wform_'.$fuid][$index]['value'].'"/> ';
				$function = '<select  id="'.$index.'_operator" name="'.'wform_'.$fuid.'['.$index.'][operator]"><option value="=">equal to</option><option  value="<="><=</option><option value=">=">>=</option></select>';
			}elseif(isset($field['sysv'])){
				$select = '<select id="'.$index.'_value" name="'.$this->tableName.'['.$index.'][value][]">';
				$select .= '<option value="---"></option>';
				foreach ($alist as $key => $val){
					if(trim($key)!=="" && trim($key)!=="rels"){
						$selected='';
						if($_GET[$this->tableName][$index]['value']==$key)
							$selected='selected=selected';
						$select .= '<option value="'.$key.'" '.$selected.'>'.$AppUI->__($alist[$key]).'</option>';
					}
				}
				$select .= '</select><a href="javascript:crAndRmSelectMultiple(\''.$index.'_value\')" style="text-decoration:none">&emsp;<img src="/images/icons/stock_new.png" width="10px" height="10px"></a>';
				$function = '<select id="'.$index.'_operator" name="'.$this->tableName.'['.$index.'][operator]"><option value="=">'.$AppUI->__('is').'</option><option value="<>">'.$AppUI->__('is not').'</option></select>';
			}elseif($field['type']==='plain'){
				$select = '<input id="'.$index.'_value" type="text" name="'.$this->tableName.'['.$index.'][value][]">';
				$function = '<select id="'.$index.'_operator" name="'.$this->tableName.'['.$index.'][operator]"><option value="=">'.$AppUI->__('is').'</option><option value="<>">'.$AppUI->__('is not').'</option></select>';
			}
			if($select && $function)
				$trRows[$index]=utf8_encode('<tr><td><span onclick="delRowFilter(this)" style="width: 16px;height: 16px;padding: 1px;cursor: pointer;font-weight: 800;float: left;background-color: #B0B0B0;margin: 2px;text-align: center;background: url(\'/modules/wizard/images/icns.png\') no-repeat;background-position: -18px 1px;">&nbsp;&nbsp;</span></td><td style="padding:3px;width:20%">'.$field["title"].'</td><td>'.$function.'</td><td style="padding:3px">'.$select.'</td></tr>');
			
			//var_dump($alist);echo '<br/>';
		}
		/* echo '<pre>';
		var_dump($trRows);
		echo '</pre>'; */
		if(isset($_GET['wform_'.$idfw])){
			$filcollapse = '';
			$filvis = 'display: block';
		}else{
			$filcollapse = 'collapsed';
			$filvis = 'display: none';
		}
		$rh = '<div align="left" style="background:white;margin-top:2px;padding: 10px">';
		$rh .= '<form id="filterform" name="filterform" action="'.$link.'" method="get">
				<fieldset id="filters" style="margin-left:-7px;" class="collapsible '.$filcollapse.' header_collapsible">
    	<legend onclick="if($(\'#filterstab\').is(\':hidden\')){$(\'#filterstab\').show();document.getElementById(\'filters\').classList.remove(\'collapsed\');}else{$(\'#filterstab\').hide();document.getElementById(\'filters\').classList.add(\'collapsed\');}" style="color: #08245b;"><b>Filtres</b></legend>
				<table id="filterstab" style="'.$filvis.';width:100%">';
		$rh .= '<tr><td colspan="4">
				&nbsp;Add filter: <select id="select_field" onchange="setTableFilter()">';
		$rh .= '<option></option>';
		$rh .= '<option value="entry_date">'.$AppUI->_('Creation Date').'</option>';
		$rh .= '<option value="last_update_date">'.$AppUI->_('Last Update').'</option>';
		$rh .= '<option value="user_creator">'.$AppUI->_('Creation User').'</option>';
		$rh .= '<option value="user_last_update">'.$AppUI->_('Last User Update').'</option>';
				foreach ($fieldsFilter as $index => $field){
					$rh .= '<option value="'.$index.'">'.$AppUI->_($field["title"]).'</option>';
				}
		$rh .= '</select></td></tr>';
		 
		
		$rh .= '</table></fieldset>';
		
		if(isset($_GET['selected_columns'])){
			$opcollapse = '';
			$opvis = 'display: block';
		}else{ 
			$opcollapse = 'collapsed';
			$opvis = 'display: none';
		}
		$rh .= '<fieldset id="options" style="margin-left: -7px;"
				class="collapsible '.$opcollapse.' header_collapsible">
				<legend
				onclick="if($(\'#optionstab\').is(\':hidden\')){
					$(\'#optionstab\').show();document.getElementById(\'options\').classList.remove(\'collapsed\');
                 }else{
		           $(\'#optionstab\').hide();document.getElementById(\'options\').classList.add(\'collapsed\');
                 }">Options</legend>
		                 <table id="optionstab" style="'.$opvis.'; width: 100%">
		                 <tbody>
		                 
						<tr>
							<td style="width:1%;margin: 0;padding: 0;border: 0;outline: 0;font-size: 100%;vertical-align: baseline;background: transparent;">
								<label for="available_columns">'.$AppUI->_('Available Columns').'</label>
								<br/>
								<select id="available_columns" multiple="multiple" size="10" style="min-width:150px">';
									
									if(count($selected_columns)>0){
										foreach ($fieldsFilter as $index => $field){
											if(!in_array($index,$selected_columns) && $field["type"] !== 'note')
												$rh .= '<option value="'.$index.'">'.$field["title"].'</option>';
										}
									}else{
										foreach ($fieldsFilter as $index => $field){
											if(!in_array($index,$this->digest) && $field["type"] !== 'note')
												$rh .= '<option value="'.$index.'">'.$field["title"].'</option>';
										}
									}
									
								
								$rh .= '</select>
							</td>
							<td style="width:1%;margin: 0;padding: 5px;border: 0;outline: 0;font-size: 100%;background: transparent;" align="center">
								<input type="button" value="→" onclick="addRemoveOption(\'available_columns\',\'selected_columns\')"/><br/><br/>
								<input type="button" value="←" onclick="addRemoveOption(\'selected_columns\',\'available_columns\')"/>
							</td>
							<td style="width:1%;margin: 0;padding: 0;border: 0;outline: 0;font-size: 100%;vertical-align: baseline;background: transparent;">
								<label for="selected_columns">'.$AppUI->_('Selected Columns').'</label>
								<br/>
								<select id="selected_columns" multiple="multiple" name="selected_columns[]" size="10" style="min-width:150px">';
									
									if(count($selected_columns)>0){
										foreach ($selected_columns as $index => $field){
											$rh .= '<option value="'.$field.'">'.$fieldsFilter[$field]["title"].'</option>';
										}
									}else{
										$rh .= '<option value="entry_date">'.$AppUI->_('Creation Date').'</option>';
										$rh .= '<option value="last_update_date">'.$AppUI->_('Last Update').'</option>';
										$rh .= '<option value="user_creator">'.$AppUI->_('Creation User').'</option>';
										$rh .= '<option value="user_last_update">'.$AppUI->_('Last User Update').'</option>';
										
										foreach ($this->digest as $index => $field){
											$rh .= '<option value="'.$field.'">'.$fieldsFilter[$field]["title"].'</option>';
										}	
									}
									
								
								$rh .= '</select>
							</td>
							<td style="margin: 0;padding: 5px;border: 0;outline: 0;font-size: 100%;background: transparent;">
								<input type="button" id="btns_up" value="↑" onclick="moveUpDown(\'selected_columns\',\'Up\')"><br/><br/>
								<input type="button" value="↓" onclick="moveUpDown(\'selected_columns\',\'Down\')">
							</td>
						</tr>
					</tbody>
			   </table>
		</fieldset>';
		
		$multiplicity = $this->getMultiplicity($idfw);
		//$countdata = $this->countByClientId($this->tableName,$this->client_id);
		//if($multiplicity=="One" && (int)$countdata==0)
		$wiz_ref = '';
		$user_fil = '';
		if($searchlike){
			if(isset($_GET['wiz_ref']))
				$wiz_ref = ' AND ref='.$_GET['wiz_ref'];
			if(isset($_GET['user']) && $_GET['user']==='me'){
				$user_fil = ' AND '.$this->tableName.'.user_creator='.$AppUI->user_id;
			}
			$count = mysql_query('SELECT count(*) as count FROM  ' . $this->tableName.' WHERE '.$searchlike.$wiz_ref.$user_fil);
		}elseif($filter){
			if(isset($_GET['wiz_ref']))
				$wiz_ref = ' AND ref='.$_GET['wiz_ref'];
			if(isset($_GET['user']) && $_GET['user']==='me'){
				$user_fil = ' AND '.$this->tableName.'.user_creator='.$AppUI->user_id;
			}
			$count = mysql_query('SELECT count(*) as count FROM  ' . $this->tableName.' WHERE '.$filter.$wiz_ref.$user_fil);
		}else{
			if(isset($_GET['wiz_ref'])){
				$wiz_ref = ' WHERE ref='.$_GET['wiz_ref'];
				if(isset($_GET['user']) && $_GET['user']==='me'){
					$wiz_ref .= ' AND '.$this->tableName.'.user_creator='.$AppUI->user_id;
				}
				
			}else{
				if(isset($_GET['user']) && $_GET['user']==='me'){
					$user_fil = ' WHERE '.$this->tableName.'.user_creator='.$AppUI->user_id;
				}
			}
			$count = mysql_query('SELECT count(*) as count FROM  ' . $this->tableName.$wiz_ref.$user_fil);
		}
		$count = mysql_fetch_assoc($count);
		$count = $count['count'];
		$start = $page * $epp - $epp;
		//$end = $start + $epp;
		$nbPages = ceil($count/$epp);
		
		
		$prev = "";
		$next = "";
		
	
		if($searchlike){
			$wiz_ref = '';
			$user_fil = '';
			if(isset($_GET['wiz_ref']))
				$wiz_ref = ' AND ref='.$_GET['wiz_ref'];
			if(isset($_GET['user']) && $_GET['user']==='me'){
				$user_fil = ' AND '.$this->tableName.'.user_creator='.$AppUI->user_id;
			}
			$sql = 'SELECT ' . $this->tableName . '.* FROM  ' . $this->tableName.' WHERE '.$searchlike.$wiz_ref.$user_fil.' ORDER BY ' . $this->tableName.'.entry_date DESC'.'  limit '.$start.','.$epp;
			/* if($ref){
				//$join = " LEFT JOIN ".$this->refTableName." ON ".$this->refTableName.".id=".$ref;
				//$sql = 'SELECT ' . $this->tableName . '.*, '.$this->refTableName.'.*  AS REF FROM  ' . $this->tableName.$join.' WHERE ref='.$ref;
				$sql = 'SELECT ' . $this->tableName . '.* FROM  ' . $this->tableName.' WHERE ref='.$ref.' AND '.$searchlike.' ORDER BY ' . $this->tableName.'.entry_date DESC'.' limit '.$start.','.$epp;
			} */
		}elseif($filter){
			$wiz_ref = '';
			$user_fil = '';
			if(isset($_GET['wiz_ref']))
				$wiz_ref = ' AND ref='.$_GET['wiz_ref'];
			if(isset($_GET['user']) && $_GET['user']==='me'){
				$user_fil = ' AND '.$this->tableName.'.user_creator='.$AppUI->user_id;
			}
			$sql = 'SELECT ' . $this->tableName . '.* FROM  ' . $this->tableName.' WHERE '.$filter.$wiz_ref.$user_fil.' ORDER BY ' . $this->tableName.'.entry_date DESC'.'  limit '.$start.','.$epp;
		}else{
			$wiz_ref = '';
			$user_fil = '';
			if(isset($_GET['wiz_ref'])){
				$wiz_ref = ' WHERE ref='.$_GET['wiz_ref'];
				if(isset($_GET['user']) && $_GET['user']==='me'){
					$wiz_ref .= ' AND '.$this->tableName.'.user_creator='.$AppUI->user_id;
				}
				
			}else{
				if(isset($_GET['user']) && $_GET['user']==='me'){
					$user_fil = ' WHERE '.$this->tableName.'.user_creator='.$AppUI->user_id;
				}
			}
			
			
			$sql = 'SELECT ' . $this->tableName . '.* FROM  ' . $this->tableName.$wiz_ref.$user_fil.' ORDER BY ' . $this->tableName.'.entry_date DESC'.' limit '.$start.','.$epp;
			/*if($ref){
				//$join = " LEFT JOIN ".$this->refTableName." ON ".$this->refTableName.".id=".$ref;
				//$sql = 'SELECT ' . $this->tableName . '.*, '.$this->refTableName.'.*  AS REF FROM  ' . $this->tableName.$join.' WHERE ref='.$ref;
				$sql = 'SELECT ' . $this->tableName . '.* FROM  ' . $this->tableName.' WHERE ref='.$ref.' ORDER BY DESC ' .$this->tableName.'.entry_date'.' limit '.$start.','.$epp;
			}*/
		}
		$res = mysql_query($sql);
		if($page==1){
			$prev = '<h3 style="color:#c0c0c0">Prev</h3>';
			if($page<$nbPages)
				$next = '<a href="'.$pageURL."&p=".($page + 1).'"><h3>Next</h3></a>';
			else 
				$next = '<h3 style="color:#c0c0c0">Next</h3>';
		}elseif($page>1){
			//$pageURL .= "&p=".($page - 1);
			$prev = '<a href="'.$pageURL."&p=".($page - 1).'"><h3>Prev</h3></a>';
			if($page<$nbPages)
				$next = '<a href="'.$pageURL."&p=".($page + 1).'"><h3>Next</h3></a>';
			else
				$next = '<h3 style="color:#c0c0c0">Next</h3>';
		}
		if($res)
			$currentNbr = $start + mysql_num_rows($res);
		
		if($this->forregistration){
			$rh .= '<input type="button" value="'.$AppUI->_('Import').'" onClick="dialogNewClient('.$idfw.');">&emsp;&emsp;';
			$rh .= '<input type="button" value="'.$AppUI->_('Add New').'" onClick="window.location=\'./index.php?m=wizard&a=form_use&fid=' . $this->tableID . '&todo=addedit\'">';
		}else if($m !='tasks' && $ref){
			if($this->multiplicity==='One'){
				$q = new DBQuery();
				$q->addTable($this->form_prefix.$this->tableID);
				$q->addQuery('COUNT(*)');
				$q->addWhere('ref='.$ref);
				$result = $q->loadResult();
				if($result==0)
					$rh .= '<input type="button" class="button" value="'.$AppUI->_('Add New').'" onClick="window.location=\'./index.php?m=wizard&a=form_use&fid=' . $this->tableID . '&todo=addedit'.$idIns.'\'">';
			}elseif($this->multiplicity==='Many'){
				/* $ref = '&ref='.$ref;
				$rh .= '<input type="button" class="button" value="'.$AppUI->_('Add New').'" onClick="window.location=\'./index.php?m=wizard&a=form_use'.$ref.'&fid=' . $this->tableID . '&todo=addedit'.$parent_id.$idIns.'\'">';
			
			 */
				$rh .= '<input type="button" class="button" value="'.$AppUI->_('Add New').'" onClick="window.location=\'./index.php?m=wizard&a=form_use&fid=' . $this->tableID . '&todo=addedit&idIns='.$ref.'\'">';
				
			}else{
				$rh .= '<input type="button" class="button" value="'.$AppUI->_('Add New').'" onClick="window.location=\'./index.php?m=wizard&a=form_use&fid=' . $this->tableID . '&todo=addedit&idIns='.$ref.'\'">';
				
			}
		}else if(!$this->parent){
			if($this->multiplicity==='One'){
					$rh .= '<input type="button" class="button" value="'.$AppUI->_('Add New').'" onClick="window.location=\'./index.php?m=wizard&a=form_use&fid=' . $this->tableID . '&todo=addedit'.$idIns.'\'">';
				
			}else{
				$rh .= '<input type="button" class="button" value="'.$AppUI->_('Add New Entry').'" onClick="window.location=\'./index.php?m=wizard&a=form_use&fid=' . $this->tableID . '&todo=addedit\'">';
			}
		}else{
			$rh .= '<input type="button" class="button" value="'.$AppUI->_('Add New').'" onClick="window.location=\'./index.php?m=wizard&a=form_use&fid=' . $this->tableID . '&todo=addedit'.$ref.'\'">';
		}
		$formdata = 'joinList(serialize(document.filterform))';
		$searchscpt = 'window.location.href = window.location.href+\'&\'+'.$formdata;
		$rh .= '<input id="search" type="hidden" name="search" style="margin-left:10px;" placeholder="'.$AppUI->_('Search').'"/>
            	<input type="button" value="'.$AppUI->_('Clear').'" onClick="window.location=\''.$link.'\'"/>
                <input type="button" value="'.$AppUI->_('Apply').'" id="submitButton" onclick="'.$searchscpt.'"/>
                		';
		$rh .= '</form>';
		$selected1 = "";
		$selected2 = "";
		$selected3 = "";
		$selected4 = "";
		if($epp===100){
			$selected1 = " selected=selected";
		}
		if($epp===250){
			$selected2 = " selected=selected";
		}
		if($epp===500){
			$selected3 = " selected=selected";
		}
		/* if($epp===2000){
			$selected4 = " selected=selected";
		} */
		if($count>$start+$epp){
			$val = $start+$epp;
		}else{
			$val = $count;
		}
		$info = "Total Records: ".$count." Viewing Records: ".($start+1)."-".$val." <span style='color: #08245b;'><b>Rows per page</b></span> <select name='epp' onchange='window.location.href = window.location.href+\"&epp=\"+this.value'><option ".$selected1.">100</option><option ".$selected2.">250</option><option ".$selected3.">500</option></select>";
		/* 
		$info .= '<div class="cleanbox" style="margin-right: 15px; display: block;">
			        <span class="fmonitor fmbox" style="background-position: 0px 0px;"></span>
			        <input type="button" class="button fclean" onclick="cleanAllF();" disabled="" value="Clear Filters">
    			 </div>'; */
		$rhp = '<span><table style="margin-top:-20px;margin-left:-3px;"><tr><td>'.$prev.'</td><td><!-- <div style="margin-top:35px;">{<b style="color:#008000">'.$currentNbr.'/'.$count.'</b>}</div>--></td><td>'.$next.'</td><td><div style="margin-top:35px;">'.$info.'</div></td></tr></table></span>';
		//$rh .= $rhp;
		$rh .= '</div>';
		$rf = '<div align="left" style="background:white;margin-top:2px;padding: 10px">';
		$rf .= $rhp;
		$rf .= '</div>';
		$rh .= '</form>';
		/*
		 <div class="sib asci"></div>
                <span class="fhref" onclick="gpgr.ifsort('desc');">Sort Asc</span>
          */
		if($view_table)
			echo $rh;
		//'<a href="?m=wizard&a=form_use&fid='.$idfw.'&idIns='.$drow["id"].'&todo=view&teaser=1&rtable=1&tab=0">View</a>
		//echo ($i + 1);
		$decs = array(//0=>'<a href="/index.php?m=clients&a=view&client_id=##4##">##0##</a>'//,1=>'date',2=>'date'
				0 => '<a href="?m=wizard&a=form_use&fid='.$idfw.'&idIns=##'.($i).'##&todo=view&teaser=1&rtable=1&tab=0">View</a>'
		);
		/* var_dump($headers);
		 echo '<br/><br/><br/><br/><br/>'; */
		$gt->setDecorators($decs);

		
		$first = false;
		$parent_id = "";
		$idIns = "";
		
		//echo $currentNbr;
		if($this->parent)
			$parent_id = '&parent_id='.$this->parent;
		if($ref)
			$idIns = '&idIns='.$ref;
		$CR = "\n";
		if ($res && mysql_num_rows($res) > 0) {
			$nrows = mysql_num_rows($res);
			if($view_table===true){
				//$code = '<table width="100%" cellspacing="1" cellpadding="2" border="0" class="tbl">';
		
				//$code .= '<thead>';
				if(count($selected_columns)>0){
					if($this->isRegisterForm($this->parent)){
						$countheader = count($selected_columns)+4;
					}else{
						$countheader = count($selected_columns)+2;
					}
				}else{
					if($this->isRegisterForm($this->parent)){
						$countheader = count($this->digest)+4;
					}else{
						$countheader = count($this->digest)+2;
					}	
				}
				
				//if ($this->valid == 1) {
				//if($this->client_id){
		
				//$rh .= '<tr><td colspan="'.$count.'" align="right" valign="top" style="background-color:#ffffff">';
				
				//$code .= $hr;
				/* if($m!='tasks')
				 $code .= '<input type="button" class="button" value="Add New" onClick="window.location=\'./index.php?m=wizard&a=form_use&fid=' . $this->tableID . '&todo=addedit'.$parent_id.$idIns.'\'">';
				 else echo  */
				//$code .= '<input type="button" class="button" value="add new visit" onClick="window.location=\'./index.php?m=wizard&a=form_use&fid=' . $this->tableID . '&todo=addedit&client_id=' . $this->client_id . '\'">';
				//elseif($multiplicity=="Many")
				//$code .= '<input type="button" class="button" value="add new visit" onClick="window.location=\'./index.php?m=wizard&a=form_use&fid=' . $this->tableID . '&todo=addedit\'">';
				//$code .= '<input type="button" class="button" value="add new visit" onClick="window.location=\'./index.php?m=wizard&a=form_use&fid=' . $this->tableID . '&todo=addedit&client_id=' . $this->client_id . '\'">';
					
				//$rh .= '</td></tr>';
				//}
				//}
				//$code .= '<tr>';
		
		
		
		
		
				
				while ($drow = mysql_fetch_assoc($res)) {
					$row_data = array();
					//$code .= '<tr>';
					/*if(!$this->client_id)
					 $code .= '<td><a href="?m=clients&a=view&client_id=' . $drow['client_id'] . '">' . $drow['client_name'] . '</a></td>
					<td>' . $drow['identify'] . '</td>';*/
					$row_data[] = '';
					/* if($this->isRegisterForm($this->parent)){
						$sql = 'SELECT fld_0,fld_1 FROM  ' .  $this->refTableName.' WHERE id='.$drow['ref'];
						$res1 = mysql_query($sql);
						while ($drow1 = mysql_fetch_assoc($res1)) {
							$row_data[] = $drow1['fld_0'];
							$row_data[] =$drow1['fld_1'];
							//$code .= '<td>' . $drow1['fld_0']  .'</td>';
							//$code .= '<td>' . $drow1['fld_1']  .'</td>';
						}
					} */
					//$code .= '<td>' . printDate($drow['entry_date']) . '</td>';
					//$row_data[] = printDate($drow['entry_date']);
					if(count($selected_columns)>0){
						foreach ($selected_columns as $dfld) {
							if ($dfld != '') {
								//if(strlen($pvalue)<6)
								//echo $this->tableName;
								//var_dump($localflds[$dfld]);
								//echo $localflds[$dfld]["sysv"];
								if(isset($localflds[$dfld]["sysv"]) && $localflds[$dfld]["sysv"]==="SysDepartment"){
									if(strlen($drow[$dfld])<2){
										$drow[$dfld] = '0'.$drow[$dfld];
										$sqlup = 'UPDATE `'.$this->tableName.'` SET `'.$dfld.'`="'.$drow[$dfld].'" WHERE id='.$drow['id'];
										db_exec($sqlup);
									}
								}
									
								if(isset($localflds[$dfld]["sysv"]) && $localflds[$dfld]["sysv"]==="SysCommunes"){
									if(strlen($drow[$dfld])<6){
										$drow[$dfld] = '0'.$drow[$dfld];
										$sqlup = 'UPDATE `'.$this->tableName.'` SET `'.$dfld.'`="'.$drow[$dfld].'" WHERE id='.$drow['id'];
										db_exec($sqlup);
									}
								}
						
								if(isset($localflds[$dfld]["sysv"]) && $localflds[$dfld]["sysv"]==="SysCommunalSection"){
									if(strlen($drow[$dfld])<8){
										$drow[$dfld] = '0'.$drow[$dfld];
										$sqlup = 'UPDATE `'.$this->tableName.'` SET `'.$dfld.'`="'.$drow[$dfld].'" WHERE id='.$drow['id'];
										db_exec($sqlup);
									}
								}
									
									
								//echo $localflds[$dfld]["sysv"];
								$valt = $this->printFieldValue($localflds[$dfld], $drow[$dfld]);
								/* if(isset($localflds[$dfld]["sysv"]) && $localflds[$dfld]["sysv"]==="SysDepartment"){
								 $valt .= ' '.$drow[$dfld];
								 }
										
								 if(isset($localflds[$dfld]["sysv"]) && $localflds[$dfld]["sysv"]==="SysCommunes"){
								 $valt .= ' '.$drow[$dfld];
								} */
									
								$row_data[] = $valt;
								//$code .= '<td>' . $this->printFieldValue($localflds[$dfld], $drow[$dfld]) . '</td>';
							}
						}
					}else{
						
						
						
						$row_data[] = $drow['entry_date'];
						$row_data[] = $drow['last_update_date'];
						
						$resp = '';
						if($drow['user_creator']){
							$q = new DBQuery();
							$q->addTable('users');
							$q->addQuery('CONCAT(c.contact_last_name," ",c.contact_first_name)');
							$q->addJoin('contacts', 'c', 'c.contact_id=user_contact');
							$q->addWhere('user_id='.$drow['user_creator']);
							$resp = $q->loadResult();
						}
						
						$row_data[] = $resp;
						
						
						$resp = '';
						if($drow['user_last_update']){
							$q = new DBQuery();
							$q->addTable('users');
							$q->addQuery('CONCAT(c.contact_last_name," ",c.contact_first_name)');
							$q->addJoin('contacts', 'c', 'c.contact_id=user_contact');
							$q->addWhere('user_id='.$drow['user_last_update']);
							$resp = $q->loadResult();
						}
						$row_data[] = $resp;
						foreach ($this->digest as $dfld) {
							if ($dfld != '') {
								//if(strlen($pvalue)<6)
								//echo $this->tableName;
								//var_dump($localflds[$dfld]);
								//echo $localflds[$dfld]["sysv"];
								if(isset($localflds[$dfld]["sysv"]) && $localflds[$dfld]["sysv"]==="SysDepartment"){
									if(strlen($drow[$dfld])<2){
										$drow[$dfld] = '0'.$drow[$dfld];
										$sqlup = 'UPDATE `'.$this->tableName.'` SET `'.$dfld.'`="'.$drow[$dfld].'" WHERE id='.$drow['id'];
										db_exec($sqlup);
									}
								}
									
								if(isset($localflds[$dfld]["sysv"]) && $localflds[$dfld]["sysv"]==="SysCommunes"){
									if(strlen($drow[$dfld])<6){
										$drow[$dfld] = '0'.$drow[$dfld];
										$sqlup = 'UPDATE `'.$this->tableName.'` SET `'.$dfld.'`="'.$drow[$dfld].'" WHERE id='.$drow['id'];
										db_exec($sqlup);
									}
								}
						
								if(isset($localflds[$dfld]["sysv"]) && $localflds[$dfld]["sysv"]==="SysCommunalSection"){
									if(strlen($drow[$dfld])<8){
										$drow[$dfld] = '0'.$drow[$dfld];
										$sqlup = 'UPDATE `'.$this->tableName.'` SET `'.$dfld.'`="'.$drow[$dfld].'" WHERE id='.$drow['id'];
										db_exec($sqlup);
									}
								}
									
									
								//echo $localflds[$dfld]["sysv"];
								$valt = $this->printFieldValue($localflds[$dfld], $drow[$dfld]);
								/* if(isset($localflds[$dfld]["sysv"]) && $localflds[$dfld]["sysv"]==="SysDepartment"){
								 $valt .= ' '.$drow[$dfld];
								 }
										
								 if(isset($localflds[$dfld]["sysv"]) && $localflds[$dfld]["sysv"]==="SysCommunes"){
								 $valt .= ' '.$drow[$dfld];
								} */
									
								$row_data[] = $valt;
								//$code .= '<td>' . $this->printFieldValue($localflds[$dfld], $drow[$dfld]) . '</td>';
							}
						}	
					}
					
					$row_data[] = $drow['id'];
					//var_dump($row_data);echo '<br/>';
					/* $code .= '<td class="alt" style="width: 200px"><a href="?m=wizard&a=form_use&fid='.$idfw.'&idIns='.$drow["id"].'&todo=view&teaser=1&rtable=1&tab=0">View</a>
					 </td>';
					$code .= '</tr>'; */
					if ($first === false) {
						$first = $drow['id'];
					}
					/* var_dump($row_data);
					 echo '<br/><br/><br/>'; */
					$gt->fillBody($row_data);
				}
				//echo count($headers);
				//$gt->addTableHtmlRow('<tr><td colspan="'.count($headers).'">fsdfsdf</td></tr>');
				//$gt->addTableHtmlRow($CR . '<tr><td colspan="14">' . 'No clients available'. '</td></tr>');
				//$code .= '</tbody></table>';
				//$this->tpl->tableBody =  '<tr><td colspan="'.$this->headerCount.'">'.$this->emptyTableText.'</td></tr>';
				
			}else{
				$first = true;
			}
		} else {
			/* $code = 'No data available';
			 $nrows = 0;
			 if($this->forregistration){
			 $code .= '<tr><td colspan="4" align="right" valign="top" style="background-color:#ffffff">';
			 $code .= '<input type="button" class="button" value="Import" onClick="dialogNewClient('.$idfw.');">&emsp;&emsp;';
			 $code .= '<input type="button" class="button" value="Add New" onClick="window.location=\'./index.php?m=wizard&a=form_use&fid=' . $this->tableID . '&todo=addedit'.$parent_id.$idIns.'\'">';
			 $code .= '</td></tr>';
			 }else if($m!='tasks'){
			 $code .= '<tr><td colspan="4" align="right" valign="top" style="background-color:#ffffff">';
			 $code .= '<input type="button" class="button" value="add new visit" onClick="window.location=\'./index.php?m=wizard&a=form_use&fid=' . $this->tableID . '&todo=addedit'.$parent_id.$idIns.'\'">';
			 $code .= '</td></tr>';
			 }else if(!$this->parent){
			 $code .= '<tr><td colspan="4" align="right" valign="top" style="background-color:#ffffff">';
			 $code .= '<input type="button" class="button" value="Add New Entry" onClick="window.location=\'./index.php?m=wizard&a=form_use&fid=' . $this->tableID . '&todo=addedit\'">';
			 $code .= '</td></tr>';
			 } */
			/* if (!$isRegister) {
			 //if($this->client_id){
			 $code .= '<tr><td colspan="4" align="right" valign="top" style="background-color:#ffffff">';
			 $code .= '<input type="button" class="button" value="add new visit" onClick="window.location=\'./index.php?m=wizard&task_id='.$this->task_id.'&a=form_use&fid=' . $this->tableID . '&todo=addedit\'">';
			 $code .= '</td></tr>';
			 //}
			 } */
		}
		echo "\n";
		//if (count($rows) == 0) {
			//$gt->addTableHtmlRow($CR . '<tr><td colspan="14">No clients available</td></tr>');
		//}
		//$gt->addTableHtmlRow('<tr><td colspan="'.count($headers).'">fsdfsdf</td></tr>');
		$code .= $gt->compile(true);
		//$code .= $rf;
		

		return array($code, $nrows, $first, $rf, $count,$currentNbr,$trRows,$filterJs,$rhp);
	}
	
	
	public function showFieldsImport() {
		$result = array('otms' => array(), 'notms' => array());
		/* echo '<pre>';
			var_dump($this->fields);
		echo '</pre>'; */
		//var_dump($this->fields);
		foreach ($this->fields as $index => $fi) {
			if (is_array($fi['subs']) && count($fi['subs']) > 0) {
				if ($fi['otm'] === true){
					$result['otms']['wf_'.$this->formID.'_sub_'.$fi['pid']]['name'] = $fi['name'];
				    $result['otms']['wf_'.$this->formID.'_sub_'.$fi['pid']]['dbfld'] = $fi['dbfld'];
					foreach ($fi['subs'] as $fisub) {
						if(!isset($fisub['sysv']))
							$fisub['sysv'] = null;
						$result['otms']['wf_'.$this->formID.'_sub_'.$fi['pid']]['fields'][] = array('title' => $fisub['name'],'type' => $fisub['type'],'sysv' => $fisub['sysv'],'other' => $fisub["other"], 'fld' => $fisub['dbfld']);
					}
				}else{
					foreach ($fi['subs'] as $fisub) {
						$result['notms'][] = array('title' => $fisub['name'], 'fld' => $fisub['dbfld'], 'raw' => $fisub,'section' => $index);
					}
				}
			} else {
				$result['notms'][] = array('title' => $fi['name'], 'fld' => $fi['dbfld'], 'raw' => $fi);
			}
		}
		return $result;
	}
	

}

function importForm() {
	global $wres, $newd;
	$fpath = $_FILES['frfile']['tmp_name'];
	$res = 'fail';
	if (is_uploaded_file($fpath)) {
		$newQuery = file_get_contents($fpath);
		if (strlen($newQuery) > 0) {
			$newin = @unserialize(@gzuncompress(@stripslashes(@base64_decode($newQuery))));
			$newsets = $newin['sets'];
			$newForm = $newin['form'];
		}
	}
	/*elseif(isset($_SESSION['form_delay_store']) && $_SESSION['form_delay_store'] != ''){
		$tform = $_SESSION['form_delay_store'];
		$newForm = unserialize(tmpFileRead($tform),true);
		unset($_SESSION['form_delay_store']);
		}*/
	if (isset($newsets) && count($newsets) > 0) {
		$sres = importSets($newsets);
		if ($sres['result'] === 'partial') {
			if (count($sres['multi']) > 0) {
				$_SESSION['form_delay_store'] = tmpFileStore(serialize($newForm));
				//$_SESSION['sets_details'] = serialize($sres);
				$sres['form_case'] = true;
				$res = json_encode(array("withsets" => true, "sinfo" => $sres));
			}
		} elseif ($sres['result'] === true) {
			// Update sysvals' ID so they have to be valid in new place
			$fds = & $newForm['fileds'];
			if (count($fds) > 0 && count($sres['multi']) > 0) {
				foreach ($fds as &$fi) {
					if (is_numeric($fi['sysv']) && array_key_exists($fi['sysv'], $sres['multi'])) {
						$fi['sysv'] = $sres['multi'][$fi['sysv']];
					}
				}
			}
			$res = formInject($newForm);
		}
		if ($sres['result'] == 'ok' || !isset($sres)) {
			$res = formInject($newForm);
		}
	}
	return $res;
}

function wrapT($a) {
	return '"' . $a . '"';
}

function formInject($newForm) {
	global $wres, $newd;
	if (is_array($newForm) && count($newForm) > 2) {
		$_POST['formName'] = $newForm['title'];
		$_POST['formsum'] = json_encode(unserialize(gzuncompress($newForm['fields'])));
		$_POST['regForm'] = $newForm['registry'];
		$_POST['fakereturn'] = true;
		require_once('saveform.php');
		if ($wres) {
			$pdata = $newForm['rowData'];
			$subPrefix = 'wf_' . $newd;
			$plain = 0;
			$sqlInsert = 'insert into wform_' . $newd;
			$once = false;
			if (count($pdata) > 0) {
				$tvals = array();
				foreach ($pdata as $pid => &$prow) {
					if (is_numeric($pid)) {
						if ($once === false) {
							$akeys = array_keys($prow);
							$once = true;
							$tinsert = $sqlInsert . ' (' . join(",", $akeys) . ') VALUES ';
						}
						$tvals[] = '(' . join(",", array_map("wrapT", array_values($prow))) . ')';

						++$plain;
					} else {
						$subinsert = 'insert into wf_' . $newd . '_' . $pid . ' ';
						$sonce = false;
						$subvals = array();
						foreach ($prow as $sid => &$sprow) {
							if ($sonce === false) {
								$subkeys = array_keys($sprow);
								$subinsert .= '(' . join(",", $subkeys) . ') VALUES ';
							}
							$subvals[] = '(' . join(",", array_map("wrapT", array_values($sprow))) . ')';
						}
						if (count($subvals) > 0) {
							$subinsert .= join(",", $subvals);
							$sires = mysql_query($subinsert);
						}
					}
				}
				if (count($tvals) > 0) {
					$sql = $tinsert . join(",", $tvals);
					$din = mysql_query($sql);
				}
			}
			$res = json_encode(
				array(
					0 => array(
						'title'        => $newForm['title'],
						'registry'     => $newForm['registry'],
						'valid'        => $newForm['valid'],
						'valid_change' => '&nbsp;',
						'id'           => $newd,
						'rows'         => $plain
					)
				)
			);
		}
		return $res;
	}
}

function importSets($upset) {
	global $dpConfig, $baseDir;
	$resume = array("result"   => false, "multi" => array(), 'passed' => 0, 'form_case' => false, 'done' => array(),
	                'children' => array()
	);
	$delay = array();
	$happen = 0;
	$children = array();
	$stats = array();
	if (count($upset) > 0) {
		foreach ($upset as $upid => $ndset) {
			$sql = 'select title,touch,id from svsets where title="' . $ndset['title'] . '" limit 1';
			$res = mysql_query($sql);
			if ($ndset['id'] != $ndset['parent']) {
				$resume['children'][$ndset['id']] = $ndset['parent'];
			}
			if (!$res || mysql_num_rows($res) == 0) {
				$sql = 'insert into svsets (title,touch,vtype,level,status,options)
					values("' . $ndset['title'] . '",
					"' . $ndset['touch'] . '",
					"' . $ndset['vtype'] . '",
					"' . $ndset['level'] . '",
					"' . $ndset['status'] . '",
					"' . $ndset['options'] . '"
					)';
				$ires = mysql_query($sql);
				if ($ires) {
					$parid = mysql_insert_id();
					if ($ndset['id'] == $ndset['parent']) {
						$sql = 'update svsets set parent="' . $parid . '" where id="' . $parid . '" limit 1';
						$nres = mysql_query($sql);
					}
					/*else{
										// newID => oldParentID
										$children[$parid] = $ndset['parent'];
										}*/
					$resume['done'][$ndset[id]] = $parid;
					++$happen;
				}
			} else {
				//set with such name is found, now will have to compare
				$prev = mysql_fetch_assoc($res);
				$resume['multi'][] = array(
					"title"     => $ndset['title'],
					'in_touch'  => $ndset['touch'],
					'now_touch' => $prev['touch'],
					'in_id'     => $ndset['id'],
					'now_id'    => $prev['id']
				);
				$delay[$ndset['id']] = $ndset;
			}
		}
	}
	$resume['happen'] = $happen;
	if (count($delay) > 0) {
		$fpath = tmpFileStore(serialize($delay));
		$_SESSION['set_delay_store'] = $fpath;
		$resume['result'] = 'partial';
	}
	if ($happen > 0 && count($delay) === 0) {
		$resume['result'] = true;
		fixParent($resume);
	}
	return $resume;
}

function fixParent($clist) {
	foreach ($clist['children'] as $old_id => $oldParentId) {
		if (array_key_exists($oldParentId, $clist['done'])) {
			$sql = 'update svsets set parent="' . $clist['done'][$oldParentId] . '" where id="' . $clist['done'][$old_id] . '"';
			$ires = mysql_query($sql);
		}
	}
}

function swapKV($a) {
	$b = array();
	if (count($a) > 0) {
		foreach ($a as $k => $v) {
			$b[$v] = $k;
		}
	}
	return $b;
}

function importDelayed($solution, $conserv) {
	if (!is_array($solution)) {
		$solution = array();
	}

	if (!is_array($conserv)) {
		$conserv = array();
	}
	$children = array();
	$complete = array();
	if ($_POST['relvs'] != '') {
		$children = json_decode(stripslashes($_POST['relvs']), true);
		if (!is_array($children)) {
			$children = array();
		}
	}
	$res = 'ok';
	$ncom = array();
	if ($_SESSION['set_delay_store'] != '' && file_exists($_SESSION['set_delay_store']) && (count($solution) > 0 || count($conserv) > 0)) {
		$desd = unserialize(tmpFileRead($_SESSION['set_delay_store'], true));
		$cnt = 0;
		$setsDone = json_decode(stripslashes($_POST['wdone']), true);
		// current -> incoming IDs
		foreach ($solution as $spart => $svalue) {
			$set = $desd[$svalue];

			$sql = 'update svsets set vtype="' . $set['vtype'] . '",
						level="' . $set['level'] . '",
						options="' . $set['options'] . '",
						touch="' . $set['touch'] . '",
						status="' . $set['status'] . '"
					where id="' . (int)$spart . '"';
			$res = mysql_query($sql);
			if ($res)
				++$cnt;
			if ($set['id'] != $set['parent']) {
				$children[$svalue] = $spart;
			}
			$setsDone[$svalue] = $spart;
		}
		$complete = swapKV($conserv);
		unset($_SESSION['set_delay_store']);
		if (count($setsDone) > 0) {
			$complete = $complete + $setsDone;
		}
		fixParent(array("done" => $complete, "children" => $children));

		if ($cnt === count($solution)) {
			if ((int)$_GET['isform'] === 1) {
				if (isset($_SESSION['form_delay_store']) && $_SESSION['form_delay_store'] != '') {
					$tform = $_SESSION['form_delay_store'];
					$newForm = unserialize(tmpFileRead($tform, true));
					unset($_SESSION['form_delay_store']);
					//Update fields in form with new Ids
					$fds = unserialize(gzuncompress($newForm['fields']));
					$ncom = swapKV($complete);
					if (count($fds) > 0 && count($complete) > 0) {
						foreach ($fds as &$fi) {
							if (is_numeric($fi['sysv']) && array_key_exists($fi['sysv'], $complete)) {
								$fi['sysv'] = $complete[$fi['sysv']];
							}
						}
						$newForm['fields'] = gzcompress(serialize($fds));
					}
					$fres = formInject($newForm);
					if ($res) {
						$res .= '#@#' . $fres;
					}
				}
			}

		} else {
			$res = "fail";
		}
	}
	return $res;
}


?>