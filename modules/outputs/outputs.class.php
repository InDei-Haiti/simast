<?php
$dateDetail = false;

/* $titles = array(
	'counselling_intake' => array(
		'title'      => 'INTAKE AND PCR',
		'db'         => 'counselling_info',
		'client'     => 'counselling_client_id',
		'uid'        => 'tb1',
		'date'       => 'counselling_entry_date',
		'center'     => 'counselling_clinic',
		'staff'      => 'counselling_staff_id',
		'did'        => 'counselling_id',
		'abbr'       => 'INT',
		'defered'    => array(),
		'link'       => array('href' => '?m=clients&a=add&client_id=#client_id#&counselling_id=#did#',
		                      'vals' => array('client_id', 'did')
		),
		'plurals'    => array(),
		'referral'   => '',
		'next_visit' => ''
	),
	'counselling_visit'  => array(
		'title'      => 'COUNSELLING VISIT',
		'db'         => 'counselling_visit',
		'client'     => 'counselling_client_id',
		'uid'        => 'tb2',
		'date'       => 'counselling_entry_date',
		'center'     => 'counselling_center_id',
		'staff'      => 'counselling_staff_id',
		'did'        => 'counselling_id',
		'abbr'       => 'COUN',
		'defered'    => array(),
		'link'       => array('href' => '?m=counselling&a=addedit&counselling_id=#did#&client_id=#client_id#',
		                      'vals' => array('client_id', 'did')
		),
		'plurals'    => array(),
		'referral'   => 'counselling_referer',
		'next_visit' => 'counselling_next_visit'
	),
	'clinical_visit'     => array(
		'title'      => 'CLINICAL VISIT',
		'db'         => 'clinical_visits',
		'client'     => 'clinical_client_id',
		'uid'        => 'tb3',
		'date'       => 'clinical_entry_date',
		'center'     => 'clinical_clinic_id',
		'staff'      => 'clinical_staff_id',
		'did'        => 'clinical_id',
		'abbr'       => 'CLIN',
		'defered'    => array(),
		'link'       => array('href' => '?m=clinical&a=addedit&clinical_id=#did#&client_id=#client_id#',
		                      'vals' => array('client_id', 'did')
		),
		'plurals'    => array(),
		'referral'   => 'clinical_referral',
		'next_visit' => 'clinical_next_date'
	),
	'social_visit'       => array(
		'title'      => 'SOCIAL WORK VISIT',
		'db'         => 'social_visit',
		'client'     => 'social_client_id',
		'uid'        => 'tb4',
		'date'       => 'social_entry_date',
		'center'     => 'social_clinic_id',
		'staff'      => 'social_staff_id',
		'did'        => 'social_id',
		'abbr'       => 'SOC',
		'defered'    => array('lname', 'fname', 'health', 'mobile', 'idno', 'age', 'educ', 'employment', 'marital',
			'health', 'relationship', 'status'
		),
		'link'       => array('href' => '?m=social&a=addedit&social_id=#did#&client_id=#client_id#',
		                      'vals' => array('client_id', 'did')
		),
		'plurals'    => array('social_services'  => array(
			'table'   => 'social_services',
			'index'   => 'social_services_social_id',
			'client'  => 'social_services_client_id',
			'fields'  => array('social_services_service_id', 'social_services_date', 'social_services_notes',
				'social_services_value'
			),
			'eparser' => array('social_services_date' => '$resex=turnDateSQL("#XYZ#");')
		),
		                      'social_household' => array(
			                      'table'   => 'household_info',
			                      'index'   => 'household_client_id',
			                      //'client'=>'household_client_id',
			                      'fields'  => array('household_name', 'household_relationship', 'household_gender',
				                      'household_notes', 'household_yob'
			                      ),
			                      'eparser' => array('household_yob' => '$var="#XYZ#";$vprs=split("/",$var); if(count($vprs) == 3){$resex=$vprs[2];}else {$resex=$var;}')
		                      )
		),
		'referral'   => 'social_referral',
		'next_visit' => 'social_next_visit'

	),
	'nutrition_visit'    => array(
		'title'      => 'NUTRITION VISIT',
		'db'         => 'nutrition_visit',
		'client'     => 'nutrition_client_id',
		'uid'        => 'tb5',
		'date'       => 'nutrition_entry_date',
		'center'     => 'nutrition_center',
		'staff'      => 'nutrition_staff_id',
		'did'        => 'nutrition_id',
		'abbr'       => 'NUT',
		'defered'    => array(),
		'link'       => array('href' => '?m=nutrition&a=addedit&nutrition_id=#did#&client_id=#client_id#',
		                      'vals' => array('client_id', 'did')
		),
		'plurals'    => array('nutrition_service' => array(
			'table'  => 'nutrition_service',
			'index'  => 'nutrition_service_visit_id',
			'client' => 'nutrition_service_client_id',
			'fields' => array('nutrition_service_program', 'nutrition_service_item', 'nutrition_service_qty')
		)
		),
		'referral'   => 'nutrition_refer',
		'next_visit' => 'nutrition_next_visit'
	),
	'admission'          => array(
		'title'      => 'ADMISSION DETAILS',
		'db'         => 'admission_info',
		'client'     => 'admission_client_id',
		'uid'        => 'tb6',
		'date'       => 'admission_entry_date',
		'center'     => 'admission_clinic_id',
		'staff'      => 'admission_staff_id',
		'did'        => 'admission_id',
		'abbr'       => 'ADM',
		'defered'    => array('lname', 'fname', 'health', 'mobile', 'idno', 'age', 'educ', 'employment', 'marital',
			'health', 'relationship', 'status'
		),
		'link'       => array('href' => '?m=admission&a=addedit&client_id=#client_id#&admission_id=#did#',
		                      'vals' => array('client_id', 'did')
		),
		'plurals'    => array('admission_other_household_members' => array(
			'table'   => 'household_info',
			'index'   => 'household_client_id',
			//'client'=>'household_client_id',
			'fields'  => array('household_name', 'household_relationship', 'household_gender', 'household_notes',
				'household_yob'
			),
			'eparser' => array('household_yob' => '$var="#XYZ#";$vprs=split("/",$var); if(count($vprs) == 3){$resex=$vprs[2];}else {$resex=$var;}')
		)
		),
		'referral'   => '',
		'next_visit' => ''
	),
	'medical_assessment' => array(
		'title'      => 'MEDICAL ASSESSMENT UPON ADMISSION',
		'db'         => 'medical_assessment',
		'client'     => 'medical_client_id',
		'uid'        => 'tb7',
		'date'       => 'medical_entry_date',
		'center'     => 'medical_clinic_id',
		'staff'      => 'medical_staff_id',
		'did'        => 'medical_id',
		'abbr'       => 'MED',
		'defered'    => array(),
		'link'       => array('href' => '?m=medical&a=addedit&client_id=#client_id#&medical_id=#did#',
		                      'vals' => array('client_id', 'did')
		),
		'plurals'    => array('medical_history'     => array(
			'table'   => 'medical_history',
			'index'   => 'medical_history_medical_id',
			'client'  => 'medical_history_client_id',
			'fields'  => array('medical_history_hospital', 'medical_history_date', 'medical_history_diagnosis'),
			'eparser' => array('medical_history_date' => '$resex=turnDateSQL("#XYZ#");')
		),
		                      'medical_medications' => array(
			                      'table'  => 'medications_history',
			                      'index'  => 'medications_history_medical_id',
			                      'client' => 'medications_history_client_id',
			                      'fields' => array('medications_history_drug', 'medications_history_dose',
				                      'medications_history_frequency'
			                      )
		                      )
		),
		'referral'   => 'medical_referral',
		'next_visit' => 'medical_next_visit'
	),
	'mortality'          => array(
		'title'      => 'MORTALITY Details',
		'db'         => 'mortality_info',
		'client'     => 'mortality_client_id',
		'uid'        => 'tb8',
		'date'       => 'mortality_entry_date',
		'center'     => 'mortality_clinic_id',
		'staff'      => 'mortality_clinical_officer',
		'did'        => 'mortality_id',
		'abbr'       => 'MORT',
		'defered'    => array(),
		'link'       => array('href' => '?m=mortality&a=addedit&client_id=#client_id#&mortality_id=#did#',
		                      'vals' => array('client_id', 'did')
		),
		'plurals'    => array(),
		'referral'   => '',
		'next_visit' => ''
	),
	'discharge'          => array(
		'title'      => 'DISCHARGE',
		'db'         => 'discharge_info',
		'client'     => 'dis_client_id',
		'uid'        => 'tb9',
		'date'       => 'dis_entry_date',
		'center'     => 'dis_center',
		'staff'      => '',
		'did'        => 'dis_id',
		'abbr'       => 'DIS',
		'defered'    => array(),
		'link'       => array('href' => '?m=discharge&a=add&disid=#did#', 'vals' => array('', 'did')),
		'plurals'    => array(),
		'referral'   => '',
		'next_visit' => ''
	),
	'activity'           => array(
		'title'      => 'ACTIVITIES',
		'db'         => 'activity', //, activity_clients
		'client'     => 'activity_clients_client_id',
		'center'     => 'activity_clinic',
		'staff'      => 'NULL',
		'uid'        => 'tb9',
		'date'       => 'activity_date',
		'did'        => 'activity_id',
		'where'      => 'activity_clients_activity_id=activity_id',
//'client_name'=> 'concat(client_first_name,",",client_last_name) as client_name',
		'abbr'       => 'ACT',
		'defered'    => array(''),
		'link'       => array('href' => '?m=activity&a=add&activity_id=#did#', 'vals' => array('', 'did')),
		'plurals'    => array(
			'activity_facilitator' => array(
				'table'          => 'activity_facilitator',
				'index'          => 'facilitator_activity_id',
				'client'         => '',
				'keep'           => TRUE,
				'fields'         => array('facilitator_training_id', 'facilitator_name', 'facilitator_topic'),
				'use_form_index' => 'facilitator_id'
			),
			'activity_clients'     => array(
				'table'  => 'activity_clients',
				'index'  => 'activity_clients_activity_id',
				'client' => '',
				'fields' => array('activity_clients_client_id')
			),
			'activity_contacts'    => array(
				'table'  => 'activity_contacts',
				'index'  => 'activity_contacts_activity_id',
				'client' => '',
				'fields' => array('activity_contacts_contact_id')
			),
			'activity_curriculum'  => array(
				'table'          => 'trainings',
				'index'          => 'training_id',
				'client'         => '',
				'fields'         => array('training_name', 'training_curriculum', 'training_curriculum_desc'),
				'keep'           => TRUE,
				'use_form_index' => 'training_id'
			)
		),
		'referral'   => '',
		'next_visit' => ''
	),
	'clients'            => array(
		'title'       => 'Clients',
		'db'          => 'clients',
		'client'      => 'client_id',
		'uid'         => 'tb10',
		'date'        => 'counselling_entry_date',
		'client_name' => 'concat(client_first_name," ",client_last_name) as client_name',
		'did'         => 'client_id',
		'defered'     => array(),
		'abbr'        => 'CLI',
		'link'        => array('href' => '?m=clients&a=view&client_id=#did#', 'vals' => array('', 'did')),
		'plurals'     => array(),
		'referral'    => '',
		'next_visit'  => ''
	),
	'client_status'      => array(
		'title'       => 'Client Status Log',
		'db'          => 'status_client',
		'client'      => 'social_client_id',
		'uid'         => 'tb14',
		'date'        => 'social_entry_date',
		'client_name' => 'concat(client_first_name," ",client_last_name) as client_name',
		'did'         => 'id',
		'defered'     => array(),
		'abbr'        => 'CSL',
		'link'        => array('href' => '?m=clients&a=view&client_id=#did#', 'vals' => array('', 'did')),
		'plurals'     => array(),
		'referral'    => '',
		'next_visit'  => ''
	),

	'followup'           => array(
		'title'       => 'FOLLOW-UP',
		'db'          => 'followup_info',
		'client'      => 'followup_client_id',
		'center'      => 'followup_center_id',
		'staff'       => 'followup_officer_id',
		'uid'         => 'tb11',
		'date'        => 'followup_date',
		'client_name' => 'concat(client_first_name," ",client_last_name) as client_name',
		'did'         => 'followup_id',
		'abbr'        => 'FLW',
		'defered'     => array(),
		'link'        => array('href' => '?m=followup&a=monoedit&fid=#did#', 'vals' => array('', 'did')),
		'plurals'     => array(),
		'referral'    => '',
		'next_visit'  => ''
	),
	'chwcheck'           => array(
		'title'       => 'CHW',
		'db'          => 'chw_info',
		'client'      => 'chw_client_id',
		'center'      => 'chw_center_id',
		'staff'       => 'NULL',
		'uid'         => 'tb12',
		'date'        => 'chw_entry_date',
		'client_name' => 'concat(client_first_name," ",client_last_name) as client_name',
		'did'         => 'chw_id',
		'defered'     => array(),
		'abbr'        => 'CHW',
		'link'        => array('href' => '?m=chwcheck&a=add&initem=#did#', 'vals' => array('', 'did')),
		'plurals'     => array('chw_comms' => array(
			'keep'    => true,
			'table'   => 'chw_info',
			'index'   => 'chw_id',
			'client'  => '',
			'fields'  => array('chw_comm_mob'),
			'pparser' => array('chw_comm_mob' => '$v=\'#XYZ#\'; $va=json_decode($v,true);$resex=join(",",array_values($va));')
		)
		),
		'referral'    => '',
		'next_visit'  => ''
	),
	'cbccheck'           => array(
		'title'       => 'CBC',
		'db'          => 'cbc_info',
		'client'      => 'cbc_client_id',
		'center'      => 'cbc_center_id',
		'staff'       => 'NULL',
		'uid'         => 'tb13',
		'date'        => 'cbc_entry_date',
		'client_name' => 'concat(client_first_name," ",client_last_name) as client_name',
		'did'         => 'cbc_id',
		'defered'     => array(),
		'abbr'        => 'CBC',
		'extra'       => array(),
		'plurals'     => array(),
		'link'        => array('href' => '?m=cbccheck&a=add&initem=#did#', 'vals' => array('', 'did')),
		'referral'    => '',
		'next_visit'  => ''
	)
); */


