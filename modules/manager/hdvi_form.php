<?php
require_once($baseDir . '/modules/outputs/outputs.class.php');
buildTableDataDemand();
/*$keys = file_get_contents($baseDir . '/modules/manager/hdvi/data.json');
$keys = json_decode($keys,true);
for ($i=0;$i<count($keys);$i++){
	$q = new DBQuery();
	$q->addTable('wform_81');
	$q->addUpdate('key', $keys[$i]['key']);
	$q->addWhere('id='.($i+1+1466+8211+5954));
	$q->exec();
}*/

//check permissions for this record
$perms = & $AppUI->acl ();

$is_superAdmin = false;
$roles = $perms->getUserRoles($AppUI->user_id);
foreach ($roles as $role){
    if($role['value']=='super_admin'){
        $is_superAdmin = true;
    }
}

$q = new DBQuery ();
$q->addTable ( "projects" );
$q->addQuery ( "project_id,project_name" );
$rows = $q->loadHashList ();

$projects = '';
$perms = & $AppUI->acl ();
$htmlpre1 = "";
foreach ( $rows as $k => $v ) {
    if(!$is_superAdmin) {
        if (!$perms->checkForm($AppUI->user_id, 'projects', $k, 'view')) {
            continue;
        }
    }
	$projects .= '<label><input type="radio" name="projectslist[]" class="projects" value="' . $k . '" onclick="loadHDVIVar(this.value)">&nbsp;&nbsp;' . $v . '</label>';
}
// $htmlpre .= '<h3 style="padding: 5px;">&emsp;&emsp;'.$AppUI->_('Projects').'</h3>';
?>
<style>
    .fldtab td {
        border-bottom: 1px solid #ddd;
    }

    .fldtab td select {
        /*width: 700px;*/
    }
</style>

