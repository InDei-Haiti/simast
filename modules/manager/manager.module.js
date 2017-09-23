var $tabs;
function xstart(){	
	if(tabLaunch ){
		eval(tabLaunch);
	}
	$tabs = $j("#tabs");	
	$tabs.tabs()
		.find(".topnav  a").click(function(e){
			if ($j(this).data("pass") === true) {
				$j(this).data("pass", false);
			}
			else {
				var id = $j(this).attr("href").match(/\d/);
				manager.getTab(id[0]);
			}
		})
	.end().show();
	if(openTab > 1){
		manager.tabToView(openTab);
		$tabs.mtoTab((openTab-1));
	}
}

$j.fn.mtoTab = function (tid){
	//manager.getTab(tid);
	$j("ul.topnav > li:eq("+tid+")",this).find("a").data("pass",true).trigger("click");
	return this;
}

var manager = (function(my){
	var showTabs = ["1"], tabLinks = {
			2:'activity_queries',
		3: 'hdvi_form',
		4: 'transfer-out',
		5: 'importer',
		6: 'exported'
	},linkPrefix='/?m=manager&suppressHeaders=1&a=',
	tabLoadingIcon= new Image(),currentId=false,
	extraActions={
					2:'$j(".undobutt",$j("table.tin")[0]).live("click",function(e){e.stopPropagation();manager.undo(this);});$j("table.tin").tablesorter();',
					3:'$j(".undobutt",$j("table.tout")[0]).live("click",function(e){e.stopPropagation();manager.undo(this);});$j("table.tout").tablesorter({headers: { 0: {sorter: "idlink"}, 1: {sorter:"itlink"}, 2: { sorter:"idate" } } });', //
					5:'$j(".exterm").live("click", function(e){manager.dropExported(this);});'
					
	},afterLoaded='';
	tabLoadingIcon.src='/modules/outputs/images/ajax-loader.gif';
	
	function refreshTab(id){
		var xpos=$j.inArray((id+''),showTabs);
		delete showTabs[xpos];
		loadTab(id);
	}
	
	function loadTab(id){		
		if($j.inArray(id,showTabs) >= 0){
			return true;
		}else{
			$tabs.mtoTab(id-1);
			var $ctab=$j("div#tabs-"+id,$tabs),$mdt=$j(".mandat",$ctab).detach();
			$ctab.empty().append(tabLoadingIcon);
			console.log(linkPrefix + tabLinks[id]);
			$j.get(linkPrefix + tabLinks[id],function(msg){
				$ctab.empty().append($mdt).append(msg);
				showTabs.push(id+'');
				if(extraActions.hasOwnProperty(id))	{
					eval(extraActions[id]);
					//delete(extraActions[id]);
				}
			});
		}
	}
	
	function deleteFile(obrow){
		var eid=$j(obrow).attr("data-fid");
		if(confirm("Delete this exported file from system?")){
    	    	    $j.get("/?m=manager&suppressHeaders=1&mode=dropex",{dfid:eid},function(msg){
			if(msg && msg === 'ok'){
				$j(obrow).closest("tr").slideUp("slow",function(){
					$j(this).remove();
				})
			}
		    });
		}
	}
	
	function waitServerMsg(tabID){		
		setTimeout(function(){
			$j.ajax({
				url: linkPrefix+"monex",
				type: 'get',
				async: true,
				data: "ekey=" + currentId,
				success: function(msg){
					if (msg && msg === 'ok') {
						currentId=false;
						$j("#start_ex").attr("disabled",false);
						refreshTab(tabID);		
						eval(afterLoaded);
						afterLoaded='';				
					}
				}
			});
		},1000);
	}
	
	function transferBack(rcell){
		var $row=$j(rcell).closest("tr"),
			adm_no=$row.find("td:eq(0)").text(),
			clinic_id=$row.attr("data-clinic"),
			client_id=$row.attr("data-clid");
			$row.fadeTo(500,0.3);
		$j.get("/?m=manager&suppressHeaders=1&mode=transfer_back",{"client_id":client_id,'clinic': clinic_id},function(msg){
			if(msg && msg === 'ok'){
				var centers=$j("#sample_select").html().replace("##CLNT##",client_id);
				$row
					.toggleClass("past future")
					.find("td:lt(2)").find("a").toggleClass("exported fresh_users").end().end()
					.find("td:eq(2)").find("input:eq(1)").val(0).end()
					.next().html(centers)
					.next().html("");
				$row.fadeTo(500,1);
				$j("#tabfinish").attr("disabled",false);
			}
		});		
		return false;			
	}
	
	function departure(obj){
		var $row=$j(obj).closest("tr"),
		trin_id=$row.attr("data-tid");
		$row.fadeTo(500,0.3);
		$j.get(linkPrefix+"parse_tin&mode=undo",{'row_id':trin_id},function(msg){
			if(msg && msg === 'ok'){
				$row
				.find("td:eq(1)").toggleClass("past future").end()
				.find("td:eq(4)").html("Not Done");				
			}			
			$row.fadeTo(500,1);
		});
		return false;		
	}
	
	function postClinClients (){
		var vf=form2object("xlist");
		$j.ajax({
			url	: '?m=manager&mode=makecenters&suppressHeaders=1',
			type: 'post',
			data: 'cparts='+JSON.stringify(vf)+"&ekey=" + currentId,			
			success: function(msg){
				if(msg && msg === 'ok'){												
						var idata=$j.parseJSON(msg);
						showButs(idata);
					}else{
						alert("Parsing center assignment failed");
					}
				
			}
		});
	}
	
	function itererD (){
		var $cli_outs=$j("#clitab"),rows = $j("tbody",$cli_outs).find("tr.future"),res=true;
		for(var i=0,l = rows.length; i < l; i++){
			if($j("td:eq(3) > select.d2chk",$j(rows[i])).val() == "0"){
				$j(rows[i]).find("td:eq(3)").addClass("bcell");
				res=false;								
			}else{
				$j("td:eq(3)",$j(rows[i])).removeClass("bcell");
			}			
		}
		if(res === false){
			alert("Please define valid center!");
			$j(".bcell > select",$cli_outs).change(function(e){
				if($j(this).val() > 0){
					$j(this).parent().removeClass("bcell");
				}
			});
		}else{
			$j(".bcell > select",$cli_outs).unbind("change");
		}
		return res;		
	}
	
	function doImportClients(){
		return false;
	}
	
	
	return {
		getTab: function(tabId){
			loadTab(tabId);
		},
		tabToView: function(id){
			showTabs.push(id+'');
		},
		importDone: function(msg){
			if(msg && msg === 'ok'){				
				$j("#importbox").slideUp('fast');				
				$j("div#tabs-4",$tabs).find(".mandat").find("#msg_place")
					.addClass("msg_ok").html("<br>File successfully uploaded. Select file to import<br>").show().delay(3000).fadeOut(2000,function(){
						$j(this).removeClass("msg_ok").hide();
					});
				refreshTab(4);

					
			}
		},
		exportDone: function(){
			return true;			
		},
		initExport: function(){
			$j("#start_ex").attr("disabled",true);
			currentId=randomString();
			document.doEx.skey.value=currentId;
			waitServerMsg(5);			
		},
		dropExported: function(obj){			
			deleteFile(obj);
		},
		undo: function(obj){
			var $tabcase=$j(obj).closest("table");
			if ($tabcase.hasClass('tout')) {
				if (confirm("Undo Transfer-Out? Client status will be set back to previous state")) {
					transferBack(obj);
				}
			}else if($tabcase.hasClass('tin')){
				if (confirm("Remove this client from center?")) {
					departure(obj);
				}
			}
		},
		checkDrops: function(){
			var act=itererD();
			if(act === true){
				currentId=randomString();				
				$j("#tabfinish").attr("disabled",true);
				document.xlist.ekey.value=currentId;
				afterLoaded='manager.fixTab();';
				waitServerMsg(3);
				$j("#xlist").submit();							
			}
		},
		forzip: function(){					
			return true;
		},
		fixTab: function(){
			$j("#tabs").find("ul.topnav").find("li:eq(2)").find("b").remove();
		},
		reloadTab: function(id){
			refreshTab(id);
		},
		tinDone: function(msg){
			if(msg){
				if(msg === 'wrong_center'){
					alert("You are trying to import file for another center!");
					return false;
				}else if (msg.match(/\d+/) && parseInt(msg) > 0){
					$j("#tinbox").slideUp('fast');
					refreshTab(2);
				}
			}
		},
		startTIN: function(but){			
			$j(but).attr("disabled",true).after("<span>Importing...</span>");
			$j.get(linkPrefix+"parse_tin&mode=proceed",function(msg){
				if (msg && msg === 'ok') {
					refreshTab(2);
					res = true;
				}else{
					$j(but).attr("disabled", false).next().remove();
				}				
			});			
		}
	};
	
	
}(manager));

