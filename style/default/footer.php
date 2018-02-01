<?php
echo $AppUI->getMsg ();
?>

<!--</td>
</tr>
</table>-->

   <!-- <div class="brv" style="margin-bottom:5px;position: absolute;">
        <!--<div class="brw">
            <h6 class="bry">Dashboards</h6>
            <h2 class="brx">Overview</h2>
        </div>-


        </div>
    </div>-->
    </div>

    <!--<nav class="navbar navbar-default navbar-bottom" role="navigation" id="logobottom" style="margin-top:50px;">
        <div class="container">
            <div class="row" align="center" style="background: red">
                <div class="col-md-3" style="background: transparent">
                    <div class="imgwrapper">
                       <img src="/style/images/logopartner/korilavi.JPG" class="img-responsive">
                    </div>
                </div>
                <div class="col-md-3" style="background: transparent">
                    <div class="imgwrapper">
                       <img src="/style/images/logopartner/mast_and_usaid_logo.JPG" class="img-responsive">
                    </div>
                </div>
                <div class="col-md-3" style="background: transparent">
                    <div class="imgwrapper">
                       <img src="/style/images/logopartner/partner_logos.JPG" class="img-responsive">
                    </div>
                </div>
                <div class="col-md-3" style="background: transparent">
                    <div class="imgwrapper">
                       <img src="/style/images/logopartner/world_bank.JPG" class="img-responsive">
                    </div>
                </div>
            </div>
        </div>
    </nav>-->

<?php

@$AppUI->loadJS ();
if ($m === 'files') {
	echo '<script type="text/javascript" src="/modules/projects/projects.module.js"></script>';
}
if ($m === 'outputs') {
    echo '<script type="text/javascript" src="/modules/outputs/maps/leaf-demo.js"></script>
         <script type="text/javascript" src="/modules/outputs/maps/leaflet-image.js"></script>';
}
if ($m === 'outputs' || $m === 'projects' || $m === 'tasks') {
    echo '<script type="text/javascript" src="/modules/public/pa_edit.js"></script>';
}
if($m === 'system'){
    echo '<script type="text/javascript">(document).getElementById("logobottom").style.display="none"</script>';
}
?>