$titles = array(
		
		'clients' => array(
							'title'       => 'Clients',
							'db'          => 'clients',
							'client'      => 'client_id',
							'uid'         => 'tb10',
							'client_name' => 'concat(client_first_name," ",client_last_name) as client_name',
							'did'         => 'client_id',
							'defered'     => array(),
							'abbr'        => 'CLI',
							'link'        => array('href' => '?m=clients&a=view&client_id=#did#', 'vals' => array('', 'did')),
							'plurals'     => array(),
							'referral'    => '',
							'next_visit'  => ''
							),

		
);

$selectsCache = array();


class diskFile {
	private static $fh;
	private static $fpath;
	private static $fbh;
	private static $fbpath;
	private static $fbs;
	private static $fspath;
	private static $fjsonpath;
	private static $fjson;
	private static $fhtmlpath;
	private static $fhtml;
	private static $fbidpath;
	private static $fbid;
	private static $i = 0;
	private static $zar = array();

	static function init() {
		global $baseDir, $_SESSION;
		$fname = md5(time());
		if (isset ($_SESSION ['fileNameCsh'])) {
			$fdel = $baseDir . '/files/tmp/' . $_SESSION ['fileNameCsh'];
			if (file_exists($fdel . '.tst')) @unlink($fdel . '.tst');
			if (file_exists($fdel . '.tbd')) @unlink($fdel . '.tbd');
			if (file_exists($fdel . '.tch')) @unlink($fdel . '.tch');
			if (file_exists($fdel . '.tss')) @unlink($fdel . '.tss');
			if (file_exists($fdel . '.json')) @unlink($fdel . '.json');
			if (file_exists($fdel . '.html')) @unlink($fdel . '.html');
			if (file_exists($fdel . '.bid')) @unlink($fdel . '.bid');
		}
		$_SESSION ['fileNameCsh'] = $fname;
		$fip = $baseDir . '/files/tmp/' . $fname;
		diskFile::$fpath = $fip . '.tch';
		if (file_exists(diskFile::$fpath)) {
			if (diskFile::$fh) {
				fclose(diskFile::$fh);
			}
			unlink(diskFile::$fpath);
		}
		diskFile::$fh = fopen(diskFile::$fpath, "w+");

		diskFile::$fbpath = $fip . '.tbd';
		if (file_exists(diskFile::$fbpath)) {
			unlink(diskFile::$fbpath);
		}
		diskFile::$fbh = fopen(diskFile::$fbpath, "w+");

		diskFile::$fspath = $fip . '.tst';
		if (file_exists(diskFile::$fspath)) {
			unlink(diskFile::$fspath);
		}
		diskFile::$fbs = fopen(diskFile::$fspath, "w+");
		
		diskFile::$fjsonpath = $fip . '.json';
		if (file_exists(diskFile::$fjsonpath)) {
			unlink(diskFile::$fjsonpath);
		}
		diskFile::$fjson = fopen(diskFile::$fjsonpath, "w+");
		
		diskFile::$fhtmlpath = $fip . '.html';
		if (file_exists(diskFile::$fhtmlpath)) {
			unlink(diskFile::$fhtmlpath);
		}
		diskFile::$fhtml = fopen(diskFile::$fhtmlpath, "w+");
		
		diskFile::$fbidpath = $fip . '.bid';
		if (file_exists(diskFile::$fbidpath)) {
			unlink(diskFile::$fbidpath);
		}
		diskFile::$fbid = fopen(diskFile::$fbidpath, "w+");
	}
	
	static function startJsonArray() {
		fprintf(diskFile::$fjson, "%s", '[');
	}	
	static function putJsonData(&$str) {
		fprintf(diskFile::$fjson, "%s", $str);
		$str = '';
	}
	static function putSeparatorJsonData() {
		fprintf(diskFile::$fjson, "%s", ',');
	}
	static function endJsonArray() {
		fprintf(diskFile::$fjson, "%s", ']');
	}
	static function getJsonPath(){
		global $baseDir, $_SESSION;
		return $baseDir . '/files/tmp/' . $_SESSION ['fileNameCsh']. '.json';
	}
	static function putTXT(&$str) {
		fprintf(diskFile::$fh, "%s", $str);
		$str = '';
		diskFile::$i++;
	}
	
	static function putId(&$ids) {
		fprintf(diskFile::$fbid, "%s", $ids);
		$ids = '';
	}
	static function getBidPath($path=false){
		global $baseDir, $_SESSION;
		if(!$path)
			$path = $_SESSION ['fileNameCsh'];
		return $baseDir . '/files/tmp/' .$path. '.bid';
	}
	
	static function putHtml(&$html) {
		fprintf(diskFile::$fhtml, "%s", $html);
		$str = '';
	}
	static function getHtmlPath($path=false){
		global $baseDir, $_SESSION;
		if(!$path)
			$path = $_SESSION ['fileNameCsh'];
		return $baseDir . '/files/tmp/' .$path. '.html';
	}

	static function calls() {
		return diskFile::$i;
	}

