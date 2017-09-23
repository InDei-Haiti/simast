<?php
/**
 * Created by JetBrains PhpStorm.
 * User: stig
 * Date: 28.08.11
 * Time: 12:36:11
 * To change this template use File | Settings | File Templates.
 */

$mult_add = ($task_id > 0 ? '[]' : '');


$q = new DBQuery();
$q->addTable ( 'companies' );
$q->addQuery ( 'company_id,company_acronym' );
//$q->addWhere ( 'company_id in ('.$obj->project_cdonors.')' );
$companies = $q->loadHashList();
//$companies = implode(",", $companies);
// load the record data
//echo $obj->task_locations
$task_locations = array();
if($obj->task_locations)
	$task_locations = explode(",", $obj->task_locations);
?>

<table border="1" cellpadding="4" cellspacing="0" width="100%" 	class="std" style="border:white">
	<input name="dosql" type="hidden" value="do_task_aed" />
	<input name="task_id" type="hidden" value="<?php echo $task_id;?>" />
	<input name="task_project" type="hidden" value="<?php echo $task_project; ?>" />
	<tr valign="top" width="50%">
		<td>
		<table cellspacing="0" cellpadding="1" border="0" width="50%">
			<tr>
				<td style="white-space:no-wrap;">
					<?php echo $AppUI->_ ( 'Activity Name' );?> &nbsp;*
				</td>
				<td><input type="text" class="form-control" name="task_name"
					style='width: 230px;'
					value="<?php
					echo ($obj->task_name);
					?>" size="40"
					maxlength="255" /></td>
			</tr>
			<tr>
				<td style="white-space:no-wrap;"><?php echo $AppUI->_('Partners');?></td>
				<td>
<?php 
		//echo arraySelect( $companies, 'task_agency[]', 'id="pdonor" multiple="multiple" style="width: 230px;" class="text chosen" size="2" ', @explode(",",$row->project_donor ));
		echo "<br />".
		arraySelect ( $companies, 'task_agency[]', "style='width: 230px;' class='form-control' multiple='multiple'", @$obj->task_agency ? @explode(",",@$obj->task_agency) : '-1'/*@explode(",",$obj->task_sector)*/, false );
		?> 
					<script type="text/javascript">
						//var select = document.getElementById("pdonor");
						//var val="";
						
						<?php //if($obj->project_partners){?>
							//val = "<?php //echo $obj->project_partners?>";
							//val = val.split(',');
						<?php //}?>
						//if(val){
							//for(var i=0;i<select.length;i++){
							//	var value = select.options[i].value;
							//	for(var j=0;j<val.length;j++){
								//	if(value==val[j]){
								//		select.options[i].selected = true;
								//		break;
								//	}
								//}
							//}
						//}
					</script>
