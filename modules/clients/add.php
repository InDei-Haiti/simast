<?php

require_once($AppUI->getModuleClass("counsellinginfo"));
require_once($AppUI->getModuleClass("social"));
require_once($AppUI->getModuleClass("admission"));

$client_id = intval( dPgetParam( $_GET, "client_id", 0 ));
$client_type = intval( dPgetParam( $_GET, "client_type", 3 ));


if 	($contact_unique_update == 0)
  $contact_unique_update = uniqid("");

$perms = & $AppUI->acl();

if ($client_id)
  $canEdit = $perms->checkModuleItem( $m, "edit", $client_id );
else
  $canEdit = $perms->checkModule($m, "add");

if (!$canEdit)
{
  $AppUI->redirect("m=public&a=access_denied");
}



$q  = new DBQuery;
$q->addTable('clients');
$q->addQuery('clients.*');
$q->addWhere('clients.client_id = '.$client_id);
$sql = $q->prepare();
$q->clear();

$obj = new CClient();

if (!db_loadObject($sql, $obj) && ($client_id > 0))
{
  	$AppUI->setMsg( "invalidID", UI_MSG_ERROR, true );
	$AppUI->redirect();
}



//load cities
$citiesArray = arrayMerge(array(-1=>'-Select City-'), dPgetSysVal('ClientCities'));
//load centers

$q->clear();
$q->addTable('clinics', 'c');
$q->addQuery('c.clinic_id, c.clinic_name');
$q->addOrder('c.clinic_name');

$clinics = arrayMerge(array(0=> '-Select Center -'),$q->loadHashList());

$boolTypes = dPgetSysVal('YesNo');
$boolTypesND = dPgetSysVal('YesNoND');
$birthPlaces = dPgetSysVal('BirthPlaceType');
$birthTypes = dPgetSysVal('BirthType');
$ageTypes = dPgetSysVal('AgeType');
$awareStages = dPgetSysVal('StatusAwareType');

$admissionObj = new CAdmissionRecord();

//load counselling object
if ($client_id > 0){
    $q = new DBQuery();
	$q->addTable("counselling_info");
	$q->addQuery("counselling_info.*");
	$q->addWhere("counselling_info.counselling_client_id = ". $client_id);
	$sql = $q->prepare();

	db_loadObject($sql, $counsellingObj);
	
	$q = new DBQuery();
	$q->addTable("admission_info");
	$q->addQuery("admission_info.*");
	$q->addWhere("admission_info.admission_client_id = ". $client_id);
	$sql = $q->prepare();

	db_loadObject($sql, $admissionObj);
}

//load social visit record
$socialObj = new CSocialVisit();

if ($client_id > 0)
{
    $q = new DBQuery();
	$q->addTable("social_visit");
	$q->addQuery("social_visit.*");
	$q->addWhere("social_visit.social_client_id = ". $client_id);
	$q->addOrder("social_entry_date", "desc");
	$sql = $q->prepare();

	db_loadObject($sql, $socialObj);
}

//load company types
$types = dPgetSysVal('ClientStatus');
$type = $types[$client_type];
//load staff for officer fields
// collect all the users for the nutritionist list
$q  = new DBQuery;
$q->addTable('contacts','con');
$q->leftJoin('users','u', 'u.user_contact = con.contact_id');
$q->addQuery('contact_id');
$q->addQuery('CONCAT_WS(", ",contact_last_name,contact_first_name)');
$q->addOrder('contact_last_name');
$q->addWhere('contact_active="1"');
$owners = $q->loadHashList();

$owners =  $owners;

//load status stuff
$status = arrayMerge(array(-1=>''), dPgetSysVal('ClientStatus'));
//load priority stuff
$priority = arrayMerge(array(-1=>'-Select Current Client Priority-'), dPgetSysVal('ClientPriority'));

//$ttl = "$type :: ";
$ttl .= $client_id > 0 ? "Edit Intake & PCR" : "New Intake & PCR";

$titleBlock = new CTitleBlock( $ttl, '', $m, "$m.$a" );
$titleBlock->addCrumb( "?m=clients", "Clients" );
$titleBlock->addCrumbRight2( "javascript:clearSelection(document.forms['editFrm'])", "Clear All Selections" );
if ($client_id != 0)
  $titleBlock->addCrumb( "?m=clients&a=view&client_id=$client_id", "View" );
$titleBlock->show();

?>
<script language="javascript">
var selected_contacts_id = "<?php echo $obj->main_contacts; ?>";
var contact_unique_update = "<?php echo $contact_unique_update; ?>";
var client_id = '<?php echo $obj->client_id;?>';
var client_name_msg = "<?php echo $AppUI->_('Please enter a name for the client');?>";


var calendarField = '';
var calWin = null;


