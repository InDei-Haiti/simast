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
		parent::__construct($mode,$name);
		fprintf($this->fh,'%s','$arr = array();');
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

$pway=trim($_GET['todo']);
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

	$headers = array ('clinic_name', 'clinic_phone1', 'clinic_phone2', 'clinic_fax', 'clinic_address1', 'clinic_address2', 'clinic_city', 'clinic_state', 'clinic_zip', 'clinic_primary_url', 'clinic_owner', 'clinic_description', 'clinic_type', 'clinic_email' );


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

	$headers = array('client_adm_no', 'client_first_name', 'client_other_name', 'client_last_name', 'client_entry_date','client_notes','client_status','client_custom','client_gender','client_dob','client_doa','client_center','client_nhif','client_nhif_n','client_immun','client_immun_n');
	reset($clients);
	$kheads=array_keys(current($clients));	
	$clientw= array_map('onlyVals',$clients);		
	$mwriter->store('clients',$clientw,$kheads,$headers,$headers);
	unset($clientw);

	$headers = array ('client_adm_no', 'client_first_name', 'client_other_name', 'client_last_name','fname','lname','age','health_status','marital_status','educ_level','employment','idno','mobile','reason','datesoff','role','relationship','status' );

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
	
	$headers = array ('client_adm_no', 'client_first_name', 'client_other_name', 'client_last_name', 'clinic_name', 'counselling_entry_date', 'counselling_clinic', 'counselling_staff_id', 'counselling_staff_name', 'contact_first_name', 'contact_other_name', 'contact_last_name', 'counselling_referral_source', 'counselling_total_orphan', 'counselling_dob', 'counselling_age_yrs', 'counselling_age_months', 'counselling_age_status', 'counselling_place_of_birth', 'counselling_birth_area', 'counselling_mode_birth', 'counselling_gestation_period', 'counselling_birth_weight', 'counselling_mothers_status_known', 'counselling_mother_antenatal', 'counselling_mother_pmtct', 'counselling_mother_illness_pregnancy', 'counselling_mother_illness_pregnancy_notes', 'counselling_breastfeeding', 'counselling_breastfeeding_duration', 'counselling_other_breastfeeding_duration', 'counselling_child_prenatal', 'counselling_child_single_nvp', 'counselling_child_nvp_date', 'counselling_child_nvp_notes', 'counselling_child_azt', 'counselling_child_azt_date', 'counselling_no_doses', 'counselling_mother_treatment', 'counselling_mother_art_pregnancy', 'counselling_mother_date_art', 'counselling_mother_cd4', 'counselling_mother_date_cd4', 'counselling_determine_date', 'counselling_determine', 'counselling_bioline_date', 'counselling_bioline', 'counselling_unigold_date', 'counselling_unigold', 'counselling_elisa_date', 'counselling_elisa', 'counselling_pcr1_date', 'counselling_pcr1', 'counselling_pcr2_date', 'counselling_pcr2', 'counselling_rapid12_date', 'counselling_rapid12', 'counselling_rapid18_date', 'counselling_rapid18', 'counselling_other_date', 'counselling_other', 'counselling_notes','counselling_other_notes','counselling_custom','counselling_vct_camp','counselling_vct_camp_site','counselling_return','counselling_client_code','counselling_partner_code','counselling_area','counselling_gender','counselling_marital','counselling_client_seen', 'counselling_final','counselling_dis_couple','counselling_mother_treatment_where','counselling_mother_pmtct_where','counselling_mother_antenatal_where','counselling_mother_cd4_note','counselling_positive_ref','counselling_positive_ref_notes','counselling_admission_date','counselling_referral_source_old','counselling_referral_source_notes');
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
	$headers = array ('client_adm_no', 'client_first_name', 'client_other_name', 'client_last_name', 'clinic_name', 'clinical_entry_date', 'clinical_clinic_id', 'clinical_staff_id', 'clinical_staff_name', 'contact_first_name', 'contact_other_name', 'contact_last_name', 'clinical_age_yrs', 'clinical_age_months', 'clinical_child_attending', 'clinical_caregiver_attending', 'clinical_caregiver', 'clinical_illness', 'clinical_illness_notes', 'clinical_diarrhoea', 'clinical_vomiting', 'clinical_current_complaints','clinical_complaints', 'clinical_bloodtest_date', 'clinical_bloodtest_cd4', 'clinical_bloodtest_cd4_percentage', 'clinical_bloodtest_viral', 'clinical_bloodtest_hb', 'clinical_xray_results','clinical_ctscan','clinical_astal', 'clinical_other_results', 'clinical_weight', 'clinical_height', 'clinical_zscore', 'clinical_muac', 'clinical_hc', 'clinical_child_unwell', 'clinical_temp', 'clinical_resp_rate', 'clinical_heart_rate', 'clinical_general', 'clinical_pallor', 'clinical_jaundice', 'clinical_examination_dehydration', 'clinical_examination_lymph', 'clinical_mouth','clinical_mouth_thrush', 'clinical_mouth_ulcer','clinical_teeth','clinical_teeth_opt', 'clinical_ears','clinical_ears_opt', 'clinical_chest', 'clinical_chest_clear','clinical_chest_creps', 'clinical_skin_clear', 'clinical_cardiovascular', 'clinical_skin','clinical_skin_opt', 'clinical_clubbing', 'clinical_abdomen', 'clinical_neurodevt','clinical_cns','clinical_eyes','clinical_eyes_opt','clinical_muscle', 'clinical_musculoskeletal', 'clinical_oedema', 'clinical_adherence', 'clinical_adherence_notes', 'clinical_diarrhoea_type', 'clinical_dehydration', 'clinical_pneumonia', 'clinical_chronic_lung', 'clinical_lung_disease', 'clinical_tb','clinical_tb_treat', 'clinical_tb_treatment_date', 'clinical_pulmonary', 'clinical_discharging_ears', 'clinical_other_diagnoses','clinical_dss', 'clinical_malnutrition', 'clinical_growth', 'clinical_assessment_notes', 'clinical_investigations','clinical_investigations_notes', 'clinical_investigations_blood', 'clinical_investigations_xray',  'clinical_other_drugs', 'clinical_new_drugs', 'clinical_on_arvs', 'clinical_arv_drugs','clinical_arv_on','clinical_arv_on_adh','clinical_arv_recomends', 'clinical_tb_treatment','clinical_tb_status','clinical_tb_notes', 'clinical_arv_notes','clinical_stage', 'clinical_who_stage', 'clinical_who_current', 'clinical_who_reason', 'clinical_tb_drugs', 'clinical_tb_drugs_notes', 'clinical_septrin', 'clinical_vitamins', 'clinical_treatment_status', 'clinical_arv_reason', 'clinical_nutritional_support', 'clinical_nutritional_notes', 'clinical_referral','clinical_referral_old', 'clinical_referral_other', 'clinical_next_date', 'clinical_notes','clinical_custom','clinical_arv_drugs_other','clinical_request','clinical_request_list','clinical_other','clinical_therapy_stage' );

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

	$headers = array ('client_adm_no', 'client_first_name', 'client_other_name', 'client_last_name', 'clinic_name', 'counselling_staff_id', 'counselling_visit_staff_name', 'contact_first_name', 'contact_other_name', 'contact_last_name', 'counselling_center_id', 'counselling_entry_date', 'counselling_visit_type', 'counselling_caregiver_fname', 'counselling_caregiver_lname', 'counselling_caregiver_age', 'counselling_caregiver_relationship', 'counselling_caregiver_marital_status', 'counselling_caregiver_educ_level', 'counselling_caregiver_employment', 'counselling_caregiver_income_level', 'counselling_caregiver_idno', 'counselling_caregiver_mobile', 'counselling_caregiver_residence', 'counselling_child_issues', 'counselling_other_issues', 'counselling_caregiver_issues', 'counselling_caregiver_other_issues', 'counselling_caregiver_issues2', 'counselling_caregiver_other_issues2', 'counselling_child_knows_status', 'counselling_otheradult_knows_status','counselling_otheradult_knows_status_old', 'counselling_disclosure_response', 'counselling_disclosure_state', 'counselling_secondary_caregiver_knows', 'counselling_primary_caregiver_tested', 'counselling_father_status', 'counselling_mother_status', 'counselling_caregiver_status', 'counselling_father_treatment', 'counselling_mother_treatment', 'counselling_caregiver_treatment', 'counselling_stigmatization_concern', 'counselling_counselling_services', 'counselling_other_services', 'counselling_notes' ,'counselling_custom','counselling_second_indent','counselling_referer','counselling_referer_other','counselling_next_visit');

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

	$headers = array ('client_adm_no', 'client_first_name', 'client_other_name', 'client_last_name', 'clinic_name', 'social_id', 'social_staff_id', 'social_staff_name', 'contact_first_name', 'contact_other_name', 'contact_last_name', 'social_clinic_id', 'social_entry_date', 'social_client_status', 'social_visit_type', 'social_death', 'social_death_notes', 'social_death_date', 'social_caregiver_pri_change', 'social_caregiver_pri_change_notes','social_caregiver_sec_change', 'social_caregiver_sec_change_notes', 'social_caregiver_fname', 'social_caregiver_lname', 'social_caregiver_age', 'social_caregiver_status', 'social_caregiver_relationship', 'social_caregiver_education', 'social_caregiver_employment', 'social_caregiver_income', 'social_caregiver_idno', 'social_caregiver_mobile', 'social_caregiver_health', 'social_caregiver_health_child_impact','social_caregiver_pri_health_child_impact','social_caregiver_sec_health_child_impact', 'social_residence_mobile', 'social_residence', 'social_caregiver_pri_employment_change', 'social_caregiver_pri_new_employment', 'social_caregiver_pri_new_employment_desc','social_caregiver_sec_employment_change', 'social_caregiver_sec_new_employment', 'social_caregiver_sec_new_employment_desc', 'social_caregiver_new_income', 'social_school_attendance', 'social_school', 'social_reason_not_attending', 'social_relocation', 'social_iga', 'social_placement', 'social_succession_planning', 'social_legal', 'social_nursing', 'social_transport', 'social_education', 'social_food', 'social_rent', 'social_solidarity', 'social_direct_support', 'social_medical_support', 'social_medical_support_desc', 'social_other_support', 'social_othersupport_value', 'social_permanency_value', 'social_succession_value', 'social_legal_value', 'social_nursing_value', 'social_transport_value', 'social_education_value', 'social_food_value', 'social_rent_value', 'social_solidarity_value', 'social_directsupport_value', 'social_medicalsupport_value', 'social_risk_level', 'social_notes', 'social_change','social_training', 'social_training_desc', 'social_next_visit', 'social_referral', 'social_caregiver_pri', 'social_caregiver_sec', 'social_caregiver_pri_type', 'social_caregiver_sec_type', 'social_nhf', 'social_nhf_y', 'social_nhf_n', 'social_immun', 'social_immun_y', 'social_immun_n', 'social_caregiver_employment_change', 'social_caregiver_new_employment', 'social_caregiver_new_employment_desc', 'social_class_form', 'social_caregiver_income','social_any_needs'  );	
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

	$headers = array ('client_adm_no', 'client_first_name', 'client_other_name', 'client_last_name', 'social_services_client_id', 'social_services_social_id', 'social_services_service_id', 'social_services_date', ' social_services_notes','social_services_custom','social_services_value' );
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
	

	$headers = array ('client_adm_no', 'client_first_name', 'client_other_name', 'client_last_name', 'clinic_name', 'nutrition_staff_id', 'nutrition_staff_name', 'contact_first_name', 'contact_other_name', 'contact_last_name', 'nutrition_entry_date', 'nutrition_center', 'nutrition_gender', 'nutrition_age_yrs', 'nutrition_age_months', 'nutrition_age_status', 'nutrition_caregiver_type', 'nutrition_caregiver_type_notes', 'nutrition_weight', 'nutrition_height', 'nutrition_zscore', 'nutrition_muac', 'nutrition_wfh', 'nutrition_wfa', 'nutrition_bmi', 'nutrition_blacktea', 'nutrition_whitetea', 'nutrition_bread', 'nutrition_porridge','nutrition_water', 'nutrition_breastfeeding', 'nutrition_formula_milk', 'nutrition_carbohydrates', 'nutrition_meat', 'nutrition_pancake', 'nutrition_eggs', 'nutrition_legumes', 'nutrition_milk', 'nutrition_vegetables', 'nutrition_fruit', 'nutrition_diet_history_notes', 'nutrition_diet_history_others', 'nutrition_food_enrichment', 'nutrition_water_access', 'nutrition_water_purification', 'nutrition_water_purification_notes', 'nutrition_food_enrichment_notes', 'nutrition_quantity', 'nutrition_quality', 'nutrition_poor_preparation', 'nutrition_mixed_feeding', 'nutrition_unclean_drinking_water', 'nutrition_education', 'nutrition_counselling', 'nutrition_demonstration', 'nutrition_dietary_supplement', 'nutrition_nan', 'nutrition_unimix', 'nutrition_harvest_pro', 'nutrition_wfp', 'nutrition_insta', 'nutrition_rutf', 'nutrition_other', 'nutrition_other_service', 'nutrition_notes','nutrition_custom','nutrition_oedema','nutrition_beverages_title','nutrition_beverages_notes','nutrition_ugali','nutrition_rice','nutrition_banan','nutrition_tubers','nutrition_wheat','nutrition_carbos_title','nutrition_carbos_notes','nutrition_protein_title','nutrition_protein_notes','nutrition_fat','nutrition_issue_notes','nutrition_program','nutrition_program_other','nutrition_rendered','nutrition_next_visit','nutrition_refer','nutrition_refer_other','nutrition_service_other','nutrition_child_attend','nutrition_care_attend','nutrition_care_who' );

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
				list($nutrition_visits,$kheads,$repeat) = $q->loadListExport (null,true);
				if(count($nutrition_visits) > 0 && $repeat === false){
					$last=true;
				}
				++$iter;
			}else {
				$last = false;
			}
		}
	}

	// Creating a worksheet for medical assessment on admission
	$iter=0;
	$last=false;
	$headers = array ('client_adm_no', 'client_first_name', 'client_other_name', 'client_last_name', 'clinic_name', 'medical_id', 'medical_staff_id', 'medical_staff_name', 'contact_first_name', 'contact_other_name', 'contact_last_name', 'medical_clinic_id', 'medical_gender', 'medical_age_yrs', 'medical_age_months', 'medical_entry_date', 'medical_birth_location', 'medical_delivery', 'medical_birth_problems', 'medical_transferred', 'medical_other_programme', 'medical_birth_weight', 'medical_pmtct', 'medical_mother_arv_given', 'medical_child_arv_given', 'medical_immunization_status', 'medical_card_seen', 'medical_breastfeeding', 'medical_exclusive_breastfeeding', 'medical_bf_duration', 'medical_father_hiv_status', 'medical_father_arv', 'medical_mother_hiv_status', 'medical_mother_arv', 'medical_no_siblings_alive', 'medical_no_siblings_deceased', 'medical_tb_contact', 'medical_tb_contact_person', 'medical_tb_date_diagnosed', 'medical_tb_pulmonary', 'medical_tb_type', 'medical_tb_type_desc', 'medical_tb_bodysite', 'medical_tb_date1', 'medical_tb_date2', 'medical_tb_date3', 'medical_history_pneumonia', 'medical_history_diarrhoea', 'medical_history_skin_rash', 'medical_history_ear_discharge', 'medical_history_fever', 'medical_history_oral_rush', 'medical_history_mouth_ulcers', 'medical_history_malnutrition', 'medical_history_prev_nutrition', 'medical_history_notes', 'medical_arv_status', 'medical_arv1', 'medical_arv1_startdate', 'medical_arv1_enddate', 'medical_arv2', 'medical_arv2_startdate', 'medical_arv2_enddate','medical_salvage', 'medical_salvage_startdate', 'medical_salvage_enddate', 'medical_arv_side_effects', 'medical_arv_adherence', 'medical_school_attendance', 'medical_school_class', 'medical_educ_progress', 'medical_sensory_hearing', 'medical_sensory_vision', 'medical_sensory_motor_ability', 'medical_sensory_speech_language', 'medical_sensory_social_skills', 'medical_meals_per_day', 'medical_food_types', 'medical_current_complaints', 'medical_weight', 'medical_height', 'medical_zscore', 'medical_muac', 'medical_hc', 'medical_condition', 'medical_temp', 'medical_conditions', 'medical_dehydration', 'medical_parotids', 'medical_lymph', 'medical_eyes', 'medical_eyes_notes', 'medical_ear_discharge', 'medical_ear_discharge_location', 'medical_throat', 'medical_mouth_thrush', 'medical_mouth_ulcers', 'medical_mouth_teeth', 'medical_oldlesions', 'medical_currentlesions', 'medical_heartrate', 'medical_recession', 'medical_percussion', 'medical_location', 'medical_breath_sounds', 'medical_breathlocation', 'medical_other_sounds', 'medical_soundlocation', 'medical_pulserate', 'medical_apex_beat', 'medical_precordial', 'medical_femoral', 'medical_heart_sound', 'medical_heart_type', 'medical_abdomen_distended', 'medical_adbomen_feel', 'medical_abdomen_tender', 'medical_abdomen_fluid', 'medical_liver_costal', 'medical_spleen_costal', 'medical_masses', 'medical_umbilical_hernia', 'medical_testes', 'medical_which_testes', 'medical_genitals_female_notes', 'medical_genitals_feel', 'medical_penis', 'medical_genitals_female', 'medical_pubertal', 'medical_gait','medical_gait_opt', 'medical_handuse','medical_handuse_opt', 'medical_weakness', 'medical_tone', 'medical_tendon_legs', 'medical_tendon_arms', 'medical_abnormal_movts', 'medical_movts_impaired', 'medical_movts_impaired_desc', 'medical_joints_swelling', 'medical_joints_swelling_desc', 'medical_motor','medical_motor_notes', 'medical_musc_notes', 'medical_hiv_status', 'medical_cd4', 'medical_cd4_percentage', 'medical_who_clinical_stage', 'medical_immuno_stage', 'medical_tests', 'medical_referral', 'medical_referral_old', 'medical_chest_shape','medical_custom','medical_heart_rate','medical_resp_rate','medical_skin_type','medical_skin_note','medical_cns','medical_cns_note','medical_muscle','medical_muscle_note','medical_request','medical_request_note','medical_next_visit', 'medical_notes' );

	$q = new DBQuery ();
	$q->addTable ( 'medical_assessment', 'ma' );
	/*$q->innerJoin ( 'clients', 'cl', 'cl.client_id = ma.medical_client_id' );
	$q->leftJoin ( 'clinics', 'cli', 'cli.clinic_id = ma.medical_clinic_id' );
	$q->leftJoin ( 'contacts', 'c', 'c.contact_id = ma.medical_staff_id' );
	$q->leftJoin ( 'contacts', 'cr', 'cr.contact_id = ma.medical_referral' );*/
	//$q->addQuery ( 'cl.client_adm_no, cl.client_first_name, cl.client_other_name, cl.client_last_name, cli.clinic_name,concat_ws(" ", c.contact_first_name, c.contact_other_name, c.contact_last_name) as medical_staff_name,concat_ws(" ", cr.contact_first_name, cr.contact_other_name, cr.contact_last_name) as medical_referral_name,ma.*' );
	list($medical_assessments,$kheads,$repeat) = $q->loadListExport ();
	
	if(count($medical_assessments) == 0){
		$iter=1;
		$repeat=false;
	}else{
		$clipos=array_search('medical_client_id',$kheads);
		$conpos=array_search('medical_staff_id',$kheads);
		$refpos=array_search('medical_referral',$kheads);
		$hospos=array_search('medical_clinic_id',$kheads);
		while($repeat || $iter == 0 || $last === true){
			foreach ($medical_assessments as $key => $vars) {
				$cclient=$clients[$vars[$clipos]];
				$medical_assessments[$key][]=$cclient['client_adm_no'];
				$medical_assessments[$key][]=$cclient['client_first_name'];
				$medical_assessments[$key][]=$cclient['client_other_name'];
				$medical_assessments[$key][]=$cclient['client_last_name'];
				$medical_assessments[$key][]=$clinics[$vars[$hospos]]['clinic_name'];
				$cstaff=$contacts[$vars[$conpos]];
				$medical_assessments[$key][]=$cstaff['contact_first_name'].' '.$cstaff['contact_other_name'].' '.$cstaff['contact_last_name'];
				unset($cstaff);
				$cstaff=$contacts[$vars[$refpos]];
				$medical_assessments[$key][]=$cstaff['contact_first_name'].' '.$cstaff['contact_other_name'].' '.$cstaff['contact_last_name'];
			}
			$kheads=array_merge($kheads,array('client_adm_no','client_first_name','client_other_name','client_last_name','clinic_name','medical_staff_name','medical_referral_name'));
			$mwriter->store("Medical_assessment" ,&$medical_assessments,$kheads,$headers,$headers,$repeat,$iter);
			unset($medical_assessments);
			if(!$last){
				list($medical_assessments,$kheads,$repeat) = $q->loadListExport (null,true);
				if(count($medical_assessments) > 0 && $repeat === false){
					$last=true;
				}
				++$iter;
			}else {
				$last = false;
			}
		}
	}

	// Creating a worksheet for admission details
	
	$headers = array ('client_adm_no', 'client_first_name', 'client_other_name', 'client_last_name', 'clinic_name', 'admission_id', 'admission_staff_id', 'admission_staff_name', 'contact_first_name', 'contact_other_name', 'contact_last_name', 'admission_clinic_id', 'admission_dob', 'admission_age_yrs', 'admission_age_months', 'admission_age_status', 'admission_gender', 'admission_residence', 'admission_location', 'location_name', 'admission_entry_date', 'admission_school_level', 'admission_reason_not_attending', 'admission_reason_not_attending_notes', 'admission_total_orphan', 'admission_province', 'admission_district', 'admission_village', 'admission_father_fname', 'admission_father_lname', 'admission_father_age', 'admission_father_status', 'admission_father_health_status', 'admission_father_raising_child', 'admission_father_marital_status', 'admission_father_educ_level', 'admission_father_employment', 'admission_father_income', 'admission_father_idno', 'admission_father_mobile', 'admission_mother_fname', 'admission_mother_lname', 'admission_mother_age', 'admission_mother_status', 'admission_mother_health_status', 'admission_mother_raising_child', 'admission_mother_marital_status', 'admission_mother_educ_level', 'admission_mother_employment', 'admission_mother_income', 'admission_mother_idno', 'admission_mother_mobile', 'admission_caregiver_fname', 'admission_caregiver_lname', 'admission_caregiver_age', 'admission_caregiver_status', 'admission_caregiver_health_status', 'admission_caregiver_relationship', 'admission_caregiver_marital_status', 'admission_caregiver_educ_level', 'admission_caregiver_employment', 'admission_caregiver_income', 'admission_caregiver_idno', 'admission_caregiver_mobile', 'admission_family_income', 'admission_risk_level', 'admission_risk_level_description', 'admission_notes','admission_custom','admission_enclosures','admission_birth_cert','admission_id_no','admission_med_recs','admission_nhf','admission_immun','admission_death_cert','admission_enclosures_other' ,'admission_father','admission_mother','admission_caregiver_pri','admission_caregiver_sec','admission_caregiver_pri_relationship','admission_caregiver_sec_relationship','admission_caregiver_sec_residence','admission_chw');
	$iter=0;
	$last=false;
	$q = new DBQuery ();
	$q->addTable ( 'admission_info', 'ai' );
	$q->leftJoin ( 'clinic_location', 'clo', 'clo.clinic_location_id = ai.admission_location' );
	$q->addQuery ( 'clinic_location as location_name,ai.*' );
	list($admission_records,$kheads,$repeat) = $q->loadListExport ();

	if(count($admission_records) == 0){
		$iter=1;
		$repeat=false;
	}else{
		$clipos=array_search('admission_client_id',$kheads);
		$conpos=array_search('admission_staff_id',$kheads);
		$hospos=array_search('admission_clinic_id',$kheads);
		while($repeat || $iter == 0 || $last === true){
			foreach ($admission_records as $key => $vars) {
				$cclient=$clients[$vars[$clipos]];
				$admission_records[$key][]=$cclient['client_adm_no'];
				$admission_records[$key][]=$cclient['client_first_name'];
				$admission_records[$key][]=$cclient['client_other_name'];
				$admission_records[$key][]=$cclient['client_last_name'];
				$admission_records[$key][]=$clinics[$vars[$hospos]]['clinic_name'];
				$cstaff=$contacts[$vars[$conpos]];
				$admission_records[$key][]=$cstaff['contact_first_name'].' '.$cstaff['contact_other_name'].' '.$cstaff['contact_last_name'];
			}
			//writeWorksheet ( $worksheet, $format_bold, $headers, &$admission_records, $headers );
			$kheads=array_merge($kheads,array('client_adm_no','client_first_name','client_other_name','client_last_name','clinic_name','admission_staff_name'));
			$mwriter->store("Admission_details" ,&$admission_records,$kheads,$headers,$headers,$repeat,$iter);
			unset($admission_records);
			if(!$last){
				list($admission_records,$kheads,$repeat) = $q->loadListExport (null,true);
				if(count($admission_records) > 0 && $repeat === false){
					$last=true;
				}
				++$iter;
			}else {
				$last = false;
			}
		}
	}

	// Creating a worksheet for family details
	$headers = array ('client_adm_no', 'client_first_name', 'client_other_name', 'client_last_name', 'household_admission_id', 'household_social_id', 'household_name', 'household_yob', 'household_relationship', 'household_gender', 'household_notes','household_custom' );
	$q = new DBQuery ();
	$q->addTable ( 'household_info', 'hi' );	
	list($household_records,$kheads,$repeat) = $q->loadListExport ();
	
	$iter=0;
	$last=false;
	if(count($household_records) == 0){
		$iter=1;
		$repeat=false;
	}else{
		$clipos=array_search('household_client_id',$kheads);
		while($repeat || $iter == 0 || $last === true){
			foreach ($household_records as $key => $vars) {
				$cclient=$clients[$vars[$clipos]];
				$household_records[$key][]=$cclient['client_adm_no'];
				$household_records[$key][]=$cclient['client_first_name'];
				$household_records[$key][]=$cclient['client_other_name'];
				$household_records[$key][]=$cclient['client_last_name'];

			}
			//writeWorksheet ( $worksheet, $format_bold, $headers, &$household_records, $headers );
			$kheads=array_merge($kheads,array('client_adm_no','client_first_name','client_other_name','client_last_name'));
			$mwriter->store("Household_details" ,&$household_records,$kheads,$headers,$headers,$repeat,$iter);
			unset($household_records);
			if(!$last){
				list($household_records,$kheads,$repeat) = $q->loadListExport (null,true);
				if(count($household_records) > 0 && $repeat === false){
					$last=true;
				}
				++$iter;
			}else {
				$last = false;
			}
		}
	}
	// Creating a worksheet for Medical History
	/*$worksheet = & $workbook->addWorksheet ( "Medical_History" );
	$format_bold = & $workbook->addFormat ();
	$format_bold->setBold ();*/

	$headers = array ('client_adm_no', 'client_first_name', 'client_other_name', 'client_last_name', 'medical_history_medical_id', 'medical_history_hospital', 'medical_history_date', 'medical_history_diagnosis', 'medical_history_notes','medical_history_custom' );
	$iter=0;
	$last=false;
	$q = new DBQuery ();
	$q->addTable ( 'medical_history', 'mi' );
	//$q->innerJoin ( 'clients', 'cl', 'cl.client_id = mi.medical_history_client_id' );
	//$q->addQuery ( 'cl.client_adm_no, cl.client_first_name, cl.client_other_name, cl.client_last_name, mi.*' );
	list($medical_history_records,$kheads,$repeat) = $q->loadListExport ();
	
	if(count($medical_history_records) == 0){
		$iter=1;
		$repeat=false;
	}else{
		$clipos=array_search('medical_history_client_id',$kheads);
		while($repeat || $iter == 0 || $last === true){
			foreach ($medical_history_records as $key => $vars) {
				$cclient=$clients[$vars[$clipos]];
				$medical_history_records[$key][]=$cclient['client_adm_no'];
				$medical_history_records[$key][]=$cclient['client_first_name'];
				$medical_history_records[$key][]=$cclient['client_other_name'];
				$medical_history_records[$key][]=$cclient['client_last_name'];
			}
			//writeWorksheet ( $worksheet, $format_bold, $headers, &$medical_history_records, $headers );
			$kheads=array_merge($kheads,array('client_adm_no','client_first_name','client_other_name','client_last_name'));
			$mwriter->store("Medical_history" ,&$medical_history_records,$kheads,$headers,$headers,$repeat,$iter);
			unset($medical_history_records);
			if(!$last){
				list($medical_history_records,$kheads,$repeat) = $q->loadListExport (null,true);
				if(count($medical_history_records) > 0 && $repeat === false){
					$last=true;
				}
				++$iter;
			}else {
				$last = false;
			}
		}
	}

	// Creating a worksheet for Medication History
	
	$headers = array ('client_adm_no', 'client_first_name', 'client_other_name', 'client_last_name', 'medications_history_drug', 'medications_history_dose', 'medications_history_frequency', 'medications_history_notes','medications_history_custom' );
	$iter=0;
	$last=false;
	$q = new DBQuery ();
	$q->addTable ( 'medications_history', 'mh' );
	//$q->innerJoin ( 'clients', 'cl', 'cl.client_id = mh.medications_history_client_id' );
	//$q->addQuery ( ' cl.client_adm_no, cl.client_first_name, cl.client_other_name, cl.client_last_name, mh.*' );
	list($medications_history,$kheads,$repeat) = $q->loadListExport ();
	
	if(count($medications_history) == 0){
		$iter=1;
		$repeat=false;
	}else{
		$clipos=array_search('medications_history_client_id',$kheads);
		while($repeat || $iter == 0 || $last === true){
			foreach ($medications_history as $key => $vars) {
				$cclient=$clients[$vars[$clipos]];
				$medications_history[$key][]=$cclient['client_adm_no'];
				$medications_history[$key][]=$cclient['client_first_name'];
				$medications_history[$key][]=$cclient['client_other_name'];
				$medications_history[$key][]=$cclient['client_last_name'];
			}
			//writeWorksheet ( $worksheet, $format_bold, $headers, &$medications_history, $headers );
			$kheads=array_merge($kheads,array('client_adm_no','client_first_name','client_other_name','client_last_name'));
			$mwriter->store("Medication_history" ,&$medications_history,$kheads,$headers,$headers,$repeat,$iter);
			unset($medications_history);
			if(!$last){
				list($medications_history,$kheads,$repeat) = $q->loadListExport (null,true);
				if(count($medications_history) > 0 && $repeat === false){
					$last=true;
				}
				++$iter;
			}else {
				$last = false;
			}
		}
	}
	// Creating a worksheet for mortality info
	

	$headers = array ('client_adm_no', 'client_first_name', 'client_other_name', 'client_last_name', 'clinic_name', 'mortality_entry_date', 'mortality_clinic_id', 'mortality_age_yrs', 'mortality_age_months', 'mortality_age_status', 'mortality_date', 'mortality_death_type', 'mortality_death_type_notes', 'mortality_informant', 'mortality_hospital', 'mortality_hospital_adm_date', 'mortality_relative_report_date', 'mortality_symptoms', 'mortality_time_course', 'mortality_treatment', 'mortality_referral', 'mortality_hospital_referral', 'mortality_hospital_adm_notes', 'mortality_cause_given', 'mortality_cause_desc', 'mortality_clinical_officer_name', 'mortality_clinical_officer_date', 'mortality_postmortem', 'mortality_cause_pm', 'mortality_likely_cause', 'mortality_notes','mortality_custom','mortality_clinical_course','mortality_postmortem_where','mortality_recents_a','mortality_recents_b','mortality_malnutrition','mortality_malnutrition_notes','mortality_cd4','mortality_cd4_percentage','mortality_viral_load','mortality_hb','mortality_clinical_date','mortality_arv','mortality_arv_dateon','mortality_arv_period','mortality_tb','mortality_rb_start','mortality_weight','mortality_height','mortality_nutrition_date','mortality_enroll_date','mortality_enrolled_time' );
	$iter=0;
	$last=false;
	$q = new DBQuery ();
	$q->addTable ( 'mortality_info', 'mi' );	
	list($mortality_records,$kheads,$repeat) = $q->loadListExport ();
	if(count($mortality_records) == 0){
		$iter=1;
		$repeat=false;
	}else{
		$clipos=array_search('mortality_client_id',$kheads);
		$hospos=array_search('mortality_clinic_id',$kheads);
		$conpos=array_search('mortality_clinical_officer',$kheads);

		while($repeat || $iter == 0 || $last === true){
			foreach ($mortality_records as $key => $vars) {
				$cclient=$clients[$vars['mortality_client_id']];
				$mortality_records[$key][]=$cclient['client_adm_no'];
				$mortality_records[$key][]=$cclient['client_first_name'];
				$mortality_records[$key][]=$cclient['client_other_name'];
				$mortality_records[$key][]=$cclient['client_last_name'];
				$mortality_records[$key][]=$clinics[$vars[$hospos]]['clinic_name'];
				$cstaff=$contacts[$vars[$conpos]];
				$mortality_records[$key][]=$cstaff['contact_first_name'].' '.$cstaff['contact_other_name'].' '.$cstaff['contact_last_name'];
			}
			//writeWorksheet ( $worksheet, $format_bold, $headers, &$mortality_records, $headers );
			$kheads=array_merge($kheads,array('client_adm_no','client_first_name','client_other_name','client_last_name','clinic_name','mortality_clinical_officer_name'));
			$mwriter->store("Mortality" ,&$mortality_records,$kheads,$headers,$headers,$repeat,$iter);
			unset($mortality_records);
			if(!$last){
				list($mortality_records,$kheads,$repeat) = $q->loadListExport (null,true);
				if(count($mortality_records) > 0 && $repeat === false){
					$last=true;
				}
				++$iter;
			}else {
				$last = false;
			}
		}
	}

	// Creating a worksheet for staff info
	/*$worksheet = & $workbook->addWorksheet ( "Staff" );
	$format_bold = & $workbook->addFormat ();
	$format_bold->setBold ();*/

	$iter=0;
	$last=false;
	$headers = array ('contact_first_name', 'contact_other_name', 'contact_last_name', 'contact_order_by', 'contact_title', 'contact_birthday', 'contact_job', 'contact_client', 'contact_department', 'contact_type', 'contact_email', 'contact_email2', 'contact_url', 'contact_phone', 'contact_phone2', 'contact_fax', 'contact_mobile', 'contact_address1', 'contact_address2', 'contact_city', 'contact_state', 'contact_zip', 'contact_country', 'contact_jabber', 'contact_icq', 'contact_msn', 'contact_yahoo', 'contact_aol', 'contact_notes', 'contact_project', 'contact_icon', 'contact_owner', 'contact_private' );

	$q = new DBQuery ();
	$q->addTable ( 'contacts', 'con' );
	$q->leftJoin ( 'users', 'u', 'u.user_contact = con.contact_id' );
	$q->addQuery ( 'contact_first_name, contact_other_name, contact_last_name, contact_order_by, contact_title, contact_birthday, contact_job, contact_client, contact_department, contact_type, contact_email, contact_email2, contact_url, contact_phone, contact_phone2, contact_fax, contact_mobile, contact_address1, contact_address2, contact_city, contact_state, contact_zip, contact_country, contact_jabber, contact_icq, contact_msn, contact_yahoo, contact_aol, contact_notes, contact_project, contact_icon, contact_owner, contact_private' );
	$q->addWhere ( 'contact_id <> 1' );
	list($staff,$kheads,$repeat) = $q->loadListExport ();
	//writeWorksheet ( $worksheet, $format_bold, $headers, &$staff, $headers );
	if (count ( $staff ) > 0) {
		while ( $repeat || $iter == 0 || $last === true ) {
			$mwriter->store ( "Staff", &$staff, $kheads, $headers, $headers, $repeat ,$iter);
			unset ( $staff );
			if (! $last) {
				list ( $staff, $kheads, $repeat ) = $q->loadListExport ( null, true );
				if (count ( $staff ) > 0 && $repeat === false) {
					$last = true;
				}
				++ $iter;
			} else {
				$last = false;
			}
		}
	}

	// Creating a worksheet for clinic location
	/*$worksheet = & $workbook->addWorksheet ( "Clinic Location" );
	$format_bold = & $workbook->addFormat ();
	$format_bold->setBold ();*/

	$iter=0;
	$last=false;
	$headers = array ('clinic_location_clinic_id', 'clinic_name', 'clinic_location', 'clinic_location_notes' );

	$q = new DBQuery ();
	$q->addTable ( 'clinic_location', 'con' );
	$q->innerJoin ( 'clinics', 'cl', ' cl.clinic_id = con.clinic_location_clinic_id' );
	$q->addQuery ( 'con.clinic_location_clinic_id,cl.clinic_name,con.clinic_location, con.clinic_location_notes' );

	list($clinic_location,$kheads,$repeat) = $q->loadListExport ();
	//writeWorksheet ( $worksheet, $format_bold, $headers, &$clinic_location, $headers );
	$mwriter->store("Clinic_location" ,&$clinic_location,$kheads,$headers,$headers);
	unset($clinic_location);

	// Creating a worksheet for Group Activities
	/*$worksheet = & $workbook->addWorksheet ( "Group_Activities" );
	$format_bold = & $workbook->addFormat ();
	$format_bold->setBold ();*/

	$headers = array ('activity_id','activity_date', 'activity_curriculum', 'activity_curriculum_desc', 'activity_entry_date', 'activity_description', 'activity_clinic', 'clinic_name', 'activity_male_count', 'activity_female_count', 'activity_notes' ,`activity_custom` , 'activity_hpd','activity_visiters_total', 'activity_cadres' , 'activity_end_date');
	$iter=0;
	$last=false;
	$q = new DBQuery ();
	$q->addTable ( 'activity', 'a' );
	$q->innerJoin ( 'clinics', 'c', 'c.clinic_id = a.activity_clinic' );
	$q->addQuery ( 'a.*, c.clinic_name as activity_clinic_name' );
	list($activity_records,$kheads) = $q->loadListExport ();
	//writeWorksheet ( $worksheet, $format_bold, $headers, &$activity_records, $headers );
	$mwriter->store("Group_Activities" ,&$activity_records,$kheads,$headers,$headers);
	unset($activity_records);

	// Creating a worksheet for Trainings
	/*$worksheet = & $workbook->addWorksheet ( "Trainings" );
	$format_bold = & $workbook->addFormat ();
	$format_bold->setBold ();*/

	$headers = array ('training_date', 'training_entry_date', 'training_name', 'training_clinic', 'clinic_name', 'training_notes','training_curriculum','training_curriculum_desc' ,'training_custom');

	$iter=0;
	$last=false;
	$q = new DBQuery ();
	$q->addTable ( 'trainings', 't' );
	$q->innerJoin ( 'clinics', 'c', 'c.clinic_id = t.training_clinic' );
	$q->addQuery ( 't.*, c.clinic_name as clinic_name' );
	list($training_records,$kheads) = $q->loadListExport ();
	//writeWorksheet ( $worksheet, $format_bold, $headers, &$training_records, $headers );
	$mwriter->store("Trainings" ,&$training_records,$kheads,$headers,$headers);
	unset($training_records);

	// Creating a worksheet for Training Facilitators/Activities
	/*$worksheet = & $workbook->addWorksheet ( "Activity_Facilitator" );
	$format_bold = & $workbook->addFormat ();
	$format_bold->setBold ();*/

	$headers = array ('activity_description', 'activity_date', 'training_name','facilitator_activity_id', 'facilitator_training_id', 'facilitator_training', 'facilitator_name', 'facilitator_topic', 'facilitator_custom' );

	$q = new DBQuery ();
	$q->addTable ( 'activity_facilitator', 'af' );
	$q->innerJoin ( 'activity', 'a', 'af.facilitator_activity_id = a.activity_id' );
	$q->innerJoin ( 'trainings', 't', 'af.facilitator_training_id = t.training_id' );
	$q->addQuery ( 'a.activity_description, a.activity_date, t.training_name, af.*' );
	list($facilitator_records,$kheads) = $q->loadListExport ();
	//writeWorksheet ( $worksheet, $format_bold, $headers, &$facilitator_records, $headers );
	$mwriter->store("Activity_Facilitator" ,&$facilitator_records,$kheads,$headers,$headers);
	unset($facilitator_records);

	// Creating a worksheet for Activity Caregivers
	/*$worksheet = & $workbook->addWorksheet ( "Activity_Caregivers" );
	$format_bold = & $workbook->addFormat ();
	$format_bold->setBold ();*/

	$headers = array ('activity_description', 'activity_date', 'caregiver_fname', 'caregiver_lname','caregiver_role', 'client_adm_no', 'client_first_name', 'client_other_name', 'client_last_name', 'activity_caregivers_activity_id', 'activity_caregivers_caregiver_id' );
	$iter=0;
	$last=false;
	$q = new DBQuery ();
	$q->addTable ( 'activity_caregivers', 'ac' );
	$q->innerJoin ( 'activity', 'a', 'ac.activity_caregivers_activity_id = a.activity_id' );
	$q->addQuery ( 'a.activity_description, a.activity_date, ac.*');
	list($caregiver_records,$kheads,$repeat) = $q->loadListExport ();
	//$clipos=array_search('activity_caregiver_client_id',$kheads);
	
	if(count($caregiver_records) == 0){
		$iter=1;
		$repeat=false;
	}else{
		$carpos=array_search('activity_caregivers_caregiver_id',$kheads);

		while($repeat || $iter == 0 || $last === true){
			foreach ($caregiver_records as $key => $vars) {
				$cclient=$carez[$vars[$carpos]];
				$caregiver_records[$key][]=$cclient['fname'];
				$caregiver_records[$key][]=$cclient['lname'];
				$caregiver_records[$key][]=$cclient['role'];
				$xclient=$clients[$cclient['client_id']];
				$caregiver_records[$key][]=$xclient['client_adm_no'];
				$caregiver_records[$key][]=$xclient['client_first_name'];
				$caregiver_records[$key][]=$xclient['client_other_name'];
				$caregiver_records[$key][]=$xclient['client_last_name'];
			}
			//writeWorksheet ( $worksheet, $format_bold, $headers, &$caregiver_records, $headers );
			$kheads=array_merge($kheads,array('caregiver_first_name','caregiver_last_name','caregiver_role','client_adm_no','client_first_name','client_other_name','client_last_name'));
			$mwriter->store("Activity_Caregivers" ,&$caregiver_records,$kheads,$headers,$headers,$repeat,$iter);
			unset($caregiver_records);
			if(!$last){
				list($caregiver_records,$kheads,$repeat) = $q->loadListExport (null,true);
				if(count($caregiver_records) > 0 && $repeat === false){
					$last=true;
				}
				++$iter;
			}else {
				$last = false;
			}
		}
	}


	// Creating a worksheet for Activity Clients
	/*$worksheet = & $workbook->addWorksheet ( "Activity_Clients" );
	$format_bold = & $workbook->addFormat ();
	$format_bold->setBold ();*/

	$headers = array ('activity_description', 'activity_date', 'client_adm_no', 'client_first_name', 'client_other_name', 'client_last_name', 'activity_clients_activity_id', 'activity_clients_client_id' );
	$iter=0;
	$last=false;
	$q = new DBQuery ();
	$q->addTable ( 'activity_clients', 'ac' );
	$q->innerJoin ( 'activity', 'a', 'ac.activity_clients_activity_id = a.activity_id' );
	//$q->innerJoin ( 'clients', 'c', 'c.client_id = ac.activity_clients_client_id' );
	//,  c.client_adm_no, c.client_first_name, c.client_other_name,c.client_last_name
	$q->addQuery ( 'a.activity_description, a.activity_date, ac.*' );
	list($client_records,$kheads,$repeat) = $q->loadListExport ();

	if(count($client_records) == 0){
		$iter=1;
		$repeat=false;
	}else{
		$clipos=array_search('activity_clients_client_id',$kheads);
		while($repeat || $iter == 0 || $last === true){
			foreach ($client_records as $key => $vars) {
				$cclient=$clients[$vars[$clipos]];
				$client_records[$key][]=$cclient['client_adm_no'];
				$client_records[$key][]=$cclient['client_first_name'];
				$client_records[$key][]=$cclient['client_other_name'];
				$client_records[$key][]=$cclient['client_last_name'];
			}
			//writeWorksheet ( $worksheet, $format_bold, $headers, &$client_records, $headers );
			$kheads=array_merge($kheads,array('client_adm_no','client_first_name','client_other_name','client_last_name'));
			$mwriter->store("Activity_clients" ,&$client_records,$kheads,$headers,$headers,$repeat,$iter);
			unset($client_records);
			if(!$last){
				list($client_records,$kheads,$repeat) = $q->loadListExport (null,true);
				if(count($client_records) > 0 && $repeat === false){
					$last=true;
				}
				++$iter;
			}else {
				$last = false;
			}
		}
	}

	// Creating a worksheet for Activity Staff
	/*$worksheet = & $workbook->addWorksheet ( "Activity_Staff" );
	$format_bold = & $workbook->addFormat ();
	$format_bold->setBold ();*/

	$headers = array ('activity_description', 'activity_date', 'contact_first_name', 'contact_other_name', 'contact_last_name', 'activity_contacts_activity_id', 'activity_contacts_contact_id' );

	$iter=0;
	$last=false;
	$q = new DBQuery ();
	$q->addTable ( 'activity_contacts', 'ac' );
	$q->innerJoin ( 'activity', 'a', 'ac.activity_contacts_activity_id = a.activity_id' );
	//$q->innerJoin ( 'contacts', 'c', 'c.contact_id = ac.activity_contacts_contact_id' );
	// c.contact_first_name, c.contact_other_name, c.contact_last_name,
	$q->addQuery ( 'a.activity_description, a.activity_date,  ac.*' );
	list($staff_records,$kheads) = $q->loadListExport ();
	
	if(count($staff_records) == 0){
		$iter=1;
		$repeat=false;
	}else{
		$clipos=array_search('activity_contacts_contact_id',$kheads);
		while($repeat || $iter == 0 || $last === true){
			foreach ($staff_records as $key => $vars) {
				$cclient=$contacts[$vars[$clipos]];
				$staff_records[$key][]=$cclient['contact_first_name'];
				$staff_records[$key][]=$cclient['contact_other_name'];
				$staff_records[$key][]=$cclient['contact_last_name'];
			}
			//writeWorksheet ( $worksheet, $format_bold, $headers, &$staff_records, $headers);
			$kheads=array_merge($kheads,array('contact_first_name','contact_other_name','contact_last_name'));
			$mwriter->store("Activity_staff" ,&$staff_records,$kheads,$headers,$headers,$repeat,$iter);
			unset($staff_records);
			if(!$last){
				list($staff_records,$kheads,$repeat) = $q->loadListExport (null,true);
				if(count($staff_records) > 0 && $repeat === false){
					$last=true;
				}
				++$iter;
			}else {
				$last = false;
			}
		}
	}


	$headers=array(  'followup_client_id',
	'followup_adm_no',
	'followup_client_type',
	'followup_object',
	'followup_visit_type',
	'followup_issues',
	'followup_issues_notes',
	'followup_service',
	'followup_service_notes',
	'followup_date',
	//'followup_center_id',
	//'followup_officer_id',
	'followup_visit_mode','client_adm_no', 'client_first_name', 'client_other_name', 'client_last_name','followup_center_id','clinic_name','followup_staff_name','followup_officer_id'
	);

	$iter=0;
	$last=false;
	$q = new DBQuery();
	$q->addTable('followup_info');
	list($folinfo,$kheads,$repeat) = $q->loadListExport ();
	if(count($folinfo) == 0){
		$iter=1;
		$repeat=false;
	}else{
		$clipos=array_search('followup_client_id',$kheads);
		$hospos=array_search('followup_center_id',$kheads);
		$ofpos=array_search('followup_officer_id',$kheads);

		while($repeat || $iter == 0 || $last === true){
			foreach ($folinfo as $key => $vars) {
				$cclient=$clients[$vars[$clipos]];
				$folinfo[$key][]=$cclient['client_adm_no'];
				$folinfo[$key][]=$cclient['client_first_name'];
				$folinfo[$key][]=$cclient['client_other_name'];
				$folinfo[$key][]=$cclient['client_last_name'];
				$folinfo[$key][]=$clinics[$vars[$hospos]]['clinic_name'];
				$cclient=$contacts[$vars[$ofpos]];
				$folinfo[$key][]=$cclient['contact_first_name'].' '.$cclient['contact_other_name'].' '.$cclient['contact_last_name'];
			}
			$kheads=array_merge($kheads,array('client_adm_no','client_first_name','client_other_name','client_last_name','clinic_name','followup_staff_name'));
			$mwriter->store("Followup_info" ,&$folinfo,$kheads,$headers,$headers,$repeat,$iter);
			unset($folinfo);
			if(!$last){
				list($folinfo,$kheads,$repeat) = $q->loadListExport (null,true);
				if(count($folinfo) > 0 && $repeat === false){
					$last=true;
				}
				++$iter;
			}else {
				$last = false;
			}
		}
	}

	$headers=array('chw_client_id',
	'client_adm_no',
	'client_first_name',
	'client_other_name',
	'client_last_name',
	'chw_name',
	'chw_center_id',
	'clinic_name',
	'chw_village',
	'chw_location',
	'chw_entry_date',
	'chw_adm_no',
	'chw_sex',
	'chw_old',
	'chw_age',
	'chw_hasplan',
	'chw_arv',
	'chw_arv_note',
	'chw_oir',
	'chw_oir_note',
	'chw_tb',
	'chw_nutrition',
	'chw_adh_support',
	'chw_assess',
	'chw_support',
	'chw_comm_mob',
	'chw_refers',
	'chw_remarks');

	$q = new DBQuery();
	$q->addTable('chw_info');
	list($chwinfo,$kheads,$repeat) = $q->loadListExport ();
	
	$iter=0;
	$last=false;
	if(count($chwinfo) == 0){
		$iter=1;
		$repeat=false;
	}else{
		$clipos=array_search('chw_client_id',$kheads);
		$hospos=array_search('chw_center_id',$kheads);
		while($repeat || $iter == 0 || $last === true){
			foreach ($chwinfo as $key => $vars) {
				$cclient=$clients[$vars[$clipos]];
				$chwinfo[$key][]=$cclient['client_adm_no'];
				$chwinfo[$key][]=$cclient['client_first_name'];
				$chwinfo[$key][]=$cclient['client_other_name'];
				$chwinfo[$key][]=$cclient['client_last_name'];
				$chwinfo[$key][]=$clinics[$vars[$hospos]]['clinic_name'];
			}
			$kheads=array_merge($kheads,array('client_adm_no','client_first_name','client_other_name','client_last_name','clinic_name'));
			$mwriter->store("CHW_info" ,&$chwinfo,$kheads,$headers,$headers,$repeat,$iter);
			unset($chwinfo);
			if(!$last){
				list($chwinfo,$kheads,$repeat) = $q->loadListExport (null,true);
				if(count($chwinfo) > 0 && $repeat === false){
					$last=true;
				}
				++$iter;
			}else {
				$last = false;
			}
		}
	}

	$headers = array('cbc_client_id',
	'client_adm_no', 'client_first_name', 'client_other_name', 'client_last_name',
	'cbc_name',
	'cbc_village',
	'cbc_center_id',
	'clinic_name',
	'cbc_location',
	'cbc_entry_date',
	'cbc_adm_no',
	'cbc_old',
	'cbc_sex',
	'cbc_age',
	'cbc_hbcare',
	'cbc_adh_support',
	'cbc_remarks',
	'cbc_refers',
	'cbc_refers_note');

	$iter=0;
	$last=false;
	$q = new DBQuery();
	$q->addTable('cbc_info');
	list($cbcinfo,$kheads) = $q->loadListExport ();
	
	if(count($cbcinfo) == 0){
		$iter=1;
		$repeat=false;
	}else{
		$clipos=array_search('cbc_client_id',$kheads);
		$hospos=array_search('cbc_center_id',$kheads);

		while($repeat || $iter == 0 || $last === true){
			foreach ($cbcinfo as $key => $vars) {
				$cclient=$clients[$vars[$clipos]];
				$cbcinfo[$key][]=$cclient['client_adm_no'];
				$cbcinfo[$key][]=$cclient['client_first_name'];
				$cbcinfo[$key][]=$cclient['client_other_name'];
				$cbcinfo[$key][]=$cclient['client_last_name'];
				$cbcinfo[$key][]=$clinics[$vars[$hospos]]['clinic_name'];
			}
			$kheads=array_merge($kheads,array('client_adm_no','client_first_name','client_other_name','client_last_name','clinic_name'));
			$mwriter->store("CBC_info" ,&$cbcinfo,$kheads,$headers,$headers,$repeat,$iter);
			unset($cbcinfo);
			if(!$last){
				list($cbcinfo,$kheads,$repeat) = $q->loadListExport (null,true);
				if(count($cbcinfo) > 0 && $repeat === false){
					$last=true;
				}
				++$iter;
			}else {
				$last = false;
			}
		}
	}
	
	
	
	$mwriter->close();
	// Let's send the file
	//echo 'top usage '.bit2text(memory_get_peak_usage());

} else {
	$AppUI->setMsg ( "clientIdError", UI_MSG_ERROR );
	$AppUI->redirect ();
}
?>
