<?php 

$partShow=true;
$selects = array(
"followup_center_id" => 'select clinic_id as id,clinic_name as name from clinics order by name asc',
"followup_officer_id" => 'select contact_id as id, CONCAT_WS(" ",contact_first_name,contact_last_name) as name from contacts  where contact_id<>"13" and contact_active="1"  order by name asc'
);

$fields=array(
'followup_client_type' => array('title'=>'Client Type','value'=>'sysval','query'=>'FollowClientType'),
'followup_visit_type' => array('title'=>"Visit Type", 'value'=>'sysval','query'=>'FollowVisitType'),
'followup_issues' => array('title'=>"Issues", 'value'=>'sysval','query'=>'FollowIssues','mode'=>'multi'),
'followup_issues_notes' => 'Issues - Other',
'followup_service' => array('title'=>"Services", 'value'=>'sysval','query'=>'FollowServices','mode'=>'multi'),
'followup_service_notes' => 'Service - Other',
'followup_officer_id' => array('title'=>"Counsellor",'value'=>'preSQL','query'=>'staffName','rquery'=>'staffId'),
'followup_object' => 'Client or Patient Name'
/*,
'followup_visit_mode' =>array('title'=>"Category", 'value'=>'sysval','query'=>'VisitMode')*/
);
?>