function popCalendar( field ){
	calendarField = field;
	idate = eval( 'document.editFrm.log_' + field + '.value' );
	window.open( 'index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'top=250,left=250,width=251, height=220, scollbars=false, resizable' );
}

function setCalendar( idate, fdate )
{
	fld_date = eval( 'document.editFrm.log_' + calendarField );
	fld_fdate = eval( 'document.editFrm.counselling_' + calendarField );
	fld_date.value = idate;
	fld_fdate.value = fdate;
}

function checkDate()
{
           if (document.frmDate.log_start_date.value == "" || document.frmDate.log_end_date.value== ""){
                alert("<?php echo $AppUI->_('You must fill fields', UI_OUTPUT_JS) ?>");
                return false;
           }
           return true;
}

function popClinic() {
//        window.open('./index.php?m=public&a=selector&dialog=1&callback=setCompany&table=companies', 'company','left=50,top=50,height=250,width=400,resizable');
	window.open("./index.php?m=clients&a=select_client_clinic&dialog=1&table_name=clinics&clinic_id=<?php echo $clinic_detail['clinic_id'];?>", "clinic", "left=50,top=50,height=250,width=400,resizable");
}

function setClinic( key, val ){
	var f = document.editFrm;
 	if (val != '') {
    	f.contact_company.value = key;
			f.client_clinic_name.value = val;
    	if ( window.clinic_id != key )
		{
    		f.contact_department.value = "";
				f.contact_department_name.value = "";
    	}
    	window.clinic_id = key;
    	window.clinic_value = val;
    }
}

function offer(){	
	var $sl=$j("#refsrc");
	$sl.find("option:first").attr("disabled",true);	
	if(!editWas){
		turner(false);	
	}
}

function turner (way){
	if(way !== null){
		way = !way;
	}
	if(way === false){
		$j("#move_active").val("1");
	}
	$j(".sedit",$j("#qtab2")[0]).find("select, input, textarea").attr("disabled",way);
}

</script>
<?php
$date_reg = ($counsellingObj->counselling_entry_date) ?  $counsellingObj->counselling_entry_date : date("Y-m-d");
$df = $AppUI->getPref('SHDATEFORMAT');

$entry_date = new CDate( $date_reg );
$dob = intval( $counsellingObj->counselling_dob) ? new CDate( $counsellingObj->counselling_dob ) : null;
$intake_date = intval( $counsellingObj->counselling_entry_date) ? new CDate( $counsellingObj->counselling_entry_date ) : new CDate(date("Y-m-d"));
$child_nvp_date = intval( $counsellingObj->counselling_child_nvp_date) ? new CDate( $counsellingObj->counselling_child_nvp_date ) : null;
$child_azt_date = intval( $counsellingObj->counselling_child_azt_date) ? new CDate( $counsellingObj->counselling_child_azt_date ) : null;
$mother_date_art = intval( $counsellingObj->counselling_mother_date_art) ? new CDate( $counsellingObj->counselling_mother_date_art ) : null;
$mother_date_cd4 = intval( $counsellingObj->counselling_mother_date_cd4) ? new CDate( $counsellingObj->counselling_mother_date_cd4 ) : null;

$editWas = ($obj->client_status == 9 || is_null($obj->client_status))? false : true;

$date_reg = date("Y-m-d");
//$entry_date = $admissionObj->admission_entry_date ? new CDate($admissionObj->admission_entry_date) : null;//intval( $date_reg) ? new CDate( dPgetParam($_REQUEST, "counselling_entry_date", date("Y-m-d") ) ) : null;
$adm_date = $counsellingObj->counselling_admission_date ? new CDate($counsellingObj->counselling_admission_date): new CDate($admissionObj->admission_entry_date);
$df = $AppUI->getPref('SHDATEFORMAT');
$rapid18_date = intval( $counsellingObj->counselling_rapid18_date) ? new CDate( $counsellingObj->counselling_rapid18_date ) : null;
$determine_date = intval( $counsellingObj->counselling_determine_date) ? new CDate( $counsellingObj->counselling_determine_date ) : null;
$bioline_date = intval( $counsellingObj->counselling_bioline_date) ? new CDate( $counsellingObj->counselling_bioline_date ) : null;
$unigold_date = intval( $counsellingObj->counselling_unigold_date) ? new CDate( $counsellingObj->counselling_unigold_date ) : null;
$elisa_date = intval( $counsellingObj->counselling_elisa_date) ? new CDate( $counsellingObj->counselling_elisa_date ) : null;
$pcr1_date = intval( $counsellingObj->counselling_pcr1_date) ? new CDate( $counsellingObj->counselling_pcr1_date ) : null;
$pcr2_date = intval( $counsellingObj->counselling_pcr2_date) ? new CDate( $counsellingObj->counselling_pcr2_date ) : null;
$rapid12_date = intval( $counsellingObj->counselling_rapid12_date) ? new CDate( $counsellingObj->counselling_rapid12_date ) : null;
$other_date = intval( $counsellingObj->counselling_other_date) ? new CDate( $counsellingObj->counselling_other_date ) : null;

$refsrc = arrayMerge(array('-1'=>'- Select -'),dPgetSysVal('IntakeReferralSource'));

$birthPlaces = dPgetSysVal('BirthPlaceType');
$birthTypes = dPgetSysVal('BirthType');
$ageTypes = dPgetSysVal('AgeType');
$awareStages = dPgetSysVal('StatusAwareType');
$rapidResultsType = dPgetSysVal('RapidResultsType');
$elisaResultsType = dPgetSysVal('ElisaResultsType');
$pcrResultsType = dPgetSysVal('PCRResultsType');
$posRef = dPgetSysVal('PositiveReferral');

?>
 <form name="editFrm" action="?m=clients&client_id=<?php echo $client_id; ?>" method="post">
   <input type="hidden" name="dosql" value="do_newclient_aed" />
   <input type="hidden" name="insert_id" value="<?php echo uniqid(""); ?>" />
   <input type="hidden" name="client[client_id]" value="<?php echo $client_id; ?>" />
   <input type="hidden" name="client[client_type]" value="<?php echo $client_type; ?>" />
   <input type="hidden" name="client[client_date_entered]" value="<?php //echo $entry_date->format( FMT_DATETIME_MYSQL ); ?>" />
   <input type="hidden" name="old_clinic" value="<?php echo $counsellingObj->counselling_clinic; ?>" />
   <!--<input type="hidden" name="counselling[counselling_entry_date]" value="<?php //echo $intake_date ? $intake_date->format( FMT_DATETIME_MYSQL ) : " "; ?>" />-->
   <?php 
   if($counsellingObj->counselling_id > 0){
   	echo '<input type="hidden" name="counselling[counselling_id]" value="'.$counsellingObj->counselling_id.'">';
   }
   ?>
   <table cellspacing="1" cellpadding="1" border="0" width="100%" class="std">
   <td align="left">
	<tr>
	  <td>
		<table id="qtab">
		<tr>
			 <td nowrap="nowrap" colspan="2"><b><?php echo $AppUI->_('A: VCT Details');?></b> </td>
		   </tr>
			<!-- <tr>
			 <td align="left" nowrap="nowrap"><?php echo $AppUI->_('Admission No');?>: </td>
			  <td>
					<input type="text" class="text" name="client[client_adm_no]" id="client_adm_no" value="<?php echo dPformSafe(@$obj->client_adm_no);?>"
					size="20" maxlength="255" /> (<?php echo $AppUI->_('required');?>)
			</td>
		   </tr> -->
		<tr>
			<td align="left">1a.<?php echo $AppUI->_('Center');?>: </td>
			<td>
				<?php echo arraySelect( $clinics, 'counselling[counselling_clinic]', 'id="clinic_id" size="1" class="text"', @$counsellingObj->counselling_clinic ? $counsellingObj->counselling_clinic:0); ?>
			</td>
		</tr>
		<tr>
			 <td align="left" nowrap="nowrap">1b.<?php echo $AppUI->_('Date');?>: </td>
			<td>
				<?php
					echo drawDateCalendar("counselling[counselling_entry_date]",$entry_date ? $entry_date->format( $df ) : "" ,"",'id = "counselling_entry_date"');					
				?>
			</td>
		  </tr>
       <tr>
        	 <td align="left">1c.<?php echo $AppUI->_('Counselor');?>:</td>
		 	 <td align="left">
				<?php echo arraySelect( $owners, 'counselling[counselling_staff_id]', 'id="staff_id" size="1" class="text"', @$counsellingObj->counselling_staff_id ? $counsellingObj->counselling_staff_id:0); ?>
			</td>
       </tr>
       <tr>
        	 <td align="left">2a.<?php echo $AppUI->_('VCT Camp');?>:</td>
		 	 <td align="left">
				<?php echo arraySelectRadio( $boolTypes, 'counselling[counselling_vct_camp]', 'id="staff_id" size="1" class="text"', @$counsellingObj->counselling_vct_camp ? $counsellingObj->counselling_vct_camp:""); ?>
			</td>
       </tr>
		<tr>
        	 <td align="left">2b...<?php echo $AppUI->_('If Yes, VCT Camp site');?>:</td>
		 	 <td align="left">
				<input type="text" class="text" name="counselling[counselling_vct_camp_site]" size="50" maxlength="255" value="<?php echo dPformSafe($counsellingObj->counselling_vct_camp_site);?>">
			</td>
       </tr>
       <tr>
        	 <td align="left">3a.<?php echo $AppUI->_('Return visit');?>:</td>
		 	 <td align="left">
				<?php echo arraySelectRadio( $boolTypes, 'counselling[counselling_return]', 'id="staff_id" size="1" class="text"', @$counsellingObj->counselling_return ? $counsellingObj->counselling_return:""); ?>
			</td>
       </tr>
	  <tr>
        	 <td align="left">3b.<?php echo $AppUI->_('Client Code');?>:</td>
		 	 <td align="left">
				<input type="text" class="text" name="counselling[counselling_client_code]" size="50" maxlength="255" value="<?php echo dPformSafe($counsellingObj->counselling_client_code);?>">
			</td>
       </tr>
       <tr>
        	 <td align="left">3c.<?php echo $AppUI->_('Partner Code');?>:</td>
		 	 <td align="left">
				<input type="text" class="text" name="counselling[counselling_partner_code]" size="50" maxlength="255" value="<?php echo dPformSafe($counsellingObj->counselling_partner_code);?>">
			</td>
       </tr>
		   <tr>
			 <td align="left">4a.<?php echo $AppUI->_('First Name');?>: </td>
			  <td>
					<input type="text" class="text" name="client[client_first_name]" id="client_first_name" value="<?php echo dPformSafe(@$obj->client_first_name);?>"
					size="50" maxlength="255" />
			</td>
		   </tr>
		   <tr>
			 <td align="left">4b.<?php echo $AppUI->_('Last Name');?>: </td>
			  <td>
					<input type="text" class="text" name="client[client_last_name]" id="client_last_name" value="<?php echo dPformSafe(@$obj->client_last_name);?>"
					size="50" maxlength="255" />
			</td>
		   </tr>
		   <tr>
			 <td align="left">4c.<?php echo $AppUI->_('Other Name(s)');?>: </td>
			  <td>
					<input type="text" class="text" name="client[client_other_name]" id="client_other_name" value="<?php echo dPformSafe(@$obj->client_other_name);?>"
					size="50" maxlength="255" />
			</td>
		   </tr>
		<tr>
         <td align="left">5a.<?php echo $AppUI->_('Referral Source');?>:</td>
         <td align="left">
         	<?php echo arraySelect($refsrc,'counselling[counselling_referral_source]','id="refsrc" class="text"',$counsellingObj->counselling_referral_source ? $counsellingObj->counselling_referral_source : '')?>
         </td>
       </tr>
       <tr>
         <td align="left">...<?php echo $AppUI->_('Referral Source Other');?>:</td>
         <td align="left">         	
          <input type="text" class="text" name="counselling[counselling_referral_source_notes]" value="<?php echo @$counsellingObj->counselling_referral_source_notes;?>" maxlength="150" size="20" />
         </td>
       </tr>
       <tr>
         <td align="left">5b.<?php echo $AppUI->_('Area of residence');?>:</td>
         <td align="left">
          <input type="text" class="text" name="counselling[counselling_area]" value="<?php echo @$counsellingObj->counselling_area;?>" maxlength="150" size="20" />
         </td>
       </tr>  	 
       <tr>
			<td align="left">6a.<?php echo $AppUI->_('Date of birth');?>:</td>
			<td align="left" valign="top">
				<?php
					echo drawDateCalendar("counselling[counselling_dob]",$dob ? $dob->format( $df ) : "",false, 'id="counselling_dob"');
					//				<input type="text" class="text" name="counselling[counselling_dob]" id="counselling_dob" value="<?php echo $dob ? $dob->format( $df ) : "" ;" maxlength="150" size="20"/>&nbsp;dd/mm/yyyy
				?>
		&nbsp;6b.&nbsp;&nbsp;<?php echo arraySelectRadio($ageTypes, "counselling[counselling_age_status]", 'onclick=toggleButtons()', $counsellingObj->counselling_age_status ? $counsellingObj->counselling_age_status : -1, $identifiers ); ?></td>
		</tr>
	 <tr>
		<td align="left">6c.<?php echo $AppUI->_('Gender');?>:</td>
		<td align="left"><?php echo arraySelectRadio(dPgetSysVal('GenderType'), "counselling[counselling_gender]", 'onclick=toggleButtons() class="genderOpts"', $counsellingObj->counselling_gender ? $counsellingObj->counselling_gender : -1, $identifiers ); ?></td>
     </tr>
     <tr>
		<td align="left">7.<?php echo $AppUI->_('Marital Status');?>:</td>
		<td align="left"><?php echo arraySelectRadio(dPgetSysVal('MaritalStatusIntake'), "counselling[counselling_marital]", 'onclick=toggleButtons()', $counsellingObj->counselling_marital ? $counsellingObj->counselling_marital : -1, $identifiers ); ?></td>
     </tr>
     <tr>
		<td align="left">8.<?php echo $AppUI->_('Client Seen as');?>:</td>
		<td align="left"><?php echo arraySelectRadio(dPgetSysVal('ClientSeen'), "counselling[counselling_client_seen]", 'onclick=toggleButtons()', $counsellingObj->counselling_client_seen ? $counsellingObj->counselling_client_seen : -1, $identifiers ); ?></td>
     </tr>
        <tr>
			<td align="left"><?php echo $AppUI->_('Current Status');?>: </td>
			<td>
				<?php echo arraySelect( $status, 'client[client_status]', 'size="1" class="text" disabled', /*@$socialObj->social_client_status ? $socialObj->social_client_status*/$obj->client_status ? $obj->client_status : 9);
				echo '<input type="hidden" name="client[client_status]" value="'.((int)$obj->client_status > 0 ? $obj->client_status : 9).'">';				
				?>

			</td>
		</tr>
 	  
       
		<!-- <tr>
         <td align="left"><?php echo $AppUI->_('Referral Source');?>:</td>
         <td align="left">
          <input type="text" class="text" name="counselling[counselling_referral_source]" value="<?php echo @$counsellingObj->counselling_referral_source;?>" maxlength="150" size="20" />
         </td>
       </tr> -->
       

     
     
	</table>
	<table>
	<tr>
		 <td nowrap="nowrap" colspan="1"><b><?php echo $AppUI->_('HIV Diagnostic Tests');?></b> </td>
	</tr>
	<tr>
    <td valign="top" colspan="2">
      <table border="0" cellpadding = "1" cellspacing="1" class="ortho">
	   <tr>
		<td align="left"><?php echo $AppUI->_('Test');?>:</td>		
		<td align="left">&nbsp;</td>
		<td align="left">&nbsp;</td>
	 </tr>
	 <tr>
		<td align="left">9a.<?php echo $AppUI->_('Determine');?>:</td>		
		<td align="left">&nbsp;&nbsp;9b.&nbsp;&nbsp;
			<?php 
				echo drawDateCalendar("counselling[counselling_determine_date]",$determine_date ? $determine_date->format($df) : "",false, 'id="counselling_determine_date"');
				//<input type="text" class="text" name="counselling[counselling_determine_date]" id="counselling_determine_date" value="<?php echo $determine_date ? $determine_date->format($df) : "";" maxlength="150" size="20"/>&nbsp;dd/mm/yyyy
			?>			
		</td>
		<td align="left">&nbsp;&nbsp;9c.
		<?php echo arraySelectRadio($rapidResultsType, "counselling[counselling_determine]", 'onclick=toggleButtons()', $counsellingObj->counselling_determine ? $counsellingObj->counselling_determine : -1 ); ?>
		</td>
	 </tr>
	 <tr>
		<td align="left">10a.<?php echo $AppUI->_('Bio-line');?>:</td>
		<td align="left">&nbsp;&nbsp;10b.
			<?php 
				echo drawDateCalendar("counselling[counselling_bioline_date]" ,$bioline_date ? $bioline_date->format($df) : "",false,'id="counselling_bioline_date"');
				//<input type="text" class="text" name="counselling[counselling_bioline_date]" id="counselling_bioline_date" value="<?php echo $bioline_date ? $bioline_date->format($df) : "";" maxlength="150" size="20"/>&nbsp;dd/mm/yyyy</td>
			?>		
		<td align="left">&nbsp;&nbsp;10c.
		<?php echo arraySelectRadio($rapidResultsType, "counselling[counselling_bioline]", 'onclick=toggleButtons()', $counsellingObj->counselling_bioline ? $counsellingObj->counselling_bioline : -1 ); ?>

		</td>
	 </tr>
	 <tr>
		<td align="left">11a.<?php echo $AppUI->_('Uni-gold');?>:</td>
		<td align="left">&nbsp;&nbsp;11b.
		<?php 
			echo drawDateCalendar("counselling[counselling_unigold_date]",$unigold_date ? $unigold_date->format($df) : "",false, 'id="counselling_unigold_date"');
			//<input type="text" class="text" name="counselling[counselling_unigold_date]" id="counselling_unigold_date" value="<?php echo $unigold_date ? $unigold_date->format($df) : "";" maxlength="150" size="20"/>&nbsp;dd/mm/yyyy</td>
		?>			
		<td align="left">&nbsp;&nbsp;11c.
		<?php echo arraySelectRadio($rapidResultsType, "counselling[counselling_unigold]", 'onclick=toggleButtons()', $counsellingObj->counselling_unigold ? $counsellingObj->counselling_unigold : -1 ); ?>
		</td>
	 </tr>
	 <tr>
		<td align="left">12a.<?php echo $AppUI->_('ELISA');?>:</td>
		<td align="left">&nbsp;&nbsp;12b.
			<?php 
				echo drawDateCalendar("counselling[counselling_elisa_date]",$elisa_date ? $elisa_date->format($df) : "",false, 'id="counselling_elisa_date"');
				//<input type="text" class="text" name="counselling[counselling_elisa_date]" id="counselling_elisa_date" value="<?php echo $elisa_date ? $elisa_date->format($df) : "";" maxlength="150" size="20"/>&nbsp;dd/mm/yyyy</td>
			?>		
		<td align="left">&nbsp;&nbsp;12c.
		<?php echo arraySelectRadio($elisaResultsType, "counselling[counselling_elisa]", 'onclick=toggleButtons()', $counsellingObj->counselling_elisa ? $counsellingObj->counselling_elisa : -1 ); ?>
		</td>
	 </tr>
	 <tr>
		<td align="left">13a.<?php echo $AppUI->_('PCR1');?>:</td>
		<td align="left">&nbsp;&nbsp;13b.
		<?php 
			echo drawDateCalendar("counselling[counselling_pcr1_date]",$pcr1_date ? $pcr1_date->format($df) : "",false,'id="counselling_pcr1_date"');
			//<input type="text" class="text" name="counselling[counselling_pcr1_date]" id="counselling_pcr1_date" value="<?php echo $pcr1_date ? $pcr1_date->format($df) : "";" maxlength="150" size="20"/>&nbsp;dd/mm/yyyy</td>
		?>		
		<td align="left">&nbsp;&nbsp;13c.
		<?php echo arraySelectRadio(/*$pcrResultsType*/ $rapidResultsType, "counselling[counselling_pcr1]", 'onclick=toggleButtons()', $counsellingObj->counselling_pcr1 ? $counsellingObj->counselling_pcr1 : -1 ); ?>
		</td>
	 </tr>
	 <tr>
		<td align="left">14a.<?php echo $AppUI->_('PCR2');?>:</td>		
		<td align="left">&nbsp;&nbsp;14b.
		<?php 
			echo drawDateCalendar("counselling[counselling_pcr2_date]",$pcr2_date ? $pcr2_date->format($df) : "",false,'id="counselling_pcr2_date"');
			//<input type="text" class="text" name="counselling[counselling_pcr2_date]" id="counselling_pcr2_date" value="<?php echo $pcr2_date ? $pcr2_date->format($df) : "";" maxlength="150" size="20"/>&nbsp;dd/mm/yyyy</td>
		?>		
		<td align="left">&nbsp;&nbsp;14c.
		<?php echo arraySelectRadio(/*$pcrResultsType*/$rapidResultsType, "counselling[counselling_pcr2]", 'onclick=toggleButtons()', $counsellingObj->counselling_pcr2 ? $counsellingObj->counselling_pcr2 : -1 ); ?>
		</td>
	 </tr>
	 <tr>
		<td align="left">15a.<?php echo $AppUI->_('Rapid @ 12 months');?>:</td>		
		<td align="left">&nbsp;&nbsp;15b.
		<?php 
			echo drawDateCalendar("counselling[counselling_rapid12_date]",$rapid12_date ? $rapid12_date->format($df) : "",false, 'id="counselling_rapid12_date"');
			//<input type="text" class="text" name="counselling[counselling_rapid12_date]" id="counselling_rapid12_date" value="<?php echo $rapid12_date ? $rapid12_date->format($df) : ""; " maxlength="150" size="20"/>&nbsp;dd/mm/yyyy</td>
		?>		
		<td align="left">&nbsp;&nbsp;15c.
		<?php echo arraySelectRadio($rapidResultsType, "counselling[counselling_rapid12]", 'onclick=toggleButtons()', $counsellingObj->counselling_rapid12 ? $counsellingObj->counselling_rapid12 : -1 ); ?>
		</td>
	 </tr>
	 <tr>
		<td align="left">16a.<?php echo $AppUI->_('Rapid @ 18 months');?>:</td>		
		<td align="left">&nbsp;&nbsp;16b.
		<?php 
			echo drawDateCalendar("counselling[counselling_rapid18_date]",$rapid18_date ? $rapid18_date->format($df) : "",false, 'id="counselling_rapid18_date"');
			//<input type="text" class="text" name="counselling[counselling_rapid18_date]" id="counselling_rapid18_date" value="<?php echo $rapid18_date ? $rapid18_date->format($df) : "";" maxlength="150" size="20"/>&nbsp;dd/mm/yyyy</td>
		?>		
		<td align="left">&nbsp;&nbsp;16c.
		<?php echo arraySelectRadio($rapidResultsType, "counselling[counselling_rapid18]", 'onclick=toggleButtons()', $counsellingObj->counselling_rapid18 ? $counsellingObj->counselling_rapid18 : -1 ); ?>
		</td>
	 </tr>
	 <tr>
		<td align="left">17a.<?php echo $AppUI->_('Other');?>:
		<input type="text" class="text" name="counselling[counselling_other_notes]" id="counselling_other_notes" value="<?php echo $counsellingObj->counselling_other_notes;?>" maxlength="150" size="20"/>
		</td>
		<td align="left">&nbsp;&nbsp;17b.
			<?php 
				echo drawDateCalendar("counselling[counselling_other_date]",$other_date ? $other_date->format($df) : "",false, 'id="counselling_other_date"');
				//<input type="text" class="text" name="counselling[counselling_other_date]" id="counselling_other_date" value="<?php echo $other_date ? $other_date->format($df) : "";" maxlength="150" size="20"/>&nbsp;dd/mm/yyyy
			?>			
		</td>
		<td align="left">&nbsp;&nbsp;17c.&nbsp;&nbsp;&nbsp;<input type="text" class="text" name="counselling[counselling_other]" id="counselling_other" value="<?php echo $counsellingObj->counselling_other;?>" maxlength="150" size="20"/></td>	
	 </tr>
	 <tr>
		<td align="left">18a.<?php echo $AppUI->_('Final Results');?>:</td>
		<td align="left" colspan="3">&nbsp;&nbsp;
			<?php echo arraySelectRadio($pcrResultsType, "counselling[counselling_final]", 'onclick=toggleButtons()', $counsellingObj->counselling_final ? $counsellingObj->counselling_final : -1 ); ?>
		</td>
	 </tr>
	 <tr>
		<td align="left">18b.<?php echo $AppUI->_('Discordant Couple');?>:</td>
		<td align="left" colspan="3">&nbsp;&nbsp;
			<?php echo arraySelectRadio($boolTypes, "counselling[counselling_dis_couple]", 'onclick=toggleButtons()', $counsellingObj->counselling_dis_couple ? $counsellingObj->counselling_dis_couple : -1 ); ?>
		</td>
	 </tr>
	 <tr>
		<td align="left">19a.<?php echo $AppUI->_('If positive, referred to');?>:</td>
		<td align="left" colspan="3">&nbsp;&nbsp;
			<?php echo arraySelectRadio($posRef, "counselling[counselling_positive_ref]", 'onclick=toggleButtons()', $counsellingObj->counselling_positive_ref ? $counsellingObj->counselling_positive_ref : -1 ); ?>
		</td>
	 </tr>
	 <tr>
		<td align="left">19b....<?php echo $AppUI->_('Other (specify)');?>:</td>
		<td align="left" colspan="3">&nbsp;&nbsp;
			<input type="text" class="text" name="counselling[counselling_positive_ref_notes]" id="counselling_positive_ref_other" value="<?php echo $counsellingObj->counselling_positive_ref_notes ;?>" maxlength="150" size="20"/>
		</td>
	 </tr>  
	</table>
    </td>
    </tr>
    <tr>
   		<td><input type="button" value="<?php echo $AppUI->_('back');?>" class="button" onClick="javascript:history.back(-1);" /></td>
		<td colspan="5" align="right"><input type="button" name="btnFuseAction" value="<?php echo $AppUI->_('submit');?>" class="button" onClick="submitIt(document.editFrm);" /></td>
   </tr>
    
	</table>
 </td>
</tr>
</table>
<table cellspacing="1" cellpadding="1" border="0" width="100%" class="std" id="qtab2">
	<tr>
		 <td nowrap="nowrap" colspan="1"><b><?php echo $AppUI->_('Intake Details');?></b> </td>
		 <td align="left">
		 	<?php
		 	if(!$editWas){
		 	?>
		 		<input type="button" onclick="turner(true);" value="Add intake details" class="text">
		 	<?php
		 	}
			?>
		 </td>
	</tr>
	<tr class="sedit">
		<td align="left" nowrap="nowrap">20a.<?php echo $AppUI->_('Date of admission');?>: </td>
		<td>
		<?php
			echo drawDateCalendar("counselling[counselling_admission_date]",$adm_date ? $adm_date->format( $df ) : "" ,"",'id = "counselling_admission_date"');					
		?>
		</td>
	</tr>    
	<tr class="sedit">
         <td align="left">20b.<?php echo $AppUI->_('Place of Birth');?>:</td>
		 <td align="left">
		 <?php echo arraySelectRadio($birthPlaces, "counselling[counselling_place_of_birth]", 'onclick=toggleButtons()', $counsellingObj->counselling_place_of_birth ? $counsellingObj->counselling_place_of_birth : -1, $identifiers ); ?></td>
		<input type="hidden" name="move_active" value="0" id="move_active">
		 </td>
	</tr>
	<tr  class="sedit">
		<td align="left">20b.<?php echo $AppUI->_('Area of birth');?>:</td>
		 <td align="left"><input type="text" class="text" name="counselling[counselling_birth_area]" value="<?php echo @$counsellingObj->counselling_birth_area;?>" maxlength="150" size="20" />
		 </td>
		 </tr>
	 <tr class="sedit">
		<td align="left">21a.<?php echo $AppUI->_('Mode of birth');?>:</td>
		<td align="left"><?php echo arraySelectRadio($birthTypes, "counselling[counselling_mode_birth]", 'onclick=toggleButtons()', $counsellingObj->counselling_mode_birth ? $counsellingObj->counselling_mode_birth : -1, $identifiers ); ?></td>

     </tr>
	 <tr class="sedit">
		<td align="left">21b.<?php echo $AppUI->_('Gestation period (months)');?>:</td>
		<td align="left"><input type="text" class="text" name="counselling[counselling_gestation_period]" id="counselling_gestation_period" value="<?php echo $counsellingObj->counselling_gestation_period;?>" maxlength="150" size="20"/></td>
     </tr>
	 <tr class="sedit">
		<td align="left">21c.<?php echo $AppUI->_('Birth Weight');?>:</td>
		<td align="left"><input type="text" class="text" name="counselling[counselling_birth_weight]" id="counselling_birth_weight" value="<?php echo $counsellingObj->counselling_birth_weight;?>" maxlength="150" size="20"/></td>
     </tr>
     <tr class="sedit">
			<td align="left">22.<?php echo $AppUI->_('Mother aware of status');?>:</td>
			<td align="left" nowrap="nowrap"><?php echo arraySelectRadio($awareStages, "counselling[counselling_mothers_status_known]", 'onclick=toggleButtons()', $counsellingObj->counselling_mothers_status_known ? $counsellingObj->counselling_mothers_status_known : -1, $identifiers ); ?>
			</td>
      </tr>
	  <tr class="sedit">
		<td align="left" nowrap="nowrap">23a.<?php echo $AppUI->_('Mother any antenatal care');?>:</td>
		<td align="left"><?php echo arraySelectRadio($boolTypesND, "counselling[counselling_mother_antenatal]", 'onclick=toggleButtons()', $counsellingObj->counselling_mother_antenatal ? $counsellingObj->counselling_mother_antenatal : -1, $identifiers ); ?></td>
     </tr>
     <tr class="sedit">
		<td align="left" valign="top">23b.<?php echo $AppUI->_('If Yes, Where ');?>:</td>
		<td align="left" valign="top">
			<input type="text" class="text" name="counselling[counselling_mother_antenatal_where]" id="counselling_antenatal_where" value="<?php echo $counsellingObj->counselling_mother_antenatal_where;?>" maxlength="150" size="20"/>
		</td>
     </tr>
	 <tr class="sedit">
		<td align="left" valign="top">24a.<?php echo $AppUI->_('Mother enrolled in a PMTCT program');?>:</td>
		<td align="left" valign="top"><?php echo arraySelectRadio($boolTypesND, "counselling[counselling_mother_pmtct]", 'onclick=toggleButtons()', $counsellingObj->counselling_mother_pmtct ? $counsellingObj->counselling_mother_pmtct : -1, $identifiers ); ?></td>
     </tr>
     <tr class="sedit">
		<td align="left" valign="top">24b.<?php echo $AppUI->_('If Yes, Where ');?>:</td>
		<td align="left" valign="top">
			<input type="text" class="text" name="counselling[counselling_mother_pmtct_where]" id="counselling_pmtct_where" value="<?php echo $counsellingObj->counselling_mother_pmtct_where;?>" maxlength="150" size="20"/>
		</td>
     </tr>
	 <tr class="sedit">
		<td align="left" valign="top">25a.<?php echo $AppUI->_('Illness/STI at pregnancy');?>:</td>
		<td align="left" valign="top"><?php echo arraySelectRadio($boolTypesND, "counselling[counselling_mother_illness_pregnancy]", 'onclick=toggleButtons()', $counsellingObj->counselling_mother_illness_pregnancy ? $counsellingObj->counselling_mother_illness_pregnancy : -1, $identifiers ); ?></td>

     </tr>
	 <tr class="sedit">
	 <td align="left" valign="top">25b...<?php echo $AppUI->_('If Y please describe');?>:</td>
		<td align="left" valign="top">
		<textarea cols="70" rows="2" class="textarea" name="counselling[counselling_mother_illness_pregnancy_notes]"><?php echo @$counsellingObj->counselling_mother_illness_pregnancy_notes;?></textarea>
		</td>
	 </tr>
	<tr class="sedit">
		<td align="left" valign="top">26a.<?php echo $AppUI->_('Exclusive breastfeeding');?></td>
		<td align="left" valign="top"><?php echo arraySelectRadio($boolTypesND, "counselling[counselling_breastfeeding]", 'onclick=toggleButtons()', $counsellingObj->counselling_breastfeeding ? $counsellingObj->counselling_breastfeeding : -1, $identifiers ); ?></td>
	</tr>
    <tr class="sedit">
		<td align="left" valign="top">26b...<?php echo $AppUI->_('If Y, duration (months)');?></td>
		<td align="left"><input type="text" class="text" name="counselling[counselling_breastfeeding_duration]" id="counselling_breastfeeding_duration" value="<?php echo $counsellingObj->counselling_breastfeeding_duration;?>" maxlength="150" size="20"/></td>
	</tr>
    <tr class="sedit">
		<td align="left" valign="top">26c...<?php echo $AppUI->_('Duration other breastfeeding (months)');?></td>
		<td align="left"><input type="text" class="text" name="counselling[counselling_other_breastfeeding_duration]" id="counselling_other_breastfeeding_duration" value="<?php echo $counsellingObj->counselling_other_breastfeeding_duration;?>" maxlength="150" size="20"/>
		</td>
	  </tr>
	<tr class="sedit">
		<td align="left" valign="top">27a.<?php echo $AppUI->_('Child prenatal ARV exposure');?>:</td>
		<td align="left" valign="top">
			<?php echo arraySelectRadio($boolTypesND, "counselling[counselling_child_prenatal]", 'onclick=toggleButtons()', $counsellingObj->counselling_child_prenatal ? $counsellingObj->counselling_child_prenatal : -1 ); ?>
        </td>
	</tr>
	<tr class="sedit">
		<td align="left" valign="top">27b....<?php echo $AppUI->_('If Y single dose NVP') ?> </td>
		<td valign="top">
		<?php echo arraySelectRadio($boolTypesND, "counselling[counselling_child_single_nvp]", 'onclick=toggleButtons()', $counsellingObj->counselling_child_single_nvp? $counsellingObj->counselling_child_single_nvp : -1 ); ?>
		</td>
     </tr>
	 <tr class="sedit">

	 	<td align="left" valign="top">27c...<?php echo $AppUI->_('When given');?>:</td>
		<td align="left" valign="top">
			<?php
				echo drawDateCalendar("counselling[counselling_child_nvp_date]" ,$child_nvp_date ? $child_nvp_date->format( $df ) : "" ,false,'id="counselling_child_nvp_date"');
				//<input type="text" class="text" name="counselling[counselling_child_nvp_date]" id="counselling_child_nvp_date" value="<?php echo $child_nvp_date ? $child_nvp_date->format( $df ) : "" ;" maxlength="150" size="20"/>&nbsp;dd/mm/yyyy
			?>
		</td>
     </tr>
	 <tr class="sedit">
		<td align="left" valign="top">27d.<?php echo $AppUI->_('AZT given');?>:</td>

		<td align="left" valign="top">
			<?php echo arraySelectRadio($boolTypesND, "counselling[counselling_child_azt]", 'onclick=toggleButtons()', $counsellingObj->counselling_child_azt? $counsellingObj->counselling_child_azt : -1); ?>
        </td>
	 </tr>
	 <tr class="sedit">
		<td align="left" valign="top">27e...<?php echo $AppUI->_('Date AZT given');?>:</td>
		<td align="left" valign="top">
		<?php
			echo drawDateCalendar("counselling[counselling_child_azt_date]",$child_azt_date ? $child_azt_date->format( $df ) : "",false, 'id="counselling_child_azt_date"');
			//<input type="text" class="text" name="counselling[counselling_child_azt_date]" id="counselling_child_azt_date" value="<?php echo $child_azt_date ? $child_azt_date->format( $df ) : "" ;" maxlength="150" size="20"/>&nbsp;dd/mm/yyyy
		?>
		</td>
	</tr>
    <tr class="sedit">
		<td nowrap="nowrap">27f.<?php echo $AppUI->_('number of doses') ?> </td>
		<td align="left"><input type="text" class="text" name="counselling[counselling_no_doses]" id="counselling_no_doses" value="<?php echo $counsellingObj->counselling_no_doses;?>" maxlength="150" size="20"/></td>
     </tr>
	<tr class="sedit">
		<td align="left" >28a.<?php echo $AppUI->_('Mother in Medical Care Program');?>:</td>
		<td align="left" >
			<?php echo arraySelectRadio($boolTypesND, "counselling[counselling_mother_treatment]", 'onclick=toggleButtons()', $counsellingObj->counselling_mother_treatment ? $counsellingObj->counselling_mother_treatment : -1 ); ?>
        </td>
	 </tr>
	 <tr class="sedit">
		<td align="left" valign="top">28b...<?php echo $AppUI->_('If Yes, Where');?></td>
		<td align="left"><input type="text" class="text" name="counselling[counselling_mother_treatment_where]" id="counselling_treatment_where" value="<?php echo $counsellingObj->counselling_mother_treatment_where;?>" maxlength="150" size="20"/></td>
	</tr>
	 <tr class="sedit">
		<td align="left" valign="top">29a.<?php echo $AppUI->_('Mother on ART in pregnancy');?>:</td>
		<td align="left" valign="top">
			<?php echo arraySelectRadio($boolTypesND, "counselling[counselling_mother_art_pregnancy]", 'onclick=toggleButtons()', $counsellingObj->counselling_mother_art_pregnancy ? $counsellingObj->counselling_mother_art_pregnancy : -1 ); ?>
        </td>
     </tr>
	 <tr class="sedit">
		<td align="left" valign="top" nowrap="nowrap">29b...<?php echo $AppUI->_('Date began ART');?>:</td>
		<td align="left" valign="top">
			<?php
				echo drawDateCalendar("counselling[counselling_mother_date_art]",$mother_date_art ? $mother_date_art->format( $df ) : "",false, 'id="counselling_mother_date_art"');
				//<input type="text" class="text" name="counselling[counselling_mother_date_art]" id="counselling_mother_date_art" value="<?php echo $mother_date_art ? $mother_date_art->format( $df ) : "" ;" maxlength="150" size="20"/>&nbsp;dd/mm/yyyy
			?>
		</td>
	 </tr>
    <tr class="sedit">
		<td align="left" valign="top">30a.<?php echo $AppUI->_('Most recent maternal CD4 count');?>:</td>
		<td align="left">
		<?php
			echo arraySelectRadio(dPgetSysVal('CD4Count'),'counselling[counselling_mother_cd4_note]','',$counsellingObj->counselling_mother_cd4_note ? $counsellingObj->counselling_mother_cd4_note : '',$identifiers);
		?>&nbsp;
			<input type="text" class="text" name="counselling[counselling_mother_cd4]" id="counselling_mother_cd4" value="<?php echo $counsellingObj->counselling_mother_cd4;?>" maxlength="150" size="20"/></td>

	 </tr>
	 <tr class="sedit">
		<td align="left" valign="top">30b...<?php echo $AppUI->_('Date of CD4 test');?>:</td>
		<td align="left" valign="top">
			<?php
				echo drawDateCalendar("counselling[counselling_mother_date_cd4]",$mother_date_cd4 ? $mother_date_cd4->format( $df ) : "",false, 'id="counselling_mother_date_cd4"');
				//<input type="text" class="text" name="counselling[counselling_mother_date_cd4]" id="counselling_mother_date_cd4" value="<?php echo $mother_date_cd4 ? $mother_date_cd4->format( $df ) : "" ;" maxlength="150" size="20"/>&nbsp;dd/mm/yyyy
			?>
		</td>
	 </tr>
	<tr class="sedit">
	 	<td align="left" valign="top">31.<?php echo $AppUI->_('Remarks');?>:</td>
	</tr>
	<tr  class="sedit">
		<td colspan="2">
		<textarea cols="70" rows="2" class="textarea" name="counselling[counselling_notes]"><?php echo @$counsellingObj->counselling_notes;?></textarea>
		</td>
     </tr>
	</table>
	</td>
	</tr>
   <tr>
   		<td><input type="button" value="<?php echo $AppUI->_('back');?>" class="button" onClick="javascript:history.back(-1);" /></td>
		<td colspan="5" align="right"><input type="button" name="btnFuseAction" value="<?php echo $AppUI->_('submit');?>" class="button" onClick="submitIt(document.editFrm);" /></td>
   </tr>

</td>
</table>
</form>
<script type="text/javascript">
<!--
var editWas=<?php echo $editWas ? $editWas : 'false'; ?>;
window.onload=offer;
//-->
</script>
