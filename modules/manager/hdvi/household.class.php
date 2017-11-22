<?php
require_once ($baseDir . '/modules/manager/hdvi/member.class.php');
class Household {
	public $id;
	public $date_survey;
	public $milieu;
	public $urbain = FALSE;
	public $rural = FALSE;
	public $metro = FALSE;
	public $members;
	public $absence_of_food;
	public $hunger;
	public $restricted_consumption;
	public $materiau_wall;
	public $materiau_floor;
	public $materiau_roof;
	public $number_of_romm;
	public $lighting_access;
	public $energy_access;
	public $potable_water;
	public $cleaning_water;
	public $waste_evacuation;
	public $toilet_acces;
	public $member_table;
	public $table;
	public $member_fld;
	public $fld;
	public $key;
	public static $COUNTER;
	private $name;
	private $complete = true;
	/**
	 * Load member by age intervalle
	 */
	function __construct() {
	}
	function setId($idH){
		$this->id = $idH;
	}

	function addMember($member) {
		$this->members [] = $member;
	}
    /*function getJsonData(){
        $var = get_object_vars($this);
        foreach ($var as &$value) {
            if (is_object($value) && method_exists($value,'getJsonData')) {
                $value = $value->getJsonData();
            }
        }
        return $var;
    }*/
	function processingHdvi() {
		global $table;
		//echo $this->id.'<br/>';
		//echo "Key: ".$this->key.'<br/>';
		if ($this->milieu == '2')
			$this->urbain = true;
		if ($this->milieu == '3')
			$this->rural = true;
		if ($this->milieu == '1')
			$this->metro = true;
		$arrl = array ();
		$arrl['key'] = $this->key;
		if (! $this->rural && ! $this->urbain && ! $this->metro)
			return;
		if (count ( $this->members ) == 0) {
			return;
		}
		
		// Count the familly size
		$family_size = 0;
		count ( $this->members );
		$chef = 0;
		foreach ( $this->members as $mf ) {
            if ($mf->member_fld_age === null) {
                return;
            }
            /*if ($mf->member_fld_write === null && $mf->member_fld_read === null) {
                return;
            }*/

            if ($mf->member_fld_linkparent != null && $mf->member_fld_linkparent <= 15){
                $family_size++;
                if ($mf->member_fld_linkparent==1)
                    $chef += $mf->member_fld_linkparent;
            }
		}
		if($family_size==0)
		    return;
        $arrl['HSize'] = $family_size;
		if($chef != 1)
		    return;
		$count_child_under_15 = 0; // count child under 15
		$count_eldery = 0; // count eldery person >= 65
		$count_member_15_plus = 0;
		$is_couple = False; // Is en couple b7 == 2
		                    
		// variable indicateur
		$d_1_1 = 0;
		$d_1_2 = 0;
		$d_2_1 = 0;
		$d_2_2 = 0;
		$d_3_1 = 0;
		$d_3_2 = 0;
		$d_3_3 = 0;
		$d_3_4 = 0;
		$d_4_1 = 0;
		$d_4_2 = 0;
		$d_4_3 = 0;
		$d_5_1 = 0;
		$d_5_2 = 0;
		$d_5_3 = 0;
		$d_6_1 = 0;
		$d_6_2 = 0;
		$d_6_3 = 0;
		$d_7_1 = 0;
		$d_7_2 = 0;
		$d_7_3 = 0;

		$prisk_1_1 = 0;
		$prisk_1_2 = 0;
		$prisk_2_1 = 0;
		$prisk_2_2 = 0;
		$prisk_3_1 = 0;
		$prisk_3_2 = 0;
		$prisk_3_3 = 0;
		$prisk_3_4 = 0;
		$prisk_4_1 = 0;
		$prisk_4_2 = 0;
		$prisk_4_3 = 0;
		$prisk_5_1 = 0;
		$prisk_5_2 = 0;
		$prisk_5_3 = 0;
		$prisk_6_1 = 0;
		$prisk_6_2 = 0;
		$prisk_6_3 = 0;
		$prisk_7_1 = 0;
		$prisk_7_2 = 0;
		$prisk_7_3 = 0;

		// variable de calcul
		$partner = 0;
		$count_child_0_4 = 0;
		$count_child_18_64 = 0;
		$count_0_64 = 0;
		$count_0_5 = 0;
		$count_6_15 = 0;
		$count_3_18 = 0;
		$count_3_20 = 0;
		$count_0_15 = 0;
		$count_not_complete_basic_edu = 0;
		$count_chronically_ill = 0;
		$count_disabled = 0;
		$count_illiterate = 0;
		$count_member_21_plus = 0;
		$count_member_not_at_school = 0;
		$count_member_schooling_age = 0;
		$count_member_3_20 = 0;
		$count_member_inactive = 0;
		$count_member_active = 0;
		$count_member_active_inactive = 0;
		$count_member_unemployed = 0;
		$count_child_labourer_1 = 0;
		$count_child_labourer_2 = 0;
		$count_child_10_12 = 0;
		$count_child_13_15 = 0;
		$count_transf = 0;
		$count_supp = 0;


		
		// iteration member
		foreach ( $this->members as $member ) {
			// 1.1
            if($member->member_fld_age===null)
                $this->complete = false;
			if ($member->member_fld_linkparent != null && $member->member_fld_linkparent == 2) {
				$partner ++;
			}
			//echo ' age: '.$member->member_fld_age;
			if ($member->member_fld_age !== null && $member->member_fld_age <= 15) { // Compte les membres de 15 ans et moins
				$count_child_under_15 ++;
			}
			
			if ($member->member_fld_age !== null && $member->member_fld_age >= 65) { // Compte les personnes ages de 65 ans et plus
				$count_eldery ++;
			}
			
			// 1.2
			if ($member->member_fld_age !== null && $member->member_fld_age >= 0 && $member->member_fld_age <= 4) {
				$count_child_0_4 ++;
			}
			if ($member->member_fld_age !== null && $member->member_fld_age >= 18 && $member->member_fld_age <= 65) {
				$count_child_18_64 ++;
			}
            if ($member->member_fld_age !== null && $member->member_fld_age >= 0 && $member->member_fld_age <= 64) {
                $count_0_64++;
            }
            if ($member->member_fld_age !== null && $member->member_fld_age >= 0 && $member->member_fld_age <= 5) {
                $count_0_5++;
            }
            if ($member->member_fld_age !== null && $member->member_fld_age >= 6 && $member->member_fld_age <= 15) {
                $count_6_15++;
            }
            if ($member->member_fld_age !== null && $member->member_fld_age >= 3 && $member->member_fld_age <= 18) {
                $count_3_18++;
            }
            if ($member->member_fld_age !== null && $member->member_fld_age >= 3 && $member->member_fld_age <= 20) {
                $count_3_20++;
            }
            if ($member->member_fld_age !== null && $member->member_fld_age >= 0 && $member->member_fld_age <= 15) {
                $count_0_15++;
            }
			
			// 2.1
			if (is_array ( $member->member_fld_lsickness ) && count ( $member->member_fld_lsickness ) > 0) {
				foreach ( $member->member_fld_lsickness as $ill ) {
					if ($ill >= 1 && $ill <= 9)
						$count_chronically_ill ++;
				}
			}
			
			// 2.2
			$count_sick = 0;
			$temp = false;
			if (is_array ( $member->member_fld_lsickness ) && count ( $member->member_fld_lsickness ) > 0) {
				foreach ( $member->member_fld_lsickness as $ill ) {
					if ($ill >= 1 && $ill <= 9)
						$count_sick ++;
				}
			}
			if (($member->member_prob_eye == 3 || $member->member_prob_eye == 4) && ! ($count_sick > 0)) {
				$count_disabled ++;
			}
			if (($member->member_prob_autooins == 3 || $member->member_prob_autooins == 4) && ! ($count_sick > 0)) {
				$count_disabled ++;
			}
			if (($member->member_prob_hear == 3 || $member->member_prob_hear == 4) && ! ($count_sick > 0)) {
				$count_disabled ++;
			}
			if (($member->member_prob_speak == 3 || $member->member_prob_speak == 4) && ! ($count_sick > 0)) {
				$count_disabled ++;
			}
			
			// 3.1
			if ($member->member_fld_age != null && $member->member_fld_age >= 15 && ($member->member_fld_read != null && $member->member_fld_read == 2 || $member->member_fld_write != null && $member->member_fld_write == 2)) {
				$count_illiterate ++;
			}
			if ($member->member_fld_age != null && $member->member_fld_age >= 15) {
				$count_member_15_plus ++;
			}
			
			// 3.2
			if (! ($member->member_fld_read == 2 || $member->member_fld_write == 2) && $member->member_fld_age >= 21 && $member->member_fld_level_edu != null && $member->member_fld_level_edu <= 7) {
				$count_not_complete_basic_edu ++;
			}
			
			if ($member->member_fld_age != null && $member->member_fld_age >= 21) {
				$count_member_21_plus ++;
			}
			if (! ($member->member_fld_read == 2 || $member->member_fld_write == 2) && $member->member_fld_age != null && $member->member_fld_age >= 21) {
				$prisk_3_2 ++;
			}
			
			// 3.3
			if ($member->member_fld_age != null && $member->member_fld_age >= 3 && $member->member_fld_age <= 18) {
				$count_member_schooling_age ++;
			}
			if ($member->member_fld_age != null && $member->member_fld_age >= 3 && $member->member_fld_age <= 18 && $member->member_fld_lst_scho_12 != null && $member->member_fld_lst_scho_12 == 2) {
				$count_member_not_at_school ++;
			}
			
			// 3.4
			
			// 4.1
			if ($member->member_fld_age != null && $member->member_fld_age >= 18 && $member->member_fld_age <= 64) {
				if ($member->member_fld_eco_active != null && (($member->member_fld_eco_active >= 1 && $member->member_fld_eco_active <= 6) || $member->member_fld_eco_active == 8)) {
					$count_member_active ++;
				}
				if ($member->member_fld_eco_active != null && ($member->member_fld_eco_active == 7 || $member->member_fld_eco_active >= 9 && $member->member_fld_eco_active <= 12)) {
					$count_member_inactive ++;
				}
			}
			
			// 4.2
			if ($member->member_fld_eco_active != null && $member->member_fld_eco_active >= 4 && $member->member_fld_eco_active <= 6 && ($member->member_fld_age != null && $member->member_fld_age >= 18 && $member->member_fld_age <= 64)) {
				$count_member_unemployed ++;
			}
			
			// 4.3
			if ($member->member_fld_age != null && $member->member_fld_age >= 10 && $member->member_fld_age <= 12) {
				$count_child_10_12 ++;
			}
			if ($member->member_fld_age != null && $member->member_fld_age >= 13 && $member->member_fld_age <= 15) {
				$count_child_13_15 ++;
			}
			if ((($member->member_fld_eco_active != null && $member->member_fld_eco_active >= 1 && $member->member_fld_eco_active <= 3) || ($member->member_fld_eco_active != null && $member->member_fld_eco_active == 8)) && $member->member_fld_age != null && $member->member_fld_age >= 10 && $member->member_fld_age <= 12) {
				$count_child_labourer_1 ++;
			}
			
			if ((($member->member_fld_eco_active != null && $member->member_fld_eco_active >= 1 && $member->member_fld_eco_active <= 3) || ($member->member_fld_eco_active != null && $member->member_fld_eco_active == 8)) && $member->member_fld_age != null && $member->member_fld_age >= 13 && $member->member_fld_age <= 15) {
				$count_child_labourer_2 ++;
			}
			
			// 5.1
			
			// 6.1
			if ($member->member_transf != null && $member->member_transf == 1) {
				$count_transf ++;
			}
			if ($member->member_supp != null && $member->member_supp == 1) {
				$count_supp ++;
			}
			
		}
		/**
		 * Calcul de l'indicateur #1 ou 1.1
		 * Household Demographic Composition
		 */
		$cat_fam = null; // Categorie de famille
		if ($partner > 0) {
			$is_couple = True;
		}
		/**
		 * Classifie les familles en categorie suivant le tableau 2 du document
		 */
		if ($count_child_under_15 == 0) {
			$cat_fam = 1;
		}
		
		if ($is_couple == False && $count_child_under_15 > 0 && $count_eldery == 0) {
			$cat_fam = 2;
		}
		
		if ($is_couple == True && $count_child_under_15 > 0 && $count_eldery == 0) {
			$cat_fam = 3;
		}
		
		if ($is_couple == False && $count_child_under_15 > 0 && $count_eldery > 0) {
			$cat_fam = 4;
		}
		//echo 'couple: '.$is_couple.' 15: '.$count_child_under_15.' eldery: '.$count_eldery.' ';
		if ($is_couple == True && $count_child_under_15 > 0 && $count_eldery > 0) {
			$cat_fam = 5;
		}
		/**
		 * Affecte une valeur a la variable $d_1_1 suivant la categorie de la famille
		 */
		//echo ' Cat: '.$cat_fam.' ';
		switch ($cat_fam) {
			case 1 :
				$d_1_1 = 0;
				break;
			case 2 :
				if ($this->urbain)
					$d_1_1 = 0.72601312;
				if ($this->rural)
					$d_1_1 = 0.41791585;
				if ($this->metro)
					$d_1_1 = 2.8165891;
				break;
			case 3 :
				if ($this->urbain)
					$d_1_1 = 0.47818928;
				if ($this->rural)
					$d_1_1 = 2.486249;
				if ($this->metro)
					$d_1_1 = 3.000000;
				break;
			case 4 :
				if ($this->urbain)
					$d_1_1 = 0.157214;
				if ($this->rural)
					$d_1_1 = 1.442669;
				if ($this->metro)
					$d_1_1 = 1.570386;
				break;
			case 5 :
				if ($this->urbain)
					$d_1_1 = 3.000000;
				if ($this->rural)
					$d_1_1 = 3.000000;
				if ($this->metro)
					$d_1_1 = 0.612641;
				break;
		}

		$prisk_1_1 = 3; // prisk_1_1 est egal a 1 page 7 du document
		$hdr_1_1 = ($d_1_1) / pow ( $prisk_1_1, 0.5 ); // Calcul de household deprivation ratio (hdr)
		$arrl ['prisk_1_1'] = $prisk_1_1;
		$arrl ['d_1_1'] = $d_1_1;
		$arrl ['hdr_1_1'] = $hdr_1_1;
        //echo '1.1: ('.$d_1_1.','.$prisk_1_1.')<br/>';
		// //echo 'hdr_1_1 '.$hdr_1_1.' <br/>';
		
		/**
		 * Indicateur #2 ou 1.2
		 * Children under 5 years old
		 */
		$d_1_2 = $count_child_0_4;
		$prisk_1_2 = $count_child_18_64 + $count_child_0_4;
		if ($prisk_1_2 != 0) {
			$hdr_1_2 = $d_1_2 / pow ( $prisk_1_2, 0.5 );
		} else {
			$hdr_1_2 = 0;
		}
		$arrl ['prisk_1_2'] = $prisk_1_2;
		$arrl ['d_1_2'] = $d_1_2;
		$arrl ['hdr_1_2'] = $hdr_1_2;
		// //echo 'hdr_1_2 '.$hdr_1_2.' <br/>';
        //echo '1.2: ('.$d_1_2.','.$prisk_1_2.')<br/>';
		/**
		 * HEALTH
		 * Indicateur # 3 ou 2.1
		 * Chronically ILL
		 */
		if ($count_chronically_ill > $family_size) {
			$d_2_1 = $family_size;
		} else {
			$d_2_1 = $count_chronically_ill;
		}
		$prisk_2_1 = $family_size;
		if ($prisk_2_1 != 0) {
			$hdr_2_1 = $d_2_1 / pow ( $prisk_2_1, 0.5 );
		} else {
			$hdr_2_1 = 0;
		}
		$arrl ['prisk_2_1'] = $prisk_2_1;
		$arrl ['d_2_1'] = $d_2_1;
		$arrl ['hdr_2_1'] = $hdr_2_1;
		// //echo 'hdr_2_1 '.$hdr_2_1.' <br/>';
        //echo '2.1: ('.$d_2_1.','.$prisk_2_1.')<br/>';

		/**
		 * Indicateur # 4 2.2
		 * Disabled or permanently injured but not chronically ill
		 */
		if ($count_disabled > $family_size) {
			$d_2_2 = $family_size;
		} else {
			$d_2_2 = $count_disabled;
		}
		
		$prisk_2_2 = $family_size;
		if ($prisk_2_2 != 0) {
			$hdr_2_2 = $d_2_2 / pow ( $prisk_2_2, 0.5 );
		} else {
			$hdr_2_2 = 0;
		}
		
		$arrl ['prisk_2_2'] = $prisk_2_2;
		$arrl ['d_2_2'] = $d_2_2;
		$arrl ['hdr_2_2'] = $hdr_2_2;
		// echo 'hdr_2_2 '.$hdr_2_2.' <br/>';////echo
		
		/**
		 * EDUCATION
		 * Indicateur #5 ou 3.1
		 * ILLITERACY
		 */
		// //echo $count_illiterate;
		$d_3_1 = $count_illiterate;
		$prisk_3_1 = $count_member_15_plus;
		if ($prisk_3_1 != 0) {
			$hdr_3_1 = $d_3_1 / pow ( $prisk_3_1, 0.5 );
		} else {
			$hdr_3_1 = 0;
		}
		
		$arrl ['prisk_3_1'] = $prisk_3_1;
		$arrl ['d_3_1'] = $d_3_1;
		$arrl ['hdr_3_1'] = $hdr_3_1;
		// echo 'hdr_3_1 '.$hdr_3_1.' <br/>';
		
		/**
		 * Indicateur #6 3.2
		 * Lack of basic education
		 */
		$d_3_2 = $count_not_complete_basic_edu;
		// $prisk_3_2 = $count_member_21_plus;
		
		if ($prisk_3_2 != 0) {
			$hdr_3_2 = $d_3_2 / pow ( $prisk_3_2, 0.5 );
		} else {
			$hdr_3_2 = 0;
		}
		$arrl ['prisk_3_2'] = $prisk_3_2;
		$arrl ['d_3_2'] = $d_3_2;
		$arrl ['hdr_3_2'] = $hdr_3_2;
		// echo 'hdr_3_2 '.$hdr_3_2.' <br/>';
		
		/**
		 * Indicateur #7 3.3
		 * School non-attendance
		 */
		$d_3_3 = $count_member_not_at_school;
		$prisk_3_3 = $count_member_schooling_age;
		
		if ($prisk_3_3 != 0) {
			$hdr_3_3 = $d_3_3 / pow ( $prisk_3_3, 0.5 );
		} else {
			$hdr_3_3 = 0;
		}
		
		$arrl ['prisk_3_3'] = $prisk_3_3;
		$arrl ['d_3_3'] = $d_3_3;
		$arrl ['hdr_3_3'] = $hdr_3_3;
		// echo 'hdr_3_3 '.$hdr_3_3.' <br/>';
		
		/**
		 * Indicateur 8 3.4
		 * School LAG
		 */
		$yearlag = array ();
		$iii = 1;
		foreach ( $this->members as $member ) {
			if ($member->member_fld_age != null && $member->member_fld_age >= 3 && $member->member_fld_age <= 20) {
				$count_member_3_20 ++;
			}
			// if($member->member_fld_level_edu != null && $member->member_fld_act_edu !=null){
			
			$normedu = null;
			if ($member->member_fld_age != null) {
				if ($member->member_fld_age <= 6)
					$normedu = 0;
				if ($member->member_fld_age == 7)
					$normedu = 1;
				if ($member->member_fld_age == 8)
					$normedu = 2;
				if ($member->member_fld_age == 9)
					$normedu = 3;
				if ($member->member_fld_age == 10)
					$normedu = 4;
				if ($member->member_fld_age == 11)
					$normedu = 5;
				if ($member->member_fld_age == 12)
					$normedu = 6;
				if ($member->member_fld_age == 13)
					$normedu = 7;
				if ($member->member_fld_age == 14)
					$normedu = 8;
				if ($member->member_fld_age == 15)
					$normedu = 9;
				if ($member->member_fld_age == 16)
					$normedu = 10;
				if ($member->member_fld_age == 17)
					$normedu = 11;
				if ($member->member_fld_age == 18)
					$normedu = 12;
				if ($member->member_fld_age >= 19)
					$normedu = 13;
			}
			
			$aedu = null;
			if (($member->member_fld_level_edu != null && $member->member_fld_level_edu <= 2) || ($member->member_fld_act_edu != null && $member->member_fld_act_edu <= 3))
				$aedu = 0;
			if ((($member->member_fld_level_edu != null && $member->member_fld_level_edu == 3) || ($member->member_fld_act_edu != null && $member->member_fld_act_edu == 4)) && $aedu == null)
				$aedu = 1;
			if ((($member->member_fld_level_edu != null && $member->member_fld_level_edu == 4) || ($member->member_fld_act_edu != null && $member->member_fld_act_edu == 5)) && $aedu == null)
				$aedu = 2;
			if ((($member->member_fld_level_edu != null && $member->member_fld_level_edu == 5) || ($member->member_fld_act_edu != null && $member->member_fld_act_edu == 6)) && $aedu == null)
				$aedu = 3;
			if ((($member->member_fld_level_edu != null && $member->member_fld_level_edu == 6) || ($member->member_fld_act_edu != null && $member->member_fld_act_edu == 7)) && $aedu == null)
				$aedu = 4;
			if ((($member->member_fld_level_edu != null && $member->member_fld_level_edu == 7) || ($member->member_fld_act_edu != null && $member->member_fld_act_edu == 8)) && $aedu == null)
				$aedu = 5;
			if ((($member->member_fld_level_edu != null && $member->member_fld_level_edu == 8) || ($member->member_fld_act_edu != null && $member->member_fld_act_edu == 9)) && $aedu == null)
				$aedu = 6;
			if ((($member->member_fld_level_edu != null && $member->member_fld_level_edu == 9) || ($member->member_fld_act_edu != null && $member->member_fld_act_edu == 10)) && $aedu == null)
				$aedu = 7;
			if ((($member->member_fld_level_edu != null && $member->member_fld_level_edu == 10) || ($member->member_fld_act_edu != null && $member->member_fld_act_edu == 11)) && $aedu == null)
				$aedu = 8;
			if ((($member->member_fld_level_edu != null && $member->member_fld_level_edu == 11) || ($member->member_fld_act_edu != null && $member->member_fld_act_edu == 12)) && $aedu == null)
				$aedu = 9;
			if ((($member->member_fld_level_edu != null && $member->member_fld_level_edu == 12) || ($member->member_fld_act_edu != null && $member->member_fld_act_edu == 13)) && $aedu == null)
				$aedu = 10;
			if ((($member->member_fld_level_edu != null && $member->member_fld_level_edu == 13) || ($member->member_fld_act_edu != null && $member->member_fld_act_edu == 14)) && $aedu == null)
				$aedu = 11;
			if ((($member->member_fld_level_edu != null && $member->member_fld_level_edu == 14) || ($member->member_fld_act_edu != null && $member->member_fld_act_edu == 15)) && $aedu == null)
				$aedu = 12;
			if ((($member->member_fld_level_edu != null && $member->member_fld_level_edu == 15) || ($member->member_fld_act_edu != null && $member->member_fld_act_edu == 16)) && $aedu == null)
				$aedu = 13;
			if (($member->member_fld_level_edu && $member->member_fld_level_edu == 16) && aedu == null)
				$aedu = 14;
				// $temp = -$aedu;
			if ($member->member_fld_level_edu == null && $member->member_fld_act_edu == null)
				$aedu = null;
			$edulag = 0;
			
			if ($aedu >= 0 && $normedu != null)
				if ($aedu < $normedu && $member->member_fld_age >= 3 && $member->member_fld_age < 21)
					$edulag = $normedu - $aedu;
			
			if ($normedu == 0 && $member->member_fld_age >= 3 && $member->member_fld_age < 21)
				$edulag = 0;
			if ($aedu >= $normedu && $member->member_fld_age >= 3 && $member->member_fld_age < 21)
				$edulag = 0;
				// //echo '('.$normedu.','.$aedu.','.$edulag.')';
			
			if ($edulag == 1 || $edulag == 2 || $edulag == 3)
				$yearlag [] = 1;
			elseif ($edulag >= 4)
				$yearlag [] = 2;
			else
				$yearlag [] = 0;
		}
		
		// var_dump($yearlag);
		$total_count = array_sum ( $yearlag ); // $yearlag[0];
		if ($total_count > $count_member_3_20) {
			$d_3_4 = $count_member_3_20;
		} else {
			$d_3_4 = $total_count;
		}
		
		/*
		 * $q11 = new DBQuery();
		 * $q11->addTable('edulag');
		 * $q11->addQuery('d_edulag');
		 * $q11->addWhere('`key`="'.$this->key.'"');
		 * $q11->limit = 1;
		 * $d_3_4 = intval($q11->loadResult());
		 */
		
		$prisk_3_4 = $count_member_3_20;
		if ($prisk_3_4 != 0) {
			$hdr_3_4 = $d_3_4 / pow ( $prisk_3_4, 0.5 );
		} else {
			$hdr_3_4 = 0;
		}
		// //echo $hdr_3_4;
		
		$arrl ['prisk_3_4'] = $prisk_3_4;
		$arrl ['d_3_4'] = $d_3_4;
		$arrl ['hdr_3_4'] = $hdr_3_4;
		// echo 'hdr_3_4 '.$hdr_3_4.' <br/>';
		
		/**
		 * LABOUR Condition
		 * Inactivity
		 * Indicateur #9 4.1
		 */
		$count_member_active_inactive = $count_member_active + $count_member_inactive;
		$d_4_1 = $count_member_inactive;
		$prisk_4_1 = $count_member_active_inactive;
		if ($prisk_4_1 != 0) {
			$hdr_4_1 = $d_4_1 / pow ( $prisk_4_1, 0.5 );
		} else {
			$hdr_4_1 = 0;
		}
		
		$arrl ['prisk_4_1'] = $prisk_4_1;
		$arrl ['d_4_1'] = $d_4_1;
		$arrl ['hdr_4_1'] = $hdr_4_1;
		// echo 'hdr_4_1 '.$hdr_4_1.' <br/>';
		
		/**
		 * Unemployment
		 * Indicateur #10 4.2
		 */
		/*
		 * $unemployed_array = [
		 * 4,
		 * 5,
		 * 6
		 * ];
		 */
		$d_4_2 = $count_member_unemployed;
		$prisk_4_2 = $count_member_active;
		
		if ($prisk_4_2 != 0) {
			$hdr_4_2 = $d_4_2 / pow ( $prisk_4_2, 0.5 );
		} else {
			$hdr_4_2 = 0;
		}
		$arrl ['prisk_4_2'] = $prisk_4_2;
		$arrl ['d_4_2'] = $d_4_2;
		$arrl ['hdr_4_2'] = $hdr_4_2;
		// echo 'hdr_4_2 '.$hdr_4_2.' <br/>';
		
		/**
		 * Child Labor
		 * Indicateur #11 4.3
		 */
		
		$d_4_3 = $count_child_labourer_1 * 1.5 + $count_child_labourer_2;
		$prisk_4_3 = $count_child_10_12 * 1.5 + $count_child_13_15;
		
		if ($prisk_4_3 != 0) {
			$hdr_4_3 = $d_4_3 / pow ( $prisk_4_3, 0.5 );
		} else {
			$hdr_4_3 = 0;
		}
		
		$arrl ['prisk_4_3'] = $prisk_4_3;
		$arrl ['d_4_3'] = $d_4_3;
		$arrl ['hdr_4_3'] = $hdr_4_3;
		// echo 'hdr_4_3 '.$hdr_4_3.' <br/>';
		
		/**
		 * FOOD SECURITY
		 * Absence of food
		 * Indicator #12 5.1
		 */
		
		$prisk_5_1 = 10;
		switch ($this->absence_of_food) {
			case 0 :
				$d_5_1 = 0;
				break;
			case 1 :
				$d_5_1 = 3;
				break;
			
			case 2 :
				$d_5_1 = 10;
				break;
		}
		$hdr_5_1 = $d_5_1 / pow ( $prisk_5_1, 0.5 );
		
		$arrl ['prisk_5_1'] = $prisk_5_1;
		$arrl ['d_5_1'] = $d_5_1;
		$arrl ['hdr_5_1'] = $hdr_5_1;
		// echo 'hdr_5_1 '.$hdr_5_1.' <br/>';
		
		/**
		 * Hunger
		 * Indicator #13 5.2
		 */
		$prisk_5_2 = 10;
		switch ($this->hunger) {
			case 0 :
				$d_5_2 = 0;
				break;
			case 1 :
				$d_5_2 = 3;
				break;
			case 2 :
				$d_5_2 = 10;
				break;
		}
		$hdr_5_2 = $d_5_2 / pow ( $prisk_5_2, 0.5 );
		
		$arrl ['prisk_5_2'] = $prisk_5_2;
		$arrl ['d_5_2'] = $d_5_2;
		$arrl ['hdr_5_2'] = $hdr_5_2;
		// echo 'hdr_5_2 '.$hdr_5_2.' <br/>';
		
		/**
		 * Restricted Consumption
		 * Indicator #14 5.3
		 */
		$prisk_5_3 = 10;
		
		switch ($this->restricted_consumption) {
			case 0 :
				$d_5_3 = 0;
				break;
			case 1 :
				$d_5_3 = 3;
				break;
			case 2 :
				$d_5_3 = 10;
				break;
		}
		
		$hdr_5_3 = $d_5_3 / pow ( $prisk_5_3, 0.5 );
		
		$arrl ['prisk_5_3'] = $prisk_5_3;
		$arrl ['d_5_3'] = $d_5_3;
		$arrl ['hdr_5_3'] = $hdr_5_3;
		// echo 'hdr_5_3 '.$hdr_5_3.' <br/>';
		
		/**
		 * Ressource at Home
		 * Absence of remittances or Benefits
		 * Indicator #15 6.1
		 */
		
		if ($count_transf > 3) {
			$count_transf = 3;
		}
		if ($count_supp > 3) {
			$count_supp = 3;
		}
		$d_6_1 = 6 - ($count_transf + $count_supp);
		$prisk_6_1 = 6;
		$hdr_6_1 = $d_6_1 / pow ( $prisk_6_1, 0.5 );
		
		$arrl ['prisk_6_1'] = $prisk_6_1;
		$arrl ['d_6_1'] = $d_6_1;
		$arrl ['hdr_6_1'] = $hdr_6_1;
		// echo 'hdr_6_1 '.$hdr_6_1.' <br/>';
		
		/**
		 * Dwelling conditions
		 * INidcator #16 6.2
		 */
		
		$prisk_6_2 = 3;
		
		if ($this->materiau_floor == 1 || $this->materiau_floor == 2 || $this->materiau_floor == 6) {
			$floor = 1;
		} else {
			$floor = 0;
		}
		
		if (($this->materiau_roof >= 1 && $this->materiau_roof <= 4) && ($this->metro || $this->urbain)) {
			$roof = 1;
		} elseif (($this->materiau_roof == 1 || $this->materiau_roof == 3 || $this->materiau_roof == 4 || $this->materiau_roof == 8) && $this->rural) {
			$roof = 1;
		} else {
			$roof = 0;
		}
		
		if (($this->materiau_wall >= 1 && $this->materiau_wall <= 4) || ($this->materiau_wall >= 7 && $this->materiau_wall <= 8)) {
			$wall = 1;
		} else {
			$wall = 0;
		}
		
		// Metropolitan
		$s1_flo = 0.66435838;
		$s1_roo = 0.08344792;
		$s1_wal = 0.2521937;
		
		// Urban
		$s2_flo = 0.66232173;
		$s2_roo = 0.0785398;
		$s2_wal = 0.25913846;
		
		// Rural
		$s3_flo = 0.5534845;
		$s3_roo = 0.07908959;
		$s3_wal = 0.36742591;
		
		// Metropolitan
		if ($this->metro) {
			$d_6_2 = ($floor * $s1_flo) + ($roof * $s1_roo) + ($wall * $s1_wal);
		}
		
		if ($this->urbain) {
			$d_6_2 = ($floor * $s2_flo) + ($roof * $s2_roo) + ($wall * $s2_wal);
		}
		
		if ($this->rural) {
			$d_6_2 = ($floor * $s3_flo) + ($roof * $s3_roo) + ($wall * $s3_wal);
		}
		$d_6_2 = $d_6_2 * 3;
		$hdr_6_2 = $d_6_2 / pow ( $prisk_6_2, 0.5 );
		
		$arrl ['prisk_6_2'] = $prisk_6_2;
		$arrl ['d_6_2'] = $d_6_2;
		$arrl ['hdr_6_2'] = $hdr_6_2;
		// echo 'hdr_6_2 '.$hdr_6_2.' <br/>';
		
		/**
		 * Overcrowding
		 * Indicator : 17
		 */
		
		if ($this->number_of_romm != null) {
			$number_of_romm = 0;
			$ratio_room_member = 0;
			$number_of_romm = $this->number_of_romm;
			if ($number_of_romm > 10) {
				$number_of_romm = 10;
			}
			
			$d_6_3 = $family_size / $number_of_romm;
			
			if ($d_6_3 < 4 && $this->metro) {
				$d_6_3 = 0;
			}
			if ($d_6_3 < 4.5 && $this->urbain) {
				$d_6_3 = 0;
			}
			if ($d_6_3 < 5 && $this->rural) {
				$d_6_3 = 0;
			}
			
			if ($this->metro) {
				$prisk_6_3 = 8;
			}
			if ($this->urbain) {
				$prisk_6_3 = 10;
			}
			if ($this->rural) {
				$prisk_6_3 = 10;
			}
			
			if ($prisk_6_3 < $d_6_3) {
				$d_6_3 = $prisk_6_3;
			}
		}
		
		if ($prisk_6_3 != 0) {
			$hdr_6_3 = $d_6_3 / pow ( $prisk_6_3, 0.5 );
		} else {
			$hdr_6_3 = 0;
		}
		
		$arrl ['prisk_6_3'] = $prisk_6_3;
		$arrl ['d_6_3'] = $d_6_3;
		$arrl ['hdr_6_3'] = $hdr_6_3;
		// echo 'hdr_6_3 '.$hdr_6_3.' <br/>';
		
		/**
		 * Deprived Lighting Access
		 * Indicator #18
		 */
		
		$prisk_7_1 = 2;
		$dum_1_7_1 = 0;
		$dum_2_7_1 = 0;
		$limye = array (
				1,
				2,
				3,
				4 
		);
		
		$dife = array ();
		// foreach($famille_ as $f){
		if ($this->lighting_access != null && in_array ( $this->lighting_access, $limye )) {
			$dum_1_7_1 = 1;
		} else {
			$dum_1_7_1 = 0;
		}
		
		if ($this->metro) {
			$dife [] = 1;
			$dife [] = 2;
			$dife [] = 3;
		}
		
		if ($this->urbain) {
			$dife [] = 1;
			$dife [] = 2;
		}
		
		if ($this->rural) {
			$dife [] = 1;
			$dife [] = 2;
		}
		
		if (in_array ( $this->energy_access, $dife )) {
			$dum_2_7_1 = 1;
		} else {
			$dum_2_7_1 = 0;
		}
		
		// metropolitan
		$s1_ele = 0.83638395;
		$s1_cook = 0.16361605;
		// Urban
		$s2_ele = 0.24063832;
		$s2_cook = 0.75936168;
		// Rural
		$s3_ele = 0.37375782;
		$s3_cook = 0.62624218;
		
		if ($this->metro) {
			$d_7_1 = $dum_1_7_1 * $s1_ele + $dum_2_7_1 * $s1_cook;
		}
		
		if ($this->urbain) {
			$d_7_1 = $dum_1_7_1 * $s2_ele + $dum_2_7_1 * $s2_cook;
		}
		
		if ($this->rural) {
			$d_7_1 = $dum_1_7_1 * $s3_ele + $dum_2_7_1 * $s3_cook;
		}
		
		$d_7_1 = $d_7_1 * 2;
		
		$hdr_7_1 = $d_7_1 / pow ( $prisk_7_1, 0.5 );
		
		$arrl ['prisk_7_1'] = $prisk_7_1;
		$arrl ['d_7_1'] = $d_7_1;
		$arrl ['hdr_7_1'] = $hdr_7_1;
		// echo 'hdr_7_1 '.$hdr_7_1.' <br/>';
		
		/*
		 * Deprived Access to water
		 * Indicator #19
		 */
		$prisk_7_2 = 2;
		$dum_1_7_2 = 0;
		$dum_2_7_2 = 0;
		
		// drinking water
		if ($this->potable_water != null && ($this->metro || $this->urbain)) {
			$dum_1_7_2 = 1;
		}
		if (($this->potable_water == 8 || $this->potable_water == 11) && ($this->metro || $this->urbain)) {
			$dum_1_7_2 = 0;
		}
		if ($this->potable_water != null && $this->rural) {
			$dum_1_7_2 = 0;
		}
		if (($this->potable_water == 4 || $this->potable_water == 5 || $this->potable_water == 7 || $this->potable_water == 2) && $this->rural) {
			$dum_1_7_2 = 1;
		}
		
		// cleaning water
		if ($this->cleaning_water != null && $this->metro) {
			$dum_2_7_2 = 1;
		}
		if (($this->cleaning_water == 12 || $this->cleaning_water == 3 || $this->cleaning_water == 6) && $this->metro) {
			$dum_2_7_2 = 0;
		}
		if ($this->urbain) {
			$dum_2_7_2 = 0;
		}
		if (($this->cleaning_water == 7 || $this->cleaning_water == 5 || $this->cleaning_water == 4 || $this->cleaning_water == 2) && $this->urbain) {
			$dum_2_7_2 = 1;
		}
		if ($this->rural) {
			$dum_2_7_2 = 0;
		}
		if (($this->cleaning_water == 2 | $this->cleaning_water == 4) && $this->rural) {
			$dum_2_7_2 = 1;
		}
		
		// Metropolitan
		$s1_dri = 0.88513678;
		$s1_now = 0.11486322;
		// Urban
		$s2_dri = 0.53738847;
		$s2_now = 0.46261153;
		// Rural
		$s3_dri = 0.64479679;
		$s3_now = 0.35520321;
		
		if ($this->urbain) {
			$d_7_2 = $dum_1_7_2 * $s2_dri + $dum_2_7_2 * $s2_now;
		}
		if ($this->rural) {
			$d_7_2 = $dum_1_7_2 * $s3_dri + $dum_2_7_2 * $s3_now;
		}
		if ($this->metro) {
			$d_7_2 = $dum_1_7_2 * $s1_dri + $dum_2_7_2 * $s1_now;
		}
		$d_7_2 = $d_7_2 * 2;
		$hdr_7_2 = $d_7_2 / pow ( $prisk_7_2, 0.5 );
		
		$arrl ['prisk_7_2'] = $prisk_7_2;
		$arrl ['d_7_2'] = $d_7_2;
		$arrl ['hdr_7_2'] = $hdr_7_2;
		
		/*
		 * Deprived sanitation conditions
		 * Indicator # 20
		 */
		$prisk_7_3 = 2;
		$dum_1_7_3 = 0;
		$dum_2_7_3 = 0;
		
		// lieu d'aisance
		if ($this->toilet_acces != null) {
			$dum_1_7_3 = 0;
		}
		if ($this->toilet_acces == 1 || $this->toilet_acces == 2) {
			$dum_1_7_3 = 1;
		}
		
		// Waste management
		if ($this->waste_evacuation != null) {
			$dum_2_7_3 = 0;
		}
		if (($this->waste_evacuation >= 2 && $this->waste_evacuation <= 4) || ($this->waste_evacuation >= 6 && $this->waste_evacuation <= 7)) {
			$dum_2_7_3 = 1;
		}
		
		// Metropolitan
		$s1_let = 0.87278542;
		$s1_was = 0.12721458;
		// Urban
		$s2_let = 0.97862221;
		$s2_was = 0.02137779;
		// Rural
		$s3_let = 0.89118072;
		$s3_was = 0.10881928;
		
		if ($this->metro) {
			$d_7_3 = $dum_1_7_3 * $s1_let + $dum_2_7_3 * $s1_was;
		}
		if ($this->urbain) {
			$d_7_3 = $dum_1_7_3 * $s2_let + $dum_2_7_3 * $s2_was;
		}
		if ($this->rural) {
			$d_7_3 = $dum_1_7_3 * $s3_let + $dum_2_7_3 * $s3_was;
		}
		$d_7_3 = $d_7_3 * 2;
		$hdr_7_3 = $d_7_3 / pow ( $prisk_7_3, 0.5 );
		
		$arrl ['prisk_7_3'] = $prisk_7_3;
		$arrl ['d_7_3'] = $d_7_3;
		$arrl ['hdr_7_3'] = $hdr_7_3;
		// echo 'hdr_7_3 '.$hdr_7_3.' <br/>';
		
		/*
		 * Step 3 : Elevate all $hdr_i_j to the power of beta
		 */
		$beta = 0.8;
		$step_3_1_1 = pow ( $hdr_1_1, $beta ); // 1
		$step_3_1_2 = pow ( $hdr_1_2, $beta ); // 2
		
		$step_3_2_1 = pow ( $hdr_2_1, $beta ); // 3
		$step_3_2_2 = pow ( $hdr_2_2, $beta ); // 4
		
		$step_3_3_1 = pow ( $hdr_3_1, $beta ); // 5
		$step_3_3_2 = pow ( $hdr_3_2, $beta ); // 6
		$step_3_3_3 = pow ( $hdr_3_3, $beta ); // 7
		$step_3_3_4 = pow ( $hdr_3_4, $beta ); // 8
		
		$step_3_4_1 = pow ( $hdr_4_1, $beta ); // 9
		$step_3_4_2 = pow ( $hdr_4_2, $beta ); // 10
		$step_3_4_3 = pow ( $hdr_4_3, $beta ); // 11
		
		$step_3_5_1 = pow ( $hdr_5_1, $beta ); // 12
		$step_3_5_2 = pow ( $hdr_5_2, $beta ); // 13
		$step_3_5_3 = pow ( $hdr_5_3, $beta ); // 14
		
		$step_3_6_1 = pow ( $hdr_6_1, $beta ); // 15
		$step_3_6_2 = pow ( $hdr_6_2, $beta ); // 16
		$step_3_6_3 = pow ( $hdr_6_3, $beta ); // 17
		
		$step_3_7_1 = pow ( $hdr_7_1, $beta ); // 18
		$step_3_7_2 = pow ( $hdr_7_2, $beta ); // 19
		$step_3_7_3 = pow ( $hdr_7_3, $beta ); // 20
		/**
		 * ** End of Step 3 ****
		 */
		
		/*
		 * Start of step 4
		 * Multiply by the weight in table A3
		 *
		 */
		
		if ($this->metro) {
			$p_v_d_1 = array (
					0.0547587772702046,
					0.0592812284929691 
			);
			$p_v_d_2 = array (
					0.0283228916516747,
					0.0442732083317981 
			);
			$p_v_d_3 = array (
					0.0779360545008914,
					0.0397786342001166,
					0.0708065340237450,
					0.0568858194128846 
			);
			$p_v_d_4 = array (
					0.0421716372884509,
					0.0800704919376153,
					0.0545837021443591 
			);
			$p_v_d_5 = array (
					0.0494011139694204,
					0.0283228916516747,
					0.0410004770389386 
			);
			$p_v_d_6 = array (
					0.0301166269775187,
					0.071489033000000,
					0.0435997240544701 
			);
			$p_v_d_7 = array (
					0.0286302533174423,
					0.0546506327690315,
					0.0439202677142728 
			);
		}
		
		if ($this->urbain) {
			$p_v_d_1 = array (
					0.0429314994696670,
					0.0902619457321613 
			);
			$p_v_d_2 = array (
					0.0245334959872276,
					0.0492349223596979 
			);
			
			$p_v_d_3 = array (
					0.0405288909750612,
					0.0560308397563253,
					0.0509274346660841,
					0.0617162252859970 
			);
			$p_v_d_4 = array (
					0.0402014273313146,
					0.0528897275638515,
					0.0410536216067491 
			);
			$p_v_d_5 = array (
					0.0262931060160781,
					0.0245334959872276,
					0.0259645459513483 
			);
			$p_v_d_6 = array (
					0.0438113000009370,
					0.0633357834303214,
					0.0479693955686851 
			);
			$p_v_d_7 = array (
					0.0769524308370264,
					0.0777533690769001,
					0.630765423973395 
			);
		}
		
		if ($this->rural) {
			$p_v_d_1 = array (
					0.0517016951729583,
					0.0604732360646597 
			);
			$p_v_d_2 = array (
					0.0262656017060029,
					0.0407405043234869 
			);
			$p_v_d_3 = array (
					0.0759261981162837,
					0.0262656017060029,
					0.0574794594116607,
					0.0565009906529475 
			);
			$p_v_d_4 = array (
					0.0543843595057296,
					0.0645926719471652,
					0.0505020632197244 
			);
			$p_v_d_5 = array (
					0.0265892524662089,
					0.0315255100127401,
					0.0286780906268562 
			);
			$p_v_d_6 = array (
					0.0592762546155926,
					0.0340540324770174,
					0.0693255265606315 
			);
			$p_v_d_7 = array (
					0.0769700142662242,
					0.0484161106198904,
					0.0603328265282168 
			);
		}
		
		$step_4_1_1 = $step_3_1_1 * $p_v_d_1 [0];
		$step_4_1_2 = $step_3_1_2 * $p_v_d_1 [1];
		
		$step_4_2_1 = $step_3_2_1 * $p_v_d_2 [0];
		$step_4_2_2 = $step_3_2_2 * $p_v_d_2 [1];
		
		$step_4_3_1 = $step_3_3_1 * $p_v_d_3 [0];
		$step_4_3_2 = $step_3_3_2 * $p_v_d_3 [1];
		$step_4_3_3 = $step_3_3_3 * $p_v_d_3 [2];
		$step_4_3_4 = $step_3_3_4 * $p_v_d_3 [3];
		
		$step_4_4_1 = $step_3_4_1 * $p_v_d_4 [0];
		$step_4_4_2 = $step_3_4_2 * $p_v_d_4 [1];
		$step_4_4_3 = $step_3_4_3 * $p_v_d_4 [2];
		
		$step_4_5_1 = $step_3_5_1 * $p_v_d_5 [0];
		$step_4_5_2 = $step_3_5_2 * $p_v_d_5 [1];
		$step_4_5_3 = $step_3_5_3 * $p_v_d_5 [2];
		
		$step_4_6_1 = $step_3_6_1 * $p_v_d_6 [0];
		$step_4_6_2 = $step_3_6_2 * $p_v_d_6 [1];
		$step_4_6_3 = $step_3_6_3 * $p_v_d_6 [2];
		
		$step_4_7_1 = $step_3_7_1 * $p_v_d_7 [0];
		$step_4_7_2 = $step_3_7_2 * $p_v_d_7 [1];
		$step_4_7_3 = $step_3_7_3 * $p_v_d_7 [2];
		
		/**
		 * *********** END OF STEP 4 ***************
		 */
		
		/**
		 * Start step 5
		 * Sum the 20 indicator obtain in step 4
		 */
		
		$sigmaStep_5 = $step_4_1_1 + $step_4_1_2 + $step_4_2_1 + $step_4_2_2 + $step_4_3_1 + $step_4_3_2 + $step_4_3_3 + $step_4_3_4 + $step_4_4_1 + $step_4_4_2 + $step_4_4_3 + $step_4_5_1 + $step_4_5_2 + $step_4_5_3 + $step_4_6_1 + $step_4_6_2 + $step_4_6_3 + $step_4_7_1 + $step_4_7_2 + $step_4_7_3;
		$step_5 = pow ( $sigmaStep_5, 1 / $beta );
		
		/**
		 * ******** END OF STEP 5 *******************
		 */
		
		/**
		 * Start step 6
		 */
		
		$dummy = array ();
		
		if ($hdr_1_1 > 0) {
			$dummy [0] = 1;
		} else {
			$dummy [0] = 0;
		}
		
		if ($hdr_1_2 > 0) {
			$dummy [1] = 1;
		} else {
			$dummy [1] = 0;
		}
		
		if ($hdr_2_1 > 0) {
			$dummy [2] = 1;
		} else {
			$dummy [2] = 0;
		}
		
		if ($hdr_2_2 > 0) {
			$dummy [3] = 1;
		} else {
			$dummy [3] = 0;
		}
		
		if ($hdr_3_1 > 0) {
			$dummy [4] = 1;
		} else {
			$dummy [4] = 0;
		}
		
		if ($hdr_3_2 > 0) {
			$dummy [5] = 1;
		} else {
			$dummy [5] = 0;
		}
		
		if ($hdr_3_3 > 0) {
			$dummy [6] = 1;
		} else {
			$dummy [6] = 0;
		}
		
		if ($hdr_3_4 > 0) {
			$dummy [7] = 1;
		} else {
			$dummy [7] = 0;
		}
		
		if ($hdr_4_1 > 0) {
			$dummy [8] = 1;
		} else {
			$dummy [8] = 0;
		}
		
		if ($hdr_4_2 > 0) {
			$dummy [9] = 1;
		} else {
			$dummy [9] = 0;
		}
		
		if ($hdr_4_3 > 0) {
			$dummy [10] = 1;
		} else {
			$dummy [10] = 0;
		}
		
		if ($hdr_5_1 > 0) {
			$dummy [11] = 1;
		} else {
			$dummy [11] = 0;
		}
		
		if ($hdr_5_2 > 0) {
			$dummy [12] = 1;
		} else {
			$dummy [12] = 0;
		}
		
		if ($hdr_5_3 > 0) {
			$dummy [13] = 1;
		} else {
			$dummy [13] = 0;
		}
		
		if ($hdr_6_1 > 0) {
			$dummy [14] = 1;
		} else {
			$dummy [14] = 0;
		}
		
		if ($hdr_6_2 > 0) {
			$dummy [15] = 1;
		} else {
			$dummy [15] = 0;
		}
		
		if ($hdr_6_3 > 0) {
			$dummy [16] = 1;
		} else {
			$dummy [16] = 0;
		}
		
		if ($hdr_7_1 > 0) {
			$dummy [17] = 1;
		} else {
			$dummy [17] = 0;
		}
		
		if ($hdr_7_2 > 0) {
			$dummy [18] = 1;
		} else {
			$dummy [18] = 0;
		}
		
		if ($hdr_7_3 > 0) {
			$dummy [19] = 1;
		} else {
			$dummy [19] = 0;
		}
		
		/**
		 * ********* END OF STEP 6 *************
		 */
		
		/**
		 * Step 7
		 * Weigth SUM
		 */
		$step7_array = array ();
		if ($this->metro) {
			$table_a_1 = array (
					0.0352677764897327,
					0.0523754043748460,
					
					0.0292093215334900,
					0.0444585435448656,
					
					0.0527735630186145,
					0.0324759606539666,
					0.0735888054525016,
					0.0368895237343493,
					
					0.0322869295613054,
					0.0354849463309216,
					0.0545603350896264,
					
					0.0739161940772515,
					0.0292093215334900,
					0.0647259721287119,
					
					0.0485497706564998,
					0.0784817944466039,
					0.0724105663023294,
					
					0.0653587710894911,
					0.0532725532934080,
					0.0347039466879947 
			);
		}
		if ($this->urbain) {
			$table_a_1 = array (
					0.0538391325301515,
					0.0853437151573815,
					
					0.0278576164952535,
					0.0593614599793962,
					
					0.0376409422854523,
					0.0457756756658074,
					0.0523652466415439,
					0.0334961534316519,
					
					0.0278576164952535,
					0.0539888346868783,
					0.0516937175755053,
					
					0.0469155962701575,
					0.0361407241240686,
					0.0300653686109902,
					
					0.0467988586280607,
					0.0829152191373995,
					0.0454000933069725,
					
					0.0837958701421488,
					0.0627205493831419,
					0.0360276094527852 
			);
		}
		if ($this->rural) {
			$table_a_1 = array (
					0.0750584321355012,
					0.0556924446773877,
					
					0.0292863667226273,
					0.0503663273721884,
					
					0.0581823883817540,
					0.0421003955173399,
					0.0438529837230281,
					0.0344171457414224,
					
					0.0292863667226273,
					0.0372683654694898,
					0.0498552209521310,
					
					0.0362947799261091,
					0.0552864471044886,
					0.0326987999677883,
					
					0.0517228537508824,
					0.0603079901156510,
					0.0799467589037649,
					
					0.0522866912924702,
					0.0679036536686293,
					0.0581855878547192 
			);
		}
		
		for($i = 0; $i < count ( $table_a_1 ); $i ++) {
			$step7_array [$i] = $dummy [$i] * $table_a_1 [$i];
		}
		
		$step7 = array_sum ( $step7_array ); // deprivation count
        //if($this->complete)
		    $arrl ['deprisum'] = $step7;
            //$arrl ['deprisum'] = null;
		
		/**
		 * ********* END OF STEP 7 *******
		 */
		
		/**
		 * Start of step 8
		 */
		
		$hdvi = $step_5 * $step7; //

        //if($this->complete)
		    $arrl ['hdvi'] = $hdvi;
        //else  $arrl ['hdvi'] = null;
		
		/**
		 * ******* END OF STEP 8 **********
		 */
		
		/*
		 * Calcul de la vulnerabilite
		 *
		 */
        //echo $hdvi.' ';
		$vulnerability = 4;
		if ($this->metro) {

			if ($hdvi >= 0.42933869) {
				$vulnerability = 1;
			} elseif ($hdvi >= 0.35082734 && $hdvi < 0.42933869) {
				$vulnerability = 2;
			} elseif ($hdvi >= 0.23433462 && $hdvi < 0.35082734) {
				$vulnerability = 3;
			}

		}
		
		if ($this->urbain) {

            if ($hdvi >= 0.42388621) {
                $vulnerability = 1;
            } elseif ($hdvi >= 0.34788468 && $hdvi < 0.42388621) {
                $vulnerability = 2;
            } elseif ($hdvi >= 0.23444542 && $hdvi < 0.34788468) {
                $vulnerability = 3;
            }
		}
		
		if ($this->rural) {

            if ($hdvi >= 0.36621299) {
                $vulnerability = 1;
            } elseif ($hdvi >= 0.25832215 && $hdvi < 0.36621299) {
                $vulnerability = 2;
            } elseif ($hdvi >= 0.18744177 && $hdvi < 0.25832215) {
                $vulnerability = 3;
            }

		}
		//if($this->complete)
		    $arrl ['vulnerability'] = $vulnerability;
        //else  $arrl ['vulnerability'] = null;
		
		if ($this->metro) {
			$w_sa_1 = 0.0616037680839676;
			$w_sa_3 = 0.0563541739621094;
			$w_sa_2 = 0.0397733974325812;
		}
		
		if ($this->urbain) {
			$w_sa_1 = 0.0721927834295678;
			$w_sa_2 = 0.0325902531436863;
			$w_sa_3 = 0.0342289366579201;
		}
		
		if ($this->rural) {
			$w_sa_2 = 0.0626075184401732;
			$w_sa_1 = 0.0347954394821190;
			$w_sa_3 = 0.0319033874278095;
		}
		
		$d_sali = (($d_5_1 / ($w_sa_1 + $w_sa_2 + $w_sa_3)) * $w_sa_1) + (($d_5_2 / ($w_sa_1 + $w_sa_2 + $w_sa_3)) * $w_sa_2) + (($d_5_3 / ($w_sa_1 + $w_sa_2 + $w_sa_3)) * $w_sa_3);
		$depr_sali = 0;
		
		if ($d_sali >= 6.5)
			$depr_sali = 1;
		//if($this->complete)
		    $arrl ['depr_sali'] = $depr_sali;
		//else  $arrl ['depr_sali'] = null;
		/*if ($this::$COUNTER == 0) {
            $table [] = array_keys ( $arrl );
        }
        $table [] = array_values ( $arrl );
        $this::$COUNTER += 1;*/
        //var_dump($this->fld);

		//update Databas
        $q = new DBQuery();
        $q->addTable($this->table);
        $q->addUpdate($this->fld['count_0_64'], $count_0_64);
        $q->addUpdate($this->fld['count_0_5'], $count_0_5);
        $q->addUpdate($this->fld['count_6_15'], $count_6_15);
        $q->addUpdate($this->fld['count_3_18'], $count_3_18);
        $q->addUpdate($this->fld['count_3_20'], $count_3_20);
        $q->addUpdate($this->fld['count_0_15'], $count_0_15);
        $q->addUpdate($this->fld['count_child_10_12'], $count_child_10_12);
        $q->addUpdate($this->fld['count_child_13_15'], $count_child_13_15);
        $q->addUpdate($this->fld['count_eldery'], $count_eldery);
        $q->addUpdate($this->fld['count_child_18_64'], $count_child_18_64);
        $q->addUpdate($this->fld['count_member_15_plus'], $count_member_15_plus);
        $q->addUpdate($this->fld['count_member_21_plus'], $count_member_21_plus);
        $q->addUpdate($this->fld['family_size'], $family_size);
        $q->addUpdate($this->fld['is_couple'], $is_couple ? 1:2);
        $q->addUpdate($this->fld['count_member_active'], $count_member_active);
        $q->addUpdate($this->fld['count_transf'], $count_transf);
        $q->addUpdate($this->fld['count_supp'], $count_supp);
        $q->addUpdate($this->fld['count_child_0_4'], $count_child_0_4);
        $q->addUpdate($this->fld['count_chronically_ill'], $count_chronically_ill);
        $q->addUpdate($this->fld['count_disabled'], $count_disabled);
        $q->addUpdate($this->fld['count_illiterate'], $count_illiterate);
        $q->addUpdate($this->fld['count_not_complete_basic_edu'], $count_not_complete_basic_edu);
        $q->addUpdate($this->fld['count_member_not_at_school'], $count_member_not_at_school);
        $q->addUpdate($this->fld['total_count_yearlag'], $total_count);
        $q->addUpdate($this->fld['count_member_inactive'], $count_member_inactive);
        $q->addUpdate($this->fld['count_member_unemployed'], $count_member_unemployed);
        $q->addUpdate($this->fld['d_4_3'], $d_4_3);
        $q->addUpdate($this->fld['step7'], $step7);
        $q->addUpdate($this->fld['hdvi'], $hdvi);
        $q->addUpdate($this->fld['vulnerability'], $vulnerability);
        $q->addUpdate($this->fld['depr_sali'], $depr_sali);
        $q->addWhere('id='.$this->id);
        //$table [] = $q->prepare();

		

	}
}

?>