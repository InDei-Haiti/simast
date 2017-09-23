function serialize(form) {
    if (!form || form.nodeName !== "FORM") {
        return;
    }
    var i, j, q = [];
    obj = document.getElementById('selected_columns');
			  console.log(obj);
	for (var i=0; i<document.getElementById('selected_columns').options.length; i++) {
		obj.options[i].selected = true;
	}
    for (i = form.elements.length - 1; i >= 0; i = i - 1) {
        if (form.elements[i].name === "") {
            continue;
        }
        switch (form.elements[i].nodeName) {
	        case 'INPUT':
	            switch (form.elements[i].type) {
	            case 'text':
	            case 'number':
	            case 'date':
	            case 'hidden':
	            case 'password':
	            case 'button':
	            case 'reset':
	            case 'submit':
	                q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
	                break;
	            case 'checkbox':
	            case 'radio':
	                if (form.elements[i].checked) {
	                    q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
	                }
	                break;
	            }
	            break;
	        case 'file':
	            break;
	        case 'TEXTAREA':
	            q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
	            break;
	        case 'SELECT':
	            switch (form.elements[i].type) {
	            case 'select-one':
	                q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
	                break;
	            case 'select-multiple':
	                for (j = form.elements[i].options.length - 1; j >= 0; j = j - 1) {
	                    if (form.elements[i].options[j].selected) {
	                        q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].options[j].value));
	                    }
	                }
	                break;
	            }
	            break;
	        case 'BUTTON':
	            switch (form.elements[i].type) {
	            case 'reset':
	            case 'submit':
	            case 'button':
	                q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
	                break;
	            }
	            break;
        }
    }
    return q;
}

function linkToArray(link){
	var request = [];
    var pairs = link.substring(link.indexOf('?') + 1).split('&');
    for (var i = 0; i < pairs.length; i++) {
        if(!pairs[i])
            continue;
        var pair = pairs[i].split('=');
        //index.php?m=wizard&a=form_use&fid=68&idIns=1&todo=view&teaser=1&rtable=1&tab=0
        if(pair[0] == 'm' || pair[0] == 'a' || pair[0] == 'project_id' || pair[0] == 'task_id' || pair[0] == 'tab' || pair[0] == 'search' || pair[0] == 'rtable' ||
        		pair[0] == 'teaser' || pair[0] == 'fid' || pair[0] == 'idIns' || pair[0] == 'todo')
        	request.push(decodeURIComponent(pair[0])+"="+decodeURIComponent(pair[1]));
     }
     return request;
}

function arrayUnique(array) {
    var a = array.concat();
    for(var i=0; i<a.length; ++i) {
        for(var j=i+1; j<a.length; ++j) {
            if(a[i] === a[j])
                a.splice(j--, 1);
        }
    }

    return a;
}

function joinList(list) {
	if(list!=null){
		return "?"+list.join('&');
	}
	return "";
}

function createForm(nameForm,classEl){
	var form = document.createElement("form");
	form.setAttribute('method',"get");
	form.setAttribute('action',"");
	form.setAttribute('name',nameForm);
	var elements = document.getElementById('filterstab').getElementsByClassName(classEl);
	for(var i=0;i<elements.length;i++){
		form.appendChild(elements[i]);
		//var cln = elements[i].cloneNode(true);
		//form.appendChild(cln);
	}
	return form;
}

var trRows;

function setTableFilter(){
	//var trRows;
	console.log(trRows);
	var filterstab = document.getElementById('filterstab')
	var val = document.getElementById('select_field').value;
	console.log(val);
	if(val!="---" && trRows[val])
		//filterstab.innerHTML += trRows[val]; 
		$('#filterstab').append(trRows[val]);
	$j(".classflddate").datepick({dateFormat: "yyyy-mm-dd"});
}

function delRowFilter(ele){
	$span=$j(ele);
	$tr = $span.closest('tr');
	$tr.remove();
}

function moveUpDown(select,move){
	 var $opts = $j('#'+select+' option:selected');
	 
	 if($opts.length){
	     (move == 'Up') ? 
	         $opts.first().prev().before($opts) : 
	         $opts.last().next().after($opts);
	 }
}

function addRemoveOption(ori,des){
	var $opts = $j('#'+ori+' option:selected');
	if($opts.length){
		for(var i=0;i<$opts.length;i++){
			$j('#'+des).append($opts[i]);
		}
		
	}
}

function crAndRmSelectMultiple(select_id){
	selectel = document.getElementById(select_id);
	if(selectel.getAttribute("multiple")!=null && selectel.getAttribute("multiple")=='multiple'){
		selectel.removeAttribute("multiple");
	}else{
		selectel.setAttribute("multiple", "multiple");
	}
}

function formPopup(id,parent_id,datepay){
	window.open('index.php?m=wizard&a=form_useed&fid='+id+'&todo=addedit&parent_id='+parent_id+'&datepay='+datepay+'&dialog=1','calwin','top=250,left=250,width=600,height=300,scollbars=false,resizable');
}