function randomSelect(zclass){
	$j("select."+zclass).each(function(){
		var opts = $j(this).find("option").length, pick = (function(opts){
			var res=0;
			while (res === 0) {
				res=Math.floor(Math.random() * opts);
			}
			return res;
		})(opts);
		$j(this).find("option:eq("+pick+")").attr("selected",true);		
	});
}
function populateHDVIVar(jsonobj,id){
	$.each(jsonobj[id]['members'], function(key,value){
		$j("#member").append("<option value='"+key+"'>"+value['name']+"</option>");
	});
	$j.get("?m=wizard&fid="+id+"&fld="+fld[$j("#commun").attr('id')]+"&mode=loadFldValDistinct&suppressHeaders=1", function (msgres) {
		if (msgres && msgres !== 'fail') {	
			msgres = $j.parseJSON(msgres);
			$.each(msgres, function(code,name){
				$j("#commun").append("<option value='"+code+"'>"+name+"</option>");
			});
		}
	});
	$j("select.household_fld").each(function(){
		$select = $j(this);
		$select.append("<option></option>");
		$.each(jsonobj[id]['fields'], function(keym,valuem){
			$select.append("<option value='"+valuem['fld']+"'>"+valuem['title']+"</option>");
		});
		$select.val(fld[$select.attr('id')]);
	});
	val = $j("#member").val();
	if( val > 0){
		$j("select.member_fld").each(function(){
			$select = $j(this);
			$select.append("<option></option>");
			$.each(jsonobj[id]['members'][val]['fields'], function(keym,valuem){
				$select.append("<option value='"+valuem['fld']+"'>"+valuem['title']+"</option>");
			});
			$select.val(member_fld[$select.attr('id')]);
		});
		
		$(".chosen").trigger("liszt:updated");
	}
	$j("#member").change(function(e){
		val = $j(this).val();
		if( val > 0){
			$j("select.member_fld").each(function(){
				$select = $j(this);
				$select.append("<option></option>");
				$.each(jsonobj[id]['members'][val]['fields'], function(keym,valuem){
					$select.append("<option value='"+valuem['fld']+"'>"+valuem['title']+"</option>");
				});
				$select.val(member_fld[$select.attr('id')]);
			});
			
			$(".chosen").trigger("liszt:updated");
			//$(".chosen").trigger("chosen:updated");
		}
	});
}
function loadHDVIVar(project_idx){
	$j("#member").empty();
	fld = {"commun":"fld_1","milieu":"fld_3","absence_of_food":"fld_30","hunger":"fld_31","restricted_consumption":"fld_32","materiau_floor":"fld_18","materiau_wall":"fld_14","materiau_roof":"fld_16","number_of_romm":"fld_20","lighting_access":"fld_22","energy_access":"fld_25","potable_water":"fld_26","cleaning_water":"fld_27","toilet_acces":"fld_21","waste_evacuation":"fld_23","complete":"fld_224", "hdr_1_1":"fld_231","hdr_1_2":"fld_232","hdr_2_1":"fld_234","hdr_2_2":"fld_233","hdr_3_1":"fld_235","hdr_3_2":"fld_236","hdr_3_3":"fld_237","hdr_3_4":"fld_238","hdr_4_1":"fld_239","hdr_4_2":"fld_240","hdr_4_3":"fld_241","hdr_5_1":"fld_243","hdr_5_2":"fld_242","hdr_5_3":"fld_244","hdr_6_1":"fld_245","hdr_6_2":"fld_246","hdr_6_3":"fld_247","hdr_7_1":"fld_248","hdr_7_2":"fld_249","hdr_7_3":"fld_250","step7":"fld_225","hdvi":"fld_226","vulnerability":"fld_217","depr_sali":"fld_227"};
    member_fld = {"member_fld_age":"fld_5","member_fld_linkparent":"fld_2","member_fld_lsickness":"fld_8","member_prob_speak":"fld_13","member_prob_hear":"fld_12","member_prob_autooins":"fld_14","member_prob_eye":"fld_11","member_fld_read":"fld_15","member_fld_write":"fld_16","member_fld_act_edu":"fld_19","member_fld_level_edu":"fld_20","member_fld_lst_scho_12":"fld_18","member_fld_eco_active":"fld_21","member_transf":"fld_22","member_supp":"fld_23"};
    $j.get("?m=wizard&pid="+project_idx+"&mode=loadHDVIVar&suppressHeaders=1", function (msg) {
    	
		if (msg && msg !== 'fail') {
			
			msg = $j.parseJSON(msg);
			console.log(msg);
			var compter = 0;
			var defaultid;
			$.each(msg, function(id,obj){
				if(compter==0){
					defaultid = id;
				}
				$j('#household').append('<option value="'+id+'">'+obj['name']+'</option>');
				compter++
			});
			
			populateHDVIVar(msg,defaultid);
			
			
			$(".chosen").trigger("liszt:updated");
			
			
			
			
			
		} else {
			$j("#msgbox").text("Can't get activity!").show().delay(2000).fadeOut(3000);
		}
	});
}
