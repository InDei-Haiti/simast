<?php /* ADMISSION $Id: followup.class.php,v 1.9 2010/10/05 06:30:43 istesin Exp $ */
/**
 * @package dotProject
 * @subpackage modules
 * @version $Revision: 1.9 $
 */
global $issues;
/*$issues=array(  1 => array('title'=>'Child','kids'=>'FollowChildIssues'),
					2 => array('title'=>'Mother/Father','kids'=>'FollowParentIssues'),
					3 => array('title'=>'Caregiver','kids'=>'FollowParentIssues'),
					4 => 'Disclosure',
					5 => 'Other close adults learn child HIV status',
					6 => 'Response of Disclosure',
					7 => 'State of Disclosure',
					8 => 'Secondary Caregiver knowledge',
					9 => 'Primary Caregiver Tested',
					10 => 'Mother/Father Status',
					11 => 'M/F/C Treatment',
					12 => 'Stigmatization',
					13 => 'Secondary Caregiver Identification'
				);
*/
$issues = dPgetSysVal('FollowIssues');
$treatIssues = true;
require_once ($AppUI->getSystemClass ( 'dp' ));

/**
 * Admission Record Class
 *
 */
class CFollowUp extends CDpObject {

	var $followup_id = NULL;
	var $followup_adm_no = NULL;
	var $followup_client_type = NULL;
	var $followup_visit_type = NULL;
	var $followup_issues = NULL;
	var $followup_issues_notes = NULL;
	var $followup_service = NULL;
	var $followup_service_notes = NULL;
	var $followup_date = NULL;
	var $followup_center_id = NULL;
	var $followup_officer_id = NULL;
	var $followup_object = NULL;
	var $followup_client_id = NULL;	

	function CFollowUp() {
		$this->CDpObject ( 'followup_info', 'followup_id' );
	}

	function store() {
		global $AppUI;

		if (($this->followup_id) && ($this->followup_id > 0)) {

			addHistory ( 'followupinfo', $this->followup_id, 'update', $this->followup_id );
			$this->_action = 'updated';

			$ret = db_updateObject ( 'followup_info', $this, 'followup_id', true );

		} else {

			$this->_action = 'added';
			$ret = db_insertObject ( 'followup_info', $this, 'followup_id' );
			addHistory ( 'followupinfo', $this->followup_id, 'add', $this->followup_id );

		}

		if (! $ret) {
			return get_class ( $this ) . "::store failed <br />" . db_error ();
		} else {
			return NULL;
		}
	}
}

function issueView($str){
	global $treatIssues,$issues;
	if($treatIssues == true){
		prepareIssue(true);
		$treatIssues=false;
	}
	$code=array();
	$list = explode(',',$str);
	foreach ($list as $val) {
		if(strstr($val,'-')){
			$parts=explode('-',$val);
			$code[]=$issues[$parts[0]]['title'].'-'.$issues[$parts[0]]['kids'][$parts[1]];

		}else{
			$code[]=$issues[$val];
		}
	}
	return join(', ',$code);
}

function prepareIssue($tail = false) {
	global $issues;
	/*foreach ( $issues as $key => $val ) {
		if (is_array ( $val )) {
			if ($val ['kids'] != '') {
				$dvals = dPgetSysVal ( $val ['kids'] );
				$issues [$key] ['kids'] = $dvals;
			}
		}
	}*/
	if($tail === true){
		$issues[14]='Other ';
	}
}

class lister {
	var $pref;
	var $cvals;
	var $hname;
	var $str;
	var $mode;
	var $list = array();
	var $lval=0;

	function lister($case,$prefix,$arr,$name,$str=false){
		if($case == 'follow'){
			global $issues;
			prepareIssue(true);
			$this->cvals=$issues;
		}else{
			$this->cvals=$arr;
		}
		if($name == 'issues'){
			$this->list = explode(',',$str);
			$this->lval='14';
		}
		$this->mode=$case;
		$this->pref=$prefix;
		$this->hname = $name;
		$this->str=$str;

	}

