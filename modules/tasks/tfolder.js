var chrd = document.createElement('div');
chrd.style.display="none";
chrd.id="bdiv";
chrd.innerHTML="";
if(!project_id){
	var project_id=0;
}
if(!task_id){
	var task_id=0;
}

function make_pre(way){
	var timg= document.createElement('img');
	timg.src='./images/icons/fplus.gif';
	timg.width="12";
	timg.height="12";
	timg.style.border="0";
	var chld = new Array();
	/*$j("tr.[class^='rowc_']").each( function(){
			var rt = $j(this).attr("class");
			rt=rt.replace("rowc_","");
			rt=parseInt(rt);
			chld[rt]=1;
		})
		.hide();*/
	$("span.[id^='fon_']").
	each(function(){
		var st= this.id;
		var rkids = $(this).attr('kids');
		st=st.replace("fon_","");
		/*if(chld[st] > 0){*/
		if(rkids > 0){
			$j(this).append( $(timg).clone(true))
				.click(function(){
					make_click(this);
				});
		}
	} );
	/*if(way == "tpov"){
		$j("tr.[class*='pbox_']").hide();
	}*/
}

function make_click(rpar){
	var pidname=rpar.id;
	var rkids = $j("#"+pidname).attr('kids');
	var rload = $j("#"+pidname).attr('load');
	var pid=pidname.replace("fon_","");
	var ml = 0;
	var tpd="";
	var ttd="";
	pid=parseInt(pid);
	if(rload == "not" && rkids > 0){
		if(project_id  > 0){	
			tpd="&project_id="+project_id;
		}
		if(task_id  > 0){	
			ttd="&task_id="+task_id;
		}
		$j("#fon_" + pid + " > img").attr("src","./images/tasks-load.gif").attr("height","16").attr("width","16");
		$j.ajax({
			type: "GET",
   			url: "?m=tasks",
   			data: "a=tasks&suppressHeaders=1&mode=childs&parid="+pid+tpd+ttd,
   			success: function(msg){
   				ml = msg.length;
   				if(ml > 1){
   					$j("#"+pidname).attr("load","done");
	   				$j(".rowp_"+pid).after(" "+msg+" ");
	   				$j("#fon_" + pid + " > img").attr("height","12").attr("width","12").attr("src","./images/icons/fminus.gif");
     			}     			
    		}
		});
		
	}
	$j("#fon_" + pid + " > img").attr("src", function(){
		var isrc=$j(this).attr("src");
		if(isrc == './images/icons/fplus.gif'){
			$j("tr.rowc_"+pid).show();
			return "./images/icons/fminus.gif";
		}else if(isrc.match("minus")){
			$j("tr.rowc_"+pid).hide();
			return "./images/icons/fplus.gif";
		}
	} );
	/*$j("tr.rowc_"+pid).each(function(){
		$j(this).toggle();
	});*/
}


function make_box(pcl){
	var imid=pcl.replace("pbox_","");
	$j("#fimg_"+imid).attr("src",function (){
		var isrc=$j(this).attr("src");
		if(isrc == './images/icons/expand.gif'){
			return "./images/icons/collapse.gif";
		}else{
			return "./images/icons/expand.gif";
		}
	} );
	$j("."+pcl).each( function(){
		var tcl= $j(this).attr("class");
		if(tcl.match("rowc_")){
			$j(this).hide();
		}else{
			$j(this).toggle();
		}
		if(tcl.match("rowp_")){
			$j(this).find("span").find("img").attr("src",'./images/icons/fplus.gif');
		}

});
}
