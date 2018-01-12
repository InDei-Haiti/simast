<?php

function loadAreas($level){
	$q = new DBQuery();
	$q->addTable('st_area');
	$q->addWhere('parent_id="'.$level.'"');
	$q->addOrder("id");
	$list = $q->loadList();

	return $list;
}

$abc = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

if(isset($_GET['mode'])){
	switch ($_GET['mode']){
		case 'save':
			if($_POST['items'] != ''){
				$items = json_decode(stripslashes($_POST['items']),true);
				$prefix = mysql_real_escape_string($_POST['prefx']);
				$parent = (int)$_POST['parent'];
				$inserts=array();
				foreach($items as $myid => $nas){
					if($nas[0] > 0){
						$sql='update st_area set title="%s",prex="%s" where parent_id="%d" and id = "%d"';
						$res=mysql_query(sprintf($sql,$nas[1],($parent == 0 ? $abc{$myid} : $prefix.'.'.($myid+1)),$parent,$nas[0]));
					}else{
						$inserts[]='("'.$parent.'", "'.mysql_real_escape_string($nas[1]).'","'.($parent == 0 ? $abc{$myid} : $prefix.'.'.($myid+1)).'")';
					}
				}
				if(count($inserts) > 0){
					$sql = 'insert into st_area (parent_id,title,prex) VALUES '.join(",",$inserts);
					$res = mysql_query($sql);
				}
				echo json_encode(loadAreas($parent));
			}
		break;
		case 'load':
			$lq = (int)$_GET['glevel'];
			$list = loadAreas($lq);
			echo json_encode($list);
		break;
	}
	return;
}

?>
<script type="text/javascript" src="<?php echo DP_BASE_URL?>/style/default/jquery-1.7.1.min.js"></script>
<script type="text/javascript">

	/*
	Fill list of level items for VIEW
	 */
	function fillLevel(tar,$tgt){
		for(var rs in tar){
			if(tar.hasOwnProperty(rs)){
				$(["<li id='area_",tar[rs]['id'],"'><div class='afold' data-state='off'>+</div><span class='intext'>",tar[rs]['title'],"</span><ol></ol></li>"].join("")).appendTo($tgt);
			}
		}
	}

	/*
	Dialog tool for edit levels
	 */
	function editAreas(level){
		var abc = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$("#sbox").append("<ol id='dear'></ol>").dialog({
			modal: true,
			width : "445px",
			buttons: {
				Add: function(){
					$("#dear").append(["<li>","<input type='text' class='text' data-pid='0' value=''>&nbsp;&nbsp;<button class='drow'>X</button></li>"].join("")).find("li:last input").focus();
				},
				Save: function(){
					var tops=[],$tgt = $("#top"),prefx=[],$preparts;
					$("#dear").find("input.text").each(function(){
						tops.push([$(this).attr("data-pid"),$(this).val()]);
					});
					if(level > 0){
						$tgt = $("#area_"+level).find("ol:first").empty();
						$preparts = $tgt.parents("li");
						$preparts.each(function(){
							if($(this).parent().attr("id") != 'top'){
								prefx.push($(this).index() + 1);
							}else{
								prefx.push(abc[$(this).index()]);
							}
						});
					}else{
						$tgt.empty();
					}

					if(tops.length > 0){
						$.post("/?m=system&a=sareas&suppressHeaders=1&mode=save",{parent: level,items: JSON.stringify(tops),prefx : prefx.reverse().join(".")},function(msg){
							if(msg && msg.length > 0 && msg !='fail'){
								var apprvd = $.parseJSON(msg);
								fillLevel(apprvd,$tgt);
								$("#sbox").dialog("option","title","Strategy areas saved !")
										.dialog("option","buttons",[])
										.effect( 'fade', {}, 1000, function(){
											$("#sbox").dialog("destroy").empty();
										});
							}
						});
					}
				},
				Cancel: function(){
					$("#sbox").dialog("destroy").empty();
				}
			}
		});
		/*
		Load data for this level and fill values into text fields for EDIT !!!!
		 */
		$.when(loadLevelBack(level))
			.done(function(msg){
				console.log("LevelBack");
					if(msg && msg.length > 0){
						var pts = $.parseJSON(msg),$tgt = $("#dear");
						if(pts && pts.length > 0){
							for(var i=0, l= pts.length; i < l; i++){
								$(["<li>","<input type='text' class='text' data-pid='",pts[i]['id'],"' value='",pts[i]['title'],"'></li>"].join("")).appendTo($tgt);
							}
						}
					}
			});
	}

	/*
	Back-end tool for async download of level data
	 */
	function loadLevelBack(level){
		var $gload = $.get("/?m=system&a=sareas&suppressHeaders=1&mode=load",{glevel: level});
		return $gload.promise();
	}

	$(".drow").live("click",function(){
		$(this).closest("li").remove();
	});

	$(".afold").live("click",function(){
		var $row = $(this).parent(),
			curID = $row.attr("id"),
			curLevel = curID.replace("area_",""),
			cstate = $(this).attr("data-state"),

			nstate,ntext;
		if(cstate == 'on'){
			//need to close all child levels
			nstate = 'off';
			ntext = '+';
			$row.find("button").remove().end().find("ol").empty();
		}else if(cstate == 'off'){
			//Now we need to download level, append it to OL and provide button for edit
			nstate = 'on';
			ntext = '-';

			$row.find(".afold").addClass("back_load").text("").after("<button class='text' onclick='editAreas("+curLevel+")'>Add/Edit</button>");
			$.when(loadLevelBack(curLevel))
				.done(function(msg){
					if(msg && msg.length > 0){
						var tar = $.parseJSON(msg);
						fillLevel(tar,$row.find("ol"));
						$row.find(".afold").removeClass("back_load");
					}
				});
		}
		$row.find(".afold")
					.text(ntext)
					.attr("data-state",nstate);
	});

	window.onload = up;

	function up (){
		$.when(loadLevelBack(0))
			.done(function(msg){
				if(msg && msg.length > 0){
					var tar = $.parseJSON(msg);
					fillLevel(tar,$("#top"));
				}
			});
	}
</script>
<br /><br />
<div class="card">
<b>Strategy Areas</b>
<hr>
<ol id="top" style="list-style: upper-alpha;"></ol>

<button class="text" onclick="editAreas(0);">Add/Edit Areas</button>
<div id="sbox" style="display: none;" title="Edit areas"></div>

<style type="text/css">
	.afold:hover{
		cursor: pointer;
	}
	#top li{
		padding: 3px;
		margin: 2px;
		padding-left: 20px;
	}
	.afold{
		border: 1px solid #000000;
        float: left;
        font-size: 10pt;
        font-weight: 800;
        height: 18px;
        margin-right: 3px;
        text-align: center;
        width: 20px;
	}
	.afold:hover{
		background-color: #cdcdcd;
		
	}
	#dear li .text{
		width: 320px;
		margin: 2px;
	}
	.intext:hover{
		background-color: #dcdcdc;
	}
	.back_load{
		background: url("/images/progress.gif") no-repeat 5px 4px;
	}

</style>

</div>