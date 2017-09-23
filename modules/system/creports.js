/**
 * Created with JetBrains PhpStorm.
 * User: stig
 * Date: 21.04.12
 * Time: 20:47
 */
function asyncUp(obj) {
	var go = true,msg=[];

	if($j("#fup_report").val() == ''){
		go = false;
		msg.push("Please select report file for upload");
	}
	$j(".mandat").each(function(){
		if( $j(this).val() == 0 ){
			go = false;
			msg.push("Please select "+$j(this).parent().attr("data-cont") );
		}
	});
	if(go === false){
		alert(msg.join("\n"));
	}else{
		$j("#hlaunch").trigger("click");
	}
	return go;
}

function bulkspace(){}

function uploadFinished(msg) {
	if(msg && msg != 'fail' && parseInt(msg) > 0){
		viewReportFrame(msg);
	}
}

function viewReportFrame(id){
	$j.get("?m=system&a=creports&mode=vreport&suppressHeaders=1",{rid : id},function(vr){
		if(vr && vr.length){
			var nw = window.open('','uprep','');
			nw.document.body.innerHTML = vr;
			window.location.reload();
			//$j("#preview").find(".vbox").html(vr).hide().end().show();
		}
	});
}

function atuFile(obj) {
	$j("#fup_report").trigger("click");
	return false;
}

function bstrap() {
	$j("#fup_report").live("change", function () {
		var fname = this.value, rfname;
		if (fname) {
			rfname = fname.split("/").shift();
			$j(".nf_name").text(rfname);
		}
	});

	$j(".swblock").live("click",function(){
		var pvb = $j(this).attr("data-vbs");
		if(!pvb || pvb == 'off'){
			$j(this).attr("data-vbs", 'on').parent().find(".vbox").show();
		}else{
			$j(this).attr("data-vbs", 'off').parent().find(".vbox").hide();
		}
	});

	$j(".visw").live("click",function(){
		var $cbox = $j(this);
		if($cbox.is(":disabled") ){
			return false;
		}
		var curStatus = $cbox.is(":checked") == true ? 1 : 0;
		var $parCell = $cbox.parent();
		var repid=$cbox.attr("data-id");
		var $loadMode = $j("#chg_view").appendTo($parCell).show();
		$j(this).attr("disabled",true);
		$j.get("/?m=system&a=creports&suppressHeaders=1&mode=vision",{rep_id: repid, rep_status : curStatus},function(msg){
			if(msg && msg != 'fail'){
				$loadMode.hide();
				info("Report visbility changed",1);
			}else{
				info("Failed to change report visibility",0);
			}
			$cbox.attr("disabled", false);
		});
	});
	prePage("site");
}

