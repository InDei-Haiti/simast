<?php

$client_id = intval ( dPgetParam ( $_GET, "client_id", 0 ) );

//require_once $AppUI->getModuleClass ( 'counsellinginfo' );
//require_once $AppUI->getModuleClass ( 'admission' );
//require_once $AppUI->getModuleClass ( 'medical' );
//require_once $AppUI->getModuleClass ( 'followup' );

$perms = & $AppUI->acl ();
$canRead = $perms->checkModuleItem ( $m, 'view', $client_id );
$canEdit = $perms->checkModuleItem ( $m, 'edit', $client_id );

$df = $AppUI->getPref ( 'SHDATEFORMAT' );

if (! $canRead) {
	$AppUI->redirect ( "m=public&a=access_denied" );
}

if (isset ( $_GET ['tab'] )) {
	$AppUI->setState ( 'ClientVwTab', $_GET ['tab'] );
}

$tab = $AppUI->getState ( 'ClientVwTab' ) !== NULL ? $AppUI->getState ( 'ClientVwTab' ) : 0;

$msg = '';
$obj = new CClient ();
$canDelete = $obj->canDelete ( $msg, $client_id );

//load record data
$q = new DBQuery ();
$q->addTable ( 'clients' );
$q->addQuery ( 'clients.*' );
//$q->addQuery('con.contact_first_name');
//$q->addQuery('con.contact_last_name');
//$q->addJoin('contacts', 'con', 'con.contact_company_id = ' . $client_id);
$q->addWhere ( 'clients.client_id = ' . $client_id );
$sql = $q->prepare ();

if (! db_loadObject ( $sql, $obj )) {
	$AppUI->setMsg ( 'Client' );
	$AppUI->setMsg ( "invalidID", UI_MSG_ERROR, true );
	$AppUI->redirect ();
} else {
	$AppUI->savePlace ();
}

if(@$obj->client_administration_section){	
	$q = new DBQuery();
	$q->addTable ('administration_section', 'adm_sec' );
	$q->addQuery ( 'adm_sec.administration_section_code_com' );
	$q->addWhere('administration_section_code ='.@$obj->client_administration_section);
	$client_administration_com = array_unique($q->loadColumns());
	$q->clear();
	$q->addTable ('administration_com', 'adm_com' );
	$q->addQuery ( 'adm_com.administration_com_name' );
	$q->addWhere('administration_com_code ="'.$client_administration_com[0].'"');
	$client_administration_com = $q->loadResult(); 
	$q->clear();
	$q->addTable ('administration_section', 'adm_sect' );
	$q->addQuery ( 'adm_sect.administration_section_name' );
	$q->addWhere('administration_section_code ="'.@$obj->client_administration_section.'"');
	$client_administration_sect = $q->loadResult();
	
}

/*
 * Unbold
 * */

if($obj->client_obsolete == 1){
	$obj->client_obsolete = 0;
	$obj->store();
}

$citiesArray = dPgetSysVal ( 'ClientCities' );
$boolTypes = dPgetSysVal ( 'YesNo' );
$boolTypesND = dPgetSysVal ( 'YesNoND' );
$birthPlaces = dPgetSysVal ( 'BirthPlaceType' );
$birthTypes = dPgetSysVal ( 'BirthType' );
$ageTypes = dPgetSysVal ( 'AgeType' );
$awareStages = dPgetSysVal ( 'StatusAwareType' );
$statusTypes = dPgetSysVal ( 'ClientStatus' );
$genderTypes = dPgetSysVal ( 'GenderType' );

