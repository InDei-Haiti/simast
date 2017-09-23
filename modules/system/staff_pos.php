<?php
/**
 * Created by JetBrains PhpStorm.
 * User: stig
 * Date: 30.05.11
 * Time: 20:59
 */

if($_GET['mode'] === 'savepos'){
	$nid=(int)$_GET['id'];
	$ntext = mysql_real_escape_string(trim($_GET['txt']));

	$res = false;

	if($nid === 0){
		$sql='insert into positions (title) values ("'.$ntext.'")';
		$res = mysql_query($sql);
		if($res){
			$res  = mysql_insert_id();
		}
	}elseif($nid > 0){
		$sql='update positions set title="'.$ntext.'" where id="'.$nid.'"';
		$res= mysql_query($sql);
	}
	if($res){
		echo $res;
	}else{
		echo 'fail';
	}

	return;
}elseif($_GET['mode'] === 'setstaff'){
	$dpos=(int)$_POST['bpos'];
	$list=json_decode(stripslashes($_POST['slist']));
	//$list=explode(",",$plist);

	$sql = 'delete from staff_position where position_id="'.$dpos.'"';
	$res=mysql_query($sql);

	if(is_array($list) && count($list) > 0){
		$sql='insert into staff_position (contact_id, position_id) values ';
		$pad= array();
		foreach($list as $l){
			$pad[]='("'.(int)$l.'","'.$dpos.'")';
		}
		$sql.=join(",",$pad);
		$res=mysql_query($sql);
	}

	if($res){
		echo 'ok';
	}else{
		echo 'fail';
	}

	return;
}elseif($_GET['mode'] === 'stafflist'){
	$q = new DBQuery();
	$q->addTable('contacts','ct');
	$q->addQuery(" ct.contact_id as id,concat(contact_first_name,' ',contact_last_name) as name");
	$q->addJoin('staff_position', 'sp','ct.contact_id = sp.contact_id');
	//$q->addWhere("sp.contact_id is NULL");
	$q->addWhere("ct.contact_first_name is not null");
	$q->addWhere('ct.contact_id<>"13"');
	$sql=$q->prepare();
	$stlist = $q->loadHashList();

	if(count($stlist) > 0)
		echo json_encode($stlist);
	else
		echo 'fail';
	return;
}

mysql_query('SET GLOBAL group_concat_max_len=512');

$sql = "SELECT p1.id,title, group_concat(contact_id separator ',') as list
		from  ( `positions` as p1 )
			left join staff_position p2
				on (p1.id = p2.position_id)
		group by title
		ORDER BY title";

//$sql = "SELECT p1.id,title,(select count(*) from staff_position p2 where p1.id = p2.position_id) as total FROM ( `positions` as p1 ) ORDER BY title";

$res = mysql_query($sql);
$posns = array();
if(mysql_num_rows($res) > 0){
	while($trow=mysql_fetch_assoc($res)){
		if(is_null($trow['list'])){
			$trow['list'] = array();
		}else{
			$trow['list']=explode(',',$trow['list']);
		}
		$posns[$trow['id']]=$trow;

	}
}


?>
<br /><br />
<div class="card">
<link rel="stylesheet" type="text/css" href="/modules/system/staff_pos.css" />
<ol id="positions"></ol>

<p class="fhref" onclick="$j('#npbox').toggle()">Add new position</p>
<div id="npbox" style="display: none;">
	<input  type="text" class="text" value="" size="20">
	<button onclick="addNew();" class="button ce pi ahr">Save</button>
</div>

<p class="fhref" onclick="showStaffList()">Define position</p>
<div id="define" style="display: none;">
	<form method="post" id="dotypes" >
		<select name="bpos" class="text"></select>
		<div id="fillstaff"></div>
	    <input type="button" class="button ce pi ahr" value="Update" onclick="checkStaff()">
	</form>
