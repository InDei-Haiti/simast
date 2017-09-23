<?php 

$partShow=true;
$selects = array(
"chw_center_id" => 'select clinic_id as id,clinic_name as name from clinics order by name asc',
'chw_adm_no'=>'select concat(client_adm_no," ",client_first_name," ",client_last_name) as name, client_adm_no as id from clients',
// "followup_officer_id" => 'select contact_id as id, CONCAT_WS(", ",contact_last_name,contact_first_name) as name from contacts order by name asc'
'chw_name'		=> 'select contact_id as id, CONCAT_WS(" ",contact_first_name,contact_last_name) as name from contacts con 
						left join users as u on u.user_contact = con.contact_id 
						where contact_active="1" and contact_type="10" order contact_last_name'
);

$fields=array(
'chw_name' => array('title'=>'CHW Name','value'=>'sql','query'=>'select concat(contact_first_name," ",contact_last_name) from contacts where contact_id="%d" limit 1'),
'chw_village' => 'Village',
'chw_center_id' => array('title'=>'Center','value'=>'sql','query'=>'select clinic_name from clinics where clinic_id="%d" limit 1'),
'chw_location' => array('title'=>'Location','value'=>'sql','query'=>'select clinic_location from clinic_location where clinic_location_id="%d" limit 1'),
'chw_entry_date' => array('title'=>'1.Date','xtype'=>'date'),
'chw_adm_no'=>array('title'=>'2,3.Client ADM #','value'=>'sql','query'=>'select concat(client_adm_no," ",client_first_name," ",client_last_name) from clients where client_adm_no="%s"'),
'chw_old' => array('title'=>'4.Old/New','value'=>'sysval','query'=>'OldNew'),
'chw_sex' => array('title'=>'5.Sex','value'=>'sysval','query'=>'GenderType'),
'chw_age' => '6.Age',
'chw_hasplan' => array('title'=>'7.Has careplan','value'=>'sysval','query'=>'YesNo'),
'chw_arv' => array('title'=>'8.ARVx','value'=>'sysval','query'=>'YesNo'),
'chw_oir' => array('title'=>'9.OI Rx','value'=>'sysval','query'=>'YesNo'),
'chw_tb' => array('title'=>'9.TB Rx','value'=>'sysval','query'=>'YesNo'),
'chw_nutrition' => '11.Nutrition',  
'chw_adh_support' => array('title'=>'12.Adherence support','value'=>'sysval','query'=>'AdherenceSupport','mode'=>'multi'),
'chw_assess' => array('title'=>'13.Need assessed','value'=>'sysval','query'=>'ServiceTypes','mode'=>'multi'),
'chw_support' => array('title'=>'14.Need supported','value'=>'sysval','query'=>'ServiceTypes','mode'=>'multi'),
'chw_comms'=> array('title'=>'15.Community mobilisation','value'=>'plural',
	'query'=>array(
		'set'=>'select SUBSTRING_INDEX(chw_comm_mob,",",1) as ma, 
					SUBSTRING_INDEX(chw_comm_mob,",",-1) as fy,
					SUBSTRING_INDEX(SUBSTRING_INDEX(chw_comm_mob,",",-2),",",1) as fa,
					SUBSTRING_INDEX(SUBSTRING_INDEX(chw_comm_mob,",",-3),",",1) as my
					from chw_info where chw_id="%d" AND chw_comm_mob is not NULL ',
		'fields'=>array(
			'ma'=>'Male Adult',
			'my'=>'Male Youth',
			'fa'=>'Female Adult',
			'fy'=>'Female Youth'			
		)
	)
),
'chw_refers' =>  array('title'=>'16.Refferal To','value'=>'sysval','query'=>'ClinicalReference','mode'=>'multi'),
'chw_remarks' => '17.Remarks',


);
?>
