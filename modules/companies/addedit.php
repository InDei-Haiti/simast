<?php /* COMPANIES $Id: addedit.php 4800 2007-03-06 00:34:46Z merlinyoda $ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

$company_id = intval( dPgetParam( $_GET, "company_id", 0 ) );

// check permissions for this company
$perms =& $AppUI->acl();
// If the company exists we need edit permission,
// If it is a new company we need add permission on the module.
if ($company_id)
  $canEdit = $perms->checkModuleItem($m, "edit", $company_id);
else
  $canEdit = $perms->checkModule($m, "add");

if (!$canEdit) {
	$AppUI->redirect( "m=public&a=access_denied" );
}

// load the company types
$types = dPgetSysVal( 'CompanyType' );

// load the record data
$q  = new DBQuery;
$q->addTable('companies');
$q->addQuery('companies.*');
$q->addWhere('companies.company_id = '.$company_id);
$sql = $q->prepare();
$q->clear();

$obj = null;
if (!db_loadObject( $sql, $obj ) && $company_id > 0) {
	// $AppUI->setMsg( '	$qid =& $q->exec(); Company' ); // What is this for?
	$AppUI->setMsg( "invalidID", UI_MSG_ERROR, true );
	$AppUI->redirect();
}

// collect all the users for the company owner list
/* $q  = new DBQuery;
$q->addTable('users','u');
$q->addTable('contacts','con');
$q->addQuery('user_id');
$q->addQuery('CONCAT_WS(", ",contact_last_name,contact_first_name)'); 
$q->addOrder('contact_last_name');
$q->addWhere('u.user_contact = con.contact_id');
$owners = $q->loadHashList(); */

// setup the title block
echo '<br /><br /><div class="card">';
$ttl = $company_id > 0 ? "Edit Agency" : "Add Agency";
$titleBlock = new CTitleBlock( $ttl, /*'handshake.png'*/'', $m, "$m.$a" );
$titleBlock->addCrumb( "?m=companies", "agencies list" );
if ($company_id != 0)
  $titleBlock->addCrumb( "?m=companies&a=view&company_id=$company_id", "View this agency" );
$titleBlock->show();

//fetch list of countries
/* $q = new DBQuery();
$q->addTable('administrative_regions');
$q->addQuery('region_id, region_name');
$q->addWhere('region_parent = 0');
$country_list = arrayMerge(array(-1 => 'all countries'), $q->loadHashList()); */

?>

<script language="javascript">
function submitIt() {
	var form = document.changeclient;
	var ccat = $j("#cfcat").val();
	if (form.company_name.value.length < 3) {
		alert( "<?php echo $AppUI->_('companyValidName', UI_OUTPUT_JS);?>" );
		form.company_name.focus();
	}else if(form.company_acronym.value.length < 3){
		alert( "Please enter valid Agency Acronym" );
		form.company_acronym.focus();
	}else if(ccat < 0){
		alert("Please select valid Category!");
		$j("#cfcat").focus();
		return false;
	}else {
		form.submit();
	}
}

function testURL( x ) {
	var test = "document.changeclient.company_primary_url.value";
	test = eval(test);
	if (test.length > 6) {
		newwin = window.open( "http://" + test, 'newwin', '' );
	}
}
</script>
 
<form name="changeclient" action="?m=companies" method="post" enctype="multipart/form-data">
	<input type="hidden" name="dosql" value="do_company_aed" />
	<input type="hidden" name="company_id" value="<?php echo $company_id;?>" />
<div class=""mtab>

<table cellspacing="1" cellpadding="1" border="0" width='100%' class="std">


<tr>
<td>


