<?php
$partShow=true;
$selects = array(
"admission_staff_id" => 'select contact_id as id,CONCAT_WS(" ",contact_first_name,contact_last_name) as name from contacts contact_id <> "13" and contact_active="1"  order by name asc',
'admission_chw' => 'select contact_id as id, CONCAT_WS(" ",contact_last_name,contact_first_name) as name from contacts where contact_type="10" and contact_active="1" order by name asc ',
'admission_location' => 'select clinic_location_id as id, clinic_location as name from clinic_location '
);

$candid = array(
   "admission_entry_date" => "Entry Date", 
   "admission_clinic_id" =>  array('title'=>"Center",'value'=>'sql','query'=>'select clinic_name from clinics where clinic_id="%d" limit 1','rquery'=> 'select clinic_id from clinics where clinic_name="%s" limit 1'),
   "admission_staff_id" => array('title'=>"Officer",'value'=>'sql','query'=>'select CONCAT_WS(" ",contact_first_name,contact_last_name) from contacts where contact_id="%d" limit 1','rquery'=>'select contact_id from contacts where LOWER(CONCAT_WS(" ",contact_first_name,contact_last_name)) ="%s" limit 1'),
);

$fields = array(
// "admission_gender" => array('title'=>"Gender",'value'=>'sysval','query'=>'GenderType'),
"admission_school_level" => array('title'=>"3a.School Level",'value'=>'sysval','query'=>'ChildEducationLevel'),
"admission_reason_not_attending" => array('title'=>"3b.If not attending,why",'value'=>'sysval','query'=> 'AdmissionReasonsNotAttendingSchool'),
"admission_reason_not_attending_notes" => "3c.Other reason", 
"admission_residence" => "4.Current Residence", 
"admission_location" => array('title'=>"Location",'value'=>'sql','query'=>'select clinic_location from clinic_location where clinic_location_id="%d"','skip'=>true), 
"admission_chw"	=>array('title'=>"5b.CHW",'value'=>'sql','query'=>'select CONCAT_WS(", ",contact_last_name,contact_first_name) as name from contacts where contact_id="%d" and contact_type="10"'), 
"admission_province" => "6a.Province", 
"admission_district" => "6b.District", 
"admission_village" => "6c.Village",
"admission_father_fname" => array('title'=>"7a.First Name (Father)",'value'=>'presql-db','query'=>'careStuff','field'=>'fname','bfield'=>'admission_father'), 
"admission_father_lname" => array('title'=>"7b.Last Name(F)",'value'=>'presql-db','query'=>'careStuff','field'=>'lname','bfield'=>'admission_father'), 
"admission_father_age" => array('title' => "7c.Age(F)",'value'=>'presql-db','query'=>'careStuff','field'=>'age','bfield'=>'admission_father'), 
"admission_father_status" => array('title'=>"8a.Status(F)",'value'=>'presql-db','bfield'=>'admission_father','field'=>'status',
			'query'=>array(
					'sysval'	=>'CaregiverStatus',
					'func'		=>'careStuff'
			)),
"admission_father_health_status" => array('title'=>"8b.Health Status(F)",'value'=>'presql-db','field'=>'health_status','bfield'=>'admission_father' ,
			'query'=>array(
					'sysval'	=>'CaregiverHealthStatus',
					'func'		=>'careStuff'
			)),
"admission_father_raising_child" => array('title'=>"8c.Raising Child(F)",'value'=>'sysval','query'=>'YesNo'/* ,'bfield'=>'admission_father' */),
"admission_father_marital_status" =>  array('title'=>"8d.Marital status(F)",'value'=>'presql-db','field'=>'marital_status','bfield'=>'admission_father', 
			'query'=>array(
					'sysval'	=>'MaritalStatus',
					'func'		=>'careStuff'
			)),

"admission_father_educ_level" => array('title'=>"9.Education Level(F)",'value'=>'presql-db','field'=>'educ_level','bfield'=>'admission_father'
			,'query'=>array(
					'sysval'	=>'EducationLevel',
					'func'		=>'careStuff'
			)),
"admission_father_employment" => array('title'=>"10.Employment(F)",'value'=>'presql-db','field'=>'employment','bfield'=>'admission_father', 'query'=>array(
					'sysval'	=>'EmploymentType',
					'func'		=>'careStuff'	
	)),
'admission_father_idno'	=>array('title' => "11a.ID #(F)",'value'=>'presql-db','query'=>'careStuff','field'=>'idno','bfield'=>'admission_father'),
'admission_father_mobile'	=>array('title' => "11b.Mobile #(F)",'value'=>'presql-db','query'=>'careStuff','field'=>'mobile','bfield'=>'admission_father'), 
"admission_mother_fname" => array('title'=>"12a.First Name (mother)",'value'=>'presql-db','query'=>'careStuff','field'=>'fname','bfield'=>'admission_mother'), 
"admission_mother_lname" => array('title'=>"12b.Last Name(M)",'value'=>'presql-db','query'=>'careStuff','field'=>'lname','bfield'=>'admission_mother'), 
"admission_mother_age" => array('title' => "12c.Age(M)",'value'=>'presql-db','query'=>'careStuff','field'=>'age','bfield'=>'admission_mother'), 
"admission_mother_status" => array('title'=>"13a.Status(M)",'value'=>'presql-db' ,'bfield'=>'admission_mother','field'=>'status',
			'query'=>array(
					'sysval'	=>'CaregiverStatus',
					'func'		=>'careStuff'
			)),
"admission_mother_health_status" => array('title'=>"13b.Health Status(M)",'value'=>'presql-db','field'=>'health_status' ,'bfield'=>'admission_mother'
			,'query'=>array(
					'sysval'	=>'CaregiverHealthStatus',
					'func'		=>'careStuff'
			)),
"admission_mother_raising_child" => array('title'=>"13c.Raising Child(M)",'value'=>'sysval','query'=>'YesNo' /*,'bfield'=>'admission_mother'*/ ),
"admission_mother_marital_status" =>  array('title'=>"13d.Marital status(M)",'value'=>'presql-db','field'=>'marital_status','bfield'=>'admission_mother',
			'query'=>array(
					'sysval'	=>'MaritalStatus',
					'func'		=>'careStuff'
			)),
"admission_mother_educ_level" => array('title'=>"14.Education Level(M)",'value'=>'presql-db','field'=>'educ_level','bfield'=>'admission_mother',
			'query'=>array(
					'sysval'	=>'EducationLevel',
					'func'		=>'careStuff'
			)),
"admission_mother_employment" => array('title'=>"15.Employment(M)",'value'=>'presql-db','field'=>'employment','bfield'=>'admission_mother',
			 'query'=>array(
					'sysval'	=>'EmploymentType',
					'func'		=>'careStuff'	
			)),
'admission_mother_idno'	=>array('title' => "16a.ID #(M)",'value'=>'presql-db','query'=>'careStuff','field'=>'idno','bfield'=>'admission_mother'),
'admission_mother_mobile'	=>array('title' => "16b.Mobile #(M)",'value'=>'presql-db','query'=>'careStuff','field'=>'mobile','bfield'=>'admission_mother'), 
"admission_caregiver_pri_fname" => array('title'=>"17a.First Name (CarePri)",'value'=>'presql-db','query'=>'careStuff','field'=>'fname','bfield'=>'admission_caregiver_pri'), 
"admission_caregiver_pri_lname" => array('title'=>"17b.Last Name(CP)",'value'=>'presql-db','query'=>'careStuff','field'=>'lname','bfield'=>'admission_caregiver_pri'), 
"admission_caregiver_pri_age" => array('title' => "17c.Age(CP)",'value'=>'presql-db','query'=>'careStuff','field'=>'age','bfield'=>'admission_caregiver_pri'), 
"admission_caregiver_pri_status" => array('title'=>"18a.Status(CP)",'value'=>'presql-db','bfield'=>'admission_caregiver_pri','field'=>'status' ,
			'query'=>array(
					'sysval'	=>'CaregiverStatus',
					'func'		=>'careStuff'
			)),			
"admission_caregiver_pri_health_status" => array('title'=>"18b.Health Status(CP)",'value'=>'presql-db','field'=>'health_status' ,'bfield'=>'admission_caregiver_pri'
			,'query'=>array(
					'sysval'	=>'CaregiverHealthStatus',
					'func'		=>'careStuff'
			)),
/*"admission_caregiver_pri_raising_child" => array('title'=>"18c.Raising Child(CP)",'value'=>'sysval','query'=>'YesNo' /*,'bfield'=>'admission_caregiver_pri' ),*/
"admission_caregiver_pri_marital_status" =>  array('title'=>"18d.Marital status(CP)",'value'=>'presql-db','field'=>'marital_status','bfield'=>'admission_caregiver_pri',
			'query'=>array(
					'sysval'	=>'MaritalStatus',
					'func'		=>'careStuff'
			)),
"admission_caregiver_pri_educ_level" => array('title'=>"19.Education Level(CP)",'value'=>'presql-db','field'=>'educ_level','bfield'=>'admission_caregiver_pri',
			'query'=>array(
					'sysval'	=>'EducationLevel',
					'func'		=>'careStuff'
			)),
"admission_caregiver_pri_employment" => array('title'=>"20.Employment(CP)",'value'=>'presql-db','field'=>'employment','bfield'=>'admission_caregiver_pri',
			 'query'=>array(
					'sysval'	=>'EmploymentType',
					'func'		=>'careStuff'	
			)),
'admission_caregiver_pri_idno'	=>array('title' => "21a.ID #(CP)",'value'=>'presql-db','query'=>'careStuff','field'=>'idno','bfield'=>'admission_caregiver_pri'),
'admission_caregiver_pri_mobile'	=>array('title' => "21b.Mobile #(CP)",'value'=>'presql-db','query'=>'careStuff','field'=>'mobile','bfield'=>'admission_caregiver_pri'), 
'admission_caregiver_pri_residence' => '21c.Residence',
"admission_caregiver_sec_fname" => array('title'=>"22a.First Name (CareSec)",'value'=>'presql-db','query'=>'careStuff','field'=>'fname','bfield'=>'admission_caregiver_sec'), 
"admission_caregiver_sec_lname" => array('title'=>"22b.Last Name(CS)",'value'=>'presql-db','query'=>'careStuff','field'=>'lname','bfield'=>'admission_caregiver_sec'), 
"admission_caregiver_sec_age" => array('title' => "22c.Age(CS)",'value'=>'presql-db','query'=>'careStuff','field'=>'age','bfield'=>'admission_caregiver_sec'), 
"admission_caregiver_sec_status" => array('title'=>"23a.Status(CS)",'value'=>'presql-db','bfield'=>'admission_caregiver_sec','field'=>'status',
			'query'=>array(
					'sysval'	=>'CaregiverStatus',
					'func'		=>'careStuff'
			)),
"admission_caregiver_sec_health_status" => array('title'=>"23b.Health Status(CS)",'value'=>'presql-db','field'=>'health_status' ,'bfield'=>'admission_caregiver_sec'
			,'query'=>array(
					'sysval'	=>'CaregiverHealthStatus',
					'func'		=>'careStuff'
			)),
/// "admission_caregiver_sec_raising_child" => array('title'=>"23c.Raising Child(CS)",'value'=>'sysval','query'=>'YesNo' /*,'bfield'=>'admission_caregiver_sec'*/ ),
"admission_caregiver_sec_marital_status" =>  array('title'=>"23d.Marital status(CS)",'value'=>'presql-db','field'=>'marital_status','bfield'=>'admission_caregiver_sec',
			'query'=>array(
					'sysval'	=>'MaritalStatus',
					'func'		=>'careStuff'
			)),
"admission_caregiver_sec_educ_level" => array('title'=>"24.Education Level(CS)",'value'=>'presql-db','field'=>'educ_level','bfield'=>'admission_caregiver_sec',
			'query'=>array(
					'sysval'	=>'EducationLevel',
					'func'		=>'careStuff'
			)),
"admission_caregiver_sec_employment" => array('title'=>"25.Employment(CS)",'value'=>'presql-db','field'=>'employment','bfield'=>'admission_caregiver_sec',
			 'query'=>array(
					'sysval'	=>'EmploymentType',
					'func'		=>'careStuff'	
			)),
'admission_caregiver_sec_idno'	=>array('title' => "26a.ID #(CS)",'value'=>'presql-db','query'=>'careStuff','field'=>'idno','bfield'=>'admission_caregiver_sec'),
'admission_caregiver_sec_mobile'	=>array('title' => "26b.Mobile #(CS)",'value'=>'presql-db','query'=>'careStuff','field'=>'mobile','bfield'=>'admission_caregiver_sec'), 
'admission_caregiver_sec_residence' => '26c.Residence',
/* 
"admission_father_fname" => "First Name (Father)", 
"admission_father_lname" => "Last Name", 
"admission_father_age" => "Age", 
"admission_father_status" => array('title'=>"Status",'value'=>'sysval','query'=>'CaregiverStatus'),
"admission_father_health_status" => array('title'=>"Health Status",'value'=>'sysval','query'=>'CaregiverHealthStatus'),
"admission_father_raising_child" => array('title'=>"Raising Child",'value'=>'sysval','query'=>'YesNo'),
"admission_father_marital_status" =>  array('title'=>"Marital status",'value'=>'sysval','query'=>'MaritalStatus'),
"admission_father_educ_level" => array('title'=>"Education Level",'value'=>'sysval','query'=>'EducationLevel'),
"admission_father_employment" => array('title'=>"Employment",'value'=>'sysval','query'=>'EmploymentType'),
//"admission_father_income" => array('title'=>"Monthly Income",'value'=>'sysval','query'=>'IncomeLevels'),
"admission_father_idno" => "ID #", 
"admission_father_mobile" => "Mobile #", 
"admission_mother_fname" => "First Name (Mother)", 
"admission_mother_lname" => "Last Name", 
"admission_mother_age" => "Age", 
"admission_mother_status" => array('title'=>"Status",'value'=>'sysval','query'=>'CaregiverStatus'),
"admission_mother_health_status" => array('title'=>"Health Status",'value'=>'sysval','query'=>'CaregiverHealthStatus'),
"admission_mother_raising_child" => array('title'=>"Raising Child",'value'=>'sysval','query'=>'YesNo'),
"admission_mother_marital_status" =>  array('title'=>"Marital status",'value'=>'sysval','query'=>'MaritalStatus'),
"admission_mother_educ_level" => array('title'=>"Education Level",'value'=>'sysval','query'=>'EducationLevel'),
"admission_mother_employment" => array('title'=>"Employment",'value'=>'sysval','query'=>'EmploymentType'),
//"admission_mother_income" => array('title'=>"Monthly Income",'value'=>'sysval','query'=>'IncomeLevels'),
"admission_mother_idno" => "ID #", 
"admission_mother_mobile" => "Mobile #", 
"admission_caregiver_fname" => "First Name (Primary Caregiver)",
"admission_caregiver_lname" => "Last Name", 
"admission_caregiver_age" => "Age", 
"admission_caregiver_status" => array('title'=>"Status",'value'=>'sysval','query'=>'CaregiverStatus'),
"admission_caregiver_health_status" => array('title'=>"Health Status",'value'=>'sysval','query'=>'CaregiverHealthStatus'),
"admission_caregiver_relationship" => "Relationship to child", 
"admission_caregiver_marital_status" => array('title'=>"Marital status",'value'=>'sysval','query'=>'MaritalStatus'),
"admission_caregiver_educ_level" => array('title'=>"Education Level",'value'=>'sysval','query'=>'EducationLevel'),
"admission_caregiver_employment" => array('title'=>"Employment",'value'=>'sysval','query'=>'EmploymentType'),
//"admission_caregiver_income" => array('title'=>"Monthly Income",'value'=>'sysval','query'=>'IncomeLevels'),
*/

/* "admission_caregiver_idno" => "ID #", 
//'admission_enclosures'=>array('title'=>"Enclosures",'value'=>'sysval','query'=>'Enclosures','mode'=> 'multi'),
"admission_caregiver_mobile" => "Mobile #", **/
"admission_family_income" => array('title'=>"27.Total Household Income",'value'=>'sysval','query'=>'IncomeLevels'),
'admission_other_household_members' => array('title'=> 'Other Household Members','value'=>'plural',
		'query'=>array(
			'set' => 'select * from household_info where household_client_id="%d"',
			'fields'=>array(
						'household_name'		=>'Name',
						'household_yob'			=>array('title'=>'YOB','xtype'=>'date'),
						'household_gender'		=>array('title'=>'Gender','value'=>'sysval','query'=>'GenderType'),
						'household_relationship'=>'Relationship to child',
						'household_notes'		=>'ADM #',
						'household_custom'		=>'Comments'
					)
		)
		),
'admission_total_orphan' => array('title'=>"31a.Total Orphan",'value'=>'sysval','query'=>'YesNoND'),
"admission_risk_level" => array('title'=>"31b.Risk Level",'value'=> 'sysval','query' => 'RiskLevel'), 		
'admission_birth_cert' => '32a.Birth Certificate #',
'admission_id_no' => '32b.ID #',
'admission_nhf'=>'32c.NHIF #',
'admission_med_recs' => array('title'=>'32d.Med Rec','value'=>'sysval','query'=>'YesNo'),
'admission_immun'=>'32e.Immun Card #',
'admission_death_cert' => '32f.Death Cert #',
'admission_enclosures_other' => '32g.Enclosures notes',
"admission_risk_level_description" => "33.Social Worker Assessment",
);

?>