	function build($cut = FALSE) {
		foreach ( $this->cvals as $key => $val ) {
			if (is_array ( $val )) {
				$code .= '<b>' . $val ['title'] . '</b><br>';
				if ($val ['kids'] != '') {
					foreach ( $val ['kids'] as $dkey => $dval ) {
						$dval=addslashes($dval);
						$chk = '';
						if (in_array ( $key . '-' . $dkey, $this->list )) {
							$chk = 'checked="checked"';
						}
						$code .= '<label><input ' . $chk . ' type="checkbox" name="' . $this->pref . '_' . $this->hname . '[]" value="' . $key . '-' . $dkey . '">&nbsp;' . /*$key ''. '.' .*/ $dval . '</label><br>'."\n";
					}
					$code .= "<hr>\n\n";
				}
			} else {
				$chk = '';
				if (in_array ( $key, $this->list )) {
					$chk = 'checked="checked"';
				}
				$code .= '<label><input ' . $chk . ' type="checkbox" name="' . $this->pref . '_' . $this->hname . '[]" value="' . $key . '">&nbsp;' . $key . '.' . addslashes($val) . '</label><br>'."\n";
			}
		}
		if ($this->mode == 'issue' ) {
			$chk = '';
			if (($this->mode  == 'issue' && in_array ( '14', $this->list ) )   ){
				$chk = 'checked="checked"';
			}
			$code .= '<input type="checkbox"  ' . $chk . ' name="' . $this->pref . '_' . $this->hname . '[]" value="' . $this->lval . '">' . $this->lval . '. Other:&nbsp;';
		}
		if ($this->str === false) {
			$code .= '<input type="text" size="18" name="' . $this->pref . '_' . $this->hname . '_note" class="live_edit" id="other_' . $this->hname . '">';
		}
		if($cut === true){
			$code=str_replace("\n",'',$code);
			$code=str_replace("\t",'',$code);
		}
		return $code;

	}
}

function listHTML($mode,$str = ''){
	$prefix='followup';
	if($mode == 'issue'){
		global $issues;
		prepareIssue();
		$cvals=$issues;
		$hname='issues';
		$lval='14';
		$list = explode(',',$str);
	}elseif($mode == 'service'){
		$cvals = dPgetSysVal('FollowServices');
		$hname='services';
		$lval=count($cvals);
		$list=array();
	}elseif($mode == 'care'){
		$cvals = dPgetSysVal('CBCHomeCare');
		$hname='care';
		$lval=count($cvals);
		$list=array();
		$prefix= 'cbc';
		$str = true;
	}elseif ($mode == 'needs'){
		$cvals = dPgetSysVal('ServiceTypes');
		$hname='need';
		$lval=count($cvals);
		$list=array();
		$prefix= 'chw';
		$str = true;
	}


}


