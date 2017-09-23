<?php
GLOBAL $AppUI, $deny1, $canRead, $canEdit, $canAdmin;

global $currentTabId;
global $currentTabName;
global $tabbed;

$page = dPgetParam( $_GET, "page", 1);
if (!isset($showProject))
$showProject = true;

//$xpg_pagesize = 30;
//$xpg_min = $xpg_pagesize * ($page - 1); // This is where we start our record set from
require_once $AppUI->getModuleClass( 'files' );


$df = $AppUI->getPref('SHDATEFORMAT');
$tf = $AppUI->getPref('TIMEFORMAT');

$file_types = dPgetSysVal("FileType");

// SQL text for count the total recs from the selected option
$q = new DBQuery;
$q->addQuery('count(file_id)');
$q->addTable('files', 'f');
if ($catsql) $q->addWhere($catsql);
$q->addGroup("file_version_id");

// SETUP FOR FILE LIST
$q2 = new DBQuery;
/*
,
'max(f.file_id) as  latest_id',
'count(f.file_version) as file_versions',
'round(max(f.file_version),2) as file_lastversion'
*/

//$q2->addQuery('f.*');
$q2->addQuery("file_id, file_name, file_size,file_date");
$q2->addTable('files', 'f');

if ($catsql) $q2->addWhere($catsql);
//$q2->setLimit($xpg_pagesize, $xpg_min);
$q2->addWhere('file_mode="export"');
//$q2->addGroup('file_version_id DESC');
$q2->addOrder('file_date asc');
/*$q3 = new DBQuery;
$q3->addQuery("file_id, file_name, file_size,file_date");
$q3->addTable('files');
$q3->addWhere('file_mode="export"');
*/

$files = array();
$file_versions = array();
if ($canRead) {

	$files = $q2->loadList();
	//$file_versions = $q3->loadHashList('file_id');
}
// counts total recs from selection
//$xpg_totalrecs = count($q->loadList());

// How many pages are we dealing with here ??
//$xpg_total_pages = ($xpg_totalrecs > $xpg_pagesize) ? ceil($xpg_totalrecs / $xpg_pagesize) : 1;

//shownavbar($xpg_totalrecs, $xpg_pagesize, $xpg_total_pages, $page);

?>
<script type="text/JavaScript">
function expand(id){
	$j("#"+id).toggle();
}
</script>
<div style="color: green; font-weight: 800;font-size: 9pt;"><?php 
if($_SESSION['export_msg'] != ''){
	echo $_SESSION['export_msg'];
	$_SESSION['export_msg']=null;
}
?></div>

<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl" id="exports">
<tr>
	
	
	<th nowrap="nowrap"><?php echo $AppUI->_( 'File Name' );?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_( 'Size' );?></th>	
	<th nowrap="nowrap"><?php echo $AppUI->_( 'Date' );?></th>
	<th nowrap="nowrap" width="25"><?php echo $AppUI->_( 'Delete' );?></th>
</tr>
<?php
$fp=-1;
$file_date = new CDate();

$id = 0;
foreach ($files as $file_row)
{
	//$latest_file = $file_versions[$file_row['latest_id']];
	$latest_file=$file_row;
	$file_date = new CDate( $latest_file['file_date'] );

?>
<tr>       
	<td >
		<?php 
		$fnamelen = 32;
		$filename = $latest_file['file_name'];
		if (strlen($latest_file['file_name']) > $fnamelen+9)
		{
			$ext = substr($filename, strrpos($filename, '.')+1);
			$filename = substr($filename, 0, $fnamelen);
			$filename .= '[...].' . $ext;
		}
		echo "<a href=\"?m=manager&a=view_imp&file_id={$latest_file['file_id']}\" title=\"{$latest_file['file_description']}\">$filename</a>";
		//	{$latest_file['file_name']}
		?>
	</td>		
	<td nowrap="nowrap" align="right"><?php echo file_size(intval($latest_file["file_size"]));?></td>	
	<td nowrap="nowrap" align="right"><?php echo $file_date->format( "$df $tf" );?></td>
	<td align="center"><div class="fbutton delbutt exterm" data-fid="<?php echo $latest_file['file_id'] ?>"></td>
</tr>
<?php 
echo $hidden_table;
$hidden_table = '';
}?>
</table>