/*$q = new DBQuery ();
$q->addTable ( 'counselling_info' );
$q->addQuery ( 'counselling_info.*' );
$q->addWhere ( 'counselling_info.counselling_client_id = ' . $client_id );
$s = '';
$sql = $q->prepare ();

if ($rows = $q->loadList ()) {
	foreach ( $rows as $row ) {
		$counsellingObj = new CCounsellingInfo ();
		$counsellingObj->load ( $row ["counselling_id"] );
	}
}

$q = new DBQuery ();
$q->addTable ( 'admission_info' );
$q->addQuery ( 'admission_info.*' );
$q->addWhere ( 'admission_info.admission_client_id = ' . $client_id );
$s = '';
$sql = $q->prepare ();

if ($rows = $q->loadList ()) {
	foreach ( $rows as $row ) {
		$admissionObj = new CAdmissionRecord ();
		$admissionObj->load ( $row ["admission_id"] );
	}
}

$q = new DBQuery();
$q->addTable('social_visit');
$q->addWhere('social_client_id="'.$client_id.'"');
$q->setLimit(1);
$q->addOrder('social_id desc');
$q->addQuery('social_caregiver_pri as caregiver_pri,social_caregiver_sec as caregiver_sec');
$sdata = $q->loadList();
if($sdata && is_array($sdata) && count($sdata) > 0){
	$sdata=$sdata[0];
}else{
	$sdata = false;
}

$q = new DBQuery ();
$q->addQuery ( 'concat(fname," ",lname) as name,age,mobile,id' );
$q->addTable ( 'admission_caregivers' );
$q->setLimit ( 1 );
$q->addWhere('(datesoff is null or datesoff = 0)');
$q->addOrder('id desc');
$q->addWhere('client_id="'.$client_id.'"');

$pcarez = array ('father' => array (), 'mother' => array (), 'caregiver_pri' => array (), 'caregiver_sec' => array () );
foreach ( $pcarez as $pctype => $pcinfo ) {
	$person = $admissionObj->{'admission_' . $pctype};
	if (strstr ( $pctype, 'caregiver' ) && $sdata !== false) {
		if (is_null ( $person ) || (int)$person === 0 || (int)$sdata[$pctype]  > 0) {
			$person = $sdata [$pctype];
		} elseif ($person > 0 && $sdata [$pctype] === null) {
			$person = 0;
		}
	}
	if ($person > 0) {
		$q1 = clone $q;
		$q1->addWhere ( 'id=' . $person );
		//$q1->addWhere('role="'.str_replace('caregiver_','',$pctype).'"');
		$tt = $q1->loadList ();

		if($tt && is_array($tt) && count($tt) > 0){
			$pcarez [$pctype] = $tt [0];
			unset ( $tt );
		}
		unset($q1);
	}
}

$q = new DBQuery ();
$q->addTable ( 'clinic_location' );
$q->addQuery ( 'clinic_location.clinic_location_id, clinic_location.clinic_location' );
$locationOptions = $q->loadHashList ();

$q = new DBQuery ();
$q->addTable ( 'medical_assessment' );
$q->addQuery ( 'medical_assessment.*' );
$q->addWhere ( 'medical_assessment.medical_client_id = ' . $client_id );
$s = '';
$sql = $q->prepare ();

if ($rows = $q->loadList ()) {
	foreach ( $rows as $row ) {
		$medicalObj = new CMedicalAssessment ();
		$medicalObj->load ( $row ["medical_id"] );
	}
}
//load clinics
$q = new DBQuery ();
$q->addTable ( 'clinics', 'c' );
$q->addQuery ( 'c.clinic_id, c.clinic_name' );
$q->addOrder ( 'c.clinic_name' );
$clinics = $q->loadHashList ();*/
// setup the title block
$types = dPgetSysVal ( 'ClientType' );
$type = $types [$obj->client_type];

