<?php /* COMPANIES $Id: vw_companies.php 4800 2007-03-06 00:34:46Z merlinyoda $ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

global $search_string,$owner_filter_id,$currentTabId,$currentTabName,$tabbed,$type_filter,$orderby,$orderdir,$AppUI,$myspeed;


$adm_rights=$AppUI->isAdmin();

// load the company types

$types = dPgetSysVal( 'CompanyType' );
// get any records denied from viewing

$obj = new CCompany();
$allowedCompanies = $obj->getAllowedRecords($AppUI->user_id, 'company_id, company_name');

$company_type_filter = $currentTabId;
$companiesType = true;
/*if($currentTabId == 100){
	$company_type_filter = '0';
}else*/
if ($currentTabId == 0){
	$companiesType = false;
}

//Not Defined

if ($currentTabName == $AppUI->_("All Agencies"))
	$companiesType = false;
if ($currentTabName == $AppUI->_("Not Applicable"))
	$company_type_filter = 0;

//UPDATE `companies` SET `company_category`="100" where `company_category`="-1"
//UPDATE `companies` SET `company_category`="7" where `company_category`="0"
	
// retrieve list of records
$q  = new DBQuery;
$q->addTable('companies', 'c');
$q->addQuery('c.company_id, c.company_name, c.company_category,c.company_acronym, c.company_description');

//$q->addJoin('projects', 'p2', 'c.company_id = p2.project_company AND p2.project_status = 7');count(distinct p.project_id) as countp, count(distinct p2.project_id) as inactive,
//$q->addJoin('projects', 'p', 'c.company_id = p.project_company AND p.project_status <> 7');
if (count($allowedCompanies) > 0) { $q->addWhere('c.company_id IN (' . implode(',', array_keys($allowedCompanies)) . ')'); }
if ($companiesType) { $q->addWhere('c.company_category = '.$company_type_filter); }
if ($search_string != "") { $q->addWhere("c.company_name LIKE '%$search_string%'"); }
//if ($owner_filter_id > 0 && !$adm_rights) { $q->addWhere("(c.company_owner = $owner_filter_id  OR c.company_owner <> $owner_filter_id)"); }

if(!$_POST['clear']){
	$pid=$_POST['project_id'];
}else{
	$pid= null;
}
$dop=false;
/* if(is_array($pid)) { 
	if(count($pid) ==1 && $pid[0] > 0){
		$q->addWhere("p.project_id = '".$pid[0]."'");
		$dop=true;
	}elseif (count($pid) > 1){
		$q->addWhere("p.project_id IN (".implode(",",$pid).")");
		$dop=true;
	}
	if($dop){
		$q->addTable("projects",'p');
		$q->addWhere("p.project_company = c.company_id");
	}
}


$q->addGroup('c.company_id'); */
$q->addOrder($orderby.' '.$orderdir);
$rows = $q->loadList();
$types = dPgetSysVal( 'CompanyType' );
?>
<div id="mholder"></div>
<?php
$s = '';
$CR = "\n"; // Why is this needed as a variable?
$plist = array();
$none = true;
//var_dump($rows);
?>
<br /><br /><div class="mtab">
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<tr><td colspan="3" align="right"><a href="?m=companies&a=addedit"  class="btn" style="color:#08245b;font-weight: bold;font-family: Osaka,verdana,Sans-Serif;font-size: 8pt;text-decoration: none;"><?php echo $AppUI->_('Add New Agency')?></a></td></tr>
<?php if(count($rows) > 0){?>
<thead>
  <tr>
  	<th style="width:25%"><?php echo $AppUI->_('Agency Acronym')?></th>
    <th style="width:25%"><?php echo $AppUI->_('Category')?></th>
    <th style="width:50%"><?php echo $AppUI->_('Agency Name');?></th>
  </tr>
</thead>
<?php } ?>
<?php 