	static function tableBody($row, $row_id) {
		//diskFile::$zar[$row_id]=$row;
		fprintf(diskFile::$fbh, "%s", serialize($row) . "\n");
		$row = null;
	}

	static function tableBodyWrite(&$stats) {
		//fprintf(diskFile::$fbh,"%s",serialize(diskFile::$zar));
		fclose(diskFile::$fbh);
		//unset(diskFile::$zar);
		fprintf(diskFile::$fbs, "%s", serialize($stats));
		fclose(diskFile::$fbs);
	}

	static function printOut() {
		//fpassthru(diskFile::$fh);
		if (strlen(diskFile::$fpath) > 0) {
			//fclose(diskFile::$fh);
			rewind(diskFile::$fh);
			while (!feof(diskFile::$fh)) {
				$buffer = fread(diskFile::$fh, 2048);
				echo $buffer;
				flush_buffers();
			}
			fclose(diskFile::$fh);
			//readfile(diskFile::$fpath);
			//unlink(diskFile::$fpath);
			flush_buffers();
		}
	}

	static function drops() {
		if (is_resource(diskFile::$fh)) fclose(diskFile::$fh);
		if (is_resource(diskFile::$fbh)) fclose(diskFile::$fbh);
		if (is_resource(diskFile::$fbs)) fclose(diskFile::$fbs);
	}

}

function DiskStatCache(&$tml) {
	global $baseDir, $_SESSION;
	$fname = $_SESSION ['fileNameCsh'];
	$fip = $baseDir . '/files/tmp/' . $fname . '.tss';
	$sfh = fopen($fip, 'w+');
	fprintf($sfh, "%s", $tml);
	fclose($sfh);
}

function DiskStatCachePartial(&$tml, $prefix = false) {
	global $baseDir, $_SESSION;
	$fname = $_SESSION ['fileNameCsh'];
	$fip = $baseDir . '/files/tmp/' . $fname . '.tss';
	$sfh = fopen($fip, 'a+');
	if ($prefix === true) {
		$fip1 = $baseDir . '/files/tmp/' . $fname . '.tss1';
		$pfh = fopen($fip1, 'a');
		fputs($pfh, $tml);
		rewind($sfh);
		while ($row = fgets($sfh)) {
			fputs($pfh, $row, strlen($row));
		}
		fclose($pfh);
		fclose($sfh);
		@unlink($fip);
		@rename($fip1, $fip);
	} else {
		fprintf($sfh, "%s", $tml);
		fclose($sfh);
	}
	$tml = '';
}

function DiskStatCacheSubstitute($tag, &$dset, $rti) {
	global $baseDir;
	$fname = $_SESSION ['fileNameCsh'];
	$fip = $baseDir . '/files/tmp/' . $fname . '.tss';
	$sfh = fopen($fip, 'r');
	$fip1 = $baseDir . '/files/tmp/' . $fname . '.tss1';
	$sfd = fopen($fip1, 'a');
	if (is_array($tag) && $dset === FALSE) {
		$colKiller = TRUE;
	} else {
		$colKiller = FALSE;
	}
	while ($row = fgets($sfh)) {
		if (is_string($tag)) {
			if (strstr($row, $tag)) {
				for ($ix = 0, $l = ($rti + 1); $ix < $l; $ix++) {
					$row = str_replace('#@#' . $ix . '#@#', ($dset[$ix] > 0 ? $dset[$ix] : 1), $row);
				}
			}
		} else {

		}
		fputs($sfd, $row, strlen($row));
	}
	fclose($sfh);
	fclose($sfd);
	@unlink($fip);
	@rename($fip1, $fip);
}


function turnDateSQL($idate) {
	$res = 'null';
	if ($idate != '' && strlen($idate) >= 8) {
		$tt = new CDate($idate);
		$res = $tt->format(FMT_DATE_MYSQL);
	}
	return $res;
}

function addChecks($arr, $part, $name) {
	$res = 'value="' . $name . '"';
	if (is_array($arr[$part]) && in_array($name, $arr[$part])) {
		$res .= ' checked="checked"';
	}
	return $res;
}

function DatetoInt($date) {
	$list = array(0, 1, 2);
	$rs = '';
	if (strlen($date) === 8) {
		return $date;
	}
	if (strlen($date) === 10) {
		if (strstr($date, '/')) {
			$delim = '/';
			$list = array_reverse($list);
		} else {
			$delim = '-';
		}
		$vdate = explode($delim, $date);
		foreach ($list as &$key) {
			$rs .= $vdate[$key];
		}
		return $rs;
	}
}

class ExIm {

	private static $xmode;
	private static $jsarr;
	private static $dates = array('sdate' => 0, 'edate' => 0);
	private static $newId;

	static private function  encode($str) {
		$encoded = base64_encode(addslashes(gzcompress(serialize($str), 9)));
		return $encoded;
	}

	static private function decode($str) {
		$string = @unserialize(@gzuncompress(@stripslashes(@base64_decode($str))));
		$str1 = @unserialize($string);
		return $str1;
	}

	static private function newTableRow($dbrow) {
		ExIm::setdates($dbrow);
		ExIm::$jsarr = array('id'    => $dbrow['id'],
		                     'name'  => $dbrow['qname'],
		                     'desc'  => $dbrow['qdesc'],
		                     'sdate' => ExIm::$dates['sdate'],
		                     'edate' => ExIm::$dates['edate'],
		                     'type'  => ExIm::$xmode,
		                     'brest' => $dbrow['show_result']
		);
	}

	static private function export() {
		global $AppUI;
		$df = $AppUI->getPref("SHDATEFORMAT");
		foreach (ExIm::$jsarr as $key => &$value) {
			if (strstr($key, 'date') && strlen($value) > 4) {
				$tmp = new CDate($value);
				ExIm::$jsarr[$key] = $tmp->format($df);
			}
		}
		return ExIm::$jsarr;
	}

	static private function setDates($sar) {
		foreach (ExIm::$dates as $key => &$val) {
			if (!$val && $sar[$key] != '' && $sar[$key] != $val) {
				ExIm::$dates[$key] = $sar[$key];
			}
		}
	}

	static public function pickFile($fpath) {
		$res = false;
		if (is_uploaded_file($fpath)) {
			$newQuery = file_get_contents($fpath);
			if (strlen($newQuery) > 0) {
				$newQuery = ExIm::decode($newQuery);
				if (is_array($newQuery) && count($newQuery) == 3) {
					ExIm::$xmode = $newQuery['mode'];
					if ($newQuery['mode'] === "Table") {
						$newQuery['query'][0]['visible'] = '1';
					} else {
						$newQuery['query'][0]['visible'] = '0';
					}
					$qid = ExIm::intoQueries($newQuery['query'][0]);
					if ($newQuery['mode'] === 'Stats' && count($newQuery['stat_query']) > 0) {
						if ($qid > 0) {
							$qs = $newQuery['stat_query'][0];
							$qs['qid'] = $qid;
							$res = ExIm::intoStats($qs, $newQuery['query'][0]);
						} else {
							$res = false;
						}
					} elseif ($newQuery['mode'] === 'Report') {
						$qs = $newQuery['query'][0];
						$res = ExIm::intoReports($qs);
					}
					$res = ExIm::export();
				}
			}
		}
		return $res;
	}

	static public function makeFile($qid, $type) {
		$dbQ = getSaves($qid, $type);
		$localname = $dbQ[0]['qname'];
		if ($type === 'Stats' || $type === 'Chart') {
			$q = new DBQuery();
			$q->addTable('stat_queries');
			$q->addWhere('id="' . $qid . '"');
			$dbStatQ = $q->loadList();
			$localname = $dbStatQ[0]['qname'];
		} else {
			if ($type === 'Report') {
				$q = new DBQuery();
				$q->addQuery('start_date,end_date,title,entries');
				$q->addTable('reports');
				$q->addWhere('id="' . $qid . '"');
				$dbQ = $q->loadList();
				$localname = $dbQ[0]['title'];
				$q->clearQuery();
				$sql = 'select backdoor from reports where id="' . $qid . '" limit 1';
				$res = mysql_query($sql);
				$bdd = mysql_fetch_array($res);
				$dbQ[0]['backdoor'] = $bdd[0];

				/*Collect all data about queries (stat and result)*/

			}
			$dbStatQ = false;
		}
		$localname = str_replace(' ', '_', trim($localname));
		$res = array('mode'       => $type,
		             'query'      => $dbQ,
		             'stat_query' => $dbStatQ
		);
		return array($localname, ExIm::encode(serialize($res)));
	}

	static public function intoQueries($dquery) {
		$sql = 'insert into queries (posts,qname,qdesc,sdate,edate,visits,fils,created,visible,dfilter,center,actives,lvdopt)
				values ("' . mysql_real_escape_string(serialize($dquery['posts'])) . '",
						"' . mysql_real_escape_string($dquery['qname']) . '",
						"' . mysql_real_escape_string($dquery['qdesc']) . '",
						"' . mysql_real_escape_string($dquery['sdate']) . '",
						"' . mysql_real_escape_string($dquery['edate']) . '",
						"' . mysql_real_escape_string($dquery['visits']) . '",
						"' . mysql_real_escape_string(serialize($dquery['fils'])) . '",now(),
						"' . mysql_real_escape_string($dquery['visible']) . '",
						"' . mysql_real_escape_string($dquery['dfilter']) . '",
						"' . mysql_real_escape_string($dquery['center']) . '",
						"' . ($dquery['actives']) . '",
						"' . mysql_real_escape_string(serialize($dquery['lvdopt'])) . '")';
		$res = mysql_query($sql);
		if ($res) {
			$qid = mysql_insert_id();
			$dquery['id'] = $qid;
			ExIm::newTableRow($dquery);
			return $qid;
		}

	}


