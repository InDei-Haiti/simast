<?php
$client_id = intval ( dPgetParam ( $_GET, "client_id", 0 ) );
/* $client_type = intval( dPgetParam( $_GET, "client_type", 3 )); */
$activity_id = intval ( dPgetParam ( $_GET, "activity_id", 0 ) );

if ($contact_unique_update == 0)
	$contact_unique_update = uniqid ( "" );

$perms = & $AppUI->acl ();

if ($client_id)
	$canEdit = $perms->checkModuleItem ( $m, "edit", $client_id );
else
	$canEdit = $perms->checkModule ( $m, "add" );

if (! $canEdit) {
	$AppUI->redirect ( "m=public&a=access_denied" );
}

$q = new DBQuery ();
$q->addTable ( 'clients' );
$q->addQuery ( 'clients.*' );
$q->addWhere ( 'clients.client_id = ' . $client_id );
$sql = $q->prepare ();
$q->clear ();





$obj = new CClient ();

$q = new DBQuery ();
$q->addTable ( 'administration_com', 'adm_com' );
$q->addQuery ( 'adm_com.administration_com_code, adm_com.administration_com_name, adm_com.administration_com_code_arr, adm_com.administration_com_code_dep' );
$q->addOrder ( 'adm_com.administration_com_name' );
$administration_com1 = $q->loadHashList ();
$administration_com = arrayMerge ( array (
		0 => ''
), $administration_com1 );

$administration_section = array ();
foreach ( $administration_com1 as $k => $v ) {
	$q = new DBQuery ();
	$q->addTable ( 'administration_section', 'adm_sec' );
	$q->addQuery ( 'adm_sec.administration_section_code, adm_sec.administration_section_name, adm_sec.administration_section_code_com, adm_sec.administration_section_code_arr, adm_sec.administration_section_code_dep' );
	$q->addWhere ( 'adm_sec.administration_section_code_com=' . $k );
	$administration_section [$k] = $q->loadHashList ();
}

if (! db_loadObject ( $sql, $obj ) && ($client_id > 0)) {
	$AppUI->setMsg ( "invalidID", UI_MSG_ERROR, true );
	$AppUI->redirect ();
}
//if($client_id)
	//$activity_id = $obj->client_activities;
	/* if ($obj->getActivity ())
		$activity_id = implode ( ',', $obj->getActivity () ); */
	// load all sales reps
	/*
 * $q = new DBQuery;
 * $q->addTable('contacts', 'c');
 * $q->addQuery('c.contact_id');
 * $q->addQuery('CONCAT_WS(", ",c.contact_last_name,c.contact_first_name)');
 * $q->innerJoin('client_contacts', 'b', 'b.client_contacts_contact_id = c.contact_id');
 * $q->addWhere('b.client_contacts_contact_type = 13');
 * $q->addOrder('c.contact_first_name');
 */
	
// load contacts
	/*
 * $chw_contacts = arrayMerge(array(0=> '-Select CHW -'),$q->loadHashList());
 * $q->clear();
 * $q->addTable('contacts', 'c');
 * $q->addQuery('c.contact_id');
 * $q->addQuery('CONCAT_WS(", ",c.contact_last_name,c.contact_first_name)');
 * $q->innerJoin('client_contacts', 'b', 'b.client_contacts_contact_id = c.contact_id');
 * $q->addWhere('b.client_contacts_contact_type = 14');
 * $q->addOrder('c.contact_first_name');
 *
 *
 * $shw_contacts = arrayMerge(array(0=> '-Select SHW -'),$q->loadHashList());
 */
	
// load status stuff
$healthstatus = arrayMerge ( array (
		- 1 => '-Select Beneficiery Health Status-' 
), dPgetSysVal ( 'CaregiverHealthStatus' ) );
// load priority stuff
$priority = arrayMerge ( array (
		- 1 => '-Select Current Client Priority-' 
), dPgetSysVal ( 'ClientPriority' ) );
$education_level = arrayMerge ( array (
		- 1 => '-Select Beneficiery Education Level-' 
), dPgetSysVal ( 'EducationLevel' ) );
// load cities
$citiesArray = arrayMerge ( array (
		- 1 => '-Select City-' 
), dPgetSysVal ( 'ClientCities' ) );
// load clinics
$q->clear ();
$q->addTable ( 'activity', 'a' );
$q->addQuery ( 'a.activity_id, a.activity_name' );
$q->addOrder ( 'a.activity_name' );

$activities = $q->loadHashList ();

