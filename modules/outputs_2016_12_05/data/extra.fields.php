<?php
$partShow=false;

$selects=array(
 "center" => 'select clinic_id as id,clinic_name as name from clinics order by clinic_name asc',
 "staff" => 'select contact_id as id ,CONCAT_WS(" ",contact_first_name,contact_last_name) as name from contacts where contact_id <> "13" and contact_active = "1" order by name asc'
);

$fields = array(
'center'=> array('title'=>"Clinic name",'value'=>'preSQL','query'=>'clinicName','rquery'=>'clinicId'),
'staff' => array('title'=>"Staff",'value'=>'preSQL','query'=>'staffName','rquery'=>'staffId'),
'date'  => array('title'=>'Visit date','xtype'=>'date'),
'referral'=>array('title'=>'Referral To','value'=>'sysval','query'=>'ClinicalReference'),
'next_visit'=>array('title'=>'Next appointment','xtype'=>'date')
);
?>