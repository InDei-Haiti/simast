<?php

require_once($AppUI->getModuleClass("contacts"));

$training_id = intval( dPgetParam( $_GET, "training_id", 0 ));
$training_type = intval( dPgetParam( $_GET, "training_type", 3 ));


if 	($contact_unique_update == 0)
  $contact_unique_update = uniqid("");
	
$perms = & $AppUI->acl();

if ($training_id)
  $canEdit = $perms->checkModuleItem( $m, "edit", $training_id );
else
  $canEdit = $perms->checkModule($m, "add");

if (!$canEdit)
{
  $AppUI->redirect("m=public&a=access_denied");
}



$q  = new DBQuery;
$q->addTable('trainings');
$q->addQuery('trainings.*');
$q->addWhere('trainings.training_id = '.$training_id);
$sql = $q->prepare();
$q->clear();

$obj = new CTraining();

if (!db_loadObject($sql, $obj) && ($training_id > 0))
{
  	$AppUI->setMsg( "invalidID", UI_MSG_ERROR, true );
	$AppUI->redirect();
}



//load cities
$citiesArray = arrayMerge(array(-1=>'-Select City-'), dPgetSysVal('ClientCities'));
//load centers

$q->clear();
$q->addTable('clinics', 'c');
$q->addQuery('c.clinic_id, c.clinic_name');
$q->addOrder('c.clinic_name');

$clinics = arrayMerge(array(0=> '-Select Center -'),$q->loadHashList());

$boolTypes = dPgetSysVal('YesNo');
$boolTypesND = dPgetSysVal('YesNoND');
$birthPlaces = dPgetSysVal('BirthPlaceType');
$birthTypes = dPgetSysVal('BirthType');
$ageTypes = dPgetSysVal('AgeType');
$awareStages = dPgetSysVal('StatusAwareType');

//load clients
/*if ($training_id > 0)
{
    $q = new DBQuery();
	$q->addTable("training_clients", "ac");
	$q->innerJoin("clients", "c", "c.client_id = ac.training_clients_client_id");
	$q->addQuery("c.*");
	$q->addWhere("training_clients.training_clients_training_id = ". $training_id);
	$clients = 	$q->loadHashList();
	
}*/
//load curriculum types
$types = dPgetSysVal('CurriculumTypes');
$type = $types[$training_type];
//load staff for officer fields
// collect all the users for the nutritionist list
$q  = new DBQuery;
$q->addTable('contacts','con');
$q->leftJoin('users','u', 'u.user_contact = con.contact_id');
$q->addQuery('contact_id');
$q->addQuery('CONCAT_WS(", ",contact_last_name,contact_first_name)'); 
$q->addOrder('contact_last_name');
$q->addWhere('contact_active="1"');
$owners = $q->loadHashList();

$owners = arrayMerge(array (0=>'-Select Officer-'), $owners);

//load status stuff
$status = arrayMerge(array(-1=>'-Select Current Client Status-'), dPgetSysVal('ClientStatus'));
//load priority stuff
$priority = arrayMerge(array(-1=>'-Select Current Client Priority-'), dPgetSysVal('ClientPriority'));

$selected_clients = array();
//var_dump($selected_clients);
//var_dump($training_id);

//$ttl = "$type :: ";
//var_dump($selected_clients);
$ttl .= $training_id > 0 ? "Edit Training" : "New Training";

$titleBlock = new CTitleBlock( $ttl, '', $m, "$m.$a" );
$titleBlock->addCrumb( "?m=training", "Trainings" );
$titleBlock->addCrumbRight2( "javascript:clearSelection(document.forms['editFrm'])", "Clear All Selections" );
if ($training_id != 0)
  $titleBlock->addCrumb( "?m=training&a=view&training_id=$training_id", "View" );
$titleBlock->show();

?>
<script language="javascript">
var selected_contacts_id = "<?php echo $obj->main_contacts; ?>";
var contact_unique_update = "<?php echo $contact_unique_update; ?>";
var training_id = '<?php echo $obj->training_id;?>';
var training_name_msg = "<?php echo $AppUI->_('Please enter a name for the client');?>";
var selected_clients_id = "<?php echo implode(',', $selected_clients); ?>";

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
	fld_fdate = eval( 'document.editFrm.training_' + calendarField );
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
	window.open("./index.php?m=training&a=select_training_clinic&dialog=1&table_name=clinics&clinic_id=<?php echo $clinic_detail['clinic_id'];?>", "clinic", "left=50,top=50,height=250,width=400,resizable");
}