foreach ($rows as $row) {
	//echo $row['company_id'];
	$none = false;
	/*$s .= $CR . '<tr>';
	$s .= $CR . '<td><a href="./index.php?m=companies&a=view&company_id=' . $row["company_id"] . '" title="'.$row['company_description'].'">' . $row["company_name"] .'</a></td>';
	$s .= $CR.'<td>'.$row['company_acronym'].'</td>';
	*/
	/* $q = new DBQuery();
	$q->addTable('projects');
	$q->addQuery('project_name');
	$q->addWhere("project_company = '".$row['company_id']."' or project_id in (select task_project from tasks where task_company='".$row['company_id']."')");
	 */
	/* $prs=$q->loadArrayList();
	$plt=""; */
	/*foreach ($prs as $pr){
		$plt == "" ? $plt.=$pr['project_name'] : $plt.="<b>,</b> ".$pr['project_name'];
	}*/;
	/* $proj_list = array_unique(array_keys($prs));
	if(count($proj_list) > 0){
		$plt = '<p style=\'white-space:nowrap;\'>'.join(",<br>",$proj_list).'</p>';
	}
	$pfst = $proj_list[0];
	if(count($proj_list) > 1){
		$pfst.='&nbsp;...';
	} */

	/* $plist[]=array(
		$row['company_id'],
		$row['company_name'],
		($row['company_descrption'] != '' ? mysql_real_escape_string($row['company_descrption'])  : ''),
		$row['company_acronym'],
		$plt,
		$pfst
	); */
	echo '<tr>';
		echo '<td><a href="?m=companies&a=view&company_id='.$row['company_id'].'">'.$row['company_acronym'].'</a></td>';
		echo '<td>'.$types[$row['company_category']].'</td>';
		echo '<td>'.$row['company_name'].'</td>';
	echo '</tr>';
}
if ($none) {
	echo '<tr><td colspan="3">' . $AppUI->_( 'No agencies available' ) . '</td></tr>';
}
?>
</table>
</div>
<?php 
//echo "$s\n";

/* if ($none) {
	echo $CR . '<tr><td colspan="5">' . $AppUI->_( 'No agencies available' ) . '</td></tr><script>var rawlist=[];</script>';
}else{
	echo '<script> var rawlist = '. (count($plist) > 0 ?  json_encode($plist) : '[]').';</script>';
} */

?>
<!-- </tbody>
</table>
</div> -->
<link rel="stylesheet" type="text/css" href="/modules/projects/projects.module.css">
<?php 

//require_once(DP_BASE_DIR.'/modules/public/pa_table.code.php');
/*$myspeed->addJs('$j(document).ready( function (){
		pf.init({
				type: ["string","string","string"],
				cdata: [1,3,4,5],
				lects: ["plain","plain","list"],
				links:{
					0:{
						url:"/?m=companies&a=view&company_id=#0#",
						val:0
					}					
				},
				columns:{
					0:{val:1,link:true,extra:{tag:"data-detail",val:2},class:"verbose cut_head"},
					1:{val:3,link:false},
					//2:{val:4,link:false},
					2:{val:6,extra:{tag:"data-detail",val:4},class:"verbose cut_head"}
				},
				heads:[
					{width:"25%",title:"'.$AppUI->_('Agency Name').'"},
					{width:"10%",title:"'.$AppUI->_('Agency Acronym').'"},
					{width:"45%",title:"'.$AppUI->_('Projects').'"}			
				]
			});
			verboseWindow(".verbose");	
	$(".multiple").multiselect({
		click: function(event,ui){
			var we = $(this).multiselect("widget");
			if(ui.checked == true){				
				var prtype = $(this).hasClass("ptype");
				if(ui.value == -1 ){
					$(this).multiselect("uncheckAll");
					$(":checkbox:first",we).attr("checked",true);					
					$(this).find("option:first").attr("selected",true)
						.end().find("option:gt(0)").attr("selected",false);
				}else{
					$(we).find(":input:first").attr("checked",false);
					$(this).find("option:first").attr("selected",false);
				}
				//$(this).multiselect("refresh");
			}
		}
	});
});','code');
$myspeed->addJs('/modules/projects/projects.module.js','file');
//var_dump($plist);*/
?>