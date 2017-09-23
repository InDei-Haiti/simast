<?php
require_once("activity.class.php");
$activity_id = intval (dPgetParam( $_GET, "activity_id", 0));
//var_dump($_GET);
if (isset( $_GET['clientorderby'] )) {
    $clientorderdir = $AppUI->getState( 'ActivityClientIdxOrderDir' ) ? ($AppUI->getState( 'ActivityClientIdxOrderDir' )== 'asc' ? 'desc' : 'asc' ) : 'desc';
	$AppUI->setState( 'ActivityClientIdxOrderBy', $_GET['clientorderby'] );
    $AppUI->setState( 'ActivityClientIdxOrderDir', $clientorderdir);
}
$clientorderby  = $AppUI->getState( 'ActivityClientIdxOrderBy' ) ? $AppUI->getState( 'ActivityClientIdxOrderBy' ) : 'client_first_name';
$clientorderdir = $AppUI->getState( 'ActivityClientIdxOrderDir' ) ? $AppUI->getState( 'ActivityClientIdxOrderDir' ) : 'asc';

if (isset( $_GET['caregiverorderby'] )) {
    $caregiverorderdir = $AppUI->getState( 'ActivityCaregiverIdxOrderDir' ) ? ($AppUI->getState( 'ActivityCaregiverIdxOrderDir' )== 'asc' ? 'desc' : 'asc' ) : 'desc';
	$AppUI->setState( 'ActivityCaregiverIdxOrderBy', $_GET['caregiverorderby'] );
    $AppUI->setState( 'ActivityCaregiverIdxOrderDir', $caregiverorderdir);
}
$caregiverorderby  = $AppUI->getState( 'ActivityCaregiverIdxOrderBy' ) ? $AppUI->getState( 'ActivityCaregiverIdxOrderBy' ) : 'caregiver_fname';
$caregiverorderdir = $AppUI->getState( 'ActivityCaregiverIdxOrderDir' ) ? $AppUI->getState( 'ActivityCaregiverIdxOrderDir' ) : 'asc';

if (isset( $_GET['contactorderby'] )) {
    $contactorderdir = $AppUI->getState( 'ActivityContactIdxOrderDir' ) ? ($AppUI->getState( 'ActivityContactIdxOrderDir' )== 'asc' ? 'desc' : 'asc' ) : 'desc';
	$AppUI->setState( 'ActivityContactIdxOrderBy', $_GET['contactorderby'] );
    $AppUI->setState( 'ActivityContactIdxOrderDir', $caregiverorderdir);
}
$contactorderby  = $AppUI->getState( 'ActivityContactIdxOrderBy' ) ? $AppUI->getState( 'ActivityContactIdxOrderBy' ) : 'contact_first_name';
$contactorderdir = $AppUI->getState( 'ActivityContactIdxOrderDir' ) ? $AppUI->getState( 'ActivityContactIdxOrderDir' ) : 'asc';



$perms = & $AppUI->acl();
$canRead = $perms->checkModuleItem( $m, 'view', $activity_id);
$canEdit = $perms->checkModuleItem( $m, 'edit', $activity_id);

$df = $AppUI->getPref('SHDATEFORMAT');

if (!$canRead)
{
  $AppUI->redirect("m=public&a=access_denied");
}

if (isset( $_GET['tab']))
{
  $AppUI->setState('ActivityVwTab', $_GET['tab']);
}

$tab = $AppUI->getState('ActivityVwTab') !== NULL ? $AppUI->getState('ActivityVwTab') : 0;

$msg = '';
$obj=  new CActivity();
$canDelete = $obj->canDelete($msg, $activity_id);

//load record data
$q = new DBQuery;
$q->addTable('activity');
$q->addQuery('activity.*');
//$q->addQuery('con.contact_first_name');
//$q->addQuery('con.contact_last_name');
//$q->addJoin('contacts', 'con', 'con.contact_company_id = ' . $activity_id);
$q->addWhere('activity.activity_id = '. $activity_id);
$sql = $q->prepare();

if (!db_loadObject( $sql, $obj ))
{
	$AppUI->setMsg( 'Activity' );
	$AppUI->setMsg( "invalidID", UI_MSG_ERROR, true );
	$AppUI->redirect();
}
else
{
	$AppUI->savePlace();
}

$citiesArray = dPgetSysVal('ClientCities');
$boolTypes = dPgetSysVal('YesNo');
$boolTypesND = dPgetSysVal('YesNoND');
$birthPlaces = dPgetSysVal('BirthPlaceType');
$birthTypes = dPgetSysVal('BirthType');
$ageTypes = dPgetSysVal('AgeType');
$awareStages = dPgetSysVal('StatusAwareType');
$statusTypes = dPgetSysVal('ClientStatus');
$genderTypes = dPgetSysVal('GenderType');
$curriculumTypes = dPgetSysVal('CurriculumTypes');

$q  = new DBQuery;
$q->addTable('clinic_location');
$q->addQuery('clinic_location.clinic_location_id, clinic_location.clinic_location');
$locationOptions = $q->loadHashList();	


//load activities

$q = new DBQuery();
$q->addTable('trainings', 'c');
$q->addQuery('c.training_id, c.training_name');
$q->addOrder('c.training_name');
$activityOptions = $q->loadHashList();


//load activities for this group activity

$q = new DBQuery();
$q->addTable("activity_facilitator");
$q->addQuery("activity_facilitator.*");
$q->addWhere("activity_facilitator.facilitator_activity_id = " . $activity_id);
$activities = $q->loadList();	



$s='';


