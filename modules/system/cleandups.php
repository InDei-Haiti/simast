<?php
/**
 * Created by JetBrains PhpStorm.
 * User: stig
 * Date: 19.05.12
 */
echo '<br /><br /><div class="card">';

$sql = 'select distinct client_id from ltp_transfers ';
$res = mysql_query($sql);
$sql_dupl = 'select * from ltp_transfers where client_id="%d" order by status asc';
$sql_del = 'delete from ltp_transfers where id  IN (%s)';
$delete_list = array();
if(is_resource($res)){
	while($row = mysql_fetch_assoc($res)){
		$clr = mysql_query( sprintf($sql_dupl,$row['client_id']) );
		//search for entry with already transfer defined
		$done_id = false;
		$temp_for_del = array();
		$do_merge= false;
		while($row_dub = mysql_fetch_assoc($clr)){
			if($row_dub['status'] == '1'){
				$done_id = $row_dub['id'];
			}else{
				$temp_for_del[]=$row_dub['id'];
			}
		}
		if($done_id !== false && count($temp_for_del) > 0){
			$do_merge = true;
		}else{
			if($done_id === false){
				if(count($temp_for_del) > 1){
					$tar = array_shift($temp_for_del);
					$do_merge = true;
				}
			}
		}
		if($do_merge === true && count($temp_for_del) > 0){
			$delete_list = array_merge($delete_list, $temp_for_del);
		}
	}
}

//delete all the duplicates in the list
if(count($delete_list) > 0){
	$rd = mysql_query( sprintf($sql_del, join(",",$delete_list) ));
}

echo count($delete_list) . " duplicates were removed from database.";
echo"</div>";