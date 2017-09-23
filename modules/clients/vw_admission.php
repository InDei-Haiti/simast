<?php
global $AppUI, $client_id, $obj;

require_once $AppUI->getModuleClass('admission');
require_once $AppUI->getModuleClass('social');
require_once $AppUI->getModuleClass('counsellinginfo');

$boolTypes = dPgetSysVal('YesNo');
$genderTypes = dPgetSysVal('GenderType');
$immunizationStatus = dPgetSysVal('ImmunizationType');
$hivStatus = dPgetSysVal('HIVStatus');
$malnutritionType = dPgetSysVal('MalnutritionType');
$educProgressType = dPgetSysVal('EducationProgressType');
$educationLevels = dPgetSysVal('EducationLevel');
$riskLevels  = dPgetSysVal('RiskLevel');
$childEducationLevels = dPgetSysVal('ChildEducationLevel');
$reasonsNoSchool = dPgetSysVal('AdmissionReasonsNotAttendingSchool');
$motorType = dPgetSysVal('MotorAbilityType');
$dehydrationType = dPgetSysVal('MalnutritionType');
$lymphType = dPgetSysVal('LymphType');
$throatType = dPgetSysVal('ThroatType');
$earType = dPgetSysVal('EarType');
$teethType = dPgetSysVal('TeethType');
$percussionType = dPgetSysVal('PercussionType');
$breathType = dPgetSysVal('BreathSoundsType');
$soundsType = dPgetSysVal('SoundsType');
$apexType = dPgetSysVal('NormalDisplacedType');
$precordialType = dPgetSysVal('NormalIncreasedType');
$femoralType = dPgetSysVal('FemoralPulseType');
$heartSoundType = dPgetSysVal('HeartType');
$toneType = dPgetSysVal('NormalIncReducedType');
$tendonLegsType = dPgetSysVal('NormalIncReducedType');
$tendonArmsType = dPgetSysVal('NormalIncReducedType');
$palpableType = dPgetSysVal('NormalIncReducedType');
$directionType = dPgetSysVal('NormalIncReducedType');
$conditionType = dPgetSysVal('ConditionType');
$locationOptions = dPgetSysVal('LocationOptions');

$q  = new DBQuery;
$q->addTable('clinic_location');
$q->addQuery('clinic_location.clinic_location_id, clinic_location.clinic_location');
$locationOptions = $q->loadHashList();

$examinationType = dPgetSysVal('ExaminationType');

$maritalStatus = dPgetSysVal('MaritalStatus');
$educationLevels = dPgetSysVal('EducationLevel');
$caregiverStatus = dPgetSysVal('CaregiverStatus');
$caregiverHealthStatus = dPgetSysVal('CaregiverHealthStatus');
$incomeLevels = dPgetSysVal('IncomeLevels');
$employmentType = dPgetSysVal('EmploymentType');


$qr = new DBQuery();
$qr->addTable('admission_caregivers');
$qr->addWhere('client_id='.$obj->client_id);
$qr->setLimit(1);

$parents = array('father'=>array(),'mother'=>array());
$carez= array('primary'=>array(),'secondary'=>array());

$careind=array('father'=>7,'mother'=>12,'primary'=>17,'secondary'=>22);

$q= new DBQuery();
$q->addTable('counselling_info');
$q->addQuery('counselling_admission_date');
$q->addWhere('counselling_client_id="'.$client_id.'"');
$q->setLimit(1);
$coun_date=$q->loadResult();

//get date format
$df = $AppUI->getPref('SHDATEFORMAT');

// collect all the users with CHW
$q  = new DBQuery;
$q->addTable('contacts','con');
$q->leftJoin('users','u', 'u.user_contact = con.contact_id');
$q->addQuery('contact_id');
$q->addQuery('CONCAT_WS(", ",contact_last_name,contact_first_name)');
$q->addWhere('contact_type="10"');
$q->addOrder('contact_last_name');
$chws = $q->loadHashList();

