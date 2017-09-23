<?php

require_once($AppUI->getModuleClass("contacts"));
require_once($AppUI->getModuleClass("activity"));

$activity_id = intval( dPgetParam( $_GET, "activity_id", 0 ));
$activity_type = intval( dPgetParam( $_GET, "activity_curriculum", 3 ));

if (isset( $_GET['clientorderby'] )) {
    $clientorderdir = $AppUI->getState( 'ActivityClientIdxOrderDir' ) ? ($AppUI->getState( 'ActivityClientIdxOrderDir' )== 'asc' ? 'desc' : 'asc' ) : 'desc';
	$AppUI->setState( 'ActivityClientIdxOrderBy', $_GET['clientorderby'] );
    $AppUI->setState( 'ActivityClientIdxOrderDir', $clientorderdir);
}
$clientorderby  = $AppUI->getState( 'ActivityClientIdxOrderBy' ) ? $AppUI->getState( 'ActivityClientIdxOrderBy' ) : 'client_first_name';
$clientorderdir = $AppUI->getState( 'ActivityClientIdxOrderDir' ) ? $AppUI->getState( 'ActivityClientIdxOrderDir' ) : 'asc';

if 	($contact_unique_update == 0)
  $contact_unique_update = uniqid("");
	
$perms = & $AppUI->acl();

if ($activity_id)
  $canEdit = $perms->checkModuleItem( $m, "edit", $activity_id );
else
  $canEdit = $perms->checkModule($m, "add");

if (!$canEdit)
{
  $AppUI->redirect("m=public&a=access_denied");
}



$q  = new DBQuery;
$q->addTable('activity');
$q->addQuery('activity.*');
$q->addWhere('activity.activity_id = '.$activity_id);
$sql = $q->prepare();
$q->clear();

$obj = new CActivity();

if (!db_loadObject($sql, $obj) && ($activity_id > 0))
{
  	$AppUI->setMsg( "invalidID", UI_MSG_ERROR, true );
	$AppUI->redirect();
}



//load cities
$citiesArray = arrayMerge(array(-1=>'-Select City-'), dPgetSysVal('ClientCities'));

$genderTypes = dPgetSysVal('GenderType');
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

//load trainings

$q = new DBQuery();
$q->addTable('trainings', 't');
$q->addQuery('t.training_id, t.training_name');
$q->addOrder('t.training_name');
$activity_contacts = array();
$activity_caregivers = array();
$activity_clients = array();

$activities = arrayMerge(array(0=> '-Select Training -'),$q->loadHashList());
//load clients
if ($activity_id > 0)
{
    $q = new DBQuery();
	$q->addTable("activity_clients", "tc");
	$q->innerJoin("clients", "c", "c.client_id = tc.activity_clients_client_id");
	$q->addQuery("c.client_id");
	$q->addWhere("tc.activity_clients_activity_id = ". $activity_id);
	$activity_clients = $q->loadColumn();
	
}
$num_activity_clients = count($activity_clients);
//var_dump($activity_clients);
if ($activity_id > 0)
{
    $q = new DBQuery();
	$q->addTable("activity_contacts", "tc");
	$q->innerJoin("contacts", "c", "c.contact_id = tc.activity_contacts_contact_id");
	$q->addQuery("c.contact_id");
	$q->addWhere("tc.activity_contacts_activity_id = ". $activity_id);
	$activity_contacts = $q->loadColumn();
	
}
$num_activity_contacts = count($activity_contacts);

