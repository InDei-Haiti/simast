<?php
/**
 * Created by JetBrains PhpStorm.
 * User: stig
 * Date: 15.03.11
 * Time: 13:54
 * To change this template use File | Settings | File Templates.
 */

global $dPconfig;

$types = dPgetSysVal('ClientStatus');

$types = arrayMerge(array('-1'=>'-- Select Status --'),$types);

$dateway = array('lt'=>"less than",'eq'=>'equal to','gt'=>'more than');

$andor = array('1'=>'AND',2=>'OR');

$codeSigns = array('lt'=>'<','eq' => '=','gt' => '>');

$msg='';

$today = date("Ymd");

if($_SERVER['REQUEST_METHOD'] === 'POST'){
	$forSave = array();
	$sqleq = array();
	if($_SERVER['CONTENT_LENGTH'] > 0){
		foreach($_POST['data'] as $did => $dvals){
			$sqleq[]='( clients.client_status="'.intval($dvals['status']).'" '.
			(intval($dvals['month']) > 0 ?
			$andor[$dvals['and']].' period_diff(date_format(now(),"%Y%m"), date_format(clients.client_lvd,"%Y%m" ) ) '.$codeSigns[$dvals['lvd']].' '.intval($dvals['month'])
			:
			''
			)
			.')';
			$forSave[] = $dvals;
		}
	}
	$regular_definition = "(".join(' OR ',$sqleq)." )";
	if($regular_definition === '( )'){
		$regular_definition='';
	}
	$sql= "update config set config_value = '".$regular_definition."' where config_name='regular_definition'";
	$res=mysql_query($sql);

	$sql= 'update config set config_value = "'.mysql_real_escape_string(serialize($forSave)).'" where config_name="regular_vars"';
	$res=mysql_query($sql);

	$saved = $forSave;
	$msg='Changes saved';
	
	
	//update set of clients considered as regular and The Others Group.
	$sql = 'select client_id from clients where client_obsolete="0" and '.$regular_definition;
	$res=mysql_query($sql);
	if($res && mysql_num_rows($res) > 0){
		while($clac = mysql_fetch_array($res)){
			$actives[]=$clac[0];
		}
		$actives_sql = join(",",$actives);

		if(strlen($actives_sql) > 0){
			$sup="update clients set client_obsolete='1' where client_status <> '9' and client_id not in ( ".$actives_sql." )";
			$res2= mysql_query($sup);

			if($res2){
				$sup2 = 'update config set config_value="'.$today.'" where config_name="regular_scan"';
				$res3 = mysql_query($sup2);
			}
		}
	}
}else{

	$saved = unserialize($dPconfig['regular_vars']);

}


$formula='';
$formula_html='<i>Defined parameters</i><br>
<ol style="padding-left: 20px;">';

if(is_array($saved) && count($saved) > 0){
	foreach($saved as $sid => $dvals){
		$formula.= '<li data-rid="'.$sid.'">
		Client Status '.arraySelect($types,'data['.$sid.'][status]',' class="text sts" ',$dvals['status']).'&nbsp;&nbsp;
		'.arraySelect($andor,'data['.$sid.'][and]',' class="text" ',$dvals['and']).'&nbsp;&nbsp;
		LVD '.arraySelect($dateway,'data['.$sid.'][lvd]',' class="text" ',$dvals['lvd']).'&nbsp;&nbsp;
		Date period in months&nbsp;<input type="text" class="text nums" name="data['.$sid.'][month]" value="'.$dvals['month'].'" size="5">
		<div class="fbutton addbutt force_off_left" title="add" onclick="rowDealer(\'add\',this)"></div>
		<div class="fbutton delbutt force_off_left" title="delete" onclick="rowDealer(\'del\',this)"></div>
		</li>';
		$formula_html.='<li>Status = '.$types[$dvals['status']].' '.
			($dvals['month'] > 0 ? 
			$andor[$dvals['and']].' LVD '.$codeSigns[$dvals['lvd']].' '.$dvals['month'].'&nbsp;months'
			:
			'').
			'</li>';
	}
	$formula_html.='</ol>';
}else{
	$formula_html= '<p>Not yet defined</p>';
	$formula = '<li data-rid="0">
		Client Status '.arraySelect($types,'data[0][status]',' class="text sts" ',-1).'&nbsp;&nbsp;
		'.arraySelect($andor,'data[0][and]',' class="text" ',1).'&nbsp;&nbsp;
		LVD '.arraySelect($dateway,'data[0][lvd]',' class="text" ','lt').'&nbsp;&nbsp;
		Date period in months&nbsp;<input type="text" class="text nums" name="data[0][month]" value="" size="5">
		<div class="fbutton addbutt force_off_left" title="add" onclick="rowDealer(\'add\',this)"></div>
		<div class="fbutton delbutt force_off_left" title="delete" onclick="rowDealer(\'del\',this)"></div>
	</li>';
}

$moduleScripts[]="./modules/system/jquery.numeric.js";
?>
<span class="msg_ok" style="font-weight:800;font-size:12pt;"><?php echo $msg;?></span><br>
<?php echo  $formula_html;?>

<h2>
	Changes to formula here
<h2>
<p id="new_rule" class="fhref" title="Add new rule"><u>Add rule</u></p>
	<hr width="500" align="left">
<form method="POST" action="?m=system&a=definition" name='defiform'>
<link rel="stylesheet" type="text/css" href="./modules/outputs/outputs.module.css" />

<script type="text/javascript">
	function rowDealer(mode,row){
		var $ols = $j("#rules"),
		$crow = $j(row).closest("li"),
		rowsl = $ols.find("li").length;
		if(rowsl === 0){
			$crow = $listore;
		}
		if(mode === 'del'){
			if(rowsl === 1){
				$listore = $crow.detach();
				$j("#new_rule").show();
			}else{
				$crow.remove();
			}			
		}else if(mode === 'add'){
			var inid = $ols.data("trip"),
			$nrow = $crow.clone(true).html(function(i,c){
				c= c.replace(/data\[\d+\]/g,"data["+ (++inid) +"]");
				return c;
			}).find(":input").val("-1").filter("input").val("").numeric().end().end().appendTo($ols);
			$ols.data('trip',inid);
		}

	}

	function checkVals(){
		var $ols= $j("#rules"),wrong=0,
		$missed = $ols.find("select.sts").each(function(){
			if($j(this).val() == -1){
				$j(this).css("border", "2px solid red");
				++wrong;
			}
		});
		if(wrong == 0){
			if(confirm("Do you want to change regular formula now?")){
				document.defiform.submit();
			}			
		}else{
			alert("Please select status for client");
			return false;
		}
	}
</script>

<?php

echo '<ol id="rules">',$formula,'</ol>
<input type="button" value="Save" class="text" onclick="checkVals();">
</form>';
?>

<script type="text/javascript">
	window.onload = up;
	var $listore,
	realrows = <?php echo count($saved);?>;
	function up(){
		var $ols=$j("#rules"),
		rows=$ols.find("li").length;
		$ols.data("trip",rows);
		$ols.find(".nums").numeric();
		$j(".msg_ok").fadeTo(5000,0,function(){
			$j(this).hide();
		});
		$j("#new_rule").click(function(){
			$j("#rules").show();		
			$j(this).hide();
			rowDealer('add',false);
		});
		if(realrows > 0){
			$j("#new_rule").hide();
		}else{
			$j("#rules").hide();			
			$j("#new_rule").show();
		}
	}
</script>