function setClinic( key, val ){
	var f = document.editFrm;
 	if (val != '') {
    	f.contact_company.value = key;
			f.training_clinic_name.value = val;
    	if ( window.clinic_id != key )
		{
    		f.contact_department.value = "";
				f.contact_department_name.value = "";
    	}
    	window.clinic_id = key;
    	window.clinic_value = val;
    }
}
function popClients() 
{
	window.open('./index.php?m=public&a=client_selector&dialog=1&call_back=setClients&selected_clients_id='+selected_clients_id, 'clients','height=600,width=400,resizable,scrollbars=yes');
}
function setClients(client_id_string)
{
	if(!client_id_string){
		client_id_string = "";
	}
	alert(client_id_string);
	document.editFrm.training_clients.value = client_id_string;
	//selected_clients_id = client_id_string;
}

   var request = false;
   try 
   {
     request = new XMLHttpRequest();
   } 
   catch (trymicrosoft) 
   {
     try 
	 {
       request = new ActiveXObject("Msxml2.XMLHTTP");
     } catch (othermicrosoft) 
	 {
       try 
	   {
         request = new ActiveXObject("Microsoft.XMLHTTP");
       } 
	   catch (failed) 
	   {
         request = false;
       }  
     }
   }

   if (!request)
     alert("Error initializing XMLHttpRequest!");

	var training = 0;

   function ajaxAssign(f) 
   {
     var client = f.client.value;
	 training = f.training_id.value;
     //var url = "?m=companies&a=do_ajaxowner_update&user=" + escape(user)+"&company_id="+escape(company);
     var url = "modules/training/do_ajaxclient_update.php?client=" + escape(client)+"&training_id="+escape(training);
	 //alert(url);	 
     request.open("GET", url, true);
     request.onreadystatechange = updatePage;
     request.send(null);
   }

   
     function updatePage() 
	 {
	     if (request.readyState == 4) 
		 {
	       if (request.status == 200) 
		   {
	         var xmlDoc = request.responseXML;
			 var showElements = xmlDoc.getElementsByTagName("client");
			 for (var x=0; x<showElements.length; x++) 
			 {
				// We know that the first child of show is title, and the second is rating
				document.getElementById("client"+training).innerHTML = showElements[x].childNodes[0].textContent;
				//alert(document.innerHTML);
			 }
	       } 
		   else if (request.status == 404) 
		   {
	         alert ("Requested URL is not found.");
	       } 
		   else if (request.status == 403) 
		   {
	         alert("Access denied.");
	       } 
		   else
	         alert("status is " + request.status);
	     }
   }

// Given a tr node and row number (newid), this iterates over the row in the
// DOM tree, changing the id attribute to refer to the new row number.
function rowrenumber(newrow, newid)
{
  var curnode = newrow.firstChild;      // td node
  while (curnode) {
    var curitem = curnode.firstChild;   // input node (or whatever)
    while (curitem) {    
      if (curitem.id) {  // replace row number in id
        var idx = 0;
        var spl = curitem.id.split('_');
        var baseid = spl[0];
        curitem.id = baseid + '_' + newid;
        if (curitem.name)
          curitem.name = baseid + '_' + newid;
        if (baseid == 'catno')
          curitem.tabIndex = newid;
      }
      curitem = curitem.nextSibling;
    }
    curnode = curnode.nextSibling;
  }
}
// Appends a row to the given table, at the bottom of the table.

