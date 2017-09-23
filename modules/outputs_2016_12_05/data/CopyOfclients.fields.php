<?php
$partShow=false;

$selects=array(
 "client_center" => 'select clinic_id as id,clinic_name as name from clinics order by clinic_name asc'
);

$fields = array(
'client_adm_no'=>array('title'=>'Client Adm #','xtype'=>'string'),
'client_dob'=>'DOB',
'client_doa'=>'DOA',
'client_name'=>'Name',
'client_gender'=>array('title'=>"Gender",'value'=>'sysval','query'=>'GenderType'),
'client_first_name'=>'First Name',
'client_other_name'=>'Middle Name',
'client_last_name'=>'Last name',
'client_status' => array('title'=>'Client Status','value'=>'sysval','query'=>'ClientStatus'),
'client_entry_date'=>'Date',
'client_center'=>array('title'=>"Main Clinic",'value'=>'preSQL','query'=>'clinicName','rquery'=>'clinicId'),
'client_nhif'=>"NHF #",
// 'client_nhif_y'=>'NHF-Y,#,',
'client_nhif_n'=>'NHF-N,?,',
'client_immun'=>"Immun Card #",
// 'client_immun_y'=>'IC-Y,#,',
'client_immun_n'=>'IC-N,?,',
'client_lvd_form'=>array('title'=>'LVD Form','value'=>'sysval','query'=>'FormNames','read-only'=>true)

);
?>