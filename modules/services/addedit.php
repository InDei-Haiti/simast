<?php
global $AppUI,$dPconfig,$loadFromTab, $tab;
global $obj, $client_id, $url,$can_edit_contact_information;
global $convert;
require_once ($AppUI->getModuleClass('clients'));
//require_once ($AppUI->getModuleClass('counselling'));
$medical_id = intval( dPgetParam( $_GET, "medical_id", 0 ) );

// check permissions for this record
$perms =& $AppUI->acl();
// If the company exists we need edit permission,
// If it is a new company we need add permission on the module.
if ($medical_id)
  $canEdit = $perms->checkModuleItem($m, "edit", $medical_id);
else
  $canEdit = $perms->checkModule($m, "add");

if (!$canEdit) {
	$AppUI->redirect( "m=public&a=access_denied" );
}

$perms = & $AppUI->acl();

$canEdit = true;
$msg = '';

$boolTypes = dPgetSysVal('YesNo');
$genderTypes = dPgetSysVal('GenderType');
$immunizationStatus = dPgetSysVal('ImmunizationStatus');
$hivStatus = dPgetSysVal('HIVStatusTypes');
$managementhivStatus = dPgetSysVal('ManagementHIVStatusTypes');
$malnutritionType = dPgetSysVal('MalnutritionType');
$arvTreatmentTypes = dPgetSysVal('ARVTreatmentTypes');
$educProgressType = dPgetSysVal('EducationProgressType');
$motorType = dPgetSysVal('MotorAbilityType');
$dehydrationType = dPgetSysVal('MalnutritionType');
$lymphType = dPgetSysVal('LymphType');
$tbTypes = dPgetSysVal('TBType');
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
$palpableType = dPgetSysVal('PalpableTypes');
$directionType = dPgetSysVal('DirectionTypes');
$umbilicalType = dPgetSysVal('UmbilicalTypes');
$conditionType = dPgetSysVal('ConditionType');
$femaleConditionType = dPgetSysVal('FemaleConditionTypes');
$examinationType = dPgetSysVal('ExaminationType');
$penisTypes = dPgetSysVal('PenisTypes');
$developmentType = dPgetSysVal('DevelopmentTypes');
$enlargementType = dPgetSysVal('EnlargementTypes');
$eyeType = dPgetSysVal('EyeStatusTypes');
$feelType = dPgetSysVal('FeelTypes');
$motorType = dPgetSysVal('MotorTypes');

// collect all the users for the staff list
$q  = new DBQuery;
$q->addTable('contacts','con');
$q->leftJoin('users','u', 'u.user_contact = con.contact_id');
$q->addQuery('contact_id');
$q->addQuery('CONCAT_WS(", ",contact_last_name,contact_first_name)'); 
$q->addOrder('contact_last_name');
$owners = $q->loadHashList();



$riskLevels = dPgetSysVal('RiskLevel');
$riskLevels = arrayMerge(array(-1=>'-Select Risk Level-'),$riskLevels );

//load clinics
$q  = new DBQuery;
$q->addTable('clinics','c');
$q->addQuery('clinic_id');
$q->addQuery('clinic_name');
$q->addOrder('clinic_name');
$clinics = $q->loadHashList();

// setup the title block
$client_id = $client_id ? $client_id : $obj->medical_client_id;
//load client


$clientObj = new CClient();

if ($clientObj->load($client_id))
{
	$ttl = $medical_id > 0 ? "Edit Medical Assessment Record : " . $clientObj->getFullName() : "New Medical Assessment Record  : " . $clientObj->getFullName();

}
else
{
   $ttl = $medical_id > 0 ? "Edit Medical Assessment Record    " : "New Medical Assessment Record  ";

}

$titleBlock = new CTitleBlock( $ttl, '', $m, "$m.$a" );
$titleBlock->addCrumb( "?m=clients", "Client" );

if ($client_id != 0)
	$titleBlock->addCrumb( "?m=clients&a=view&client_id=$client_id", "view " .$clientObj->getFullName()  );
	
if ($medical_id != 0)
  $titleBlock->addCrumb( "?m=medical&a=view&medical_id=$medical_id", "View" );
$titleBlock->show();


// load the record data
$q  = new DBQuery;
$q->addTable('medical_assessment');
$q->addQuery('medical_assessment.*');
$q->addWhere('medical_assessment.medical_id = '.$medical_id);
$sql = $q->prepare();
//var_dump($sql);
$q->clear();

$obj = new CMedicalAssessment();

if (!db_loadObject( $sql, $obj ) && $medical_id > 0) {
	// $AppUI->setMsg( '	$qid =& $q->exec(); Company' ); // What is this for?
	$AppUI->setMsg( "invalidID", UI_MSG_ERROR, true );
	$AppUI->redirect();
}

//load building solution name
//var_dump($obj->medical_building_solution_id);
//var_dump($building_solution_id);


$date_reg = date("Y-m-d");
$medical_conditions = explode(",", $obj->medical_conditions);

$entry_date = intval( $obj->medical_entry_date) ? new CDate($obj->medical_entry_date ) : new CDate($date_reg );

$medical_tb_date1 = intval( $obj->medical_tb_date1 ) ? new CDate( $obj->medical_tb_date1 ) : NULL;
$medical_tb_date2 = intval( $obj->medical_tb_date2 ) ? new CDate( $obj->medical_tb_date2 ) : NULL;
$medical_tb_date3 = intval( $obj->medical_tb_date3 ) ? new CDate( $obj->medical_tb_date3 ) : NULL;
$medical_arv2_startdate = intval( $obj->medical_arv2_startdate ) ? new CDate( $obj->medical_arv2_startdate ) : NULL;
$medical_arv2_enddate = intval( $obj->medical_arv2_enddate ) ? new CDate( $obj->medical_arv2_enddate ) : NULL;
$medical_arv1_startdate = intval( $obj->medical_arv1_startdate ) ? new CDate( $obj->medical_arv1_startdate ) : NULL;
$medical_arv1_enddate = intval( $obj->medical_arv1_enddate ) ? new CDate( $obj->medical_arv1_enddate ) : NULL;


$df = $AppUI->getPref('SHDATEFORMAT');

?>
<script language="javascript">
var selected_fw_contacts_id = "<?php echo $obj->medical_firewall_contact; ?>";
var selected_vpn_contacts_id = "<?php echo $obj->medical_vpn_contact; ?>";
var client_id = "<?php echo $obj->medical_client_id;?>";
function submitIt() {
	var form = document.changeMedical;
	if (form.medical_age_yrs && form.medical_age_yrs.value.length > 0) 
	{
		if (isNaN(parseInt(form.medical_age_yrs.value,10)) )
		{
			alert(" Invalid Age (years)");
			form.medical_age_yrs.focus();
			exit;
			
		}
	}
	if (form.medical_age_months && form.medical_age_months.value.length > 0) 
	{
		if (isNaN(parseInt(form.medical_age_months.value,10)) )
		{
			alert(" Invalid Age (months)");
			form.medical_age_months.focus();
			exit;

		}
	}	
	if (form.medical_birth_weight && form.medical_birth_weight.value.length > 0) 
	{
		if (isNaN(parseInt(form.medical_birth_weight.value,10)) )
		{
			alert(" Invalid Age (months)");
			form.medical_birth_weight.focus();
			exit;

		}
	}		
	if (form.medical_bf_duration && form.medical_bf_duration.value.length > 0) 
	{
		if (isNaN(parseInt(form.medical_bf_duration.value,10)) )
		{
			alert(" Invalid BF Duration");
			form.medical_bf_duration.focus();
			exit;

		}
	}		

	if (form.medical_tb_date1 && form.medical_tb_date1.value.length > 0) 
	{
		errormsg = checkValidDate(form.medical_tb_date1.value);
		
		if (errormsg.length > 1)
		{
			alert("Invalid TB course of treatment date 1" );
			form.medical_tb_date1.focus();
			exit;
		}
	}
	if (form.medical_tb_date2 && form.medical_tb_date2.value.length > 0) 
	{
		errormsg = checkValidDate(form.medical_tb_date2.value);
		
		if (errormsg.length > 1)
		{
			alert("Invalid TB course of treatment date 2" );
			form.medical_tb_date2.focus();
			exit;
		}
	}
	if (form.medical_tb_date3 && form.medical_tb_date3.value.length > 0) 
	{
		errormsg = checkValidDate(form.medical_tb_date3.value);
		
		if (errormsg.length > 1)
		{
			alert("Invalid TB course of treatment date 2" );
			form.medical_tb_date3.focus();
			exit;
		}
	}
	if (form.medical_arv1_startdate && form.medical_arv1_startdate.value.length > 0) 
	{
		errormsg = checkValidDate(form.medical_arv1_startdate.value);
		
		if (errormsg.length > 1)
		{
			alert("Invalid ARV 1st line start date" );
			form.medical_arv1_startdate.focus();
			exit;
		}
	}	
		if (form.medical_arv1_enddate && form.medical_arv1_enddate.value.length > 0) 
	{
		errormsg = checkValidDate(form.medical_arv1_enddate.value);
		
		if (errormsg.length > 1)
		{
			alert("Invalid ARV 1st line end date" );
			form.medical_arv1_enddate.focus();
			exit;
		}
	}
	if (form.medical_arv2_startdate && form.medical_arv2_startdate.value.length > 0) 
	{
		errormsg = checkValidDate(form.medical_arv2_startdate.value);
		
		if (errormsg.length > 1)
		{
			alert("Invalid ARV 2nd line start date" );
			form.medical_arv2_startdate.focus();
			exit;
		}
	}
	if (form.medical_arv2_enddate && form.medical_arv2_enddate.value.length > 0) 
	{
		errormsg = checkValidDate(form.medical_arv2_enddate.value);
		
		if (errormsg.length > 1)
		{
			alert("Invalid ARV 2nd line end date" );
			form.medical_arv2_enddate.focus();
			exit;
		}
	}
	
	form.submit();
}
function popFWContacts() 
{
	window.open('./index.php?m=public&a=contact_selector&dialog=1&call_back=setFWContacts&selected_contacts_id='+selected_fw_contacts_id, 'contacts','height=600,width=450,resizable,scrollbars=yes');
}

