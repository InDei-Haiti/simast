/**
 * 
 */
function popSelects(part,task_id,param){
	var brief=part.replace(/s$/,''),postVar;
	//eval("postVar=selected_"+part+"_id;");
	//if(part == "contacts"){
		//brief='staff';
	//} 
	var win = window.open("?m=public&a="+brief+"_selector&dialog=1&call_back=postStaff&fpart="+part+"&task_id="+task_id+"&selected_"+part+"_id="+postVar+param, part, "height=600,width=600,resizable,scrollbars=yes");
	win.onunload = function () {
		window.location.href = window.location.href;
	};
}
function dialogNewFields(task_id,fields) {
	//$j("#dbnewsv").dialog("destroy").remove();
	console.log(fields);
	var $dbox = $j('<div id="dbnewsv" title="' + name +'"></div>');
	var $form = $j("<form id='formnd'></form>");
	for (var key in fields) {
	    if (fields.hasOwnProperty(key)) {
	    	$form.append("<label><input type='checkbox' name='fields[]' value='"+key+"' />"+fields[key]['title']+"</label>&emsp;<br/>");
	    }
	}
	$form.appendTo($dbox);
	
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
				var param = "";
				var val = "";
				$('input[name="fields[]"]:checked').each(function() {
					val += this.value+",";
				});
				if(val!=""){
					//val = val.substring(1, val.length-1);
					param = "&fields="+val
				}
				popSelects('client',task_id,param);
			}
		}
	}).prev(".ui-dialog-titlebar").css("background","#aed0ea").css("border","1px solid #aed0ea");
	
}

