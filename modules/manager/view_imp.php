<?php /* FILES $Id: addedit.php,v 1.44.4.2 2006/02/17 08:37:48 cyberhorse Exp $ */

global $AppUI,$baseDir,$dPconfig,$m;

require_once($baseDir . '/modules/files/files.class.php');

//require_once( "$baseDir/lib/Excel/Reader.php" ) ;
$file_id = intval( dPgetParam( $_GET, 'file_id', 0 ) );
$preserve = $dPconfig['files_ci_preserve_attr'];


// check permissions for this record
$perms =& $AppUI->acl();
$canEdit = $perms->checkModuleItem( $m, 'edit', $file_id );
if (!$canEdit) {
	$AppUI->redirect( "m=public&a=access_denied" );
}

$canAdmin = $perms->checkModule('system', 'edit');

$file_parent = intval( dPgetParam( $_GET, 'file_parent', 0 ) );

$q =& new DBQuery;

// check if this record has dependencies to prevent deletion
$msg = '';
$obj = new CFile();
$canDelete = $obj->canDelete( $msg, $file_id );

// load the record data
// $obj = null;
if ($file_id > 0 && ! $obj->load($file_id)) {
	$AppUI->setMsg( 'File' );
	$AppUI->setMsg( "invalidID", UI_MSG_ERROR, true );
	$AppUI->redirect();
}

if ($obj->file_checkout != $AppUI->user_id)
        $ci = false;

if (! $canAdmin)
	$canAdmin = $obj->canAdmin();

if ($obj->file_checkout == 'final' && ! $canAdmin) {
	$AppUI->redirect('m=public&a=access_denied');
}
if (isset( $_GET['tab']))
{
  $AppUI->setState('FileVwTab', $_GET['tab']);
}
$tab = $AppUI->getState('FileVwTab') !== NULL ? $AppUI->getState('FileVwTab') : 0;
//open spreadsheet reader
// ExcelFile($filename, $encoding);
//$data = new Spreadsheet_Excel_Reader();


// Set output Encoding.
//$data->setOutputEncoding('CP1251');

/*if ($file_id)
   $data->read("$baseDir/files/$obj->file_real_filename");*/
// setup the title block
$ttl =  "View File";
$titleBlock = new CTitleBlock( $ttl, '', $m, "$m.$a" );
$titleBlock->addCrumb( "?m=manager&part=importer", "files list" );
if ($canDelete && $file_id > 0 && !$ci) {
	$titleBlock->addCrumbDelete( 'delete file', $canDelete, $msg );
}
$titleBlock->show();
$superKey = uniqid();
?>
<script language="javascript">
function submitIt() 
{
	var f = document.uploadFrm;
	f.submit();
}
function delIt() {
	if (confirm( "Delete this file?" )) {
		var f = document.delFrm;
		f.del.value='1';
		f.submit();
	}
}

function finalCI(){
        var f = document.uploadFrm;
        if (f.final_ci.value = '1')
        {
                f.file_checkout.value = 'final';
                f.file_co_reason.value = 'Final Version';
        }
        else
        {
                f.file_checkout.value = '';
                f.file_co_reason.value = '';
        }
}

function launchAll(but){
	$j(but).attr("disabled",true);
	$j("#load_pgs").show();
	$j('#upform').submit();
	monitorPs("<?php echo $superKey;?>","pcnt");
}
</script>

<form name="delFrm" action="?m=manager" method="post">
	<input type="hidden" name="dosql" value="do_file_imp_aed" />
	<input type="hidden" name="file_id" value="<?php echo $file_id;?>" />
	<input type="hidden" name="del" value="" />
</form>

<form name="uploadFrm" id="upform" action="?m=manager" method="post">
	<input type="hidden" name="dosql" value="do_file_imp_upload" />
	<input type="hidden" name="import" value="1" />
	<input type="hidden" name="file_id" value="<?php echo $file_id;?>" />
	<input type="hidden" name="del" value="" />
	<input type="hidden" name="file_version_id" value="<?php echo $obj->file_version_id;?>" />
	<input type="hidden" name="urkey" value="<?php echo $superKey?>">
	
<table width="100%" border="0" cellpadding="3" cellspacing="3" class="std">

<tr>
	<td width="100%" valign="top" align="left">
		<table cellspacing="1" cellpadding="2" width="60%">
	<?php if ($file_id) { ?>
		<tr>
			<td align="left" nowrap="nowrap"><?php echo $AppUI->_( 'File Name' );?>:</td>
			<td align="left" class="hilite"><?php echo strlen($obj->file_name)== 0 ? "n/a" : $obj->file_name;?></td>
			<td>
				<a href="./fileviewer.php?file_id=<?php echo $obj->file_id;?>"><?php echo $AppUI->_( 'download' );?></a>
			</td>
		</tr>
		<tr valign="top">
			<td align="left" nowrap="nowrap"><?php echo $AppUI->_( 'Type' );?>:</td>
			<td align="left" class="hilite"><?php echo $obj->file_type;?></td>
		</tr>
		<tr>
			<td align="left" nowrap="nowrap"><?php echo $AppUI->_( 'Size' );?>:</td>
			<td align="left" class="hilite"><?php echo bit2text($obj->file_size);?></td>
		</tr>
		<tr>
			<td align="left" nowrap="nowrap"><?php echo $AppUI->_( 'Uploaded By' );?>:</td>
			<td align="left" class="hilite"><?php echo $obj->getOwner();?></td>
		</tr>
	<?php } 
		echo file_show_attr();

		?>
		<tr>
			<td align="left" nowrap="nowrap" colspan="2">
			<?php if($obj->file_imported == 0 ){	?>
				<input class="button" type="button" value = "<?php echo $AppUI->_( 'import file' );?>" onclick="launchAll(this);" />
				&nbsp;&nbsp;
				<div id="load_pgs" style="display:none;">Progress&nbsp;<span id="pcnt" style="font-weight: 800;">0</span>&nbsp;%</div>
			<?php }else{
				echo 'File already imported';
			}
			?>
			</td>
		</tr>		
		</table>
	</td>
</tr>
</form>
</table>

<?php 
function file_show_attr()
{
	
	global $AppUI, $obj, $ci, $canAdmin,$preserve;
	$file_types = dPgetSysVal("FileType");
	$str_out = "<tr>" .
             '<td align="left" nowrap="nowrap">' .
             $AppUI->_( 'Version' ) . ":</td>";

	$str_out .= '<td align="left">';
	$str_out .= $file_types[$obj->file_category];
	$str_out .= '</td>';
	$select_disabled=' ';  



	return ($str_out);
}

		$moddir = $dPconfig['root_dir'] . '/modules/files/';
		$tabBox = new CTabBox( "?m=files&a=view&file_id=$file_id", "", $tab );
		$sheetcount = 0;
		/*for ($sheetcount=0; $sheetcount < count($data->sheets);$sheetcount++)
		{
			$tabBox->add( $moddir . 'vw_file', "Worksheet $sheetcount" );
		}*/
		$tabBox->show();
?>