<!-- <style type="text/css">
    /* chosen */

    /* @group Base */
    .chzn-container {
        position: relative;
        display: inline-block;
        zoom: 1;
        *display: inline;
    }

    .chzn-container .chzn-drop {
        background: #fff;
        border: 1px solid #aaa;
        border-top: 0;
        position: absolute;
        top: 29px;
        left: 0;
        -webkit-box-shadow: 0 4px 5px rgba(0, 0, 0, 0.15);
        -moz-box-shadow: 0 4px 5px rgba(0, 0, 0, 0.15);
        box-shadow: 0 4px 5px rgba(0, 0, 0, 0.15);
        z-index: 1010;
    }

    /* @end */
    /* @group Single Chosen */
    .chzn-container-single .chzn-single {
        background-color: #efefef;
        background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), color-stop(50%, #eeeeee), to(#f4f4f4));
        background-image: -webkit-linear-gradient(#ffffff, #eeeeee 50%, #f4f4f4);
        background-image: -moz-linear-gradient(top, #ffffff, #eeeeee 50%, #f4f4f4);
        background-image: -o-linear-gradient(#ffffff, #eeeeee 50%, #f4f4f4);
        background-image: linear-gradient(#ffffff, #eeeeee 50%, #f4f4f4);
        background-repeat: no-repeat;
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffffff', endColorstr='#fff4f4f4', GradientType=0);
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding-box;
        background-clip: padding-box;
        border: 1px solid #aaaaaa;
        -webkit-box-shadow: 0 0 3px #ffffff inset, 0 1px 1px rgba(0, 0, 0, 0.1);
        -moz-box-shadow: 0 0 3px #ffffff inset, 0 1px 1px rgba(0, 0, 0, 0.1);
        box-shadow: 0 0 3px #ffffff inset, 0 1px 1px rgba(0, 0, 0, 0.1);
        display: block;
        overflow: hidden;
        white-space: nowrap;
        position: relative;
        height: 23px;
        line-height: 24px;
        padding: 0 0 0 8px;
        color: #444444;
        text-decoration: none;
    }

    .chzn-container-single .chzn-default {
        color: #999;
    }

    .chzn-container-single .chzn-single span {
        margin-right: 26px;
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .chzn-container-single .chzn-single abbr {
        position: absolute;
        top: 0;
        right: 26px;
    }

    .chzn-container-single .chzn-single abbr:before {
        content: '\00D7';
        float: right;
        font-size: 20px;
        font-weight: bold;
        line-height: 20px;
        color: #000000;
        text-shadow: 0 1px 0 #ffffff;
        opacity: 0.2;
        filter: alpha(opacity=20);
    }

    .chzn-container-single .chzn-single abbr:before:hover {
        color: #000000;
        text-decoration: none;
        cursor: pointer;
        opacity: 0.4;
        filter: alpha(opacity=40);
    }

    .chzn-container-single .chzn-single abbr:hover,
    .chzn-container-single.chzn-disabled .chzn-single abbr:hover {
        text-decoration: none;
        cursor: pointer;
    }

    .chzn-container-single .chzn-single abbr:hover:before,
    .chzn-container-single.chzn-disabled .chzn-single abbr:hover:before {
        color: #000000;
        opacity: 0.4;
        filter: alpha(opacity=40);
    }

    .chzn-container-single .chzn-single div {
        position: absolute;
        right: 0;
        top: 0;
        display: block;
        height: 100%;
        width: 18px;
    }

    .chzn-container-single .chzn-single div b {
        display: block;
        width: 100%;
        height: 100%;
    }

    .chzn-container-single .chzn-single div b:after {
        content: '\f078';
        font-family: FontAwesome;
    }

    .chzn-container-single .chzn-search {
        padding: 3px 4px;
        position: relative;
        margin: 0;
        white-space: nowrap;
        z-index: 1010;
    }

    .chzn-container-single .chzn-search:after {
        content: '\f002';
        font-family: FontAwesome;
        position: relative;
        right: 16px;
    }

    .chzn-container-single .chzn-search input {
        background-color: #f5f5f5;
        background-image: -moz-linear-gradient(top, #eeeeee, #ffffff);
        background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#eeeeee), to(#ffffff));
        background-image: -webkit-linear-gradient(top, #eeeeee, #ffffff);
        background-image: -o-linear-gradient(top, #eeeeee, #ffffff);
        background-image: linear-gradient(to bottom, #eeeeee, #ffffff);
        background-repeat: repeat-x;
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffeeeeee', endColorstr='#ffffffff', GradientType=0);
        margin: 1px 0;
        padding: 4px 20px 4px 5px;
        outline: 0;
        border: 1px solid #aaa;
        font-family: sans-serif;
        font-size: 1em;
    }

    .chzn-container-single .chzn-drop {
        -webkit-border-radius: 0 0 4px 4px;
        -moz-border-radius: 0 0 4px 4px;
        border-radius: 0 0 4px 4px;
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding-box;
        background-clip: padding-box;
    }

    /* @end */
    .chzn-container-single-nosearch .chzn-search input {
        position: absolute;
        left: -9000px;
    }

    /* @group Multi Chosen */
    .chzn-container-multi .chzn-choices {
        background-color: #ffffff;
        border: 1px solid #cccccc;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        -webkit-transition: border linear 0.2s, box-shadow linear 0.2s;
        -moz-transition: border linear 0.2s, box-shadow linear 0.2s;
        -o-transition: border linear 0.2s, box-shadow linear 0.2s;
        transition: border linear 0.2s, box-shadow linear 0.2s;
        margin: 0;
        padding: 0;
        cursor: text;
        overflow: hidden;
        height: auto !important;
        height: 1%;
        position: relative;
    }

    .chzn-container-multi .chzn-choices li {
        float: left;
        list-style: none;
    }

    .chzn-container-multi .chzn-choices .search-field {
        white-space: nowrap;
        margin: 0;
        padding: 0;
    }

    .chzn-container-multi .chzn-choices .search-field input {
        color: #666;
        background: transparent !important;
        border: 0 !important;
        font-family: sans-serif;
        font-size: 100%;
        height: 15px;
        padding: 5px;
        margin: 1px 0;
        outline: 0;
        -webkit-box-shadow: none;
        -moz-box-shadow: none;
        box-shadow: none;
    }

    .chzn-container-multi .chzn-choices .search-field .default {
        color: #999;
    }

    .chzn-container-multi .chzn-choices .search-choice {
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding-box;
        background-clip: padding-box;
        background-color: #f0f0f0;
        background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#f4f4f4), color-stop(50%, #f0f0f0), to(#eeeeee));
        background-image: -webkit-linear-gradient(#f4f4f4, #f0f0f0 50%, #eeeeee);
        background-image: -moz-linear-gradient(top, #f4f4f4, #f0f0f0 50%, #eeeeee);
        background-image: -o-linear-gradient(#f4f4f4, #f0f0f0 50%, #eeeeee);
        background-image: linear-gradient(#f4f4f4, #f0f0f0 50%, #eeeeee);
        background-repeat: no-repeat;
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#fff4f4f4', endColorstr='#ffeeeeee', GradientType=0);
        -webkit-box-shadow: 0 0 2px #ffffff inset, 0 1px 0 rgba(0, 0, 0, 0.05);
        -moz-box-shadow: 0 0 2px #ffffff inset, 0 1px 0 rgba(0, 0, 0, 0.05);
        box-shadow: 0 0 2px #ffffff inset, 0 1px 0 rgba(0, 0, 0, 0.05);
        color: #333;
        border: 1px solid #aaaaaa;
        line-height: 13px;
        padding: 3px 20px 3px 5px;
        margin: 3px 0 3px 5px;
        position: relative;
        cursor: default;
    }

    .chzn-container-multi .chzn-choices .search-choice-focus {
        background: #d4d4d4;
    }

    .chzn-container-multi .chzn-choices .search-choice .search-choice-close {
        position: absolute;
        right: 3px;
        top: 0;
    }

    .chzn-container-multi .chzn-choices .search-choice .search-choice-close:before {
        content: '\00D7';
        font-size: 20px;
        font-weight: bold;
        line-height: 14px;
        color: #000000;
        text-shadow: 0 1px 0 #ffffff;
        opacity: 0.2;
        filter: alpha(opacity=20);
    }

    .chzn-container-multi .chzn-choices .search-choice .search-choice-close:hover,
    .chzn-container-multi .chzn-choices .search-choice-focus .search-choice-close {
        text-decoration: none;
        cursor: pointer;
    }

    .chzn-container-multi .chzn-choices .search-choice .search-choice-close:hover:before,
    .chzn-container-multi .chzn-choices .search-choice-focus .search-choice-close:before {
        color: #000000;
        opacity: 0.4;
        filter: alpha(opacity=40);
    }

    /* @end */
    /* @group Results */
    .chzn-container .chzn-results {
        margin: 0 4px 4px 0;
        max-height: 240px;
        padding: 0 0 0 4px;
        position: relative;
        overflow-x: hidden;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }

    .chzn-container-multi .chzn-results {
        margin: -1px 0 0;
        padding: 0;
    }

    .chzn-container .chzn-results li {
        display: none;
        line-height: 15px;
        padding: 5px 6px;
        margin: 0;
        list-style: none;
    }

    .chzn-container .chzn-results .active-result {
        cursor: pointer;
        display: list-item;
    }

    .chzn-container .chzn-results .highlighted {
        background-color: #1290c7;
        background-image: -moz-linear-gradient(top, #149ed9, #107cab);
        background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#149ed9), to(#107cab));
        background-image: -webkit-linear-gradient(top, #149ed9, #107cab);
        background-image: -o-linear-gradient(top, #149ed9, #107cab);
        background-image: linear-gradient(to bottom, #149ed9, #107cab);
        background-repeat: repeat-x;
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff149ed9', endColorstr='#ff107cab', GradientType=0);
        color: #fff;
    }

    .chzn-container .chzn-results li em {
        background: #feffde;
        font-style: normal;
    }

    .chzn-container .chzn-results .highlighted em {
        background: transparent;
    }

    .chzn-container .chzn-results .no-results {
        background: #f4f4f4;
        display: list-item;
    }

    .chzn-container .chzn-results .group-result {
        cursor: default;
        color: #999;
        font-weight: bold;
    }

    .chzn-container .chzn-results .group-option {
        padding-left: 15px;
    }

    .chzn-container-multi .chzn-drop .result-selected {
        display: none;
    }

    /* @end */
    /* @group Active  */
    .chzn-container-active .chzn-single {
        -webkit-box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
        -moz-box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
        border: 1px solid #5897fb;
    }

    .chzn-container-active .chzn-single-with-drop {
        border: 1px solid #aaa;
        -webkit-box-shadow: 0 1px 0 #ffffff inset;
        -moz-box-shadow: 0 1px 0 #ffffff inset;
        box-shadow: 0 1px 0 #ffffff inset;
        background-color: #f5f5f5;
        background-image: -moz-linear-gradient(top, #eeeeee, #ffffff);
        background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#eeeeee), to(#ffffff));
        background-image: -webkit-linear-gradient(top, #eeeeee, #ffffff);
        background-image: -o-linear-gradient(top, #eeeeee, #ffffff);
        background-image: linear-gradient(to bottom, #eeeeee, #ffffff);
        background-repeat: repeat-x;
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffeeeeee', endColorstr='#ffffffff', GradientType=0);
        -webkit-border-bottom-left-radius: 0;
        -webkit-border-bottom-right-radius: 0;
        -moz-border-radius-bottomleft: 0;
        -moz-border-radius-bottomright: 0;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
    }

    .chzn-container-active .chzn-single-with-drop div {
        background: transparent;
        border-left: none;
    }

    .chzn-container-active .chzn-single-with-drop div b:after {
        content: '\f077';
    }

    .chzn-container-active .chzn-choices {
        -webkit-border-bottom-left-radius: 0;
        -webkit-border-bottom-right-radius: 0;
        -moz-border-radius-bottomleft: 0;
        -moz-border-radius-bottomright: 0;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
        border-color: rgba(82, 168, 236, 0.8);
        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 8px rgba(82, 168, 236, .6);
        -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 8px rgba(82, 168, 236, .6);
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 8px rgba(82, 168, 236, .6);
    }

    .chzn-container-active .chzn-choices .search-field input {
        color: #111 !important;
    }

    /* @end */
    /* @group Disabled Support */
    .chzn-disabled {
        cursor: default;
        opacity: 0.5 !important;
    }

    .chzn-disabled .chzn-single {
        cursor: default;
    }

    .chzn-disabled .chzn-choices .search-choice .search-choice-close {
        cursor: default;
    }

    /* @end */
    /* @group Right to Left */
    .chzn-rtl {
        text-align: right;
    }

    .chzn-rtl .chzn-single {
        padding: 0 8px 0 0;
        overflow: visible;
    }

    .chzn-rtl .chzn-single span {
        margin-left: 26px;
        margin-right: 0;
        direction: rtl;
    }

    .chzn-rtl .chzn-single div {
        left: 3px;
        right: auto;
    }

    .chzn-rtl .chzn-single abbr {
        left: 26px;
        right: auto;
    }

    .chzn-rtl .chzn-choices .search-field input {
        direction: rtl;
    }

    .chzn-rtl .chzn-choices li {
        float: right;
    }

    .chzn-rtl .chzn-choices .search-choice {
        padding: 3px 5px 3px 19px;
        margin: 3px 5px 3px 0;
    }

    .chzn-rtl .chzn-choices .search-choice .search-choice-close {
        left: 4px;
        right: auto;
    }

    .chzn-rtl.chzn-container-single .chzn-results {
        margin: 0 0 4px 4px;
        padding: 0 4px 0 0;
    }

    .chzn-rtl .chzn-results .group-option {
        padding-left: 0;
        padding-right: 15px;
    }

    .chzn-rtl.chzn-container-active .chzn-single-with-drop div {
        border-right: none;
    }

    .chzn-rtl.chzn-container-single .chzn-search:after {
        right: auto;
        left: 16px;
    }

    .chzn-rtl .chzn-search input {
        background-color: #f5f5f5;
        background-image: -moz-linear-gradient(top, #eeeeee, #ffffff);
        background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#eeeeee), to(#ffffff));
        background-image: -webkit-linear-gradient(top, #eeeeee, #ffffff);
        background-image: -o-linear-gradient(top, #eeeeee, #ffffff);
        background-image: linear-gradient(to bottom, #eeeeee, #ffffff);
        background-repeat: repeat-x;
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffeeeeee', endColorstr='#ffffffff', GradientType=0);
        padding: 4px 5px 4px 20px;
        direction: rtl;
    }
    </style> -->

<script type="text/javascript">
       //function select(obj,title) {
            //$(obj).chosen(title);
        //}
        $(function () {
            //$(".chosen").chosen();
        });

        function updateSelect(obj){
        	$(obj).trigger("liszt:updated");
        	$(obj).trigger("chosen:updated");
        }
    </script>
<?php if($m=='clients' && $a=='addedit'){?>
<script type="text/javascript">
var v = null;
	<?php if(@$obj->client_administration_section){?>
			v = "<?php echo @$obj->client_administration_section ?>";
	<?php }?>
			function loadSection(commune_code){
				$j.get("?m=clients&ccode="+commune_code+"&mode=loadsection&suppressHeaders=1", function (msg) {
					if (msg && msg !== 'fail') {
						msg = $j.parseJSON(msg);
						$j("#administration_sect").empty();
						$j("#administration_sect").append("<option></option>");

						$.each(msg, function(key,value){

							var select = '';
							if(v!=null && v==key)
								select = 'selected=selected';
							$j("#administration_sect").append("<option value='"+key+"' "+select+">"+value+"</option>");
						});
						updateSelect("#administration_sect");
					} else {
						$j("#msgbox").text("Invalid form selected!").show().delay(2000).fadeOut(3000);
					}
				});
			}
			<?php if($client_administration_com){?>
					loadSection("<?php echo $client_administration_com?>");
			<?php }?>
/* function loadCommun(section_code){
	$j.get("?m=clients&ccode="+commune_code+"&mode=loadsection&suppressHeaders=1", function (msg) {
		//console.log(msg);
		if (msg && msg !== 'fail') {
			msg = $j.parseJSON(msg);
			console.log(msg);
			/* $j("#administration_sect").empty();
			$j("#administration_sect").append("<option></option>");
			$.each(msg, function(key,value){
				$j("#administration_sect").append("<option value='"+key+"'>"+value+"</option>");
			});
			updateSelect("#administration_sect"); ///
		} else {
			$j("#msgbox").text("Invalid form selected!").show().delay(2000).fadeOut(3000);
		}
	});
}*/
	 $('#administration_com').change(function() {
		console.log("ok");
		loadSection($('#administration_com').val());
	});
/*var section = null; */

	//$obj->client_administration_section ? @$obj->client_administration_section : '-1'
	//if()


	/*function ch(){
		var vj = jsonsec[$('#administration_com').val()];
	  	$('#administration_sect').find('option').remove().end();
	  	$('#administration_sect').append(
    			'<option value="" ></option>'
    		);
    	for(var key in vj) {
	    	selected = '';
	    	var val="";
	    if(@$obj->client_administration_section){?>
    			val = "echo @$obj->client_administration_section";
    		}
	    	if(val){
	    		if(val==key){
	    			selected = 'selected=selected';
		    	}
	    	}
    		$('#administration_sect').append(
    			"<option value=" + key  +" "+selected+"'>" +vj[key] + "</option>"
    		);
        }
	}*/
</script>
<!--

//-->
</script>
<?php }?>

<?php if($m=='activity' && $a=='addedit'){?>


<script type="text/javascript">
	var select = document.getElementById("administration_com");
	var val="";

	<?php if($activity_administration_com){?>
		val = "<?php echo $activity_administration_com?>";
		val = val.split(',');
	<?php }?>
	if(val){
		for(var i=0;i<select.length;i++){
			var value = select.options[i].value;
			for(var j=0;j<val.length;j++){
				if(value==val[j]){
					select.options[i].selected = true;
					break;
				}
			}
		}
	}



</script>


<script type="text/javascript">

		/*var values=" //echo $activity_administration_com";
		if(values){
			$.each(values.split(","), function(i,e){

			    $("#administration_com option[value='" + e + "']").prop("selected", true);
			    console.log(i);
			});
			$("#administration_com").trigger("liszt:updated");
		}*/
		var l = [];
		function ch() {
		    $('#administration_com').change(function() {
			    populatesection();
			    $("#administration_sect").trigger("liszt:updated");
			    //select('#administration_sect','-Select Section Communales -');
			   // console.log($("#administration_sect").val());
		    });
		}
		function populatesection(){
			var html = "";
		    if($('#administration_com').val()){
			    console.log('change');
			    //$('#administration_sect').empty();
		    	$('#administration_sect').find('option').remove().end();
		    	j = 0;
		    	<?php if(@$row->activity_administration_section){?>
	    		var val = "<?php echo @$row->activity_administration_section?>";
	    		val = val.split(',');
	    		<?php }?>
		        for(i=0;i<$('#administration_com').val().length;i++){
		        	var vj = jsonsec[$('#administration_com').val()[i]];

			    	for(var key in vj) {
				    	selected = '';


			    		if(val){
			    			for(var k=0;k<val.length;k++){
			    				if(key==val[k]){
			    					selected = 'selected=selected';
			    					break;
			    				}
			    			}
			    		}
			    		console.log(key+" selected :"+selected);
			    		$('#administration_sect').append(
			    			"<option value=" + key  +" "+selected+"'>" +vj[key] + "</option>"
			    		).trigger("liszt:updated");
			    		l[j] = key;
			    		j++;
			        }
			    }
		        console.log($('#administration_com').val());
		        //$("#administration_sect").trigger("liszt:updated");
		    }else{
		    	//$('#administration_sect').find('option').remove().end();
		    	//$('#administration_sect').append(
		    		//	'<option value="">---------------------------------</option>');
		    	$("#administration_sect").trigger("liszt:updated");
			}
		}
		ch();
		populatesection();
		var values="<?php echo $row->activity_administration_section?>";
		/*function update(list,bool){
			$.each(list, function(i,e){
			    $("#administration_sect option[value='" + e + "']").prop("selected", bool);
			});
		}*/
		function list(){
			$("#administration_sect").trigger("liszt:updated");
		}

		/*$.each(values.split(","), function(i,e){
		    $("#administration_sect option[value='" + e + "']").prop("selected", true);
		});*/
		//update(values.split(","),true);
		/*$(function () {
			$.each(values.split(","), function(i,e){
			    $("#administration_sect option[value='" + e + "']").prop("selected", true);
			})
        });
		list();*/
	</script>
<?php }?>

	<?php if($m=='outputs'){?>

<!-- <script type="text/javascript" src="http://maps.google.com/maps/api/js?key=<?php echo $GMapKey ?>"></script> -->
<!-- <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false&libraries=geometry&v=3&key=<?php echo $GMapKey ?>"></script> -->
<script type="text/javascript" src="modules/outputs/jquery-ui.js"></script>
<!-- <script type="text/javascript" src="modules/outputs/maps2.js"></script> -->
<!-- <script type="text/javascript" src="/modules/outputs/jquery9.min.js"></script> -->
<!-- <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAXBklWqmHO8dubF66h9VEBlRQnuLS9P_g&sensor=false"></script>
		 -->
<link rel="stylesheet" type="text/css"
	href="/modules/outputs/gchart.css" />
<script type="text/javascript" src="/modules/outputs/gchart.js"></script>
<script>
			//window.onload = up;
			function up() {
				charter.init();
			}
			up();
			//var alevels = @@alevels@@;
		</script>
<script type="text/javascript">
			//$j(document).ready(function(){
				/*geocoder = new google.maps.Geocoder();
				geocoder.geocode( { 'address': "Paricot, Port-a-Piment, Haiti"}, function(results, status) {
				      if (status == google.maps.GeocoderStatus.OK) {
					      console.log(results[0].geometry.location);
				        //map.setCenter(results[0].geometry.location);
				        //var marker = new google.maps.Marker({
				          //  map: map,
				           // position: results[0].geometry.location
				       // });
				      } else {
				        alert("Geocode was not successful for the following reason: " + status);
				      }
				});*/
				//xMap.initz();
				<?php if($zones){ ?>
					//xMap.loadCoordinatesZone(<?php echo $zones ?>);
				<?php }?>
				//$j("#accordion").accordion({ autoHeight: false,animated: false, collapsible: true,navigation: true });
				//watchsels();
				//$j("#rendp").center();
			//});
			//$j("#tfilter").show();
		</script>
<script type="text/javascript">
			/* $j(document).ready(function(){
				xMap.initz();
				$j("#accordion").accordion({ autoHeight: false,animated: false, collapsible: true,navigation: true });
				watchsels();
				$j("#rendp").center();
			});
			$j("#tfilter").show(); */
		</script>
<script type="text/javascript" src="modules/outputs/progressBar.js"></script>
<script type="text/javascript">
    function cmSelector(){
        if($(".wform_81_fld_0") !== undefined || $(".wform_81_fld_0") !== null){
            var depId = $(".wform_81_fld_0").val();
            $(".wform_81_fld_1 option").each(function(){
                var compDepID = $(this).val();
//                alert($(this).html));
                if(depId != compDepID.substring(0,2)){
                    $(this).attr("disabled","disabled");
                }else{
                    $(this).removeAttr("disabled");
                }
            });

        }

        setTimeout(cmSelector,500);
    }

        function unactiveBuildStats(){
        if($("#toactivate").hasClass("tabs-selected")){
            $("#go2stats").removeAttr("onclick");
            $("#go2stats").attr("disabled","disabled");
//            console.log($("#go2stats"));
        }

        setTimeout(unactiveBuildStats,500);
    }
    var $objectClick;
    function miness(){
        /*if($("#rbox").children("li").length > 0){
          $("#rbox").children("li").each(function(){
            if($(this).attr("data-type")=="numeric" || $(this).attr("data-type")=="calculateNumeric"){
              $(this).find("div.kill_area").html('<i class="fa fa-close" style="font-size:24px;color:red"></i>');
                // $("#rbox li div.kill_area").html('<i class="fa fa-close" style="font-size:24px;color:red"></i>');
                // $("li.ui-state-disabled").css("background-image","");

                if($(this).children("div.showmn").length == 0){
                    $(this).append("<div class='showmn'></div>");
                    var theDownArrow = $(this).find(".showmn");
                    if(theDownArrow.html() == ""){
                       theDownArrow.html('<i class="fa fa-chevron-down" style="margin-top: 21px;margin-left: -250px; color:white;"></i>');
                    }
                }
            }else{
              $(this).find("div.kill_area").html('<i class="fa fa-close" style="font-size:24px;color:red"></i>');
            }
          });
        }*/

        /*if($("#cbox").children("li").length > 0){
          $("#cbox").children("li").each(function(){
            if($(this).attr("data-type")=="numeric" || $(this).attr("data-type")=="calculateNumeric"){
              //$(this).find("div.kill_area").html('<i class="fa fa-close" style="font-size:24px;color:red"></i>');
                // $("#rbox li div.kill_area").html('<i class="fa fa-close" style="font-size:24px;color:red"></i>');
                // $("li.ui-state-disabled").css("background-image","");

                if($(this).children("div.showmn").length == 0){
                    //$(this).append("<div class='showmn'></div>");
                    //var theDownArrow = $(this).find(".showmn");
                    //if(theDownArrow.html() == ""){
                      // theDownArrow.html('<i class="fa fa-chevron-down" style="margin-top: 21px;margin-left: -250px; color:white;"></i>');
                    //}
                }
            }else{
              //$(this).find("div.kill_area").html('<i class="fa fa-close" style="font-size:24px;color:red"></i>');
            }
          });
        }*/

        /*$(".showmn1 i").click(function(){
//          console.log($(this).closest("li").html());
//          $("<p style='display:none;'>LeMenu</p>").insertAfter($(this).closest("li"));
            $objectClick = $(this).closest("li");
            console.log($objectClick.html());
            var varTitle = $(this).closest("li").find("div.ulit").text();
            var getModal = $("#avgCalcs");
            getModal.css("display","block");
            $("#closeAvgCalcsModal").click(function(){
                getModal.css("display","none");
            });

            $("#avgCalcs div.modal-body").html('<br /><br /><div class="row" style="padding-left: 10px;"><form class="form-inline">' +
             '<div class="form-group"><input id="avg" type="checkbox" class="form-control theCheck" /><label style="margin-left:5px;" for="avg">AVG</label><input type="text" style="width:  300px; margin-left:10px;" class="form-control" name="avg" value="AVG -'+varTitle+'" /></div> <br /><br />' +
              '<div class="form-group"><input id="sum" type="checkbox" class="form-control theCheck" /><label style="margin-left:5px;" for="sum">SUM</label><input type="text" style="width:  300px; margin-left:10px;" class="form-control" name="sum" value="SUM -'+varTitle+'" /></div><br /><br />' +
               '<div class="form-group"><input id="min" type="checkbox" class="form-control theCheck" /><label style="margin-left:5px;" for="max">MIN</label><input type="text" style="width:  300px; margin-left:10px;" class="form-control" name="min" value="MIN -'+varTitle+'" /></div><br /><br />' +
               '<div class="form-group"><input id="max" type="checkbox" class="form-control theCheck" /><label style="margin-left:5px;" for="max">MAX</label><input type="text" style="width:  300px; margin-left:10px;" class="form-control" name="max" value="MAX -'+varTitle+'" /></div><br /><br />' +
              '<br /><button id="valid-choose-func" class="button ce pi ahr">Valider</button></form></div>');
        });*/

        /*$("#valid-choose-func1").click(function(event){
            //var $objectClick;
            event.preventDefault();
            canClose = 1;
            console.log($objectClick);
            $objectClick.attr("data-example", "Haiti trou de merde");
            $(".theCheck").each(function(){
              if($(this).is(":checked")){
                le_id = this.id; //console.log(this.id);
                $("form input").each(function(){
                    if(le_id == $(this).attr("name")){
                      // console.log($(this).val());
                      if($(this).val() == ""){
                        canClose = 0;
                      }
                    }

                });
              }
            });

            if(canClose == 1){
              console.log("Modification opere avec success");
            }else{
              console.log("Le champs cocher ne doit pas etre vide!");
            }
        });*/
        // if($("#rbox").children().length > 0){
        //     $("#rbox li div.kill_area").html('<i class="fa fa-close" style="font-size:24px;color:red"></i>');
        //     $("li.ui-state-disabled").css("background-image","");
        //
        //     $("#rbox li").append('<div class="showmn"></div>');
        //     if($("#rbox li .showmn").html() == ""){
        //        $("#rbox li .showmn").html('<i class="fa fa-chevron-down" style="margin-top: 21px;margin-left: -250px; color:white;"></i>');
        //     }
        //
        //     if( $("#rbox li div.showmn i").html() == ""){
        //         $("#rbox li div.showmn i").click(function(){
        //             alert("C'est moi");
        //         });
        //     }
        //
        //    // html('<i class="fa fa-close" style="font-size:24px;color:red"></i>');
        // }

        // if($("#cbox").children().length > 0){
        //     $("#cbox li div.kill_area").html('<i class="fa fa-close" style="font-size:24px;color:red"></i>');
        //     $("li.ui-state-disabled").css("background-image","");
        // }
        //setTimeout(miness,500);
    }

    $(document).ready(function(){
        miness();
        cmSelector();
        unactiveBuildStats();

    });

    $(document).ready(function(){
      //$(document).ready(function(){
        $("switchx").on("click",function(ev){
            ev.preventDefault();
            alert("switch");
        });
        $("#dxsave,#saving").click(function(){
            setTimeout(function(){
                 $('a[href="#tabs-1"]').closest("li").addClass("tabs-selected");
                 $('a[href="#tabs-5"]').closest("li").removeClass("tabs-selected");
                 $("#tabs-1").removeClass("tabs-hide");
                 $("#tabs-5").addClass("tabs-hide");
            },500);

        });
    });

</script>
<script type="text/javascript" src="<?php echo DP_BASE_URL;?>/modules/outputs/output_Js_script_RPA.js"></script>
<?php

}

	if ($m == 'manager'){
		?>
<script type="text/javascript" src="modules/outputs/jquery-ui.js"></script>
<link rel="stylesheet" type="text/css"
	href="/modules/outputs/gchart.css" />
<script type="text/javascript" src="/modules/outputs/gchart.js"></script>
<script>
			//window.onload = up;
			function up() {
				charter.init();
			}
			up();
			//var alevels = @@alevels@@;
		</script>
<?php
	}
	?>

<link rel="stylesheet" type="text/css"
	href="/style/<?php echo $uistyle;?>/chosen.css" />
<style type="text/css" media="all">
/* fix rtl for demo */
/*.chosen-rtl .chosen-drop {
	left: -9000px;
}*/
</style>
<script type="text/javascript"
	src="/style/<?php echo $uistyle;?>/jquery-chosen.js"></script>

<script type="text/javascript">
    $(document).ready(function(){
//    alert("execute");
        $(".tabs-nav .tabs-selected  a").css("border-bottom","4px solid #354c8c");
        console.log($("#toptab_\\d")); //css("border-bottom","4px solid #354c8c");
    });

</script>

<script type="text/javascript">
		$(function () {
	        //$(".chosen").chosen();
	        /* var config = {
	        	      '.chosen'           : {},
	        	      '.chosen-deselect'  : {allow_single_deselect:true},
	        	      '.chosen-no-single' : {disable_search_threshold:10},
	        	      '.chosen-no-results': {no_results_text:'Oops, nothing found!'},
	        	      '.chosen-width'     : {width:"95%"}
	        	    }
	        	    for (var selector in config) {
	        	      $(selector).chosen(config[selector]);
	        	    } */
	    });

	    if($j('#rtable')!=null)
	    	$j('#rtable').attr('style','display:block');
	    $(function () {
    	<?php if($m==='tasks' && $a==='selector'){?>
		    	  //console.log($("#rtable th:first-child").html());
		    	  $("#rtable th:first-child div").remove();//.attr('class', '');
    	<?php }?>
	    });
	</script>
<?php
if($m=='dashboard'){
    ?>
    <script src="/style/default/mresize.js"></script>
    <script type="text/javascript">
    $j("#tabs").tabs().show();
     $j(".sstabs").tabs().show();

    $j(document).ready(function(){
        $j(".nav-link").click(function(){
            var locationHash = $(this).attr('href').replace('#','');
            $(".nav-link").css("color","#1997c6");
            $(this).css("color","red");
            $j(".tabContent").hide();
            $j('#'+locationHash).show();
        });
        var locationHash = window.location.hash.substring(1);
        $j(".tabContent").hide();
        $j('#'+locationHash).show();
        $j(".resizable").on("mresize",function(){
            if($j(this).width()>600){
                $j(this).parent().attr('class', 'col-md-12');
            }else{
                $j(this).parent().attr('class', 'col-md-6');
            }
            console.log($j(this).width()+"x"+$j(this).height());
        });
    });

    </script>

    <script>
        $(document).ready(function(){
            $(".getInfo").click(function(){
                    if($(this).closest("div.col-md-6").find("div.descrip").css("display") == "none"){
                        $(this).closest("div.col-md-6").find("div.descrip").css("display","block");
                    }else{
                        $(this).closest("div.col-md-6").find("div.descrip").css("display","none");
                    }
            });

        }
        );
    </script>
    <?php
}
if($m=='projects'){
    ?>
    <script type="text/javascript">
        $j("#tabs").tabs().show();
    </script>
<?php
}
?>
<?php
// Added By RPAlexis
if($m=='system' || $m == 'wizard'){
?>
<script type="text/javascript" src="<?php echo DP_BASE_URL;?>/style/default/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="<?php echo DP_BASE_URL;?>/style/default/bootstrap.min.js"></script>

<script type="text/javascript">
  $(document).ready(function(){
//        $("div#tabs ul.nav.nav-tabs").removeClass("tabs-nav");
  });
</script>

<script>
	$(document).ready(function(){
		$("input.text ,select.text").addClass("form-control");
		$("input.text ,select.text").css("width","400px");

		$("table.usub tbody input.text").removeClass("form-control");
		$("table.usub tbody select.text").removeClass("form-control");
		$("table.usub tbody input.text").css("width","100px");
		$("table.usub tbody select.text").css("width","100px");

		$("table.mtab tbody tr input[type=button]").removeClass("form-control");
		$("table.mtab tbody tr input[type=button]").addClass("button ce pi ahr");
		$("table.mtab tbody tr textarea").addClass("form-control");

	});
</script>


<?php
}

if($m=='outputs' AND isset($_GET['rep'])){
?>
<script type="text/javascript">
setTimeout(function(){
     $('a[href="#tabs-1"]').closest("li").removeClass("tabs-selected");
     $('a[href="#tabs-5"]').closest("li").addClass("tabs-selected");
     $("#tabs-1").addClass("tabs-hide");
     $("#tabs-5").removeClass("tabs-hide");
},2000);

 $.post("/?m=outputs&a=reports&suppressHeaders=1&mode=updated",
    {
        idRep: "<?php echo $_GET['rep']?>",
        city: "Duckburg"
    },function(data,status){
//        console.log(data);
        rslt = JSON.parse(data);
        console.log(rslt);
        var sec = function getSectionString(sIndex,id,content,types){
            var sectionTmpl = ["<tr id='sec_"+ sIndex+ "' data-stype='' data-sid='"+ sIndex+ "' class='slink zxrow'><td colspan='2' class='sec_ware'>"+
                "<div style='float: left;'>Section name&nbsp;</div><div style='float:left;width: 350px;'>"+
                "<input type='hidden' class='text offview  longwrite ichsection'  name='sec["+ id+ "][name]'><div class='rte_fld'></div>"+
                "<div style='float: right;'>"+
                "<span class='fbutton delbutt' onclick='reporter.delSection("+ sIndex+ ",true)' title='Remove Section'></span>"+
                "<span class='fbutton sceditor' onclick='reporter.editSection("+ sIndex+ ",this)' title='Edit Section'></span>"+
                "<span class='section_move section_move_up' title='Move UP'></span>"+
                "<span class='section_move section_move_down' title='Move DOWN'/></span>"+
                "</div><div class='sec_cont_view'></div><input type='hidden' name='sec["+ sIndex+ "][content]' class='sec_cont_all' id='cnt_id_' value='"+content+"'>"+
                "<input type='hidden' name='sec["+ sIndex+ "][type]' class='sec_cont_type' id='ctype_id_' value='"+types+"'></td>" +
                "<td style='width: 120px;'><span class='fbutton delbutt fhref fa fa-trash-o' onclick='reporter.delSection("+sIndex+",true)' title='Remove Section'></span><span class='fbutton sceditor fa fa-pencil' onclick='reporter.editSection("+sIndex+",this)' title='Edit Section'></span><span class='section_move section_move_up fa fa-arrow-up' title='Move UP'></span><span class='section_move section_move_down fa fa-arrow-down' title='Move DOWN'></span>"+
                "</td></tr>"];
                return sectionTmpl.join();
        }
        setTimeout(function(){
            $("#tabs-5 #rep_name").val(rslt['titre']);
            $("#tabs-5 #rep_desc").val(rslt['entries']['rep_desc']);
            $("#tabs-5 #rep_dept").find("option").each(function(i,e){
                    $(this).removeAttr("selected","selected");
                    if($(this).attr("value") == rslt['entries']['rep_dept']){
                        $(this).attr("selected","selected");
                    }
            });
            $("#tabs-5 #rep_start").val(rslt['start_date']);
            $("#tabs-5 #rep_end").val(rslt['end_date']);

//            alert($j(sec(1,1)).find(".breport").html());

                alert(rslt['entries']['sec'][1]);
                   var sections = rslt['entries']['sec'];
                   for(i = 1; i<= Object.keys(sections).length; i++){
                        $(".breport").append($j(sec(i,i,sections[i]['content'],sections[i]['type'])));
//                        $(".breport").find("input[name=sec["+i+"][content]]").attr('value',sections[i]['content']);
////                        $(".breport").find("input[name=sec["+i+"][content]]").val(sections[i]['content']);
//                        $(".breport").find("input[name=sec["+i+"][type]]").val(sections[i]['type']);
//                        alert(sections[i]['content']);
                   }

//                $(".breport").append($j(sec(1,1)));
                $("#second-column").css("display","none");
        },3000);
    });


//        if($("#qtable")){
//              $('a[href="#tabs-1"]').closest("li").removeClass("tabs-selected");
//              $('a[href="#tabs-5"]').closest("li").addClass("tabs-selected");
//
//              alert($('a[href="#tabs-1"]').closest("li").html());
//              alert($('a[href="#tabs-5"]').closest("li").html());
//        }
//      $("#qtable").load(function(){
//              $('a[href="#tabs-1"]').closest("li").removeClass("tabs-selected");
//              $('a[href="#tabs-5"]').closest("li").addClass("tabs-selected");
//
//              alert($('a[href="#tabs-1"]').closest("li").html());
//              alert($('a[href="#tabs-5"]').closest("li").html());
//      });
    });
</script>
<?php
}
if($m="projects" AND $a = "view"){?>
    <script>
        $(document).ready(function(){
                $("#mholder").css("overflow-x","scroll");

        });
</script>
<?php }

if($m = "outputs" AND $a="reports" AND isset($_GET["mode"]) AND $_GET["mode"] == "compile"){
?>
<script type="text/javascript" src="<?php echo DP_BASE_URL;?>/style/default/jspdf.min.js">
</script><script type="text/javascript" src="<?php echo DP_BASE_URL;?>/style/default/html2canvas.js"></script>
<script type="text/javascript">
function genPDF(){
    html2canvas($('#content'),{
        onrendered: function(canvas){
//        var imgWidth = 210;
//        var pageHeight = 295;
//        var imgHeight = canvas.height * imgWidth / canvas.width;
//        var heightLeft = imgHeight;
//        var doc = new jsPDF('p', 'mm');
//        var position = 0;
//        var imgData = canvas.toDataURL('image/png');
//        doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
//        heightLeft -= pageHeight;
//
//        while (heightLeft >= 0) {
//          position = heightLeft - imgHeight;
//          doc.addPage();
//          doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
//          heightLeft -= pageHeight;
//        }
//        doc.save( 'file.pdf');﻿
            var width = canvas.width;
            var height = canvas.height;
            var millimeters = {};
            millimeters.width = Math.floor(width * 0.264583);
            millimeters.height = Math.floor(height * 0.264583);
            var img =  canvas.toDataURL("image/png");
            var doc = new jsPDF("p", "mm", "a6");
            var widthP = doc.internal.pageSize.width;
            var heightP = doc.internal.pageSize.height;
//            doc.deletePage(1);
//            doc.addPage(widthP,heightP);
            doc.addImage(img,"JPEG",0,0);
            doc.save("pdf_export.pdf");
        }
    });
}

function genPDFFromHtml(){
    var pdf = new jsPDF('p','pt','a4');
    pdf.fromHTML($("#misere"),20,20,{"width":500});
//    pdf.addHTML($("#cont"),function(){
//    });
    pdf.save('web.pdf');
}
//function genPDF(){
//    var doc = new jsPDF();
//    doc.fromHTML($('#content').get(0),20,20,{'width':500});
//    doc.save("my.pdf");
//}
//var doc = new jsPDF();
//$('#cmd').onclick(function () { alert("PDF");
//    doc.addHTML($('#content').html(), 15, 15, {
//        'width': 170
//    });
//    doc.save('sample-file.pdf');
//});
//    $(document).ready(function(){
//
//var doc = new jsPDF();
//var specialElementHandlers = {
//    '#editor': function (element, renderer) {
//        return true;
//    }
//};alert("tmk");
//$('#cmd').onclick(function () {
// alert("jjjj");
//    doc.fromHTML($('#content').html(), 15, 15, {
//        'width': 170,
//            'elementHandlers': specialElementHandlers
//    });
//    doc.save('sample-file.pdf');
//});
//    });
</script>
<?php }?>
</body>

</html>
