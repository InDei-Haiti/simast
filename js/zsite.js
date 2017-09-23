
$j.extend({
    distinct : function(anArray) {
       var result = [];
       $j.each(anArray, function(i,v){
           if ($j.inArray(v, result) == -1) result.push(v);
       });
       return result;
    }
});

$j.fn.tableStripe = function() {
	$j(this)
		.delegate("td","mouseenter",function(){
			$j(this).parent().find("td").addClass("row_hi");
		})
		.delegate("tr","mouseleave",function(){
			$j(this).parent().find("td").removeClass("row_hi");
		});
	return this;
};

function formFileExt(e){
	var bext=$j(this).attr("data-ext"),
		rcvd = $j(this).val().split(".").pop();
	bext = bext.split("|");
	if( $j.inArray(rcvd,bext) >= 0){
		$j(this).next().attr("disabled",false);
	}else{
		$j(this).val("");
		alert("File for import must have extension : "+bext.join(" OR ") );
	}
}

function info(msg,context){
	var iclass;
	switch (context){
		case 0:
			iclass = 'rnote_err';
		break;
		case 1:
			iclass = 'rnote_ok';
		break;
		default:
			iclass = 'rnote_msg';
		break;
	}
	//<div id="msg_note_box"><div class="note_msg ci_sprite"></div><span></span></div>
	if($j("#msg_note_box").length == 0){
		$j("<div/>",{id: "msg_note_box"})
			.append('<div class="note_msg ci_sprite"></div><span></span>')
			.appendTo(document.body);
	}

	$j("#msg_note_box").find(".note_msg").attr("class","note_msg ci_sprite "+iclass)
		.next().text(msg).end().end()
		.show().animate({"top": "5px"}, 400, function(){
					$j(this).stop(true).delay(1000).animate({
						top: "-105px"
					}, 800);
				});
}

function addslashes( str ) {	// Quote string with slashes
	//
	// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   improved by: Ates Goral (http://magnetiq.com)
	// +   improved by: marrtins
	return (str + '').replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
}
