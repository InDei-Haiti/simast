<?php
$partShow=true;

$selects = array(
'mortality_clinic_id' => 'select clinic_id as id,clinic_name as name from clinics order by name asc',
'mortality_clinical_officer' => 'select contact_id as id, CONCAT_WS(" ",contact_first_name,contact_last_name) as name from contacts where contact_id<>"13" and contact_active="1" order by name asc'
);

$fields=array(
//  'mortality_entry_date'=>'Date',
//  'mortality_clinic_id'=> array('title'=>"Center",'value'=>'sql','query'=>'select clinic_name from clinics where clinic_id="%d" limit 1','rquery'=>'select clinic_id from clinics where clinic_name="%s" limit 1'),
  'mortality_social_worker' => '1c.Social Worker',
  'mortality_age_yrs' => '4b.Age (years)',
  'mortality_age_months'=> '4b.Age (months)',
  'mortality_age_status' => array('title'=>'4c.Age (status)','value'=>'sysval','query'=>'AgeType'),
  'mortality_enroll_date' => array('title'=>'Date of admission','xtype'=>'date'),
  'mortality_enrolled_time' => '5b.Enrolled time',
  'mortality_date'=> array('title'=>'6a.Date of death' ,'xtype'=>'date'),
  'mortality_death_type'=> array('title'=>'6b.Place of death','value'=>'sysval','query'=>'DeathPlaceTypes'),
  'mortality_death_type_notes'=> '6c.Other',
  'mortality_informant' => '7.Informant(relationship)',
  'mortality_hospital' => '8a.Name of hospital attended',
  'mortality_hospital_adm_date' => array('title'=>'9.Date of admission (to hospital)','xtype'=>'date'),
  'mortality_relative_report_date' => array('title'=>'Date of report from relative','xtype'=>'date'),
  'mortality_symptoms' => '10a.Symptoms',
  'mortality_time_course' => '10b.Time course',
  'mortality_treatment' => '10c.Treatment',  
  'mortality_hospital_referral'=> array('title'=>'11a.Was the child refered to hospital by LT clinic','value'=>'sysval','query'=>'YesNo'),
  'mortality_referral' => '11b.Why child was referred',
  'mortality_hospital_adm_notes'=> '12.Reason for admission',
  'mortality_clinical_course' => '13.Clinical Course',
  'mortality_cause_given' =>array('title'=>'14.Cause of death given','value'=>'sysval','query'=>'YesNo'),
  'mortality_cause_desc' => '14b.Given cause of death',
  'mortality_clinical_officer' => array('title'=>'15.Clinical officer','value'=>'preSQL','query'=>'staffName','rquery'=>'staffId'),//,'transit'=>true
  'mortality_clinical_officer_old' => '15.Clinical Officer (obs)',
  'mortality_clinical_officer_date' => array('title'=>'15b.Date of report','xtype'=>'date'),
  'mortality_cd4' => '16a.CD4',
  'mortality_cd4_percentage' => '16b.CD4%',
  'mortality_viral_load' => '16c.Viral load',
  'mortality_hb' => '16d.Hb',
  'mortality_clinical_date' => array('title'=>'16e.Clinical Date','xtype'=>'date'),
  'mortality_arv' =>  array('title'=>"17a.ARV's",'value'=>'sysval','query'=>'YesNo'),
  'mortality_arv_dateon' => array('title'=>'17b.ARV start date','xtype'=>'date'),  
  'mortality_arv_period' => '17c.Time on ARV(mo)',
  'mortality_tb' =>  array('title'=>"18a.TB treatment",'value'=>'sysval','query'=>'YesNo'),
  'mortality_tb_start' => array('title'=>'18b.Tb treatment start date','xtype'=>'date'),
  'mortality_weight' => '19a.Last Weight',
  'mortality_height' => '19b.Last Height',
  'mortality_nutrition_date' => array('title'=>'19c.Nutrition visit date','xtype'=>'date'),  
  'mortality_malnutrition' => array('title'=>"20a.Malnutrition",'value'=>'sysval','query'=>'YesNo'),
  'mortality_malnutrition_notes' => array('title'=>"20b.Malnutrition grade",'value'=>'sysval','query'=>'Grades'),
  'mortality_recents_a' => '21a.Other recent problems ',
  'mortality_recents_b' => '21b.Other recent problems ',  
  'mortality_postmortem' => array('title'=>'22a.Is the postmortem arranged','value'=>'sysval','query'=>'YesNo'),
  'mortality_postmortem_where' => '22b.Postmortem, where',  
  'mortality_cause_pm' => '22c.Cause of death from PM',
  'mortality_likely_cause' => '23.Cause of death w/o PM',
  'mortality_notes' => '24.Other factors'/*,
  'mortality_custom' => 'Custom',*/
);
?>