</div>
<script type="text/javascript">
	var $plist,$nbox,$tofill,psn = <?php echo json_encode($posns); ?>,
		listvis = false;

	function collectPositions (){
		var $tsel=$j("#define").find("select"),prev = $tsel.val();
		$tsel.empty().append("<option value='-1' disabled='disabled'>-- Select --</option>");
		if($plist && $plist.length === 1 && $plist.find("li").length > 0){
			$plist.find("li").each(function(){
				$j(["<option value='",$j(this).attr("data-pid"),"'>",$j(this).find("span:first").text(),"</option>"].join("")).appendTo($tsel);
			});
		}
		$tsel.val(function(i,x){
			if(prev && prev != '-1'){
				return prev;
			}else	return  "-1";
		});
	}

	function showStaffList(){
		collectPositions();
		if(listvis === false){
			$j.get("/?m=system&a=staff_pos&suppressHeaders=1&mode=stafflist",function(msg){
				if(msg && msg !== 'fail'){
					var mlist=$j.parseJSON(msg);
					for(var i in mlist){
						if(mlist.hasOwnProperty(i)){
							$j(["<label><input type='checkbox' name='slist[]' value='",i,"'>",mlist[i],'</label><br>' ].join("")).appendTo($tofill);
						}
					}
					if(amnt(mlist) > 0){
						$j("#define").show();
					}else{
						$j("#define").hide();
					}
				}
			});
			listvis = true;
			$j("#define").show();
		}else{
			$j("#define").toggle();
		}

	}

	function checkStaff(){
		var tosend=[],posid=$j("#define").find("select").val();
		/*if($tofill.find("input:checked").length > 0){
			//$j("#dotypes").submit();

		}*/
		$j("#define").find(".vbut").attr("disabled",true).after("<div class='loading'></div>");
		$tofill.find("input:checked").each(function(){
			tosend.push($j(this).val());
		});

		$j.post("/?m=system&mode=setstaff&suppressHeaders=1&a=staff_pos",{slist: JSON.stringify(tosend),bpos: posid},function(msg){
			if(msg && msg === 'ok'){
				$j("#define").append("<span class='msg'>Staff positions updated</span>").find(".msg").fadeOut(3000,function(){
					$j(this).remove();
				});
				psn[posid]['list']=tosend;
				$plist.find("li[data-pid='"+posid+"']").find(".tcnt").addClass("bold").text(tosend.length);
			}
			$j("#define").find(".vbut").attr("disabled",false).end().find(".loading").remove();
		});

	}

	function up(){
		$plist = $j("#positions");
		$nbox=$j("#npbox");
		$tofill=$j("#fillstaff");

		if(psn && amnt(psn) > 0){
			fillPos(psn);
		}

		$plist.delegate("li","dblclick",function(e){
			var $cli = $j(this),ctext=$cli.find("span:first").text(),cid = $cli.attr("data-pid"),cnt=$j(this).attr("data-cnt");
			$cli.find("span:first").html(["<input type='text' class='text' value='", ctext ,"'>"].join(""));
			$cli.find(".text").keypress(function(e){
				var code = (e.keyCode ? e.keyCode : e.which),$pli=$j(this).closest("li"),ntxt = $j(this).val();
				if(code === 27){
					$pli.find("span:first").empty().text(ctext);
				}else if(code === 13 ){
					if(ntxt && trim(ntxt).length > 0){
						$j.when(storePos($pli.attr("data-pid"),ntxt,$pli ))
							.done(function(msg){
								if(msg && msg != 'fail'){
									msg = parseInt(msg);
									$pli.removeClass("alert").find("span:first").empty().text(ntxt);
								}
							});
					}else{
						$pli.addClass("alert");
					}
				}
			});
		});

		$j("#dotypes").find("select").change(function(){
			var ulist=psn[$j(this).val()].list;
			$tofill.find("input").attr("checked",false).each(function(){
				$j(this).attr("checked",function(){
					var res=false;
					if($j.inArray($j(this).val(),ulist) >= 0){
						res = true;
					}
					return res;
				})
			});
		});
	}

	function addNew(){
		var ptext=$nbox.find(".text").val(),np=amnt(psn),fob = {};
		/*var nid = storePos(false,ptext,$nbox);*/
		$j.when(storePos(false,ptext,$nbox))
			.done(function(msg){
					if(msg && msg !== 'fail'){
						msg = parseInt(msg);
						$nbox.hide().find(".text").val("");
						var vc = {
							id: msg,title : ptext,list : []
						};
						fob[msg]=vc;
						fillPos(fob);
						psn[msg]=vc;
					}
				});
	}

	function fillPos(list){
		for(var i in list){
			if(list.hasOwnProperty(i) && list[i] !== null){
				$j(["<li data-pid='",list[i].id,"' data-cnt='",list[i].list.length,"'><span>",list[i].title,"</span>&nbsp;&nbsp;Total:&nbsp;<span class='tcnt'>",list[i].list.length,"</span></li>"].join("")).appendTo($plist);
			}
		}
	}

	function storePos(vid,vtext,obj){
		$j(obj).append($j("<div/>",{"class": "loading"}));
		var pq = $j.get("/?m=system&a=staff_pos&suppressHeaders=1&mode=savepos",{id: vid, txt: vtext},function(msg){
			if(msg && msg.length > 0 && msg !== 'fail'){
				msg=parseInt(msg);
			}else{
				msg = false;
			}
			$j(obj).find(".loading").remove();
			return msg;
		});
		return pq.promise();
	}


	window.onload = up;

</script>
</div>