	static public function intoStats($statd, $dq1) {
		$res = false;
		$sql = 'insert into stat_queries (qname,qdesc,rows,cols,query_id,turns,ranges,sdate,edate,show_result,qmode,chart_data,created)
			values("' . mysql_real_escape_string($statd['qname']) . '",
			"' . mysql_real_escape_string($statd['qdesc']) . '",
			"' . mysql_real_escape_string($statd['rows']) . '",
			"' . mysql_real_escape_string($statd['cols']) . '",
			"' . mysql_real_escape_string($statd['query_id']) . '",
			"' . mysql_real_escape_string($statd['turns']) . '",
			"' . mysql_real_escape_string($statd['ranges']) . '",
			"' . mysql_real_escape_string($dq1['sdate']) . '",
			"' . mysql_real_escape_string($dq1['edate']) . '",
			"' . mysql_real_escape_string($statd['show_result']) . '",
			"' . mysql_real_escape_string($statd['qmode']) . '",
			"' . mysql_real_escape_string($statd['chart_data']) . '",
			now()
			)';
		$res = mysql_query($sql);
		if ($res) {
			$qid = mysql_insert_id();
			$statd['id'] = $qid;
			ExIm::newTableRow($statd);
			return $qid;
		}
	}

	static public function intoReports($statd) {
		$res = false;
		$sql = 'insert into reports (title,start_date,end_date,entries,backdoor)
			values("' . mysql_real_escape_string($statd['title']) . '",
			"' . mysql_real_escape_string($statd['start_date']) . '",
			"' . mysql_real_escape_string($statd['end_date']) . '",
			"' . mysql_real_escape_string($statd['entries']) . '",
			"' . mysql_real_escape_string($statd['backdoor']) . '"
			)';
		$res = mysql_query($sql);
		if ($res) {
			$qid = mysql_insert_id();
			ExIm::newTableRow(array(
				'qname' => $statd['title'],
				'sdate' => $statd['start_date'],
				'edate' => $statd['end_date'],
				'id'    => $qid
			));
			return $qid;
		}
	}

}

function getFileBody($case) {
	global $baseDir;
	$blist = '';
	$art = '';
	$func = '';
	if ($case === 'body') {
		$art = 'tbd';
		$func = 'file($fbpath);';
	} else if ($case === 'stat') {
		$art = 'tst';
		$func = '@unserialize(@file_get_contents($fbpath));';
	}
	$fbf = $_SESSION['fileNameCsh'];
	$fbpath = $baseDir . '/files/tmp/' . $fbf . '.' . $art;
	if (file_exists($fbpath)) {
		eval('$blist=' . $func);
	}
	return $blist;
}

function prepareStr($str) {
	return str_replace("_", " ", ucfirst($str));
}

function sqlstr($str) {
	return mysql_real_escape_string(trim($str));
}

function findkey($key, $arr, $xf = false) {
	if ($xf === true) {
		$arr = array_keys($arr);
	}
	foreach ($arr as &$item) {
		if ($xf === true) {
			$a = $item;
			$b = $key;
		} else {
			$b = $item;
			$a = $key;
		}
		if (strstr($a, $b)) {
			return $item;
		}
	}
	return false;
}

function processQuery($tname, $tar) {
	global $titles;
}


function prepareDate($data) {
	if (strlen($data) == 10) {
		$res = str_replace('-', '', $data);
	} else {
		$res = $data;
	}
	$tdd = new CDate($res);
	return $tdd->format(FMT_DATE_MYSQL);
}

function viewDate($str) {
	$res = array();
	if ($str == 0 || strlen($str) < 8) {
		return array("N/D&nbsp;", 0);
	} elseif (strlen($str) == 8) {
		return array($str{6} . $str{7} . '/' . $str{4} . $str{5} . '/' . substr($str, 0, 4), $str);
	}
}

function getSaves($qid = 0, $mode = false) {
	$q = new DBQuery();

	$q->addQuery('qs.id,qs.qname,qs.qdesc,visits,fils,posts,actives,dfilter,lvdopt');
	if ($mode === 'Table') {
		$q->addQuery('sdate, edate');
		$q->addTable('queries', 'qs');
		if ($qid > 0) {
			$q->addWhere('qs.id = ' . $qid);
		}
		$q->addOrder('qs.created asc');
	} else {
		$q->addQuery('sqs.sdate, sqs.edate');
		$q->addTable('stat_queries', 'sqs');
		$q->addJoin('queries', 'qs', 'sqs.query_id=qs.id');
		if ($qid > 0) {
			$q->addWhere('sqs.id = ' . $qid);
		}
		$q->addOrder('sqs.created asc');
	}

	$qds = $q->loadList();
	$nar = array();
	foreach ($qds as $qid => &$qar) {
		$nar[$qid] = $qar;
		$nar[$qid]['posts'] = unserialize($nar[$qid]['posts']);
		$nar[$qid]['fils'] = unserialize($nar[$qid]['fils']);
	}

	return $nar;
}

function getTitles($akeys, $fields, $prefix = '', $appendix = '') {
	$res = array();
	if (is_array($akeys)) {
		foreach ($akeys as &$ak) {
			if (!is_array($fields[$ak])) {
				$res[$prefix . $ak . $appendix] = $fields[$ak];
			} else {
				$res[$prefix . $ak . $appendix] = $fields[$ak]['title'];
			}
		}
	} else {
		if ($fields[$akeys] != '') {
			$res[$akeys] = $fields[$akeys];
		}
	}
	return $res;
}

function oneWord($txt) {
	$res = preg_split("/\s/", $txt, -1, PREG_SPLIT_NO_EMPTY);
	return $res[0];
}

function turnDate($idate) {
	$year = substr($idate, 0, 4);
	$mon = substr($idate, 4, 2);
	$day = substr($idate, 6);
	return $day . '/' . $mon . '/' . $year;
}


function ifd($a, $b) {
	if ($a['r'] == false) {
		return -1;
	} elseif ($b['r'] == false) {
		return 1;
	} else {
		if ($a['r'] == $b['r']) {
			return 0;
		}
		return ($a['r'] < $b['r']) ? -1 : 1;
	}
}

function buildForms(&$fielder) {
	global $auto_open, $block_count, $lpost, $vmode, $mi, $tchex;
	$html = '<div id="cboxes">
		<label><input type=checkbox onclick="markAll(this);">ALL</label><ul class="mflt">' .
		topRowFields($lpost) . '
		</ul>
	</div>
	<input type="hidden" name="qsid" value=""><br>
	</div>';
	$html .= '<div class="moretable">';
	$toup = ' class="listmat" ';
	//echo 'fielders<br><br>';
	//var_dump($fielder);
	foreach ($fielder as $item => &$tar) {
		$inep = 0;
		$scpart = 0;
		$block_html = '';
		$pclass = '';
		$wfc = preg_match("/_(\d+)$/", $item, $wfs);
		if ($wfc) {
			$wfid = $wfs[1];
			$sql = 'select 1 from form_master where id="' . $wfid . '" and valid="0" limit 1';
			$res = mysql_query($sql);
			if (mysql_num_rows($res) === 1) {
				$pclass = " obsfield ";
			}
		}

		if ($tar['visible']) {
			$block_html .= "\n" . '<p class="hands ' . $pclass . '" data-col="' . $block_count . '" id="dler_' . $block_count . '">' . $tar ['title'] . '</p>' . "\n\t";
			$block_html .= "\t" . '<div class="exborder" id="block_' . $block_count . '"><ul class="cblox">
				<li><input type="checkbox" class="alltag">All</li>
				' . "\n";
			$ilist = $tar ['list']->getList();
			$bchex = 0;
			$ons = 0;
			$ind = 1;
			$next = false;
			$scpart = 0;
			foreach ($ilist as $key => &$val) {

				$pass = false;
				if (is_array($val)) {
					$sval = $val ['title'];
					if ($val['skip'] === true) {
						$pass = true;
					}
				} else {
					$sval = $val;
				}

				if (!$pass) {
					$ratio = $ind / 15;
					if ((int)$ratio === $ratio && $ratio > 0) {
						$scpart++;
						$next = true;
						$classAdd = $toup;
					} else {
						$classAdd = '';
					}

					if (is_array($lpost) && array_key_exists($item, $lpost) && count($lpost [$item]) > 0 && in_array($key, $lpost [$item])) {
						$stext = 'checked';
						++$bchex;
						++$tchex;
						++$ons;
					} else {
						$stext = '';
					}
					$vmode = trimView($sval, 'forms');
					$tclass = array();
					if ($vmode['show'] == true) {
						$tclass[] = 'moreview';
						$tadd = ' data-text="' . $sval . '" ';
					} else {
						//$tclass='';
						$tadd = '';
					}

					($key !== 'chw_old' && $key !== 'cbc_old' && preg_match('/_old$/', trim($key)) ? $tclass[] = 'obsfield' : '') . '"';
					$block_html .= "\t\t" . '<li style="' . ($scpart > 0 ? 'margin-left: ' . ($scpart * 230) . 'px;' : '') . '" ' . $classAdd . '>
											<label ' . (count($tclass) > 0 ? 'class="' . join(',', $tclass) . '"' : '') . $tadd . '><input class="jcheck" type="checkbox" name="' . $item . '[]" value="' . $key . '" ' . $stext . ' >'
						. $vmode['str'] .
						'</label>
									</li>' . "\n";
					$mi++;
					$ind++;
				}
			}
			$block_html .= "</ul>\n</div>";
			if ($bchex > 0) {
				$auto_open[] = 'dler_' . $block_count;
			}
			$block_count++;
		}
		if ($ons == count($ilist)) {
			$block_html = str_replace('class="alltag"', 'checked="checked" class="alltag"', $block_html);
		}
		$html .= $block_html;
	}
	return $html . '</div>';
}
/*0 => array(
			'clients' => array(
				'client_adm_no' => 'Adm #',
				'client_status' => 'Status',
				'client_name'   => 'Name',
				'client_dob'    => 'DoB',
				'client_gender' => 'Gender',
				'client_doa'    => 'DoA',
			),
			'extra'   => array('date' => 'Visit date')
		),*/
