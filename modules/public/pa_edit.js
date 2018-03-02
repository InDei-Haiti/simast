/**
 * Created by JetBrains PhpStorm.
 * User: stig
 * Date: 27.08.11
 * Time: 19:32
 * To change this template use File | Settings | File Templates.
 */

function closeTree(part,lpart){
	$j('#locations_skin').hide();
	var ins=[],$tgt;
	$j('#land > #locations input:checked.last').each(function(){
		ins.push($j(this).attr('id'));
	});
	if(lpart >= 0){
		$tgt = $j("#act_box > li:eq("+lpart+")").find(".tree_edit[data-part='"+part+"']");
	}else{
		$tgt = $j(".tree_edit[data-part='"+part+"']");
	}
	var nstxt=$tgt.closest("tr").find("td:first").text();
	$tgt.parent().find(".snote").text(function(i, c){
		if(ins.length > 0)
			return nstxt +' selected';
		else
			return '';
	}).end().end()
		.next().val( ins.join(",") );
	//$j('#lox_box input:checked').attr('checked', false);
}

function updComps (onList){
	var puids =[];
	onList = (onList ? onList : []);
	$j("#pdonor").multiselect("getChecked").each(function(){
		puids.push($j(this).val());
	});
	if(puids.length > 0){
		jQuery.post("/?m=projects&a=addedit&suppressHeaders=1&mode=getcomps",{progs: puids.join(",")},function(msg){
			if(msg && msg.length > 0 && msg != 'fail'){
				var ncomps = JSON.parse(msg),
					$clist = $j("#pcomp_list");
				$clist.multiselect("getChecked").each(function(){
					onList.push($j(this).val());
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
		$j("#pcomp_list").empty().multiselect("refresh");
	}
}

function annualBudget(bob){
	$j("#bub").append("<ol id='yblist'></ol>").dialog({
		modal: true,
		width: "410px",
		title: "Edit annual budget for " + ab_word,
		buttons: {
			Add: function(){
				var lasty =$j("#yblist").find("li:last select").val();
				$j("<li/>")
					.append($j("#year_shelter > select").clone(true).val((lasty > 0 ? ++lasty : '')))
					.append("&nbsp;&nbsp;&nbsp;<input type='text' class='text' value='' >")
					.append("&nbsp;&nbsp;&nbsp;<button class='text del_ab'>X</button>")
				.appendTo("#yblist");
				$j("#yblist").find("li:last input.text").focus();

			},
			Save: function(){
				var res=[];
				$j("#yblist").find("li").each(function(){
					res.push( [$j("select",this).val(),"|",$j(".text:eq(1)",this).val()].join(""));
				});
				$j(bob).next().val(res.join(";"));
				$j("#bub").dialog("destroy").empty();
			},
			Close: function(){
				$j("#bub").dialog("destroy").empty();
			}
		}
	});
	var ian = $j(bob).next().val(),$toaddz;
	if(ian  && ian.length > 0){
		var ilist = ian.split(";");
		for(var i=0,l=ilist.length; i < l; i++){
			var vl = ilist[i].split("|");
			$j("<li/>")
				.append($j("#year_shelter > select").clone(true).val(vl[0]))
				.append("&nbsp;&nbsp;&nbsp;<input type='text' class='text' value='"+ vl[1] +"' >")
				.appendTo("#yblist")
		}
	}
}



$j(document).ready(function(){
    $j("<div id='lmonitor'>").text("Loading...").appendTo(document.body);
	$j(".dfield").live("click",function(){
		$j(this).datepicker({
			dateFormat: 'dd/mm/yy',
			showOn: 'focus',
			changeMonth: true,
			changeYear: true
		}).focus();

	});

	$j(".del_ab").live("click",function(){
		$j(this).closest("li").remove();
	});

	$j(".multiple").multiselect();

	$j("#lox_shut").live("click",function(){
		closeTree($j("#locations_skin").attr("data-view"), $j("#locations_skin").attr("data-lpart"));
	});

	$j(".tree_edit").live("click",function(){
		$j(document.body).css("cursor","wait");
		$j("#lmonitor").show();
		// console.log($j("#lmonitor").html()	);
		var self=this,
			liact = ( $j(this).closest("#act_box").length == 1 ? $j(this).closest("li").index() : -1),
			orf = {
				fin : function(){
					var $t = $j(self), offset = $t.offset(),
					height = $t.outerHeight(true),
					part = $t.attr("data-part");
					// Checking if we trying to close or open it
					if ( $j('#locations_skin').is(':visible') ) {
						//closing
						var vpart =$j("#lox_box").attr("data-view");
						if(vpart == part){
							return false;
						}else{
							closeTree(vpart,liact);
						}
					}  //opening
					var cur_location = $t.next().val(),
					$lbx = $j("#land").html($j("#"+part+"_box").html()).find("ul:first").attr("id","locations").end().detach();
					//$j('#land  > #locations')
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
						$j('#locations',$lbx).checkboxTree('check', $j( '#'+cur_location.replace(/,/g, ',#'), $lbx));
					}
					$j('#locations_skin').css({
						top: offset.top + height,
						left: offset.left
					}).append($lbx).show().attr({
						"data-view": part,
						"data-lpart":liact
					});
					$j("#lmonitor").hide();
					$j(document.body).css("cursor","auto");
				}
			};
	setTimeout( function() { orf.fin(); } , 150);
	});

	$j("#lmonitor").hide();

	//$j(".myimporter").delegate("input:eq(0)","change",function(e){
	$j("#fultra").change(function(e){
	    var rcvd = $j(this).val();
	    if(rcvd && rcvd.length > 0){
			$j('.file_dock').css("visibility","visible").effect("pulsate",{},1000);
		    var pdata={};
		    $j("#importbox > :input:not([type='button'])").each(function(){
		    	console.log($j(this));
			   pdata[$j(this).attr("name")] = $j(this).val();
		    });
		    if (typeof document.forms['big-front'] !== 'undefined')
		    	pdata['project_id'] = document.forms['big-front'].project_id.value;
			document.forms['upq-back'].fdata.value = JSON.stringify(pdata);
		    //document.forms['upq-back'].submit();
		    $j("#push_file").trigger("click");
	    }else{
			$j('.file_dock').css("visibility","hidden");
	    }
	});
	
	$j('#land > #locations input.parent').live("click",function(){
		console.log("Test "+$j('#land > #locations input.parentcheck').is(":checked"));
	});
	$j('#land > #locations input.child').live("click",function(){
		console.log("Test "+$j('#land > #locations input.child').is(":checked"));
	});
});

function postDataPA(){
	var bf=0,$form = $j("#fPA");
	$j(".mandat").each(function(){
		if($j(this).val() == '' || $j(this).val() == '-1' || $j(this).val() == null){
			if($j(this).hasClass("dfield") || $j(this).is(":visible") ){
				$j(this).addClass("alert");
			}else{
				$j(this).closest("td").addClass("alert");
			}
			++bf;
		}else{
			if($j(this).hasClass("dfield") || $j(this).is(":hidden")){
				$j(this).removeClass("alert");
			}else{
				$j(this).closest("td").removeClass("alert");
			}
		}
	});
	if(bf > 0){
		alert("Please fill all manadatory fields!");
		return false;
	}else{
		var acts=[];
		$j("#act_box > li").each(function(i){
			console.log(this);
			acts[i]=form2object(this);
			
		});
		$j("#act_data").val(JSON.stringify(acts));
		$form.submit();
	}
}

function addAct(){
	var $nbox = $j("#act_box").find("li:first").clone();
	$nbox
		.find(".unq").val("").end()
		.find(".alert").removeClass("alert").end()
		.find(".dfield").removeClass("hasDatepicker").attr("id","").end()
		.find(".snote").text("").end()
		.find(".multiple")
			.each(function(){
				$j(this).multiselect("destroy").closest("td").find("button,div").remove().end().end()
				.multiselect()
			}).end()
		.appendTo("#act_box");

}

function killAct(but){
	var $parent = $j(but).closest("li");
	if($parent.siblings().length == 0 ){
		return false;
	}else{
		$parent.remove();
	}
}

function acceptPrFile(m){
	if(m && m.length > 0){
		var mdata = $.parseJSON(m);
		$j("#importbox").empty()
				.append('<span> File <b>' + mdata.file_name + '</b> saved!</span>')
				.append('<div class="file_dock file_saved_ok"></div>');
		document.forms['fPA'].project_file.value = mdata.id;
	}
}

function startCallback(){}

function moveform(){
	var $t = $j("#importbox").detach();
	$t.appendTo(document.body).find("form").submit();
}

