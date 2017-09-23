<?php

$training_id = intval (dPgetParam( $_GET, "training_id", 0));


$perms = & $AppUI->acl();
$canRead = $perms->checkModuleItem( $m, 'view', $training_id);
$canEdit = $perms->checkModuleItem( $m, 'edit', $training_id);

$df = $AppUI->getPref('SHDATEFORMAT');

if (!$canRead)
{
  $AppUI->redirect("m=public&a=access_denied");
}

if (isset( $_GET['tab']))
{
  $AppUI->setState('TrainingVwTab', $_GET['tab']);
}

$tab = $AppUI->getState('TrainingVwTab') !== NULL ? $AppUI->getState('TrainingVwTab') : 0;

$msg = '';
$obj=  new CTraining();
$canDelete = $obj->canDelete($msg, $training_id);

//load record data
$q = new DBQuery;
$q->addTable('trainings');
$q->addQuery('trainings.*');
//$q->addQuery('con.contact_first_name');
//$q->addQuery('con.contact_last_name');
//$q->addJoin('contacts', 'con', 'con.contact_company_id = ' . $training_id);
$q->addWhere('trainings.training_id = '. $training_id);
$sql = $q->prepare();

if (!db_loadObject( $sql, $obj ))
{
	$AppUI->setMsg( 'Training' );
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


$q  = new DBQuery;
$q->addTable('clinic_location');
$q->addQuery('clinic_location.clinic_location_id, clinic_location.clinic_location');
$locationOptions = $q->loadHashList();	


$s='';


//load clinics
$q = new DBQuery();
$q->addTable('clinics', 'c');
$q->addQuery('c.clinic_id, c.clinic_name');
$q->addOrder('c.clinic_name');
$clinics = $q->loadHashList();
	// setup the title block
$types = dPgetSysVal('ClientType');
$curriculumTypes = dPgetSysVal('CurriculumTypes');
//$type = $types[$obj->training_type];

$titleBlock = new CTitleBlock( "View Training :: " . $obj->training_name, NULL, $m, "$m.$a" );
	
$search_string = addslashes($search_string);
/*if ($canEdit) {
	$titleBlock->addCell();
	$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_('new training').'" />', '',
		'<form action="?m=training&a=addedit" method="post">', '</form>'
	);
}*/


$titleBlock->addCrumb( "?m=training", " Trainings" );

if ($canEdit)
{
	$titleBlock->addCrumb( "?m=training&a=add&training_id=$training_id", "Edit" );

	if ($canDelete)
    {
		$titleBlock->addCrumbDelete( 'Delete Training', $canDelete, $msg );
	}

}
//format date
$entry_date = intval($obj->training_entry_date) ? new CDate($obj->training_entry_date ) :  null;

$titleBlock->show();

?>
<script language="javascript">
var selected_contacts_id = "<?php echo $obj->training_contacts; ?>";

<?php

if ($canDelete)
{
?>
function delIt()
{
	if (confirm( "<?php echo $AppUI->_('doDelete').' '.$AppUI->_('Training').'?';?>" ))
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
	 client = f.training_id.value;
     //var url = "?m=companies&a=do_ajaxowner_update&user=" + escape(user)+"&company_id="+escape(company);
     var url = "modules/clients/do_ajaxstatus_update.php?status=" + escape(status)+"&training_id="+escape(client);
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

<table border="0" cellpadding="4" cellspacing="0" width="50%" class="std">

<?php if ($canDelete)
{
?>

<form name="frmDelete" action="./index.php?m=training" method="post">
	<input type="hidden" name="dosql" value="do_newtraining_aed" />
	<input type="hidden" name="del" value="1" />
	<input type="hidden" name="training_id" value="<?php echo $training_id;?>" />
</form>
<?php } ?>
<tr>
	<td valign="top">
		<strong><?php echo $AppUI->_('Details');?></strong>
		<table cellspacing="1" cellpadding="2">
		<tr>
			<td align="left" nowrap="nowrap"><?php echo $AppUI->_('Center');?>:</td>
			<td class="hilite" width="50%"><?php echo $clinics[$obj->training_clinic];?></td>
		</tr>	
		<tr>
			<td align="left" nowrap="nowrap"><?php echo $AppUI->_('Training Name');?>:</td>
			<td class="hilite" width="50%"><?php echo $obj->training_name;?></td>
		</tr>			
		<tr>
			<td align="left" nowrap="nowrap"><?php echo $AppUI->_('Curriculum');?>:</td>
			<td class="hilite" width="50%"><?php echo $curriculumTypes[$obj->training_curriculum];?></td>
		</tr>		
		<tr>
			<td align="left" nowrap="nowrap"><?php echo $AppUI->_('...Curriculum (Other)');?>:</td>
			<td class="hilite" width="50%"><?php echo $obj->training_curriculum_desc;?></td>
		</tr>	
		<tr>
			<td align="left" nowrap="nowrap"><?php echo $AppUI->_('Date entered');?>:</td>
			<td class="hilite" width="50%"><?php if (isset ($entry_date)) echo $entry_date->format($df);?></td>
		</tr>

		<tr>
			<td align="left" nowrap="nowrap"><?php echo $AppUI->_('Notes');?>:</td>
			<td class="hilite" align="right" width="50%"><?php echo $obj->training_notes;?></td>
		</tr>	
	</table>
		
	</td>
	</tr>

</table>