function topRowFields($lpost) {

	$boxes = array(
		0 => array(
			'clients' => array(
				'client_name' => 'Name',
				'client_activities' => 'Activities',
				'client_gender' => 'Sex',
		    	'client_birthday' => 'Day of Birth',
		    	'client_education_level' => 'Education Level',
				'client_health_status' => 'Health Status',
				'client_cin' => 'CIN',
				'client_other_id' => 'Other Identification',
				'client_occupation' => 'Occupation',
				'client_place_of_birth' => 'Place Of Birth',
				'client_marital_status' => 'Marital Status',
				'client_profession' => 'Profession',
			)/*,
			'extra'   => array('date' => 'Visit date')*/
		),
		/*1 => array(
			'clients'   => array(
				'client_center' => 'Main Center',
			),
			'admission' => array(
				'admission_location' => 'Location'
			),
			'extra'     => array(
				'center'     => 'Center',
				'staff'      => 'Staff',
				'referral'   => 'Referral To',
				'next_visit' => 'Next Visit'
			),

		),
		2 => array(
			'clients' => array(
				//'client_lvd_form' => 'LVD Form',
				'client_nhif'    => 'NHF #',
				'client_nhif_n'  => 'NHF-No, why',
				'client_immun'   => 'IC #',
				'client_immun_n' => 'IC-No, why'
			)
		)*/
	);

	$code = '';
	foreach ($boxes as $bid => &$bvals) {
		if ($bid === 2) {
			$code .= '<li style="width: 379px; float: right;">&nbsp;</li>';
		}
		foreach ($bvals as $key => &$vset) {
			foreach ($vset as $vname => &$vtit) {
				$code .= '<li><label><input type=checkbox name="' . $key . '[]" ' . addChecks($lpost, $key, $vname) . '>' . $vtit . '</label></li>';
			}
		}
	}

	return $code;

}

function cleanALoc($lp) {
	if (is_array($lp)) {
		if (array_key_exists('admission', $lp)) {
			if ($vp = array_search('admission_location', $lp['admission'])) {
				array_splice($lp['admission'], $vp, 1);
			}
		}
	}
}

function getReportList() {
	global $AppUI;
	$q = new DBQuery();
	$q->addTable('reports');
	$q->addQuery('title,start_date,end_date,id');
	$q->addOrder('id asc');
	$list = $q->loadList();
	$html = '<tbody>';
	$df = $AppUI->getPref('SHDATEFORMAT');
	if (count($list) > 0) {
		foreach ($list as &$row) {
			$sdate = new CDate($row['start_date']);
			$edate = new CDate($row['end_date']);
			$html .= '<tr data-rid="' . $row['id'] . '">
					<td><div class="qreditor fbutton"></div>
					<td class="rep_name">
						<div class="limiter"><a href="?m=outputs&a=reports&mode=compile&itid=' . $row['id'] . '">' . $row['title'] . '</a></div>
						<div class="fader"></div>
					</td>
					<td>' . ($sdate->getYear() > 0 ? $sdate->format($df) : '') . '</td>
					<td>' . ($edate->getYear() > 0 ? $edate->format($df) : '') . '</td>
					<td><span onclick="reporter.delr(this);" class="fhref" title="Delete">
							<img height="16" border="0" alt="Delete" weight="16" src="/images/delete1.png">
						</span>
					</td>
				</tr>' . "\n";
			unset($sdate, $edate);
		}
	} else {
		$html .= '<tr class="emptydb"><td colspan="5">No reports saved </td></tr>';
	}
	$html .= '</tbody>';
	return $html;
}

function saveFileBody(&$blist) {
	global $baseDir;
	$fbf = $_SESSION['fileNameCsh'];
	$fname = $baseDir . '/files/tmp/' . $fbf . '.tbd';
	$f = fopen($fname, "w");
	foreach ($blist as &$zrow) {
		fputs($f, $zrow);
	}
	fclose($f);
}

function my_array_merge($a1, $a2) {
	foreach ($a2 as $ai) {
		$a1[] = $ai;
	}
	return $a1;
}

function buildSelectOptions() {
	global $show_start, $show_end, $show_lvd, $lpost, $uamode, $curcentext, $lasttext, $alltext, $firsttext, $lvd_sel;
	$t = array('null' => '-- Select comparison mode --', 'gt' => 'after', 'eq' => 'equal', 'lt' => 'before');
	$lvd_mode = arraySelect(
		$t,
		'lvd_cmp_mode', 'class="text"', $lvd_sel != '' ? $lvd_sel : '');

	$code = '<div style="float: none; margin: 10px;" id="rctrl">&nbsp;&nbsp;&nbsp;
	<div style="white-space:nowrap;width: 550px;">Start &nbsp;' . drawDateCalendar('beginner', $show_start, false, 'id="start_date"', false, 10) . '
	&nbsp;&nbsp;&nbsp;End ' . drawDateCalendar('finisher', $show_end, false, 'id="end_date"', false, 10) . ' &nbsp;&nbsp;&nbsp;
	<label><input type="checkbox" name="actives" id="ashow" ' . ($uamode === false ? 'checked="checked"' : '') . '>Regular clients only</label>
	<span class="out_block">
	 	Last Visit Date
		' . $lvd_mode . '&nbsp;&nbsp;' . drawDateCalendar('lvd_date', $show_lvd, false, 'id="lvd_date"', false, 10) . '
	</span>
	<div class="result_opts result_opts-more" id="more_flip" title="Extended search"></div>	<br>
	</div>
	<div id="more_opts" style="">
	<label ><input type="checkbox" name="cur_center" value="1" id="curcen" ' . $curcentext . '>Only this center</label>
	<span class="out_block">
		<label ><input type="radio" name="vis_sel" value="all" id="allv" ' . $alltext . '>All visits</label> &nbsp;&nbsp;
		<label ><input type="radio" name="vis_sel" value="first" id="firstv" ' . $firsttext . '>First visit</label>
		<label ><input type="radio" name="vis_sel" value="last" id="lastv" ' . $lasttext . '>Last visit</label>
	</span>
	&nbsp;&nbsp;&nbsp;
	<span class="out_block">
		<label><input checked="checked" type="radio" name="dfilter" value="visit">Visit&nbsp;</label>&nbsp;&nbsp;
	<label><input type="radio" name="dfilter" value="doa">DOA&nbsp;</label>
	</span>
	</div>';
	return $code;
}

function buildTableDataDemand() {
	global $titles, $fielder, $baseDir, $fields, $tkeys;
	$fieldsPath = $baseDir . '/modules/outputs/titles';

	$ddir = opendir($fieldsPath);
	if (is_dir($fieldsPath)) {
		if ($dh = opendir($fieldsPath)) {
			while (($file = readdir($dh)) !== false) {
				if ($file != '.' && $file != '..' && $file{0} != '.') {
					$selects = array();
					$fileItem = str_replace(".title.php", "", $file);
					require $fieldsPath . '/' . $file;
				}
				unset ($vname);
			}
			closedir($dh);
		}
	}
	$tkeys = array_keys($titles);
	$fieldsPath = $baseDir . '/modules/outputs/data';

	$fielder = array();
	$ddir = opendir($fieldsPath);
	if (is_dir($fieldsPath)) {
		if ($dh = opendir($fieldsPath)) {
			while (($file = readdir($dh)) !== false) {
				if ($file != '.' && $file != '..') {
					$selects = array();
					$fileItem = str_replace(".fields.php", "", $file);
					$vname = findkey($fileItem, $tkeys);
					if ($vname != '' || $fileItem == 'extra' || strstr($fileItem, 'wform')) {
						require $fieldsPath . '/' . $file;
						$fielder [str_replace(".fields.php", "", $file)] = array('list'    => new wallE ($fields, $selects),
						                                                         'title'   => $titles [$vname] ['title'],
						                                                         'visible' => $partShow
						);
						unset ($fields);
					}
				}
				unset ($vname);
			}
			closedir($dh);
		}
	}

}