<table class="tbl tablesorter" id="tfor_sort">
	<tr>
		<td align="right"><?php echo $AppUI->_('Agency Name');?>&nbsp;*:</td>
		<td>
			<input type="text" class="form-control" name="company_name" value="<?php echo dPformSafe(@$obj->company_name);?>" size="50" maxlength="255" />
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo $AppUI->_('Agency Acronym');?>&nbsp;*:</td>
		<td>
			<input type="text" class="form-control" name="company_acronym" value="<?php echo dPformSafe(@$obj->company_acronym);?>" size="50" maxlength="255" />
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo $AppUI->_('Category');?>&nbsp;*:</td>
		<td>
		<?php 
			echo  arraySelect(dPgetSysVal("CompanyType"), 'company_category', 
                              "style='width:255px;' id='cfcat' class='form-control'" , $obj->company_category ? $obj->company_category : -1, true)
		?> 
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo $AppUI->_('Email');?>:</td>
		<td>
			<input type="text" class="form-control" name="company_email" value="<?php echo dPformSafe(@$obj->company_email);?>" size="30" maxlength="255" />
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo $AppUI->_('Phone');?>:</td>
		<td>
			<input type="text" class="form-control" name="company_phone1" value="<?php echo dPformSafe(@$obj->company_phone1);?>" maxlength="30" />
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo $AppUI->_('Mobile Phone');?>:</td>
		<td>
			<input type="text" class="form-control" name="company_phone2" value="<?php echo dPformSafe(@$obj->company_phone2);?>" maxlength="50" />
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo $AppUI->_('Fax');?>:</td>
		<td>
			<input type="text" class="form-control" name="company_fax" value="<?php echo dPformSafe(@$obj->company_fax);?>" maxlength="30" />
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo $AppUI->_('Logo');?>:</td>
		<td>
			<input type="file" class="form-control" name="company_logo" maxlength="30" />
		</td>
	</tr>
	<tr>
		<td colspan=2 align="center">
			<img src="images/shim.gif" width="50" height="1" /><?php echo $AppUI->_('Address');?><br />
			<hr width="500" align="center" size=1 />
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo $AppUI->_('Address');?>1:</td>
		<td><input type="text" class="form-control" name="company_address1" value="<?php echo dPformSafe(@$obj->company_address1);?>" size=50 maxlength="255" /></td>
	</tr>
	<tr>
		<td align="right"><?php echo $AppUI->_('Address');?>2:</td>
		<td><input type="text" class="form-control" name="company_address2" value="<?php echo dPformSafe(@$obj->company_address2);?>" size=50 maxlength="255" /></td>
	</tr>
	<tr>
		<td align="right"><?php echo $AppUI->_('Postal Code');?>:</td>
		<td><input type="text" class="form-control" name="company_zip" value="<?php echo dPformSafe(@$obj->company_zip);?>" maxlength="15" /></td>
	</tr>
	<tr>
		<td align="right">
			URL http://<A name="x"></a></td><td><input type="text" class="form-control" value="<?php echo dPformSafe(@$obj->company_primary_url);?>" name="company_primary_url" size="50" maxlength="255" />
			<a href="#x" onClick="testURL('CompanyURLOne')">[<?php echo $AppUI->_('test');?>]</a>
		</td>
	</tr>
	
	
	<tr>
		<td align="right" valign=top><?php echo $AppUI->_('Description');?>:</td>
		<td align="left">
			<textarea cols="70" rows="10" class="form-control" name="company_description"><?php echo @$obj->company_description;?></textarea>
		</td>
	</tr>
</table>


</td>
	<td align='left'>
		<?php
 			require_once($AppUI->getSystemClass( 'CustomFields' ));
 			$custom_fields = New CustomFields( $m, $a, $obj->company_id, "edit" );
 			$custom_fields->printHTML();
		?>		
	</td>
</tr>
<tr>
	<td><input type="button" value="<?php echo $AppUI->_('back');?>" class="button ce pi ahr" onClick="javascript:history.back(-1);" /></td>
	<td align="right"><input type="button" value="<?php echo $AppUI->_('submit');?>" class="button ce pi ahr" onClick="submitIt()" /></td>
</tr>

</table>
</div>
</form>
<?php echo "</div>";?>