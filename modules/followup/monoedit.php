<?php
require_once $AppUI->getModuleClass('followup');

function breakOut(){
    global $AppUI;
	$AppUI->setMsg( "invalidID", UI_MSG_ERROR, true );
	$AppUI->redirect();    
}

if((int)$_GET['fid'] > 0){
	$ufid=(int)$_GET['fid'];
	$q= new DBQuery();
	$q->addWhere('followup_id='.$ufid);
	$q->addTable('followup_info');
	$q->addQuery('followup_info.*');
	$q->setLimit(1);
	$sql=$q->prepare();
	
	$obj = new CFollowUp();
	if (!db_loadObject( $sql, $obj ) && $ufid > 0){
	    breakOut();
	}	
	
}else{
	breakOut();
}

global $issues;

// collect all the users for the staff list
$q  = new DBQuery;
$q->addTable('contacts','con');
$q->leftJoin('users','u', 'u.user_contact = con.contact_id');
$q->addQuery('contact_id');
$q->addQuery('CONCAT_WS(", ",contact_first_name,contact_last_name)'); 
$q->addOrder('contact_last_name');
$owners = $q->loadHashList();

//load clinics
$q->clear();
$q->addTable('clinics', 'c');
$q->addQuery('c.clinic_id, c.clinic_name');
$q->addOrder('c.clinic_name');
$clinics = $q->loadHashList();

$clinics = arrayMerge(array('-1'=>'-- Select --'),$clinics);

$clientTypes = arrayMerge (array('-1'=>'-- Select --'), dPgetSysVal('FollowClientType'));
$servTypes = dPgetSysVal('FollowServices');
$visitTypes = dPgetSysVal('FollowVisitType');

$edate = new Date($obj->followup_date);
$df = $AppUI->getPref('SHDATEFORMAT');

$staff = makeListPerson($obj->followup_adm_no);
$selstaff.='<select name="followup_object">';
$ilister = new lister('follow','followup',$issues,'issues',$obj->followup_issues);
foreach ( $staff as $sname => $svar ) {
	$zstr = '';
	if (is_array ( $svar ) ){
		if( count ( $svar ) > 0)  {
			foreach ( $svar as $vtit => $v1 ) {
				if(($sname === 'child' && $vtit === 0) || $sname !== 'child'){
					if ($v1 == $obj->followup_object) {
						$zstr .= 'selected="selected"';
					} else {
						$zstr = '';
					}
					$selstaff .= '<option ' . $zstr . ' value="' . $v1 . '">' . $v1 . '</option>';
				}
			}
		}
	} else if($sname !='client_id'){
		if ($svar == $obj->followup_object) {
			$zstr .= 'selected="selected"';
		}
		$selstaff .= '<option ' . $zstr . ' value="' . $svar . '">' . $svar . '</option>';
	}
}
$selstaff.='</select>';

?>
<form method="post" action="?m=followup">
	<input type="hidden" name="dosql" value="do_followup_aed" />
	<input type="hidden" name="followup_id" value="<?php echo $obj->followup_id;?>">
	<input type="hidden" name="followup_adm_no" value="<?php echo $obj->followup_adm_no;?>">
	<input type="hidden" name="followup_client_id" value="<?php echo $obj->followup_client_id;?>"> 

<table class="std" border="0" width="100%">
	<tr>
		<td>
		<table id="ftab">
			<tr>
				<td align="left"><?php echo $AppUI->_('Counsellor');?>:</td>
				<td align="left">
		<?php echo arraySelect( $owners, 'followup_officer_id', 'size="1" class="text"', @$obj->followup_officer_id ? $obj->followup_officer_id:-1); ?>
	</td>
			</tr>
			<tr>
				<td align="left"><?php echo $AppUI->_('Center');?>:</td>
				<td align="left">
		<?php echo arraySelect( $clinics, 'followup_center_id', 'size="1" class="text"', @$obj->followup_center_id ? $obj->followup_center_id:-1); ?>
	</td>
	</tr>
	<tr>
				<td align="left"><?php echo $AppUI->_('Date');?>: </td>
				<td align="left">
		<?php echo  drawDateCalendar('followup_date',($edate ? $edate->format( $df ) : ""),false,20);?>
	 </td>
	</tr>
		<tr>
				<td align="left"><?php echo $AppUI->_('Adm No');?>:</td>
				<td align="left"><input type="text" class="text"
					value="<?php echo $obj->followup_adm_no;?>"
					maxlength="150" size="20" disabled readonly="readonly" /></td>
			</tr>
			<tr>
				<td align="left"><?php echo $AppUI->_('Client Name');?>:</td>
				<td align="left">
		<?php echo $selstaff; ?>
	</td>
			</tr>
			<tr>
				<td align="left"><?php echo $AppUI->_('Client Type');?>:</td>
				<td align="left">
		<?php echo arraySelect( $clientTypes, 'followup_client_type', 'size="1" class="text"', @$obj->followup_client_type ? $obj->followup_client_type:-1); ?>
	</td>
			</tr>
			<tr>
				<td align="left"><?php echo $AppUI->_('Visit Type');?>:</td>
				<td align="left">
		<?php echo arraySelect( $visitTypes, 'followup_visit_type', 'size="1" class="text"', @$obj->followup_visit_type ? $obj->followup_visit_type:-1); ?>
	</td>
			</tr>
			<tr>
				<td align="left"><?php echo $AppUI->_('Issues');?>:</td>
				<td align="left">
		<?php echo $ilister->build(); ?>
	</td>
			</tr>
			<tr>
				<td align="left">...<?php echo $AppUI->_('Other');?>:</td>
				<td align="left"><input type="text" class="text"
					name="followup_issues_notes"
					value="<?php echo $obj->followup_issues_notes;?>" maxlength="150"
					size="20" /></td>
			</tr>
			<tr>
				<td align="left"><?php echo $AppUI->_('Services');?>:</td>
				<td align="left">
		<?php echo arraySelectCheckbox($servTypes,'followup_service[]','',$obj->followup_service); ?>
	</td>
			</tr>
			<tr>
				<td align="left">...<?php echo $AppUI->_('Other');?>:</td>
				<td align="left"><input type="text" class="text"
					name="followup_service_notes"
					value="<?php echo $obj->followup_service_notes;?>" maxlength="150"
					size="20" /></td>
			</tr>

			<tr>
				<td align="left"><input type="button" class="button"
					onclick="history.back(-1);" value="Back"></td>
				<td align="right"><input type="submit" value="Submit" class="button"></td>
			</tr>


		</table>
		</td>
	</tr>
</table>
</form>
