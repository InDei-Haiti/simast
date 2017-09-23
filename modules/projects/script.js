function setColor(color) {
	var f = document.editFrm;
	if (color) {
		f.project_color_identifier.value = color;
	}
	//test.style.background = f.project_color_identifier.value;
	document.getElementById('test').style.background = '#' + f.project_color_identifier.value; 		//fix for mozilla: does this work with ie? opera ok.
}

var calendarField = '';
var calWin = null;

function popCalendar( field ){
	calendarField = field;
	//idate = eval( 'document.editFrm.project_' + field + '.value' );
	idate = $('');
	window.open( 'index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'width=280, height=250, scrollbars=no, status=no,resizable=yes' );
}

/**
*	@param string Input date in the format YYYYMMDD
*	@param string Formatted date
*/
function setCalendar( idate, fdate ) {
	fld_date = eval( 'document.editFrm.project_' + calendarField );
	fld_fdate = eval( 'document.editFrm.' + calendarField );
	fld_date.value = idate;
	fld_fdate.value = fdate;

	// set end date automatically with start date if start date is after end date
	if (calendarField == 'start_date') {
		if( document.editFrm.end_date.value < idate) {
			document.editFrm.project_end_date.value = idate;
			document.editFrm.end_date.value = fdate;
		}
	}
}

function submitIt() {
	var f = document.editFrm;
	var msg = '';

	/*
	if (f.project_end_date.value > 0 && f.project_end_date.value < f.project_start_date.value) {
		msg += "\n<?php echo $AppUI->_('projectsBadEndDate1');?>";
	}
	if (f.project_actual_end_date.value > 0 && f.project_actual_end_date.value < f.project_start_date.value) {
		msg += "\n<?php echo $AppUI->_('projectsBadEndDate2');?>";
	}
	*/

	<?php 
	/*
	** Automatic required fields generated from System Values
	*/
	$requiredFields = dPgetSysVal( 'ProjectRequiredFields' );
	echo dPrequiredFields($requiredFields);
	?>

	if (msg.length < 1) {
		f.submit();
	} else {
		alert(msg);
	}
}

var selected_contacts_id = "<?php echo implode(',', $selected_contacts); ?>";

function popContacts() {
	window.open('./index.php?m=public&a=contact_selector&dialog=1&call_back=setContacts&selected_contacts_id='+selected_contacts_id, 'contacts','height=600,width=400,resizable,scrollbars=yes');
}

function setContacts(contact_id_string){
	if(!contact_id_string){
		contact_id_string = "";
	}
	document.editFrm.project_contacts.value = contact_id_string;
	selected_contacts_id = contact_id_string;
}

var selected_departments_id = "<?php echo implode(',', $selected_departments); ?>";

function popDepartment() {
        var f = document.editFrm;
	var url = './index.php?m=public&a=selector&dialog=1&callback=setDepartment&table=departments&company_id='
            + f.project_company.options[f.project_company.selectedIndex].value
            + '&dept_id='
            + selected_departments_id;
//prompt('',url);
        window.open(url,'dept','left=50,top=50,height=250,width=400,resizable');

//	window.open('./index.php?m=public&a=selector&dialog=1&call_back=setDepartment&selected_contacts_id='+selected_contacts_id, 'contacts','height=600,width=400,resizable,scrollbars=yes');
}

function setDepartment(department_id_string){
	if(!department_id_string){
		department_id_string = "";
	}
	document.editFrm.project_departments.value = department_id_string;
	selected_departments_id = department_id_string;
}

function popManager() {
    var f = document.editFrm;
    if (f.project_company.selectedIndex == 0) {
        alert("<?php
								echo $AppUI->_ ( 'Please select a project first!', UI_OUTPUT_JS );
								?>");
    } else {
        window.open('./index.php?m=public&a=selector&dialog=1&callback=setManager&table=manager&avar='
            + f.project_company.options[f.project_company.selectedIndex].value, 'task','left=50,top=50,height=250,width=400,resizable')
    }
}

//Callback function for the generic selector
function setManager(key, val) {
    var f = document.editFrm;
    var data_link= val.split("#@@#");
    if(data_link[0]!=""){
        $j("#pmanager").val(data_link[0]);
    }
	var drops=data_link[1].split(":#:");
	if(drops[1] !=""){
	    $j("#powner").val(drops[1]);
	}
}

function testURL( x ) {
	var test = "document.editFrm.project_url.value";
	test = eval(test);
	if (test.length > 4) {
		newwin = window.open( "http://" + test, 'newwin', '' );
	 }
}