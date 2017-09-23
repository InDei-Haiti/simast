<?php
global $AppUI, $client_id, $obj,$baseDir;

require_once $AppUI->getModuleClass('wizard');
if(isset($_GET['tab'])){
	$_SESSION['selected_tab']=$_GET['fid']=$_SESSION['wiz_tab'][(int)$_GET['tab']];
}else{
	$_GET['tab']=array_search($_SESSION['selected_tab'],$_SESSION['wiz_tab']);
	$_GET['fid']=$_SESSION['wiz_tab'][(int)$_GET['tab']]=$_SESSION['selected_tab'];
}

$q = new DBQuery();
$q->addTable('activity_queries');
$q->addQuery('prepare,list,headers');
$q->addWhere('id='.$_GET['fid']);
$activity_queries = $q->loadList();
$sql = gzdecode($activity_queries[0]['prepare']);
$sql = stripslashes($sql);
$sql = stripslashes($sql);
$sql = stripslashes($sql);
$sql = stripslashes($sql);
$sql = stripslashes($sql);
$sql = stripslashes($sql);

$headers = gzdecode($activity_queries[0]['headers']);
$list = gzdecode($activity_queries[0]['list']);
eval ('$headers=' . $headers . ';');

$tabsql = explode('FROM', $sql);
$querypart = $tabsql[1];
$count_request =db_loadResult('Select count(*) FROM '.$querypart);

$pageSub = "";
$pageURL = 'http';
if ($_SERVER ["HTTPS"] == "on") {
    $pageURL .= "s";
}
$pageURL .= "://";
if ($_SERVER ["SERVER_PORT"] != "80") {
    $pageURL .= $_SERVER ["SERVER_NAME"] . ":" . $_SERVER ["SERVER_PORT"]; // .$_SERVER["REQUEST_URI"];
} else {
    $pageURL .= $_SERVER ["SERVER_NAME"]; // .$_SERVER["REQUEST_URI"];
}

$queryString = $_SERVER ["REQUEST_URI"];
$queryString = explode ( "?", $queryString );
$pageURL .= $queryString [0] . "?";
$pageSub .= $queryString [0] . "?";
if (isset ( $queryString [1] )) {
    $params = explode ( "&", $queryString [1] );
    foreach ( $params as $x => $param ) {
        $tabparam = explode ( "=", $param, 2 );
        if ($tabparam [0] != 'p') {
            if ($tabparam [0] != 'token') {
                if (isset ( $tabparam [1] )) {
                    $pageURL .= "&" . $tabparam [0] . '=' . $tabparam [1];
                    $pageSub .= "&" . $tabparam [0] . '=' . $tabparam [1];
                }
            }
        }
    }
}

$page = 1;
if (isset ( $_GET ['p'] ) && is_numeric ( $_GET ['p'] )) {
    $page = intval ( $_GET ['p'] );

    if ($page < 1) {
        $page = 1;
    }
}

$epp = ( int ) $_GET ['epp'];

if (! $epp) {
    $epp = 100;
}



$count = $count_request;
$start = $page * $epp - $epp;
// $end = $start + $epp;
$nbPages = ceil ( $count / $epp );
$sql = $sql .' limit '.$start.', '.$epp;
$res = db_exec($sql);
$rhtml = '';

if($page==1){
    $prev = '<span style="color:#c0c0c0">Prev</span>';
    if($page<$nbPages)
        $next = '<a href="'.$pageURL."&token=".$token."&p=".($page + 1).'&epp='.$epp.'"><span class="ce pi ahr">Next</span></a>';
    else
        $next = '<span style="color:#c0c0c0">Next</span>';
}elseif($page>1){
    //$pageURL .= "&p=".($page - 1);
    $prev = '<a href="'.$pageURL."&token=".$token."&p=".($page - 1).'&epp='.$epp.'"><span class="ce pi ahr">Prev</span></a>';
    if($page<$nbPages)
        $next = '<a href="'.$pageURL."&token=".$token."&p=".($page + 1).'&epp='.$epp.'"><span class="ce pi ahr">Next</span></a>';
    else
        $next = '<span style="color:#c0c0c0">Next</span>';
}
//if($res)
//$currentNbr = $start + mysql_num_rows($res);
$currentNbr = 1;