function AppendRow(table_id)
{
  var row = document.getElementById(table_id).rows.item(1);  // 1st row
  var newid = row.parentNode.rows.length;  // Since this includes the header row, we don't need to add one
  var newrow = row.cloneNode(true);
  
  rowrenumber(newrow, newid);
  row.parentNode.appendChild(newrow);      // Attach to table
    
    // Clear out data from new row.
	
  var curnode = document.getElementById('training_' + newid);
  curnode.value = "";
  curnode.tabIndex = newid;
  curnode = document.getElementById('facilitator_a_' + newid);
  curnode.value = "";
  curnode = document.getElementById('facilitator_b_' + newid);
  curnode.value = "";
  curnode = document.getElementById('delete_' + newid);
  curnode.innerHTML = "X";
  curnode = document.getElementById('delete_1');  // Really only need this when newid = 2
  curnode.innerHTML = "X";
}
// Give a node within a row of the table (one level down from the td node),
// this deletes that row, renumbers the other rows accordingly, updates
// the Grand Total, and hides the delete button if there is only one row
// left.
function DeleteRow(el)
{
  var row = el.parentNode.parentNode;   // tr node
  var rownum = row.rowIndex;            // row to delete
  var tbody = row.parentNode;           // tbody node
  var numrows = tbody.rows.length - 1;  // don't count header row!
  if (numrows == 1)                     // can't delete when only one row left
    return false;

  var node = row;
  tbody.removeChild(node);
  var newid = -1;
  
    // Loop through tr nodes and renumber - only rows numbered
    // higher than the row we just deleted need renumbering.
  
  row = tbody.firstChild;
  while (row) {
    if (row.tagName == 'TR') {
      newid++;
      if (newid >= rownum)
        rowrenumber(row, newid);
    }
    row = row.nextSibling;
  }
  if (numrows == 2) {  // 2 rows before deleting - only 1 left now, so 'hide' delete button
    var delbutton = document.getElementById('delete_1');
    //delbutton.innerHTML = ' ';
  }
}

</script>
<?php
$date_reg = ($obj->training_entry_date) ?  $obj->training_entry_date : date("Y-m-d");
$df = $AppUI->getPref('SHDATEFORMAT');

$entry_date = new CDate( $date_reg );
// Get clients list

?>
 <form name="editFrm" action="?m=training&training_id=<?php echo $training_id; ?>" method="post">
   <input type="hidden" name="dosql" value="do_newtraining_aed" />
   <input type="hidden" name="insert_id" value="<?php echo uniqid(""); ?>" />
   <input type="hidden" name="training[training_id]" value="<?php echo $training_id; ?>" />
   <input type="hidden" name="training[training_type]" value="<?php echo $obj->training_type; ?>" />
   <input type="hidden" name="training[training_entry_date]" value="<?php echo $entry_date->format( FMT_DATETIME_MYSQL ); ?>" />
   <table cellspacing="1" cellpadding="1" border="0" width="65%" class="std">
   <td align="left">
	<tr>
	  <td>
		<table>
			<tr>
			 <td align="left" nowrap="nowrap"><?php echo $AppUI->_('Center');?>: </td>
			  <td>
					<?php echo arraySelect( $clinics, 'training[training_clinic]', 'size="1" class="text"', @$obj->training_clinic ? $obj->training_clinic:0); ?>
			</td>
		   </tr>
	 <tr>
		<td align="left"><?php echo $AppUI->_('Training Name');?>:</td>
		<td align="left"><input type="text" class="text" name="training[training_name]" id="training_name" value="<?php echo dPformSafe(@$obj->training_name);?>" size="25" maxlength="255" /> </td>
     </tr>			   
   <tr>
		<td align="left"><?php echo $AppUI->_('Curriculum');?>:</td>
		<td align="left"><?php echo arraySelectRadio($types, "training[training_curriculum]", 'onclick=toggleButtons()', $obj->training_curriculum ? $obj->training_curriculum : -1, $identifiers ); ?></td>
     </tr>	   
	 <tr>
		<td align="left"><?php echo $AppUI->_('...Curriculum (Other)');?>:</td>
		<td align="left"><input type="text" class="text" name="training[training_curriculum_desc]" id="training_curriculum_desc" value="<?php echo dPformSafe(@$obj->training_curriculum_desc);?>" size="25" maxlength="255" /> </td>
     </tr>	 		 
	
	  <tr>
	  	<td align="left"><?php echo $AppUI->_('Notes');?>:</td>
		</tr>
		<tr>		
		<td colspan="2">
		<textarea cols="70" rows="2" class="textarea" name="training[training_notes]"><?php echo @$obj->training_notes;?></textarea>
		</td>
     </tr>
	</table>
	</td>
	</tr>   
   <tr>
   		<td><input type="button" value="<?php echo $AppUI->_('back');?>" class="button" onClick="javascript:history.back(-1);" /></td>
		<td colspan="5" align="right"><input type="button" name="btnFuseAction" value="<?php echo $AppUI->_('submit');?>" class="button" onClick="javascript:submitIt(document.editFrm)" /></td>
   </tr>
</td>
</table>
</form>