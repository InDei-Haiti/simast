var calendarField = '';
var calWin = null;

function popContacts() 
{
	window.open('./index.php?m=public&a=contact_selector&dialog=1&call_back=setContacts&selected_contacts_id='+selected_contacts_id, 'contacts','height=600,width=400,resizable,scrollbars=yes');
}


function setContacts(contact_id_string){
	if(!contact_id_string){
		contact_id_string = "";
	}
	
	task_contacts = $j('#task_contacts');
	task_contacts.value = contact_id_string;
	selected_contacts_id = contact_id_string;
}


/**
* no comment needed
*/
function isInArray(myArray, intValue) {

	for (var i = 0; i < myArray.length; i++) {
		if (myArray[i] == intValue) {
			return true;
		}
	}		
	return false;
}


var subForm = new Array();

//create prototype object
//new FormDefinition(0, 'test', 'test', null);

function FormDefinition(id, form, check, save) 
{
    
	this.id = id;
	this.form = form;
	this.checkHandler = check;
	this.saveHandler = save;
	this.check = fd_check;
	this.save = fd_save;
	this.submit = fd_submit;
	this.seed = fd_seed;
	//alert(form);
	//alert(this.form);
}


function fd_check()
{
	if (this.checkHandler) 
	{
		return this.checkHandler(this.form);
	} 
	else 
	{
		return true;
	}
}

function fd_save()
{
	if (this.saveHandler) 
	{
		var copy_list = this.saveHandler(this.form);
		//alert(copy_list);
		return copyForm(this.form, document.editFrm, copy_list);
	} 
	else 
	{
		return this.form.submit();
	}
}

function fd_submit()
{
	if (this.saveHandler)
		this.saveHandler(this.form);
	return this.form.submit();
}

function fd_seed()
{
	return copyForm(document.editFrm, this.form);
}

// Sub-form specific functions.
function checkDates(form) 
{
	if (can_edit_time_information && check_domain_dates) {
		if (!form.company_domain_reg_date.value) 
		{
			alert( task_start_msg );
			form.company_domain_reg_date.focus();
			return false;
		}
		if (!form.company_domain_reg_date.value) 
		{
			alert( task_end_msg );
			form.company_domain_reg_date.focus();
			return false;
		}
	}
	return true;
}

function copyForm(form, to, extras) 
{
	// Grab all of the elements in the form, and copy them
	// to the main form.  Do not copy hidden fields.
	//alert(form);
	var h = new HTMLex;
    //alert ('form:' + form.name);	
	//alert('to: ' + to);
	for (var i = 0; i < form.elements.length; i++) 
	{
		var elem = form.elements[i];
		if (elem.type == 'hidden') 
		{
			// If we have anything in the extras array we check to see if we
			// need to copy it across
			if (!extras)
				continue;
			var found = false;
			for (var j = 0; j < extras.length; j++) 
			{
				if (extras[j] == elem.name) 
				{
				  found = true;
				  break;
				}
			}
			if (! found)
				continue;
		}
		// Determine the node type, and determine the current value
		switch (elem.type) 
		{
			case 'text':
			case 'textarea':
			case 'hidden':
				to.appendChild(h.addHidden(elem.name, elem.value));
				break;
			case 'select-one':
				if (elem.options.length > 0)
					to.appendChild(h.addHidden(elem.name, elem.options[elem.selectedIndex].value));
				break;
			case 'select-multiple':
				var sel = to.appendChild(h.addSelect(elem.name, false, true));
				for (var x = 0; x < elem.options.length; x++) 
				{
					if (elem.options[x].selected) 
					{
						sel.appendChild(h.addOption(elem.options[x].value, '', true));
					}
				}
				break;
			case 'radio':
			case 'checkbox':
				if (elem.checked) 
				{
					to.appendChild(h.addHidden(elem.name, elem.value));
				}
				break;
		}
	}
	return true;
}


function checkDetail(form) {
	return true;
}

function saveDetail(form) {
	return null;
}

function checkResource(form) {
	return true;
}

