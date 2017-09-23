<?php
global $AppUI,$dPconfig,$loadFromTab, $tab;
global $obj, $client_id, $url,$can_edit_contact_information;
global $convert;

//require_once ($AppUI->getModuleClass('counselling'));


$perms = & $AppUI->acl();

$canEdit = true;
$msg = '';
//$row = new CClientCounsellingInfo();

$boolTypes = dPgetSysVal('YesNo');

$riskLevels = dPgetSysVal('RiskLevel');
$riskLevels = arrayMerge(array(-1=>'-Select Risk Level-'),$riskLevels );

$schoolLevels = dPgetSysVal('EducationLevel');
$schoolLevels = arrayMerge(array(-1=>'-Select Education Level-'),$schoolLevels );


$provinceList = dPgetSysVal('Province');
$provinceList = arrayMerge(array(-1=>'-Select Province-'),$provinceList );


//load all sales reps
$q  = new DBQuery;
$q->addTable('contacts', 'c');
$q->addQuery('c.contact_id');
$q->addQuery('CONCAT_WS(", ",c.contact_last_name,c.contact_first_name)');
$q->innerJoin('client_contacts', 'b', 'b.client_contacts_contact_id = c.contact_id');
$q->addWhere('b.client_contacts_contact_type = 13');
$q->addOrder('c.contact_first_name');

//load contacts
$chw_contacts = arrayMerge(array(0=> '-Select CHW -'),$q->loadHashList());
$q->clear();
$q->addTable('contacts', 'c');
$q->addQuery('c.contact_id');
$q->addQuery('CONCAT_WS(", ",c.contact_last_name,c.contact_first_name)');
$q->innerJoin('client_contacts', 'b', 'b.client_contacts_contact_id = c.contact_id');
$q->addWhere('b.client_contacts_contact_type = 14');
$q->addOrder('c.contact_first_name');


$shw_contacts = arrayMerge(array(0=> '-Select SHW -'),$q->loadHashList());

$date_reg = date("Y-m-d");
$entry_date = intval( $date_reg) ? new CDate( dPgetParam($_REQUEST, "admission_entry_date", date("Y-m-d") ) ) : null;
$df = $AppUI->getPref('SHDATEFORMAT');


/*
if (isset($client_id) && $client_id > 0)
{
	$sql = 'SELECT * FROM client_admission_info WHERE admission_client_id = ' . $client_id;

	//$admission_info_id  = db_exec($sql);
	//$row = new CClientCounsellingInfo();

	if (!db_loadObject( $sql, $row) && !isset($convert))
	{
		$AppUI->setMsg('Counselling Info');
		$AppUI->setMsg("invalidID", UI_MSG_ERROR, true);
		$AppUI->redirect();
	}
}
//load building solution name
//var_dump($row->admission_building_solution_id);
//var_dump($building_solution_id);

if (($building_solution_id > 0 ))
{
	$q = new DBQuery;
	$q->addTable ('building_solution');
	$q->addQuery('building_solution_location');
	$q->addWhere("building_solution_id = $building_solution_id");
	$bs_name = $q->loadResult();
}

//load types of networks 
$q  = new DBQuery;
$q->addTable('customer_ntwk_types');
$q->addQuery('typ_id');
$q->addQuery('typ_desc');
$q->addOrder('typ_desc');
$networkTypes = arrayMerge(array(0=>'Select network type'),$q->loadHashList());

//load vpn types

$vpnTypes = dPgetSysVal('VPNTypes');

//load network services
$q->clear();
$q->addTable('ntwk_service_types');
$q->addQuery('ntwk_service_type_id');
$q->addQuery('ntwk_service_type_desc');
$serviceTypes = $q->loadHashList();
$identifiers = array(0=>'', 1=>'on_bs', 2=>'not_on_bs');*/
?>
<script language="javascript">
var selected_fw_contacts_id = "<?php echo $row->admission_firewall_contact; ?>";
var selected_vpn_contacts_id = "<?php echo $row->admission_vpn_contact; ?>";
var client_id = "<?php echo $row->admission_client_id;?>";
var selected_bs_id = "<?php echo $building_solution_id > 0 ? $building_solution_id : $row->admission_building_solution_id; ?>";
var bs_name = "<?php echo $bs_name? $bs_name: ""?>";

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
	
	admission_firewall_contact = document.getElementById('admission_firewall_contact');
	admission_firewall_contact.value = contact_id_string;
	
	selected_fw_contacts_id = contact_id_string;
}