$titleBlock = new CTitleBlock ( "View Beneficiery :: " . $obj->getFullName (), NULL, $m, "$m.$a" );
$titleBlock->addCell ( "<form name='searchform' action='?m=search' method='post'>
                        <table>
                         <tr>
                           <td>
                              <input class = 'text' type='text' name ='search_string' value='$search_string' />
						   </td>
						   <td>
							  <input type='submit' value='" . $AppUI->_ ( 'search' ) . "' class='button' />
						   </td>
						  </tr>
                         </table>
                        </form>" );

$search_string = addslashes ( $search_string );

if ($canEdit) {
	$titleBlock->addCell ();
	/*
	$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_('enter new company details').'" />', '',
		'<form action="?m=companies&a=addedit" method="post">', '</form>'
	);*/
}

$titleBlock->addCrumb ( "?m=clients", "Beneficieries" );

if ($canEdit) {
	$titleBlock->addCrumb( "?m=clients&a=addedit&client_id=$client_id", "Edit" );


	/*if ($canDelete) {
		$titleBlock->addCrumbDelete ( 'Delete client', $canDelete, $msg );
	}*/

}
//get age
$age_years = 0;
$age_months = 0;
//$obj->getAge ( $age_years, $age_months );

//format date
//$entry_date = intval ( $counsellingObj->counselling_admission_date ) ? new CDate ( $counsellingObj->counselling_admission_date ) : null;//new CDate($admissionObj->admission_entry_date);
//$dob = intval ( $counsellingObj->counselling_dob ) ? new CDate ( $counsellingObj->counselling_dob ) : null;

$titleBlock->show ();

?>
<script language="javascript">
var selected_contacts_id = "<?php echo $obj->client_contacts; ?>";
<?php

if ($canDelete) {
	?>
function delIt()
{
	if (confirm( "<?php echo $AppUI->_ ( 'doDelete' ) . ' ' . $AppUI->_ ( 'Client' ) . '?';	?>" ))
    {
		document.frmDelete.submit();
	}
}
<?php
}
if ($canEdit) {
	?>

	var request = false;
   try {
     request = new XMLHttpRequest();
   } catch (trymicrosoft) {
     try {
       request = new ActiveXObject("Msxml2.XMLHTTP");
     } catch (othermicrosoft) {
       try {
         request = new ActiveXObject("Microsoft.XMLHTTP");
       } catch (failed) {
         request = false;
       }
     }
   }

   if (!request)
     alert("Error initializing XMLHttpRequest!");

	var client = 0;

   function ajaxChangeStatus(f) {
     var status = f.status.value;
	 client = f.client_id.value;
     //var url = "?m=companies&a=do_ajaxowner_update&user=" + escape(user)+"&company_id="+escape(company);
     var url = "modules/clients/do_ajaxstatus_update.php?status=" + escape(status)+"&client_id="+escape(client);
	 //alert(url);
     request.open("GET", url, true);
     request.onreadystatechange = updatePage;
     request.send(null);
   }


     function updatePage()
	 {
     if (request.readyState == 4) {
       if (request.status == 200)
	   {
         var xmlDoc = request.responseXML;
		 var showElements = xmlDoc.getElementsByTagName("status");
		 for (var x=0; x<showElements.length; x++)
		 {

			document.getElementById("status"+client).innerHTML = showElements[x].childNodes[0].textContent;
			//alert(document.innerHTML);
		 }
       } else if (request.status == 404) {
         alert ("Requested URL is not found.");
       } else if (request.status == 403) {
         alert("Access denied.");
       } else
         alert("status is " + request.status);
     }
   }

function singleUpdate(f)
{
	var changed = new Array();
    if (hasOneSelected(f, 'status')) {
        changed[changed.length] = 'Assigned Status';
    }
    if (changed.length < 1) {
        alert('Please choose new values for the status for this client');
        return false;
    }
    f.submit();

}
function resetBulkUpdate()
{
    var f = document.getElementsByName('graph[]');
    clearSelectedChecks(f);

}
function submitIt() {
	var form = document.updatestatus ;
	/*if (form.company_name.value.length < 3) {
		alert( "<?php
	echo $AppUI->_ ( 'companyValidName', UI_OUTPUT_JS );
	?>" );
		form.company_name.focus();
	} else {
		form.submit();
	}*/
	form.submit();
}
function bulkUpdate()
{
    var f = document.forms.assign;

    if (!hasOneChecked(f, 'graph[]')) {
        alert('Please choose the graphs to be assigned.');
        return false;
    }

    // figure out what is changing
    var changed = new Array();
    if (hasOneSelected(f, 'user')) {
        changed[changed.length] = 'Assigned CREs';
    }
    if (changed.length < 1) {
        alert('Please choose new values for the select graphs');
        return false;
    }
    var msg = 'Warning: If you continue, you will change the ';
    for (var i = 0; i < changed.length; i++) {
        msg += changed[i];
        if ((changed.length > 1) && (i == (changed.length-2))) {
            msg += ' and ';
        } else {
            if (i != (changed.length-1)) {
                msg += ', ';
            }
        }
    }
    msg += ' for all selected graphs. Are you sure you want to continue?';
    if (!confirm(msg)) {
        return false;
    }
    f.submit();
}
<?php
}
//$ages = $obj->age ();
?>


</script>

<table border="0" cellpadding="4" cellspacing="0" class="std">

<?php
if ($canDelete) {
	?>

<form name="frmDelete" action="./index.php?m=clients" method="post"><input
		type="hidden" name="dosql" value="do_newclient_aed" /> <input
		type="hidden" name="del" value="1" /> <input type="hidden"
		name="client_id" value="<?php echo $client_id; ?>" /></form>
<?php
}
?>

    <tr>
		<td valign="top" width="25%"><strong><?php
		echo $AppUI->_ ( 'Details' );
		?></strong>
		<table cellspacing="1" cellpadding="2">
			
			<tr>
				<td align="left" nowrap="nowrap"><?php
				echo $AppUI->_ ( "Client's Last Name" );
				?>:&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td class="hilite" width="50%"><?php
				echo $obj->client_last_name;
				?></td>
			</tr>
			<tr>
				<td align="left" nowrap="nowrap"><?php
				echo $AppUI->_ ( "Client's First Name" );
				?>:&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td class="hilite" width="50%"><?php
				echo $obj->client_first_name;
				?></td>
			</tr>
			<tr>
				<td align="left" nowrap="nowrap"><?php
				echo $AppUI->_ ( "Client's Nickname" );
				?>:&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td class="hilite" width="50%"><?php
				echo $obj->client_nickname;
				?></td>
			</tr>
			<tr>
				<td align="left" nowrap="nowrap"><?php
				echo $AppUI->_ ( 'Gender' );
				?>:</td>
				<td class="hilite" width="50%"><?php
				$GenderType = dPgetSysVal('GenderType');
				echo $GenderType[$obj->client_gender];
				?></td>
			</tr>
			<tr>
				<td align="left" nowrap="nowrap"><?php
				echo $AppUI->_ ( 'Client Birthday' );
				?>:</td>
				<td class="hilite" width="50%"><?php
					echo $obj->client_birthday;
				?></td>
			</tr>
			<tr>
				<td align="left" nowrap><?php echo $AppUI->_('Place of Birth');?>: </td>
				<td class="hilite" width="50%">
					<?php echo @$obj->client_place_of_birth ?>
				</td>
			</tr>
			<tr>
				<td align="left" nowrap><?php echo $AppUI->_('Adress');?>: </td>
				<td class="hilite" width="50%">
					<?php echo @$obj->client_address?>
				</td>
			</tr>
			<tr>
				<td align="left"><?php echo $AppUI->_('Commune');?>:</td>
				<td class="hilite">
					<?php 
					echo $client_administration_com;
					?>
				</td>
			</tr>
			<tr>
				<td align="left"><?php echo $AppUI->_('Communal section');?>:</td>
				<td class="hilite">
					<?php 
					echo $client_administration_sect;
					?>
				</td>
			</tr>
			<tr>
				<td align="left" nowrap><?php echo $AppUI->_('Phone');?>: </td>
				<td class="hilite" width="50%">
					<?php 
						echo @$obj->client_phone1?@$obj->client_phone1.'/':'';
						echo @$obj->client_phone2?@$obj->client_phone2.'/':'';
						echo @$obj->client_phone3?@$obj->client_phone3.'/':'';
					?>
				</td>
			</tr>
			<tr>
				<td align="left"><?php echo $AppUI->_('Marital Status');?>:</td>
				<td class="hilite" width="50%">
				
					<?php 
					$MaritalStatus = dPgetSysVal('MaritalStatus');
					echo $MaritalStatus[$obj->client_marital_status];
					?>
				</td>
			</tr>
			<tr>
				<td align="left" nowrap="nowrap"><?php
				echo $AppUI->_ ( 'CIN' );
				?>:</td>
				<td class="hilite" width="50%"><?php
				echo $obj->client_cin;
				?></td>
			</tr>
			<tr>
				<td align="left" nowrap="nowrap"><?php
				echo $AppUI->_ ( 'Other identification' );
				?>:</td>
				<td class="hilite" width="50%"><?php
				echo $obj->client_other_id;//$obj->getDescription ();
				?></td>
			</tr>
			<tr>
				<td align="left" nowrap="nowrap"><?php
				echo $AppUI->_ ( 'Education level' );
				?>:</td>
				<td class="hilite" width="50%"><?php
				$education_level = dPgetSysVal('EducationLevel');
				echo $education_level[$obj->client_education_level];//$obj->getDescription ();
				?></td>
			</tr>
			<tr>
				<td align="left" nowrap="nowrap"><?php
				echo $AppUI->_ ( 'Health status' );
				?>:</td>
				<td class="hilite" width="50%"><?php
				$health_status = dPgetSysVal('CaregiverHealthStatus');
				echo $health_status[$obj->client_health_status];//$obj->getDescription ();
				?></td>
			</tr>
			<tr>
				<td align="left" nowrap><?php echo $AppUI->_('Profession');?>: </td>
				<td class="hilite" width="50%">
					<?php echo @$obj->client_profession  ;?>
				</td>
			</tr>
			<tr>
				<td align="left" nowrap="nowrap"><?php
				echo $AppUI->_ ( 'Occupation' );
				?>:</td>
				<td class="hilite" width="50%"><?php
				echo $obj->client_occupation;//$obj->getDescription ();
				?></td>
			</tr>
			<tr>
				<td align="left" nowrap><?php echo $AppUI->_('Status in the house');?>: </td>
				<td class="hilite" width="50%">
					<?php echo @$obj->client_status_house  ;?>
				</td>
			</tr>
			<tr>
				<td align="left" nowrap><?php echo $AppUI->_('How many rooms');?>: </td>
				<td class="hilite" width="50%">
					<?php echo @$obj->client_number_rooms  ;?>
				</td>
			</tr>
			<tr>
				<td align="right" colspan="3">
					<?php
						require_once("./classes/CustomFields.class.php");
						$custom_fields = New CustomFields( $m, $a, @$obj->client_id, "view" );
						$custom_fields->printHTML();
					?>
				</td>
			</tr>

</table>
<?php

$moddir = $dPconfig ['root_dir'] . '/modules/clients/';
$tabBox = new CTabBox ( "?m=clients&a=view&client_id=$client_id", "", $tab );
//echo "jdsfjdsfjs". @$obj->client_activities;
//if(@$obj->client_activities){
$q = new DBQuery();
$q->addTable('activity_clients', 'c');
$q->addQuery('c.activity_clients_activity_id');
//$q->addWhere($task_id.' in (SPLIT(",",c.client_activities))');
$q->addWhere("c.activity_clients_client_id = ".$client_id);
$sql = $q->prepare ();

//echo $sql;
//var_dump($sql);
//print $sql;

$qid = db_exec ( $sql );
//var_dump($qid);
$count = db_num_rows ( $qid );

$rows = $q->loadList ();
$lid = array();
foreach ( $rows as $rid =>  $row ){
	$lid[] = $row["activity_clients_activity_id"];
}
//var_dump($lid);
if($lid && count($lid)>0){
	$lid = implode(',', $lid);
	$q = new DBQuery();
	$q->addTable('form_master');
	$q->addQuery('id,title');
	$q->addWhere('registry = "0"');
	$q->addWhere('task_id in ('.@$lid.')');
	$newforms = $q->loadHashList();
	
	$_SESSION['wiz_tab']=array();
	if(count($newforms) > 0){
		foreach($newforms as $nid => $nform){
			$tpos=$tabBox->add ( $moddir.'vw_wizard', $nform );
			$_SESSION['wiz_tab'][$tpos]=$nid;
		}
	}	
}
$tabBox->show();
?>