function popVPNContacts() 
{
	window.open('./index.php?m=public&a=contact_selector&dialog=1&call_back=setVPNContacts&selected_contacts_id='+selected_vpn_contacts_id, 'contacts','height=600,width=450,resizable,scrollbars=yes');
}

function setFWContacts(contact_id_string)
{

	if(!contact_id_string)
	{
		contact_id_string = "";
	}
	
	medical_firewall_contact = document.getElementById('medical_firewall_contact');
	medical_firewall_contact.value = contact_id_string;
	
	selected_fw_contacts_id = contact_id_string;
}

function setVPNContacts(contact_id_string)
{

	if(!contact_id_string)
	{
		contact_id_string = "";
	}
	
	medical_vpn_contact = document.getElementById('medical_vpn_contact');
	medical_vpn_contact.value = contact_id_string;
	selected_vpn_contacts_id = contact_id_string;
}
function toggleButtons()
{
	client_on_bs = document.getElementById('on_bs');
	client_not_on_bs = document.getElementById('not_on_bs');
	building_solution = document.getElementById('building_solution');
	
	building_solution.disabled = true;
	
	if ((!client_on_bs.checked) && (!client_not_on_bs.checked))
	{
		building_solution.disabled = true;
	}
	
	if ((client_on_bs.checked) || (selected_bs_id > 0))
	{
		building_solution.disabled = false;
	}
    	
}
function popBuildingSolutions(field)
{
	bs_name = field;
	window.open('./index.php?m=public&a=bs_selector&dialog=1&call_back=setBuildingSolution&selected_bs_id='+selected_bs_id, 'building_solutions','height=600,width=600,resizable,scrollbars=yes');
}

function setBuildingSolution(bs_id_string, bs_location)
{

	if(!bs_id_string)
	{
		bs_id_string = "";
	}
	building_solution_id = document.getElementById('building_solution_id');
	
	building_solution = document.getElementById('bs_name');
	building_solution_id.value = bs_id_string;
	
	bs_name.value = bs_location;
}
// Given a tr node and row number (newid), this iterates over the row in the
// DOM tree, changing the id attribute to refer to the new row number.
function rowrenumber(newrow, newid)
{
  var curnode = newrow.firstChild;      // td node
  while (curnode) {
    var curitem = curnode.firstChild;   // input node (or whatever)
    while (curitem) {    
      if (curitem.id) {  // replace row number in id
        var idx = 0;
        var spl = curitem.id.split('_');
        var baseid = spl[0];
        curitem.id = baseid + '_' + newid;
        if (curitem.name)
          curitem.name = baseid + '_' + newid;
        if (baseid == 'catno')
          curitem.tabIndex = newid;
      }
      curitem = curitem.nextSibling;
    }
    curnode = curnode.nextSibling;
  }
}

// Appends a row to the given table, at the bottom of the table.
function AppendRow(table_id)
{
  var row = document.getElementById(table_id).rows.item(1);  // 1st row
  var newid = row.parentNode.rows.length;  // Since this includes the header row, we don't need to add one
  var newrow = row.cloneNode(true);
  
  rowrenumber(newrow, newid);
  row.parentNode.appendChild(newrow);      // Attach to table
    
    // Clear out data from new row.
	
  var curnode = document.getElementById('name_' + newid);
  curnode.value = "";
  curnode.tabIndex = newid;
  curnode = document.getElementById('yob_' + newid);
  curnode.value = "";
  curnode = document.getElementById('gender_' + newid);
  curnode.value = "";
  curnode = document.getElementById('relationship_' + newid);
  curnode.value = "";
  curnode = document.getElementById('comments_' + newid);
  curnode.value = "";
  curnode = document.getElementById('delete_' + newid);
  curnode.innerHTML = "X";
  curnode = document.getElementById('delete_1');  // Really only need this when newid = 2
  curnode.innerHTML = "X";
}

function NewRow(table_id)
{
  var row = document.getElementById(table_id).rows.item(1);  // 1st row
  var newid = row.parentNode.rows.length;  // Since this includes the header row, we don't need to add one

  var newrow = row.cloneNode(true);
  rowrenumber(newrow, newid);
  row.parentNode.appendChild(newrow);      // Attach to table
    
    // Clear out data from new row.
	
  var curnode = document.getElementById('drug_' + newid);
  curnode.value = "";
  curnode.tabIndex = newid;
  curnode = document.getElementById('dose_' + newid);
  curnode.value = "";
  curnode = document.getElementById('frequency_' + newid);
  curnode.value = "";
  curnode = document.getElementById('delete_' + newid);
  curnode.innerHTML = "X";
  curnode = document.getElementById('delete_1');  // Really only need this when newid = 2
  curnode.innerHTML = "X";
}
// Give a node within a row of the table (one level down from the td node),
// this deletes that row, renumbers the other rows accordingly, updates
// the Grand Total, and hides the delete button if there is only one row
// left.
function DeleteRow(el)
{
  var row = el.parentNode.parentNode;   // tr node
  var rownum = row.rowIndex;            // row to delete
  var tbody = row.parentNode;           // tbody node
  var numrows = tbody.rows.length - 1;  // don't count header row!
  if (numrows == 1)                     // can't delete when only one row left
    return false;

  var node = row;
  tbody.removeChild(node);
  var newid = -1;
  
    // Loop through tr nodes and renumber - only rows numbered
    // higher than the row we just deleted need renumbering.
  
  row = tbody.firstChild;
  while (row) {
    if (row.tagName == 'TR') {
      newid++;
      if (newid >= rownum)
        rowrenumber(row, newid);
    }
    row = row.nextSibling;
  }
  if (numrows == 2) {  // 2 rows before deleting - only 1 left now, so 'hide' delete button
    var delbutton = document.getElementById('delete_1');
    //delbutton.innerHTML = ' ';
  }
}
</script>
<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">
<form name="changeMedical" action="?m=medical" method="post">
  <input type="hidden" name="dosql" value="do_medical_aed"/>
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="medical_unique_update" value="<?php echo uniqid("");?>" />
  <input type="hidden" name="medical_id" value="<?php echo $obj->medical_id;?>" />
  <input type="hidden" name="medical_client_id" value="<?php echo $client_id;?>" />
  <input type="hidden" name="medical_history_rows" value="0" />
  <input type="hidden" name="medication_history_rows" value="0" />

