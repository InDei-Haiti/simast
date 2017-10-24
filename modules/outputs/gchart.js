/**
 * Created by stig on 26.11.13.
 */
var uuid;
var charter = (function (my) {

	var $accd = $j("#bset");
	var currentPart;
	var $fblock = $j("#fields_block", $accd);
	var $bay = $j("#bay");
	var $loadico = $j(".loadingz", $bay);
	//var fieldOption = doT.template($j("#fld_tpl").html());
	var fieldCfg = {};
	var alevIds = [];
	var fopts;
	var selectedFields = [];
	var dataListID = 0;
	var instanceID = 0;
	var locationNames = [];
	var lanames = false;
	var levelOrder = [
		'region',
		'municipality',
		'village'
	];
	var xChart;
	var gCoder;
	var mTxt = [];
	var infowindow;
    var project;
	function start() {
		/*for (var i in alevels) {
			if (alevels.hasOwnProperty(i)) {
				alevIds.push(i);
			}
		}*/
		
		$accd.accordion({
			heightStyle: "content",
			activate: function (event, ui) {
				alert(ui);
			}
		});
		$j("#bparts", $accd).delegate("input", "click", function () {
			changePart(this.value);			
			//loadTasks(this.value);
			//waterFall();
			$j("#forms").empty();
			loadForms(this.value)			
			waterFall();
		});		
		$j("#tasks", $accd).delegate("input", "click", function () {			
			changePart(this.value);			
			loadForms(this.value)			
			waterFall();		
		});
		$j(".area_select").live("change", function () {
			if (currentPart === 'dataset' && this.value !== '-1') {
				updateInstances(this.value);
			}
			loadFilters(this.value);
			$j("#fields_block").css("height", "auto");
		});

		$j (".instance .button_link").click(function(){
		 loadFilters();
		});
		
		$j("#doChart").click(function () {
			getDataChart();
		});

		//gCoder = new google.maps.Geocoder();
		//infowindow = new google.maps.InfoWindow();
	}

	function getDataChart() {
		selectedFields = [];
		instanceID = $j(".instance_select:visible").val();
		$j("#fields", $accd).find(":input:checked").each(function () {
			selectedFields.push({fld: $j(this).attr("name"), table: $j(this).attr("data-forms"), title: $j(this).parent().text()});
		});
		$j.post("/?m=outputs&a=gchart_be&suppressHeaders=1&xmode=getData", {fields: JSON.stringify(selectedFields), parent: dataListID, item: instanceID, area: currentPart, elocs: (fieldCfg.location.tbl + "." + fieldCfg.location.fld)}, function (md) {
			if (md && md.length > 0) {
				var rdata = $j.parseJSON(md);
				if (amnt(rdata) > 0) {
					if (lanames === false) {
						//1.get name of admin level
						if (fieldCfg.location.hasOwnProperty('svs')) {
							var ts = fieldCfg.location.svs;
							var tname = alevels[ts];
							var fcfg = {
								dlevel: fieldCfg.location.isLevel,
								header: [
									tname
								],
								body: [
									fieldCfg.location.tbl + "." + fieldCfg.location.fld
								]
							};
							for (var i = 0, l = selectedFields.length; i < l; i++) {
								fcfg.header.push(selectedFields[i].title);
								fcfg.body.push(selectedFields[i].table + "." + selectedFields[i].fld);
							}
							draw(fcfg, rdata);
						}
					}
				} else {
					info("Obtained data is not valid", 0);
				}
			} else {
				info("Failed to receive data", 0);
			}
		});
	}		
	function loadTasks(project_id){		
		$j.get("/?m=outputs&a=gchart&suppressHeaders=1", {mode: "loadtask", pid: project_id}, function (msg) {
			if (msg && msg !== 'fail') {
				project = project_id;
				msg = $j.parseJSON(msg);				
				$j("#tasks").empty();				
				$j("#tasks").append("");
				$j("#tasks").append('<label><input type="radio" name="dataSrc" value="all">&nbsp;&nbsp;All</label>');
				$.each(msg, function(i,e){					
					$j("#tasks").append('<label><input type="radio" name="dataSrc" value="'+msg[i].task_id+'">&nbsp;&nbsp;'+msg[i].task_name+'</label>');
					});			
			} else {				
				$j("#msgbox").text("Invalid form selected!").show().delay(2000).fadeOut(3000);			
			}		
		});			
	}	
	var where;
	function pushWhere(key,val){
		var arrbool = false;
	
		if( Object.prototype.toString.call(val) === '[object Array]' ) {
			key1 = key.replace('sec_','');
		    val = key1 + " in (" + val.join() + ")";
		    arrbool = true;
		}
		if(arrbool){
			where[key] = val;
		}else if(val.indexOf("between")>0){
			where[key] = val;
	     }else if(val.indexOf("=")>0){
			where[key] = val;
	     }else if(val.indexOf(">")>0){
			where[key] = val;
	     }else if(val.indexOf("<")>0){
			where[key] = val;
	     }else{
	    	key1 = key.replace('sec_','');
			where[key] = key1+"='"+val+"'";
	     } 
		$j('#where').val(JSON.stringify(where));
	}
	function loadForms(project_id){
		
		$('#imgloader').show();
		$j.get("/?m=outputs&a=gchart&suppressHeaders=1", {mode: "loadforms", pid: project_id}, function (msg) {

			if (msg && msg !== 'fail') {
				msg = $j.parseJSON(msg);
				where = {};
				$j("#forms").empty();
				$j("#forms").append('<input type="hidden" name="where" id="where"/>');
				$j("#forms").append("<ul id='ulforms' style='list-style:none;margin:0;margin-left:-40px'></ul>");
                $.each(msg, function(i){
					var tablename;
					var fields = msg[i].fieldsv;
					var inputfilAct ='';
					if(!msg[i].activity){
                        tablename = 'wform_'+msg[i].id;
					}else{
                        tablename = 'tasks';
                        inputfilAct = '<input type="hidden" value="tasks" name';
					}
					//icon + permettant de derouler les formulaires - Il est accompagne d'un identifiant
					icon = '<div class="switch fa fa-plus" id="switch'+msg[i].id+'" style="color: blue;font-size: large"></div>';
					//icon + permettant de derouler les formulaires - Il est accompagne d'un identifiant
                    if(msg[i].activity){
                    	icon = '<input type="checkbox" name="activity" value="1" class="jcheck">';
                    }

					var li = '<li>'+icon+msg[i].title;
					// creation du tableau panel pour chaque formulaire
					li += '<table id="panel'+msg[i].id+'" class="std" border="1" cellpadding="3" cellspacing="0" style="background:transparent;display:none;margin-left:20px;list-style:none;border-collapse: collapse;border-color: #a5cbf7;"></table>';
					li += '</li>';
					$j("#ulforms").append(li);
					/*if(project_id==367)
						uuid = 'key_ui';
					else
						uuid = null;*/
					console.log(fields);
					if(fields){
						var optLst = '';
						/*if(uuid != null)
							$j("#panel"+msg[i].id).append('<tr style="padding:5px"><td><label><input type="checkbox" class="jcheck" name="form[wform_'+msg[i].id+'][]" value="'+uuid+'"/>Key</label></td><td></td></tr>');*/
                        //var vlit = '<tr style="padding:5px"><td align="left" style="width:30%;margin:15px;background:transparent" valign="middle" class="hilite"><label><input type="checkbox" class="jcheck" name="form['+tablename+'][]" value="'+key+'"/>'/*+key+'- '*/+val.title+'</label>'+tempval+'</td><td>';

						var li_z = '<tr>' +
							'<td colspan="2"><select style="width: 250px;display:inline-block;float:left;margin-left:20px;" id="slectOrdre" class="form-control">' +
							'<option>---SELECT---</option>'+
							'<option>ASC</option>' +
							'<option>DESC</option>' +
							'</select>' +
							'<select id="slectChamps"  style="display:inline-block;float:left;margin-left:20px;">' +
							'<option>---SELECT---</option>' +
							'</select>' +
							'<input style="width: 300px;display:inline-block;float:left;margin-left:20px;" id="lmnt" type="number" min="0" class="form-control" value=""></td>' +
							'</tr>';
						// $j("#ulforms").append(li_z);
						$j("#panel"+msg[i].id).append(li_z);
                        $.each(fields.fields, function(key,val){
							optLst+='<option value="'+key+'">' +val.title+
								'</option>';
							tempval = '<span class="title">'+val.title+'</span>';
							if(val.title.length>50){
								val.title = val.title.substring(0, 49)+'...';
							}else{
								tempval = '';
							}
							var vli = '<tr style="padding:5px"><td align="left" style="width:30%;margin:15px;background:transparent" valign="middle" class="hilite"><label><input type="checkbox" class="jcheck" name="form['+tablename+'][]" value="'+key+'"/>'/*+key+'- '*/+val.title+'</label>'+tempval+'</td><td>';
							if(val.type==='entry_date' || val.type==='date'){
								var idstart = 'wform_'+msg[i].id+'_'+key+"_start";
								var idend = 'wform_'+msg[i].id+'_'+key+"_end";
								var keyname = 'wform_'+msg[i].id+'.'+key;
								vli += '&emsp;Start Date :&nbsp<span style="white-space:nowrap;flaot:left">';
							
								vli += '<input type="date" id="'+idstart+'" class="text spCals  hasDatepick" name="beginner" value="" size="10" >';
								//<img src="images/calendar.png" alt="Popup" class="trigger datepick-trigger">
								vli += '</span>';
								
								vli += '&emsp;End Date :&nbsp<span style="white-space:nowrap;flaot:left">';
								vli += '<input type="date" id="'+idend+'" class="text spCals  hasDatepick" name="beginner" value="" size="10" >';
								//<img src="images/calendar.png" alt="Popup" class="trigger datepick-trigger">
								vli += '</span>';
								$j('#'+idstart).live("change",function(){
									startvalue = $j('#'+idstart).val();//$('.'+classname+':checked');
									endvalue = $j('#'+idend).val();
									t = "";
									if(startvalue && endvalue)
										t = keyname + " between '"+startvalue +"' and '"+ endvalue +"'";
									else if(startvalue)
										t = keyname + "' >= '"+startvalue +"'";
									if(t)
										pushWhere(keyname,t);
								});
								$j('#'+idend).live("change",function(){
									startvalue = $j('#'+idstart).val();//$('.'+classname+':checked');
									endvalue = $j('#'+idend).val();
									t = "";
									if(startvalue && endvalue)
										t = keyname + " between '"+startvalue +"' and '"+ endvalue +"'";
									else if(endvalue)
										t = keyname + " <= '"+endvalue+"'";
									if(t)
										pushWhere(keyname,t);
								});
							}else if(val.type==='numeric' || val.type==='calculateNumeric'){
								
								vli += '&emsp;<span class="out_block">';
								var classname = 'wform_'+msg[i].id+'_'+key;
								var keyname = 'wform_'+msg[i].id+'.'+key;
								vli += '<select id="'+classname+'_ope"><option></option><option value="=">=</option><option  value="<"><</option><option value=">">></option></select>&emsp;';
								vli += '<input type="numeric" id="'+classname+'_val">';
								$j('#'+classname+'_val').live("change",function(){
									num_val = $j('#'+classname+'_val').val();
									num_ope = $j('#'+classname+'_ope').val();
									if(num_ope != "" && num_val != ""){
										t = keyname+num_ope+num_val;
										pushWhere(keyname,t);
									}
								});
								$j('#'+classname+'_ope').live("change",function(){
									num_val = $j('#'+classname+'_val').val();
									num_ope = $j('#'+classname+'_ope').val();
									if(num_ope != "" && num_val != ""){
										t = keyname+num_ope+num_val;
										pushWhere(keyname,t);
									}
								});
								vli += '</span>';
								
							}else if(val.type==='plain'){
								
								vli += '&emsp;<span class="out_block">';
								var classname = 'wform_'+msg[i].id+'_'+key;
								var keyname = 'wform_'+msg[i].id+'.'+key;
								vli += '<select id="'+classname+'_ope"><option></option><option value="=">is</option><option  value="<>">is not</option></select>&emsp;';
								vli += '<input type="text" id="'+classname+'_val">';
								$j('#'+classname+'_val').live("change",function(){
									num_val = $j('#'+classname+'_val').val();
									num_ope = $j('#'+classname+'_ope').val();
									if(num_ope != "" && num_val != ""){
										t = keyname+num_ope+num_val;
										pushWhere(keyname,t);
									}
								});
								$j('#'+classname+'_ope').live("change",function(){
									num_val = $j('#'+classname+'_val').val();
									num_ope = $j('#'+classname+'_ope').val();
									if(num_ope != "" && num_val != ""){
										t = keyname+num_ope+num_val;
										pushWhere(keyname,t);
									}
								});
								vli += '</span>';
								
							}else if(val.type==='radio' || val.type==='checkbox' || val.type==='calculateChoice'){
								vli += '&emsp;<span class="out_block">';
								var classname = 'wform_'+msg[i].id+'_'+key;
								var keyname = 'wform_'+msg[i].id+'.'+key;
								$.each(val.sysdata, function(ksys,vsys){
									if(ksys!=-1 && ksys!='rels')
										vli += '<label><input type="checkbox" class="'+classname+'" name="'+classname+'[]" value="'+ksys+'">'+vsys+'</label>';
								});
								$j('.'+classname).live("click",function(){
									checkedValue = $('.'+classname+':checked');
									t = [];
									for(l=0;l<checkedValue.length;l++)
										t[l] = checkedValue[l].value;
									if(t.length>0)
										pushWhere(keyname,t);
								});
								vli += '</span>';
								
							}else if((val.type==='select' || val.type==='multi-select') && val.sysv==='SysCommunalSection'){
								
								vli += '&emsp;<span class="out_block">';
								var classname = 'wform_'+msg[i].id+'_'+key;
								var keyname = 'wform_'+msg[i].id+'.'+key;
								vli += '<select name="lvd_cmp_mode" class="select '+classname+'">';
								vli += '<option value=""></option>';
								$.each(val.sysdata, function(ksys,vsys){
									if(ksys!=-1 && ksys!='rels')
										vli += '<option value="'+ksys+'">'+vsys+'</option>';
								});
								vli += '</select>';
								$j('.'+classname).live("change",function(){
									/*checkedValue = $('.'+classname+':checked');
									t = [];
									for(l=0;l<checkedValue.length;l++)
										t[l] = checkedValue[l].value;*/
									
									pushWhere(keyname,$j('.'+classname).val());
								});
								vli += '</span>';
								
							}else if((val.type==='select' || val.type==='multi-select') && val.sysv==='SysCommunes'){
								
								vli += '&emsp;<span class="out_block">';
								var classname = 'wform_'+msg[i].id+'_'+key;
								var keyname = 'wform_'+msg[i].id+'.'+key;
								vli += '<select name="lvd_cmp_mode" class="select '+classname+'">';
								vli += '<option value=""></option>';
								$.each(val.sysdata, function(ksys,vsys){
									if(ksys!=-1 && ksys!='rels')
										vli += '<option value="'+ksys+'">'+vsys+'</option>';
								});
								vli += '</select>';
								$j('.'+classname).live("change",function(){
									/*checkedValue = $('.'+classname+':checked');
									t = [];
									for(l=0;l<checkedValue.length;l++)
										t[l] = checkedValue[l].value;*/
									
									pushWhere(keyname,$j('.'+classname).val());
								});
								vli += '</span>';
								
							}else if((val.type==='select' || val.type==='multi-select') && val.sysv==='SysDepartment'){
								
								vli += '&emsp;<span class="out_block">';
								var classname = 'wform_'+msg[i].id+'_'+key;
								var keyname = 'wform_'+msg[i].id+'.'+key;
								vli += '<select name="lvd_cmp_mode" class="'+classname+'">';
								vli += '<option value=""></option>';
								$.each(val.sysdata, function(ksys,vsys){
									if(ksys!=-1 && ksys!='rels')
										vli += '<option value="'+ksys+'">'+vsys+'</option>';
								});
								vli += '</select>';
								$("."+classname).multiselect(); 
								$j('.'+classname).live("change",function(){
									/*checkedValue = $('.'+classname+':checked');
									t = [];
									for(l=0;l<checkedValue.length;l++)
										t[l] = checkedValue[l].value;*/
									pushWhere(keyname,$j('.'+classname).val());
								});
								vli += '</span>';
								
							}else if((val.type==='select' || val.type==='multi-select') && !isNaN(val.sysv)){
								var classname = 'wform_'+msg[i].id+'_'+key;
								var keyname = 'wform_'+msg[i].id+'.'+key;
								vli += '&emsp;<span class="out_block" id='+keyname+'_span>';
								
								/*arraydata = val.sysdata;
								var chunk = 3;
								arrayf = [];
								console.log(arraydata);
								for (x=0,y=arraydata.length; x<y; x+=chunk) {
								    temparray = arraydata.slice(x,x+chunk);
								    // do whatever
								    arrayf.push(temparray);
								    
								}*/
								//console.log(arrayf);
								vli += '<table>';
								chunk = 3;
								vli += '<tr>';
								$.each(val.sysdata, function(ksys,vsys){
									if(ksys!=-1 && ksys!='rels'){	
										vli += '<td><label><input type="checkbox" class="'+classname+'" name="'+classname+'[]" value="'+ksys+'">'+vsys+'&nbsp;</label></td>'
										chunk--;
										if(chunk==0){
											vli += '</tr><tr>';
											chunk = 3;
										}
									}
								});
								vli += '</tr>';
								vli += '</table>';
								$j('.'+classname).live("click",function(){
									checkedValue = $('.'+classname+':checked');
									t = [];
									for(l=0;l<checkedValue.length;l++)
										t[l] = checkedValue[l].value;
									pushWhere(keyname,t);
								});
								vli += '</span>';
								
							}else{
								
							}
							/*if(val.type==='entry_date' || val.type==='date'){
								vli += '&emsp;Start Date :&nbsp<span style="white-space:nowrap;">';
								vli += '<input type="text" class="text spCals  hasDatepick" name="beginner" id="start_date" value="" size="10" readonly="readonly"><img src="images/calendar.png" alt="Popup" class="trigger datepick-trigger">';
								vli += '</span>';
								
								vli += '&emsp;End Date :&nbsp<span style="white-space:nowrap;">';
								vli += '<input type="text" class="text spCals  hasDatepick" name="beginner" id="start_date" value="" size="10" readonly="readonly"><img src="images/calendar.png" alt="Popup" class="trigger datepick-trigger">';
								vli += '</span>';
							}*/
					  		vli += '</td></tr>';
							// Inclusion des li(s) creer dans les structures conditionnelles pour l 'ajouter dans le tableau contenant le formulaire
							$j("#panel"+msg[i].id).append(vli);
							// Inclusion des li(s) creer dans les structures conditionnelles pour l 'ajouter dans le tableau contenant le formulaire
						});
						
						if(fields.section){
							$.each(fields.section, function(key,val){
								var classname = key;
								var li = '<tr><td valign="top" colspan="2"><strong>'+val['name']+'</strong></td></tr>';
								var ftext = 'fields';
								var ntext = 'name';
							     li += '<tr><td colspan="2"  align="left" style="width:30%;margin:15px;background:transparent" valign="middle" class="hilite"><input type="hidden" class="jcheck" name="wform_'+msg[i].id+'['+ntext+']" value="'+key+'">'+'<table  class="std" border="1" cellpadding="3" cellspacing="0" style="background:transparent;margin-left:20px;list-style:none;border-collapse: collapse;border-color: #a5cbf7;">';
							     
							     $.each(val['fields'], function(keyf,valf){
							    	 li +=  '<tr><td align="left" style="width:40%;margin:15px;background:transparent" valign="middle" class="hilite"><label><input type="checkbox" class="jcheck" name="wform_'+msg[i].id+'['+ftext+'][]" value="'+keyf+'">'+keyf+' '+valf['title']+'&nbsp;</label></td><td>'
							    	 if(valf['type']==='date'){
											var idstart = key+'_'+keyf+"_start";
											var idend = key+'_'+keyf+"_end";
											var keyname = key+'.'+keyf;
											li += '&emsp;Start Date :&nbsp<span style="white-space:nowrap;flaot:left">';
											li += '<input type="date" id="'+idstart+'" class="text spCals  hasDatepick" name="beginner" value="" size="10" >';
											//<img src="images/calendar.png" alt="Popup" class="trigger datepick-trigger">
											li += '</span>';
											li += '&emsp;End Date :&nbsp<span style="white-space:nowrap;flaot:left">';
											li += '<input type="date" id="'+idend+'" class="text spCals  hasDatepick" name="beginner" value="" size="10" >';
											//<img src="images/calendar.png" alt="Popup" class="trigger datepick-trigger">
											li += '</span>';
											$j('#'+idstart).live("change",function(){
												startvalue = $j('#'+idstart).val();//$('.'+classname+':checked');
												endvalue = $j('#'+idend).val();
												t = "";
												if(startvalue && endvalue)
													t = keyname + " between '"+startvalue +"' and '"+ endvalue +"'";
												else if(startvalue)
													t = keyname + "' >= '"+startvalue +"'";
												if(t)
													pushWhere('sec_'+keyname,t);
											});
											$j('#'+idend).live("change",function(){
												startvalue = $j('#'+idstart).val();//$('.'+classname+':checked');
												endvalue = $j('#'+idend).val();
												t = "";
												if(startvalue && endvalue)
													t = keyname + " between '"+startvalue +"' and '"+ endvalue +"'";
												else if(endvalue)
													t = keyname + " <= '"+endvalue+"'";
												if(t)
													pushWhere('sec_'+keyname,t);
											});
										}else if(valf.type==='plain'){
											
											li += '&emsp;<span class="out_block">';
											var classname = key+'_'+keyf;
											var keyname = key+'.'+keyf;
											li += '<select id="'+classname+'_ope"><option></option><option value="=">is</option><option  value="<>">is not</option></select>&emsp;';
											li += '<input type="text" id="'+classname+'_val">';
											$j('#'+classname+'_val').live("change",function(){
												num_val = $j('#'+classname+'_val').val();
												num_ope = $j('#'+classname+'_ope').val();
												if(num_ope != "" && num_val != ""){
													t = keyname+num_ope+num_val;
													pushWhere('sec_'+keyname,t);
												}
											});
											$j('#'+classname+'_ope').live("change",function(){
												num_val = $j('#'+classname+'_val').val();
												num_ope = $j('#'+classname+'_ope').val();
												if(num_ope != "" && num_val != ""){
													t = keyname+num_ope+num_val;
													pushWhere('sec_'+keyname,t);
												}
											});
											li += '</span>';
											
										}else if(valf.type==='radio' || val.type==='checkbox'){
											
											li += '&emsp;<span class="out_block">';
											var classname = key+'_'+keyf;
											var keyname = key+'.'+keyf;
											$.each(valf.sysdata, function(ksys,vsys){
												if(ksys!=-1 && ksys!='rels')
													li += '<label><input type="checkbox" class="'+classname+'" name="'+classname+'[]" value="'+ksys+'">'+vsys+'</label>';
											});
											$j('.'+classname).live("click",function(){
												checkedValue = $('.'+classname+':checked');
												t = [];
												for(l=0;l<checkedValue.length;l++)
													t[l] = checkedValue[l].value;
												if(t.length>0)
													pushWhere('sec_'+keyname,t);
											});
											li += '</span>';
											
										}else if((valf.type==='select' || valf.type==='multi-select')){
											
											li += '&emsp;<span class="out_block">';
											var classname = key+'_'+keyf;
											var keyname = key+'.'+keyf;
											li += '<select name="lvd_cmp_mode" class="select '+classname+'">';
											li += '<option value=""></option>';
											$.each(valf.sysdata, function(ksys,vsys){
												if(ksys!=-1 && ksys!='rels')
													li += '<option value="'+ksys+'">'+vsys+'</option>';
											});
											li += '</select>';
											$j('.'+classname).live("change",function(){
												/*checkedValue = $('.'+classname+':checked');
												t = [];
												for(l=0;l<checkedValue.length;l++)
													t[l] = checkedValue[l].value;*/
												
												pushWhere('sec_'+keyname,$j('.'+classname).val());
											});
											li += '</span>';
											
										}
							    	 
							    	 
							    	 li += '</td></tr>';
							     });
							     li += "</table>";
								
								li += '</td></tr>';
								$j("#panel"+msg[i].id).append(li);
							});
						}
						//$('.multiple').multiple();
						$j('#slectChamps').append(optLst);
					}
					
					
					if(msg[i].parent_id!='0'){
						var fields1 = msg[i].parent_id.fieldsv;
						var li1 = '<li><div class="switch" id="switch'+msg[i].id+'_'+msg[i].parent_id.id+'" style="color: blue;font-size: large"></div>'+msg[i].parent_id.title;
						li1 += '<ul id="panel'+msg[i].id+'_'+msg[i].parent_id.id+'" style="display:none;list-style:none"></ul>';
						li1 += '</li>';
						$j("#panel"+msg[i].id).append(li1);
						if(fields1){
							
							$.each(fields1.fields, function(pkey,pval){
								var pli = '<li><label><input type="checkbox"  class="jcheck" name="form[wform_'+msg[i].id+'][ref][wform_'+msg[i].parent_id.id+'][]" value="'+pkey+'"/>'+pval.title+'</label>';
								
								pli += '</li>';
								
								$j("#panel"+msg[i].id+'_'+msg[i].parent_id.id).append(pli);
								
							});
							
						}
						$j("#switch"+msg[i].id+'_'+msg[i].parent_id.id).live("click", function () {
							if($j("#panel"+msg[i].id+'_'+msg[i].parent_id.id).is(':visible')){
								$j("#panel"+msg[i].id+'_'+msg[i].parent_id.id).hide();
								$j("#switch"+msg[i].id+'_'+msg[i].parent_id.id).attr('style','background-position: 0px -144px;');
							}else{
								$j("#panel"+msg[i].id+'_'+msg[i].parent_id.id).show();
								$j("#switch"+msg[i].id+'_'+msg[i].parent_id.id).attr('style','background-position: -12px -144px;');
							}
						});
					}
					$j("#switch"+msg[i].id).live("click", function () {
						if($j("#panel"+msg[i].id).is(':visible')){
							$j("#panel"+msg[i].id).hide();
							$j("#switch"+msg[i].id).attr('class','switch fa fa-plus');
						}else{
							$j("#panel"+msg[i].id).show();
							$j("#switch"+msg[i].id).attr('class','switch fa fa-minus');
						}
					});
				});
			} else {				
				$j("#msgbox").text("Invalid form selected!").show().delay(2000).fadeOut(3000);			
			}
			$('#imgloader').hide();
		}).fail(function() {
			$('#imgloader').hide();
		});	
	}

	/**
	 * Obtain list of instances for selected Dataset
	 * @param dval
	 */
	function updateInstances(dval) {
		var dp = dval.split("||");
		$j(".instance_select", $accd).empty().attr("disabled", true);
		if (dp[0] > 0) {
			$j.get("/?m=outputs&a=gchart_be&suppressHeaders=1", {xmode: 'getInstances', did: dp[0]}, function (m) {
				m = trim(m);
				if (m && m.length > 0) {
					$j(".instance_select", $accd).html(m).attr("disabled", false).parent().show();
				} else {
					info("Failed to get instances for selected Dataset", 0);
				}
			});
		}
	}

	/**
	 * Activate next folder
	 */
	function waterFall() {
		var active = $accd.accordion("option", "active");
		$accd.accordion("option", "active", ++active).accordion('refresh');
	}

	/**
	 * Redefine data source area - datasets or lists
	 * @param val name of area
	 */
	function changePart(val) {
		currentPart = val;
		$j(".area_select, .instance", $accd).hide();
		$j("#filters", $accd).find("." + currentPart).find(".area_select").show();
	}

	function getLevelNames(lid) {

		var tr;
		for (var e in alevels) {
			if (alevels.hasOwnProperty(e)) {
				if (e == lid.svs) {
					tr = alevels[e];
				}
			}
		}

		var dLevel = $j.inArray(tr.toLowerCase(), levelOrder);
		++dLevel;
		if (amnt(locationNames[dLevel]) > 0) {
			return true;
		} else {
			fieldCfg.location.isLevel = dLevel;
			lanames = $j.get("/?m=outputs&a=gchart_be&suppressHeaders=1&xmode=getLevelNames", {level: dLevel});
			$j.when(lanames).done(function (m) {
				if (m && m.length > 0) {
					var t = $j.parseJSON(m);
					if (amnt(t) > 0) {
						locationNames[dLevel] = t;
					} else {
						info("Locations not found in Database", 0);
					}
				} else {
					info("Failed to receive location names", 0);
				}
				lanames = false;
			});
			return lanames.promise();
		}
	}

	function loadFilters(pval) {
		$fblock.children().hide().end().append($loadico.clone());
		fopts = [];
		dataListID = pval;
		$j.get("/?m=outputs&a=gchart_be&suppressHeaders=1", {xmode: "getFields", apart: currentPart, sval: pval}, function (m) {
			if (m && m.length > 0) {
				var fdz = $j.parseJSON(m);
				if (fdz && amnt(fdz) > 0) {
					for (var f in fdz) {
						if (fdz.hasOwnProperty(f)) {
							if (fdz[f].type == 'select' && $j.inArray(fdz[f].svs, alevIds) >= 0) {
								fieldCfg.location = fdz[f];
								getLevelNames(fieldCfg.location);
							} else {
								fopts.push(fieldOption(fdz[f]));
							}
						}
					}
					if (fopts.length > 0) {
						$j("#fields").html(fopts.join("")).show();
						$accd.accordion('refresh');
						$j("#fields_block").css("height", "auto");
					}
				} else {
					info("No fields found for selected item", 0);
				}
			} else {
				info("Failed to retrieve info about selected item", 0);
			}
			$fblock.find(".loadingz").remove().end().find("#fields").show();
			waterFall();
		});
	}

	/**
	 * Call geocharting script with new parameters
	 */
	function draw2(cfg, data) {

		var prearr = [], tr = [], metLocs = {}, doAdd = false, tv = 0;
		prearr.push(cfg.header);
		for (var v in data) {
			if (data.hasOwnProperty(v)) {
				tr = [];
				doAdd = false;
				for (var x = 0, cx = cfg.body.length; x < cx; x++) {
					var rcv = data[v][cfg.body[x]];
					if (x === 0) {
						if (metLocs.hasOwnProperty(rcv)) {
							doAdd = metLocs[rcv];
						} else {
							metLocs[rcv] = (prearr.length);
						}
						rcv = (locationNames[cfg.dlevel][rcv]);
					}
					if (doAdd === false) {
						tr.push(rcv);
					} else {
						if (x > 0) {
							tv = Number(prearr[doAdd][x]) + Number(rcv);
							prearr[doAdd][x] = Number(tv);
							prearr[doAdd][x] = Number(prearr[doAdd][x].toFixed(2));
							var c = 1;
						}
					}

				}
				if (doAdd === false) {
					prearr.push(tr);
				}
			}
		}

		var data2 = google.visualization.arrayToDataTable(prearr);


		var options = {
			region: 'GE',
			displayMode: 'markers',
			colorAxis: {colors: ['green', 'red']}
			/*tooltip: {textStyle: {color: '#FF0000'}, showColorCode: true},
			 legend: {
			 textStyle: {color: 'blue', fontSize: 16},
			 numberFormat: ".##"
			 }*/
		};


		var cd = document.getElementById("chart_div");
		//cd.innerHTML = "";
		if (!xChart) {
			xChart = new google.visualization.GeoChart(cd);
		} else {
			xChart.clearChart();
		}
		google.visualization.events.addListener(xChart, "error", careChart);
		xChart.draw(data2, options);

	}

	function getLL(id, name) {
		var r = new $j.Deferred();

		gCoder.geocode({ 'address': name }, function (results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				r.resolve([id, name,  results[0].geometry.location]);
			}
		});
		return r.promise();
	}

	function draw(cfg, data) {
		var mapOptions = {
			center: new google.maps.LatLng(42.0796, 43.46953),
			zoom: 8,
			panControl: true,
			zoomControl: true,
			scaleControl: true,
			mapTypeControl: true
		},
		map = new google.maps.Map(document.getElementById("chart_div"),	mapOptions);

		var markIds =[];

		var prearr = [], tr = [], metLocs = {}, doAdd = false, tv = 0,td=[];
		prearr.push(cfg.header);
		for (var v in data) {
			if (data.hasOwnProperty(v)) {
				td = []; tr = [];
				doAdd = false;
				for (var x = 0, cx = cfg.body.length; x < cx; x++) {
					var rcv = data[v][cfg.body[x]];
					if (x === 0) {
						if (metLocs.hasOwnProperty(rcv)) {
							doAdd = metLocs[rcv];
						} else {
							metLocs[rcv] = (prearr.length);
						}
						rcv = (locationNames[cfg.dlevel][rcv]);
					}
					if (doAdd === false) {
						x === 0? tr.push(rcv) : td.push(rcv);
						//td.push(rcv);
					} else {
						if (x > 0) {
							td.push(rcv);
							/*tv = Number(prearr[doAdd][x]) + Number(rcv);
							prearr[doAdd][x] = Number(tv);
							prearr[doAdd][x] = Number(prearr[doAdd][x].toFixed(2));*/
							//var c = 1;
						}
					}

				}
				if (doAdd === false) {
					tr.push([td]);
					prearr.push(tr);
				}else{
					prearr[doAdd][1].push(td);
				}
			}
		}
		for (var i= 1, xl = prearr.length; i < xl; i++){
			$j.when (getLL(i, prearr[i][0])).done (function(arts){
				var marker = new google.maps.Marker({
					position: arts[2],
					map: map,
					title: prearr[arts[0]][0]
				});
				markIds[marker.__gm_id] = arts[0];
				google.maps.event.addListener(marker, 'click', function() {
					infowindow.open(map,marker);
					infowindow.setContent (popForm(cfg.header, prearr[markIds[marker.__gm_id]][1]));
				});
			});


		}

		google.maps.event.addListener(map, 'click',function(evt){
			addMark(evt.latLng);
		});
	}


	function popForm (cfg, data){
		var ctab = '<table>' +
			'<tr>';
		for (var i= 1, zl = cfg.length; i < zl; i++){
			ctab+="<th>"+cfg[i]+"</th>";
		}
		ctab+='</tr>';
		if (data && data.length > 0){
			for (var i= 0, zl = data.length; i < zl; i++){
				var tar = data[i];
				ctab+="<tr>";
				for (var v=0, vl = tar.length; v < vl; v++){
					ctab+="<td>"+tar[v]+"</td>";
				}
				ctab+="</tr>";
			}
		}
		return ctab+'</table>';
	}



	return {
		init: function () {
			
			start();
		}
	};
})(charter);


function careChart() {
	alert(1);
}