$boolTypes = dPgetSysVal ( 'YesNo' );
$birthPlaces = dPgetSysVal ( 'BirthPlaceType' );
$birthTypes = dPgetSysVal ( 'BirthType' );
$ageTypes = dPgetSysVal ( 'AgeType' );
$awareStages = dPgetSysVal ( 'StatusAwareType' );
/*
 * $q->clear();
 * $q->addTable('client_status');
 * $q->addQuery('client_status_id');
 * $q->addQuery('client_status_desc');
 * $q->addOrder('client_status_desc');
 *
 *
 * //load priorities
 * $q->clear();
 * $q->addTable('client_priority');
 * $q->addQuery('client_priority_id');
 * $q->addQuery('client_priority_desc');
 * $q->addOrder('client_priority_id');
 * $priority = arrayMerge(array(0=>'-Select Client Class-'), $q->loadHashList());
 * //load company types
 * $types = dPgetSysVal('ClientStatus');
 * $type = $types[$client_type];
 *
 * //load cities
 * $citiesArray = arrayMerge(array(0=>'-Select City-'), dPgetSysVal('ClientCities'));
 */
// $ttl = "$type :: ";
$ttl .= $client_id > 0 ? "Edit Beneficiery" : "New Beneficiery";

$titleBlock = new CTitleBlock ( $ttl, '', $m, "$m.$a" );
$titleBlock->addCrumb ( "?m=clients", "Beneficieries" );
if ($client_id != 0)
	$titleBlock->addCrumb ( "?m=clients&a=view&client_id=$client_id", "View" );
$titleBlock->show ();

?>
<script language="javascript">
var selected_contacts_id = "<?php echo $obj->main_contacts; ?>";
var contact_unique_update = "<?php echo $contact_unique_update; ?>";
var client_id = '<?php echo $obj->client_id;?>';
var client_name_msg = "<?php echo $AppUI->_('Please enter a name for the client');?>";


var calendarField = '';
var calWin = null;


