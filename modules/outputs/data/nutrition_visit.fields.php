<?php

$partShow=true;
$selects = array(
"nutrition_center" => 'select clinic_id as id,clinic_name as name from clinics order by name asc',
"nutrition_staff_id" => 'select contact_id as id, CONCAT_WS("  ",contact_first_name,contact_last_name) as name from contacts where  where contact_id<>"13" and contact_active="1"  order by name asc'
);
 
$fields = array(
// "nutrition_entry_date" => "Visit Date" ,
// "nutrition_center" => array('title'=>"Center",'value'=>'sql','query'=>'select clinic_name from clinics where clinic_id="%d" limit 1','rquery'=>'select clinic_id from clinics where clinic_name="%s" limit 1'),
// "nutrition_staff_id" => array('title'=>"Nutritionist",'value'=>'sql','query'=>'select CONCAT_WS(", ",contact_last_name,contact_first_name) from contacts where contact_id="%d" limit 1','rquery'=>'select contact_id from contacts where lower(CONCAT_WS(", ",contact_last_name,contact_first_name))="%s" limit 1'),
"nutrition_child_attend" => array('title'=>"3b.Child attending" ,'value'=>'sysval','query'=>'YesNo'),
"nutrition_care_attend" => array('title'=>"3c.Caregiver attending" ,'value'=>'sysval','query'=>'YesNo'),
"nutrition_care_who" => "3d.Caregiver - Who",
/* "nutrition_caregiver_type" => array('title'=>"Caregiver" ,'value'=>'sysval','query'=>'CaregiverRelation'),
"nutrition_caregiver_type_notes" => "Other Caregiver" ,*/
"nutrition_weight" => "4a.Weight(kg)" ,
"nutrition_height" => "4b.Height(cm)" ,
"nutrition_zscore" => "z score" ,
"nutrition_muac" => "4c.MUAC (mm)" ,
"nutrition_wfh" => "5a.WFH" ,
"nutrition_wfa" => "5b.WFA" ,
"nutrition_bmi" => "5d.BMI" ,
"nutrition_blacktea" =>  array('title'=>"6.Black tea" ,'value'=>'sysval','query'=>'DietHistoryOptions','mode'=>'multi'),
"nutrition_whitetea" =>  array('title'=>"6.White tea" ,'value'=>'sysval','query'=>'DietHistoryOptions','mode'=>'multi'),
"nutrition_porridge" =>  array('title'=>"6.Porridge" ,'value'=>'sysval','query'=>'DietHistoryOptions','mode'=>'multi'),
"nutrition_water" =>  array('title'=>"6.Water" ,'value'=>'sysval','query'=>'DietHistoryOptions','mode'=>'multi'),
// "nutrition_bread" =>  array('title'=>"Bread/Cake" ,'value'=>'sysval','query'=>'DietHistoryOptions','mode'=>'multi'),
"nutrition_ugali" =>  array('title'=>"7.Ugali/Maize" ,'value'=>'sysval','query'=>'DietHistoryOptions','mode'=>'multi'),
"nutrition_rice" =>  array('title'=>"7.Rice" ,'value'=>'sysval','query'=>'DietHistoryOptions','mode'=>'multi'),
"nutrition_banan" =>  array('title'=>"7.Bananas" ,'value'=>'sysval','query'=>'DietHistoryOptions','mode'=>'multi'),
"nutrition_tubers" =>  array('title'=>"7.Tubers" ,'value'=>'sysval','query'=>'DietHistoryOptions','mode'=>'multi'),
"nutrition_wheat" =>  array('title'=>"7.Wheat" ,'value'=>'sysval','query'=>'DietHistoryOptions','mode'=>'multi'),
"nutrition_legumes" =>  array('title'=>"8.Legumes / Pulses / Nuts" ,'value'=>'sysval','query'=>'DietHistoryOptions','mode'=>'multi'),
"nutrition_milk" =>  array('title'=>"8.Milk/Milk products" ,'value'=>'sysval','query'=>'DietHistoryOptions','mode'=>'multi'),
"nutrition_meat" =>  array('title'=>"8.Beef/chicken/fish" ,'value'=>'sysval','query'=>'DietHistoryOptions','mode'=>'multi'),
"nutrition_eggs" =>  array('title'=>"8.Eggs" ,'value'=>'sysval','query'=>'DietHistoryOptions','mode'=>'multi'),
"nutrition_vegetables" =>  array('title'=>"9.Vegetables" ,'value'=>'sysval','query'=>'DietHistoryOptions','mode'=>'multi'),
"nutrition_fruit" =>  array('title'=>"9.Fruits/Juices" ,'value'=>'sysval','query'=>'DietHistoryOptions','mode'=>'multi'),
"nutrition_fat" =>  array('title'=>"9.Fats/Oils" ,'value'=>'sysval','query'=>'DietHistoryOptions','mode'=>'multi'),
"nutrition_breastfeeding" =>  array('title'=>"9.Breast milk" ,'value'=>'sysval','query'=>'DietHistoryOptions','mode'=>'multi'),
"nutrition_formula_milk" =>  array('title'=>"9.Formula milk" ,'value'=>'sysval','query'=>'DietHistoryOptions','mode'=>'multi'),
"nutrition_food_enrichment" => array('title'=>"10.Food enrichment" ,'value'=>'sysval','query'=>'FoodEnrichmentOptions','mode'=>'multi'),
"nutrition_food_enrichment_notes" => "10f.Other food enrichment options" ,
"nutrition_water_access" => array('title'=>"11.Daily water access" ,'value'=>'sysval','query'=>'WaterSourceOptions'),
// "nutrition_diet_history_notes" =>"Other" ,
// "nutrition_diet_history_others" => array('title'=>"Other (diet history)" ,'value'=>'sysval','query'=>'DietHistoryOptions','mode'=>'multi'),


"nutrition_water_purification" => array('title'=>"12.How to purify water" ,'value'=>'sysval','query'=>'WaterPurificationOptions'),
"nutrition_water_purification_notes" => "12d.Other purification methods" ,

"nutrition_quantity" => array('title'=>"13a.Adequate quantity" ,'value'=>'sysval','query'=>'YesNo'),
"nutrition_quality" => array('title'=>"13b.Adequate quality" ,'value'=>'sysval','query'=>'YesNo'),
"nutrition_poor_preparation" => array('title'=>"13c.Poor practises" ,'value'=>'sysval','query'=>'YesNo'),
"nutrition_mixed_feeding" => array('title'=>"13d.Mixed feeding" ,'value'=>'sysval','query'=>'YesNo'),
"nutrition_unclean_drinking_water" => array('title'=>"13e.Unclean drinking water" ,'value'=>'sysval','query'=>'YesNo'),
'nutrition_issue_notes' => '13f.Others',
/* nutrition_education" => array('title'=>"19.Nutrition education" ,'value'=>'sysval','query'=>'YesNo'),
"nutrition_counselling" => array('title'=>"19.Nutrition counselling" ,'value'=>'sysval','query'=>'YesNo'),
"nutrition_demonstration" => array('title'=>"19.Demonstration" ,'value'=>'sysval','query'=>'YesNo'),
"nutrition_dietary_supplement" => array('title'=>"19.Dietary supplementation" ,'value'=>'sysval','query'=>'YesNo'),
"nutrition_nan" => array('title'=>"NaN" ,'value'=>'sysval','query'=>'YesNo'),
"nutrition_unimix" => array('title'=>"Unimix" ,'value'=>'sysval','query'=>'YesNo'),
"nutrition_harvest_pro" => array('title'=>"Harvest Pro" ,'value'=>'sysval','query'=>'YesNo'),
"nutrition_wfp" => array('title'=>"WFP" ,'value'=>'sysval','query'=>'YesNo'),
"nutrition_insta" => array('title'=>"Insta" ,'value'=>'sysval','query'=>'YesNo'),
"nutrition_rutf" => array('title'=>"RUTF" ,'value'=>'sysval','query'=>'YesNo'),
"nutrition_other_service" => array('title'=>"Other services" ,'value'=>'sysval','query'=>'YesNo'),*/
'nutrition_program' =>array('title'=>"14.Recommended Food Program" ,'value'=>'sysval','query'=>'NutritionProgram','mode'=> 'multi'),
'nutrition_program_other' => 'Recommended program - Other',
'nutrition_rendered' =>array('title'=>"15.Services Rendered" ,'value'=>'sysval','query'=>'NutritionRendered','mode'=> 'multi'),
'nutrition_service'=> array('title'=>'16.Received Food Program','value'=>'plural',
	'query'=>array(
			'set' => 'select * from nutrition_service where nutrition_service_visit_id="%d"',
			'fields'=>array(						
						'nutrition_service_program'	=>array('title'=>'Food Program','value'=>'sysval','query'=>'NutritionProgram'),
						'nutrition_service_item'=>'Item',
						'nutrition_service_qty'=>'Quantity'						
					)
			)
	),
// 'nutrition_refer' =>array('title'=>'20.Refer to','value'=>'sysval','query'=>'NutritionReferer'),
// "nutrition_refer_other" => "20b.Other" ,
// 'nutrition_next_visit' => array('title'=>'21.Next appointment','xtype'=>'date'),
"nutrition_notes" => "22.Comments"
);
?>