function proceedReportItem($rbdata) {
	global $baseDir, $masterStart, $masterEnd, $sdate, $edate, $df, $monitorKey, $sblk, $totalSections, $bigtar_keys, $clients, $nfei, $svals;
	$html = '';
	if (!$sdate) {
		$sdate = new CDate ();
		$edate = new CDate ();
		$sdvalid = $sdate->getYear();
		$edvalid = $edate->getYear();
		buildTableDataDemand();
	}
	$ugla = $rbdata['tbsdata'];
	$crowData = $rbdata ['r'];
	$selectedCase = $rbdata ['c'];
	if (isset ($_SESSION ['fileNameCsh'])) {
		$oldFNC = $_SESSION ['fileNameCsh'];
	}
	$currentFNC = md5(time());
	$_SESSION ['fileNameCsh'] = $currentFNC;
	if ($selectedCase === 'stat' || $selectedCase === 'graph') {
		if (isset($sec_rows[0]['title'])) {
			$html .= "\n\n" . '<i>' . (is_array($sec_rows[0]['title']) ? join(" ", $sec_rows[0]['title']) : $sec_rows[0]['title']) . '</i></td></tr>' . "\n<tr><td>";
		}
		require_once ('result.func.php');
		require_once ('stater.class.php');
		$onlyActive = $ugla ['actives'];
		$onlyThisCenter = $ugla ['cur_center'];
		if ($onlyThisCenter === true) {
			$curCenter = getThisCenter();
		}
		$dateApply = $ugla ['date_crit']; // possible values are DOA and VISIT
		$_POST = (count($crowData) === 2 ? $crowData[0] : $crowData);
		if ($masterStart !== false && !is_null($masterStart)) {
			$_POST['beginner'] = $sdate->format($df);
		}
		if ($masterEnd !== false && !is_null($masterEnd)) {
			$_POST['finisher'] = $edate->format($df);
		}
		
		$final = array();
		$nfei = new evolver ();
		resultBuilder('out');
		
		updateLiveState($monitorKey, ($sblk - 1) + ($selectedCase === "graph" ? .25 : .5), $totalSections);
		$svals = array(
			"rows"       => ($ugla ['rows']),
			'cols'       => ($ugla ['cols']),
			'range'      => ($ugla ['range']),
			'sunqs'      => bool2bit($ugla ['sunqs']),
			'stots_rows' => bool2bit($ugla ['stots_rows']),
			'stots_cols' => bool2bit($ugla ['stots_cols']),
			'sperc_rows' => bool2bit($ugla ['sperc_rows']),
			'sperc_cols' => bool2bit($ugla ['sperc_cols']),
			'sblanks'    => bool2bit($ugla ['sblanks']),
			'list'       => array()
		);
		$do_show_result = bool2bit($ugla ['brest']);

		$trows = count($ugla ['rows']);
		$tcols = count($ugla ['cols']);
		$bar = getFileBody('stat');
		if (count($bigtar_keys) > 0) {
			$ulines = $bigtar_keys; //array_keys($bigtar);
		} else {
			$ulines = range(0, count($clients));
		}
		$svals ['list'] = $ulines;
		$table = makeStat($bar, $svals);
		//$fps=$baseDir.'/files/tmp/'.$fip.'.tss';
		//$table = file_get_contents($baseDir.'/files/tmp/'.$currentFNC.'.tss');

		if ($selectedCase === 'graph') {
			updateLiveState($monitorKey, ($sblk - 1) + .5, $totalSections);
		}
		unset($bigtar, $bigtar_keys);
		if ($selectedCase === 'graph') {
			$gsetdata = (is_string($crowData[1]['dset']) ? json_decode($crowData[1]['dset'], true) : $crowData[1]['dset']);
			$pdata = collectChart($table, $ugla, $gsetdata);
			//return;
			$parray = json_encode(array(
				"boxes"   => array('cols' => $ugla['cols'], 'rows' => $ugla['rows']),
				'colb'    => $pdata['colb'],
				'cols'    => $pdata['cols'],
				'rowb'    => $pdata['rowb'],
				'rows'    => $pdata['rows'],
				'data'    => $pdata['dset'],
				'col_use' => $gsetdata['col_use'],
				'row_use' => $gsetdata['row_use']
			));
			$_POST = array('dset' => $parray, 'cmode' => $crowData[1]['cmode'], 'urow' => $crowData[1]['urow']);
			if (isset($crowData[1]['palette'])) {
				$_POST['palette'] = $crowData[1]['palette'];
			}
			updateLiveState($monitorKey, ($sblk - 1) + .75, $totalSections);
			$fname = require_once('graph.php');
			//$html.='<img src="/files/tmp/'.$fname.'.png"><br>';
			if (file_exists($fname) && filesize($fname) > 1) {
				$fph = fopen($fname, 'r');
				$html .= '<img width="' . $rbdata['s']['width'] . '" height="' . $rbdata['s']['height'] . '" src="data:image/png;base64,' .
					base64_encode(fread($fph, filesize($fname)))
					. '"><br>';
				fclose($fph);
			}
		} else {
			$html .= '<br>' . $table;
		}
		unset ($table, $bar, $svals);
	}

	$ufname = $baseDir . '/files/tmp/' . $_SESSION ['fileNameCsh'];
	diskFile::drops();
	$listToKill = array("tbd", "tch", "tst", "png");
	foreach ($listToKill as $appnd) {
		if (file_exists($ufname . '.' . $appnd)) {
			unlink($ufname . '.' . $appnd);
		}
	}
	return $html;
}

class wallE {
	private $filled = array();
	private $list = array();
	private $parsed = array();
	private $selects = array();
	private $sqlCache = array();
	private $plurals = array();
	private $plVals = array();

	function __construct($lar, $sar) {
		$this->list = $lar;
		$this->selects = $sar;
	}

	function getSys($key) {
		if (is_array($key) && count($key) > 0 && $key['sysval'] != '') {
			$key = $key['sysval'];
		}
		if (!@array_key_exists($key, $this->parsed)) {
			if(!is_numeric($key)){
				$this->parsed[$key] = dPgetSysVal($key);
			}else{
				$this->parsed[$key] = dPgetSysValSet($key);
			}
		}
	}

	function parsePLVals($key, $value = false, $query = false, $parent, $dval) {
		if (!is_array($this->plVals[$parent])) {
			$this->plVals[$parent] = array();
		}
		if (!array_key_exists($key, $this->plVals[$parent]) || $value === 'preSQL') {
			if ($value == 'sysval') {
				$this->getSys($query);
				$this->plVals[$parent][$key] = $this->parsed[$query];
			} elseif ($value == 'sql') {
				$res = mysql_query($query);
				$rar = array();
				if ($res && mysql_num_rows($res) > 0) {
					while ($row = mysql_fetch_object($res)) {
						$rar[$row->id] = $row->name;
					}
					$this->plVals[$parent][$key] = $rar;
					mysql_free_result($res);
					unset($rar);
				}
			} elseif ($value === 'preSQL') {
				if (!is_array($this->plVals[$parent][$key])) {
					$this->plVals[$parent][$key] = array();
				}
				$this->plVals[$parent][$key][$dval] = Validate::$query($dval);
			}
		}
		return $this->plVals[$parent][$key];
	}

	function polyCase($key) {
		$res = false;
		if (array_key_exists($key, $this->list) && is_array($this->list[$key]) && isset($this->list[$key]['mode'])) {
			$res = $this->list[$key]['mode'];
		}
		return $res;
	}

	function pluriCase($key) {
		$res = false;
		if (array_key_exists($key, $this->list) && is_array($this->list[$key]) && $this->list[$key]['value'] === 'plural') {
			$res = true;
		}
		return $res;
	}

	function isReadOnly($key) {
		$res = false;
		if (array_key_exists($key, $this->list)) {
			$vc = $this->list[$key];
			if (is_array($vc) && isset($vc['read-only']) && $vc['read-only'] === true) {
				$res = true;
			}
		}
		return $res;
	}

	function extraMode($key) {
		$res = FALSE;
		if (array_key_exists($key, $this->list) && is_array($this->list[$key]) && isset($this->list[$key]['xtype'])) {
			$res = $this->list[$key]['xtype'];
		}
		return $res;
	}

	function instant($key) {
		$res = false;
		if (array_key_exists($key, $this->list) && !is_array($this->list[$key]) || (is_array($this->list[$key]) && !isset($this->list[$key]['delay']))) {
			$res = true;
		}
		return $res;
	}

	function isComplex($key) {
		if (array_key_exists($key, $this->list) && is_array($this->list[$key])) {
			if (array_key_exists('bfield', $this->list[$key]) && !is_null($this->list[$key]['bfield'])) {
				return $this->list[$key]['bfield'];
			}
		}
		return false;
	}

	function getParsed($key, $val, $multi = false) {
		if ($multi === true) {
			$vals = explode(',', $val);
			if (count($vals) > 0) {
				foreach ($vals as &$vx) {
					if (!strstr($vx, '-')) {
						$rval[] = $this->parsed[$key][$vx];
					} else {
						$parts = explode('-', $vx);
						$rval[] = $this->parsed[$key][$parts[0]]['kids'][$parts[1]];
					}
				}
			}
		} else {
			$rval = (isset($this->parsed[$key][$val]) ? $this->parsed[$key][$val] : false);
		}
		return $rval;
	}