if ($activity_id > 0)
{
    $q = new DBQuery();
	$q->addTable("activity_caregivers", "ac");
	$q->innerJoin("caregiver_client", "cc", "cc.caregiver_id = ac.activity_caregivers_caregiver_id");
	$q->addQuery("cc.caregiver_id");
	$q->addWhere("ac.activity_caregivers_activity_id = ". $activity_id);
	$activity_caregivers = $q->loadColumn();
	
}
//load curriculum types
$types = dPgetSysVal('CurriculumTypes');
$type = $types[$activity_type];
//load staff for officer fields
// collect all the users for the nutritionist list
$q  = new DBQuery;
$q->addTable('contacts','con');
$q->leftJoin('users','u', 'u.user_contact = con.contact_id');
$q->addQuery('contact_id');
$q->addQuery('CONCAT_WS(", ",contact_last_name,contact_first_name)'); 
$q->addOrder('contact_last_name');
$owners = $q->loadHashList();

$owners = arrayMerge(array (0=>'-Select Officer-'), $owners);

//load status stuff
$status = arrayMerge(array(-1=>'-Select Current Client Status-'), dPgetSysVal('ClientStatus'));
//load priority stuff
$priority = arrayMerge(array(-1=>'-Select Current Client Priority-'), dPgetSysVal('ClientPriority'));

$cadresType=dPgetSysVal('CadresTrained');

$selected_clients = array();
//var_dump($selected_clients);
//var_dump($activity_id);
/*
if ($activity_id > 0) {
	$q =& new DBQuery;
	$q->addTable('activity_clients');
	$q->addQuery('activity_clients_client_id');
	$q->addWhere('activity_clients_activity_id = ' . $activity_id);
	$res =& $q->exec();
	for ( $res; ! $res->EOF; $res->MoveNext())
		$selected_clients[] = $res->fields['activity_clients_client_id'];
	$q->clear();
}
if ($activity_id == 0 && $client_id > 0){
	$selected_clients[] = "$client_id";
}*/

//var_dump($selected_clients);
//$ttl = "$type :: ";
//var_dump($selected_clients);
$ttl .= $activity_id > 0 ? "Edit Activity" : "New Activity";

$titleBlock = new CTitleBlock( $ttl, '', $m, "$m.$a" );
$titleBlock->addCrumb( "?m=activity", "Activities" );
$titleBlock->addCrumbRight2( "javascript:clearSelection(document.forms['editFrm'])", "Clear All Selections" );
if ($activity_id != 0)
  $titleBlock->addCrumb( "?m=activity&a=view&activity_id=$activity_id", "View" );
$titleBlock->show();

?>
<script language="javascript">
var selected_contacts_id = "<?php echo implode(',', $activity_contacts); ?>",
	contact_unique_update = "<?php echo $contact_unique_update; ?>",
    activity_id = '<?php echo $obj->activity_id;?>',
    activity_description_msg = "<?php echo $AppUI->_('Please enter a name for the activity');?>",
    selected_clients_id = "<?php echo implode(',', $activity_clients); ?>",
    selected_caregivers_id = "<?php echo implode(',', $activity_caregivers); ?>",
    calendarField = '',
    calWin = null,
    reallyNew = <?php echo ($obj->activity_id > 0 ? "false" : "true"); ?>;



