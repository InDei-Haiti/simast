/**
 * Created by JetBrains PhpStorm.
 * User: stig
 * Date: 27.08.11
 * Time: 19:32
 * To change this template use File | Settings | File Templates.
 */

function closeTree(part,lpart){
	$('#locations_skin').hide();
	var ins=[],$tgt;
	$('#land > #locations input:checked.last').each(function(){
		ins.push($(this).attr('id'));
	});
	if(lpart >= 0){
		$tgt = $("#act_box > li:eq("+lpart+")").find(".tree_edit[data-part='"+part+"']");
	}else{
		$tgt = $(".tree_edit[data-part='"+part+"']");
	}
	var nstxt=$tgt.closest("tr").find("td:first").text();
	$tgt.parent().find(".snote").text(function(i, c){
		if(ins.length > 0)
			return nstxt +' selected';
		else
			return '';
	}).end().end()
		.next().val( ins.join(",") );
	//$('#lox_box input:checked').attr('checked', false);
}

function updComps (onList){
	var puids =[];
	onList = (onList ? onList : []);
	$("#pdonor").multiselect("getChecked").each(function(){
		puids.push($(this).val());
	});
	if(puids.length > 0){
		jQuery.post("/?m=projects&a=addedit&suppressHeaders=1&mode=getcomps",{progs: puids.join(",")},function(msg){
			if(msg && msg.length > 0 && msg != 'fail'){
				var ncomps = JSON.parse(msg),
					$clist = $("#pcomp_list");
				$clist.multiselect("getChecked").each(function(){
					onList.push($(this).val());
				});
				$clist.empty();
				if(ncomps){
					for (var ni in ncomps){
						if(ncomps.hasOwnProperty(ni)){
							$clist.append(["<option ",($.inArray(ncomps[ni]['id'],onList) >=0 ? 'selected="selected" ': '')," value='",ncomps[ni]['id'],"'>",ncomps[ni]['ctitle'],"</option>"].join(""));
						}
					}
				}else{
					$clist.attr("disabled",true);
				}
				$clist.multiselect("refresh");
			}
		});
	}else{
		$("#pcomp_list").empty().multiselect("refresh");
	}
}

function annualBudget(bob){
	$("#bub").append("<ol id='yblist'></ol>").dialog({
		modal: true,
		width: "410px",
		title: "Edit annual budget for " + ab_word,
		buttons: {
			Add: function(){
				var lasty =$("#yblist").find("li:last select").val();
				$("<li/>")
					.append($("#year_shelter > select").clone(true).val((lasty > 0 ? ++lasty : '')))
					.append("&nbsp;&nbsp;&nbsp;<input type='text' class='text' value='' >")
					.append("&nbsp;&nbsp;&nbsp;<button class='text del_ab'>X</button>")
				.appendTo("#yblist");
				$("#yblist").find("li:last input.text").focus();

			},
			Save: function(){
				var res=[];
				$("#yblist").find("li").each(function(){
					res.push( [$("select",this).val(),"|",$(".text:eq(1)",this).val()].join(""));
				});
				$(bob).next().val(res.join(";"));
				$("#bub").dialog("destroy").empty();
			},
			Close: function(){
				$("#bub").dialog("destroy").empty();
			}
		}
	});
	var ian = $(bob).next().val(),$toaddz;
	if(ian  && ian.length > 0){
		var ilist = ian.split(";");
		for(var i=0,l=ilist.length; i < l; i++){
			var vl = ilist[i].split("|");
			$("<li/>")
				.append($("#year_shelter > select").clone(true).val(vl[0]))
				.append("&nbsp;&nbsp;&nbsp;<input type='text' class='text' value='"+ vl[1] +"' >")
				.appendTo("#yblist")
		}
	}
}



