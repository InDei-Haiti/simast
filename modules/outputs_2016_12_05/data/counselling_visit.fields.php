<?php
$partShow=true;

$selects = array(
"counselling_center_id" => 'select clinic_id as id,clinic_name as name from clinics order by name asc',
"counselling_staff_id" => 'select contact_id as id, CONCAT_WS(" ",contact_first_name,contact_last_name) as name from contacts  where contact_id<>"13" and contact_active="1" order by name asc'
);

$fields = array(
// "counselling_entry_date" => "Visit Date", 
// "counselling_center_id" => array('title'=>"Center",'value'=>'sql','query'=>'select clinic_name from clinics where clinic_id="%d" limit 1','rquery'=>'select clinic_id from clinics where clinic_name="%s" limit 1'),
// "counselling_staff_id" => array('title'=>"Counsellor",'value'=>'sql','query'=>'select CONCAT_WS(", ",contact_last_name,contact_first_name) from contacts where contact_id="%d" limit 1','rquery'=>'select contact_id from contacts where lower(CONCAT_WS(", ",contact_last_name,contact_first_name))="%s" limit 1'),
/* "counselling_caregiver_fname" => "Caregiver First Name", 
"counselling_caregiver_lname" => "Caregiver Last Name", 
"counselling_caregiver_age" => "Caregiver Age", 
"counselling_caregiver_relationship" => "Caregiver Reln.", 
"counselling_caregiver_marital_status" => array('title'=>"Caregiver Marital Status",'value'=>'sysval','query'=>'MaritalStatus'),
"counselling_caregiver_educ_level" => array('title'=>"Educ. Level", 'value'=>'sysval','query'=>'EducationLevel'),
"counselling_caregiver_employment" => array('title'=>"Employment",'value'=>'sysval','query'=>'EmploymentType'),
"counselling_caregiver_income_level" => array('title'=>"Income Level", 'value'=>'sysval','query'=>'IncomeLevels'),
"counselling_caregiver_idno" => "ID No.", 
"counselling_caregiver_mobile" => "Mobile", 
"counselling_caregiver_residence" => "Residence", */

"counselling_visit_type" => array('title'=>"3.Visit Type",'value'=>'sysval','query'=>'VisitType'),
"counselling_child_issues" => array('title'=>"4.Child Issues", 'value'=>'sysval','query'=>'ChildHealthIssues','mode'=>'multi'),
"counselling_other_issues" => "4j.Other issues", 
"counselling_caregiver_issues" => array('title'=>"5.Mother/father's personal health history includes", 'value'=>'sysval','query'=>'CaregiverHealthIssues','mode'=>'multi'),
"counselling_caregiver_other_issues" => "5f.Other caregiver issues", 
"counselling_caregiver_issues2" => array('title'=>"6.Other primary c/givers history includes",  'value'=>'sysval','query'=>'CaregiverHealthIssues','mode'=>'multi'),
"counselling_caregiver_other_issues2" => "6f.Other issues", 
"counselling_child_knows_status" => array('title'=>"7.Does child know HIV status (new)", 'value'=>'sysval','query'=>'ChildHivAware'),
"counselling_child_knows_status_old" => array('title'=>"7.Does child know his HIV status (obs)", 'value'=>'sysval','query'=>'DisclosureStatus'),
"counselling_otheradult_knows_status" => array('title'=>"8.Do other adults know childs HIV's status", 'value'=>'sysval','query'=>'HivAdultChildOptions'),
"counselling_disclosure_response" => array('title'=>"9.Response to new disclosure", 'value'=>'sysval','query'=>'DisclosureResponse'),
"counselling_disclosure_state" => array('title'=>"10.State of disclosure process", 'value'=>'sysval','query'=>'DisclosureProcessStatus'),
"counselling_secondary_caregiver_knows" =>  array('title'=>"11.Secondary caregiver know child's HIV status", 'value'=>'sysval','query'=>'HivCaregiverChildOptions'),
"counselling_primary_caregiver_tested" => array('title'=>"12.Child's primary caregiver tested for HIV", 'value'=>'sysval','query'=>'HIVCaregiverOptions'),
"counselling_father_status" => array('title'=>"13a.Father's HIV Status", 'value'=>'sysval','query'=>'HIVStatusTypes','transit'=>true),//HIVPrimaryCaregiverOptions
"counselling_mother_status" => array('title'=>"13b.Mother HIV Status", 'value'=>'sysval','query'=>'HIVStatusTypes','transit'=>true),
"counselling_caregiver_status" => array('title'=>"13c.Caregiver HIV Status", 'value'=>'sysval','query'=>'HIVStatusTypes','transit'=>true),
"counselling_father_treatment" => array('title'=>"14a.Father receiving medical treatment", 'value'=>'sysval','query'=>'HIVTreatmentOptions'),
"counselling_mother_treatment" => array('title'=>"14b.Mother receiving medical treatment", 'value'=>'sysval','query'=>'HIVTreatmentOptions'),
"counselling_caregiver_treatment" =>  array('title'=>"14c.Caregiver receiving medical treatment", 'value'=>'sysval','query'=>'HIVTreatmentOptions'),
"counselling_stigmatization_concern" => array('title'=>"15.Degree to which HIV related stigmatization", 'value'=>'sysval','query'=>'StigmatizationOptions'),
'counselling_second_ident' => array('title'=>"16.Has secondary Caregiver been identified", 'value'=>'sysval','query'=>'SecondIdentified'), 
"counselling_counselling_services" => array('title'=>"17.Services Offered", 'value'=>'sysval','query'=>'ServiceOptions','mode'=>'multi'),
"counselling_other_services" => "17f.Other services", 
/* 'counselling_referer' => array('title'=>"18.Refer to", 'value'=>'sysval','query'=>'CounsellingReferer'),
'counselling_next_visit' => array('title'=>'19.Next appointment','xtype'=>'date'), */
"counselling_notes" => "20.Counsellor's overall assessments"
);
?>
