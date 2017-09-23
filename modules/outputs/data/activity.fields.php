<?php

$partShow=true;
$selects=array(
 "activity_clinic" => 'select clinic_id as id,clinic_name as name from clinics order by clinic_name asc',
 'activity_training_type' => 'select training_id as id,training_name as name from trainings order by name asc',
 'activity_contacts' => 'select contact_id as id, CONCAT_WS(" ",contact_first_name,contact_last_name) as name from contacts  where contact_id<>"13" and contact_active="1" order by name asc',
 'activity_clients' => 'select concat(client_first_name," ",client_last_name) as name, client_id as id from clients order by name asc'
 
// 'activity_caregiver' => 'select caregiver_id as id, concat(caregiver_fname," ",caregiver_lname) as name from caregiver_client order by name asc'
);

$fields=array(
    'activity_clinic'=>array('title'=>'1a.Center','value'=>'sql','query'=>'select clinic_name from clinics where clinic_id="%d"'),
  'activity_date'=> array('title'=>'1b.Start date','xtype'=>'date'),
  'activity_end_date'=> array('title'=>'1c.End date','xtype'=>'date'),
  'activity_hpd'=> '1d.Hours per day',
  'activity_description' => '2a.Activity name',
  // 'activity_curriculum'=> array('title'=>'Activity curriculum','value'=>'sysval','query'=>'CurriculumTypes'),
  // 'activity_curriculum_desc' => 'Activity curriculum description',
  
  'activity_male_count'=> '3a.No. of persons (Male)',
  'activity_female_count'=> '3b.No. of persons (Female)',
  'activity_visiters_total' => '3c.Total',
  'activity_cadres' =>   array('title'=>'4.Cadres trained','value'=>'sysval','query'=>'CadresTrained','mode'=>'multi'),
  'activity_curriculum' => array('title'=>'Training curriculum','value'=>'plural','read-only'=>true,
		'query'=>array(
  				'set'=>'select training_id,training_name, training_curriculum, training_curriculum_desc from activity_facilitator af left join trainings on training_id = facilitator_training_id
						where facilitator_activity_id="%d" ',
  				'fields'=>array(
					'training_id'=>array('form'=>'hidden'),
					'training_name'=>'Training Name',
  					'training_curriculum'=>array('title'=>'Curriculum','value'=>'sysval','query'=>'CurriculumTypes'),  					
  					'training_curriculum_desc'=>'Curriculum (other)'
					
  				)  				
  			)
		),
  'activity_entry_date'=>array('title'=>'Entry date','xtype'=>'date'),

  
//  'activity_clinic'=>  array('title'=>"Center",'value'=>'preSQL','query'=>'clinicName','rquery'=>'clinicId'),

  'activity_notes'=> '11.Notes',
  'activity_custom' => 'Custom',
  'activity_contacts'=>array('title'=>'Staff','value'=>'sql-one',
  		'query'=>'select activity_contacts_contact_id from activity_contacts where activity_contacts_activity_id = "%d"','mode'=>'multi','delay'=>true),
  'activity_facilitator' => array('title'=>'Facilitator','value'=>'plural',
  			'query'=>array(
  				'set'=>'select * from activity_facilitator where facilitator_activity_id="%d"',
  				'fields'=>array(
					'facilitator_id'=>array('form'=>'hidden'),
  					'facilitator_training_id'=>array('title'=>'Training','value'=>'sql','query'=>'select training_name as name, training_id as id from trainings '),
  					'facilitator_name'=>'Facilitator',
  					'facilitator_topic'=>'Topics'
  				)  				
  			)
	),
  // 'activity_training_type'=>array('title'=>'Training type','value'=>'sql','query'=>'select t.training_name from activity_facilitator af left join trainings t on af.facilitator_training_id = t.training_id where af.facilitator_activity_id="%d"','delay'=>true),
  'activity_caregiver'=>array('title'=>'Caregiver','value'=>'sql',
  	'query'=>
		'select group_concat(IFNULL(concat(adc.fname," ", adc.lname),SUBSTRING_INDEX(ac.activity_caregivers_other,"#@#",-1)) ) as aname 	from activity_caregivers ac
		left join admission_caregivers adc on ac.activity_caregivers_caregiver_id = adc.id
		where ac.activity_caregivers_activity_id = "%d"','delay'=>true,'read-only'=>true),
  'activity_clients'=>array('title'=>'Clients','value'=>'sql-one',
		'query'=>'select activity_clients_client_id from activity_clients where activity_clients_activity_id="%d"','delay'=>true,'mode'=>'multi')
  ); 
?>