function submitIt(form){
	var idField = $j('#client_adm_no'); 		
	/*if ((idField) && (idField.value.length <  1))
	{
			alert( 'Please enter a valid admission number' );
			idField.focus();
			return false;
	} */
	
	var errormsg ='';
	if(!manField("staff_id") ){
		alert("Please select Counselor");
		return;		
	}	
	if(!manField("clinic_id")){
		alert("Please select Center");
		return;		
	}
	if(!manDateField("counselling_dob",false)){
		alert("Please enter Birth Date!");
		return;		
	}
	var gens=$j(".genderOpts").filter(":checked").length;
	if(gens == 0){
		alert("Please select gender of client!");
		return false;
	}
	
	if($j("#move_active").val() === "1"){
	  sDate=$j("#counselling_entry_date");
    	    if(sDate.val() == 0){
		alert("Please select entry date");
		sDate.focus();
		return;		
	    }
	}
	aDate=$j("#counselling_admission_date");
	if(aDate.attr("disabled") === false && aDate.val().length == 0){
		alert("Please select admission date");
		aDate.focus();
		return;		
	}		
	if (!manField("client_first_name")){
		alert( "Please enter First name of the client" );		
		return false;
	}
	if (!manField("client_last_name")) {
		alert( "Please enter Last name of the client" );		
		return false;
	}
	
	

	/*if (fieldToCheck.val().length == 0) 
	{
		/*errormsg = checkValidDate(fieldToCheck.val());
		
		if (errormsg.length > 1)
		{ 
			alert("Invalid date of birth" );
			fieldToCheck.focus();
		        return;
		}
	}else if(fieldToCheck.val().length == 0){
		alert("Please enter Date of birth");
		fieldToCheck.focus();
		return;
	}*/
	
	/*if(!manDateField('counselling_age_yrs',true)){
		alert(" Invalid Age (years)");
   		return false;
	}
	/*fieldToCheck = $j('#counselling_age_yrs');
	if (fieldToCheck.length > 0){
	        if( fieldToCheck.val().length > 0) {
        		if (isNaN(parseInt(fieldToCheck.val(),10)) ){
        			alert(" Invalid Age (years)");
	        		fieldToCheck.focus();
	        		return false;
	        		
	                }
        	}
        }*/
	/*if(!manDateField('counselling_age_month',true)){
		alert(" Invalid Age (months)");		
		return false;
	}
	/*fieldToCheck = $j('#counselling_age_months');
	if (fieldToCheck.length > 0){
	 if (fieldToCheck.val().length > 0) {
		if (isNaN(parseInt(fieldToCheck.val(),10)) )
		{
			alert(" Invalid Age (months)");
			fieldToCheck.focus();
			return false;

		}
	        }
	}*/
	if(!manDateField('counselling_gestation_period',true)){
		alert(" Invalid Gestation Period");
		return false;
	}
	/*fieldToCheck = $j('#counselling_gestation_period');
	if (fieldToCheck.length > 0){
	 if (fieldToCheck.val().length > 0) 	{
		if (isNaN(parseInt(fieldToCheck.val(),10)) ){
			alert(" Invalid Gestation Period");
			fieldToCheck.focus();
			return false;

		}
		}
	}*/	
	if(!manDateField('counselling_birth_weight',true)){
		alert(" Invalid Birth Weight");
		return false;
	}
	/*fieldToCheck = $j('#counselling_birth_weight');
	if (fieldToCheck.length > 0){
	 if (fieldToCheck.val().length > 0) {		
		if (isNaN(parseInt(fieldToCheck.val(),10)) ){
			alert(" Invalid Birth Weight");
			fieldToCheck.focus();
			return false;
			
		}
		}
	}*/	
	if(!manDateField('counselling_breastfeeding_duration',true)){
		alert(" Invalid Breastfeeding Duration");
		return false;
	}
	/*fieldToCheck = $j('#counselling_breastfeeding_duration');
	if (fieldToCheck.length > 0){
	 if (fieldToCheck.val().length > 0) 	{
		if (isNaN(parseInt(fieldToCheck.val(),10)) )	{
			alert(" Invalid Breastfeeding Duration");
			fieldToCheck.focus();
			return false;

		}
	        }	
	}*/
	if(!manDateField('counselling_other_breastfeeding_duration',true)){
		alert(" Invalid Breastfeeding Duration");
		return false;
	}
	/*fieldToCheck = $j('#counselling_other_breastfeeding_duration');
	if (fieldToCheck.length > 0){
	 if (fieldToCheck.val().length > 0) {
		if (isNaN(parseInt(fieldToCheck.val(),10)) ){
			alert(" Invalid Breastfeeding Duration");
			fieldToCheck.focus();
			return false;

		}
		}
	}*/
	if(!manDateField('counselling_child_nvp_date',false)){
		alert("Child NVP Date !");
		return false;
	}
	/*
	fieldToCheck = $j('#counselling_child_nvp_date');
	if (fieldToCheck.length > 0){
	 if (fieldToCheck.val().length > 0) 	{
		//errormsg = checkValidDate(fieldToCheck.val());
		if (errormsg.length > 1){
			alert("Child NVP Date " + errormsg);
			fieldToCheck.focus();
			return false;
			}
        }
    }*/
    if(!manDateField('counselling_child_azt_date')){
    	alert("Child AZT Date " + errormsg);
			return false;
    }
	fieldToCheck = $j('#counselling_child_azt_date');	
	if (fieldToCheck.length > 0){
	 if (fieldToCheck.val().length > 0) 	{
		//errormsg = checkValidDate(fieldToCheck.val());
		if (errormsg.length > 1){
			alert("Child AZT Date " + errormsg);
			fieldToCheck.focus();
			return false;

		}
	}
        } 
	fieldToCheck = $j('#counselling_no_doses');
	if (fieldToCheck.length > 0){
	 if (fieldToCheck.val().length > 0) {
		if (isNaN(parseInt(fieldToCheck.val(),10)) ){
			alert(" Invalid No of AZT Doses");
			fieldToCheck.focus();
			return false;

		}
	        }
	}
	fieldToCheck = $j('#counselling_mother_date_art');
	if (fieldToCheck.length > 0){
	 if ( fieldToCheck.val().length > 0) {
		errormsg = checkValidDate(fieldToCheck.val());
		if (errormsg.length > 1){
			alert("Date Mother began ART : " + errormsg);
			fieldToCheck.focus();
			return false;

		}
		}
        }
        fieldToCheck = $j('#counselling_mother_date_cd4');	
        if (fieldToCheck.length > 0){
                if ( fieldToCheck.val().length > 0) {
		errormsg = checkValidDate(fieldToCheck.val());
		if (errormsg.length > 1){
			alert("Date Mother had CD4 Test: " + errormsg);
			fieldToCheck.focus();
			return false;

		}
                }
        }
        fieldToCheck = $j('#counselling_determine_date');	
        if (fieldToCheck.length > 0){
		if ( fieldToCheck.val().length > 0) {
		//errormsg = checkValidDate(fieldToCheck.val());
		if (errormsg.length > 1){
			alert("Date of Determine Test date: " + errormsg);
			fieldToCheck.focus();
			return false;

		}
		}
	} 
	fieldToCheck = $j('#counselling_bioline_date');
	if (fieldToCheck.length > 0){
	if ( fieldToCheck.val().length > 0) {
		//errormsg = checkValidDate(fieldToCheck.val());
		if (errormsg.length > 1){
			alert("Date of Bioline Test: " + errormsg);
			fieldToCheck.focus();
			return false;

		}
		}
	} 
	fieldToCheck = $j('#counselling_unigold_date');
	if (fieldToCheck.length > 0){
	if ( fieldToCheck.val().length > 0) {
		//errormsg = checkValidDate(fieldToCheck.val());
		if (errormsg.length > 1){
			alert("Date of Unigold Test: " + errormsg);
			fieldToCheck.focus();
			return false;

		}
    		}
    	}
	fieldToCheck = $j('#counselling_elisa_date');	
	if (fieldToCheck.length > 0){
		if ( fieldToCheck.val().length > 0) {
		///errormsg = checkValidDate(fieldToCheck.val());
		if (errormsg.length > 1){
			alert("Date of ELISA Test: " + errormsg);
			fieldToCheck.focus();
			return false;

		}
    		}
    	}
	fieldToCheck = $j('#counselling_pcr1_date');	
	if (fieldToCheck.length > 0){
		if ( fieldToCheck.val().length > 0) {
		//errormsg = checkValidDate(fieldToCheck.val());
		if (errormsg.length > 1){
			alert("Date of PCR1 Test: " + errormsg);
			fieldToCheck.focus();
			return false;

		}
		}
	}
	fieldToCheck = $j('#counselling_pcr2_date');	
	if (fieldToCheck.length > 0){
	if ( fieldToCheck.val().length > 0) {
		///errormsg = checkValidDate(fieldToCheck.val());
		if (errormsg.length > 1){
			alert("Date of PCR2 Test: " + errormsg);
			fieldToCheck.focus();
			return false;

		}
		}
	} 
	fieldToCheck = $j('#counselling_rapid12_date');
	if (fieldToCheck.length > 0){
		if ( fieldToCheck.val().length > 0) {
		///errormsg = checkValidDate(fieldToCheck.val());
		if (errormsg.length > 1){
			alert("Date of Rapid Test @ 12 months: " + errormsg);
			fieldToCheck.focus();
			return false;

		}
		}
	} 
	fieldToCheck = $j('#counselling_rapid18_date');
	if (fieldToCheck.length > 0){
		if ( fieldToCheck.val().length > 0) {
		///errormsg = checkValidDate(fieldToCheck.val());
		if (errormsg.length > 1){
			alert("Date of Rapid Test @ 18 months: " + errormsg);
			fieldToCheck.focus();
			return false;
		}
		}
	}
	fieldToCheck = $j('#counselling_other_date');	
	if (fieldToCheck.length > 0){
		if ( fieldToCheck.val().length > 0) {
		///errormsg = checkValidDate(fieldToCheck.val());
		if (errormsg.length > 1){
			alert("Date of other Test: " + errormsg);
			fieldToCheck.focus();
			return false;
		}
		}
	} 

	// Check the sub forms
	//alert ('subformlength: ' + subForm.length);
	//for (var c = 0; c < subForm[0].form.elements.length; c++)
	   // alert(subForm[0].form.elements[c].name);
	
	for (var i = 1; i < subForm.length; i++) 
	{
		//alert (subForm[i]);
		if (!subForm[i].check())
			return false;
		// Save the subform, this may involve seeding this form
		// with data
		subForm[i].save();
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
	//alert(contact_id_string);
	network_firewall_contact = $j('#network_firewall_contact');
	network_firewall_contact.value = contact_id_string;
	
	selected_fw_contacts_id = contact_id_string;
}

function setVPNContacts(contact_id_string)
{

	if(!contact_id_string)
	{
		contact_id_string = "";
	}
	//alert(contact_id_string);
	network_vpn_contact = $j('#network_vpn_contact');
	network_vpn_contact.value = contact_id_string;
	selected_vpn_contacts_id = contact_id_string;
}

function setBuildingSolution(bs_id_string)
{

/*	if(!bs_id_string)
	{
		bs_id_string = "";
	}
	//alert(contact_id_string);
	building_solution_id = $j('#building_solution_id');
	building_solution_id.value = bs_id_string;
*/
}

function popCalendar( field )
{
    
	calendarField = field;
	i_cal = $j('#company_domain_' + field.name);
	idate = i_cal.value;
	window.open( 'index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'top=250,left=250,width=251, height=220, scollbars=false' );
	
}
/**
 *	@param string Input date in the format YYYYMMDD
 *	@param string Formatted date
 */

function setCalendar( idate, fdate ) 
{
	fld_date = $j('#company_domain_' + calendarField.name);
	calendarField.value = fdate;
	fld_date.value = idate;
	
	e_date = $j('#company_domain_' + 'exp_date');
	e_fdate = $j('#exp_date');
	if (calendarField.name == 'reg_date') 
	{
		if( e_fdate.value < idate) 
		{
			e_date.value = idate;
			e_fdate.value = fdate;
		}
	}

}
function saveSocialInfo (form)
{
	return Array("social[social_info_id]","social[social_client_id]","social[social_chw_contact]","social[social_shw_contact]","social[social_entry_date]");
}
function saveCounsellingInfo (form)
{
	return Array("social[social_info_id]","social[social_client_id]","social[social_chw_contact]","social[social_shw_contact]","social[social_entry_date]");
}

function saveCustNet (form)
{
	return Array("custnet[network_info_id]","custnet[network_company_id]","custnet[network_firewall_contact]","custnet[network_vpn_contact]","custnet[building_solution_id]");
}
function saveTelkom(form)
{
	return Array("telkom[telkom_config_id]");
}

function saveIPConfig(form)
{
	return Array("custip[company_ip_info_id]");
}

function saveDomain(form)
{
	return Array("domain[company_domain_info_id]");
}

function saveTerm(form)
{
	return Array("term_equip[term_equip_id]");
}

function saveTraining(form)
{
	return Array("training[training_info_id]");
}

function saveAKConfig(form)
{
	return Array("akconfig[company_ak_config_id]");
}
function saveVOIP(form)
{
	return Array("voip[company_voip_info_id]");
}
function saveBuildingSolution (form)
{
	return Array();
}

function saveVPNContacts(form)
{
  return null;
}
