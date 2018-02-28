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
<!--<script type="text/javascript" src="<?php /*echo DP_BASE_URL*/?>/style/default/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="<?php /*echo DP_BASE_URL*/?>/style/default/jquery-ui.min.js"></script>-->

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

    .ui-widget-header {
        border: 1px solid #354c8c;
        background: #354c8c 50% 50% repeat-x;
        color: #ffffff;
        font-weight: bold;
    }

</style>

</div>