$q = new DBQuery;
$q->addTable('admission_info');
$q->addQuery ('admission_info.*');
$q->addWhere('admission_info.admission_client_id = '.$client_id);
$s='';
$sql= $q->prepare();
//print_r($sql);
if (!($rows=$q->loadList()))
{
	echo $AppUI->_("No data available") . '<br />'.  $AppUI->getMsg();
	$title="add admission record...";
	$url = "./index.php?m=admission&a=addedit&client_id=$client_id";
	$s .= '<tr><td colspan="6" align="left" valign="top">';
	$s .= '<input type="button" class=button value="'.$AppUI->_( $title ).'" onClick="javascript:window.location=\''.$url.'\'">';
	$s .= '</td></tr>';
	echo $s;

}
else
{
	$title="Edit admission record...";

?>
<table cellpadding="4" cellspacing="0" width="100%" class="std">


<?php

foreach ($rows as $row){
	$url = "./index.php?m=admission&a=addedit&client_id=$client_id&admission_id=".$row["admission_id"];
	$obj = new CAdmissionRecord();
	$obj->load($row["admission_id"]);
	$entry_date = intval( $obj->admission_entry_date ) ? new CDate( $obj->admission_entry_date ) : new CDate($coun_date);
	//load family members
	$q = new DBQuery();
	$q->addTable("household_info");
	$q->addQuery("household_info.*");
	// $q->addWhere("household_info.household_admission_id = " . $row["admission_id"]);
	$q->addWhere("household_info.household_client_id = \"" . $client_id.'"');
	$housemembers = $q->loadList();
	//load social and counselling info

	if (!empty($client_id))	{
		$q  = new DBQuery;
		$q->addTable('social_visit');
		$q->addQuery('social_visit.*');
		$q->addWhere('social_visit.social_client_id = "'.$client_id.'"');
		$sql = $q->prepare();
		//var_dump($sql);
		$q->clear();
		$socialObj = new CSocialVisit();
		db_loadObject( $sql, $socialObj );
	}

	if (!empty($client_id))	{
		$q  = new DBQuery;
		$q->addTable('counselling_info');
		$q->addQuery('counselling_info.*');
		$q->addWhere('counselling_info.counselling_client_id = '.$client_id);
		$sql = $q->prepare();
		//var_dump($sql);
		$q->clear();
		$counsellingObj = new CCounsellingInfo();
		db_loadObject( $sql, $counsellingObj );
	}

	foreach ( $parents as $ctype => $carr ) {
		if ($obj->{'admission_' . $ctype} > 0) {
			$q1 = clone $qr;
			$q1->addWhere ( 'role="' . $ctype . '"' );
			$q1->addWhere ( 'id=' . $obj->{'admission_' . $ctype} );
			$tt = $q1->loadList ();
			$parents [$ctype] = $tt[0];
			unset ( $q1,$tt );
		}
	}

	foreach ($carez  as $ctype => $carr) {
		$brief=substr($ctype,0,3);
		if($obj->{'admission_caregiver_'.$brief} > 0){
			$q1 = clone $qr;
			//$q1->addWhere('role="'.$brief.'"');
			$q1->addWhere('id='.$obj->{'admission_caregiver_'.$brief});
			$tt=$q1->loadList();
			$carez[$ctype]=$tt[0];
			unset($q1,$tt);
		}
	}

	$s .= '<tr><td colspan="6" align="left" valign="top">';
	//$s .= '<input type="button" class=button value="'.$AppUI->_( $title ).'" onClick="javascript:window.location=\''.$url.'\'">';
	$s .= '<a href="'.$url . '">'. $AppUI->_( $title ).'</a>';
	$s .= '</td></tr>';
	echo $s;

?>
<tr>
	<td colspan="2" valign="top">
	<table border="0" cellpadding="4" cellspacing="1">
		<tr>
				<td colspan="2" align="left"><strong><?php echo $AppUI->_('Details'); ?><br /></strong>
				<hr width="500" align="left" size=1 />
			</td>
		</tr>
		<tr>
				<td align="left" nowrap><?php echo $AppUI->_('Date of admission');?>: </td>
				<td align="left" class="hilite">
					<?php echo $entry_date ? $entry_date->format( $df ) : "" ;?>
				</td>
		</tr>
			<tr>
				<td align="left" nowrap>3a.<?php echo $AppUI->_('School Level');?>:</td>
				<td align="left" class="hilite">
	   <?php echo $childEducationLevels[$obj->admission_school_level]; ?>
		 </td>
			</tr>
			<tr>
				<td align="left" nowrap>3b...<?php echo $AppUI->_('If not attending,why');?>:</td>
				<td align="left" class="hilite">
	    <?php echo $reasonsNoSchool[@$obj->admission_reason_not_attending];?>
		 </td>
			</tr>
			<tr>
				<td align="left" nowrap>3c...<?php echo $AppUI->_('Other reason');?>:</td>
				<td align="left" class="hilite">
	    <?php echo @$obj->admission_reason_not_attending_notes;?>
		 </td>
			</tr>
			<tr>
				<td align="left" nowrap>4.<?php echo $AppUI->_('Current Residence');?>:</td>
				<td align="left" class="hilite">
	    <?php echo @$obj->admission_residence;?>
		 </td>

			</tr>
			<tr>
				<td align="left">5a.<?php echo $AppUI->_('Location');?>:</td>
				<td nowrap="nowrap" align="left" class="hilite">
					<?php echo $locationOptions[@$obj->admission_location];?>
				</td>
			</tr>
			<tr>
				<td align="left">5b.<?php echo $AppUI->_('CHW');?>:</td>
				<td nowrap="nowrap" align="left" class="hilite">
					<?php echo $chws[@$obj->admission_chw];?>
				</td>
			</tr>
			
			<tr>
				<td align="left" nowrap="nowrap"><?php echo $AppUI->_('Rural Home');?>:</td>
			</tr>
			<tr>
				<td align="left" nowrap="nowrap">6a...<?php echo $AppUI->_('Province');?>:</td>
				<td align="left" class="hilite">
	    <?php echo @$obj->admission_province;?>
		 </td>
			</tr>
			<tr>
				<td align="left" nowrap="nowrap">6b...<?php echo $AppUI->_('District');?>:</td>
				<td align="left" class="hilite">
	    <?php echo @$obj->admission_district;?>
		 </td>
			</tr>
			<tr>
				<td align="left" nowrap="nowrap">6c...<?php echo $AppUI->_('Village');?>:</td>
				<td align="left" class="hilite">
	    <?php echo @$obj->admission_village;?>
		 </td>
			</tr>
			<tr>
				<td colspan="2" align="left"><strong><?php echo $AppUI->_('Caregiver Information'); ?><br />
				</strong>
				<hr width="500" align="left" size=1 />
				</td>
			</tr>
	 <?php 

	 foreach ($parents as $ptype =>$pinfo) {
	 	$vst=$careind[$ptype];
	 ?>
	 
	   <tr>
				<td align="left" nowrap="nowrap"><b><?php echo $AppUI->_(ucfirst($ptype));?>:</b></td>
			</tr>
			<tr>
				<td align="left" nowrap="nowrap"><?php echo ($vst+0);?>a...<?php echo $AppUI->_('First Name');?>:</td>
				<td align="left" class="hilite"><?php echo @$pinfo['fname'];?></td>
			</tr>
			<tr>
				<td align="left"><?php echo ($vst+0);?>b...<?php echo $AppUI->_('Last Name');?>:</td>
				<td align="left" class="hilite"><?php echo @$pinfo['lname'];?></td>
			</tr>
			<tr>
				<td align="left"><?php echo ($vst+0);?>c...<?php echo $AppUI->_('Age');?>:</td>
				<td align="left" class="hilite"><?php echo @$pinfo['age'];?></td>
			</tr>
			<tr>
				<td align="left"><?php echo ($vst+1);?>a...<?php echo $AppUI->_('Status');?>:</td>
				<td align="left" class="hilite"><?php echo $caregiverStatus[$pinfo['status']]/*$caregiverStatus[$obj->{'admission_'.$ptype.'_status'}]*/; ?>
		 </td>
			</tr>
			<tr>
				<td align="left"><?php echo ($vst+1);?>b.&nbsp;</td>
				<td align="left" class="hilite"><?php echo $caregiverHealthStatus[$pinfo['health_status']]; ?>
		 </td>
			</tr>

			<tr>
				<td align="left"><?php echo ($vst+1);?>c...<?php echo $AppUI->_('Raising Child');?>:</td>
				<td align="left" class="hilite"><?php echo $boolTypes[$obj->{'admission_'.$ptype.'_raising_child'}]; ?>
		 </td>
			</tr>
			<tr>
				<td align="left"><?php echo ($vst+1);?>d...<?php echo $AppUI->_('Marital status');?>:</td>
				<td align="left" class="hilite"><?php echo $maritalStatus[$pinfo['marital_status']]; ?>
		 </td>
			</tr>
			</tr>
			<tr>
				<td align="left"><?php echo ($vst+2);?>...<?php echo $AppUI->_('Education Level');?>:</td>
				<td align="left" class="hilite">
		   <?php echo $educationLevels[$pinfo['educ_level']]; ?>
		   </td>
			</tr>
			<tr>
				<td align="left"><?php echo ($vst+3);?>...<?php echo $AppUI->_('Employment');?>:</td>
				<td align="left" class="hilite">
		   <?php echo $employmentType[$pinfo['employment']]; ?>
		   </td>
			</tr>
			<!--  <tr>
		   <td align="left">...<?php echo $AppUI->_('Monthly Income');?>:</td>
		   <td align="left" class="hilite">
		   <?php// echo $incomeLevels[$pinfo['income']]; ?>
		   </td>
		 </tr> -->
			<tr>
				<td align="left"><?php echo $AppUI->_('Other Details');?>:</td>
			</tr>
			<tr>
				<td align="left"><?php echo ($vst+4);?>a...<?php echo $AppUI->_('ID #');?>:</td>
				<td align="left" class="hilite"><?php echo @$pinfo['idno'];?></td>
			</tr>
			<tr>
				<td align="left"><?php echo ($vst+4);?>b...<?php echo $AppUI->_('Mobile #');?>:</td>
				<td align="left" class="hilite"><?php echo @$pinfo['mobile'];?></td>
			</tr>
		<?php 
	 }
		?>	  
	  <?php 
	  foreach ($carez as $ctype =>$cinfo){
	  	$vst=$careind[$ctype];
	  	$brief=substr($ctype,0,3);
	  ?>
	  <tr>
				<td align="left" nowrap valign="top"><b><?php echo $AppUI->_(ucfirst($ctype).' Caregiver');?>:</b></td>
			</tr>
			<tr>
				<td align="left"><?php echo ($vst+0);?>a...<?php echo $AppUI->_('First Name');?>:</td>
				<td align="left" class="hilite"><?php echo @$cinfo['fname'];?></td>
			</tr>
			<tr>
				<td align="left"><?php echo ($vst+0);?>b...<?php echo $AppUI->_('Last Name');?>:</td>
				<td align="left" class="hilite"><?php echo @$cinfo['lname'];?></td>
			</tr>
			<tr>
				<td align="left"><?php echo ($vst+0);?>c...<?php echo $AppUI->_('Age');?>:</td>
				<td align="left" class="hilite"><?php echo @$cinfo['age'];?></td>
			</tr>
			<tr>
				<td align="left"><?php echo ($vst+1);?>a...<?php echo $AppUI->_('Status');?>:</td>
				<td align="left" class="hilite">
					<?php echo $caregiverStatus[$cinfo['status']]; ?>
		 		</td>
			</tr>
			<tr>
				<td align="left"><?php echo ($vst+1);?>b...<?php echo $AppUI->_('Health Status');?>:</td>
				<td align="left" class="hilite">
					<?php echo $caregiverHealthStatus[$cinfo['health_status']]; ?>
		 		</td>
			</tr>
			<tr>
				<td align="left"><?php echo ($vst+1);?>c...<?php echo $AppUI->_('Relationship to child');?>:</td>
				<td align="left" class="hilite"><?php echo @$cinfo['relationship'];?>
		 </td>
			</tr>
			<tr>
				<td align="left"><?php echo ($vst+1);?>d...<?php echo $AppUI->_('Marital status');?>:</td>
				<td align="left" class="hilite"><?php echo $maritalStatus[$cinfo['marital_status']]; ?>
		 </td>
			</tr>
			<tr>
				<td align="left"><?php echo ($vst+2);?>...<?php echo $AppUI->_('Education Level');?>:</td>

				<td align="left" class="hilite">
		   <?php echo $educationLevels[$cinfo['educ_level']]; ?>
		   </td>
			</tr>
			<tr>
				<td align="left"><?php echo ($vst+3);?>...<?php echo $AppUI->_('Employment');?>:</td>
				<td align="left" class="hilite">
		   <?php echo $employmentType[$cinfo['employment']]; ?>
		   </td>
			</tr>
			<!--  <tr>
		   <td align="left">...<?php echo $AppUI->_('Monthly Income');?>:</td>
		   <td align="left" class="hilite">
		   <?php echo $incomeLevels[$cinfo['income']]; ?>
		   </td>
		 </tr>-->
			<tr>
				<td align="left"><?php echo $AppUI->_('Other Details');?>:</td>
			</tr>
			<tr>
				<td align="left"><?php echo ($vst+4);?>a...<?php echo $AppUI->_('ID #');?>:</td>
				<td align="left" class="hilite"><?php echo @$cinfo['idno'];?></td>
			</tr>
			<tr>
				<td align="left"><?php echo ($vst+4);?>b...<?php echo $AppUI->_('Mobile #');?>:</td>
				<td align="left" class="hilite"><?php echo @$cinfo['mobile'];?></td>
			</tr>			
			<tr>
				<td align="left"><?php echo ($vst + 4);?>c...<?php echo $AppUI->_('Residence');?>:</td>
				<td align="left" class="hilite"><?php echo @$obj->{"admission_caregiver_".$ctype."_residence"};?></td>				
			</tr>
			
		<?php 
	  }
		?>
	 <tr>
				<td align="left">...<?php echo $AppUI->_('Total Family Income');?>:</td>
				<td class="hilite">
			  <?php echo $incomeLevels[$obj->admission_family_income]; ?>
			  </td>
			</tr>
			<tr>
				<td align="left" nowrap valign="top"><?php echo $AppUI->_('Other Household Members');?>:</td>
				<td align="left">
				<table class="tbl ortho">
					<tr>
						<th>a.<?php echo $AppUI->_('Name');?></th>
						<th>b.<?php echo $AppUI->_('Year of Birth');?></th>
						<th>c.<?php echo $AppUI->_('Gender');?></th>
						<th>d.<?php echo $AppUI->_('Relationship to child');?></th>
						<th>e.<?php echo $AppUI->_('If registered, Adm #');?></th>
						<!-- <th><?php echo $AppUI->_('Comments');?></th> -->
					</tr>
		 <?php foreach ($housemembers as $icount => $housemember)
		 {
		 ?>
		 <tr>
						<td><?php echo $housemember["household_name"];?></td>
						<td><?php echo $housemember["household_yob"];?></td>
						<td><?php echo $genderTypes[$housemember["household_gender"]];?></td>
						<td><?php echo $housemember["household_relationship"];?></td>
						<td><?php echo $housemember["household_notes"];?></td>
						<!-- <td><?php echo $housemember["household_custom"];?></td> -->
					</tr>
		 <?php } ?>
		 </table>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="left"><strong><?php echo $AppUI->_('Social Worker Assessment'); ?><br />
				</strong>
				<hr width="500" align="left" size=1 />
				</td>
			</tr>
			<tr>
				<td align="left" valign="top">31b.<?php echo $AppUI->_('Risk Level');?>:</td>
				<td align="left" valign="top" class="hilite">
			<?php echo $riskLevels[$obj->admission_risk_level];?>
 			</td>
			</tr>
			<tr>
				<td align="left" valign="top"><?php echo $AppUI->_('Enclosures');?>:</td>
			</tr>
			<tr>
				<td align="left" valign="top">32a.<?php echo $AppUI->_('Birth certificate #');?>:</td>
				<td align="left" valign="top" class="hilite">
			<?php echo $obj->admission_birth_cert;?>
 			</td>
			</tr>
			<tr>
				<td align="left" valign="top">32b.<?php echo $AppUI->_('ID #');?>:</td>
				<td align="left" valign="top" class="hilite">
			<?php echo $obj->admission_id_no;?>
 			</td>
			</tr>
			<tr>
				<td align="left" valign="top">32c.<?php echo $AppUI->_('NHF #');?>:</td>
				<td align="left" valign="top" class="hilite">
			<?php echo $obj->admission_nhf;?>
 			</td>
			</tr>
			<tr>
				<td align="left" valign="top">32d.<?php echo $AppUI->_('Med Records');?>:</td>
				<td align="left" valign="top" class="hilite">
			<?php echo $boolTypes[$obj->admission_med_recs];?>
 			</td>
			</tr>
			<tr>
				<td align="left" valign="top">32e.<?php echo $AppUI->_('Immun Card #');?>:</td>
				<td align="left" valign="top" class="hilite">
			<?php echo $obj->admission_immun;?>
 			</td>
			</tr>
			<tr>
				<td align="left" valign="top">32f.<?php echo $AppUI->_('Death Cert #');?>:</td>
				<td align="left" valign="top" class="hilite">
			<?php echo $obj->admission_death_cert;?>
 			</td>
			</tr>
			<tr>
				<td align="left" valign="top">32g...<?php echo $AppUI->_('Other');?>:</td>
				<td align="left" valign="top" class="hilite">
			<?php echo $obj->admission_enclosures_other;?>
 			</td>
			</tr>
			<tr>
				<td align="left" valign="top" nowrap>33.<?php echo $AppUI->_('Social worker assessment');?>:</td>
				<td align="left" valign="top" class="hilite">
		<?php echo wordwrap( str_replace( chr(10), "<br />", $obj->admission_risk_level_description), 75,"<br />", true);?>
		</td>

			</tr>
			</tr>
			<td align='left'>
		<?php
		require_once("./classes/CustomFields.class.php");
		$custom_fields = New CustomFields( $m, $a, $obj->admission_id, "edit" );
		$custom_fields->printHTML();
		?>		
	</td>
			</tr>

			</td>
			</tr>
		</table>
	
	</tr>
<?php
}
}
?>

</table>
