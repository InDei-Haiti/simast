<?php
$partShow=true;

$selects = array(
'dis_center' => 'select clinic_id as id,clinic_name as name from clinics order by name asc',
'dis_client_health_staff' => 'select contact_id as id, CONCAT_WS(" ",contact_first_name,contact_last_name) as name from contacts where contact_id<>"13" and contact_active="1" order by name asc',
'dis_client_psy_staff' => 'select contact_id as id, CONCAT_WS(" ",contact_first_name,contact_last_name) as name from contacts where contact_id<>"13" and contact_active="1" order by name asc',
'dis_client_social_staff' => 'select contact_id as id, CONCAT_WS(" ",contact_first_name,contact_last_name) as name from contacts where contact_id<>"13" and contact_active="1" order by name asc'
);

$fields=array(
    'dis_age_years' => '4b.Age (years)',
    'dis_age_months'=> '4c.Age (months)',
    'dis_age_exact'=>array('title'=>'4d.Age exactness','value'=>'sysval','query'=>'AgeType'),
    'dis_time_in'=> '5b.Time in programme (mon)',
    'dis_client_status'=> array('title'=>'6.Client status','value'=>'sysval','query'=>'ClientDischargeStatus'),
    'dis_status_delta_date' => array('title'=>'7a.Date status changed','xtype'=>'date'),
    'dis_status_mdt_date' => array('title'=>'7b.Date MDT recommend discharge','xtype'=>'date'),
    'dis_status_next_date' => array('title'=>'7c.Date of next appointment (T.C.A.) ','xtype'=>'date'),
    'dis_phys_address'=>'8a.Physical address',
    'dis_landmarks'=>'8b.Landmarks',
    'dis_contact'=>'9.Contact',
    'dis_caregiver' => array('title'=>"10a.Caregiver",'value'=>'sql',
        'query'=>'select concat_ws(" ",fname,lname) from discharge_info di left join admission_caregivers ac on di.dis_caregiver = ac.id where dis_id="%d"',
            "read-only"=>true,'delay'=> true),
    'dis_caregiver_relship'=> array('title'=>'10b.Caregiver relationship','read-only'=>true),
    'dis_client_health'=>'11.Health Status',
	'dis_client_health_staff'=>array('title'=>'12a.Health note Officer','value'=>'preSQL','query'=>'staffName','rquery'=>'staffId'),
	'dis_client_health_date'=>array('title'=>'12b.Health note Date','xtype'=>'date'),
	'dis_client_psy'=>'13.Psychological Status',
	'dis_client_psy_staff'=>array('title'=>'14a.Psychological note Officer','value'=>'preSQL','query'=>'staffName','rquery'=>'staffId'),
	'dis_client_psy_date'=>array('title'=>'14b.Psychological note Date','xtype'=>'date'),
	'dis_client_social'=>'15.Social Status',
	'dis_client_social_staff'=>array('title'=>'14a.Social note Officer','value'=>'preSQL','query'=>'staffName','rquery'=>'staffId'),
	'dis_client_social_date'=>array('title'=>'14b.Social note Date','xtype'=>'date')  
);
?>