$selected1 = "";
$selected2 = "";
$selected3 = "";
$selected4 = "";
if($epp===100){
    $selected1 = " selected=selected";
}
if($epp===250){
    $selected2 = " selected=selected";
}
if($epp===500){
    $selected3 = " selected=selected";
}
/* if($epp===2000){
 $selected4 = " selected=selected";
 } */
if($count>$start+$epp){
    $val = $start+$epp;
}else{
    $val = $count;
}
$tokenUrl = '';
if($token){
    $tokenUrl = "&token=".$token;
}
$info = "Total Records: ".$count." Viewing Records: ".($start+1)."-".$val." <span style='color: #08245b;'><b>Rows per page</b></span> <select name='epp' onchange='window.location.href = window.location.href+\"".$tokenUrl."\"+\"&epp=\"+this.value'><option ".$selected1.">100</option><option ".$selected2.">250</option><option ".$selected3.">500</option></select>";

$rhp = '<table style="margin-left:-3px;width:100%"><tr><td>'.$prev.'</td><td><!-- <div style="margin-top:35px;">{<b style="color:#008000">'.$currentNbr.'/'.$count.'</b>}</div>--></td><td>'.$next.'</td><td><div style="">'.$info.'</div></td></tr></table>';
//$rh .= $rhp;
//$rh .= '</div>';
$rf = '<div align="left" style="background:white;padding: 10px">';
$rf .= $rhp;
$rf .= '</div>';


$rhtml .= '</div>';
$rhtml .= $rf;

?>
<div class="mtab">
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl tablesorter" id="tbl">
	<thead>
		<tr>
			<?php
				foreach ( $headers as $hcode => $hcodeArray ) {
					if($hcodeArray['type']==='entry_date')
						$hcodeArray['type'] = 'date';
					$point = '';
					if(strlen($AppUI->_($hcodeArray['title']))>50)
						$point = '...';
					echo '<th id="head_' . $ind . '" data-thid="' . $ind . '" data-form="'.$hcodeArray['form'].'" data-type="'.$hcodeArray['type'].'" data-field="'.$hcodeArray['fld'].'" data-title='.$source.' data-sys="'.$hcodeArray['sysv'].'"  class="head ' . $addcl . '" data-part="'.$data_part.'">' . substr($AppUI->_($hcodeArray['title']), 0,49).$point . '<div class="head_menu"></div></th>' . "\n";

				 }
             ?>
		</tr>
	</thead>
    <tbody>
    <?php
    while ( $rowdata = db_fetch_assoc ( $res ) ) {
        $wz = new Wizard('import');
        $wz->loadFormInfo($headers['id']['form']);
        echo '<tr>';
        foreach ( $headers as $hcode => $hcodeArray ) {
            $forView = $rowdata[$hcode];

            if($hcode=='id'){
                $forView = $rowdata[$hcodeArray['table'].'_id'];
                $forView = '<a href="?m=wizard&a=form_use&fid='.$hcodeArray['form'].'&idIns='.$forView.'&todo=view&teaser=1&rtable=1&tab=0">View</a>';
            }
            if(isset($hcodeArray['sysv'])){
                $forView = $wz->getValues($hcodeArray['type'],$hcodeArray['sysv'],$forView);
            }
            if($hcodeArray['sysv'] != 'SysCommunalSection'){
                $forView = stripslashes($forView);
                $forView = stripslashes($forView);
                $forView = stripslashes($forView);
                $forView = stripslashes($forView);
                $forView = stripslashes($forView);
                $forView = stripslashes($forView);
            }else{
            }
            echo '<td>'.$forView.'</td>';
        }
        echo '</tr>';
    }
    ?>
    </tbody>
</table>
</div>
<?php echo $rhtml;?>