function makeListPerson($admno,$alone = 0){
	$answer=array();

	$sql= 'select client_first_name,client_last_name,client_other_name,client_gender,client_id,client_dob,client_doa,client_status
			from clients
			where client_adm_no ="'.mysql_real_escape_string($admno).'" limit 1';
	$res=mysql_query($sql);
	if($res && mysql_num_rows($res) == 1){
		$kid = mysql_fetch_object($res);
		//echo $name->aname;
		/*if($alone === 2){
			$q = new DBQuery();
			$q->addTable('discharge_info');
			$q->addWhere('dis_client_id="'.$kid->client_id.'"');
			$q->setLimit(1);
			$q->addQuery('dis_id');
			$stored = $q->loadResult();
			if($stored > 0){
				echo 'exist';
				return;
			}else{
				$alone = 0;
			}
		}*/
	}else{
		echo 'fail';
		return ;
	}
	$lages=digiAge($kid->client_dob);
	if(!$lages || !is_array($lages)){
		$lages=array();
		$lages[0]='';
	}
	$q =new DBQuery();
	$q->addTable('counselling_info');
	$q->addWhere('counselling_client_id="'.$kid->client_id.'"');
	$q->setLimit(1);
	$q->addQuery('counselling_age_status');
	$ageExact = $q->loadResult();
	
	$sql= 'select period_diff(date_format(now(), "%Y%m"), date_format(client_doa, "%Y%m")) as timein from clients where client_id="'.$kid->client_id.'" limit 1';
	$rtin=mysql_query($sql);
	
	if($rtin){
		$timein=mysql_fetch_array($rtin);		
		$timein = $timein[0];
	}else{
		$timein=0;
	}	
	
	$agetypes=dPgetSysVal('AgeType');
	$gendertypes = dPgetSysVal('GenderType');

	if($alone == 2){
		$sql='select social_entry_date from status_client where social_client_id="%d" and social_client_status in (3,7) order by id desc limit 1';
		$res = mysql_query(sprintf($sql,$kid->client_id));
		if($res){
			$changeDate = mysql_fetch_array($res);
		}
	}else{
		$changeDate = array(0=>false);
	}

	$answer['child']=array($kid->client_first_name.' '.$kid->client_other_name.' '.$kid->client_last_name,
							$lages[0],
							$kid->client_gender,
							array('fname'=>$kid->client_first_name,
								'oname'=>$kid->client_other_name,
								'lname'=>$kid->client_last_name),
							printDate($kid->client_doa),
							printDate($kid->client_dob),
							$agetypes[$ageExact].'|'.$ageExact,
							$gendertypes [$kid->client_gender],
							$lages[1],
							$timein,
							$kid->client_status,
							($changeDate[0] ? printDate($changeDate[0]) : null)
							);
	$answer['client_id']=$kid->client_id;

	//if ($alone === 0) {
		$sql = 'select distinct role, concat_ws(" ",fname,lname) as aname,relationship from admission_caregivers where client_id="' . $kid->client_id . '" and role IN ("father","mother") limit 2';
		$res2 = mysql_query ( $sql );
		$parentscount = mysql_num_rows ( $res2 );
		if ($res2) {
			$answer ['parent'] = array ();
			for($i = 0; $i < $parentscount; $i ++) {
				$pars = mysql_fetch_object ( $res2 );
				if (! is_null ( $pars->aname )) {
					$answer ['parent'] [] = $pars->aname;
				}
			}
		}
		unset ( $pars );
		$carez = array ('pri', 'sec' );
		$answer ['caregiver'] = array ();
		$answer ['relship'] = array();
		$answer ['careids'] = array();
		foreach ( $carez as $val ) {
			$sql = 'select concat_ws(" ",fname,lname) as aname,relationship,id, role from admission_caregivers where client_id="' . $kid->client_id . '" and lname <> "" and lname is not null  and role="' . $val . '" and datesoff is null limit 1';
			$res2 = mysql_query ( $sql );
			if ($res2) {
				$pars = mysql_fetch_object ( $res2 );
				if (! is_null ( $pars->aname )) {
					$answer ['caregiver'] [] = $pars->aname;
					$answer ['relship'] [] = $pars->relationship;
					$answer ['careids'] [] = $pars->id;
				}
			}
		}
	//}
	return $answer;
}

function getCHWList(){
	
	// collect all the users for the staff list with type CHW (10)
	$q  = new DBQuery;
	$q->addTable('contacts','con');
	$q->leftJoin('users','u', 'u.user_contact = con.contact_id');
	$q->addQuery('contact_id');
	$q->addQuery('CONCAT_WS(" ",contact_first_name,contact_last_name)');
	$q->addOrder('contact_last_name');
	$q->addWhere('contact_active="1"');
	$q->addWhere('contact_type="10"');
	$owners = $q->loadHashList();
	$owners = array_merge(array(0=>'-- Select --'),$owners);
	return $owners;
}

?>