<tr>
    <td colspan="2" valign="top">
      <table border="0" cellpadding = "1" cellspacing="1">
		<tr>
			<td colspan="2" align="left">
				<strong><?php echo $AppUI->_('Client Information'); ?><br /></strong>
				<hr width="500" align="left" size=1 />
			</td>
	 </tr>  
	   <tr>
			<td align="left" nowrap><?php echo $AppUI->_('Registration Date');?>: </td>
			<td align="left">
				<input type="hidden" name="log_entry_date" value="<?php echo $entry_date ? $entry_date->format( FMT_TIMESTAMP_DATE ) : "" ;?>" />
				<input type="text" name="medical_entry_date" value="<?php echo $entry_date ? $entry_date->format( $df ) : "" ;?>" class="text" readonly disabled="disabled" />
			</td>
		   </tr>
		<tr>
         <td align="left"><?php echo $AppUI->_('Clinician');?>:</td>
		 <td align="left">
				<?php echo arraySelect( $owners, 'medical_staff_id', 'size="1" class="text"', @$obj->medical_staff_id ? $obj->medical_staff_id:-1); ?>        
			</td>		 
       </tr>    
	   <tr>
         <td align="left"><?php echo $AppUI->_('Center');?>:</td>
         <td align="left">
          <?php echo arraySelect($clinics, "medical_clinic_id", 'class="text"', $obj->medical_clinic_id ); ?>
         </td>
		 </tr>
		 <tr>
		 <td align="left"><?php echo $AppUI->_('Date');?>: </td>
			<td align="left">
				<input type="hidden" name="medical_entry_date" value="<?php echo $entry_date ? $entry_date->format( FMT_TIMESTAMP_DATE ) : "" ;?>" />
				<input type="text" name="entry_date" value="<?php echo $entry_date ? $entry_date->format( $df ) : "" ;?>" class="text" readonly disabled="disabled" />
			</td>
       </tr>
      <tr>	 
       <tr>
         <td align="left"><?php echo $AppUI->_('Adm No');?>:</td>
         <td align="left">
          <input type="text" class="text" name="client_adm_no" value="<?php echo dPformSafe(@$clientObj->client_adm_no);?>" maxlength="150" size="20" disabled  readonly="readonly" />
         </td>
       </tr>

	 <tr>
         <td align="left"><?php echo $AppUI->_('Client Name');?>:</td>
         <td align="left">
		    <input type="text" class="text" name="medical_client_name" value="<?php echo dPformSafe(@$clientObj->getFullName());?>" maxlength="150" size="20" disabled  readonly="readonly" />
         </td>
       </tr>

      <tr>
         <td align="left"><?php echo $AppUI->_('Age (years)');?>:</td>
		 <td align="left">
	    <input type="text" class="text" name="medical_age_yrs" value="<?php echo dPformSafe(@$obj->medical_age_yrs);?>" maxlength="30" size="20" />
		 </td>
	 </tr>
	 <tr>
	 <td><?php echo $AppUI->_('Age (months)');?>:</td>
	 <td align="left">
	    <input type="text" class="text" name="medical_age_months" value="<?php echo dPformSafe(@$obj->medical_age_months);?>" maxlength="30" size="20" />
		 </td>

	 </tr>

       <tr>
         <td align="left" nowrap="nowrap"><?php echo $AppUI->_('Transferred from another programme?');?>:</td>
		 <td align="left"><?php echo arraySelectRadio($boolTypes, "medical_transferred", 'onclick=toggleButtons()', $obj->medical_transferred? $obj->medical_transferred : -1, $identifiers ); ?></td>

       </tr>
	  <tr>	   
	      <td align="left">...<?php echo $AppUI->_('If Y, which?');?>:</td>
          <td><input type="text" class="text" name="medical_other_programme" value="<?php echo @$obj->medical_other_programme;?>" maxlength="150" size="20" />
         </td>
	   </tr>	 
	   <tr>
		<td align="left"><?php echo $AppUI->_('Birth Weight');?>:</td>
		<td align="left"><input type="text" class="text" name="medical_birth_weight" id="medical_birth_weight" value="<?php echo $obj->medical_birth_weight;?>" maxlength="150" size="20"/></td>
      </tr>
	 	  
	   <tr>
		<td align="left"><?php echo $AppUI->_('PMTCT');?>:</td>
		<td align="left"><input type="text" class="text" name="medical_pmtct" id="medical_pmtct" value="<?php echo $obj->medical_pmtct;?>" maxlength="150" size="20"/></td>
      </tr>
       <tr>
         <td align="left" valign="top"><?php echo $AppUI->_('ARVs given');?>:</td>
         </tr>
		 <tr>
		  <td align="left">...<?php echo $AppUI->_('Mother');?>:</td>
		   <td align="left"><?php echo arraySelectRadio($boolTypes, "medical_mother_arv_given", 'onclick=toggleButtons()', $obj->medical_mother_arv_given? $obj->medical_mother_arv_given : -1, $identifiers ); ?></td>
		 </tr>
		 <tr>
         <td align="left">...<?php echo $AppUI->_('Baby');?>:</td>
		 <td align="left"><?php echo arraySelectRadio($boolTypes, "medical_child_arv_given", 'onclick=toggleButtons()', $obj->medical_child_arv_given? $obj->medical_child_arv_given : -1, $identifiers ); ?></td>
		</tr>
	   <tr>
		<td align="left"><?php echo $AppUI->_('Immunization status');?>:</td>
		<td align="left"><?php echo arraySelectRadio($immunizationStatus, "medical_immunization_status", 'onclick=toggleButtons()', $obj->medical_immunization_status ? $obj->medical_immunization_status : -1, $identifiers ); ?>
		</tr>
		<tr>
		<td align="left">...<?php echo $AppUI->_('Card seen?');?>:</td>
		<td align="left"><?php echo arraySelectRadio($boolTypes, "medical_card_seen", 'onclick=toggleButtons()', $obj->medical_card_seen ? $obj->medical_card_seen : -1, $identifiers ); ?></td>
		</tr>
	   <tr>
		<td align="left"><?php echo $AppUI->_('Breastfeeding?');?>:</td>
		<td align="left"><?php echo arraySelectRadio($boolTypes, "medical_breastfeeding", 'onclick=toggleButtons()', $obj->medical_breastfeeding ? $obj->medical_breastfeeding : -1, $identifiers ); ?></td>
	   </tr>
       <tr>	   
		<td align="left">...<?php echo $AppUI->_('Exclusive BF?');?>:</td>
		<td align="left"><?php echo arraySelectRadio($boolTypes, "medical_exclusive_breastfeeding", 'onclick=toggleButtons()', $obj->medical_exclusive_breastfeeding ? $obj->medical_exclusive_breastfeeding : -1, $identifiers ); ?></td>
	   </tr>
       <tr>	   
		<td align="left">...<?php echo $AppUI->_('Duration of BF');?>:</td>
		<td align="left"><input type="text" class="text" name="medical_bf_duration" id="medical_bf_duration" value="<?php echo $obj->medical_bf_duration;?>" maxlength="150" size="20"/></td>
	   </tr>
     <tr>
			<td align="left"><?php echo $AppUI->_('Father HIV Status');?>:</td>
			<td align="left"><?php echo arraySelectRadio($hivStatus, "medical_father_hiv_status", 'onclick=toggleButtons()', $obj->medical_father_hiv_status ? $obj->medical_father_hiv_status : -1, $identifiers ); ?></td>
	 </tr>
	 <tr>
			<td align="left">...<?php echo $AppUI->_('On ARVs');?>:</td>
			<td align="left"><?php echo arraySelectRadio($boolTypes, "medical_father_arv", 'onclick=toggleButtons()', $obj->medical_father_arv ? $obj->medical_father_arv : -1, $identifiers ); ?></td>
	 </tr> 
     <tr>
			<td align="left"><?php echo $AppUI->_('Mother HIV Status');?>:</td>
			<td align="left"><?php echo arraySelectRadio($hivStatus, "medical_mother_hiv_status", 'onclick=toggleButtons()', $obj->medical_mother_hiv_status ? $obj->medical_mother_hiv_status : -1, $identifiers ); ?></td>
	 </tr>
	 <tr>
			<td align="left">...<?php echo $AppUI->_('On ARVs');?>:</td>
			<td align="left"><?php echo arraySelectRadio($boolTypes, "medical_mother_arv", 'onclick=toggleButtons()', $obj->medical_mother_arv ? $obj->medical_mother_arv : -1, $identifiers ); ?></td>
	  </tr> 
     <tr>
			<td align="left"><?php echo $AppUI->_('Number of siblings alive');?>:</td>
			<td align="left"><input type="text" class="text" name="medical_no_siblings_alive" id="medical_no_siblings_alive" value="<?php echo $obj->medical_no_siblings_alive;?>" maxlength="150" size="20"/></td>
		 </tr>
	 <tr>

			<td align="left"><?php echo $AppUI->_('Number of siblings deceased');?>:</td>
			<td align="left"><input type="text" class="text" name="medical_no_siblings_deceased" id="medical_no_siblings_deceased" value="<?php echo $obj->medical_no_siblings_deceased;?>" maxlength="150" size="20"/></td>
	  </tr> 
     <tr>
			<td align="left"><?php echo $AppUI->_('TB: Any Household contact');?>:</td>
			<td align="left">
			<?php echo arraySelectRadio($boolTypes, "medical_tb_contact", 'onclick=toggleButtons()', $obj->medical_tb_contact ? $obj->medical_tb_contact : -1, $identifiers ); ?>
			</td>
	 </tr>
	 <tr>

			<td align="left">...<?php echo $AppUI->_('Who');?>:</td>
			<td align="left"><input type="text" class="text" name="medical_tb_contact_person" id="medical_tb_contact_person" value="<?php echo $obj->medical_tb_contact_person;?>" maxlength="150" size="20"/></td>
	 </tr>
	 <tr>

			<td align="left">...<?php echo $AppUI->_('When diagnosed');?>:</td>
			<td>
				<input type="text" class="text" name="medical_tb_date_diagnosed" id="medical_tb_date_diagnosed" value="<?php echo $medical_tb_date_diagnosed ? $medical_tb_date_diagnosed->format( $df ) : "" ;?>" maxlength="150" size="20"/>&nbsp;dd/mm/yyyy
			 </td>
	  </tr> 
	  
	  
	<tr>
			<td colspan="2" align="left">
				<strong><?php echo $AppUI->_('Medical History'); ?><br /></strong>
				<hr width="500" align="left" size=1 />
			</td>
	 </tr>
	 <tr>
		<td>&nbsp;</td>
		 <td align="left">
		 <table>
		   <tr> 
		    <td>
				 <table id="family">
					 <th><?php echo $AppUI->_('Name');?></th>
					 <th><?php echo $AppUI->_('Year of Birth');?></th>
					 <th><?php echo $AppUI->_('Gender');?></th>
					 <th><?php echo $AppUI->_('Relationship to child');?></th>
					 <th><?php echo $AppUI->_('Comments');?></th>
					 <th>&nbsp;</th>
					 <tr>
						 <td align="left"><input type="text" class="text" id="name_1" name="name_1" value="<?php echo @$row->name;?>" maxlength="150" size="20" /></td>
						 <td align="left"><input type="text" class="text" id="yob_1" name="yob_1" value="<?php echo @$row->yob;?>" maxlength="150" size="20" /></td>
						 <td align="left"><input type="text" class="text" id="gender_1" name="gender_1" value="<?php echo @$row->gender;?>" maxlength="150" size="20" /></td>
						 <td align="left"><input type="text" class="text" id="relationship_1" name="relationship_1" value="<?php echo @$row->relationship;?>" maxlength="150" size="20" /></td>
						 <td align="left"><input type="text" class="text" id="comments_1" name="comments_1" value="<?php echo @$row->comments;?>" maxlength="150" size="20" /></td>
						 <td align="left">
				              <span id="delete_1" style="color:red; cursor: pointer;" onclick="DeleteRow(this);">X</span>
				         </td>
					 </tr>
				</table>
			  </td>
            </tr>			  
		 <tr>
			<td>
				<input class="button" type="button" name="append" value="new entry" onclick="AppendRow('family'); return false;"/>
			</td>
		</tr>
		 </table>
		 <?php
		 /*
            if ($AppUI->isActiveModule('contacts') && $perms->checkModule('contacts', 'view')) 
		{
			echo "<input type='button' class='button' value='".$AppUI->_("enter medical history...")."' onclick='javascript:popFWContacts(selected_fw_contacts_id);' />";
		}*/?>
		 </td>
	  </tr>

	 <tr>
			<td align="left"><?php echo $AppUI->_('TB');?>:</td>
			<td align="left"><?php echo arraySelectRadio($tbTypes, "medical_tb_pulmonary", 'onclick=toggleButtons()', $obj->medical_tb_pulmonary ? $obj->medical_tb_pulmonary : -1, $identifiers ); ?>
			</td>	
	 </tr>
	 <tr>
	 <td align="left"><?php echo $AppUI->_('Body site');?>:</td>
		<td align="left"><input type="text" class="text" name="medical_tb_bodysite" id="medical_tb_bodysite" value="<?php echo $obj->medical_tb_bodysite;?>" maxlength="150" size="20"/></td>
	
      </tr>

      <tr>
			<td align="left"><?php echo $AppUI->_('Courses of treatment(dates)');?>:</td>
	  </tr>

	  <tr>
			  <td align="left">
				...<?php echo $AppUI->_('1st');?>:
			  </td>
			  <td>
				<input type="text" class="text" name="medical_tb_date1" id="medical_tb_date1" value="<?php echo $medical_tb_date1 ? $medical_tb_date1->format( $df ) : "" ;?>" maxlength="150" size="20"/>&nbsp;dd/mm/yyyy
			  </td>
	          </tr>
			  <tr>
			  <td>

			  ...<?php echo $AppUI->_('2nd');?>:
			  </td>
			  <td>
			  <input type="text" class="text" name="medical_tb_date2" id="medical_tb_date2" value="<?php echo $medical_tb_date2 ? $medical_tb_date2->format( $df ) : "" ;?>" maxlength="150" size="20"/>&nbsp;dd/mm/yyyy<br/>
			  </td>
	          </tr>
			 <tr>
			  <td>

			  ...<?php echo $AppUI->_('3rd');?>:
			  </td>
			  <td>

			  <input type="text" class="text" name="medical_tb_date3" id="medical_tb_date3" value="<?php echo $medical_tb_date3 ? $medical_tb_date3->format( $df ) : "" ;?>" maxlength="150" size="20"/>&nbsp;dd/mm/yyyy<br/>
	          </tr>
			 <tr>
	   <tr>
		<td align="left" valign="top">
		<?php echo $AppUI->_('Have there been a recurring history');?><br/>
		<?php echo $AppUI->_('of any of the following?');?>:
		</td>
	  </tr>
		  <tr>
		  <td align="left">...<?php echo $AppUI->_('Pneumonia');?>:</td>
		  <td align="left"><?php echo arraySelectRadio($boolTypes, "medical_history_pneumonia", 'onclick=toggleButtons()', $obj->medical_history_pneumonia ? $obj->medical_history_pneumonia : -1, $identifiers ); ?></td>
		  </tr>
		<tr>
		<td align="left">...<?php echo $AppUI->_('Diarrhoeal episodes');?>:</td><td><?php echo arraySelectRadio($boolTypes, "medical_history_diarrhoea", 'onclick=toggleButtons()', $obj->medical_history_diarrhoea ? $obj->medical_history_diarrhoea : -1, $identifiers ); ?>
		</td>
		</tr>
		<tr>
		<td align="left">...<?php echo $AppUI->_('Skin rashes');?>:</td><td><?php echo arraySelectRadio($boolTypes, "medical_history_skin_rash", 'onclick=toggleButtons()', $obj->medical_history_skin_rash ? $obj->medical_history_skin_rash : -1, $identifiers ); ?>
		</td>
		</tr>
		<tr>
		<td align="left">...<?php echo $AppUI->_('Ear discharge');?>:</td><td><?php echo arraySelectRadio($boolTypes, "medical_history_ear_discharge", 'onclick=toggleButtons()', $obj->medical_history_ear_discharge ? $obj->medical_history_ear_discharge : -1, $identifiers ); ?>
		</td>
		</tr>
		<tr>
		<td align="left">...<?php echo $AppUI->_('Fever ');?>:</td><td><?php echo arraySelectRadio($boolTypes, "medical_history_fever", 'onclick=toggleButtons()', $obj->medical_history_fever ? $obj->medical_history_fever : -1, $identifiers ); ?>
		</td>
		</tr>
		<tr>
		<td align="left">...<?php echo $AppUI->_('Persistent oral thrush');?>:</td><td><?php echo arraySelectRadio($boolTypes, "medical_history_oral_rush", 'onclick=toggleButtons()', $obj->medical_history_oral_rush ? $obj->medical_history_oral_rush : -1, $identifiers ); ?>
		</td>
		</tr>
		<tr>
		<td align="left">...<?php echo $AppUI->_('Mouth ulcers');?>:</td><td><?php echo arraySelectRadio($boolTypes, "medical_history_mouth_ulcers", 'onclick=toggleButtons()', $obj->medical_history_mouth_ulcers ? $obj->medical_history_mouth_ulcers : -1, $identifiers ); ?>
		</td>
		</tr>
	 <tr>
		<td align="left" valign="top"><?php echo $AppUI->_('Malnutrition');?>:</td>
		<td align="left" valign="top"><?php echo arraySelectRadio($malnutritionType, "medical_history_malnutrition", 'onclick=toggleButtons()', $obj->medical_history_malnutrition ? $obj->medical_history_malnutrition : -1, $identifiers ); ?></td>
     </tr>	  
	 <tr>	
	<td align="left" valign="top"><?php echo $AppUI->_('Previous nutritional rehabilitation?');?>:</td>
		<td align="left" valign="top"><?php echo arraySelectRadio($boolTypes, "medical_history_prev_nutrition", 'onclick=toggleButtons()', $obj->medical_history_prev_nutrition ? $obj->medical_history_prev_nutrition : -1, $identifiers ); ?></td>
     </tr>
	 <tr>
		<td align="left" valign="top">...<?php echo $AppUI->_('Other (specify)?');?>:</td>
		<td align="left" valign="top">
		<textarea cols="70" rows="2" class="textarea" name="medical_history_notes"><?php echo @$obj->medical_history_notes;?></textarea>
		</td>
     </tr>
	 
	<tr>
			<td colspan="2" align="left">
				<strong><?php echo $AppUI->_('Medications'); ?><br /></strong>
				<hr width="500" align="left" size=1 />
			</td>
	 </tr>	 
	 <tr>
		<td align="left" valign="top"><?php echo $AppUI->_('ARVs');?>:</td>
		<td align="left" valign="top"><?php echo arraySelectRadio($arvTreatmentTypes, "medical_arv_status", 'onclick=toggleButtons()', $obj->medical_arv_status ? $obj->medical_arv_status : -1, $identifiers ); ?></td>     
	</tr>

 	 <tr>
		<td align="left" valign="top"><?php echo $AppUI->_('ARV regimes');?></td>
	 </tr>
		<tr>
		<td align="left">
		
		...<?php echo $AppUI->_('1st line');?>
		</td>
		<td align="left">
		<input type="text" class="text" name="medical_arv1" id="medical_arv1" value="<?php echo $obj->medical_arv1;?>" maxlength="150" size="20"/>
		</td>
	</tr>
    <tr>  	
		<td align="left">
		...<?php echo $AppUI->_('Started');?>:
		</td>
		<td align="left">
		<input type="text" class="text" name="medical_arv1_startdate" id="medical_arv1_startdate" value="<?php echo $medical_arv1_startdate ? $medical_arv1_startdate->format( $df ) : "" ;?>" maxlength="150" size="20"/>&nbsp;dd/mm/yyyy
		</td>
	</tr>
    <tr>  	

		<td align="left">
		...<?php echo $AppUI->_('Stopped');?>:
		</td>
		<td align="left">
		<input type="text" class="text" name="medical_arv1_enddate" id="medical_arv1_enddate" value="<?php echo $medical_arv1_enddate ? $medical_arv1_enddate->format( $df ) : "" ;?>" maxlength="150" size="20"/>&nbsp;dd/mm/yyyy
	   </td>
	 </tr>		
	 <tr>
		<td align="left">
		
		...<?php echo $AppUI->_('2nd line');?>
		</td>
		<td align="left">
		<input type="text" class="text" name="medical_arv2" id="medical_arv2" value="<?php echo $obj->medical_arv2;?>" maxlength="150" size="20"/>
		</td>
	</tr>
    <tr>  	

		<td align="left">
		...<?php echo $AppUI->_('Started');?>:
		</td>
		<td align="left">
		<input type="text" class="text" name="medical_arv2_startdate" id="medical_arv2_startdate" value="<?php echo $medical_arv2_startdate ? $medical_arv2_startdate->format( $df ) : "" ;?>" maxlength="150" size="20"/>&nbsp;dd/mm/yyyy
		</td>
	</tr>
    <tr>  	
		
		<td align="left">
		...<?php echo $AppUI->_('Stopped');?>:
		</td>
		<td align="left">
		<input type="text" class="text" name="medical_arv2_enddate" id="medical_arv2_enddate" value="<?php echo $medical_arv2_enddate ? $medical_arv2_enddate->format( $df ) : "" ;?>" maxlength="150" size="20"/>&nbsp;dd/mm/yyyy
	   </td>
	</tr> 

	 <tr>
		<td align="left">...<?php echo $AppUI->_('Side effects');?>:</td>
		<td align="left"><input type="text" class="text" name="medical_arv_side_effects" id="medical_arv_side_effects" value="<?php echo $obj->medical_arv_side_effects;?>" maxlength="150" size="20"/></td>
     </tr>
	 <tr>
		<td align="left">...<?php echo $AppUI->_('Adherence');?>:</td>
		<td align="left"><input type="text" class="text" name="medical_arv_adherence" id="medical_arv_adherence" value="<?php echo $obj->medical_arv_adherence;?>" maxlength="150" size="20"/></td>
     </tr>
	 <tr>
		<td>&nbsp;</td>
		 <td align="left">
		 <table>
		   <tr> 
		    <td>
				 <table id="drugs">
					 <th><?php echo $AppUI->_('Drug');?></th>
					 <th><?php echo $AppUI->_('Dose');?></th>
					 <th><?php echo $AppUI->_('Frequency');?></th>
					 <th>&nbsp;</th>
					 <tr>
						 <td align="left"><input type="text" class="text" id="drug_1" name="drug_1" value="<?php echo @$row->medical_medication_drug;?>" maxlength="150" size="20" /></td>
						 <td align="left"><input type="text" class="text" id="dose_1" name="dose_1" value="<?php echo @$row->medical_medication_dose;?>" maxlength="150" size="20" /></td>
						 <td align="left"><input type="text" class="text" id="frequency_1" name="frequency_1" value="<?php echo @$row->medical_medication_frequency;?>" maxlength="150" size="20" /></td>
						 <td align="left">
				              <span id="delete_1" style="color:red; cursor: pointer;" onclick="DeleteRow(this);">X</span>
				         </td>
					 </tr>
				</table>
			  </td>
            </tr>			  
		 <tr>
			<td>
				<input class="button" type="button" name="append" value="new row" onclick="NewRow('drugs'); return false;"/>
			</td>
		</tr>
		 </table>		 
		 <?php
		 /*
            if ($AppUI->isActiveModule('contacts') && $perms->checkModule('contacts', 'view')) 
		{
			echo "<input type='button' class='button' value='".$AppUI->_("enter medical history...")."' onclick='javascript:popFWContacts(selected_fw_contacts_id);' />";
		}*/?>
		 </td>
	  </tr>	 
	 
	<tr>
			<td colspan="2" align="left">
				<strong><?php echo $AppUI->_('Development History and Diet'); ?><br /></strong>
				<hr width="500" align="left" size=1 />
			</td>
	 </tr>
	 <tr>
		<td align="left" valign="top"><?php echo $AppUI->_('Attend School Regularly');?>:</td>
		<td align="left" valign="top">
		<?php echo arraySelectRadio($boolTypes, "medical_school_attendance", 'onclick=toggleButtons()', $obj->medical_school_attendance ? $obj->medical_school_attendance : -1, $identifiers ); ?>
		</td>     
	</tr>
	<tr>
	   <td align="left">
			...<?php echo $AppUI->_('If Yes, class');?>
		</td>
       <td>		
		<input type="text" class="text" name="medical_school_class" id="medical_school_class" value="<?php echo $obj->medical_school_class;?>" maxlength="150" size="20"/> 
       </td>
	</tr>
	 <tr>
		<td align="left" valign="top">...<?php echo $AppUI->_('Progress');?>:</td>
		<td align="left" valign="top"><?php echo arraySelectRadio($educProgressType, "medical_educ_progress", 'onclick=toggleButtons()', $obj->medical_educ_progress ? $obj->medical_educ_progress : -1, $identifiers ); ?></td>     
	</tr>
	<tr>
		<td align="left" valign="top"><?php echo $AppUI->_('Sensory impairment');?>:</td>
	</tr>
		 <tr>
		  <td>
		...<?php echo $AppUI->_('Hearing');?>
		</td>
		<td>
		<?php echo arraySelectRadio($boolTypes, "medical_sensory_hearing", 'onclick=toggleButtons()', $obj->medical_sensory_hearing ? $obj->medical_sensory_hearing : -1, $identifiers ); ?>
	    </td>
        </tr>
        <tr>		
		  <td>

		...<?php echo $AppUI->_('vision');?>:
		</td>
		<td>

		<?php echo arraySelectRadio($boolTypes, "medical_sensory_vision", 'onclick=toggleButtons()', $obj->medical_sensory_vision ? $obj->medical_sensory_vision : -1, $identifiers ); ?>
		</td>
        </tr>
        <tr>		

		<td>

		...<?php echo $AppUI->_('motor ability');?>:
		</td>
		<td>
		<?php echo arraySelectRadio($motorType, "medical_sensory_motor_ability", 'onclick=toggleButtons()', $obj->medical_sensory_motor_ability ? $obj->medical_sensory_motor_ability : -1, $identifiers ); ?>
        </td>
	    </tr>
	    <tr>
		<td>	
		...<?php echo $AppUI->_('speech and language');?>
		</td>
		<td>

		<?php echo arraySelectRadio($boolTypes, "medical_sensory_speech_language", 'onclick=toggleButtons()', $obj->medical_sensory_speech_language ? $obj->medical_sensory_speech_language : -1, $identifiers ); ?>
		</td>
		</tr>
		<tr>
		<td>

		...<?php echo $AppUI->_('social skills');?>:
		</td>
		<td>
		<?php echo arraySelectRadio($boolTypes, "medical_sensory_social_skills", 'onclick=toggleButtons()', $obj->medical_sensory_social_skills ? $obj->medical_sensory_social_skills : -1, $identifiers ); ?>
		</td>
	    </tr>
	    <tr>
		<td align="left"><?php echo $AppUI->_('Number of meals per day');?>:</td>
		<td align="left"><input type="text" class="text" name="medical_meals_per_day" id="medical_meals_per_day" value="<?php echo $obj->medical_meals_per_day;?>" maxlength="150" size="20"/></td>
        </tr>
	 <tr>
		<td align="left"><?php echo $AppUI->_('Types of food (list)');?>:</td>
		<td align="left" valign="top">
		<textarea cols="70" rows="2" class="textarea" name="medical_food_types"><?php echo @$obj->medical_food_types;?></textarea>
		</td>		
     </tr>
	 	 <tr>
		<td align="left" valign="top"><?php echo $AppUI->_('Current complaints?');?>:</td>
		<td align="left" valign="top">
		<textarea cols="70" rows="2" class="textarea" name="medical_current_complaints"><?php echo @$obj->medical_current_complaints;?></textarea>
		</td>
     </tr>	
	<tr>
			<td colspan="2" align="left">
				<strong><?php echo $AppUI->_('Examination'); ?><br /></strong>
				<hr width="500" align="left" size=1 />
			</td>
	 </tr>
	  <tr>
        <td align="left"><?php echo $AppUI->_("Weight (kg)");?>:</td>
        <td align="left">
            <input type="text" class="text" name="medical_weight" value="<?php echo dPformSafe(@$obj->medical_weight);?>" maxlength="30" size="20" />
        </td>
      </tr>
      <tr>
			<td align="left"><?php echo $AppUI->_('Height (cm)');?>:</td>
			<td align="left" valign="top"><input type="text" class="text" name="medical_height" id="medical_height" value="<?php echo $obj->medical_height;?>" maxlength="30" size="20"/></td>
      </tr>
      <tr>
			<td align="left"><?php echo $AppUI->_('z score');?>:</td>
			<td align="left" valign="top"><input type="text" class="text" name="medical_zscore" id="medical_zscore" value="<?php echo $obj->medical_zscore;?>" maxlength="30" size="20"/></td>
      </tr>      <tr>
			<td align="left"><?php echo $AppUI->_('MUAC (mm) ');?>:</td>
			<td align="left" valign="top"><input type="text" class="text" name="medical_muac" id="medical_muac" value="<?php echo $obj->medical_muac;?>" maxlength="30" size="20"/></td>
      </tr>
      <tr>
			<td align="left"><?php echo $AppUI->_('Head Circum (cm)');?>:</td>
			<td align="left" valign="top"><input type="text" class="text" name="medical_hc" id="medical_hc" value="<?php echo $obj->medical_hc;?>" maxlength="30" size="20"/></td>
      </tr>
      <tr>
			<td align="left"><?php echo $AppUI->_('Looks');?>:</td>
			<td align="left" valign="top"><?php echo arraySelectRadio($conditionType, "medical_condition", 'onclick=toggleButtons()', $obj->medical_condition ? $obj->medical_condition : -1, $identifiers ); ?></td>     
      </tr>
      <tr>
			<td align="left"><?php echo $AppUI->_('Temperature (Celcius)');?>:</td>
			<td align="left" valign="top"><input type="text" class="text" name="medical_temp" id="medical_temp" value="<?php echo $obj->medical_temp;?>" maxlength="30" size="20"/></td>
      </tr>
      <tr>
			<td align="left" valign="top"><?php echo $AppUI->_('Identify');?>:</td>
			<td align="left" >
			<?php 
			echo arraySelectCheckbox($examinationType, "medical_conditions[]", NULL, $medical_conditions); 
			?>
			</td>     
      </tr>
      <tr>
			<td align="left"><?php echo $AppUI->_('Dehydration');?>:</td>
			<td align="left" valign="top"><?php echo arraySelectRadio($dehydrationType, "medical_dehydration", 'onclick=toggleButtons()', $obj->medical_dehydration ? $obj->medical_dehydration : -1, $identifiers ); ?></td>     
      </tr>
      <tr>
			<td align="left"><?php echo $AppUI->_('Parotids');?>:</td>
			<td align="left" valign="top"><?php echo arraySelectRadio($enlargementType, "medical_parotids", 'onclick=toggleButtons()', $obj->medical_parotids ? $obj->medical_parotids : -1, $identifiers ); ?></td>     
      </tr>
      <tr>
			<td align="left"><?php echo $AppUI->_('Lymph nodes');?>:</td>
			<td align="left" valign="top"><?php echo arraySelectRadio($lymphType, "medical_lymph", 'onclick=toggleButtons()', $obj->medical_lymph ? $obj->medical_lymph : -1, $identifiers ); ?></td>     
      </tr>
      <tr>
			<td align="left"><?php echo $AppUI->_('Eyes');?>:</td>
			<td align="left" valign="top"><?php echo arraySelectRadio($eyeType, "medical_eyes", 'onclick=toggleButtons()', $obj->medical_eyes ? $obj->medical_eyes : -1, $identifiers ); ?></td>     
      </tr>
      <tr>
			<td align="left"><?php echo $AppUI->_('Ear discharge');?>:</td>
			<td align="left" valign="top"><?php echo arraySelectRadio($earType, "medical_ear_discharge", 'onclick=toggleButtons()', $obj->medical_ear_discharge ? $obj->medical_ear_discharge : -1, $identifiers ); ?></td>     
      </tr>
      <tr>
			<td align="left"><?php echo $AppUI->_('Throat');?>:</td>
			<td align="left" valign="top"><?php echo arraySelectRadio($throatType, "medical_throat", 'onclick=toggleButtons()', $obj->medical_throat ? $obj->medical_throat : -1, $identifiers ); ?></td>     
      </tr>
      <tr>
			<td align="left" valign="top"><?php echo $AppUI->_('Mouth');?>:</td>
	  </tr>
			 <tr>
			  <td>
				...<?php echo $AppUI->_('thrush');?>:
			  </td>
			  <td>	
				<?php echo arraySelectRadio($boolTypes, "medical_mouth_thrush", 'onclick=toggleButtons()', $obj->medical_mouth_thrush ? $obj->medical_mouth_thrush : -1, $identifiers ); ?>
			</td>
				</tr>
				<tr>
				<td>		
			...<?php echo $AppUI->_('ulcers');?>:
			</td>
			  <td>	
			<?php echo arraySelectRadio($boolTypes, "medical_mouth_ulcers", 'onclick=toggleButtons()', $obj->medical_mouth_ulcers ? $obj->medical_mouth_ulcers : -1, $identifiers ); ?>
			</td>
				</tr>
				<tr>
				<td>
			...<?php echo $AppUI->_('teeth');?>:
			</td>
			<td>
			<?php echo arraySelectRadio($teethType, "medical_mouth_teeth", 'onclick=toggleButtons()', $obj->medical_mouth_teeth ? $obj->medical_mouth_teeth : -1, $identifiers ); ?>
			</td>     
			 </tr>
	  <tr>
			<td align="left"><?php echo $AppUI->_('Skin');?>:</td>
	  </tr>
      <tr> 	  
			<td align="left">
			...<?php echo $AppUI->_('Old lesions');?>:
			</td>
			<td align="left">
			<input type="text" class="text" name="medical_oldlesions" id="medical_oldlesions" value="<?php echo $obj->medical_oldlesions;?>" maxlength="30" size="20"/>
			</td>
		</tr>
		<tr>  	
			<td align="left">
			...<?php echo $AppUI->_('Current lesions');?>:
			</td>

			<td align="left">
			<input type="text" class="text" name="medical_currentlesions" id="medical_currentlesions" value="<?php echo $obj->medical_currentlesions;?>" maxlength="30" size="20"/>
			</td>     
      </tr>
	<tr>
			<td colspan="2" align="left">
				<strong><?php echo $AppUI->_('Respiratory and Cardiovascular'); ?><br /></strong>
				<hr width="500" align="left" size=1 />
			</td>
	 </tr>
	 <tr>
			  <td align="left">
			<?php echo $AppUI->_('heart rate');?>:
			  </td>
			   <td align="left">
			    <input type="text" class="text" name="medical_heartrate" id="medical_heartrate" value="<?php echo $obj->medical_heartrate;?>" maxlength="30" size="20"/>
			</td>
			      </tr>
	<tr>
			<td align="left">
			<?php echo $AppUI->_('recession');?>:
			</td>
			<td align="left">
			<?php echo arraySelectRadio($boolTypes, "medical_recession", 'onclick=toggleButtons()', $obj->medical_recession ? $obj->medical_recession : -1, $identifiers ); ?>
			</td>
      </tr>
	<tr>		
		<td>
			<?php echo $AppUI->_('percussion');?>:
			</td>
			<td>
			<?php echo arraySelectRadio($percussionType, "medical_percussion", 'onclick=toggleButtons()', $obj->medical_percussion ? $obj->medical_percussion : -1, $identifiers ); ?>
			</td>
      </tr>
	  <tr>		
	 	<td>
			...<?php echo $AppUI->_('location');?>:
			</td>
			<td>
			<input type="text" class="text" name="medical_location" id="medical_location" value="<?php echo $obj->medical_location;?>" maxlength="30" size="20"/>
			</td>
	      </tr>	  
		  <tr>		
	 	 <td>
			<?php echo $AppUI->_('breath sounds');?>:
			</td>
			<td>
			  <?php echo arraySelectRadio($breathType, "medical_breath_sounds", 'onclick=toggleButtons()', $obj->medical_breath_sounds ? $obj->medical_breath_sounds : -1, $identifiers ); ?>
			</td>
	      </tr>	  
		  <tr>		

			<td>
			...<?php echo $AppUI->_('location');?>:
			</td>
			<td>
			<input type="text" class="text" name="medical_breathlocation" id="medical_breathlocation" value="<?php echo $obj->medical_breathlocation;?>" maxlength="30" size="20"/>
			</td>
	      </tr>
	  
      </tr>
	  <tr>
			<td align="left">
			<?php echo $AppUI->_('added sounds');?>:
			</td>
			<td>
			<?php echo arraySelectRadio($soundsType, "medical_other_sounds", 'onclick=toggleButtons()', $obj->medical_other_sounds ? $obj->medical_other_sounds : -1, $identifiers ); ?>
			</td>
	 </tr>
      </tr>
	  <tr>
			<td>

	  ...<?php echo $AppUI->_('location');?>:
			</td>
			<td>
	  
	  <input type="text" class="text" name="medical_soundlocation" id="medical_soundlocation" value="<?php echo $obj->medical_soundlocation;?>" maxlength="30" size="20"/>
			</td>     
      </tr>
	  <tr>
			<td align="left">
			<?php echo $AppUI->_('pulse rate');?>:
			</td>
			<td>

			<input type="text" class="text" name="medical_pulserate" id="medical_pulserate" value="<?php echo $obj->medical_pulserate;?>" maxlength="30" size="20"/>
			</td>
      </tr>
	  <tr>

			<td>

			<?php echo $AppUI->_('apex beat');?>:
			</td>
			<td>

			<?php echo arraySelectRadio($apexType, "medical_apex_beat", 'onclick=toggleButtons()', $obj->medical_apex_beat ? $obj->medical_apex_beat : -1, $identifiers ); ?>
			</td>
      </tr>
	  <tr>

			<td>

			<?php echo $AppUI->_('Precordial activity');?>:
			</td>
			<td>

			<?php echo arraySelectRadio($precordialType, "medical_precordial", 'onclick=toggleButtons()', $obj->medical_precordial ? $obj->medical_precordial : -1, $identifiers ); ?>
			</td>     
      </tr>
	  <tr>
			<td align="left" valign="top">
			
			<?php echo $AppUI->_('femoral pulses');?>:
						</td>
			<td>
			<?php echo arraySelectRadio($femoralType, "medical_femoral", 'onclick=toggleButtons()', $obj->medical_femoral ? $obj->medical_femoral : -1, $identifiers ); ?>
						</td>
				      </tr>	  
		  <tr>		
			
			<td>
			<?php echo $AppUI->_('heart');?>:
						</td>
			<td>
			<?php echo arraySelectRadio($heartSoundType, "medical_heart_sound", 'onclick=toggleButtons()', $obj->medical_heart_sound ? $obj->medical_heart_sound : -1, $identifiers ); ?>
						</td>
	      </tr>	  
		  <tr>		

						<td>
			<?php echo $AppUI->_('type');?>:
						</td>
			<td>
			<input type="text" class="text" name="medical_heart_type" id="medical_heart_type" value="<?php echo $obj->medical_heart_type;?>" maxlength="30" size="20"/>
			</td>     
      </tr>
	<tr>
			<td colspan="2" align="left">
				<strong><?php echo $AppUI->_('Abdomen'); ?><br /></strong>
				<hr width="500" align="left" size=1 />
			</td>
	 </tr>

	  <tr>
			<td align="left" valign="top">
			<?php echo $AppUI->_('distended');?>:
			</td>
			<td>			
			<?php echo arraySelectRadio($boolTypes, "medical_abdomen_distended", 'onclick=toggleButtons()', $obj->medical_abdomen_distended ? $obj->medical_abdomen_distended : -1, $identifiers ); ?>
				</td>
      </tr>
	<tr>
		<td>		
			<?php echo $AppUI->_('feel');?>:
		</td>
		<td>
			<?php echo arraySelectRadio($feelType, "medical_adbomen_feel", 'onclick=toggleButtons()', $obj->medical_adbomen_feel ? $obj->medical_adbomen_feel : -1, $identifiers ); ?>
						</td>
      </tr>
	<tr>		
		<td>
			<?php echo $AppUI->_('tender');?>:
		</td>
		<td>		
		<?php echo arraySelectRadio($boolTypes, "medical_abdomen_tender", 'onclick=toggleButtons()', $obj->medical_abdomen_tender ? $obj->medical_abdomen_tender : -1, $identifiers ); ?>
						</td>
	      </tr>
	<tr>		
			<td>
			<?php echo $AppUI->_('fluid');?>:
						</td>
			<td>
			<?php echo arraySelectRadio($boolTypes, "medical_abdomen_fluid", 'onclick=toggleButtons()', $obj->medical_abdomen_fluid ? $obj->medical_abdomen_fluid : -1, $identifiers ); ?>
			</td>     
      </tr>
	  <tr>
			<td align="left" valign="top">
			
			<?php echo $AppUI->_('Liver (cm below costal margin)');?>:
						</td>
			<td>
			<input type="text" class="text" name="medical_liver_costal" id="medical_liver_costal" value="<?php echo $obj->medical_liver_costal;?>" maxlength="30" size="20"/>
						</td>
      </tr>
	<tr>
						<td>
			<?php echo $AppUI->_('Spleen (cm below costal margin)');?>:
						</td>
		
		<td>
			<input type="text" class="text" name="medical_spleen_costal" id="medical_spleen_costal" value="<?php echo $obj->medical_spleen_costal;?>" maxlength="30" size="20"/>
			</td>     
      </tr>
	  <tr>
			<td align="left" valign="top">
			
			<?php echo $AppUI->_('Masses (specify)');?>:
			</td>
			<td>		  
		  <input type="text" class="text" name="medical_masses" id="medical_masses" value="<?php echo $obj->medical_masses;?>" maxlength="30" size="20"/>
						</td>
      </tr>
	<tr>
						<td>
		
		<?php echo $AppUI->_('Umbilical hernia');?>:
						</td>
			<td>
			<?php echo arraySelectRadio($umbilicalType, "medical_umbilical_hernia", 'onclick=toggleButtons()', $obj->medical_umbilical_hernia ? $obj->medical_umbilical_hernia : -1, $identifiers ); ?>
			</td>     
      </tr>


	  <tr>
	  		<td align="left"  valign="top"><?php echo $AppUI->_('Genitalia');?>:</td>
	  </tr>
			 <tr>
			  <td align="left">
				...<?php echo $AppUI->_('Male testes ');?>:
			</td>
		
				<td align="left">
					<?php echo arraySelectRadio($palpableType, "medical_testes", 'onclick=toggleButtons()', $obj->medical_testes ? $obj->medical_testes : -1, $identifiers ); ?>
				</td>
			</tr>
			<tr>		
				<td align="left">
					&nbsp;
				</td>
				<td align="left">
					<?php echo arraySelectRadio($directionType, "medical_which_testes", 'onclick=toggleButtons()', $obj->medical_which_testes ? $obj->medical_which_testes : -1, $identifiers ); ?>
				</td>
			</tr>
			<tr>
				<td align="left">
				...<?php echo $AppUI->_('penis');?>:
				</td>
				<td align="left">
				<?php echo arraySelectRadio($penisTypes, "medical_penis", 'onclick=toggleButtons()', $obj->medical_penis ? $obj->medical_penis : -1, $identifiers ); ?>
					
				</td>
			</tr>
			<tr>		
				<td align="left">
					...<?php echo $AppUI->_('OR Female');?>:
				</td>
				<td align="left">
				<?php echo arraySelectRadio($femaleConditionType, "medical_genitals_female", 'onclick=toggleButtons()', $obj->medical_genitals_female ? $obj->medical_genitals_female : -1, $identifiers ); ?>
				</td>     
			</tr>
			<tr>
			<td>&nbsp;</td>
			<td>		  
		  <input type="text" class="text" name="medical_genitals_female_notes" id="medical_genitals_female_notes" value="<?php echo $obj->medical_genitals_female_notes;?>" maxlength="30" size="20"/>
			</td>			
			</tr>
      <tr>
			<td align="left"><?php echo $AppUI->_('Pubertal development');?>:</td>
			
			<td align="left" valign="top">
			<?php echo arraySelectRadio($developmentType, "medical_pubertal", 'onclick=toggleButtons()', $obj->medical_pubertal ? $obj->medical_pubertal : -1, $identifiers ); ?></td>     
      </tr>
	  	<tr>
			<td colspan="2" align="left">
				<strong><?php echo $AppUI->_('Central Nervous System and Musculoskeletal'); ?><br /></strong>
				<hr width="500" align="left" size=1 />
			</td>
	 </tr>
	  <tr>
			<td align="left" valign="top">
			
			<?php echo $AppUI->_('Gait');?>:
						</td>
			<td>
			<input type="text" class="text" name="medical_gait" id="medical_gait" value="<?php echo $obj->medical_gait;?>" maxlength="30" size="20"/>
						</td>
      </tr>
	<tr>		
		<td>
			<?php echo $AppUI->_('Hand use');?>:
						</td>
		
		<td>
			<input type="text" class="text" name="medical_handuse" id="medical_handuse" value="<?php echo $obj->medical_handuse;?>" maxlength="30" size="20"/>
			</td>     
      </tr>	
	  <tr>
			<td align="left" valign="top">
			
			<?php echo $AppUI->_('Weakness');?>:
						</td>
			<td>
			<input type="text" class="text" name="medical_weakness" id="medical_weakness" value="<?php echo $obj->medical_weakness;?>" maxlength="30" size="20"/>
						</td>
      </tr>
	<tr>
						<td>
			<?php echo $AppUI->_('Tone');?>:
					</td>
			<td>	
			<?php echo arraySelectRadio($toneType, "medical_tone", 'onclick=toggleButtons()', $obj->medical_tone ? $obj->medical_tone : -1, $identifiers ); ?>
			</td>     
      </tr>	  
      <tr>
	  		<td align="left" valign="top"><?php echo $AppUI->_('Tendon reflexes');?>:</td>
					
		</tr>
			<tr>
			<td align="left">
			...<?php echo $AppUI->_('legs');?>:
			</td>
  
			<td align="left">
				<?php echo arraySelectRadio($tendonLegsType, "medical_tendon_legs", 'onclick=toggleButtons()', $obj->medical_tendon_legs ? $obj->medical_tendon_legs : -1, $identifiers ); ?>
			</td>
			</tr>
			<tr>
			<td align="left">
			...<?php echo $AppUI->_('arms');?>:
						</td>
			<td align="left">
			<?php echo arraySelectRadio($tendonArmsType, "medical_tendon_arms", 'onclick=toggleButtons()', $obj->medical_tendon_arms ? $obj->medical_tendon_arms : -1, $identifiers ); ?>
			</td>
			</tr>
     
	  <tr>
			<td align="left" valign="top">
			
			<?php echo $AppUI->_('Abnormal movements');?>:
						</td>
			<td align="left">
			<input type="text" class="text" name="medical_abnormal_movts" id="medical_abnormal_movts" value="<?php echo $obj->medical_abnormal_movts;?>" maxlength="30" size="20"/>
						</td>

	   </tr>
	   
	  <tr>
			<td align="left" valign="top">
			<?php echo $AppUI->_('Joints range of movement impaired');?>:
			</td>
			<td align="left">
			<?php echo arraySelectRadio($boolTypes, "medical_movts_impaired", 'onclick=toggleButtons()', $obj->medical_movts_impaired ? $obj->medical_movts_impaired : -1, $identifiers ); ?>
					</td>
    </tr>
	<tr>			
	<td align="left">	
			...<?php echo $AppUI->_('specify');?>:
						</td>
			<td>
			<input type="text" class="text" name="medical_movts_impaired_desc" id="medical_movts_impaired_desc" value="<?php echo $obj->medical_movts_impaired_desc;?>" maxlength="30" size="20"/>
			</td>     
      </tr>	  
	  <tr>
			<td align="left" valign="top">
			<?php echo $AppUI->_('Joints swelling');?>:
			</td>
			<td>
			<?php echo arraySelectRadio($boolTypes, "medical_joints_swelling", 'onclick=toggleButtons()', $obj->medical_joints_swelling ? $obj->medical_joints_swelling : -1, $identifiers ); ?>
			</td>
	  </tr>
	  <tr>			
			<td>
			...<?php echo $AppUI->_('specify');?>:
			</td>
			<td>
			<input type="text" class="text" name="medical_joints_swelling_desc" id="medical_joints_swelling_desc" value="<?php echo $obj->medical_joints_swelling_desc;?>" maxlength="30" size="20"/>
			</td>     
      </tr>	  
	  
	  <tr>
			<td align="left"><?php echo $AppUI->_('Motor');?>:</td>
			<td align="left" valign="top">
			<?php echo arraySelectRadio($motorType, "medical_motor", 'onclick=toggleButtons()', $obj->medical_motor ? $obj->medical_motor : -1, $identifiers ); ?>
			</td>     
      </tr>

	 <tr>
	 
	 	<td align="left" valign="top"><?php echo $AppUI->_('Summary');?>:</td>
		<td valign="top">
		<textarea cols="70" rows="2" class="textarea" name="medical_musc_notes"><?php echo @$obj->medical_musc_notes;?></textarea>
		</td>
     </tr>
	<tr>
			<td colspan="2" align="left">
				<strong><?php echo $AppUI->_('Management Plan'); ?><br /></strong>
				<hr width="500" align="left" size=1 />
			</td>
	 </tr>	 
	 
	<tr>
			<td align="left" valign="top">
			<?php echo $AppUI->_('HIV status');?>:
						</td>
			<td>

			<?php echo arraySelectRadio($managementhivStatus, "medical_hiv_status", 'onclick=toggleButtons()', $obj->medical_hiv_status ? $obj->medical_hiv_status : -1, $identifiers ); ?>
						</td>
    </tr>
	<tr>		
						<td>
			<?php echo $AppUI->_('CD4');?>:
						</td>
			<td>
			<input type="text" class="text" name="medical_cd4" id="medical_cd4" value="<?php echo $obj->medical_cd4;?>" maxlength="30" size="20"/>
						</td>
	    </tr>
	<tr>				
			<td>
			<?php echo $AppUI->_('CD4%');?>:
						</td>
			<td>
			<input type="text" class="text" name="medical_cd4_percentage" id="medical_cd4_percentage" value="<?php echo $obj->medical_cd4_percentage;?>" maxlength="30" size="20"/>
			</td>     
      </tr>	 
	  <tr>
			<td align="left" valign="top">
			<?php echo $AppUI->_('Clinical stage (WHO)');?>:
						</td>
			<td>
			<input type="text" class="text" name="medical_who_medical_stage" id="medical_who_medical_stage" value="<?php echo $obj->medical_who_medical_stage;?>" maxlength="30" size="20"/>
						</td>
    </tr>
	<tr>				
		<td>
			
			<?php echo $AppUI->_('Immunological stage');?>:
						</td>
			<td>
			<input type="text" class="text" name="medical_immuno_stage" id="medical_immuno_stage" value="<?php echo $obj->medical_immuno_stage;?>" maxlength="30" size="20"/>
			</td>     
      </tr>	 
	  <tr>
			<td align="left" valign="top">
			<?php echo $AppUI->_('Tests');?>:
						</td>
			<td>
			<input type="text" class="text" name="medical_tests" id="medical_tests" value="<?php echo $obj->medical_tests;?>" maxlength="30" size="20"/>
						</td>
			    </tr>
	       <tr>		
			<td>
			
			<?php echo $AppUI->_('Referral to');?>:
						</td>
			<td>
			<input type="text" class="text" name="medical_referral" id="medical_referral" value="<?php echo $obj->medical_referral;?>" maxlength="30" size="20"/>
			</td>     
      </tr>	 
	 <tr>
	 
	 	<td align="left" valign="top"><?php echo $AppUI->_('Treatment');?>:</td>
		<td valign="top">
		<textarea cols="70" rows="2" class="textarea" name="medical_treat_notes"><?php echo @$obj->medical_treat_notes;?></textarea>
		</td>
     </tr>
	 </table>
 </td>
</tr>
	<tr>
	<td><input type="button" value="<?php echo $AppUI->_('back');?>" class="button" onClick="javascript:history.back(-1);" /></td>
	<td colspan="2" align="right"><input type="button" value="<?php echo $AppUI->_('submit');?>" class="button" onClick="submitIt()" /></td>
    </tr>	
</table>

</form>