	function value($key, $val, $row_id = FALSE) {
		$sqlCase = false;
		$ikey = $this->list[$key];
		if (isset($ikey) && @!is_array($ikey) || $val == '-') {
			$rval = $val;
		} else {
			if (isset($ikey['query']) && (!is_array($ikey['query']) && array_key_exists($ikey['query'], $this->parsed)) && (isset($ikey['value']) && $ikey['value'] === 'sysval')) {
				//$rval = $this->parsed[$key][$val];
				$rval = $this->getParsed($ikey['query'], $val, (isset($ikey['mode']) && $ikey['mode'] === 'multi'));
			} else {
				$ikval = (isset($ikey['value']) ? $ikey['value'] : false);
				//switch ($ikey['value']) {
				if ($ikval === 'sql') {
					//case 'sql':
					if (!is_array($val)) {
						if (!isset($this->sqlCache[$key][$val])) {
							$sql = sprintf($ikey['query'], $val);
							$sqlCase = true;
						} else {
							return $this->sqlCache[$key][$val];
						}
					} else {
						$str = array();
						foreach ($val as &$v) {
							$str[] = ' "' . $v . '" ';
						}
						$fs = implode(',', $str);
						$sql = sprintf($ikey['query'], $fs);
					}
					$res = mysql_query($sql);
					if ($res && mysql_num_rows($res) > 0) {
						$rval = mysql_fetch_array($res);
						$rval = $rval[0];
					} else {
						$rval = '&nbsp;';
					}
				} elseif ($ikval === 'sql-one') {

					//case 'sql-one':
					if (!is_array($val)) {
						$sql = sprintf($ikey['query'], $val);
						$sqlCase = true;
					} else {
						$str = array();
						foreach ($val as &$v) {
							$str[] = ' "' . $v . '" ';
						}
						$fs = implode(',', $str);
						//$feval='$sql='."sprintf('".$ikey['query']."',".$fs.");";
						//eval($feval);
						$sql = sprintf($ikey['query'], $fs);
					}
					$res = mysql_query($sql);
					$rar = array();
					$zrow = FALSE;
					if ($res && mysql_num_rows($res) > 0) {
						$dvals = $this->getSelects($key);
						while ($zrow = mysql_fetch_array($res)) {
							$rar[] = $dvals[$zrow[0]]['v'];
						}
						$rval = $rar;
						unset($rar);
					} else {
						$rval = '&nbsp;';
					}
					//break;
				} elseif ($ikval === 'preSQL') {

					//case 'preSQL':
					if (!is_null($val) && trim($val) != '' && $val != '&nbsp;' || $ikey['field'] != '') {
						if (array_key_exists('field', $ikey) && $ikey['field'] != '') {
							$ustr = $val . '", "' . $ikey['field'] . '"';
						} else {
							$ustr = $val;
						}
						//eval('$rval=Validate::'.$ikey['query'].'("'.$ustr.'");');
						$rval = Validate::$ikey['query']($ustr);
					}
					//break;
				} elseif ($ikval === 'sysval') {
					//case 'sysval':
					$this->getSys($ikey['query']);
					if (isset($ikey['mode']) && $ikey['mode'] == 'multi' && $val !== null) {
						/*$vals=explode(',',$val);
						if(count($vals) > 0){
							foreach ($vals as &$vx) {
								if(!strstr($vx,'-')){
									$rval[]=$this->parsed[$ikey['query']][$vx];
								}else{
									$parts=explode('-',$vx);
									$rval[]=$this->parsed[$ikey['query']][$parts[0]]['kids'][$parts[1]];
								}
							}
						}*/
						$rval = $this->getParsed($ikey['query'], $val, true);
					} else {
						if (is_array($ikey['query']) && count($ikey['query']) > 0 && $ikey['query']['sysval'] != '') {
							//$rval=$this->parsed[$ikey['query']['sysval']][$val];
							$rval = $this->getParsed($ikey['query']['sysval'], $val);
						} else {
							//$rval=$this->parsed[$ikey['query']][$val];
							$rval = $this->getParsed($ikey['query'], $val);
						}
					}
					//break;
				} elseif ($ikval === 'presql-db') {
					if ($ikey['field'] != '') {
						$ustr = '"' . $val . '", "' . $ikey['field'] . '" ,"' . $ikey['bfield'] . '" , "' . $row_id . '"';
						if (isset($ikey['xrole']) && $ikey['xrole'] != '') {
							$ustr .= ' , "' . $ikey['xrole'] . '"';
						}
					} else {
						$ustr = $val;
					}
					if (is_array($ikey['query'])) {
						eval('$rval=Validate::$ikey["query"]["func"](' . $ustr . ');');
						$this->getSys($ikey['query']['sysval']);
						//$rval=$this->parsed[$ikey['query']['sysval']][$rval];
						$rval = $this->getParsed($ikey['query']['sysval'], $rval);
					} else {
						eval('$rval=Validate::$ikey["query"](' . $ustr . ');');
					}
					//}
					//break;
				} elseif ($ikval === 'sql-db') {
					//case 'sql-db':
					if (is_array($val)) {
						$str = array();
						foreach ($val as &$v) {
							$str[] = ' "' . $v . '" ';
						}
						$fs = implode(',', $str);
					} else {
						$fs = "$val";
					}
					$sql = sprintf($ikey['query']['sql'], $fs);
					$res = mysql_query($sql);
					if ($res && mysql_num_rows($res) > 0) {
						$rval = mysql_fetch_array($res);
						$rval1 = $rval[0];
						$this->getSys($ikey['query']['sysval']);
						$rval = $this->parsed[$ikey['query']['sysval']][$rval1];
						mysql_free_result($res);
					} else {
						$rval = '&nbsp;';
					}
					//break;
				} elseif ($ikval === 'plural') {

					//case 'plural':
					if ($ikey['query']['set'] != '') {
						$dquery = sprintf($ikey['query']['set'], (is_array($val) ? $val[1] : $val));
						$lres = mysql_query($dquery);
						if ($lres && mysql_num_rows($lres) > 0) {
							$fnames = array();
							$inames = array();
							$visibility = array();
							foreach ($ikey['query']['fields'] as $fld_name => &$fld_value) {
								if (is_array($fld_value)) {
									$fnames[] = $fld_value['title'];
									$inames[] = $fld_name;
									$visibility[] = (isset($fld_value['form']) ? false : true);
								} else {
									$fnames[] = $fld_value;
									$inames[] = $fld_name;
									$visibility[] = true;
								}
							}
							$restr = array();
							$rind = 0;
							$rvars = array();
							$datarow = array();
							while ($row = mysql_fetch_assoc($lres)) {
								$restr1 = array();
								$datarow[$rind] = array();
								foreach ($ikey['query']['fields'] as $fv => &$frv) {
									if (is_array($frv) && $frv['query'] != '') {
										if ($rind === 0 || !array_key_exists($row[$fv], $this->plVals[$key][$fv])) {
											//$this->getSys($frv['query']);
											$rvars[] = $this->parsePLVals($fv, $frv['value'], $frv['query'], $key, $row[$fv]);
											//$rvars[]=$this->parsed//[$frv['query']];
										}
										//$restr1[]=$this->parsed[$frv['query']][$row[$fv]];
										$restr1[] = $this->plVals[$key][$fv][$row[$fv]];
										$datarow[$rind][] = $row[$fv];
									} else {
										if (!isset ($frv['form']) || !is_array($frv)) {
											$restr1[] = $row[$fv];
										}

										if (isset ($frv ['xtype']) && $frv ['xtype'] == 'date') {
											if (( int )$row [$fv] > 0) {
												$datarow [$rind] [] = $row [$fv];
											} else {
												$datarow [$rind] [] = '';
											}
										} else {
											$datarow [$rind] [] = $row [$fv];
										}

										if ($rind === 0) {
											if (is_array($frv)) {
												if (isset($frv['xtype']) && $frv['xtype'] == 'date') {
													$rvars[] = 'date';
												} elseif (isset($frv['query'])) {
													$rvars[] = 'select';
												} else {
													$rvars [] = 'plain';
												}
											} else {
												$rvars[] = 'plain';
											}
										}
									}
								}
								if ($rind === 0) {
									$this->pluralStore($key, 'header', $fnames);
									$this->pluralStore($key, 'inames', $inames);
									$this->pluralStore($key, 'columns', $rvars);
									$this->pluralStore($key, 'visibility', $visibility);
								}

								$restr[$rind] = implode('|', $restr1);
								$rind++;

							}
							if ($rind > 0) {
								$this->pluralStore($key, 'data', array((strstr($key, 'household') ? $row_id : $val),
									$datarow
								));
								$rval = implode('; ', $restr);
							}

						}
					}
					//break;
				} else {
					/*default:*/
					$rval = $val;
					//break;

					//}
				}

			}
		}

		if (isset($ikey['transit']) && $ikey['transit'] === true && ($rval == '&nbsp;' || !$rval) && $val != '') {
			$rval = $val;
		}
		if ( /*$rval!='0' || */
			((is_string($rval) && strlen($rval) == 0) || (is_array($rval) && count($rval) == 0)) || is_null($rval)
		) {
			$rval = '&nbsp;';
		} elseif (!is_array($rval)) {
			$rval = trim($rval);
		}
		return $rval;
	}

	function pluralStore($key, $mode, $val = '', $parent = "") {
		if (!is_array($this->plurals[$key])) {
			$this->plurals[$key] = array();
		}
		if (!is_array($this->plurals[$key][$mode])) {
			$this->plurals[$key][$mode] = array();
		}
		if ($mode === 'data') {
			if (!is_array($this->plurals[$key][$mode])) {
				$this->plurals[$key][$mode] = array();
			}
			if (is_array($val[0])) {
				$this->plurals[$key][$mode][$val[0][1]] = $val[1];
			} else {
				$this->plurals[$key][$mode][$val[0]] = $val[1];
			}
		} else {
			if (!in_array($key, $this->plurals)) {
				$this->plurals[$key][$mode] = $val;
			}
		}
	}

	function getPData($key) {
		$res = (isset($this->plurals[$key]) ? $this->plurals[$key] : '______');
		return $res;
	}

	function reverse($key, $val) {
		if (@!is_array($this->list[$key]) || $val === '-') {
			if (is_object($val)) {
				$val = $val->r;
			}
			return $val;

		} else {
			if ($this->list[$key]['value'] === 'sql' && $this->list[$key]['rquery'] != '') {
				$sql = sprintf($this->list[$key]['rquery'], $val);
				$res = mysql_query($sql);
				if ($res) {
					$tval = mysql_fetch_array($res);
					return $tval[0];
				}
			} else {
				$this->value($key, $val);
			}
			$ntar = $this->parsed[$this->list[$key]['query']];
			if (is_array($ntar) && count($ntar) > 0) {
				foreach ($ntar as $nid => &$nval) {
					if (strtolower($nval) == $val) {
						return $nid;
					}
				}
			} else {
				return $val;
			}
		}
	}

	function getList() {
		return $this->list;
	}

