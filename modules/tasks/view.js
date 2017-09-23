// $Id: view.js 3073 2005-03-19 05:58:52Z ajdonnison $
// Task view support routines.
function popEmailContacts() {
	updateEmailContacts();
	var email_others = document.getElementById('email_others');
	window.open(
	  './index.php?m=public&a=contact_selector&dialog=1&call_back=setEmailContacts&selected_contacts_id='
		+ email_others.value, 'contacts','height=600,width=400,resizable,scrollbars=yes');
}

function setEmailContacts(contact_id_string) {
	if (! contact_id_string)
		contact_id_string = "";
	var email_others = document.getElementById('email_others');
	email_others.value = contact_id_string;
}

function updateEmailContacts() {
	var email_others = document.getElementById('email_others');
	var task_emails = document.getElementById('email_task_list');
	var proj_emails = document.getElementById('email_project_list');
	var do_task_emails = document.getElementById('email_task_contacts');
	var do_proj_emails = document.getElementById('email_project_contacts');

	// Build array out of list of contact ids.
	var email_list = email_others.value.split(',');
	if (do_task_emails.checked) {
		var telist = task_emails.value.split(',');
		var full_list = email_list.concat(telist);
		email_list = full_list;
		do_task_emails.checked = false;
	}

	if (do_proj_emails.checked) {
		var prlist = proj_emails.value.split(',');
		var full_proj = email_list.concat(prlist);
		email_list = full_proj;
		do_proj_emails.checked = false;
	}

	// Now do a reduction
	email_list.sort();
	var output_array = new Array();
	var last_elem = -1;
	for (var i = 0; i < email_list.length; i++) {
		if (email_list[i] == last_elem) {
			continue;
		}
		last_elem = email_list[i];
		output_array.push(email_list[i]);
	}
	email_others.value = output_array.join();
}

function emailNumericCompare(a, b) {
	return a - b;
}
function addFileToImport(test,task_id){
	if(test){
		if(!$j("#fileimp").length){
			$j("#formnd").append("<form id='formfileid'  action='/?m=tasks&task_id="+task_id+"&a=clients_import&mode=read' enctype='multipart/form-data' method='POST'><table><tr><td><input type='file' id='fileimp' name='excelfile'  data-ext='xls|xlsx'/></td></tr><tr><td><label  id='duplicateimp'><input type='checkbox' name='duplicate' value='duplicate'/>Merge duplicate data</label></td></tr></table></form>");
			/*$j("#formnd").attr("action","/?m=tasks&mode=uploadfcli");
			$j("#formnd").attr("enctype","multipart/form-data");
			$j("#formnd").attr("method","POST");*/
		}
		
		//action="" enctype="multipart/form-data" method="POST"
	}else{
		if($j("#fileimp").length){
			$j("#fileimp").remove();
			$j("#duplicateimp").remove();
			$j("#formnd").removeAttr("action");
			$j("#formnd").removeAttr("enctype");
			$j("#formnd").removeAttr("method");
		}
	}
}
function isCorrect(){
	
}
function popSelects(part,task_id){
	var brief=part.replace(/s$/,''),postVar;
	//eval("postVar=selected_"+part+"_id;");
	//if(part == "contacts"){
		//brief='staff';
	//} 
	var win = window.open("?m=public&a="+brief+"_selector&dialog=1&call_back=postStaff&fpart="+part+"&task_id="+task_id+"&selected_"+part+"_id="+postVar, part, "height=600,width=600,resizable,scrollbars=yes");
	win.onunload = function () {
		window.location.href = window.location.href;
	};
}
function dialogNewClient(id,name) {
	
	//$j("#dbnewsv").dialog("destroy").remove();
	var $dbox = $j('<div id="dbnewsv" title="' + name +'"></div>')
	$j("<form id='formnd'></form>")
		.append("<label><input type='radio' name='addingtype' value='manually' onchange='if(this.checked)addFileToImport(false,"+id+");'/>Manually</label>&emsp;")
		.append("<label><input type='radio' name='addingtype' value='excel'  onchange='if(this.checked)addFileToImport(true,"+id+");'/>From Excel</label>&emsp;")
		.append("<label><input type='radio' name='addingtype' value='existing' onchange='if(this.checked)addFileToImport(false,"+id+");'/>Existing in Database</label><br/><br/>&emsp;")
		.appendTo($dbox);
	
	$dbox.dialog({
		modal: true,
		width: "400px",
		resizable: false,
		autoOpen: true,
		buttons: {
			Cancel: function () {
				$j(this).dialog("close");
				$j("#dbnewsv").remove();
			},
			Process: function () {
				var atLeastOneIsChecked = $('input[name="addingtype"]:checked').length > 0;
				var val = $('input[name="addingtype"]:checked').val();
				if(atLeastOneIsChecked){
					if(val=='manually'){
						window.location = '/?m=clients&a=addedit&task_id='+id;
					}else if(val=='excel'){
						$("#formfileid").submit();
						/*var form_elements = $("#formfileid").serialize();
						console.log( $("#formfileid").serialize());
						var url = $("#formfileid").attr('action');
						var type = $("#formfileid").attr('method')*/
						/*$j.post(url, form_elements, function (ret) {
							var retObject = $j.parseJSON(ret);
							if (way === 'import' && $("#fultra").val() != '') {
								//we need import data for new instance, according to client choice
								$("#exdata").val(ret);
								rerun.set({dataset: retObject.dataset_id, instance: retObject.instance_id});
							} else {
								document.location.href = [nxt, '&dataset=', retObject.dataset_id, '&instance=', retObject.instance_id].join("");
							}
						});*/
						/*$.ajax({
				                   url: url,
				                   type: type,
				                   data: form_elements,
				                   mimeType: "multipart/form-data",
				                   contentType: false,
				                   cache: false,
				                   processData: false,
				                   success: function (data, textStatus, jqXHR) {
				                	   console.log(data);
				                	   if(data.status=="OK"){
				                		   
				                	   }else{
				                		   
				                	   }
				                   },
				                   error: function (jqXHR, textStatus, errorThrown) {
				                       
				                   }
			             });*/
					}else if(val=='existing'){
						popSelects('client',id);
						$j("#dbnewsv").remove();
					}else{
						
					}
				}
			}
		}
	}).prev(".ui-dialog-titlebar").css("background","#aed0ea").css("border","1px solid #aed0ea");
	
}
function valid8Import() {
	var selected = 0, missed = 0, go = false;
	$j("select").each(function () {
		if ($j(this).val() == '-1') {
			$j(this).addClass("alert");
			++missed;
		} else {
			$j(this).removeClass("alert");
			++selected;
		}
	});
	if (selected > 0) {
		if (missed > 0) {
			if (confirm("There are not selected columns. Skip them?")) {
				go = true;
			}
		} else {
			go = true;
		}
		if (go === true) {
			document.forms['finishim'].submit();
		}
	} else {
		jAlert("Please select at least one data column");
		return false;
	}
}
