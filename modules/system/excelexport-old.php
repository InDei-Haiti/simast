<?php
require_once( "$baseDir/lib/Spreadsheet/Excel/Writer.php" ) ;
// get GETPARAMETER for client_id
$client_id = 1;

$canRead = !getDenyRead( 'clients' );
if (!$canRead) {
	$AppUI->redirect( "m=public&a=access_denied" );
}

function writeWorksheet(&$worksheet, &$format, $headers, $data, $keys)
{
	for ($rowcount=0; $rowcount<count($headers);$rowcount++)
	{
		$worksheet->write(0, $rowcount, $headers[$rowcount],$format);
	}
	for ($datacount=0; $datacount<count($data);$datacount++)
	{	
		$colcount = 0;
		foreach ($keys as $key)
		{
			$worksheet->write($datacount+1, $colcount, $data[$datacount][$key]);
			//echo $data[$datacount][$key];
			$colcount++;
		}
	}
	
}
if (1 == 1)
	 {
		//export clients
		// Creating a workbook
		$workbook = new Spreadsheet_Excel_Writer();

		// sending HTTP headers
		$file_name= $dPconfig['company_name'] . ".xls";
		$workbook->send($file_name);
		// The actual data
		
		
		// Creating a worksheet for clinics
		$worksheet =& $workbook->addWorksheet("clinics");
		$format_bold =& $workbook->addFormat();
		$format_bold->setBold();
		

		$headers = array('clinic_name','clinic_phone1','clinic_phone2','clinic_fax','clinic_address1','clinic_address2','clinic_city','clinic_state','clinic_zip','clinic_primary_url','clinic_owner','clinic_description','clinic_type','clinic_email');
		$keys = array('clinic_name','clinic_phone1','clinic_phone2','clinic_fax','clinic_address1','clinic_address2','clinic_city','clinic_state','clinic_zip','clinic_primary_url','clinic_owner','clinic_description','clinic_type','clinic_email');
		


		//$text = sprintf("%s\r\n","\"First Name\",\"Middle Name\",\"Last Name\",\"Entry Date\"");
		$q  = new DBQuery;
		$q->addTable('clinics', 'cli');
		$q->innerJoin('counselling_info', 'ci', 'cli.clinic_id = ci.counselling_clinic');
		$q->addQuery('distinct cli.*');
		$clinics = $q->loadList();
		writeWorksheet($worksheet, $format_bold, $headers, $clinics , $keys);
		
		
		// Creating a worksheet for clients
		$worksheet =& $workbook->addWorksheet("clients");
		$format_bold =& $workbook->addFormat();
		$format_bold->setBold();
		

		$headers = array('client_adm_no', 'client_first_name', 'client_other_name', 'client_last_name', 'client_entry_date');
		$keys = array('client_adm_no', 'client_first_name', 'client_other_name', 'client_last_name', 'client_entry_date');
		//$text = sprintf("%s\r\n","\"First Name\",\"Middle Name\",\"Last Name\",\"Entry Date\"");
		$q  = new DBQuery;
		$q->addTable('clients', 'cl');
		$q->addQuery('distinct cl.*');
		$clients = $q->loadList();
		writeWorksheet($worksheet, $format_bold, $headers, $clients , $keys);
	
		// Creating a worksheet for counselling info
		$worksheet =& $workbook->addWorksheet("Intake_PCR");
		$format_bold =& $workbook->addFormat();
		$format_bold->setBold();
		$headers = array ('client_adm_no','client_first_name','client_other_name','client_last_name','clinic_name','counselling_entry_date','counselling_clinic','counselling_staff_id','counselling_staff_name','contact_first_name','contact_other_name','contact_last_name','counselling_referral_source','counselling_total_orphan','counselling_dob','counselling_age_yrs','counselling_age_months','counselling_age_status','counselling_place_of_birth','counselling_birth_area','counselling_mode_birth','counselling_gestation_period','counselling_birth_weight','counselling_mothers_status_known','counselling_mother_antenatal','counselling_mother_pmtct','counselling_mother_illness_pregnancy','counselling_mother_illness_pregnancy_notes','counselling_breastfeeding','counselling_breastfeeding_duration','counselling_other_breastfeeding_duration','counselling_child_prenatal','counselling_child_single_nvp','counselling_child_nvp_date','counselling_child_nvp_notes','counselling_child_azt','counselling_child_azt_date','counselling_no_doses',
'counselling_mother_treatment','counselling_mother_art_pregnancy','counselling_mother_date_art','counselling_mother_cd4','counselling_mother_date_cd4','counselling_determine_date','counselling_determine','counselling_bioline_date','counselling_bioline','counselling_unigold_date','counselling_unigold','counselling_elisa_date','counselling_elisa','counselling_pcr1_date','counselling_pcr1','counselling_pcr2_date','counselling_pcr2','counselling_rapid12_date','counselling_rapid12','counselling_rapid18_date','counselling_rapid18','counselling_other_date','counselling_other','counselling_notes');		

$keys = array('client_adm_no','client_first_name','client_other_name','client_last_name','clinic_name','counselling_entry_date','counselling_clinic','counselling_staff_id','counselling_staff_name','contact_first_name','contact_other_name','contact_last_name','counselling_referral_source','counselling_total_orphan','counselling_dob','counselling_age_yrs','counselling_age_months','counselling_age_status','counselling_place_of_birth','counselling_birth_area','counselling_mode_birth','counselling_gestation_period','counselling_birth_weight','counselling_mothers_status_known','counselling_mother_antenatal','counselling_mother_pmtct','counselling_mother_illness_pregnancy','counselling_mother_illness_pregnancy_notes','counselling_breastfeeding','counselling_breastfeeding_duration','counselling_other_breastfeeding_duration','counselling_child_prenatal','counselling_child_single_nvp','counselling_child_nvp_date','counselling_child_nvp_notes','counselling_child_azt','counselling_child_azt_date','counselling_no_doses',
'counselling_mother_treatment','counselling_mother_art_pregnancy','counselling_mother_date_art','counselling_mother_cd4','counselling_mother_date_cd4','counselling_determine_date','counselling_determine','counselling_bioline_date','counselling_bioline','counselling_unigold_date','counselling_unigold','counselling_elisa_date','counselling_elisa','counselling_pcr1_date','counselling_pcr1','counselling_pcr2_date','counselling_pcr2','counselling_rapid12_date','counselling_rapid12','counselling_rapid18_date','counselling_rapid18','counselling_other_date','counselling_other','counselling_notes');
			
		$rowcount = 0;
		//$text = sprintf("%s\r\n","\"First Name\",\"Middle Name\",\"Last Name\",\"Entry Date\"");
		$q  = new DBQuery;
		$q->addTable('counselling_info', 'ci');
		$q->innerJoin('clients', 'cl', 'cl.client_id = ci.counselling_client_id');
		$q->leftJoin('clinics', 'cli', 'cli.clinic_id = ci.counselling_clinic');
		$q->leftJoin('contacts', 'c', 'c.contact_id = ci.counselling_staff_id');
		$q->addQuery('distinct cl.client_adm_no, cl.client_first_name, cl.client_other_name, cl.client_last_name, cli.clinic_name, concat_ws(" ", c.contact_first_name, c.contact_other_name, c.contact_last_name) as counselling_staff_name,ci.*');
		$counselling_records = $q->loadList();
		writeWorksheet($worksheet, $format_bold, $headers, $counselling_records, $keys);			

		
		// Creating a worksheet for clinical visits
		$worksheet =& $workbook->addWorksheet("Clinical_Visits");
		$format_bold =& $workbook->addFormat();
		$format_bold->setBold();
		
		$headers = array('client_adm_no','client_first_name','client_other_name','client_last_name','clinic_name','clinical_entry_date','clinical_clinic_id','clinical_staff_id','clinical_staff_name','contact_first_name','contact_other_name','contact_last_name','clinical_age_yrs','clinical_age_months','clinical_child_attending','clinical_caregiver_attending','clinical_caregiver','clinical_illness','clinical_illness_notes','clinical_diarrhoea','clinical_vomiting','clinical_current_complaints','clinical_bloodtest_date','clinical_bloodtest_cd4','clinical_bloodtest_cd4_percentage','clinical_bloodtest_viral','clinical_bloodtest_hb','clinical_xray_results','clinical_other_results','clinical_weight','clinical_height','clinical_zscore','clinical_muac','clinical_hc','clinical_child_unwell','clinical_temp','clinical_resp_rate','clinical_heart_rate','clinical_general','clinical_pallor','clinical_jaundice','clinical_examination_dehydration','clinical_examination_lymph','clinical_mouth','clinical_teeth','clinical_ears','clinical_chest','clinical_chest_clear','clinical_skin_clear','clinical_cardiovascular','clinical_skin','clinical_clubbing','clinical_abdomen','clinical_neurodevt','clinical_musculoskeletal','clinical_oedema','clinical_adherence','clinical_adherence_notes','clinical_child_condition','clinical_diarrhoea_type','clinical_dehydration','clinical_pneumonia','clinical_chronic_lung','clinical_lung_disease','clinical_tb','clinical_tb_treatment_date','clinical_pulmonary','clinical_discharging_ears','clinical_other_diagnoses','clinical_malnutrition','clinical_growth','clinical_assessment_notes','clinical_investigations','clinical_investigations_blood','clinical_investigations_xray','clinical_investigations_notes','clinical_other_drugs','clinical_new_drugs','clinical_on_arvs','clinical_arv_drugs','clinical_tb_treatment','clinical_arv_notes','clinical_who_stage','clinical_who_current','clinical_who_reason','clinical_tb_drugs','clinical_tb_drugs_notes','clinical_septrin','clinical_vitamins','clinical_treatment_status','clinical_arv_reason','clinical_nutritional_support','clinical_nutritional_notes','clinical_referral','clinical_referral_name','clinical_next_date','clinical_notes');
		
		$keys = array('client_adm_no','client_first_name','client_other_name','client_last_name','clinic_name','clinical_entry_date','clinical_clinic_id','clinical_staff_id','clinical_staff_name','contact_first_name','contact_other_name','contact_last_name','clinical_age_yrs','clinical_age_months','clinical_child_attending','clinical_caregiver_attending','clinical_caregiver','clinical_illness','clinical_illness_notes','clinical_diarrhoea','clinical_vomiting','clinical_current_complaints','clinical_bloodtest_date','clinical_bloodtest_cd4','clinical_bloodtest_cd4_percentage','clinical_bloodtest_viral','clinical_bloodtest_hb','clinical_xray_results','clinical_other_results','clinical_weight','clinical_height','clinical_zscore','clinical_muac','clinical_hc','clinical_child_unwell','clinical_temp','clinical_resp_rate','clinical_heart_rate','clinical_general','clinical_pallor','clinical_jaundice','clinical_examination_dehydration','clinical_examination_lymph','clinical_mouth','clinical_teeth','clinical_ears','clinical_chest','clinical_chest_clear','clinical_skin_clear','clinical_cardiovascular','clinical_skin','clinical_clubbing','clinical_abdomen','clinical_neurodevt','clinical_musculoskeletal','clinical_oedema','clinical_adherence','clinical_adherence_notes','clinical_child_condition','clinical_diarrhoea_type','clinical_dehydration','clinical_pneumonia','clinical_chronic_lung','clinical_lung_disease','clinical_tb','clinical_tb_treatment_date','clinical_pulmonary','clinical_discharging_ears','clinical_other_diagnoses','clinical_malnutrition','clinical_growth','clinical_assessment_notes','clinical_investigations','clinical_investigations_blood','clinical_investigations_xray','clinical_investigations_notes','clinical_other_drugs','clinical_new_drugs','clinical_on_arvs','clinical_arv_drugs','clinical_tb_treatment','clinical_arv_notes','clinical_who_stage','clinical_who_current','clinical_who_reason','clinical_tb_drugs','clinical_tb_drugs_notes','clinical_septrin','clinical_vitamins','clinical_treatment_status','clinical_arv_reason','clinical_nutritional_support','clinical_nutritional_notes','clinical_referral','clinical_refferal_name','clinical_next_date','clinical_notes');
		$q  = new DBQuery;
		$q->addTable('clinical_visits', 'cv');
		$q->innerJoin('clients', 'cl', 'cl.client_id = cv.clinical_client_id');
		$q->leftJoin('clinics', 'cli', 'cli.clinic_id = cv.clinical_clinic_id');
		$q->leftJoin('contacts', 'c', 'c.contact_id = cv.clinical_staff_id');
		$q->leftJoin('contacts', 'cr', 'cr.contact_id = cv.clinical_referral');

		$q->addQuery('distinct cl.client_adm_no, cl.client_first_name, cl.client_other_name, cl.client_last_name, concat_ws(" ", c.contact_first_name, c.contact_other_name, c.contact_last_name) as clinical_staff_name, concat_ws(" ", cr.contact_first_name, cr.contact_other_name, cr.contact_last_name) as clinical_referral_name, cli.clinic_name,cv.*');
		//$q->setLimit(200,0);
		//$sql = $q->prepare();
		//print ($sql);
		$clinical_visits = $q->loadList();
		//var_dump($clinical_visits);
		
		if (!empty($clinical_visits))
		{
			writeWorksheet($worksheet, $format_bold, $headers, $clinical_visits, $keys);	
		}
	
	
	
		
	
		// Creating a worksheet for counselling visits
		$worksheet =& $workbook->addWorksheet("Counselling_Visits");
		$format_bold =& $workbook->addFormat();
		$format_bold->setBold();
		
		$headers = array('client_adm_no','client_first_name','client_other_name','client_last_name','clinic_name','counselling_staff_id','counselling_visit_staff_name','contact_first_name','contact_other_name','contact_last_name','counselling_center_id','counselling_entry_date','counselling_visit_type','counselling_caregiver_fname','counselling_caregiver_lname','counselling_caregiver_age','counselling_caregiver_relationship','counselling_caregiver_marital_status','counselling_caregiver_educ_level','counselling_caregiver_employment','counselling_caregiver_income_level','counselling_caregiver_idno','counselling_caregiver_mobile','counselling_caregiver_residence','counselling_child_issues','counselling_other_issues','counselling_caregiver_issues','counselling_caregiver_other_issues','counselling_caregiver_issues2','counselling_caregiver_other_issues2','counselling_child_knows_status','counselling_otheradult_knows_status','counselling_disclosure_response','counselling_disclosure_state','counselling_secondary_caregiver_knows','counselling_primary_caregiver_tested','counselling_father_status','counselling_mother_status','counselling_caregiver_status','counselling_father_treatment','counselling_mother_treatment','counselling_caregiver_treatment','counselling_stigmatization_concern','counselling_counselling_services','counselling_other_services','counselling_notes');

		$keys = array('client_adm_no','client_first_name','client_other_name','client_last_name','clinic_name','counselling_staff_id','counselling_visit_staff_name','contact_first_name','contact_other_name','contact_last_name','counselling_center_id','counselling_entry_date','counselling_visit_type','counselling_caregiver_fname','counselling_caregiver_lname','counselling_caregiver_age','counselling_caregiver_relationship','counselling_caregiver_marital_status','counselling_caregiver_educ_level','counselling_caregiver_employment','counselling_caregiver_income_level','counselling_caregiver_idno','counselling_caregiver_mobile','counselling_caregiver_residence','counselling_child_issues','counselling_other_issues','counselling_caregiver_issues','counselling_caregiver_other_issues','counselling_caregiver_issues2','counselling_caregiver_other_issues2','counselling_child_knows_status','counselling_otheradult_knows_status','counselling_disclosure_response','counselling_disclosure_state','counselling_secondary_caregiver_knows','counselling_primary_caregiver_tested','counselling_father_status','counselling_mother_status','counselling_caregiver_status','counselling_father_treatment','counselling_mother_treatment','counselling_caregiver_treatment','counselling_stigmatization_concern','counselling_counselling_services','counselling_other_services','counselling_notes');
		
		$q  = new DBQuery;
		$q->addTable('counselling_visit', 'cv');
		$q->innerJoin('clients', 'cl', 'cl.client_id = cv.counselling_client_id');
		$q->leftJoin('clinics', 'cli', 'cli.clinic_id = cv.counselling_center_id');
		$q->leftJoin('contacts', 'c', 'c.contact_id = cv.counselling_staff_id');

		$q->addQuery('distinct cl.client_adm_no, cl.client_first_name, cl.client_other_name, cli.clinic_name, concat_ws(" ", c.contact_first_name, c.contact_other_name, c.contact_last_name) as counselling_visit_staff_name,cl.client_last_name,cli.clinic_name, cv.*');
		$counselling_visits = $q->loadList();
        writeWorksheet($worksheet, $format_bold, $headers, $counselling_visits, $keys);					
		
		
		
		// Creating a worksheet for social visits
		$worksheet =& $workbook->addWorksheet("Social_Visits");
		$format_bold =& $workbook->addFormat();
		$format_bold->setBold();
		
		$headers = array('client_adm_no','client_first_name','client_other_name','client_last_name','clinic_name','social_id','social_staff_id','social_staff_name','contact_first_name','contact_other_name','contact_last_name','social_clinic_id','social_entry_date','social_client_status','social_visit_type','social_death','social_death_notes','social_death_date','social_caregiver_change','social_caregiver_change_notes','social_caregiver_fname','social_caregiver_lname','social_caregiver_age','social_caregiver_status','social_caregiver_relationship','social_caregiver_education','social_caregiver_employment','social_caregiver_income','social_caregiver_idno','social_caregiver_mobile','social_caregiver_health','social_caregiver_health_child_impact','social_residence_mobile','social_residence','social_caregiver_employment_change','social_caregiver_new_employment','social_caregiver_new_employment_desc','social_caregiver_new_income','social_school_attendance','social_school','social_reason_not_attending','social_relocation','social_iga','social_placement','social_succession_planning','social_legal','social_nursing','social_transport','social_education','social_food','social_rent','social_solidarity','social_direct_support','social_medical_support','social_medical_support_desc','social_other_support','social_othersupport_value','social_permanency_value','social_succession_value','social_legal_value','social_nursing_value','social_transport_value','social_education_value','social_food_value','social_rent_value','social_solidarity_value','social_directsupport_value','social_medicalsupport_value','social_risk_level','social_notes','social_change');

		$keys = array('client_adm_no','client_first_name','client_other_name','client_last_name','clinic_name','social_id','social_staff_id','social_staff_name','contact_first_name','contact_other_name','contact_last_name','social_clinic_id','social_entry_date','social_client_status','social_visit_type','social_death','social_death_notes','social_death_date','social_caregiver_change','social_caregiver_change_notes','social_caregiver_fname','social_caregiver_lname','social_caregiver_age','social_caregiver_status','social_caregiver_relationship','social_caregiver_education','social_caregiver_employment','social_caregiver_income','social_caregiver_idno','social_caregiver_mobile','social_caregiver_health','social_caregiver_health_child_impact','social_residence_mobile','social_residence','social_caregiver_employment_change','social_caregiver_new_employment','social_caregiver_new_employment_desc','social_caregiver_new_income','social_school_attendance','social_school','social_reason_not_attending','social_relocation','social_iga','social_placement','social_succession_planning','social_legal','social_nursing','social_transport','social_education','social_food','social_rent','social_solidarity','social_direct_support','social_medical_support','social_medical_support_desc','social_other_support','social_othersupport_value','social_permanency_value','social_succession_value','social_legal_value','social_nursing_value','social_transport_value','social_education_value','social_food_value','social_rent_value','social_solidarity_value','social_directsupport_value','social_medicalsupport_value','social_risk_level','social_notes','social_change');
		$q  = new DBQuery;
		$q->addTable('social_visit', 'sv');
		$q->innerJoin('clients', 'cl', 'cl.client_id = sv.social_client_id');
		$q->leftJoin('clinics', 'cli', 'cli.clinic_id = sv.social_clinic_id');
		$q->leftJoin('contacts', 'c', 'c.contact_id = sv.social_staff_id');

		$q->addQuery('cl.client_adm_no, cl.client_first_name, cl.client_other_name, cl.client_last_name, cli.clinic_name,concat_ws(" ", c.contact_first_name, c.contact_other_name, c.contact_last_name) as social_staff_name,sv.*');
		$social_visits = $q->loadList();
        writeWorksheet($worksheet, $format_bold, $headers, $social_visits, $keys);	
		
		
		// Creating a worksheet for social services details
		$worksheet =& $workbook->addWorksheet("Social_Services_Details");
		$format_bold =& $workbook->addFormat();
		$format_bold->setBold();
		
		$headers = array('client_adm_no','client_first_name','client_other_name','client_last_name','social_services_client_id','social_services_social_id','social_services_service_id','social_services_date',' social_services_notes');

		$keys = array('client_adm_no','client_first_name','client_other_name','client_last_name','social_services_client_id','social_services_social_id','social_services_service_id','social_services_date',' social_services_notes');
		$q  = new DBQuery;
		$q->addTable('social_services', 'ss');
		$q->innerJoin('clients', 'cl', 'cl.client_id = ss.social_services_client_id');
		$q->addQuery('cl.client_adm_no, cl.client_first_name, cl.client_other_name, cl.client_last_name, ss.*');
		$social_service_records = $q->loadList();
        writeWorksheet($worksheet, $format_bold, $headers, $social_service_records, $keys);	


		
		// Creating a worksheet for nutritional visits
		$worksheet =& $workbook->addWorksheet("Nutritional_Visits");
		$format_bold =& $workbook->addFormat();
		$format_bold->setBold();
		
		$headers = array('client_adm_no','client_first_name','client_other_name','client_last_name','clinic_name','nutrition_staff_id','nutrition_staff_name','contact_first_name','contact_other_name','contact_last_name','nutrition_entry_date','nutrition_center','nutrition_gender','nutrition_age_yrs','nutrition_age_months','nutrition_age_status','nutrition_caregiver_type','nutrition_caregiver_type_notes','nutrition_weight','nutrition_height','nutrition_zscore','nutrition_muac','nutrition_wfh','nutrition_wfa','nutrition_bmi','nutrition_blacktea','nutrition_whitetea','nutrition_bread','nutrition_porridge','nutrition_breastfeeding','nutrition_formula_milk','nutrition_carbohydrates','nutrition_meat','nutrition_pancake','nutrition_eggs','nutrition_legumes','nutrition_milk','nutrition_vegetables','nutrition_fruit','nutrition_diet_history_notes','nutrition_diet_history_others','nutrition_food_enrichment','nutrition_water_access','nutrition_water_purification','nutrition_water_purification_notes','nutrition_food_enrichment_notes','nutrition_quantity','nutrition_quality','nutrition_poor_preparation','nutrition_mixed_feeding','nutrition_unclean_drinking_water','nutrition_education','nutrition_counselling','nutrition_demonstration','nutrition_dietary_supplement','nutrition_nan','nutrition_unimix','nutrition_harvest_pro','nutrition_wfp','nutrition_insta','nutrition_rutf','nutrition_other','nutrition_other_service','nutrition_notes');

		$keys = array('client_adm_no','client_first_name','client_other_name','client_last_name','clinic_name','nutrition_staff_id','nutrition_staff_name','contact_first_name','contact_other_name','contact_last_name','nutrition_entry_date','nutrition_center','nutrition_gender','nutrition_age_yrs','nutrition_age_months','nutrition_age_status','nutrition_caregiver_type','nutrition_caregiver_type_notes','nutrition_weight','nutrition_height','nutrition_zscore','nutrition_muac','nutrition_wfh','nutrition_wfa','nutrition_bmi','nutrition_blacktea','nutrition_whitetea','nutrition_bread','nutrition_porridge','nutrition_breastfeeding','nutrition_formula_milk','nutrition_carbohydrates','nutrition_meat','nutrition_pancake','nutrition_eggs','nutrition_legumes','nutrition_milk','nutrition_vegetables','nutrition_fruit','nutrition_diet_history_notes','nutrition_diet_history_others','nutrition_food_enrichment','nutrition_water_access','nutrition_water_purification','nutrition_water_purification_notes','nutrition_food_enrichment_notes','nutrition_quantity','nutrition_quality','nutrition_poor_preparation','nutrition_mixed_feeding','nutrition_unclean_drinking_water','nutrition_education','nutrition_counselling','nutrition_demonstration','nutrition_dietary_supplement','nutrition_nan','nutrition_unimix','nutrition_harvest_pro','nutrition_wfp','nutrition_insta','nutrition_rutf','nutrition_other','nutrition_other_service','nutrition_notes');
		$q  = new DBQuery;
		$q->addTable('nutrition_visit', 'nv');
		$q->innerJoin('clients', 'cl', 'cl.client_id = nv.nutrition_client_id');
		$q->leftJoin('clinics', 'cli', 'cli.clinic_id = nv.nutrition_center');
		$q->leftJoin('contacts', 'c', 'c.contact_id = nv.nutrition_staff_id');

		$q->addQuery('cl.client_adm_no, cl.client_first_name, cl.client_other_name, cl.client_last_name,cli.clinic_name,concat_ws(" ", c.contact_first_name, c.contact_other_name, c.contact_last_name) as nutrition_staff_name, nv.*');
		$nutrition_visits = $q->loadList();
        writeWorksheet($worksheet, $format_bold, $headers, $nutrition_visits, $keys);	
		
		
		
		// Creating a worksheet for medical assessment on admission
		$worksheet =& $workbook->addWorksheet("Medical_Assessment");
		$format_bold =& $workbook->addFormat();
		$format_bold->setBold();
		
		$headers = array('client_adm_no','client_first_name','client_other_name','client_last_name','clinic_name','medical_id','medical_staff_id','medical_staff_name','contact_first_name','contact_other_name','contact_last_name','medical_clinic_id','medical_gender','medical_age_yrs','medical_age_months','medical_entry_date','medical_birth_location','medical_delivery','medical_birth_problems','medical_transferred','medical_other_programme','medical_birth_weight','medical_pmtct','medical_mother_arv_given','medical_child_arv_given','medical_immunization_status','medical_card_seen','medical_breastfeeding','medical_exclusive_breastfeeding','medical_bf_duration','medical_father_hiv_status','medical_father_arv','medical_mother_hiv_status','medical_mother_arv','medical_no_siblings_alive','medical_no_siblings_deceased','medical_tb_contact','medical_tb_contact_person','medical_tb_date_diagnosed','medical_tb_pulmonary','medical_tb_type','medical_tb_type_desc','medical_tb_bodysite','medical_tb_date1','medical_tb_date2','medical_tb_date3','medical_history_pneumonia','medical_history_diarrhoea','medical_history_skin_rash','medical_history_ear_discharge','medical_history_fever','medical_history_oral_rush','medical_history_mouth_ulcers','medical_history_malnutrition','medical_history_prev_nutrition','medical_history_notes','medical_arv_status','medical_arv1','medical_arv1_startdate','medical_arv1_enddate','medical_arv2','medical_arv2_startdate','medical_arv2_enddate','medical_arv_side_effects','medical_arv_adherence','medical_school_attendance','medical_school_class','medical_educ_progress','medical_sensory_hearing','medical_sensory_vision','medical_sensory_motor_ability','medical_sensory_speech_language','medical_sensory_social_skills','medical_meals_per_day','medical_food_types','medical_current_complaints','medical_weight','medical_height','medical_zscore','medical_muac','medical_hc','medical_condition','medical_temp','medical_conditions','medical_dehydration','medical_parotids','medical_lymph','medical_eyes','medical_eyes_notes','medical_ear_discharge','medical_ear_discharge_location','medical_throat','medical_mouth_thrush','medical_mouth_ulcers','medical_mouth_teeth','medical_oldlesions','medical_currentlesions','medical_heartrate','medical_recession','medical_percussion','medical_location','medical_breath_sounds','medical_breathlocation','medical_other_sounds','medical_soundlocation','medical_pulserate','medical_apex_beat','medical_precordial','medical_femoral','medical_heart_sound','medical_heart_type','medical_abdomen_distended','medical_adbomen_feel','medical_abdomen_tender','medical_abdomen_fluid','medical_liver_costal','medical_spleen_costal','medical_masses','medical_umbilical_hernia','medical_testes','medical_which_testes','medical_genitals_female_notes','medical_genitals_feel','medical_penis','medical_genitals_female','medical_pubertal','medical_gait','medical_handuse','medical_weakness','medical_tone','medical_tendon_legs','medical_tendon_arms','medical_abnormal_movts','medical_movts_impaired','medical_movts_impaired_desc','medical_joints_swelling','medical_joints_swelling_desc','medical_motor','medical_musc_notes','medical_hiv_status','medical_cd4','medical_cd4_percentage','medical_who_clinical_stage','medical_immuno_stage','medical_tests','medical_referral','medical_referral_name','medical_notes');

		$keys = array('client_adm_no','client_first_name','client_other_name','client_last_name','clinic_name','medical_id','medical_staff_id','medical_staff_name','contact_first_name','contact_other_name','contact_last_name','medical_clinic_id','medical_gender','medical_age_yrs','medical_age_months','medical_entry_date','medical_birth_location','medical_delivery','medical_birth_problems','medical_transferred','medical_other_programme','medical_birth_weight','medical_pmtct','medical_mother_arv_given','medical_child_arv_given','medical_immunization_status','medical_card_seen','medical_breastfeeding','medical_exclusive_breastfeeding','medical_bf_duration','medical_father_hiv_status','medical_father_arv','medical_mother_hiv_status','medical_mother_arv','medical_no_siblings_alive','medical_no_siblings_deceased','medical_tb_contact','medical_tb_contact_person','medical_tb_date_diagnosed','medical_tb_pulmonary','medical_tb_type','medical_tb_type_desc','medical_tb_bodysite','medical_tb_date1','medical_tb_date2','medical_tb_date3','medical_history_pneumonia','medical_history_diarrhoea','medical_history_skin_rash','medical_history_ear_discharge','medical_history_fever','medical_history_oral_rush','medical_history_mouth_ulcers','medical_history_malnutrition','medical_history_prev_nutrition','medical_history_notes','medical_arv_status','medical_arv1','medical_arv1_startdate','medical_arv1_enddate','medical_arv2','medical_arv2_startdate','medical_arv2_enddate','medical_arv_side_effects','medical_arv_adherence','medical_school_attendance','medical_school_class','medical_educ_progress','medical_sensory_hearing','medical_sensory_vision','medical_sensory_motor_ability','medical_sensory_speech_language','medical_sensory_social_skills','medical_meals_per_day','medical_food_types','medical_current_complaints','medical_weight','medical_height','medical_zscore','medical_muac','medical_hc','medical_condition','medical_temp','medical_conditions','medical_dehydration','medical_parotids','medical_lymph','medical_eyes','medical_eyes_notes','medical_ear_discharge','medical_ear_discharge_location','medical_throat','medical_mouth_thrush','medical_mouth_ulcers','medical_mouth_teeth','medical_oldlesions','medical_currentlesions','medical_heartrate','medical_recession','medical_percussion','medical_location','medical_breath_sounds','medical_breathlocation','medical_other_sounds','medical_soundlocation','medical_pulserate','medical_apex_beat','medical_precordial','medical_femoral','medical_heart_sound','medical_heart_type','medical_abdomen_distended','medical_adbomen_feel','medical_abdomen_tender','medical_abdomen_fluid','medical_liver_costal','medical_spleen_costal','medical_masses','medical_umbilical_hernia','medical_testes','medical_which_testes','medical_genitals_female_notes','medical_genitals_feel','medical_penis','medical_genitals_female','medical_pubertal','medical_gait','medical_handuse','medical_weakness','medical_tone','medical_tendon_legs','medical_tendon_arms','medical_abnormal_movts','medical_movts_impaired','medical_movts_impaired_desc','medical_joints_swelling','medical_joints_swelling_desc','medical_motor','medical_musc_notes','medical_hiv_status','medical_cd4','medical_cd4_percentage','medical_who_clinical_stage','medical_immuno_stage','medical_tests','medical_referral','medical_referral_name','medical_notes');
		$q  = new DBQuery;
		$q->addTable('medical_assessment', 'ma');
		$q->innerJoin('clients', 'cl', 'cl.client_id = ma.medical_client_id');
		$q->leftJoin('clinics', 'cli', 'cli.clinic_id = ma.medical_clinic_id');
		$q->leftJoin('contacts', 'c', 'c.contact_id = ma.medical_staff_id');
		$q->leftJoin('contacts', 'cr', 'cr.contact_id = ma.medical_referral');

		$q->addQuery('cl.client_adm_no, cl.client_first_name, cl.client_other_name, cl.client_last_name, cli.clinic_name,concat_ws(" ", c.contact_first_name, c.contact_other_name, c.contact_last_name) as medical_staff_name,concat_ws(" ", cr.contact_first_name, cr.contact_other_name, cr.contact_last_name) as medical_referral_name,ma.*');
		$medical_assessments = $q->loadList();
        writeWorksheet($worksheet, $format_bold, $headers, $medical_assessments, $keys);

		
		// Creating a worksheet for admission details
		$worksheet =& $workbook->addWorksheet("Admission_Details");
		$format_bold =& $workbook->addFormat();
		$format_bold->setBold();
		
		$headers = array('client_adm_no','client_first_name','client_other_name','client_last_name','clinic_name','admission_id','admission_staff_id','admission_staff_name','contact_first_name','contact_other_name','contact_last_name','admission_clinic_id','admission_dob','admission_age_yrs','admission_age_months','admission_age_status','admission_gender','admission_residence','admission_location','location_name','admission_entry_date','admission_school_level','admission_reason_not_attending','admission_reason_not_attending_notes','admission_total_orphan','admission_province','admission_district','admission_village','admission_father_fname','admission_father_lname','admission_father_age','admission_father_status','admission_father_health_status','admission_father_raising_child','admission_father_marital_status','admission_father_educ_level','admission_father_employment','admission_father_income','admission_father_idno','admission_father_mobile','admission_mother_fname','admission_mother_lname','admission_mother_age','admission_mother_status','admission_mother_health_status','admission_mother_raising_child','admission_mother_marital_status','admission_mother_educ_level','admission_mother_employment','admission_mother_income','admission_mother_idno','admission_mother_mobile','admission_caregiver_fname','admission_caregiver_lname','admission_caregiver_age','admission_caregiver_status','admission_caregiver_health_status','admission_caregiver_relationship','admission_caregiver_marital_status','admission_caregiver_educ_level','admission_caregiver_employment','admission_caregiver_income','admission_caregiver_idno','admission_caregiver_mobile','admission_family_income','admission_risk_level','admission_risk_level_description','admission_notes');

		$keys = array('client_adm_no','client_first_name','client_other_name','client_last_name','clinic_name','admission_id','admission_staff_id','admission_staff_name','contact_first_name','contact_other_name','contact_last_name','admission_clinic_id','admission_dob','admission_age_yrs','admission_age_months','admission_age_status','admission_gender','admission_residence','admission_location','location_name','admission_entry_date','admission_school_level','admission_reason_not_attending','admission_reason_not_attending_notes','admission_total_orphan','admission_province','admission_district','admission_village','admission_father_fname','admission_father_lname','admission_father_age','admission_father_status','admission_father_health_status','admission_father_raising_child','admission_father_marital_status','admission_father_educ_level','admission_father_employment','admission_father_income','admission_father_idno','admission_father_mobile','admission_mother_fname','admission_mother_lname','admission_mother_age','admission_mother_status','admission_mother_health_status','admission_mother_raising_child','admission_mother_marital_status','admission_mother_educ_level','admission_mother_employment','admission_mother_income','admission_mother_idno','admission_mother_mobile','admission_caregiver_fname','admission_caregiver_lname','admission_caregiver_age','admission_caregiver_status','admission_caregiver_health_status','admission_caregiver_relationship','admission_caregiver_marital_status','admission_caregiver_educ_level','admission_caregiver_employment','admission_caregiver_income','admission_caregiver_idno','admission_caregiver_mobile','admission_family_income','admission_risk_level','admission_risk_level_description','admission_notes');
		$q  = new DBQuery;
		$q->addTable('admission_info', 'ai');
		$q->innerJoin('clients', 'cl', 'cl.client_id = ai.admission_client_id');
		$q->leftJoin('clinics', 'cli', 'cli.clinic_id = ai.admission_clinic_id');
		$q->leftJoin('clinic_location', 'clo', 'clo.clinic_location_id = ai.admission_location');
		$q->leftJoin('contacts', 'c', 'c.contact_id = ai.admission_staff_id');

		$q->addQuery('cl.client_adm_no, cl.client_first_name, cl.client_other_name, cl.client_last_name, cli.clinic_name,concat_ws(" ", c.contact_first_name, c.contact_other_name, c.contact_last_name) as admission_staff_name,clo.clinic_location as location_name,ai.*');
		$admission_records = $q->loadList();
        writeWorksheet($worksheet, $format_bold, $headers, $admission_records, $keys);	

		
		// Creating a worksheet for family details
		$worksheet =& $workbook->addWorksheet("Household_Details");
		$format_bold =& $workbook->addFormat();
		$format_bold->setBold();
		
		$headers = array('client_adm_no','client_first_name','client_other_name','client_last_name','household_admission_id','household_social_id','household_name','household_yob','household_relationship','household_gender','household_notes'
);

		$keys = array('client_adm_no','client_first_name','client_other_name','client_last_name','household_admission_id','household_social_id','household_name','household_yob','household_relationship','household_gender','household_notes'
);
		$q  = new DBQuery;
		$q->addTable('household_info', 'hi');
		$q->innerJoin('clients', 'cl', 'cl.client_id = hi.household_client_id');
		$q->addQuery('cl.client_adm_no, cl.client_first_name, cl.client_other_name, cl.client_last_name, hi.*');
		$household_records = $q->loadList();
        writeWorksheet($worksheet, $format_bold, $headers, $household_records, $keys);		
		
	
		// Creating a worksheet for Medical History
		$worksheet =& $workbook->addWorksheet("Medical_History");
		$format_bold =& $workbook->addFormat();
		$format_bold->setBold();
		
		$headers = array('client_adm_no','client_first_name','client_other_name','client_last_name','medical_history_medical_id','medical_history_hospital','medical_history_date','medical_history_diagnosis','medical_history_notes'
);

		$keys = array('client_adm_no','client_first_name','client_other_name','client_last_name','medical_history_medical_id','medical_history_hospital','medical_history_date','medical_history_diagnosis','medical_history_notes'
);
		$q  = new DBQuery;
		$q->addTable('medical_history', 'mi');
		$q->innerJoin('clients', 'cl', 'cl.client_id = mi.medical_history_client_id');
		$q->addQuery('cl.client_adm_no, cl.client_first_name, cl.client_other_name, cl.client_last_name, mi.*');
		$medical_history_records = $q->loadList();
        writeWorksheet($worksheet, $format_bold, $headers, $medical_history_records, $keys);		
			
		// Creating a worksheet for Medication History
		$worksheet =& $workbook->addWorksheet("Medication_History");
		$format_bold =& $workbook->addFormat();
		$format_bold->setBold();
		
		$headers = array('client_adm_no','client_first_name','client_other_name','client_last_name','medications_history_drug','medications_history_dose','medications_history_frequency','medications_history_notes');

		$keys = array('client_adm_no','client_first_name','client_other_name','client_last_name','medications_history_drug','medications_history_dose','medications_history_frequency','medications_history_notes');
		$q  = new DBQuery;
		$q->addTable('medications_history', 'mh');
		$q->innerJoin('clients', 'cl', 'cl.client_id = mh.medications_history_client_id');
		$q->addQuery(' cl.client_adm_no, cl.client_first_name, cl.client_other_name, cl.client_last_name, mh.*');
		$medications_history = $q->loadList();
        writeWorksheet($worksheet, $format_bold, $headers, $medications_history, $keys);		
		
		
		
		
		// Creating a worksheet for mortality info
		$worksheet =& $workbook->addWorksheet("Mortality");
		$format_bold =& $workbook->addFormat();
		$format_bold->setBold();
		
		$headers = array('client_adm_no','client_first_name','client_other_name','client_last_name','clinic_name','mortality_entry_date','mortality_clinic_id','mortality_age_yrs','mortality_age_months','mortality_age_status','mortality_date','mortality_death_type','mortality_death_type_notes','mortality_informant','mortality_hospital','mortality_hospital_adm_date','mortality_relative_report_date','mortality_symptoms','mortality_time_course','mortality_treatment','mortality_referral','mortality_hospital_referral','mortality_hospital_adm_notes','mortality_cause_given','mortality_cause_desc','mortality_clinical_officer','mortality_clinical_officer_date','mortality_postmortem','mortality_cause_pm','mortality_likely_cause','mortality_notes');

		$keys = array('client_adm_no','client_first_name','client_other_name','client_last_name','clinic_name','mortality_entry_date','mortality_clinic_id','mortality_age_yrs','mortality_age_months','mortality_age_status','mortality_date','mortality_death_type','mortality_death_type_notes','mortality_informant','mortality_hospital','mortality_hospital_adm_date','mortality_relative_report_date','mortality_symptoms','mortality_time_course','mortality_treatment','mortality_referral','mortality_hospital_referral','mortality_hospital_adm_notes','mortality_cause_given','mortality_cause_desc','mortality_clinical_officer','mortality_clinical_officer_date','mortality_postmortem','mortality_cause_pm','mortality_likely_cause','mortality_notes');
		$q  = new DBQuery;
		$q->addTable('mortality_info', 'mi');
		$q->leftJoin('clinics', 'cli', 'cli.clinic_id = mi.mortality_clinic_id');
		$q->innerJoin('clients', 'cl', 'cl.client_id = mi.mortality_client_id');
		$q->addQuery('cl.client_adm_no, cl.client_first_name, cl.client_other_name, cl.client_last_name,cli.clinic_name, mi.* ');
		$mortality_records = $q->loadList();
        writeWorksheet($worksheet, $format_bold, $headers, $mortality_records, $keys);	

		
		
		// Creating a worksheet for staff info
		$worksheet =& $workbook->addWorksheet("Staff");
		$format_bold =& $workbook->addFormat();
		$format_bold->setBold();
		
		$headers = array('contact_first_name', 'contact_other_name', 'contact_last_name', 'contact_order_by', 'contact_title', 'contact_birthday', 'contact_job', 'contact_client', 'contact_department', 'contact_type', 'contact_email', 'contact_email2', 'contact_url', 'contact_phone', 'contact_phone2', 'contact_fax', 'contact_mobile', 'contact_address1', 'contact_address2', 'contact_city', 'contact_state', 'contact_zip', 'contact_country', 'contact_jabber', 'contact_icq', 'contact_msn', 'contact_yahoo', 'contact_aol', 'contact_notes', 'contact_project', 'contact_icon', 'contact_owner', 'contact_private');

		$keys = array('contact_first_name', 'contact_other_name', 'contact_last_name', 'contact_order_by', 'contact_title', 'contact_birthday', 'contact_job', 'contact_client', 'contact_department', 'contact_type', 'contact_email', 'contact_email2', 'contact_url', 'contact_phone', 'contact_phone2', 'contact_fax', 'contact_mobile', 'contact_address1', 'contact_address2', 'contact_city', 'contact_state', 'contact_zip', 'contact_country', 'contact_jabber', 'contact_icq', 'contact_msn', 'contact_yahoo', 'contact_aol', 'contact_notes', 'contact_project', 'contact_icon', 'contact_owner', 'contact_private');
		
		$q  = new DBQuery;
		$q->addTable('contacts','con');
		$q->leftJoin('users','u', 'u.user_contact = con.contact_id');
		$q->addQuery('contact_first_name, contact_other_name, contact_last_name, contact_order_by, contact_title, contact_birthday, contact_job, contact_client, contact_department, contact_type, contact_email, contact_email2, contact_url, contact_phone, contact_phone2, contact_fax, contact_mobile, contact_address1, contact_address2, contact_city, contact_state, contact_zip, contact_country, contact_jabber, contact_icq, contact_msn, contact_yahoo, contact_aol, contact_notes, contact_project, contact_icon, contact_owner, contact_private');
		$q->addWhere('contact_id <> 1');
		$staff = $q->loadList();		
		writeWorksheet($worksheet, $format_bold, $headers, $staff, $keys);	

		
		
		// Creating a worksheet for clinic location
		$worksheet =& $workbook->addWorksheet("Clinic Location");
		$format_bold =& $workbook->addFormat();
		$format_bold->setBold();
		
		$headers = array('clinic_location_clinic_id', 'clinic_name', 'clinic_location', 'clinic_location_notes' );

		$keys = array('clinic_location_clinic_id', 'clinic_name','clinic_location', 'clinic_location_notes' );
		
		$q  = new DBQuery;
		$q->addTable('clinic_location','con');
		$q->innerJoin('clinics','cl',' cl.clinic_id = con.clinic_location_clinic_id');
		$q->addQuery('con.clinic_location_clinic_id,cl.clinic_name,con.clinic_location, con.clinic_location_notes' );
		
		//$sql = $q->prepare();
		//print $sql;		

		$clinic_location = $q->loadList();
		//var_dump($clinic_location);
		//exit;
		writeWorksheet($worksheet, $format_bold, $headers, $clinic_location, $keys);

		
		

		
		// Creating a worksheet for Group Activities
		$worksheet =& $workbook->addWorksheet("Group_Activities");
		$format_bold =& $workbook->addFormat();
		$format_bold->setBold();
		
		$headers = array('activity_date','activity_curriculum','activity_curriculum_desc','activity_entry_date','activity_description', 'activity_clinic', 'clinic_name','activity_male_count', 'activity_female_count','activity_notes'
);

		$keys = array('activity_date','activity_curriculum','activity_curriculum_desc','activity_entry_date','activity_description', 'activity_clinic', 'clinic_name','activity_male_count', 'activity_female_count','activity_notes'
);
		$q  = new DBQuery;
		$q->addTable('activity', 'a');
		$q->innerJoin('clinics' , 'c', 'c.clinic_id = a.activity_clinic');
		$q->addQuery('a.*, c.clinic_name as activity_clinic_name');
		$activity_records = $q->loadList();
        writeWorksheet($worksheet, $format_bold, $headers, $activity_records, $keys);	
		
		
		// Creating a worksheet for Trainings
		$worksheet =& $workbook->addWorksheet("Trainings");
		$format_bold =& $workbook->addFormat();
		$format_bold->setBold();
		
		$headers = array('training_date','training_entry_date','training_name','training_clinic','clinic_name','training_notes'
);

		$keys = array('training_date','training_entry_date','training_name','training_clinic','clinic_name','training_notes'
);
		$q  = new DBQuery;
		$q->addTable('trainings', 't');
		$q->innerJoin('clinics' , 'c', 'c.clinic_id = t.training_clinic');
		$q->addQuery('t.*, c.clinic_name as clinic_name');
		$training_records = $q->loadList();
		
        writeWorksheet($worksheet, $format_bold, $headers, $training_records, $keys);	

		
		// Creating a worksheet for Training Facilitators/Activities
		$worksheet =& $workbook->addWorksheet("Activity_Facilitator");
		$format_bold =& $workbook->addFormat();
		$format_bold->setBold();
		
		$headers = array('activity_description', 'activity_date', 'training_name', 'facilitator_training_id','facilitator_training', 'facilitator_name');

		$keys = array('activity_description', 'activity_date', 'training_name', 'facilitator_training_id','facilitator_training', 'facilitator_name');
		$q  = new DBQuery;
		$q->addTable('activity_facilitator', 'af');
		$q->innerJoin('activity', 'a', 'af.facilitator_activity_id = a.activity_id');
		$q->innerJoin('trainings', 't', 'af.facilitator_training_id = t.training_id');
		$q->addQuery('a.activity_description, a.activity_date, t.training_name, af.*');
		$facilitator_records = $q->loadList();
        writeWorksheet($worksheet, $format_bold, $headers, $facilitator_records, $keys);		
		
		
		// Creating a worksheet for Activity Caregivers
		$worksheet =& $workbook->addWorksheet("Activity_Caregivers");
		$format_bold =& $workbook->addFormat();
		$format_bold->setBold();
		
		$headers = array('activity_description', 'activity_date', 'caregiver_fname', 'caregiver_lname','client_adm_no','client_first_name', 'client_other_name', 'client_last_name','activity_caregivers_activity_id', 'activity_caregivers_caregiver_id');

		$keys = array('activity_description', 'activity_date', 'caregiver_fname', 'caregiver_lname','client_adm_no','client_first_name', 'client_other_name', 'client_last_name','activity_caregivers_activity_id', 'activity_caregivers_caregiver_id');
		$q  = new DBQuery;
		$q->addTable('activity_caregivers', 'ac');
		$q->innerJoin('activity', 'a', 'ac.activity_caregivers_activity_id = a.activity_id');
		$q->innerJoin('caregiver_client', 'cc', 'cc.caregiver_id = ac.activity_caregivers_caregiver_id');
		$q->innerJoin('clients', 'c', 'c.client_id = cc.caregiver_client_id');
		$q->addQuery('a.activity_description, a.activity_date, cc.caregiver_fname, cc.caregiver_lname, c.client_adm_no, c.client_first_name, c.client_other_name,c.client_last_name, ac.*');
		$caregiver_records = $q->loadList();
        writeWorksheet($worksheet, $format_bold, $headers, $caregiver_records, $keys);
		
		
		// Creating a worksheet for Activity Clients
		$worksheet =& $workbook->addWorksheet("Activity_Clients");
		$format_bold =& $workbook->addFormat();
		$format_bold->setBold();
		
		$headers = array('activity_description', 'activity_date', 'client_adm_no','client_first_name', 'client_other_name', 'client_last_name','activity_clients_activity_id', 'activity_clients_client_id');

		$keys = array('activity_description', 'activity_date', 'client_adm_no','client_first_name', 'client_other_name', 'client_last_name','activity_clients_activity_id', 'activity_clients_client_id');
		$q  = new DBQuery;
		$q->addTable('activity_clients', 'ac');
		$q->innerJoin('activity', 'a', 'ac.activity_clients_activity_id = a.activity_id');
		$q->innerJoin('clients', 'c', 'c.client_id = ac.activity_clients_client_id');
		$q->addQuery('a.activity_description, a.activity_date,  c.client_adm_no, c.client_first_name, c.client_other_name,c.client_last_name, ac.*');
		$client_records = $q->loadList();
        writeWorksheet($worksheet, $format_bold, $headers, $client_records, $keys);
		
		
		// Creating a worksheet for Activity Staff
		$worksheet =& $workbook->addWorksheet("Activity_Staff");
		$format_bold =& $workbook->addFormat();
		$format_bold->setBold();
		
		$headers = array('activity_description', 'activity_date', 'contact_first_name','contact_other_name', 'contact_last_name', 'activity_contacts_activity_id', 'activity_contacts_contact_id');

		$keys = array('activity_description', 'activity_date', 'contact_first_name','contact_other_name', 'contact_last_name', 'activity_contacts_activity_id', 'activity_contacts_contact_id');
		$q  = new DBQuery;
		$q->addTable('activity_contacts', 'ac');
		$q->innerJoin('activity', 'a', 'ac.activity_contacts_activity_id = a.activity_id');
		$q->innerJoin('contacts', 'c', 'c.contact_id = ac.activity_contacts_contact_id');
		$q->addQuery('a.activity_description, a.activity_date,  c.contact_first_name, c.contact_other_name, c.contact_last_name, ac.*');
		$staff_records = $q->loadList();
        writeWorksheet($worksheet, $format_bold, $headers, $staff_records, $keys);

		
		// Let's send the file
		$workbook->close();		
} 
else 
{
	$AppUI->setMsg( "clientIdError", UI_MSG_ERROR );
	$AppUI->redirect();
}
?>