	function transform($ar) {
		$res = array();
		foreach ($ar as $aid => &$av) {
			$res[] = array('r' => $aid, 'v' => $av);
		}
		return $res;
	}

	function getSelects($key) {
		global $selectsCache;
		$ikey = $this->list[$key];
		$vid = '';
		$complex = false;
		if (isset($ikey['value'])) {
			if ($ikey['value'] == 'sql' || $ikey['value'] == 'preSQL' || $ikey['value'] == 'sql-one') {
				$uzip = 'sql';
			} elseif ($ikey['value'] == 'presql-db' || $ikey['value'] == 'sysval') {
				if (is_array($ikey['query'])) {
					$uzip = 'sysval';
					$complex = true;
				} else {
					$uzip = $ikey['value'];
				}
			} else {
				$uzip = $ikey['value'];
			}

		}

		if (!is_array($ikey) && $ikey != '') {
			return 'plain';
		}
		if (is_array($ikey) && array_key_exists('read-only', $ikey) && $ikey['read-only'] === TRUE) {
			return 'read-only';
		}
		switch ($uzip) {
			case 'sysval':
				$this->value($key, '');
				if (!$complex) {
					$vid = $this->transform($this->parsed[$ikey['query']]);
				} else {
					$vid = $this->transform($this->parsed[$ikey['query']['sysval']]);
				}
				break;
			case 'sql':
				if (array_key_exists($key, $this->selects)) {
					$vid = array();
					if (count($this->selects) > 0) {
						$sql = $this->selects [$key];
						$sql_enc = md5($sql);
						/*if (array_key_exists ( $sql_enc, $selectsCache )) {
						$vid = $selectsCache [$sql_enc];
						} else {*/
						$res = mysql_query($sql);
						if ($res && mysql_num_rows($res) > 0) {
							while ($row = mysql_fetch_assoc($res)) {
								$vid [] = array('r' => $row ['id'], 'v' => $row ['name']);
							}
							mysql_free_result($res);
						}
						//$selectsCache[$sql_enc]=$vid;

						//}
					}
				}
				break;
			case 'sql-db':
				$this->getSys($ikey['query']['sysval']);
				$vid = $this->transform($this->parsed[$ikey['query']['sysval']]);
				break;

			case 'plural':
				$vid = 'plural';
				break;
			default:
				$vid = 'plain';
				break;
		}
		return $vid;
	}
}

function collectChart(&$table, &$boxes, &$gsed) {
	require_once 'phpQuery-onefile.php';
	phpQuery::newDocumentHTML($table, 'utf-8');
	$cols = array();
	$rows = array();
	$rowb = array();
	$colb = array();
	$dataset = array();
	$thead = pq("thead");
	$tbody = pq("tbody");
	$clp = $clpx = $ocols = $tcols = 0;
	//	$colall = $j("#colall").is(":checked");
	$colall = FALSE;
	if ($colall === false) {
		//$j("tr", $thead).each(function(i){
		foreach (pq("tr", $thead) as $i => $rowhead) {
			$cols[$i] = array();
			foreach (pq("th[data-ptitle]", $rowhead)->filter(':not(.missgr)') as $roh) {
				//$j("th[data-ptitle]", this).filter(":not(.missgr)").each(function(){

				$clp = pq($roh)->attr("data-ptitle"); //$j(this).attr("data-ptitle");
				$tcols = pq($roh)->attr("colspan"); //$j(this).attr("colspan");
				$ocols = pq($roh)->attr("data-ocols"); //$j(this).attr("data-ocols");
				if ($tcols > 1) {
					$tcols = $ocols;
				}
				$clpx = array_push($cols[$i], array($tcols, $clp));
				if ($i == 0) {
					array_push($colb, array(($clpx - 1), $clp));
				}
			}
		}
	} else {
		$cols[] = (array(1, 'All'));
	}

	$rsp = 0;
	$nobj = FALSE;
	$tdtxt = $rpos = $use_next = true;
	$needcells = count($boxes['rows']);
	$rspleft = 0;
	$noclass = false;
	$vdc = "vdata";
	$sudc = "summr";
	$tct = "tcol";
	$pcell = "perc";
	$migro = "missgr";
	$tcl = '';
	//$j("tbody > tr ", $table).filter(":not(.jkdata)").each(function(y){
	foreach (pq("tr", $tbody)->filter(":not(.jkdata)") as $y => $trb) {
		//if (!$j(this).hasClass("itog")) {
		if (!pq($trb)->hasClass('itog')) {
			//$j("td", this).each(function(yd){
			foreach (pq("td", $trb) as $yd => $td) {
				if ($use_next === true) {
					$tcl = pq($td)->attr("class"); //$j(this).attr("class");
					$vcs = strstr($tcl, $vdc);
					$scs = strstr($tcl, $sudc);
					$tcs = strstr($tct, $sudc);
					$pcs = strstr($tcl, $pcell);
					$mit = strstr($tcl, $migro);
					$rsp = pq($td)->attr("rowspan"); //$j(this).attr("rowspan");
					if (!is_numeric($rsp)) {
						$rsp = 1;
					}
					if (!$vcs && !$scs && !$tcs && !$pcs && !$mit) {
						$tdtxt = pq($td)->attr("data-rtitle"); //$j(this).attr("data-rtitle");
						if (!$tdtxt || strlen($tdtxt) == 0) {
							$tdtxt = pq($td)->text(); //$j(this).text();
							//crsp--;
						}
						/*else{
								   //crsp=rsp;
								   }*/
						$rpos = array_push($rows, array($rsp, $tdtxt));
						if ($yd == 0 /*&& crsp == rsp*/) {

							//crsp--;
							$nobj = pq($td)->next();
							$noclass = pq($nobj)->attr("class");
							if (!strstr($noclass, $vdc) && !strstr($noclass, $sudc) && !strstr($noclass, $pcell)) {
								$rowb[] = array(($rpos - 1), $tdtxt);
								$rows[] = array(1, pq($nobj)->text());
								$use_next = false;
							}
						}
					} else
						if (((!$colall && !$scs) || ($colall === true && $scs === true && !$vcs)) && !$tcs && !$pcs && $use_next === true) {
							if (!is_array($dataset[$y])) {
								$dataset[$y] = array();
							}
							$tdt = trim(pq($td)->text());
							if (!is_numeric($tdt) || strlen($tdt) === 0) {
								$tdt = 0;
							}
							$dataset[$y][] = $tdt;
						}

				} else {
					$use_next = true;
				}
			}
		}
	}
	if (count($dataset) == 0 && count(pq("tr", $tbody)->elements) == 1) {
		foreach (pq("tr", $tbody)->find("td:not(.summr):not(.rowhead)") as $zx => $xt) {
			if (!is_array($dataset[0])) {
				$dataset[0] = array();
			}
			$tdt = trim(pq($xt)->text());
			if (strlen($tdt) == 0 || !is_numeric($tdt)) {
				$tdt = 0;
			}
			$dataset[0][] = $tdt;
		}
	}


	$colsInPart = 0;
	$ndataset = array();
	$xepos = 0;
	$ncols = array(array());
	$ncolb = 0;
	$nrows = array();
	$nrowb = 0;
	if (count($colb) > 0 && $gsed['col_use'] === 'xcall') {
		//perform aggregation of columns into parent
		for ($pi = 0, $pl = count($colb); $pi < $pl; $pi++) {
			$colsInPart = $cols[0][$colb[$pi][0]][0];
			$ncols[0][] = array(1, $cols[0][$colb[$pi][0]][1]);
			if ($colsInPart > 0) {
				for ($xe = 0; $xe < $colsInPart; $xe++) {
					for ($y = 0, $yl = count($dataset); $y < $yl; $y++) {
						if (!$ndataset[$y] && !is_array($ndataset[$y])) {
							$ndataset[$y] = array();
						}
						if (!$ndataset[$y][$pi]) {
							$ndataset[$y][$pi] = 0;
						}
						$ndataset[$y][$pi] += intval($dataset[$y][($xepos + $xe)]);
					}
				}
				$xepos += $xe;
			}
		}
		$ncolb = array();
	} else {
		$ndataset = $dataset;
		$ncols = $cols;
		$ncolb = $colb;
	}

	if (count($rowb) > 0 && $gsed['row_use'] === 'ycall') {
		//perform aggregation of rowss into parent
		$rdataset = array();
		$rowOffset = 0;
		for ($pi = 0, $pl = count($rowb); $pi < $pl; $pi++) {
			$rowsInPart = $rows[$rowb[$pi][0]][0];
			$nrows[] = array(1, $rows[$rowb[$pi][0]][1]);
			$rdataset[$pi] = array();
			if ($rowsInPart > 0) {
				for ($xe = (0 + $rowOffset); $xe < ($rowsInPart + $rowOffset); $xe++) {
					for ($y = 0, $yl = count($ndataset[$xe]); $y < $yl; $y++) {
						if (!is_numeric($rdataset[$pi][$y])) {
							$rdataset[$pi][$y] = 0;
						}
						if (is_array($ndataset[$xe])) {
							$rdataset[$pi][$y] += intval($ndataset[$xe][$y]);
						} else {
							if (!is_numeric($rdataset[$pi])) {
								$rdataset[$pi] = 0;
							}
							$rdataset[$pi] += intval($ndataset[$xe][$y]);
						}
					}
				}
				$rowOffset = $rowsInPart;
				$xepos += $xe;
			}
		}
		$nrowb = array();
		$ndataset = $rdataset;
		unset($rdataset);

	} else {
		$nrows = $rows;
		$nrowb = $rowb;
	}


	return array("dset" => $ndataset, "cols" => $ncols, "rows" => $nrows, "colb" => $colb, "rowb" => $rowb);

}


?>