function popCalendar( field ){
	calendarField = field;
	idate = eval( 'document.editFrm.log_' + field + '.value' );
	window.open( 'index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'top=250,left=250,width=251, height=220, scollbars=false, resizable' );
}

function setCalendar( idate, fdate ) 
{
	fld_date = eval( 'document.editFrm.log_' + calendarField );
	fld_fdate = eval( 'document.editFrm.client_' + calendarField );
	fld_date.value = idate;
	fld_fdate.value = fdate;
}

function checkDate()
{
           if (document.frmDate.log_start_date.value == "" || document.frmDate.log_end_date.value== ""){
                alert("<?php echo $AppUI->_('You must fill fields', UI_OUTPUT_JS) ?>");
                return false;
           } 
           return true;
}

function popClinic() {
//        window.open('./index.php?m=public&a=selector&dialog=1&callback=setCompany&table=companies', 'company','left=50,top=50,height=250,width=400,resizable');
	window.open("./index.php?m=clients&a=select_client_clinic&dialog=1&table_name=clinics&clinic_id=<?php echo $clinic_detail['clinic_id'];?>", "clinic", "left=50,top=50,height=250,width=400,resizable");
}

function setClinic( key, val ){
	var f = document.editFrm;
 	if (val != '') {
    	f.contact_company.value = key;
			f.client_clinic_name.value = val;
    	if ( window.clinic_id != key )
		{
    		f.contact_department.value = "";
				f.contact_department_name.value = "";
    	}
    	window.clinic_id = key;
    	window.clinic_value = val;
    }
}
var jsonsec = <?php echo json_encode($administration_section)?>;
</script>
<?php
$date_reg = date ( "Y-m-d" );
$entry_date = intval ( $date_reg ) ? new CDate ( dPgetParam ( $_REQUEST, "client_entry_date", date ( "Y-m-d" ) ) ) : null;
$df = $AppUI->getPref ( 'SHDATEFORMAT' );

//if ($ver) {
	if (@$obj->client_administration_section) {
		$q->addTable ( 'administration_section', 'adm_sec' );
		$q->addQuery ( 'adm_sec.administration_section_code_com' );
		// cho @$row->activity_administration_section;
		$q->addWhere ( 'administration_section_code in (' . @$obj->client_administration_section . ')' );
		$client_administration_com = implode ( ',', array_unique ( $q->loadColumns () ) );
	}
//}


?>
<form name="editFrm"
	action="?m=clients&client_id=<?php echo $client_id; ?>" method="post">
	<input type="hidden" name="dosql" value="do_newclient_aed" /> <input
		type="hidden" name="client[client_id]"
		value="<?php echo $client_id; ?>" />
	<!-- <input type="hidden" name="client[client_type]" value="<?php echo $client_type; ?>" />
   <input type="hidden" name="client[client_date_entered]" value="<?php echo date('Y-m-d h:i:s A'); ?>" /> -->
	<table cellspacing="1" cellpadding="1" border="0" width="100%"
		class="std">
		<td align="left">
		
		<tr>
			<td>
				<table>
					<!-- <tr>
						<td align="left"><?php echo $AppUI->_('Activity');?>: <strong style="color: red">*</strong></td>
						<td>
					<?php
					
					//echo arraySelectCheckboxFlat( $activities, 'client[client_activities]', 'size="1" class="text"', $activity_id ? $activity_id : - 1,false);
					// arraySelect( $clinics, 'client[client_activity]', 'size="1" class="text"', $activity_id ? $activity_id : -1);					?>        
				</td>
					</tr> -->
					<tr>
						<td align="left"><?php echo $AppUI->_('First Name');?>: <strong style="color: red">*</strong></td>
						<td><input type="text" class="text"
							name="client[client_first_name]" id="client_first_name"
							value="<?php echo dPformSafe(@$obj->client_first_name);?>"
							size="50" maxlength="255" /></td>
					</tr>

					<tr>
						<td align="left"><?php echo $AppUI->_('Last Name');?>: <strong style="color: red">*</strong></td>
						<td><input type="text" class="text"
							name="client[client_last_name]" id="client_last_name"
							value="<?php echo dPformSafe(@$obj->client_last_name);?>"
							size="50" maxlength="255" /></td>
					</tr>

					<tr>
						<td align="left"><?php echo $AppUI->_('Nickname');?>: </td>
						<td><input type="text" class="text" name="client[client_nickname]"
							id="client_nickname"
							value="<?php echo dPformSafe(@$obj->client_nickname);?>"
							size="50" maxlength="255" /></td>
					</tr>
					<tr>
						<td align="left"><?php echo $AppUI->_('Gender');?>: <strong style="color: red">*</strong></td>
						<td align="left"><?php echo arraySelectRadio(dPgetSysVal('GenderType'), "client[client_gender]", 'onclick=toggleButtons() class="genderOpts"', @$obj->client_gender ? @$obj->client_gender : -1, $identifiers ); ?></td>
					</tr>
					<tr>
						<td align="left" nowrap><?php echo $AppUI->_('Birthday');?>: <strong style="color: red">*</strong></td>
						<td><input type="date" name="client[client_birthday]"
							value="<?php echo @$obj->client_birthday ;?>" class="text" /></td>
					</tr>
					<tr>
						<td align="left" nowrap><?php echo $AppUI->_('Place of Birth');?>: </td>
						<td><input type="text" name="client[client_place_of_birth]"
							class="text" value="<?php echo @$obj->client_place_of_birth?>" />
						</td>
					</tr>
					<tr>
						<td align="left" nowrap><?php echo $AppUI->_('Adress');?>: </td>
						<td><input type="text" name="client[client_address]"
							class="text" value="<?php echo @$obj->client_address?>" />
						</td>
					</tr>
					<tr>
						<td align="left"><?php echo $AppUI->_('Communes');?>:</td>
						<td>
							<?php
								echo arraySelect ( $administration_com, 'administration_com[administration_com_name]', 'class="text chosen" id="administration_com" size=1', $client_administration_com ? $client_administration_com : '-1' );
							?>
						</td>
					</tr>
					<tr>
						<td align="left"><?php echo $AppUI->_('Communal section');?>:</td>
						<td>
							<?php
							$ar = array (
									0 => '' 
							);
							$defaultsection = array();
							if(activity_id && @$obj->client_administration_section){
								$tabtemp = explode(',', @$obj->client_administration_section);
								for($i=0;$i<count($tabtemp);$i++)
									$defaultsection[] = $administration_section [$tabtemp[$i]];
							}
							echo arraySelect ($defaultsection , 'client[client_administration_section]', 'class="text chosen" id="administration_sect" size=1', @$obj->client_administration_section ? @$obj->client_administration_section : '-1' );
							?>
						</td>
					</tr>
					<tr>
						<td align="left" nowrap><?php echo $AppUI->_('Phone');?> 1: </td>
						<td><input type="text" name="client[client_phone1]"
							class="text" value="<?php echo @$obj->client_phone1?>" />
						</td>
					</tr>
					<tr>
						<td align="left" nowrap><?php echo $AppUI->_('Phone');?> 2: </td>
						<td><input type="text" name="client[client_phone2]"
							class="text" value="<?php echo @$obj->client_phone2?>" />
						</td>
					</tr>
					<tr>
						<td align="left" nowrap><?php echo $AppUI->_('Phone');?> 3: </td>
						<td><input type="text" name="client[client_phone3]"
							class="text" value="<?php echo @$obj->client_phone3?>" />
						</td>
					</tr>
					<tr>
						<td align="left"><?php echo $AppUI->_('Marital Status');?>: <strong style="color: red">*</strong></td>
						<td align="left"><?php echo arraySelectRadio(dPgetSysVal('MaritalStatus'), "client[client_marital_status]", 'onclick=toggleButtons() class="genderOpts"', @$obj->client_marital_status ? @$obj->client_marital_status : -1, $identifiers ); ?></td>
					</tr>
					<tr>
						<td align="left" nowrap><?php echo 'NIF/CIN';?>: <strong style="color: red">*</strong></td>
						<td><input type="text" name="client[client_cin]"
							value="<?php echo @$obj->client_cin ;?>" class="text" /></td>
					</tr>
					<tr>
						<td align="left" nowrap><?php echo $AppUI->_('Other identification');?>: </td>
						<td><input type="text" name="client[client_other_id]"
							value="<?php echo @$obj->client_other_id ;?>" class="text" /></td>
					</tr>
					<tr>
						<td align="left"><?php echo $AppUI->_('Education level');?>:</td>
						<td>
					<?php
					echo arraySelect ( $education_level, 'client[client_education_level]', 'class=text size=1', $education_level [@$obj->client_education_level] ? @$obj->client_education_level : 0 );
					?>       
		
				</td>
					</tr>
					<tr>
						<td align="left"><?php echo $AppUI->_('Health status');?>: </td>
						<td>
				<?php echo arraySelect( $healthstatus, 'client[client_health_status]', 'size="1" class="text"', $healthstatus[@$obj->client_health_status] ? $obj->client_health_status:-1); ?>        
			</td>
					</tr>
					<tr>
						<td align="left" nowrap><?php echo $AppUI->_('Profession');?>: </td>
						<td><input type="text" name="client[client_profession]"
							value="<?php echo @$obj->client_profession  ;?>" class="text" />
						</td>
					</tr>
					<tr>
						<td align="left" nowrap><?php echo $AppUI->_('Occupation');?>: </td>
						<td><input type="text" name="client[client_occupation]"
							value="<?php echo @$obj->client_occupation  ;?>" class="text" />
						</td>
					</tr>
					<tr>
						<td align="left" nowrap><?php echo $AppUI->_('Status in the house');?>: </td>
						<td><input type="text" name="client[client_status_house]"
							value="<?php echo @$obj->client_status_house  ;?>" class="text" />
						</td>
					</tr>
					<tr>
						<td align="left" nowrap><?php echo $AppUI->_('How many rooms');?>: </td>
						<td><input type="number" name="client[client_number_rooms]"
							value="<?php echo @$obj->client_number_rooms  ;?>" class="text" />
						</td>
					</tr>
					<tr>
						<td align="right" colspan="3">
							<?php
								require_once("./classes/CustomFields.class.php");
								$custom_fields = New CustomFields( $m, $a, @$obj->client_id, "edit" );
								$custom_fields->printHTML();
							?>
						</td>
					</tr>
				</table>
			</td>
			<td>
				<table>
					<tr>
						<td>
							<b><?php echo $AppUI->_('Notes');?></b>
						<textarea cols="90" rows="10" class="textarea"
								name="client[client_notes]"
								value="<?php echo @$obj->client_notes  ;?>">
				</textarea></td>
					</tr>
				</table>
			</td>
		</tr>
		
		
		<tr>
			<td><input type="button" value="<?php echo $AppUI->_('back');?>"
				class="button" onClick="javascript:history.back(-1);" /></td>
			<td colspan="5" align="right"><input type="button"
				name="btnFuseAction" value="<?php echo $AppUI->_('submit');?>"
				class="button" onClick="javascript:submitIt(document.editFrm)" /></td>
		</tr>

<?php
/*
 * if (isset($_GET['tab']))
 * $AppUI->setState('ClientAeTabIdx', dPgetParam($_GET, 'tab', 0));
 *
 * $moddir = $dPconfig['root_dir'] . '/modules/clients/';
 *
 * $tab = $AppUI->getState('ClientAeTabIdx', 0);
 * $tabBox =& new CTabBox("?m=clients&a=addedit&client_id=$client_id", "", $tab, "");
 * $tabBox->add($moddir . "ae_counselling", "PCR Tests");
 * //$tabBox->add($moddir . "ae_contacts", "Contacts");
 * $tabBox->loadExtras('clients', 'addedit');
 * $tabBox->show('', true);
 */
?>
</td>
	</table>
</form>