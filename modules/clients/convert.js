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
	
	task_contacts = document.getElementById('task_contacts');
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

function submitIt(form)
{

    var nameField = document.getElementById('company_name'); 		
	
	if ((nameField) && (nameField.value.length <  3))
	{
			alert( company_name_msg );
			nameField.focus();
			return false;
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
	network_firewall_contact = document.getElementById('network_firewall_contact');
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
	network_vpn_contact = document.getElementById('network_vpn_contact');
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
	building_solution_id = document.getElementById('building_solution_id');
	building_solution_id.value = bs_id_string;
*/
}

function popCalendar( field )
{
    
	calendarField = field;
	i_cal = document.getElementById('company_domain_' + field.name);
	idate = i_cal.value;
	window.open( 'index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'top=250,left=250,width=251, height=220, scollbars=false' );
	
}
/**
 *	@param string Input date in the format YYYYMMDD
 *	@param string Formatted date
 */

function setCalendar( idate, fdate ) 
{
	fld_date = document.getElementById('company_domain_' + calendarField.name);
	calendarField.value = fdate;
	fld_date.value = idate;
	
	e_date = document.getElementById('company_domain_' + 'exp_date');
	e_fdate = document.getElementById('exp_date');
	if (calendarField.name == 'reg_date') 
	{
		if( e_fdate.value < idate) 
		{
			e_date.value = idate;
			e_fdate.value = fdate;
		}
	}

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

