<?php 

$partShow=true;
$selects = array(
	"cbc_center_id" => 'select clinic_id as id,clinic_name as name from clinics order by name asc',
	'cbc_location' => 'select clinic_location as name,clinic_location_id as id from clinic_location',
	'cbc_name'		=> 'select contact_id as id, CONCAT_WS(" ",contact_first_name,contact_last_name) as name from contacts con 
						left join users as u on u.user_contact = con.contact_id 
						where contact_active="1" and contact_type="10" order contact_last_name'
	
	
);

$fields=array(
'cbc_name' => array('title'=>'1.CBC Name','value'=>'sql','query'=>'select concat(contact_first_name," ",contact_last_name) from contacts where contact_id="%d" limit 1'),
'cbc_village' => '2.Village/Estate',
'cbc_location' => array('title'=>'4.Location','value'=>'sql','query'=>'select clinic_location from clinic_location where clinic_location_id="%d" limit 1'),
// 'cbc_old' => array('title'=>'4.Old/New','value'=>'sysval','query'=>'OldNew'),
'cbc_sex' => array('title'=>'5.Sex','value'=>'sysval','query'=>'GenderType'),
'cbc_age' => '6.Age',
'cbc_hbcare' => array('title'=>'7.Home Based Care','value'=>'sysval','query'=>'CBCHomeCare','mode'=>'multi'),
'cbc_adh_support' => array('title'=>'8.Adherence Support','value'=>'sysval','query'=>'YesNo'),
'cbc_remarks' => '9.Remarks',
'cbc_refers' =>  array('title'=>'10.Reffered','value'=>'sysval','query'=>'ClinicalReference','mode'=>'multi'),
'cbc_refers_note' =>  '10a.Reffered = Clinician',
);
?>
