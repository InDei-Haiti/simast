<?php
require_once $AppUI->getSystemClass('systemExporter');

$fh=null;
$prefix='';
$outs=array();
function onlyVals($a){
	/*global $mwriter,$prefix;
	$s = '\''.serialize(array_values($a)).'\'';
	$mwriter->putstr($prefix.$s);*/
	return array_values($a);
}

function cleaner ($a){
	global $outs;
	$final=array();
	foreach ($a as $key=>$value) {
		if(!in_array($key,$outs)){
			$final[]=$value;
		}
	}
	return serialize($final);
}

function szip(&$str){
	$s=var_export($str,true);
	$str=null;
	return gzcompress($s,9);
}

class ExportWriter extends systemExporter {	

	function __construct($mode, $name){
		global $dPconfig;
		parent::__construct($mode,$name);
				
		fprintf($this->fh,'%s','$inCenter = "'.$dPconfig['current_center'].'";$arr = array();');
	}
	
	function store($title,&$data,$zkeys=array(),$headers,$keys,$multi = false,$iter = 0) {
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
	}
	
}



// get GETPARAMETER for client_id
$client_id = 1;

$q = new DBQuery();
$q->addTable('clients');
$clients = $q->loadHashListMine();

$q= new DBQuery();
$q->addTable('contacts');
$contacts = $q->loadHashListMine();

$q= new DBQuery();
$q->addTable('clinics');
$clinics = $q->loadHashListMine();

$q= new DBQuery();
$q->addTable('admission_caregivers');
$carez = $q->loadHashListMine();

$canRead = ! getDenyRead ( 'clients' );
if (! $canRead) {
	$AppUI->redirect ( "m=public&a=access_denied" );
}

//$pway=trim($_GET['todo']);
$pway='plain';
$tvar=array('excel','plain');