function setVPNContacts(contact_id_string)
{

	if(!contact_id_string)
	{
		contact_id_string = "";
	}
	
	admission_vpn_contact = document.getElementById('admission_vpn_contact');
	admission_vpn_contact.value = contact_id_string;
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

var contact_unique_update = document.socialInfoFrm.insert_id.value;
function popRelatives() 
{
	window.open('./index.php?m=public&a=relative_adder&type_ui_active=1&dialog=1&contact_unique_update='+contact_unique_update, 'contacts','height=600,width=800,resizable,scrollbars=yes');
}
function popCaregivers() 
{
	window.open('./index.php?m=public&a=caregiver_adder&type_ui_active=1&dialog=1&contact_unique_update='+contact_unique_update, 'contacts','height=600,width=800,resizable,scrollbars=yes');
}
</script>
<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">
<form name="admissionInfoFrm" action="?m=clients&a=addedit&client_id=<?php echo $client_id; ?>" method="post">
  <input type="hidden" name="dosql" value="do_newclient_aed"/>
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="admission_unique_update" value="<?php echo uniqid("");?>" />
  <input type="hidden" name="admission[admission_info_id]" value="<?php echo $row->admission_info_id;?>" />
  <input type="hidden" name="admission[admission_client_id]" value="<?php echo $client_id;?>" />
  <input type="hidden" name="admission[admission_officer]" id="admission_officer" value="<?php echo @$row->admission_officer;?>" />
 
 
<tr>
    <td colspan="2" valign="top">
      <table border="0" cellpadding = "1" cellspacing="1">

		<tr>
			 <td align="left" nowrap><?php echo $AppUI->_('Entry Date');?>: </td>
			<td align="right">
				<input type="hidden" name="log_entry_date" value="<?php echo $entry_date ? $entry_date->format( FMT_TIMESTAMP_DATE ) : "" ;?>" />
				<input type="text" name="admission_entry_date" value="<?php echo $entry_date ? $entry_date->format( $df ) : "" ;?>" maxlength="150" size="20" class="text" readonly disabled="disabled" />
			</td>
		   </tr>
    
      <tr>
         <td align="left" nowrap><?php echo $AppUI->_('School Level');?>:</td>
		 <td align="left">
	   <?php echo arraySelectRadio($schoolLevels, "admission[admission_school_level]", 'onclick=toggleButtons()', $row->school_level ? $row->school_level : -1, $identifiers ); ?>
		 </td>
      </tr>            
	  <tr>
         <td align="left" nowrap><?php echo $AppUI->_('If not attending,why');?>:</td>
		 <td align="right">
	    &nbsp;&nbsp;<input type="text" class="text" name="admission[admission_reason_not_attending]" value="<?php echo @$row->admission_total_orphan;?>" maxlength="150" size="20" />
		 </td>
         <td align="left" nowrap><?php echo $AppUI->_('Current Residence');?>:</td>
		 <td align="right">
	    &nbsp;&nbsp;<input type="text" class="text" name="admission[admission_total_orphan]" value="<?php echo @$row->admission_total_orphan;?>" maxlength="150" size="20" />
		 </td>

	 </tr>      
	  <tr>
      </tr>
		  <tr>
			<td align="left" width="100"><?php echo $AppUI->_('Location');?>:</td>
			<td nowrap align="right">
				<input type="text" class="text" name="client_clinic_name" value="<?php 
					echo $clinic_detail['clinic_name'];
					?>" maxlength="100" size="25" />
				<input type="button" class="button" value="<?php echo $AppUI->_('select center...');?>..." onclick="popClinic()" />
				<input type='hidden' name='client_clinic' value="<?php echo $clinic_detail['clinic_id']; ?>">
				</td>
		</tr> 
        <tr>
			<td align="left" nowrap><?php echo $AppUI->_('Rural Home');?>:</td>
			<td align="right">
			<table>
			<tr>
			  <td align="left" nowrap><?php echo $AppUI->_('Province');?>:</td>
		 <td align="right">
	    &nbsp;&nbsp;<input type="text" class="text" name="admission[admission_province]" value="<?php echo @$row->admission_province;?>" maxlength="150" size="20" />
		 </td>
		   </tr>
			<tr>
			  <td align="left" nowrap><?php echo $AppUI->_('District');?>:</td>
		 <td align="right">
	    &nbsp;&nbsp;<input type="text" class="text" name="admission[admission_district]" value="<?php echo @$row->admission_district;?>" maxlength="150" size="20" />
		 </td>
		   </tr>
			<tr>
			  <td align="left" nowrap><?php echo $AppUI->_('Village');?>:</td>
		 <td align="right">
	    &nbsp;&nbsp;<input type="text" class="text" name="admission[admission_village]" value="<?php echo @$row->admission_village;?>" maxlength="150" size="20" />
		 </td>
		   </tr>
			</table>
 			</td>
		</tr>
		<tr>
			<td align="left" nowrap><?php echo $AppUI->_('SHW Rep.');?>:</td>
			<td align="right">
			<?php echo arraySelect( $shw_contacts, 'client[shw_contact]', 'size="1" class="text"', @$obj->shw_contact ); ?>
 			</td>
		</tr>	  

	      <tr>
			<td colspan="2" align="left">
				<?php echo $AppUI->_('Caregiver Information'); ?><br />
				<hr width="500" align="left" size="1" />
			</td>
	 </tr>
	   <tr>
         <td align="left" nowrap><?php echo $AppUI->_('Father');?>:</td>
		 <td align="right">
		 <table>
		 <tr>
		   <td><?php echo $AppUI->_('First Name');?>:</td>
		   <td><input type="text" class="text" name="admission[admission_father_fname]" value="<?php echo @$row->admission_father_fname;?>" maxlength="150" size="20" /></td>
		 </tr>		 
		 <tr>
		   <td><?php echo $AppUI->_('Last Name');?>:</td>
		   <td><input type="text" class="text" name="admission[admission_father_lname]" value="<?php echo @$row->admission_father_lname;?>" maxlength="150" size="20" /></td>
		 </tr>		 
		 <tr>
		   <td><?php echo $AppUI->_('Age');?>:</td>
		   <td><input type="text" class="text" name="admission[admission_father_age]" value="<?php echo @$row->admission_father_age;?>" maxlength="150" size="20" /></td>
		 </tr>
		 <tr>
		   <td><?php echo $AppUI->_('Status');?>:</td>
		   <td>
		   <table>
		   <tr>
		   <td align="left">
	   <?php echo arraySelectRadio($caregiverStatus, "admission[admission_father_status]", 'onclick=toggleButtons()', $row->admission_father_status ? $row->admission_father_status  : -1, $identifiers ); ?>
		 </td>
		   </tr>
		   <tr>
			<td><?php echo $AppUI->_('Raising Child');?>:</td>
		   <td align="left">
	   <?php echo arraySelectRadio($boolTypes, "admission[admission_father_raising_child]", 'onclick=toggleButtons()', $row->admission_father_raising_child ? $row->admission_father_raising_child  : -1, $identifiers ); ?>
		 </td>
		   </tr>
		   <tr>
			<td><?php echo $AppUI->_('Marital status');?>:</td>
		   <td align="left">
	   <?php echo arraySelectRadio($maritalStatus, "admission[admission_father_marital_status]", 'onclick=toggleButtons()', $row->admission_father_marital_status ? $row->admission_father_marital_status  : -1, $identifiers ); ?>
		 </td>
		   </tr>
		   </table>
		   </td>
		 </tr>
		 <tr>
		   <td><?php echo $AppUI->_('Education Level');?>:</td>
		   
		   <td>
		   <?php echo arraySelectRadio($educLevels, "admission[admission_father_educ_level]", 'onclick=toggleButtons()', $row->admission_father_educ_level ? $row->admission_father_educ_level  : -1, $identifiers ); ?>
		   </td>
		 </tr>
		 <tr>
		   <td><?php echo $AppUI->_('Employment');?>:</td>
		   <td>
		   <?php echo arraySelectRadio($employmentTypes, "admission[admission_father_employment]", 'onclick=toggleButtons()', $row->admission_father_employment ? $row->admission_father_employment  : -1, $identifiers ); ?>
		   </td>
		 </tr>
		 <tr>
		   <td><?php echo $AppUI->_('Monthly Income');?>:</td>
		   <td>
		   <?php echo arraySelectRadio($incomeLevels, "admission[admission_father_income]", 'onclick=toggleButtons()', $row->admission_father_income ? $row->admission_father_income  : -1, $identifiers ); ?>
		   </td>
		 </tr>
		 <tr>
		   <td><?php echo $AppUI->_('Other Details');?>:</td>
		   <td>
		   <table>
		    <tr>
			  <td><?php echo $AppUI->_('ID #');?>:</td>
			  <td><input type="text" class="text" name="admission[admission_father_idno]" value="<?php echo @$row->admission_father_idno;?>" maxlength="150" size="20" /></td>
			</tr>
		    <tr>
			  <td><?php echo $AppUI->_('Mobile #');?>:</td>
			  <td><input type="text" class="text" name="admission[admission_father_mobile]" value="<?php echo @$row->admission_father_mobile;?>" maxlength="150" size="20" /></td>
			</tr>
		   </table>
		   </td>
		 </tr>
		 </table>
		</td>
	  </tr>	 
	  <tr>
         <td align="left" nowrap><?php echo $AppUI->_('Mother');?>:</td>
		 <td align="right">
		  <table>
		 <tr>
		   <td><?php echo $AppUI->_('First Name');?>:</td>
		   <td><input type="text" class="text" name="admission[admission_mother_fname]" value="<?php echo @$row->admission_mother_fname;?>" maxlength="150" size="20" /></td>
		 </tr>		 
		 <tr>
		   <td><?php echo $AppUI->_('Last Name');?>:</td>
		   <td><input type="text" class="text" name="admission[admission_mother_lname]" value="<?php echo @$row->admission_mother_lname;?>" maxlength="150" size="20" /></td>
		 </tr>		 
		 <tr>
		   <td><?php echo $AppUI->_('Age');?>:</td>
		   <td><input type="text" class="text" name="admission[admission_mother_age]" value="<?php echo @$row->admission_mother_age;?>" maxlength="150" size="20" /></td>
		 </tr>
		 <tr>
		   <td><?php echo $AppUI->_('Status');?>:</td>
		   <td>
		   <table>
		   <tr>
		   <td align="left">
	   <?php echo arraySelectRadio($caregiverStatus, "admission[admission_mother_status]", 'onclick=toggleButtons()', $row->admission_mother_status ? $row->admission_mother_status  : -1, $identifiers ); ?>
		 </td>
		   </tr>
		   <tr>
			<td><?php echo $AppUI->_('Raising Child');?>:</td>
		   <td align="left">
	   <?php echo arraySelectRadio($boolTypes, "admission[admission_mother_raising_child]", 'onclick=toggleButtons()', $row->admission_mother_raising_child ? $row->admission_mother_raising_child  : -1, $identifiers ); ?>
		 </td>
		   </tr>
		   <tr>
			<td><?php echo $AppUI->_('Marital status');?>:</td>
		   <td align="left">
	   <?php echo arraySelectRadio($maritalStatus, "admission[admission_mother_marital_status]", 'onclick=toggleButtons()', $row->admission_mother_marital_status ? $row->admission_mother_marital_status  : -1, $identifiers ); ?>
		 </td>
		   </tr>
		   </table>
		   </td>
		 </tr>
		 <tr>
		   <td><?php echo $AppUI->_('Education Level');?>:</td>
		   
		   <td>
		   <?php echo arraySelectRadio($educLevels, "admission[admission_mother_educ_level]", 'onclick=toggleButtons()', $row->admission_mother_educ_level ? $row->admission_mother_educ_level  : -1, $identifiers ); ?>
		   </td>
		 </tr>
		 <tr>
		   <td><?php echo $AppUI->_('Employment');?>:</td>
		   <td>
		   <?php echo arraySelectRadio($employmentTypes, "admission[admission_mother_employment]", 'onclick=toggleButtons()', $row->admission_mother_employment ? $row->admission_mother_employment  : -1, $identifiers ); ?>
		   </td>
		 </tr>
		 <tr>
		   <td><?php echo $AppUI->_('Monthly Income');?>:</td>
		   <td>
		   <?php echo arraySelectRadio($incomeLevels, "admission[admission_mother_income]", 'onclick=toggleButtons()', $row->admission_mother_income ? $row->admission_mother_income  : -1, $identifiers ); ?>
		   </td>
		 </tr>
		 <tr>
		   <td><?php echo $AppUI->_('Other Details');?>:</td>
		   <td>
		   <table>
		    <tr>
			  <td><?php echo $AppUI->_('ID #');?>:</td>
			  <td><input type="text" class="text" name="admission[admission_mother_idno]" value="<?php echo @$row->admission_mother_idno;?>" maxlength="150" size="20" /></td>
			</tr>
		    <tr>
			  <td><?php echo $AppUI->_('Mobile #');?>:</td>
			  <td><input type="text" class="text" name="admission[admission_mother_mobile]" value="<?php echo @$row->admission_mother_mobile;?>" maxlength="150" size="20" /></td>
			</tr>
		   </table>
		   </td>
		 </tr>
		 </table>
		 </td>
	  </tr>		  
	  <tr>
         <td align="left" nowrap><?php echo $AppUI->_('Primary Caregiver');?>:</td>
		 <td align="right">
		  <table>
		 <tr>
		   <td><?php echo $AppUI->_('First Name');?>:</td>
		   <td><input type="text" class="text" name="admission[admission_caregiver_fname]" value="<?php echo @$row->admission_caregiver_fname;?>" maxlength="150" size="20" /></td>
		 </tr>		 
		 <tr>
		   <td><?php echo $AppUI->_('Last Name');?>:</td>
		   <td><input type="text" class="text" name="admission[admission_caregiver_lname]" value="<?php echo @$row->admission_caregiver_lname;?>" maxlength="150" size="20" /></td>
		 </tr>		 
		 <tr>
		   <td><?php echo $AppUI->_('Age');?>:</td>
		   <td><input type="text" class="text" name="admission[admission_caregiver_age]" value="<?php echo @$row->admission_caregiver_age;?>" maxlength="150" size="20" /></td>
		 </tr>
		 <tr>
		   <td><?php echo $AppUI->_('Status');?>:</td>
		   <td>
		   <table>
		   <tr>
		   <td align="left">
	   <?php echo arraySelectRadio($caregiverStatus, "admission[admission_caregiver_status]", 'onclick=toggleButtons()', $row->admission_caregiver_status ? $row->admission_caregiver_status  : -1, $identifiers ); ?>
		 </td>
		   </tr>
		   <tr>
			<td><?php echo $AppUI->_('Relationship to child');?>:</td>
		   <td align="left">
	   <input type="text" class="text" name="admission[admission_caregiver_relationship]" value="<?php echo @$row->admission_caregiver_relationship;?>" maxlength="150" size="20" />
		 </td>
		   </tr>
		   <tr>
			<td><?php echo $AppUI->_('Marital status');?>:</td>
		   <td align="left">
	   <?php echo arraySelectRadio($maritalStatus, "admission[admission_caregiver_marital_status]", 'onclick=toggleButtons()', $row->admission_caregiver_marital_status ? $row->admission_caregiver_marital_status  : -1, $identifiers ); ?>
		 </td>
		   </tr>
		   </table>
		   </td>
		 </tr>
		 <tr>
		   <td><?php echo $AppUI->_('Education Level');?>:</td>
		   
		   <td>
		   <?php echo arraySelectRadio($educLevels, "admission[admission_caregiver_educ_level]", 'onclick=toggleButtons()', $row->admission_caregiver_educ_level ? $row->admission_caregiver_educ_level  : -1, $identifiers ); ?>
		   </td>
		 </tr>
		 <tr>
		   <td><?php echo $AppUI->_('Employment');?>:</td>
		   <td>
		   <?php echo arraySelectRadio($employmentTypes, "admission[admission_caregiver_employment]", 'onclick=toggleButtons()', $row->admission_caregiver_employment ? $row->admission_caregiver_employment  : -1, $identifiers ); ?>
		   </td>
		 </tr>
		 <tr>
		   <td><?php echo $AppUI->_('Monthly Income');?>:</td>
		   <td>
		   <?php echo arraySelectRadio($incomeLevels, "admission[admission_caregiver_income]", 'onclick=toggleButtons()', $row->admission_caregiver_income ? $row->admission_caregiver_income  : -1, $identifiers ); ?>
		   </td>
		 </tr>
		 <tr>
		   <td><?php echo $AppUI->_('Other Details');?>:</td>
		   <td>
		   <table>
		    <tr>
			  <td><?php echo $AppUI->_('ID #');?>:</td>
			  <td><input type="text" class="text" name="admission[admission_caregiver_idno]" value="<?php echo @$row->admission_caregiver_idno;?>" maxlength="150" size="20" /></td>
			</tr>
		    <tr>
			  <td><?php echo $AppUI->_('Mobile #');?>:</td>
			  <td><input type="text" class="text" name="admission[admission_caregiver_mobile]" value="<?php echo @$row->admission_caregiver_mobile;?>" maxlength="150" size="20" /></td>
			</tr>
		   </table>
		   </td>
		 </tr>
		 </table>
		 </td>
	  </tr>	
	 <tr>
			  <td><?php echo $AppUI->_('Total Family Income');?>:</td>
			  <td>
			  <?php echo arraySelectRadio($incomeLevels, "admission[admission_family_income]", 'onclick=toggleButtons()', $row->admission_family_income ? $row->admission_family_income  : -1, $identifiers ); ?>
			  </td>
	</tr>
      <tr>
         <td align="left" nowrap><?php echo $AppUI->_('Other Household Members');?>:</td>
		 <td align="right">
		 <?php
            if ($AppUI->isActiveModule('relatives') && $perms->checkModule('relatives', 'view')) 
		{
			echo "<input type='button' class='button' value='".$AppUI->_("enter household info...")."' onclick='javascript:popRelatives();' />";
		}?>
		 </td>
	  </tr>	
	
     </table>
    </td>
 <td width="50%">
   <table border="0" cellpadding="1" cellspacing="1" class="details" width="100%">
    <tr>
			<td colspan="2" align="center">
				<img src="images/shim.gif" width="50" height="1" /><?php echo $AppUI->_('Social Worker Assessment'); ?><br />
				<hr width="500" align="center" size=1 />
			</td>
	 </tr>
	  <tr>
		<td align="left" valign="top"><?php echo $AppUI->_('Risk Level');?>:</td>
			<td align="left" valign="top">
			<?php echo arraySelect( $riskLevels, 'admission[admission_risk_level]', 'size="1" class="text"', @$obj->admission_risk_level ); ?>
 			</td>
	  </tr>		
      <tr>
		<td align="left" valign="top" nowrap><?php echo $AppUI->_('Explanation of risk level assessment');?>:</td>
		<td align="left" valign="top">
		<textarea cols="30" rows="5" class="textarea" name="admission[admission_risk_level_description]"><?php echo @$obj->admission_risk_level_description;?></textarea>
		</td>

      </tr>
	</table>
 </td>
</tr>
</form>
</table>
<script language="javascript">
 subForm.push(new FormDefinition(<?php echo $tab;?>, document.admissionInfoFrm, checkDetail, saveSocialInfo));
</script>