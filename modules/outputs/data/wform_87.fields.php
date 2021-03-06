<?php
$partShow=true;
$selects = array("fld_1" => '',
"fld_2" => '',
"fld_3" => '');
$fields=array("fld_0" => "1.Localite",
"fld_1" => array('title'=>'2.Departement','value'=>'preSQL','query'=>'','rquery'=>''
				),
"fld_2" => array('title'=>'3.Commune','value'=>'preSQL','query'=>'','rquery'=>''
				),
"fld_3" => array('title'=>'4.Section communal','value'=>'preSQL','query'=>'','rquery'=>''
				),
"fld_4" => "5.Telephone",
"fld_5" => "6.Precision gps",
"fld_6" => "7.Altitude",
"fld_7" => "8.Latitude",
"fld_8" => "9.Longitude",
"fld_9" => "10.#non residents",
"fld_10" => "11.#residents",
"fld_11" => "12.# total membre",
"fld_12" => array("title"=>"13.presence handicap","value"=>"sysval","query"=>"9"),
"fld_13" => "14.#malad dans le menage",
"fld_14" => array("title"=>"15.malade mois dernier","value"=>"sysval","query"=>"9"),
"fld_15" => "16.#malade dernier",
"fld_16" => "17.#mourir mois dernier",
"fld_17" => array("title"=>"18.Acces a la terre","value"=>"sysval","query"=>"9"),
"fld_18" => array("title"=>"19.Possession terre","value"=>"sysval","query"=>"9"),
"fld_19" => array("title"=>"20.Fou électrik ou de gas?","value"=>"sysval","query"=>"9"),
"fld_20" => array("title"=>"21.Recho ?","value"=>"sysval","query"=>"9"),
"fld_21" => array("title"=>"22.Chodyè ?","value"=>"sysval","query"=>"9"),
"fld_22" => array("title"=>"23.Vantilatè ?","value"=>"sysval","query"=>"9"),
"fld_23" => array("title"=>"24.Telefòn pòtab ?","value"=>"sysval","query"=>"9"),
"fld_24" => array("title"=>"25.Radyo ?","value"=>"sysval","query"=>"9"),
"fld_25" => array("title"=>"26.Zouti/materyèl (pèch, agrikilti, ti metie) ?","value"=>"sysval","query"=>"9"),
"fld_26" => array("title"=>"27.Fè pou repase ?","value"=>"sysval","query"=>"9"),
"fld_27" => array("title"=>"28.Televizyon ?","value"=>"sysval","query"=>"9"),
"fld_28" => array("title"=>"29.Machin a koud ?","value"=>"sysval","query"=>"9"),
"fld_29" => array("title"=>"30.Bisiklèt ?","value"=>"sysval","query"=>"9"),
"fld_30" => array("title"=>"31.Motosiklèt ?","value"=>"sysval","query"=>"9"),
"fld_31" => array("title"=>"32.Machin oubien kamyonèt ?","value"=>"sysval","query"=>"9"),
"fld_32" => array("title"=>"33.Ordinatè/laptop","value"=>"sysval","query"=>"9"),
"fld_33" => array("title"=>"34.Refrigerateur/freezer","value"=>"sysval","query"=>"9"),
"fld_34" => array("title"=>"35.Batterie/invetè/pano solaire/generatris","value"=>"sysval","query"=>"9"),
"fld_35" => array("title"=>"36.DVD player, camera photo","value"=>"sysval","query"=>"9"),
"fld_36" => array("title"=>"37.Pano solè/antenne parabolik","value"=>"sysval","query"=>"9"),
"fld_37" => array("title"=>"38.Possédez-vous des animaux (bétails, volailles) ?","value"=>"sysval","query"=>"9"),
"fld_38" => "39.Combien de volailles possédez-vous?",
"fld_39" => "40.Combien de cabris possédez-vous?",
"fld_40" => "41.Combien de porcs possédez-vous?",
"fld_41" => "42.Combien de boeufs possédez-vous?",
"fld_42" => "43.Combien de moutons possédez-vous?",
"fld_43" => "44.Combien de chevaux ou d'ânes ",
"fld_44" => "45.Autre ",
"fld_45" => array("title"=>"46.Type logement","value"=>"sysval","query"=>"57"),
"fld_46" => "47.#pieces logement",
"fld_47" => array("title"=>"48.Materiaux toit logement","value"=>"sysval","query"=>"58"),
"fld_48" => array("title"=>"49.Materiaux mur logement","value"=>"sysval","query"=>"59"),
"fld_49" => array("title"=>"50.Materiaux sol logement","value"=>"sysval","query"=>"60"),
"fld_50" => array("title"=>"51.Type de toilette","value"=>"sysval","query"=>"61"),
"fld_51" => array("title"=>"52.Energie pour eclairage","value"=>"sysval","query"=>"62"),
"fld_52" => array("title"=>"53.Energie pour cuisine","value"=>"sysval","query"=>"63"),
"fld_53" => array("title"=>"54.Installations cuisine","value"=>"sysval","query"=>"64"),
"fld_54" => array("title"=>"55.Ou cuisiner?","value"=>"sysval","query"=>"65"),
"fld_55" => array("title"=>"56.Ventillation pour la fumee","value"=>"sysval","query"=>"9"),
"fld_56" => array("title"=>"57.Qualite ventillation fumee","value"=>"sysval","query"=>"66"),
"fld_57" => array("title"=>"58.Eau d'usage general","value"=>"sysval","query"=>"67"),
"fld_58" => "59.Distance pour obtenir l'eau",
"fld_59" => array("title"=>"60.#eau famille par jour","value"=>"sysval","query"=>"68"),
"fld_60" => array("title"=>"61.Traiter eau pour boire?","value"=>"sysval","query"=>"9"),
"fld_61" => array("title"=>"62.Si non,pourquoi?","value"=>"sysval","query"=>"69"),
"fld_62" => array("title"=>"63.Si oui,comment?","value"=>"sysval","query"=>"70"),
"fld_63" => array("title"=>"64.Famille/amis qui envoient transferts?","value"=>"sysval","query"=>"9"),
"fld_64" => array("title"=>"65.Support/pret/don recu?","value"=>"sysval","query"=>"9"),
"fld_65" => array("title"=>"66.Blé (farine, bulgur)","value"=>"sysval","query"=>"71"),
"fld_66" => array("title"=>"67.Maïs (grain, farine)","value"=>"sysval","query"=>"71"),
"fld_67" => array("title"=>"68.Riz","value"=>"sysval","query"=>"71"),
"fld_68" => array("title"=>"69.Petit mil","value"=>"sysval","query"=>"71"),
"fld_69" => array("title"=>"70.Manioc/cassave","value"=>"sysval","query"=>"71"),
"fld_70" => array("title"=>"71.Pommes de terre, patates, igname","value"=>"sysval","query"=>"71"),
"fld_71" => array("title"=>"72.Banane ","value"=>"sysval","query"=>"71"),
"fld_72" => array("title"=>"73.Arbre à pain/lam","value"=>"sysval","query"=>"71"),
"fld_73" => array("title"=>"74.Spaghetti, macaroni","value"=>"sysval","query"=>"71"),
"fld_74" => array("title"=>"75.Pain, Beignets, biscuits","value"=>"sysval","query"=>"71"),
"fld_75" => array("title"=>"76.Pois, haricots, lentilles","value"=>"sysval","query"=>"71"),
"fld_76" => array("title"=>"77.Fruits","value"=>"sysval","query"=>"71"),
"fld_77" => array("title"=>"78.Viande rouge, abats","value"=>"sysval","query"=>"71"),
"fld_78" => array("title"=>"79.Poulet, volaille","value"=>"sysval","query"=>"71"),
"fld_79" => array("title"=>"80.Œufs","value"=>"sysval","query"=>"71"),
"fld_80" => array("title"=>"81.Poisson (hareng, morue), fruits de mer","value"=>"sysval","query"=>"71"),
"fld_81" => array("title"=>"82.Lait, fromage, yaourt","value"=>"sysval","query"=>"71"),
"fld_82" => array("title"=>"83.Sucre, miel, confiture","value"=>"sysval","query"=>"71"),
"fld_83" => array("title"=>"84.Huile, graisses, noix de coco","value"=>"sysval","query"=>"71"),
"fld_84" => array("title"=>"85.Pistache, noix, mamba","value"=>"sysval","query"=>"71"),
"fld_85" => array("title"=>"86.Chocolat, cacao","value"=>"sysval","query"=>"71"),
"fld_86" => array("title"=>"87.CSB / Farine pomme de terre","value"=>"sysval","query"=>"71"),
"fld_87" => array("title"=>"88.Légumes, Feuilles, Giraumont","value"=>"sysval","query"=>"71"),
"fld_88" => array("title"=>"89.1. Blé (farine, bulgur)","value"=>"sysval","query"=>"9"),
"fld_89" => array("title"=>"90.2. Maïs (grain, farine)","value"=>"sysval","query"=>"9"),
"fld_90" => array("title"=>"91.3. Riz","value"=>"sysval","query"=>"9"),
"fld_91" => array("title"=>"92.4. Petit mil","value"=>"sysval","query"=>"9"),
"fld_92" => array("title"=>"93.5. Manioc/cassave","value"=>"sysval","query"=>"9"),
"fld_93" => array("title"=>"94.6. Pommes de terre, patates, igname","value"=>"sysval","query"=>"9"),
"fld_94" => array("title"=>"95.7. Banane ","value"=>"sysval","query"=>"9"),
"fld_95" => array("title"=>"96.8. Arbre à pain/lam","value"=>"sysval","query"=>"9"),
"fld_96" => array("title"=>"97.9. Spaghetti, macaroni","value"=>"sysval","query"=>"9"),
"fld_97" => array("title"=>"98.10. Pain, Beignets, biscuits","value"=>"sysval","query"=>"9"),
"fld_98" => array("title"=>"99.11. Pois, haricots, lentilles","value"=>"sysval","query"=>"9"),
"fld_99" => array("title"=>"100.12. Fruits","value"=>"sysval","query"=>"9"),
"fld_100" => array("title"=>"101.13. Viande rouge, abats","value"=>"sysval","query"=>"9"),
"fld_101" => array("title"=>"102.14. Poulet, volaille","value"=>"sysval","query"=>"9"),
"fld_102" => array("title"=>"103.15. Œufs","value"=>"sysval","query"=>"9"),
"fld_103" => array("title"=>"104.16. Poisson (hareng, morue), fruits de mer","value"=>"sysval","query"=>"9"),
"fld_104" => array("title"=>"105.17. Lait, fromage, yaourt","value"=>"sysval","query"=>"9"),
"fld_105" => array("title"=>"106.18. Sucre, miel, confiture","value"=>"sysval","query"=>"9"),
"fld_106" => array("title"=>"107.19. Huile, graisses, noix de coco","value"=>"sysval","query"=>"9"),
"fld_107" => array("title"=>"108.20. Pistache, noix, mamba","value"=>"sysval","query"=>"9"),
"fld_108" => array("title"=>"109.21. Chocolat, cacao","value"=>"sysval","query"=>"9"),
"fld_109" => array("title"=>"110.22. CSB / Farine pomme de terre","value"=>"sysval","query"=>"9"),
"fld_110" => array("title"=>"111.23. Légumes, Feuilles, Giraumont","value"=>"sysval","query"=>"9"),
"fld_111" => array("title"=>"112.H1. Blé (farine, bulgur)","value"=>"sysval","query"=>"72"),
"fld_112" => array("title"=>"113.H2. Maïs (grain, farine)","value"=>"sysval","query"=>"72"),
"fld_113" => array("title"=>"114.H3. Riz","value"=>"sysval","query"=>"72"),
"fld_114" => array("title"=>"115.H4. Petit mil","value"=>"sysval","query"=>"72"),
"fld_115" => array("title"=>"116.H5. Manioc/cassave","value"=>"sysval","query"=>"72"),
"fld_116" => array("title"=>"117.H6. Pommes de terre, patates, igname","value"=>"sysval","query"=>"72"),
"fld_117" => array("title"=>"118.H7. Banane ","value"=>"sysval","query"=>"72"),
"fld_118" => array("title"=>"119.H8. Arbre à pain/lam","value"=>"sysval","query"=>"72"),
"fld_119" => array("title"=>"120.H9. Spaghetti, macaroni","value"=>"sysval","query"=>"72"),
"fld_120" => array("title"=>"121.H10. Pain, Beignets, biscuits","value"=>"sysval","query"=>"72"),
"fld_121" => array("title"=>"122.H11. Pois, haricots, lentilles","value"=>"sysval","query"=>"72"),
"fld_122" => array("title"=>"123.H12. Fruits","value"=>"sysval","query"=>"72"),
"fld_123" => array("title"=>"124.H13. Viande rouge, abats","value"=>"sysval","query"=>"72"),
"fld_124" => array("title"=>"125.H14. Poulet, volaille","value"=>"sysval","query"=>"72"),
"fld_125" => array("title"=>"126.H15. Œufs","value"=>"sysval","query"=>"72"),
"fld_126" => array("title"=>"127.H16. Poisson (hareng, morue), fruits de mer","value"=>"sysval","query"=>"72"),
"fld_127" => array("title"=>"128.H17. Lait, fromage, yaourt","value"=>"sysval","query"=>"72"),
"fld_128" => array("title"=>"129.H18. Sucre, miel, confiture","value"=>"sysval","query"=>"72"),
"fld_129" => array("title"=>"130.H19. Huile, graisses, noix de coco","value"=>"sysval","query"=>"72"),
"fld_130" => array("title"=>"131.H20. Pistache, noix, mamba","value"=>"sysval","query"=>"72"),
"fld_131" => array("title"=>"132.H21. Chocolat, cacao","value"=>"sysval","query"=>"72"),
"fld_132" => array("title"=>"133.H22. CSB / Farine pomme de terre","value"=>"sysval","query"=>"72"),
"fld_133" => array("title"=>"134.H23. Légumes, Feuilles, Giraumont","value"=>"sysval","query"=>"72"),
"fld_134" => array("title"=>"135.Blé (farine, bulgur)","value"=>"sysval","query"=>"73"),
"fld_135" => array("title"=>"136.Maïs (grain, farine)","value"=>"sysval","query"=>"73"),
"fld_136" => array("title"=>"137.Riz","value"=>"sysval","query"=>"73"),
"fld_137" => array("title"=>"138.Petit mil","value"=>"sysval","query"=>"73"),
"fld_138" => array("title"=>"139.Manioc/cassave","value"=>"sysval","query"=>"73"),
"fld_139" => array("title"=>"140.Pommes de terre, patates, igname","value"=>"sysval","query"=>"73"),
"fld_140" => array("title"=>"141.Banane ","value"=>"sysval","query"=>"73"),
"fld_141" => array("title"=>"142.Arbre à pain/lam","value"=>"sysval","query"=>"73"),
"fld_142" => array("title"=>"143.Spaghetti, macaroni","value"=>"sysval","query"=>"73"),
"fld_143" => array("title"=>"144.Pain, Beignets, biscuits","value"=>"sysval","query"=>"73"),
"fld_144" => array("title"=>"145.Pois, haricots, lentilles","value"=>"sysval","query"=>"73"),
"fld_145" => array("title"=>"146.Fruits","value"=>"sysval","query"=>"73"),
"fld_146" => array("title"=>"147.Viande rouge, abats","value"=>"sysval","query"=>"73"),
"fld_147" => array("title"=>"148.Poulet, volaille","value"=>"sysval","query"=>"73"),
"fld_148" => array("title"=>"149.Œufs","value"=>"sysval","query"=>"73"),
"fld_149" => array("title"=>"150.Poisson (hareng, morue), fruits de mer","value"=>"sysval","query"=>"73"),
"fld_150" => array("title"=>"151.Lait, fromage, yaourt","value"=>"sysval","query"=>"73"),
"fld_151" => array("title"=>"152.Sucre, miel, confiture","value"=>"sysval","query"=>"73"),
"fld_152" => array("title"=>"153.Huile, graisses, noix ","value"=>"sysval","query"=>"73"),
"fld_153" => array("title"=>"154.Pistache, noix, mamba","value"=>"sysval","query"=>"73"),
"fld_154" => array("title"=>"155.Chocolat, cacao","value"=>"sysval","query"=>"73"),
"fld_155" => array("title"=>"156.CSB / Farine pomme de terre","value"=>"sysval","query"=>"73"),
"fld_156" => array("title"=>"157.Légumes, Feuilles, Giraumont","value"=>"sysval","query"=>"73"),
"fld_157" => array("title"=>"158.Sans nourriture,manque de ressource","value"=>"sysval","query"=>"74"),
"fld_158" => array("title"=>"159.Aller au lit ayant faim","value"=>"sysval","query"=>"74"),
"fld_159" => array("title"=>"160.Passer journee sans manger","value"=>"sysval","query"=>"74"),
"fld_160" => array("title"=>"161.Possession moustiquaire","value"=>"sysval","query"=>"9"),
"fld_161" => array("title"=>"162.Si oui,chaque membre en a un","value"=>"sysval","query"=>"9"),
"fld_162" => array("title"=>"163.Que faire des ordures menagers","value"=>"sysval","query"=>"76"),
"fld_163" => array("title"=>"164.Ou jetter les ordures","value"=>"sysval","query"=>"77"),
"fld_164" => array("title"=>"165.Methode de planification","value"=>"sysval","query"=>"9"),
"fld_165" => array("title"=>"166.Quelle methode","value"=>"sysval","query"=>"78"),
"fld_166" => array("title"=>"167.Comment reprimander un membre","value"=>"sysval","query"=>"79"),
"fld_167" => "168.# personne moins de 65",
"fld_168" => "169.# enfants [0 5)",
"fld_169" => "170.# enfants [0 5]",
"fld_170" => "171.# enfants [6 15]",
"fld_171" => "172.# enfants [3 18]",
"fld_172" => "173.# enfants [3 20]",
"fld_173" => "174.# enfants [0 15]",
"fld_174" => "175.# enfants [10 12]",
"fld_175" => "176.# enfants [13 15]",
"fld_176" => "177.# personne (+65)",
"fld_177" => "178.# personne [18 64)",
"fld_178" => "179.# personne aynat plus de 15",
"fld_179" => "180.# personne ayant plus de 21",
"fld_180" => "181.taille du menage",
"fld_181" => "182.Nombre de personne active au sein du menage",
"fld_182" => "183.Nombre de personne recevant un transfert ou beneficiant d'un soutien financier",
"fld_183" => "184.Nombre de personne ne parlant pas francais dans le menage",
"fld_184" => "185.Nombre de naissance vivante",
"fld_185" => "186.Nombre de Naissance non vivante",
"fld_186" => "187.Nombre des maladies chroniques présentes au sein du ménage",
"fld_187" => "188.Nombre total des membres du ménage vivant avec un handicap",
"fld_188" => "189.Nombre total de personnes de 15 ans ou plus ne sachant pas lire et / ou écrire au sein de chaque ménage",
"fld_189" => "190.Nombre total de membres du ménage âgés de 21 ans et plus, qui ne sont pas analphabètes mais n’ont pas achevé leur cursus d’école primaire (en l’occurrence le certificat pour le niveau Moyen 2 / 6 A.F.)",
"fld_190" => "191.Nombre total de membres du ménage âgés de 3 à 18 ans et qui n’ont pas fréquenté l'école au cours des 12 mois précédant l'enquête",
"fld_191" => "192.Somme des lacunes scolaires accumulées par le ménage",
"fld_192" => "193.Nombre total des membres du ménage entrant dans la population inactive",
"fld_193" => "194.Nombre total de membres du ménage qui peuvent être considérés comme sans emploi parmi les membres actifs de la famille",
"fld_194" => "195.Somme des toutes les enfants au travail de 10 à 12 ans, ajustée par le facteur de réajustement, et de tous les enfants au travail de 13 à 15 ans, dans chaque ménage",
"fld_195" => "196.Weighted sum of deprivations",
"fld_196" => "197.Haiti's deprivations and Vulnerab",
"fld_197" => "198.Rank of households ",
"fld_198" => array("title"=>"199.Food Insecurity","value"=>"sysval","query"=>"56"),
"fld_199" => array("title"=>"200.Vulnerability group","value"=>"sysval","query"=>"55"),
"fld_200" => array("title"=>"201.korefanmi","value"=>"sysval","query"=>"101"),
"fld_201" => array("title"=>"202.Information complete","value"=>"sysval","query"=>"56"),
"fld_202" => "203.key",
"wform_sub_201" => array("title"=>"Membre","value"=>"plural",
							"query"=>array(
									"set"=>"select * from wf_87_sub_201 where wf_id='%d'",
									"fields"=>array(
										"fld_0" => "1.prenom",
"fld_1" => "2.nom",
"fld_2" => "3.surnom",
"fld_3" => array("title"=>"4.sexe ","value"=>"sysval","query"=>"1"),
"fld_4" => "5.age",
"fld_5" => array("title"=>"6.date de naissance","xtype"=>"date"),
"fld_6" => array("title"=>"7.document d'identification","value"=>"sysval","query"=>"81"),
"fld_7" => "8.numero d'identification",
"fld_8" => array("title"=>"9.lien de parente avec le chef de famille","value"=>"sysval","query"=>"82"),
"fld_9" => "10.numero pere",
"fld_10" => "11.numero mere",
"fld_11" => "12.numero personne responsable",
"fld_12" => "13.relation conjugale",
"fld_13" => array("title"=>"14.statut matrimonial","value"=>"sysval","query"=>"83"),
"fld_14" => array("title"=>"15.situation de residdence actuelle","value"=>"sysval","query"=>"84"),
"fld_15" => array("title"=>"16.membre moins de 18 ans vivant a l'exterieur","value"=>"sysval","query"=>"9"),
"fld_16" => array("title"=>"17.pourquoi vivre a l'exterieur(-18)","value"=>"sysval","query"=>"85"),
"fld_17" => array("title"=>"18.savoir lire","value"=>"sysval","query"=>"9"),
"fld_18" => array("title"=>"19.savoir ecrire","value"=>"sysval","query"=>"9"),
"fld_19" => array("title"=>"20.langue autre que le creole","value"=>"sysval","query"=>"9"),
"fld_20" => array("title"=>"21.assister l'ecole 12 derniers mois","value"=>"sysval","query"=>"9"),
"fld_21" => array("title"=>"22.education actuelle","value"=>"sysval","query"=>"86"),
"fld_22" => array("title"=>"23.plus haut niveau atteint","value"=>"sysval","query"=>"87"),
"fld_23" => array("title"=>"24.niveau d'education","value"=>"sysval","query"=>"88"),
"fld_24" => array("title"=>"25.assiste a l'ecole","value"=>"sysval","query"=>"9"),
"fld_25" => array("title"=>"26.pour quelles raisons","value"=>"sysval","query"=>"89"),
"fld_26" => array("title"=>"27.beneficier du programme PSUGO","value"=>"sysval","query"=>"9"),
"fld_27" => array("title"=>"28.capable de travailler","value"=>"sysval","query"=>"9"),
"fld_28" => array("title"=>"29.travaille actuellement","value"=>"sysval","query"=>"9"),
"fld_29" => array("title"=>"30.activite lucrative pour compte propre","value"=>"sysval","query"=>"9"),
"fld_30" => array("title"=>"31.type de travail","value"=>"sysval","query"=>"90"),
"fld_31" => array("title"=>"32.principale activite economique de la personne","value"=>"sysval","query"=>"91"),
"fld_32" => array("title"=>"33.transferts d'un ami ou d'autres personnes 12 derniers mois","value"=>"sysval","query"=>"9"),
"fld_33" => array("title"=>"34.transfert de famille ou autres personnnes","value"=>"sysval","query"=>"9"),
"fld_34" => array("title"=>"35.beneficiaire de support pret ou dons","value"=>"sysval","query"=>"9"),
"fld_35" => array("title"=>"36.maladie chronique","value"=>"sysval","query"=>"9"),
"fld_36" => array("title"=>"37.disposer de traitement pour maladie chronique","value"=>"sysval","query"=>"9"),
"fld_37" => array("title"=>"38.duree du traitement pour maladie chronique","value"=>"sysval","query"=>"92"),
"fld_38" => array("title"=>"39.pourquoi la personne n'a pas de traitement pour maladie chronique","value"=>"sysval","query"=>"93"),
"fld_39" => array("title"=>"40.autre maladie?","value"=>"sysval","query"=>"9"),
"fld_40" => array("title"=>"41.disposer d'un traitement pour autre maladie","value"=>"sysval","query"=>"9"),
"fld_41" => array("title"=>"42.duree du traitement pour autre maladie","value"=>"sysval","query"=>"92"),
"fld_42" => array("title"=>"43.pourquoi la personne n'a pas de traitemnt pour autre maladie","value"=>"sysval","query"=>"93"),
"fld_43" => array("title"=>"44.presence d'handicap ou de limitation physique","value"=>"sysval","query"=>"9"),
"fld_44" => array("title"=>"45.Quelle limitation a la personne","value"=>"sysval","query"=>"94"),
"fld_45" => array("title"=>"46.la limitation est elle grave","value"=>"sysval","query"=>"9"),
"fld_46" => array("title"=>"47.Type de maladie mois dernier","value"=>"sysval","query"=>"95"),
"fld_47" => array("title"=>"48.Avoir chercher des conseils/traitements","value"=>"sysval","query"=>"9"),
"fld_48" => array("title"=>"49.Ou chercher conseil","value"=>"sysval","query"=>"96"),
"fld_49" => array("title"=>"50.Pourquoi ne pas chercher conseils/traitement","value"=>"sysval","query"=>"93"),
"fld_50" => array("title"=>"51.Est que l'enfant(moins de 5 ans ayant souffert de diarrhee)a pris du serum oral","value"=>"sysval","query"=>"9"),
"fld_51" => array("title"=>"52.Grossesse actuelle","value"=>"sysval","query"=>"98"),
"fld_52" => array("title"=>"53.Grossese dans les 3 années précédentes","value"=>"sysval","query"=>"9"),
"fld_53" => array("title"=>"54.Avoir recu une consultation pour des soins prenataux","value"=>"sysval","query"=>"9"),
"fld_54" => array("title"=>"55.Accouchement lors de la derniere grossesse","value"=>"sysval","query"=>"99"),
"fld_55" => "56.Indiquez le numéro de l'enfant éligible à B1 ",
"fld_56" => array("title"=>"57.Est-ce que vous avez allaité l'enfant?","value"=>"sysval","query"=>"9"),
"fld_57" => array("title"=>"58.Allaitez-vous toujours…   (Prénom de l'enfant)","value"=>"sysval","query"=>"9"),
"fld_58" => "59.A quel âge avez vous terminé d'allaiter l'enfant ?",
"fld_59" => array("title"=>"60.A t-il été pesé pendant les 2 derniers mois?","value"=>"sysval","query"=>"9"),
"fld_60" => array("title"=>"61.Avez-vous sa carte de croissance","value"=>"sysval","query"=>"9"),
"fld_61" => array("title"=>"62.l'enfant a t-il été vacciné?","value"=>"sysval","query"=>"9"),
"fld_62" => array("title"=>"63.Avez-vous la carte de la vaccination de l'enfant?","value"=>"sysval","query"=>"9"),
"fld_63" => "64.Depuis combien de mois (prénom de l'enfant) a-t-il/elle pris sa dernière dose de Vitamine A?"
									)
								)
							),
"fld_203" => array("title"=>"204.source d'eau pour boire","value"=>"sysval","query"=>"67"),
"fld_204" => array("title"=>"205.Comment laver les mains?","value"=>"sysval","query"=>"75"),
"fld_205" => "206.nombre de fois laver les mains?",
"fld_206" => array("title"=>"207.Milieu","value"=>"sysval","query"=>"100"));
?>