if (in_array($pway,$tvar)) {
	//export clients
	// Creating a workbook

	$mwriter = new ExportWriter($pway,str_replace(' ','_',$dPconfig ['company_name']).'-'.$dPconfig['current_center'] );
	// sending HTTP headers

	// The actual data


	// Creating a worksheet for clinics
	/*$worksheet = & $workbook->addWorksheet ( "clinics" );	$format_bold = & $workbook->addFormat ();
	$format_bold->setBold ();*/

	$headers = array ('clinic_name', 'clinic_phone1', 'clinic_phone2', 'clinic_fax', 'clinic_address1', 'clinic_address2', 'clinic_city', 'clinic_state', 'clinic_zip', 
						'clinic_primary_url', 'clinic_owner', 'clinic_description', 'clinic_type', 'clinic_email' );


	//$text = sprintf("%s\r\n","\"First Name\",\"Middle Name\",\"Last Name\",\"Entry Date\"");
	$q = new DBQuery ();
	$q->addTable ( 'clinics', 'cli' );
	$q->innerJoin ( 'counselling_info', 'ci', 'cli.clinic_id = ci.counselling_clinic' );
	$q->addQuery ( 'distinct cli.*' );
	list($clinicsw,$kheads) = $q->loadListExport ();
	//writeWorksheet ( $worksheet, $format_bold, $headers, &$clinicsw, $headers );
	$mwriter->store('clinics',$clinicsw,$kheads,$headers,$headers);
	unset($clinicsw);

	//dumping caregivers

	// Creating a worksheet for clients
	/*$worksheet = & $workbook->addWorksheet ( "caregivers" );
	$format_bold = & $workbook->addFormat ();
	$format_bold->setBold ();*/

	$headers = array('client_adm_no', 'client_first_name', 'client_other_name', 'client_last_name', 'client_entry_date','client_notes','client_status','client_custom','client_gender',
					 'client_dob','client_doa','client_center','client_nhif','client_nhif_n','client_immun','client_immun_n','client_lvd','client_lvd_form','client_obsolete','clinic_name');
	reset($clients);
	$clientStatusCount=array(1=>0,9=>0,'rest'=>0);
	foreach ($clients as $clnt) {
		if($clnt['client_status'] == '1'){
			++$clientStatusCount[1];
		}elseif ($clnt['client_status'] == '9'){
			++$clientStatusCount[9];
		}else {
			++$clientStatusCount['rest'];
		}
	}
	reset($clients);
	$kheads=array_keys(current($clients));	
	$kheads[]='clinic_name';
	$clientw= array_map('onlyVals',$clients);	
	$cenpos= array_search('client_center',$kheads);	
	foreach ($clientw as $key => $cvals) {
		$clientw[$key][]=$clinics[$cvals[$cenpos]]['clinic_name'];
	}
	$mwriter->store('clients',$clientw,$kheads,$headers,$headers);
	unset($clientw);

	$headers = array ('client_adm_no', 'client_first_name', 'client_other_name', 'client_last_name','fname','lname','age','health_status','marital_status','educ_level','employment',
						'idno','mobile','reason','datesoff','role','relationship','status' );

	$caresw =$carez;
	reset($caresw);
	$kheads=array_keys(current($caresw));
	foreach ($caresw as $key => $vals){
		$cclient=$clients[$vals['client_id']];
		$caresw[$key]['client_adm_no']=$cclient['client_adm_no'];
		$caresw[$key][]=$cclient['client_first_name'];
		$caresw[$key][]=$cclient['client_other_name'];
		$caresw[$key][]=$cclient['client_last_name'];
	}
	//writeWorksheet ( $worksheet, $format_bold, $headers, &$caresw, $headers );
	$kheads=array_merge($kheads,array('client_adm_no','client_first_name','client_other_name','client_last_name'));
	$caresw=array_map('onlyVals',$caresw);
	$mwriter->store("caregivers",&$caresw,$kheads,$headers,$headers);
	unset($caresw);
	
	$headers = array ('client_adm_no', 'client_first_name', 'client_other_name', 'client_last_name', 'clinic_name', 'counselling_entry_date', 'counselling_clinic', 'counselling_staff_id',
					'counselling_staff_name', 'contact_first_name', 'contact_other_name', 'contact_last_name', 'counselling_referral_source', 'counselling_total_orphan', 
					'counselling_dob', 'counselling_age_yrs', 'counselling_age_months', 'counselling_age_status', 'counselling_place_of_birth', 'counselling_birth_area', 
					'counselling_mode_birth', 'counselling_gestation_period', 'counselling_birth_weight', 'counselling_mothers_status_known', 'counselling_mother_antenatal', 
					'counselling_mother_pmtct', 'counselling_mother_illness_pregnancy', 'counselling_mother_illness_pregnancy_notes', 'counselling_breastfeeding', 
					'counselling_breastfeeding_duration', 'counselling_other_breastfeeding_duration', 'counselling_child_prenatal', 'counselling_child_single_nvp', 
					'counselling_child_nvp_date', 'counselling_child_nvp_notes', 'counselling_child_azt', 'counselling_child_azt_date', 'counselling_no_doses', 
					'counselling_mother_treatment', 'counselling_mother_art_pregnancy', 'counselling_mother_date_art', 'counselling_mother_cd4', 
					'counselling_mother_date_cd4', 'counselling_determine_date', 'counselling_determine', 'counselling_bioline_date', 'counselling_bioline', 
					'counselling_unigold_date', 'counselling_unigold', 'counselling_elisa_date', 'counselling_elisa', 'counselling_pcr1_date', 'counselling_pcr1', 
					'counselling_pcr2_date', 'counselling_pcr2', 'counselling_rapid12_date', 'counselling_rapid12', 'counselling_rapid18_date', 'counselling_rapid18', 
					'counselling_other_date', 'counselling_other', 'counselling_notes','counselling_other_notes','counselling_custom','counselling_vct_camp',
					'counselling_vct_camp_site','counselling_return','counselling_client_code','counselling_partner_code','counselling_area','counselling_gender',
					'counselling_marital','counselling_client_seen', 'counselling_final','counselling_dis_couple','counselling_mother_treatment_where',
					'counselling_mother_pmtct_where','counselling_mother_antenatal_where','counselling_mother_cd4_note','counselling_positive_ref',
					'counselling_positive_ref_notes','counselling_admission_date','counselling_referral_source_old','counselling_referral_source_notes');
	/*
	* INTAKE & PCR
	*/
	$rowcount = 0;
	//$text = sprintf("%s\r\n","\"First Name\",\"Middle Name\",\"Last Name\",\"Entry Date\"");
	$q = new DBQuery ();
	$q->addTable ( 'counselling_info', 'ci' );

	$iter=0;
	$last=false;
	list($counselling_records,$kheads,$repeat) = $q->loadListExport ();

	if(count($counselling_records) == 0){
		$iter=1;
		$repeat=false;
	}else{
		$clipos=array_search('counselling_client_id',$kheads);
		$conpos=array_search('counselling_staff_id',$kheads);
		$hospos=array_search('counselling_clinic',$kheads);
		while($repeat || $iter == 0 || $last === true){
			foreach ($counselling_records as $key => $vars) {
				$cclient=$clients[$vars[$clipos]];
				$counselling_records[$key][]=$cclient['client_adm_no'];
				$counselling_records[$key][]=$cclient['client_first_name'];
				$counselling_records[$key][]=$cclient['client_other_name'];
				$counselling_records[$key][]=$cclient['client_last_name'];
				$counselling_records[$key][]=$clinics[$vars[$hospos]]['clinic_name'];
				$cstaff=$contacts[$vars[$conpos]];
				$counselling_records[$key][]=$cstaff['contact_first_name'].' '.$cstaff['contact_other_name'].' '.$cstaff['contact_last_name'];
			}
			$kheads=array_merge($kheads,array('client_adm_no','client_first_name','client_other_name','client_last_name','clinic_name','counselling_staff_name'));
			$mwriter->store("Intake_PCR" ,&$counselling_records,$kheads,$headers,$headers,$repeat,$iter);
			unset($counselling_records);
			if(!$last){
				list($counselling_records,$kheads,$repeat) = $q->loadListExport (null,true);
				if(count($counselling_records) > 0 && $repeat === false){
					$last=true;
				}
				++$iter;
			}else {
				$last = false;
			}
		}
	}

	$iter=0;
	$last=false;
	// Creating a worksheet for clinical visits

	//'clinical_child_condition',
	$headers = array ('client_adm_no', 'client_first_name', 'client_other_name', 'client_last_name', 'clinic_name', 'clinical_entry_date', 'clinical_clinic_id', 'clinical_staff_id',
	 					'clinical_staff_name', 'contact_first_name', 'contact_other_name', 'contact_last_name', 'clinical_age_yrs', 'clinical_age_months', 'clinical_child_attending',
	 					'clinical_caregiver_attending', 'clinical_caregiver', 'clinical_illness', 'clinical_illness_notes', 'clinical_diarrhoea', 'clinical_vomiting', 
	 					'clinical_current_complaints','clinical_complaints', 'clinical_bloodtest_date', 'clinical_bloodtest_cd4', 'clinical_bloodtest_cd4_percentage', 
	 					'clinical_bloodtest_viral', 'clinical_bloodtest_hb', 'clinical_xray_results','clinical_ctscan','clinical_astal', 'clinical_other_results', 
	 					'clinical_weight', 'clinical_height', 'clinical_zscore', 'clinical_muac', 'clinical_hc', 'clinical_child_unwell', 'clinical_temp', 'clinical_resp_rate', 
	 					'clinical_heart_rate', 'clinical_general', 'clinical_pallor', 'clinical_jaundice', 'clinical_examination_dehydration', 'clinical_examination_lymph', 
	 					'clinical_mouth','clinical_mouth_thrush', 'clinical_mouth_ulcer','clinical_teeth','clinical_teeth_opt', 'clinical_ears','clinical_ears_opt', 
	 					'clinical_chest', 'clinical_chest_clear','clinical_chest_creps', 'clinical_skin_clear', 'clinical_cardiovascular', 'clinical_skin','clinical_skin_opt', 
	 					'clinical_clubbing', 'clinical_abdomen', 'clinical_neurodevt','clinical_cns','clinical_eyes','clinical_eyes_opt','clinical_muscle', 'clinical_musculoskeletal', 
	 					'clinical_oedema', 'clinical_adherence', 'clinical_adherence_notes', 'clinical_diarrhoea_type', 'clinical_dehydration', 'clinical_pneumonia', 
	 					'clinical_chronic_lung', 'clinical_lung_disease', 'clinical_tb','clinical_tb_treat', 'clinical_tb_treatment_date', 'clinical_pulmonary', 
	 					'clinical_discharging_ears', 'clinical_other_diagnoses','clinical_dss', 'clinical_malnutrition', 'clinical_growth', 'clinical_assessment_notes', 
	 					'clinical_investigations','clinical_investigations_notes', 'clinical_investigations_blood', 'clinical_investigations_xray',  'clinical_other_drugs', 
	 					'clinical_new_drugs', 'clinical_on_arvs', 'clinical_arv_drugs','clinical_arv_on','clinical_arv_on_adh','clinical_arv_recomends', 'clinical_tb_treatment',
	 					'clinical_tb_status','clinical_tb_notes', 'clinical_arv_notes','clinical_stage', 'clinical_who_stage', 'clinical_who_current', 'clinical_who_reason', 
	 					'clinical_tb_drugs', 'clinical_tb_drugs_notes', 'clinical_septrin', 'clinical_vitamins', 'clinical_treatment_status', 'clinical_arv_reason', 
	 					'clinical_nutritional_support', 'clinical_nutritional_notes', 'clinical_referral','clinical_referral_old', 'clinical_referral_other', 'clinical_next_date', 
	 					'clinical_notes','clinical_custom','clinical_arv_drugs_other','clinical_request','clinical_request_list','clinical_other','clinical_therapy_stage' );

	$q = new DBQuery ();
	$q->addTable ( 'clinical_visits', 'cv' );
	
	list($clinical_visits,$kheads,$repeat) = $q->loadListExport ();
	
	if(count($clinical_visits) == 0){
		$iter=1;
		$repeat=false;
	}else{
		$clipos=array_search('clinical_client_id',$kheads);
		$conpos=array_search('clinical_staff_id',$kheads);
		$refpos=array_search('clinical_referral',$kheads);
		$hospos=array_search('clinical_clinic_id',$kheads);
		while($repeat || $iter == 0 || $last === true){
			foreach ($clinical_visits as $key => $vars) {
				$cclient=$clients[$vars[$clipos]];
				$clinical_visits[$key][]=$cclient['client_adm_no'];
				$clinical_visits[$key][]=$cclient['client_first_name'];
				$clinical_visits[$key][]=$cclient['client_other_name'];
				$clinical_visits[$key][]=$cclient['client_last_name'];
				$clinical_visits[$key][]=$clinics[$vars[$hospos]]['clinic_name'];
				$cstaff=$contacts[$vars[$conpos]];
				$clinical_visits[$key][]=$cstaff['contact_first_name'].' '.$cstaff['contact_other_name'].' '.$cstaff['contact_last_name'];
				$cstaff=$contacts[$vars[$refpos]];
				$clinical_visits[$key][]=$cstaff['contact_first_name'].' '.$cstaff['contact_other_name'].' '.$cstaff['contact_last_name'];
			}
			$kheads=array_merge($kheads,array('client_adm_no','client_first_name','client_other_name','client_last_name','clinic_name','clinical_staff_name','clinical_referral_name'));
			$mwriter->store("Clinical_visits" ,&$clinical_visits,$kheads,$headers,$headers,$repeat,$iter);
			unset($clinical_visits);
			if(!$last){
				list($clinical_visits,$kheads,$repeat) = $q->loadListExport (null,true);
				if(count($clinical_visits) > 0 && $repeat === false){
					$last=true;
				}
				++$iter;
			}else {
				$last = false;
			}
		}
	}
	unset($clinical_visits,$kheads);

	// Creating a worksheet for counselling visits

	$headers = array ('client_adm_no', 'client_first_name', 'client_other_name', 'client_last_name', 'clinic_name', 'counselling_staff_id', 'counselling_visit_staff_name', 
					  'contact_first_name', 'contact_other_name', 'contact_last_name', 'counselling_center_id', 'counselling_entry_date', 'counselling_visit_type', 
					  'counselling_caregiver_fname', 'counselling_caregiver_lname', 'counselling_caregiver_age', 'counselling_caregiver_relationship', 
					  'counselling_caregiver_marital_status', 'counselling_caregiver_educ_level', 'counselling_caregiver_employment', 'counselling_caregiver_income_level', 
					  'counselling_caregiver_idno', 'counselling_caregiver_mobile', 'counselling_caregiver_residence', 'counselling_child_issues', 'counselling_other_issues', 
					  'counselling_caregiver_issues', 'counselling_caregiver_other_issues', 'counselling_caregiver_issues2', 'counselling_caregiver_other_issues2', 
					  'counselling_child_knows_status', 'counselling_otheradult_knows_status','counselling_otheradult_knows_status_old', 'counselling_disclosure_response', 
					  'counselling_disclosure_state', 'counselling_secondary_caregiver_knows', 'counselling_primary_caregiver_tested', 'counselling_father_status', 
					  'counselling_mother_status', 'counselling_caregiver_status', 'counselling_father_treatment', 'counselling_mother_treatment', 
					  'counselling_caregiver_treatment', 'counselling_stigmatization_concern', 'counselling_counselling_services', 'counselling_other_services', 
					  'counselling_notes' ,'counselling_custom','counselling_second_indent','counselling_referer','counselling_referer_other','counselling_next_visit');

	$iter=0;
	$last=false;
	$q = new DBQuery ();
	$q->addTable ( 'counselling_visit', 'cv' );
	list($counselling_visits,$kheads,$repeat) = $q->loadListExport ();

	if(count($counselling_visits) == 0){
		$iter=1;
		$repeat=false;
	}else{
		$clipos=array_search('counselling_staff_id',$kheads);
		$conpos=array_search('counselling_staff_id',$kheads);
		$hospos=array_search('counselling_center_id',$kheads);
		while($repeat || $iter == 0 || $last === true){
			foreach ($counselling_visits as $key => $vars) {
				$cclient=$clients[$vars[$clipos]];
				$counselling_visits[$key][]=$cclient['client_adm_no'];
				$counselling_visits[$key][]=$cclient['client_first_name'];
				$counselling_visits[$key][]=$cclient['client_other_name'];
				$counselling_visits[$key][]=$cclient['client_last_name'];
				$counselling_visits[$key][]=$clinics[$vars[$hospos]]['clinic_name'];
				$cstaff=$contacts[$vars[$conpos]];
				$counselling_visits[$key][]=$cstaff['contact_first_name'].' '.$cstaff['contact_other_name'].' '.$cstaff['contact_last_name'];
			}
			$kheads=array_merge($kheads,array('client_adm_no','client_first_name','client_other_name','client_last_name','clinic_name','counselling_visit_staff_name'));
			$mwriter->store("Counselling_visits" ,&$counselling_visits,$kheads,$headers,$headers,$repeat,$iter);
			unset($counselling_visits);
			if(!$last){
				list($counselling_visits,$kheads,$repeat) = $q->loadListExport (null,true);
				if(count($counselling_visits) > 0 && $repeat === false){
					$last=true;
				}
				++$iter;
			}else {
				$last = false;
			}
		}
	}
	// Creating a worksheet for social visits

	$headers = array ('client_adm_no', 'client_first_name', 'client_other_name', 'client_last_name', 'clinic_name', 'social_id', 'social_staff_id', 'social_staff_name', 
					  'contact_first_name', 'contact_other_name', 'contact_last_name', 'social_clinic_id', 'social_entry_date', 'social_client_status', 'social_visit_type', 
					  'social_death', 'social_death_notes', 'social_death_date', 'social_caregiver_pri_change', 'social_caregiver_pri_change_notes','social_caregiver_sec_change', 
					  'social_caregiver_sec_change_notes', 'social_caregiver_fname', 'social_caregiver_lname', 'social_caregiver_age', 'social_caregiver_status', 
					  'social_caregiver_relationship', 'social_caregiver_education', 'social_caregiver_employment', 'social_caregiver_income', 'social_caregiver_idno', 
					  'social_caregiver_mobile', 'social_caregiver_health', 'social_caregiver_health_child_impact','social_caregiver_pri_health_child_impact',
					  'social_caregiver_sec_health_child_impact', 'social_residence_mobile', 'social_residence', 'social_caregiver_pri_employment_change', 
					  'social_caregiver_pri_new_employment', 'social_caregiver_pri_new_employment_desc','social_caregiver_sec_employment_change', 
					  'social_caregiver_sec_new_employment', 'social_caregiver_sec_new_employment_desc', 'social_caregiver_new_income', 'social_school_attendance', 
					  'social_school', 'social_reason_not_attending', 'social_relocation', 'social_iga', 'social_placement', 'social_succession_planning', 
					  'social_legal', 'social_nursing', 'social_transport', 'social_education', 'social_food', 'social_rent', 'social_solidarity', 
					  'social_direct_support', 'social_medical_support', 'social_medical_support_desc', 'social_other_support', 'social_othersupport_value', 
					  'social_permanency_value', 'social_succession_value', 'social_legal_value', 'social_nursing_value', 'social_transport_value', 
					  'social_education_value', 'social_food_value', 'social_rent_value', 'social_solidarity_value', 'social_directsupport_value', 
					  'social_medicalsupport_value', 'social_risk_level', 'social_notes', 'social_change','social_training', 'social_training_desc', 
					  'social_next_visit', 'social_referral', 'social_caregiver_pri', 'social_caregiver_sec', 'social_caregiver_pri_type', 
					  'social_caregiver_sec_type', 'social_nhf', 'social_nhf_y', 'social_nhf_n', 'social_immun', 'social_immun_y', 'social_immun_n', 
					  'social_caregiver_employment_change', 'social_caregiver_new_employment', 'social_caregiver_new_employment_desc', 'social_class_form', 
					  'social_caregiver_income','social_any_needs'  );	
	$iter=0;
	$last=false;
	$q = new DBQuery ();
	$q->addTable ( 'social_visit', 'sv' );
	list($social_visits,$kheads,$repeat) = $q->loadListExport ();
	
	if(count($social_visits) == 0){
		$iter=1;
		$repeat=false;
	}else{
		$clipos=array_search('social_client_id',$kheads);
		$conpos=array_search('social_staff_id',$kheads);
		$hospos=array_search('social_clinic_id',$kheads);
		while($repeat || $iter == 0 || $last === true){
			foreach ($social_visits as $key => $vars) {
				$cclient=$clients[$vars[$clipos]];
				$social_visits[$key][]=$cclient['client_adm_no'];
				$social_visits[$key][]=$cclient['client_first_name'];
				$social_visits[$key][]=$cclient['client_other_name'];
				$social_visits[$key][]=$cclient['client_last_name'];
				$social_visits[$key][]=$clinics[$vars[$hospos]]['clinic_name'];
				$cstaff=$contacts[$vars[$conpos]];
				$social_visits[$key][]=$cstaff['contact_first_name'].' '.$cstaff['contact_other_name'].' '.$cstaff['contact_last_name'];
			}
			$kheads=array_merge($kheads,array('client_adm_no','client_first_name','client_other_name','client_last_name','clinic_name','social_staff_name'));
			$mwriter->store("Social_visits" ,&$social_visits,$kheads,$headers,$headers,$repeat,$iter);
			unset($social_visits,$kheads);
			if(!$last){
				list($social_visits,$kheads,$repeat) = $q->loadListExport (null,true);
				if(count($social_visits) > 0 && $repeat === false){
					$last=true;
				}
				++$iter;
			}else {
				$last = false;
			}
		}
	}
	//writeWorksheet ( $worksheet, $format_bold, $headers, &$social_visits, $headers );
	

	// Creating a worksheet for social services details

	$headers = array ('client_adm_no', 'client_first_name', 'client_other_name', 'client_last_name', 'social_services_client_id', 'social_services_social_id', 'social_services_service_id', 
						'social_services_date','social_services_notes','social_services_custom','social_services_value','social_direct_support_desc','social_training_value' );
	$iter=0;
	$last=false;
	$q = new DBQuery ();
	$q->addTable ( 'social_services', 'ss' );
	/*$q->innerJoin ( 'clients', 'cl', 'cl.client_id = ss.social_services_client_id' );
	$q->addQuery ( 'cl.client_adm_no, cl.client_first_name, cl.client_other_name, cl.client_last_name, ss.*' );*/
	list($social_service_records,$kheads,$repeat) = $q->loadListExport ();

	if(count($social_service_records) == 0){
		$iter=1;
		$repeat=false;
	}else{
		$clipos=array_search('social_services_client_id',$kheads);
		while($repeat || $iter == 0 || $last === true){
			foreach ($social_service_records as $key => $vars) {
				$cclient=$clients[$vars[$clipos]];
				$social_service_records[$key][]=$cclient['client_adm_no'];
				$social_service_records[$key][]=$cclient['client_first_name'];
				$social_service_records[$key][]=$cclient['client_other_name'];
				$social_service_records[$key][]=$cclient['client_last_name'];
			}
			//writeWorksheet ( $worksheet, $format_bold, $headers, &$social_service_records, $headers );
			$kheads=array_merge($kheads,array('client_adm_no','client_first_name','client_other_name','client_last_name'));
			$mwriter->store("Social_services_details" ,&$social_service_records,$kheads,$headers,$headers,$repeat,$iter);
			unset($social_service_records,$kheads);
			if(!$last){
				list($social_service_records,$kheads,$repeat) = $q->loadListExport (null,true);
				if(count($social_service_records) > 0 && $repeat === false){
					$last=true;
				}
				++$iter;
			}else {
				$last = false;
			}
		}
	}
	// Creating a worksheet for nutritional visits
	

	$headers = array ('client_adm_no', 'client_first_name', 'client_other_name', 'client_last_name', 'clinic_name', 'nutrition_staff_id', 'nutrition_staff_name', 'contact_first_name', 
						'contact_other_name', 'contact_last_name', 'nutrition_entry_date', 'nutrition_center', 'nutrition_gender', 'nutrition_age_yrs', 'nutrition_age_months', 
						'nutrition_age_status', 'nutrition_caregiver_type', 'nutrition_caregiver_type_notes', 'nutrition_weight', 'nutrition_height', 'nutrition_zscore', 
						'nutrition_muac', 'nutrition_wfh', 'nutrition_wfa', 'nutrition_bmi', 'nutrition_blacktea', 'nutrition_whitetea', 'nutrition_bread', 'nutrition_porridge',
						'nutrition_water', 'nutrition_breastfeeding', 'nutrition_formula_milk', 'nutrition_carbohydrates', 'nutrition_meat', 'nutrition_pancake', 'nutrition_eggs', 
						'nutrition_legumes', 'nutrition_milk', 'nutrition_vegetables', 'nutrition_fruit', 'nutrition_diet_history_notes', 'nutrition_diet_history_others', 
						'nutrition_food_enrichment', 'nutrition_water_access', 'nutrition_water_purification', 'nutrition_water_purification_notes', 'nutrition_food_enrichment_notes', 
						'nutrition_quantity', 'nutrition_quality', 'nutrition_poor_preparation', 'nutrition_mixed_feeding', 'nutrition_unclean_drinking_water', 'nutrition_education', 
						'nutrition_counselling', 'nutrition_demonstration', 'nutrition_dietary_supplement', 'nutrition_nan', 'nutrition_unimix', 'nutrition_harvest_pro', 'nutrition_wfp', 
						'nutrition_insta', 'nutrition_rutf', 'nutrition_other', 'nutrition_other_service', 'nutrition_notes','nutrition_custom','nutrition_oedema','nutrition_beverages_title',
						'nutrition_beverages_notes','nutrition_ugali','nutrition_rice','nutrition_banan','nutrition_tubers','nutrition_wheat','nutrition_carbos_title','nutrition_carbos_notes',
						'nutrition_protein_title','nutrition_protein_notes','nutrition_fat','nutrition_issue_notes','nutrition_program','nutrition_program_other','nutrition_rendered',
						'nutrition_next_visit','nutrition_refer','nutrition_refer_other','nutrition_service_other','nutrition_child_attend','nutrition_care_attend','nutrition_care_who' );

	$iter=0;
	$last=false;
	$q = new DBQuery ();
	$q->addTable ( 'nutrition_visit', 'nv' );	
	list($nutrition_visits,$kheads,$repeat) = $q->loadListExport ();
	
	if(count($nutrition_visits) == 0){
		$iter=1;
		$repeat=false;
	}else{
		$clipos=array_search('nutrition_client_id',$kheads);
		$conpos=array_search('nutrition_staff_id',$kheads);
		$hospos=array_search('nutrition_center',$kheads);
		while($repeat || $iter == 0 || $last === true){
			foreach ($nutrition_visits as $key => $vars) {
				$cclient=$clients[$vars[$clipos]];
				$nutrition_visits[$key][]=$cclient['client_adm_no'];
				$nutrition_visits[$key][]=$cclient['client_first_name'];
				$nutrition_visits[$key][]=$cclient['client_other_name'];
				$nutrition_visits[$key][]=$cclient['client_last_name'];
				$nutrition_visits[$key][]=$clinics[$vars[$hospos]]['clinic_name'];
				$cstaff=$contacts[$vars[$conpos]];
				$nutrition_visits[$key][]=$cstaff['contact_first_name'].' '.$cstaff['contact_other_name'].' '.$cstaff['contact_last_name'];
			}
			//writeWorksheet ( $worksheet, $format_bold, $headers, &$nutrition_visits, $headers );
			$kheads=array_merge($kheads,array('client_adm_no','client_first_name','client_other_name','client_last_name','clinic_name','nutrition_staff_name'));
			$mwriter->store("Nutritional_visits" ,&$nutrition_visits,$kheads,$headers,$headers,$repeat,$iter);
			unset($nutrition_visits,$kheads);
			if(!$last){
				list($nutrition_visits,$kheads,$repeat) = $q->loadListExpo