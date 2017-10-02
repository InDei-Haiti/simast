<?php
$partShow=false;

$selects=array(
 "client_center" => 'select clinic_id as id,clinic_name as name from clinics order by clinic_name asc'
);

$fields = array(
		'client_name' => 'Name',
		'client_gender'=>array('title'=>"Gender",'value'=>'sysval','query'=>'GenderType'),
		'client_birthday' => 'Day of Birth',
		'client_education_level' => 'Education Level',
		'client_health_status' => 'Health Status',
		'client_cin' => 'CIN',
		'client_other_id' => 'Other Identification',
		'client_occupation' => 'Occupation',
		'client_place_of_birth' => 'Place Of Birth',
		'client_marital_status' => 'Marital Status',
		'client_profession' => 'Profession',
);
?>