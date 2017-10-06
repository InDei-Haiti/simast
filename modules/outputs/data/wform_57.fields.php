<?php
$partShow=true;
$selects = array("fld_7" => '',
"fld_15" => '',
"fld_16" => '',
"fld_17" => '');
$fields=array("fld_0" => "1.Informations Personnelles",
"fld_1" => array("title"=>"2.Chef de ménage","value"=>"sysval","query"=>"9"),
"fld_2" => array("title"=>"3.Date d'enregistrement","xtype"=>"date"),
"fld_3" => "4.Prénom",
"fld_4" => "5.Nom",
"fld_5" => array("title"=>"6.Sexe","value"=>"sysval","query"=>"1"),
"fld_6" => array("title"=>"7.Date de naissance","xtype"=>"date"),
"fld_7" => array('title'=>'8.Lieu de naissance','value'=>'preSQL','query'=>'','rquery'=>''
				),
"fld_8" => "9.Adresse personnelle",
"fld_9" => "10.Téléphone personnel",
"fld_10" => array("title"=>"11.Etat de santé","value"=>"sysval","query"=>"8"),
"fld_11" => "12.NIF",
"fld_12" => "13.CIN",
"fld_13" => array("title"=>"14.Catégorie","value"=>"sysval","query"=>"38"),
"fld_14" => "15.Personne de référence",
"fld_15" => array('title'=>'16.Département','value'=>'preSQL','query'=>'','rquery'=>''
				),
"fld_16" => array('title'=>'17.Commune','value'=>'preSQL','query'=>'','rquery'=>''
				),
"fld_17" => array('title'=>'18.Section Communale','value'=>'preSQL','query'=>'','rquery'=>''
				),
"wform_sub_16" => array("title"=>"Mandataire","value"=>"plural",
							"query"=>array(
									"set"=>"select * from wf_57_sub_16 where wf_id='%d'",
									"fields"=>array(
										"fld_0" => "1.Prénom",
"fld_1" => "2.Nom",
"fld_2" => "3.Nif/CIN",
"fld_3" => "4.Adresse",
"fld_4" => "5.Téléphone",
"fld_5" => array("title"=>"6.Type de Relation","value"=>"sysval","query"=>"48"),
"fld_6" => array("title"=>"7.Autoriser à recueillir les allocations","value"=>"sysval","query"=>"36")
									)
								)
							),
"fld_18" => "19.Informations professionnelles",
"fld_19" => array("title"=>"20.Niveau d’étude","value"=>"sysval","query"=>"40"),
"fld_20" => "21.Formation",
"fld_21" => "22.Occupation actuelle",
"fld_22" => "23.Lieu de travail",
"fld_23" => "24.Téléphone de l’employeur ",
"fld_24" => array("title"=>"25.Revenu mensuel","value"=>"sysval","query"=>"42"),
"fld_25" => array("title"=>"26.Etat Civil","value"=>"sysval","query"=>"41"),
"fld_26" => "27.Autres Informations",
"fld_27" => "28.Téléphone de vérification 1  ",
"fld_28" => "29.Téléphone de vérification 2  ",
"fld_29" => array("title"=>"30.Statut de la maison","value"=>"sysval","query"=>"43"),
"fld_30" => "31.Nombre de pièces dans la maison ",
"fld_31" => "32.Nombre de personne à charge",
"wform_sub_31" => array("title"=>"Personnes à charge","value"=>"plural",
							"query"=>array(
									"set"=>"select * from wf_57_sub_31 where wf_id='%d'",
									"fields"=>array(
										"fld_0" => "1.Prénom",
"fld_1" => "2.Nom",
"fld_2" => array("title"=>"3.Date de naissance","xtype"=>"date"),
"fld_3" => "4.Nom de l’établissement fréquenté",
"fld_4" => "5.Adresse de l’établissement",
"fld_5" => "6.Téléphone de l’établissement",
"fld_6" => "7.Téléphone de l’hôte"
									)
								)
							),
"fld_32" => "33.Nombre d’enfants qui vont à l’école",
"fld_33" => "34.Nombre d’enfants en âge scolaire",
"fld_34" => "35.Prénom du conjoint",
"fld_35" => "36.Nom du conjoint",
"fld_36" => "37.Formation du conjoint",
"fld_37" => "38.Occupation actuelle du conjoint",
"fld_38" => "39.Remarque");
?>