</td>
			</tr>
			<tr>
				<td>
					<?php echo $AppUI->_ ( 'Sector' ); ?>&nbsp;*
				</td>
				<td>
					<?php 
					//echo arraySelectCheckbox ( dPgetSysVal ( "TypeOfIntervention" ), 'activity_type_of_intervention[]', 'class=text size=1', @$row->activity_type_of_intervention || @$row->activity_type_of_intervention=='0' ? @$row->activity_type_of_intervention : '-1', null );
					//echo @$obj->task_sector;
					//arraySelect
					echo "<br />";
					echo 
						arraySelect ( $sector_list, "task_sector[]", "style='width: 230px;' class='form-control' multiple='multiple'", @$obj->task_sector ? @explode(",",@$obj->task_sector): '-1'/*@explode(",",$obj->task_sector)*/, false );
					echo "<br />";
					?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo $AppUI->_ ( 'Strategic Areas' ); ?>
				</td>
				<td>
					<input type="button" class="button ce pi ahr tree_edit" data-part="area" value="Select">
					<input type="hidden" name="task_areas" value="<?php echo $obj->task_areas ?>" class="button ce pi ahr">
					<span class="snote"><?php if($obj->task_areas) echo 'Strategic Areas selected';  ?></span>
				</td>

				<td>
					<?php echo $AppUI->_ ( 'Locations' ); ?>
				</td>
				<td>
					<input type="button" class="button ce pi ahr tree_edit" data-part="lox" value="Select">
					<input type="hidden" name="task_locations" value="<?php echo $obj->task_locations;?>" class="button ce pi ahr">
					<span class="snote"><?php if($obj->task_locations)echo 'Locations selected';?></span>
				</td>
			</tr>
			<br /><br />
			<tr>
				<td>
					<?php echo $AppUI->_ ( 'Status' ); ?>
				</td>
				<td>
					<?php
						echo "<br /><br />".arraySelect ( $status, 'task_status', 'size="1" style="width: 230px;" class="form-control"', $obj->task_status, true );
					?>
				</td>
			</tr>
			<tr>
				<td >
					<?php echo $AppUI->_ ( 'Type Of Beneficiary' ); ?>
				</td>
				<td>
					<?php 
					//echo arraySelectCheckbox ( dPgetSysVal ( "TypeOfIntervention" ), 'activity_type_of_intervention[]', 'class=text size=1', @$row->activity_type_of_intervention || @$row->activity_type_of_intervention=='0' ? @$row->activity_type_of_intervention : '-1', null );
						
					echo arraySelectCheckbox ( dPgetSysVal ( 'TypeOfBeneficiery' ), 'task_type_of_beneficiery[]', 'class=button ce pi ahr size=1', @$obj->task_type_of_beneficiery || @$obj->task_type_of_beneficiery=='0' ? @$obj->task_type_of_beneficiery : '-1' );
				?>
				</td>
			</tr>
			<tr>
				<td >
					<?php echo $AppUI->_ ( 'Beneficiaries' ); ?>
				</td>
				<td >
					<input type="text" class="form-control" name="task_beneficiares" style='width: 230px;'  value="<?php echo @$obj->task_beneficiares;	?>" size="10" maxlength="10" />
				</td>
			</tr>	


		</table>
		</td>
		<td>
		<table cellspacing="0" cellpadding="2" border="0" width="80%" style="border: white;">
			<tr>
					<td nowrap="nowrap">
						<div class="pretty_td"><?php echo $AppUI->_ ( 'Start Date' ); ?>&nbsp;*&nbsp;</div>
						<input type="text" readonly="readonly" style='min-width: 90px;' class="form-control dfield  " name="task_start_date" value="<?php echo $start_date ? $start_date->format ( $df ) : "";?>" />
						&nbsp;&nbsp;&nbsp;
					<?php
						echo "<br />".$AppUI->_ ( 'End Date' );
						?>&nbsp;*&nbsp;&nbsp;&nbsp;
						<input type="text" readonly="readonly" style='min-width: 90px;' class="form-control dfield  " name="task_end_date" value="<?php echo $end_date ? $end_date->format ( $df ) : "";?>" />
					</td>
				</tr>
			<tr>
				<td >
					<br /><div class="pretty_td"><?php echo $AppUI->_ ( 'Budget' ); ?>&nbsp;*
				<input type="text" class="form-control" name="task_target_budget" style='min-width: 90px;'
				           value="<?php echo @$obj->task_target_budget; ?>" size="10"  />&nbsp;&nbsp;&nbsp;</div>
				<?php echo $AppUI->_('Yearly');?>&nbsp;&nbsp;&nbsp;
				<input type="button" class="button ce pi ahr" onclick="annualBudget(this)" value="<?php echo $AppUI->_('Edit');?>"/>
				<input type="hidden" name='task_annual_budget' value="<?php echo $obj->task_annual_budget;?>" class="unq">
				</td>
			</tr>
			
			<tr>
				<td >
					<div class="pretty_td"><?php echo $AppUI->_ ( 'Description' ).'(1000 '.$AppUI->_('Chars max').')'; ?>:</div>
					<textarea name="task_description"  maxlength="1000" class="form-control" cols="28"
					rows="3" wrap="virtual"><?php
					echo @$obj->task_description;
					?></textarea> <br />
					<?php if($task_id == 0 && $_GET['m'] == 'projects'){
						echo '<input type="button" class="button ce pi ahr" value="'.$AppUI->_('Delete Activity').'" style="float: right;" onclick="killAct(this);">';
					}
					?>
			</td>
			</tr>


		</table>
		</td>
	</tr>
</table>

<?php
function taskAddon(){
	global $task_locations;
	?>
	<div id="bub" style="display: none;"></div>
	<div id="area_box" style="display: none;"><?php echo htmlAreasList();?></div>
	<div id="lox_box" style="display: none;"><?php echo buildLocations($task_locations);?></div>
	<div id="locations_skin" style="display: none;" class="ui-widget ui-widget-content ui-corner-all ui-movable">
		<button type="button" id="lox_shut" class="ui-multiselect ui-widget ui-state-default ui-corner-all" title="Save and Close"><span>Save and Close</span></button>
		<div id="land" style="padding:1px;"></div>
	</div>



<!--	Application Modal to select-->
	<div id="myModal" class="modal">

		<!-- Modal content -->
		<div class="modal-content">
			<div class="modal-header">
				<span class="close">&times;</span>
				<h2>Modal Header</h2>
			</div>
			<div class="modal-body">
				<p>Some text in the Modal Body</p>
				<div id="locations_skin"  class="ui-widget ui-widget-content ui-corner-all ui-movable">
					<button type="button" id="lox_shut" class="ui-multiselect ui-widget ui-state-default ui-corner-all" title="Save and Close"><span>Save and Close</span></button>
					<div id="land" style="padding:1px;"></div>
				</div>
				<p>Some other text...</p>
			</div>
			<div class="modal-footer">
				<h3>Modal Footer</h3>
			</div>
		</div>

	</div>

<!--	Application Modal to select-->
	<?php
	$thisYear = date("Y",time());
	$annuals = range(2000,2030); //range($thisYear,$thisYear+15);
	$sel_arr= array();
	for($i=0,$l = count($annuals);$i < $l; $i++){
		$sel_arr[]="<option value='".$annuals[$i]."'>".$annuals[$i].'</option>';
	}
	$bud_annual = '<select class="text yr">'.join("",$sel_arr).'</select>';
	echo '<div id="year_shelter" style="display: none;">',$bud_annual,'</div>';
}
?>