//load clinics
$q = new DBQuery();
$q->addTable('clinics', 'c');
$q->addQuery('c.clinic_id, c.clinic_name');
$q->addOrder('c.clinic_name');
$clinics = $q->loadHashList();
	// setup the title block
$types = dPgetSysVal('ClientType');
//$type = $types[$obj->activity_type];

$titleBlock = new CTitleBlock( "View Activity :: " . $obj->activity_description, NULL, $m, "$m.$a" );
$titleBlock->addCell ("<form name='searchform' action='?m=search' method='post'>
                        <table>
                         <tr>
                           <td>						   
                              <input class = 'text' type='text' name ='search_string' value='$search_string' />
						   </td>
						   <td>
							  <input type='submit' value='" .$AppUI->_( 'search' )."' class='button' />
						   </td>
						  </tr>
                         </table>
                        </form>"
);

						
$search_string = addslashes($search_string);

/*if ($canEdit) {
	$titleBlock->addCell();
	$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_('new activity').'" />', '',
		'<form action="?m=training&a=add_activity" method="post">', '</form>'
	);
} */

$titleBlock->addCrumb( "?m=training", "Activities" );

if ($canEdit)
{
	$titleBlock->addCrumb( "?m=training&a=add_activity&activity_id=$activity_id", "Edit" );

	if ($canDelete)
    {
		$titleBlock->addCrumbDelete( 'Delete Activity', $canDelete, $msg );
	}

}
//format date
$entry_date = intval($obj->activity_entry_date) ? new CDate($obj->activity_entry_date ) :  null;

$titleBlock->show();

?>
<script language="javascript">
var selected_contacts_id = "<?php echo $obj->activity_contacts; ?>";

<?php

if ($canDelete)
{
?>
function delIt()
{
	if (confirm( "<?php echo $AppUI->_('doDelete').' '.$AppUI->_('Activity').'?';?>" ))
    {
		document.frmDelete.submit();
	}
}
<?php } 
if ($canEdit)
{
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
	 client = f.activity_id.value;
     //var url = "?m=companies&a=do_ajaxowner_update&user=" + escape(user)+"&company_id="+escape(company);
     var url = "modules/clients/do_ajaxstatus_update.php?status=" + escape(status)+"&activity_id="+escape(client);
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
		alert( "<?php echo $AppUI->_('companyValidName', UI_OUTPUT_JS);?>" );
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
<?php } ?>


</script>

<table border="0" cellpadding="4" cellspacing="0" width="65%" class="std">

<?php if ($canDelete)
{
?>

<form name="frmDelete" action="./index.php?m=training" method="post">
	<input type="hidden" name="dosql" value="do_newactivity_aed" />
	<input type="hidden" name="del" value="1" />
	<input type="hidden" name="activity_id" value="<?php echo $activity_id;?>" />
</form>
<?php } ?>
<tr>
	<td valign="top">
		<strong><?php echo $AppUI->_('Details');?></strong>
		<table cellspacing="1" cellpadding="2">
		<tr>
			<td align="left" nowrap="nowrap"><?php echo $AppUI->_('Center');?>:</td>
			<td class="hilite" width="50%"><?php echo $clinics[$obj->activity_clinic];?></td>
		</tr>	
		<tr>
			<td align="left" nowrap="nowrap"><?php echo $AppUI->_('Activity Description');?>:</td>
			<td class="hilite" width="50%"><?php echo $obj->activity_description;?></td>
		</tr>	
		<tr>
			<td align="left" nowrap="nowrap"><?php echo $AppUI->_('Date entered');?>:</td>
			<td class="hilite" width="50%"><?php if (isset ($entry_date)) echo $entry_date->format($df);?></td>
		</tr>
		<tr>
			<td align="left" nowrap="nowrap"><?php echo $AppUI->_('No. of persons (Male)');?>:</td>
			<td class="hilite" width="50%"><?php echo $obj->activity_male_count;?></td>
		</tr>
		<tr>
			<td align="left" nowrap="nowrap"><?php echo $AppUI->_('No. of persons (Female)');?>:</td>
			<td class="hilite" ><?php echo $obj->activity_female_count;?></td>
		</tr>
		<tr>
			<td align="left" nowrap="nowrap"><?php echo $AppUI->_('Notes');?>:</td>
			<td class="hilite" align="right" width="50%"><?php echo $obj->activity_notes;?></td>
		</tr>	
	</table>
		
	</td>
	<td valign="top">
	   <table>	
	   <tr>
         <td align="left" nowrap valign="top">&nbsp;</td>
		 <td align="left">
		 <table class="tbl">
		 <tr>
		 	<th><?php echo $AppUI->_('Training');?></th>
			<th><?php echo $AppUI->_('Facilitator');?></th>
		 </tr>
		 <?php 
		 if (count($activities) > 0)
		 {
			 foreach ($activities as $activityrecord)
			 {
				
			 ?>
			 <tr>
				<td><?php echo $activityOptions[$activityrecord["facilitator_training_id"]];?></td>
				<td><?php echo $activityrecord["facilitator_name"]?></td>
			 </tr>
			 <?php } 
			
		 }	 
		?>
		 </table>		 
		 </td>
	  </tr>	
	</table>	  
	</td>
	</tr>

</table>
<?php

$moddir = $dPconfig['root_dir'] . '/modules/training/';
$tabBox = new CTabBox( "?m=training&a=view_act&activity_id=$activity_id", "", $tab );
$tabBox->add( $moddir . 'vw_activity_clients', 'Clients' );
$tabBox->add( $moddir . 'vw_activity_contacts', 'Staff' );
$tabBox->add( $moddir . 'vw_activity_caregivers', 'Caregivers' );
$tabBox->show();
?>