<form method="POST" action="?m=manager&a=hdvi&suppressHeaders=1">
	<table class="fldtab">
		<tr>
			<td colspan="2"> <?php echo $projects;?>
			</td>
		</tr>
		<tr>
			<td>Forms households: <!-- <input type="text" readonly="readonly"
				id="household" /> <input type="text" name="householdid"
				id="householdid" /> -->
			</td>
            <td>
                <select id="household" name="householdid">
                </select>
            </td>
		</tr>
		<tr>
			<td> Sart date:</td>
            <td><input type="date" class="text spCals  hasDatepick" name="beginner" value="" size="10"></td>
		</tr>
		<tr>
			<td>Section Membre:</td>
			<td><select name="section" id="member" style="width: auto;"></select></td>
		</tr>
		<tr>
			<td>Commune:</td>
			<td><select name="commun" id="commun" style="width: auto;"></select></td>
		</tr>
		<!-- <tr>
			<td colspan="2"><h1>Variable pour les calculs</h1></td>
		</tr>
		<tr>
			<td>Milieu:</td>
			<td><select name="household[milieu]" id="milieu" class="household_fld chosen"></select></td>
		</tr>
		<tr>
			<td colspan="2"><h4>Vulnerabilite demographique</h4></td>
		</tr>
		<tr>
			<td>Variable age membre:</td>
			<td><select name="member[member_fld_age]" id="member_fld_age" class="member_fld chosen"></select></td>
		</tr>
		<tr>
			<td>Lien de parente:</td>
			<td><select name="member[member_fld_linkparent]"  id="member_fld_linkparent"
				class="member_fld chosen"></select></td>
		</tr>
		<tr>
			<td colspan="2"><h4>Sante</h4></td>
		</tr>

		<tr>

			<td>Maladie chronique membre:</td>
			<td><select name="member[member_fld_lsickness]"  id="member_fld_lsickness"
				class="member_fld chosen"></select></td>
		</tr>

		<tr>

			<td>Difficulte avec la parole:</td>
			<td><select name="member[member_prob_speak]" id="member_prob_speak"
				class="member_fld chosen"></select></td>

		</tr>

		<tr>

			<td>Difficulte d'ecoute:</td>
			<td><select name="member[member_prob_hear]" id="member_prob_hear" class="member_fld chosen"></select></td>

		</tr>

		<tr>

			<td>Difficulte avec les autosoins:</td>
			<td><select name="member[member_prob_autooins]" id="member_prob_autooins"
				class="member_fld chosen"></select></td>

		</tr>
		<tr>
			<td>Difficulte de voir:</td>
			<td><select name="member[member_prob_eye]" id="member_prob_eye" class="member_fld chosen"></select></td>
		</tr>
		<tr>
			<td colspan="2"><h4>Education</h4></td>
		</tr>
		<tr>
			<td>Lire:</td>
			<td><select name="member[member_fld_read]" id="member_fld_read" class="member_fld chosen"></select></td>
		</tr>
		<tr>
			<td>Ecrire:</td>
			<td><select name="member[member_fld_write]" id="member_fld_write" class="member_fld chosen"></select></td>
		</tr>
		<tr>
			<td>Niveau d'etude actuel:</td>
			<td><select name="member[member_fld_act_edu]" id="member_fld_act_edu"
				class="member_fld chosen"></select></td>
		</tr>
		<tr>
			<td>Plus haut niveau d'etude:</td>
			<td><select name="member[member_fld_level_edu]" id="member_fld_level_edu"
				class="member_fld chosen"></select></td>
		</tr>
		<tr>
			<td>Derniere frequentation a l'ecole:</td>
			<td><select name="member[member_fld_lst_scho_12]" id="member_fld_lst_scho_12"
				class="member_fld chosen"></select></td>
		</tr>
		<tr>
			<td colspan="2"><h4>Conditions de travail</h4></td>
		</tr>
		<tr>
			<td>Activite economique:</td>
			<td><select name="member[member_fld_eco_active]" id="member_fld_eco_active"
				class="member_fld chosen"></select></td>
		</tr>
		<tr>
			<td colspan="2"><h4>Securite alimentaire</h4></td>
		</tr>
		<tr>
			<td>Absence de nourriture:</td>
			<td><select name="household[absence_of_food]" id="absence_of_food"
				class="household_fld chosen"></select></td>
		</tr>
		<tr>
			<td>Coucher en ayant faim:</td>
			<td><select name="household[hunger]" id="hunger" class="household_fld chosen"></select></td>
		</tr>
		<tr>
			<td>Jeuner une journee entiere:</td>
			<td><select name="household[restricted_consumption]" id="restricted_consumption"
				class="household_fld chosen"></select></td>
		</tr>
		<tr>
			<td>Transfert de fonds:</td>
			<td><select name="member[member_transf]" id="member_transf" class="member_fld chosen"></select></td>
		</tr>
		<tr>
			<td>Support financiers:</td>
			<td><select name="member[member_supp]" id="member_supp" class="member_fld chosen"></select></td>
		</tr>
		<tr>
			<td>Materiaux du plancher:</td>
			<td><select name="household[materiau_floor]" id="materiau_floor"
				class="household_fld chosen"></select></td>
		</tr>
		<tr>
			<td>Materiaux des murs:</td>
			<td><select name="household[materiau_wall]" id="materiau_wall"
				class="household_fld chosen"></select></td>
		</tr>
		<tr>
			<td>Materiaux du toit:</td>
			<td><select name="household[materiau_roof]" id="materiau_roof"
				class="household_fld chosen"></select></td>
		</tr>
		<tr>
			<td>Nombre de pieces:</td>
			<td><select name="household[number_of_romm]" id="number_of_romm"
				class="household_fld chosen"></select></td>
		</tr>
		<tr>
			<td>Sources de lumieres:</td>
			<td><select name="household[lighting_access]" id="lighting_access"
				class="household_fld chosen"></select></td>
		</tr>
		<tr>
			<td>Sources d'energie:</td>
			<td><select name="household[energy_access]" id="energy_access"
				class="household_fld chosen"></select></td>
		</tr>
		<tr>
			<td>Eau a boire:</td>
			<td><select name="household[potable_water]" id="potable_water"
				class="household_fld chosen"></select></td>
		</tr>
		<tr>
			<td>Eau pour nettoyage:</td>
			<td><select name="household[cleaning_water]" id="cleaning_water"
				class="household_fld chosen"></select></td>
		</tr>
		<tr>
			<td>Acces a des toilettes:</td>
			<td><select name="household[toilet_acces]" id="toilet_acces"
				class="household_fld chosen"></select></td>
		</tr>
		<tr>
			<td>Evacuation de dechets:</td>
			<td><select name="household[waste_evacuation]" id="waste_evacuation"
				class="household_fld chosen"></select></td>
		</tr>
		<tr>
			<td>1.1.- Indicateur sur la composition demographique :</td>
			<td><select name="household[hdr_1_1]" id="hdr_1_1"
				class="household_fld chosen"></select></td>
		</tr>
		<tr>
			<td>1.2.- Indicateur sur les enfants moins de 5 ans :</td>
			<td><select name="household[hdr_1_2]" id="hdr_1_2"
				class="household_fld chosen"></select></td>
		</tr>
		<tr>
			<td>2.1.- Indicateur sur les maladies chroniques :</td>
			<td><select name="household[hdr_2_1]" id="hdr_2_1"
				class="household_fld chosen"></select></td>
		</tr>
		<tr>
			<td>2.2.- Indicateur sur les handicapes :</td>
			<td><select name="household[hdr_2_2]" id="hdr_2_2"
				class="household_fld chosen"></select></td>
		</tr><tr>
			<td>3.1.- Indicateur sur l'alphabetisme:</td>
			<td><select name="household[hdr_3_1]" id="hdr_3_1"
				class="household_fld chosen"></select></td>
		</tr><tr>
			<td>3.2.- Indicateur sur l'abscence d'education primaire:</td>
			<td><select name="household[hdr_3_2]" id="hdr_3_2"
				class="household_fld chosen"></select></td>
		</tr><tr>
			<td>3.3.- Indicateur sur la non scolarisation:</td>
			<td><select name="household[hdr_3_3]" id="hdr_3_3"
				class="household_fld chosen"></select></td>
		</tr><tr>
			<td>3.4.- Indicateur sur le retard scolaire:</td>
			<td><select name="household[hdr_3_4]" id="hdr_3_4"
				class="household_fld chosen"></select></td>
		</tr><tr>
			<td>4.1.- Indicateur sur l'inactivite:</td>
			<td><select name="household[hdr_4_1]" id="hdr_4_1"
				class="household_fld chosen"></select></td>
		</tr><tr>
			<td>4.2.- Indicateur sur chomage:</td>
			<td><select name="household[hdr_4_2]" id="hdr_4_2"
				class="household_fld chosen"></select></td>
		</tr><tr>
			<td>4.3.- Indicateur sur le travail des enfants:</td>
			<td><select name="household[hdr_4_3]" id="hdr_4_3"
				class="household_fld chosen"></select></td>
		</tr><tr>
			<td>5.1.- Indicateur sur la manque de nourriture:</td>
			<td><select name="household[hdr_5_1]" id="hdr_5_1"
				class="household_fld chosen"></select></td>
		</tr><tr>
			<td>5.2.- Indicateur sur la faim:</td>
			<td><select name="household[hdr_5_2]" id="hdr_5_2"
				class="household_fld chosen"></select></td>
		</tr><tr>
			<td>5.3.- Indicateur sur la consommation reduite:</td>
			<td><select name="household[hdr_5_3]" id="hdr_5_3"
				class="household_fld chosen"></select></td>
		</tr><tr>
			<td>6.1.- Indicateur sur les sources d'argent:</td>
			<td><select name="household[hdr_6_1]" id="hdr_6_1"
				class="household_fld chosen"></select></td>
		</tr><tr>
			<td>6.2.- Indicateur sur les conditions de logement:</td>
			<td><select name="household[hdr_6_2]" id="hdr_6_2"
				class="household_fld chosen"></select></td>
		</tr><tr>
			<td>6.3.- Indicateur sur le surpeuplement:</td>
			<td><select name="household[hdr_6_3]" id="hdr_6_3"
				class="household_fld chosen"></select></td>
		</tr><tr>
			<td>7.1.- Indicateur sur les moyens d'eclairage:</td>
			<td><select name="household[hdr_7_1]" id="hdr_7_1"
				class="household_fld chosen"></select></td>
		</tr><tr>
			<td>7.2.- Indicateur sur l'acces a l'eau:</td>
			<td><select name="household[hdr_7_2]" id="hdr_7_2"
				class="household_fld chosen"></select></td>
		</tr><tr>
			<td>7.3.- Indicateur sur les conditions sanitaires:</td>
			<td><select name="household[hdr_7_3]" id="hdr_7_3"
				class="household_fld chosen"></select></td>
		</tr>
		<tr>
			<td>Deprisum:</td>
			<td><select name="household[step7]" id="step7"
				class="household_fld chosen"></select></td>
		</tr>
		<tr>
			<td>HDVI:</td>
			<td><select name="household[hdvi]" id="hdvi"
				class="household_fld chosen"></select></td>
		</tr>
		<tr>
			<td>Groupe de Vulnerabilite:</td>
			<td><select name="household[vulnerability]" id="vulnerability"
				class="household_fld chosen"></select></td>
		</tr>complete
		<tr>
			<td>Securite alimentaire:</td>
			<td><select name="household[depr_sali]" id="depr_sali"
				class="household_fld chosen"></select></td>
		</tr>
		<tr>
			<td>Information complete:</td>
			<td><select name="household[complete]" id="complete"
				class="household_fld chosen"></select></td>
		</tr> -->
		<tr>
			<td colspan="2">
				<input type="submit"  class="button ce pi ahr"/>
			</td>
		</tr>
	</table>
</form>