function popCalendar( field ){
	calendarField = field;
	idate = eval( 'document.editFrm.log_' + field + '.value' );
	window.open( 'index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'top=250,left=250,width=251, height=220, scollbars=false, resizable' );
}

function setCalendar( idate, fdate ) 
{
	fld_date = eval( 'document.editFrm.log_' + calendarField );
	fld_fdate = eval( 'document.editFrm.activity_' + calendarField );
	fld_date.value = idate;
	fld_fdate.value = fdate;
}

function checkDate(){
           if (document.frmDate.log_start_date.value == "" || document.frmDate.log_end_date.value== ""){
                alert("<?php echo $AppUI->_('You must fill fields', UI_OUTPUT_JS) ?>");
                return false;
           } 
           return true;
}

function postStaff(part,str){	
	var ntab,upart=part,starr={clients: "0",contacts: "1",caregivers: "2"},oktest=new RegExp("^ok","g");
	if(part == "contacts"){
		upart="staff";
	}
	eval('selected_'+part+'_id="'+str+'";');
	var curr=starr[part];
	if(part != ''){
		$j.ajax({
			url		: '?m=activity&a=async_activity_aed&mode=stuff&suppressHeaders=1',
			data	: 'act_id='+activity_id+"&staff="+part+"&list="+str,
			type	: 'post',
			success : function (data){
				if(data.length > 0 ) {
					var res=$j.parseJSON(data);
					if(res.res == 'ok' && res.id > 0){
						activity_id=res.id;
						$j("#tab_"+curr).html($j("#img_load").html());
						$j.get('?m=activity&a=ae_'+upart+"&suppressHeaders=1&act_id="+activity_id,
							function(code){
								if(code && code.length > 1){
									$j("#tab_"+curr).delay(500).html(code);
								}
							}
						);
					}
				}
			} 

		});
	}
}

function popSelects(part){
	var brief=part.replace(/s$/,''),postVar;
	eval("postVar=selected_"+part+"_id;");
	if(part == "contacts"){
		brief='staff';
	} 
	window.open("./index.php?m=public&a="+brief+"_selector&dialog=1&call_back=postStaff&fpart="+part+"&selected_"+part+"_id="+postVar, part, "height=600,width=400,resizable,scrollbars=yes");
}

function cleanTails(){
	if(reallyNew === true && activity_id > 0){
		$j.get("?m=activity&a=async_activity_aed&suppressHeaders=1&mode=clean&act_id="+activity_id,function(data){
			history.back(-1);
		});		
	}else{
		history.back(-1);
	}
}

function popContacts() {
//        window.open('./index.php?m=public&a=selector&dialog=1&callback=setCompany&table=companies', 'company','left=50,top=50,height=250,width=400,resizable');
	window.open("./index.php?m=public&a=staff_selector&dialog=1&call_back=setContacts&selected_contacts_id="+selected_contacts_id, "contacts", "height=600,width=400,resizable,scrollbars=yes");
}

function setContacts( contact_id_string ){
	if(!contact_id_string){
		contact_id_string = "";
	}
	//alert(contact_id_string);
	document.editFrm.activity_contacts.value = contact_id_string;
}
function popCaregivers() {
//        window.open('./index.php?m=public&a=selector&dialog=1&callback=setCompany&table=companies', 'company','left=50,top=50,height=250,width=400,resizable');
	window.open("./index.php?m=public&a=caregiver_selector&dialog=1&call_back=setCaregivers&selected_caregivers_id="+selected_caregivers_id, "caregivers", "height=600,width=400,resizable,scrollbars=yes");
}

function setCaregivers( caregiver_id_string ){
	if(!caregiver_id_string){
		caregiver_id_string = "";
	}
	
	document.editFrm.activity_caregivers.value = caregiver_id_string;
}
function popClients() 
{
	window.open('./index.php?m=public&a=client_selector&dialog=1&call_back=setClients&selected_clients_id='+selected_clients_id, 'clients','height=600,width=400,resizable,scrollbars=yes');
}
function setClients(client_id_string){
	if(!client_id_string){
		client_id_string = "";
	}else{
		

	}
	//alert(client_id_string);
	document.editFrm.activity_clients.value = client_id_string;
	//selected_clients_id = client_id_string;
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
  $j(newrow)
  	.find("input").val("").end()
  	.find("#delete_1 #delete_"+newid).html("X");
  row.parentNode.appendChild(newrow);      // Attach to table
    // Clear out data from new row.
	
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

	var activity = 0;

   function ajaxAssign(f) 
   {
     var client = f.client.value;
	 activity = f.activity_id.value;
     //var url = "?m=companies&a=do_ajaxowner_update&user=" + escape(user)+"&company_id="+escape(company);
     var url = "modules/activity/do_ajaxclient_update.php?client=" + escape(client)+"&activity_id="+escape(activity);
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
				document.getElementById("client"+activity).innerHTML = showElements[x].childNodes[0].textContent;
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



</script>
<?php
$date_reg = ($obj->activity_entry_date) ?  $obj->activity_entry_date : date("Y-m-d");
$end_reg = ($obj->activity_end_date) ?  $obj->activity_end_date : date("Y-m-d");
$df = $AppUI->getPref('SHDATEFORMAT');
//var_dump($date_reg);
$entry_date = new CDate( $date_reg );
$end_date = new CDate( $end_reg );
//var_dump($entry_date);


//load facilitators
if ($activity_id > 0)
{
	$q = new DBQuery();
	$q->addTable("activity_facilitator");
	$q->addQuery("activity_facilitator.*");
	$q->addWhere("activity_facilitator.facilitator_activity_id = " . $obj->activity_id);
	$rows = $q->loadList();
}
// Get clients list

?>
 <form name="editFrm" action="?m=activity&activity_id=<?php echo $activity_id; ?>" method="post">
   <input type="hidden" name="dosql" value="do_newactivity_aed" />
   <input type="hidden" name="insert_id" value="<?php echo uniqid(""); ?>" />
   <input type="hidden" name="activity_num_rows" value="<?php echo count($rows); ?>" />
   <input type="hidden" name="activity[activity_id]" id="act_id" value="<?php echo $activity_id; ?>" />
   <input type="hidden" name="activity[activity_type]" value="<?php echo $activity_type; ?>" />
   <input type="hidden" name="activity[activity_clients]" id="activity_clients" value="<?php echo implode(",",$activity_clients); ?>" />
   <input type="hidden" name="activity[activity_contacts]" id="activity_contacts" value="<?php echo implode(",",$activity_contacts); ?>" />
   <input type="hidden" name="activity[activity_caregivers]" id="activity_caregivers" value="<?php echo implode(",",$activity_caregivers); ?>" />
   <input type="hidden" name="activity[activity_entry_date]" value="<?php echo $entry_date->format( FMT_DATETIME_MYSQL ); ?>" />
   <!--<input type="hidden" name="counselling[counselling_entry_date]" value="<?php //echo $intake_date ? $intake_date->format( FMT_DATETIME_MYSQL ) : " "; ?>" />-->
   <table cellspacing="1" cellpadding="1" border="0" width="100%" class="std">
   <td align="left">
	<tr>
	  <td>
		<table>
			<tr>
			 <td align="left" nowrap="nowrap"><?php echo $AppUI->_('Center');?>: </td>
			  <td>
					<?php echo arraySelect( $clinics, 'activity[activity_clinic]', 'size="1" class="text"', @$obj->activity_clinic ? $obj->activity_clinic:0); ?>
			</td>
		   </tr>
		<tr>
			<td align="left" nowrap="nowrap"><?php echo $AppUI->_('Start Date');?>: </td>
			<td>
				
				<?php 
					echo drawDateCalendar('activity[activity_date]',$entry_date ? $entry_date->format($df) : '' );
					/*
					 *<input type="hidden" name="log_entry_date" value="<?php echo $entry_date ? $entry_date->format( FMT_TIMESTAMP_DATE ) : "" ;?>" />
					 *<input type="text" name="activity[activity_date]" value="<?php echo $entry_date ? $entry_date->format( $df ) : "" ;?>" class="text" readonly="readonly"/>
						<a href="#" onClick="popCalendar('entry_date')">
						<img src="./images/calendar.png" width="16" height="16" alt="<?php echo $AppUI->_('Calendar');?>" border="0" ></a> 
					 */
				?>				
			</td>
		</tr>		
		<tr>
			<td align="left" nowrap="nowrap"><?php echo $AppUI->_('End Date');?>: </td>
			<td>
			<?php 
				echo drawDateCalendar("activity[activity_end_date]",$end_date ? $end_date->format( $df ) : "");
				/*
				 *<input type="hidden" name="log_end_date" value="<?php echo $entry_date ? $end_date->format( FMT_TIMESTAMP_DATE ) : "" ;?>" />
				<input type="text" name="activity[activity_end_date]" value="<?php echo $end_date ? $end_date->format( $df ) : "" ;?>" class="text" readonly="readonly"/>
				<a href="#" onClick="popCalendar('end_date')">
				<img src="./images/calendar.png" width="16" height="16" alt="<?php echo $AppUI->_('Calendar');?>" border="0" ></a> 
				 * 
				 */
			?>				
			</td>
		</tr>
		<tr>
			<td align="left"><?php echo $AppUI->_('Hours per Day');?>:</td>
			<td align="left"><input type="text" class="text" name="activity[activity_hpd]" id="activity_hpd" value="<?php echo dPformSafe(@$obj->activity_hpd);?>" size="25" maxlength="255" /> </td>
     	</tr>
	 <tr>
		<td align="left"><?php echo $AppUI->_('Activity Name');?>:</td>
		<td align="left"><input type="text" class="text" name="activity[activity_description]" id="activity_description" value="<?php echo dPformSafe(@$obj->activity_description);?>" size="25" maxlength="255" /> </td>
     </tr>
	 <tr>
		<td align="left"><?php echo $AppUI->_('No. of Persons (Male)');?>:</td>
		<td align="left"><input type="text" class="text" name="activity[activity_male_count]" id="activity_male_count" value="<?php echo dPformSafe(@$obj->activity_male_count);?>" size="25" maxlength="255" /> </td>
     </tr>	 		 
     <tr>
		<td align="left"><?php echo $AppUI->_('No. of Persons (Female)');?>:</td>
		<td align="left"><input type="text" class="text" name="activity[activity_female_count]" id="activity_female_count" value="<?php echo dPformSafe(@$obj->activity_female_count);?>" size="25" maxlength="255" /> </td>
     </tr>	 		   
     <tr>
		<td align="left"><?php echo $AppUI->_('Total');?>:</td>
		<td align="left"><input type="text" class="text" name="activity[activity_visiters_total]" id="activity_visiters_total" value="<?php echo dPformSafe(@$obj->activity_visiters_total);?>" size="25" maxlength="255" /> </td>
     </tr>
     <tr>
		<td align="left"><?php echo $AppUI->_('Cadres Trained');?>:</td>
		<td align="left">
			<?php echo arraySelectCheckbox($cadresType,'activity_cadres[]','',$obj->activity_cadres,$identifiers); 	?> 
		</td>
     </tr>
     
      <tr>
         <td align="left" class="std" colspan="2">
		 <table>
		   <tr> 
		    <td>
				 <table id="facilitators">
				     <th><?php echo $AppUI->_('Training');?></th>				     
					 <th><?php echo $AppUI->_('Facilitator');?></th>
					 <th><?php echo $AppUI->_('Topics');?></th>					 
					 <th>&nbsp;</th>
					 
					 <?php 
					 $rowcount = 1;
					 if (count($rows) > 0 )
					 {
						foreach ($rows as $row)
						{
							
					 ?>
					 <tr>
						 <td align="left">
						 <input type="hidden" name="facilitator_id_<?php echo $rowcount; ?>" value="<?php echo @$row["facilitator_id"]?>" />
						  <?php echo arraySelect( $activities, "trainingid_$rowcount", 'size="1" class="text" id="trainingid_'.$rowcount.'"', @$row["facilitator_training_id"] ); ?>
						 </td>
						 <td align="left">
						 <input type="text" class="text" id="facilitator_<?php echo $rowcount; ?>" name="facilitator_<?php echo $rowcount; ?>" value="<?php echo @$row["facilitator_name"];?>" maxlength="150" size="20" />
						 </td>
						 <td align="left">
						 <input type="text" class="text" id="topic_<?php echo $rowcount; ?>" name="topic_<?php echo $rowcount; ?>" value="<?php echo @$row["facilitator_topic"];?>" maxlength="150" size="20" />
						 </td>
						 
						  <td align="left">
				              <span id="delete_<?php echo $rowcount; ?>" style="color:red; cursor: pointer;" onclick="DeleteRow(this);">X</span>
				         </td>
					 </tr>
					 <?php 
						 $rowcount++;	
					     } //end for
					  }
					  else
					  {
					  ?>
					  <tr>
						 <td align="left">
						 <input type="hidden" name="facilitator_id_<?php echo $rowcount; ?>" value="<?php echo @$row["facilitator_id"]?>" />
						 <?php echo arraySelect( $activities, "trainingid_$rowcount", 'size="1" class="text" id="trainingid_'.$rowcount.'"', @$row["facilitator_training_id"] ); ?>						 
						 </td>
						 <td align="left">
						 <input type="text" class="text" id="facilitator_<?php echo $rowcount; ?>" name="facilitator_<?php echo $rowcount; ?>" value="<?php echo @$row["facilitator_name"];?>" maxlength="150" size="20" />
						 </td>
						 <td align="left">
						 <input type="text" class="text" id="topic_<?php echo $rowcount; ?>" name="topic_<?php echo $rowcount; ?>" value="<?php echo @$row["facilitator_topic"];?>" maxlength="150" size="20" />
						 </td>
						 
						 <td align="left">
				              <span id="delete_<?php echo $rowcount; ?>" style="color:red; cursor: pointer;" onclick="DeleteRow(this);">X</span>
				         </td>
					 </tr>
					  <?php
					  }//end if
					 ?>
				</table>
			  </td>
            </tr>			  
		 <tr>
			<td>
				<input class="button" type="button" name="append" value="new entry" onclick="AppendRow('facilitators'); return false;"/>
			</td>
		</tr>
		 </table>
		 <?php
            /*if ($AppUI->isActiveModule('relatives') && $perms->checkModule('relatives', 'view')) 
		{
			echo "<input type='button' class='button' value='".$AppUI->_("enter household info...")."' onclick='javascript:popRelatives();' />";
		}*/
		?>
		 </td>
	  </tr>		   
	  <tr>
	  	<td align="left"><?php echo $AppUI->_('Notes');?>:</td>
		</tr>
		<tr>		
		<td colspan="2">
		<textarea cols="70" rows="2" class="textarea" name="activity[activity_notes]"><?php echo @$obj->activity_notes;?></textarea>
		</td>
     </tr>
	</table>
	</td>
	</tr>   
   <tr>
   		<td><input type="button" value="<?php echo $AppUI->_('back');?>" class="button" onClick="cleanTails();" /></td>
		<td colspan="5" align="right"><input type="button" name="btnFuseAction" value="<?php echo $AppUI->_('submit');?>" class="button" onClick="javascript:submitIt(document.editFrm)" /></td>
   </tr>

<?php
	if (isset($_GET['tab']))
		$AppUI->setState('ActivityAeTabIdx', dPgetParam($_GET, 'tab', 0));

		$moddir = $dPconfig['root_dir'] . '/modules/activity/';	
	
	$tab = $AppUI->getState('ActivityAeTabIdx', 0);
	$tabBox =& new CTabBox("?m=activity&a=addedit&activity_id=$activity_id", "", $tab, "");
	$tabBox->add($moddir . "ae_clients", "Clients");
	$tabBox->add($moddir . "ae_staff", "Staff");
	$tabBox->add($moddir . "ae_caregivers", "Caregivers");
	//$tabBox->add($moddir . "ae_contacts", "Contacts");
	$tabBox->loadExtras('activity', 'add');
	$tabBox->show('', true);
?>
</td>
</table>
</form>
<div id="img_load" style="display:none;">
<img alt="Loading" src="images/tab_load.gif">
</div>