$(document).ready(function(){
    $j("<div id='lmonitor'>").text("Loading...").appendTo(document.body);
	$(".dfield").live("click",function(){
		$(this).datepicker({
			dateFormat: 'dd/mm/yy',
			showOn: 'focus',
			changeMonth: true,
			changeYear: true
		}).focus();

	});

	$(".del_ab").live("click",function(){
		$(this).closest("li").remove();
	});

	$(".multiple").multiselect();

	$("#lox_shut").live("click",function(){
		closeTree($("#locations_skin").attr("data-view"), $("#locations_skin").attr("data-lpart"));
	});

	$(".tree_edit").live("click",function(){
		$(document.body).css("cursor","wait");
		$("#lmonitor").show();
		// console.log($("#lmonitor").html()	);
		var self=this,
			liact = ( $(this).closest("#act_box").length == 1 ? $(this).closest("li").index() : -1),
			orf = {
				fin : function(){
					var $t = $(self), offset = $t.offset(),
					height = $t.outerHeight(true),
					part = $t.attr("data-part");
					// Checking if we trying to close or open it
					if ( $('#locations_skin').is(':visible') ) {
						//closing
						var vpart =$("#lox_box").attr("data-view");
						if(vpart == part){
							return false;
						}else{
							closeTree(vpart,liact);
						}
					}  //opening
					var cur_location = $t.next().val(),
					$lbx = $("#land").html($("#"+part+"_box").html()).find("ul:first").attr("id","locations").end().detach();
					//$('#land  > #locations')
					$lbx.find("#locations")
							.checkboxTree({
								collapseDuration: 100,
								expandDuration: 100,
								initializeChecked: 'expanded',
								initializeUnchecked: 'collapsed',
								onCheck:{
									ancestors: 'check',
									descendants: ''
								},
								onUncheck:{
									ancestors: '',
									descendants: 'uncheck'
								}
					});
					if ( cur_location.length > 0 ) {
						$('#locations',$lbx).checkboxTree('check', $( '#'+cur_location.replace(/,/g, ',#'), $lbx));
					}
					$('#locations_skin').css({
						top: offset.top + height,
						left: offset.left
					}).append($lbx).show().attr({
						"data-view": part,
						"data-lpart":liact
					});
					$("#lmonitor").hide();
					$(document.body).css("cursor","auto");
				}
			};
	setTimeout( function() { orf.fin(); } , 150);
	});

	$("#lmonitor").hide();

	//$(".myimporter").delegate("input:eq(0)","change",function(e){
	$("#fultra").change(function(e){
	    var rcvd = $j(this).val();
	    if(rcvd && rcvd.length > 0){
			$('.file_dock').css("visibility","visible").effect("pulsate",{},1000);
		    var pdata={};
		    $("#importbox > :input:not([type='button'])").each(function(){
		    	console.log($(this));
			   pdata[$(this).attr("name")] = $(this).val();
		    });
		    if (typeof document.forms['big-front'] !== 'undefined')
		    	pdata['project_id'] = document.forms['big-front'].project_id.value;
			document.forms['upq-back'].fdata.value = JSON.stringify(pdata);
		    //document.forms['upq-back'].submit();
		    $("#push_file").trigger("click");
	    }else{
			$('.file_dock').css("visibility","hidden");
	    }
	});
	
	$('#land > #locations input.parent').live("click",function(){
		console.log("Test "+$('#land > #locations input.parentcheck').is(":checked"));
	});
	$('#land > #locations input.child').live("click",function(){
		console.log("Test "+$('#land > #locations input.child').is(":checked"));
	});
});

function postDataPA(){
	var bf=0,$form = $("#fPA");
	$(".mandat").each(function(){
		if($(this).val() == '' || $(this).val() == '-1' || $(this).val() == null){
			if($(this).hasClass("dfield") || $(this).is(":visible") ){
				$(this).addClass("alert");
			}else{
				$(this).closest("td").addClass("alert");
			}
			++bf;
		}else{
			if($(this).hasClass("dfield") || $(this).is(":hidden")){
				$(this).removeClass("alert");
			}else{
				$(this).closest("td").removeClass("alert");
			}
		}
	});
	if(bf > 0){
		alert("Please fill all manadatory fields!");
		return false;
	}else{
		var acts=[];
		$("#act_box > li").each(function(i){
			console.log(this);
			acts[i]=form2object(this);
			
		});
		$("#act_data").val(JSON.stringify(acts));
		$form.submit();
	}
}

function addAct(){
	var $nbox = $("#act_box").find("li:first").clone();
	$nbox
		.find(".unq").val("").end()
		.find(".alert").removeClass("alert").end()
		.find(".dfield").removeClass("hasDatepicker").attr("id","").end()
		.find(".snote").text("").end()
		.find(".multiple")
			.each(function(){
				$(this).multiselect("destroy").closest("td").find("button,div").remove().end().end()
				.multiselect()
			}).end()
		.appendTo("#act_box");

}

function killAct(but){
	var $parent = $(but).closest("li");
	if($parent.siblings().length == 0 ){
		return false;
	}else{
		$parent.remove();
	}
}

function acceptPrFile(m){
	if(m && m.length > 0){
		var mdata = $.parseJSON(m);
		$("#importbox").empty()
				.append('<span> File <b>' + mdata.file_name + '</b> saved!</span>')
				.append('<div class="file_dock file_saved_ok"></div>');
		document.forms['fPA'].project_file.value = mdata.id;
	}
}

function startCallback(){}

function moveform(){
	var $t = $("#importbox").detach();
	$t.appendTo(document.body).find("form").submit();
}

