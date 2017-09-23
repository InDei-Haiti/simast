<?php
global $AppUI,$dPconfig,$loadFromTab, $tab;
global $obj, $client_id, $url,$can_edit_contact_information;
global $convert;

require_once ($AppUI->getModuleClass('counsellinginfo'));


$perms = & $AppUI->acl();

$canEdit = true;
$msg = '';


$boolTypes = dPgetSysVal('YesNo');
$birthPlaces = dPgetSysVal('BirthPlaceType');
$birthTypes = dPgetSysVal('BirthType');
$ageTypes = dPgetSysVal('AgeType');
$awareStages = dPgetSysVal('StatusAwareType');
$rapidResultsType = dPgetSysVal('RapidResultsType');
$elisaResultsType = dPgetSysVal('ElisaResultsType');
$pcrResultsType = dPgetSysVal('PCRResultsType');
$posRef = dPgetSysVal('PositiveReferral');

if (isset($client_id) && $client_id > 0)
{
	$sql = 'SELECT * FROM counselling_info WHERE counselling_client_id = ' . $client_id;

	$counsellingObj = new CCounsellingInfo();	
	db_loadObject( $sql, $counsellingObj);
}
$identifiers = array(0=>'', 1=>'on_bs', 2=>'not_on_bs');

$date_reg = date("Y-m-d");
$entry_date = intval( $date_reg) ? new CDate( dPgetParam($_REQUEST, "counselling_entry_date", date("Y-m-d") ) ) : null;
$df = $AppUI->getPref('SHDATEFORMAT');
$rapid18_date = intval( $counsellingObj->counselling_rapid18_date) ? new CDate( $counsellingObj->counselling_rapid18_date ) : null;
$determine_date = intval( $counsellingObj->counselling_determine_date) ? new CDate( $counsellingObj->counselling_determine_date ) : null;
$bioline_date = intval( $counsellingObj->counselling_bioline_date) ? new CDate( $counsellingObj->counselling_bioline_date ) : null;
$unigold_date = intval( $counsellingObj->counselling_unigold_date) ? new CDate( $counsellingObj->counselling_unigold_date ) : null;
$elisa_date = intval( $counsellingObj->counselling_elisa_date) ? new CDate( $counsellingObj->counselling_elisa_date ) : null;
$pcr1_date = intval( $counsellingObj->counselling_pcr1_date) ? new CDate( $counsellingObj->counselling_pcr1_date ) : null;
$pcr2_date = intval( $counsellingObj->counselling_pcr2_date) ? new CDate( $counsellingObj->counselling_pcr2_date ) : null;
$rapid12_date = intval( $counsellingObj->counselling_rapid12_date) ? new CDate( $counsellingObj->counselling_rapid12_date ) : null;
$other_date = intval( $counsellingObj->counselling_other_date) ? new CDate( $counsellingObj->counselling_other_date ) : null;
?>
<script language="javascript">

var client_id = "<?php echo $counsellingObj->counselling_client_id;?>";

function toggleButtons()
{
	client_on_bs = document.getElementById('on_bs');
	client_not_on_bs = document.getElementById('not_on_bs');
	building_solution = document.getElementById('building_solution');
	
	building_solution.disabled = true;
	
	if ((!client_on_bs.checked) && (!client_not_on_bs.checked))
	{
		building_solution.disabled = true;
	}
	
	if ((client_on_bs.checked) || (selected_bs_id > 0))
	{
		building_solution.disabled = false;
	}
    	
}
</script>
<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">
<form name="counsellingInfoFrm" action="?m=clients&a=addedit&client_id=<?php echo $client_id; ?>" method="post">
  <input type="hidden" name="dosql" value="do_newclient_aed"/>
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="counselling_unique_update" value="<?php echo uniqid("");?>" />
  <input type="hidden" name="counselling[counselling_id]" value="<?php echo $counsellingObj->counselling_id;?>" />
  <input type="hidden" name="counselling[counselling_client_id]" value="<?php echo $client_id;?>" />

<tr>
    <td width="75%" valign="top">
      <table border="0" cellpadding = "1" cellspacing="1">
	   <tr>
		<th align="left"><?php echo $AppUI->_('Test');?>:</th>
		<th>&nbsp;</th>
		<th align="left"><?php echo $AppUI->_('Date');?>:</th>
		<th align="left"><?php echo $AppUI->_('Result');?>:</th>
	 </tr>
	 <tr>
		<td align="left"><?php echo $AppUI->_('Determine');?>:</td>
		<td>&nbsp;</td>
		<td align="left">&nbsp;&nbsp;
			<?php 
				echo drawDateCalendar("counselling[counselling_determine_date]",$determine_date ? $determine_date->format($df) : "",false, 'id="counselling_determine_date"');
				//<input type="text" class="text" name="counselling[counselling_determine_date]" id="counselling_determine_date" value="<?php echo $determine_date ? $determine_date->format($df) : "";" maxlength="150" size="20"/>&nbsp;dd/mm/yyyy
			?>			
		</td>
		<td align="left">&nbsp;&nbsp;
		<?php echo arraySelectRadio($rapidResultsType, "counselling[counselling_determine]", 'onclick=toggleButtons()', $counsellingObj->counselling_determine ? $counsellingObj->counselling_determine : -1 ); ?>
		</td>
	 </tr>
	 <tr>
		<td align="left"><?php echo $AppUI->_('Bio-line');?>:</td>
		<td>&nbsp;</td>
		<td align="left">&nbsp;&nbsp;
			<?php 
				echo drawDateCalendar("counselling[counselling_bioline_date]" ,$bioline_date ? $bioline_date->format($df) : "",false,'id="counselling_bioline_date"');
				//<input type="text" class="text" name="counselling[counselling_bioline_date]" id="counselling_bioline_date" value="<?php echo $bioline_date ? $bioline_date->format($df) : "";" maxlength="150" size="20"/>&nbsp;dd/mm/yyyy</td>
			?>		
		<td align="left">&nbsp;&nbsp;
		<?php echo arraySelectRadio($rapidResultsType, "counselling[counselling_bioline]", 'onclick=toggleButtons()', $counsellingObj->counselling_bioline ? $counsellingObj->counselling_bioline : -1 ); ?>

		</td>
	 </tr>
	 <tr>
		<td align="left"><?php echo $AppUI->_('Uni-gold');?>:</td>
		<td>&nbsp;</td>
		<td align="left">&nbsp;&nbsp;
		<?php 
			echo drawDateCalendar("counselling[counselling_unigold_date]",$unigold_date ? $unigold_date->format($df) : "",false, 'id="counselling_unigold_date"');
			//<input type="text" class="text" name="counselling[counselling_unigold_date]" id="counselling_unigold_date" value="<?php echo $unigold_date ? $unigold_date->format($df) : "";" maxlength="150" size="20"/>&nbsp;dd/mm/yyyy</td>
		?>			
		<td align="left">&nbsp;&nbsp;
		<?php echo arraySelectRadio($rapidResultsType, "counselling[counselling_unigold]", 'onclick=toggleButtons()', $counsellingObj->counselling_unigold ? $counsellingObj->counselling_unigold : -1 ); ?>
		</td>
	 </tr>
	 <tr>
		<td align="left"><?php echo $AppUI->_('ELISA');?>:</td>
		<td>&nbsp;</td>
		<td align="left">&nbsp;&nbsp;
			<?php 
				echo drawDateCalendar("counselling[counselling_elisa_date]",$elisa_date ? $elisa_date->format($df) : "",false, 'id="counselling_elisa_date"');
				//<input type="text" class="text" name="counselling[counselling_elisa_date]" id="counselling_elisa_date" value="<?php echo $elisa_date ? $elisa_date->format($df) : "";" maxlength="150" size="20"/>&nbsp;dd/mm/yyyy</td>
			?>		
		<td align="left">&nbsp;&nbsp;
		<?php echo arraySelectRadio($elisaResultsType, "counselling[counselling_elisa]", 'onclick=toggleButtons()', $counsellingObj->counselling_elisa ? $counsellingObj->counselling_elisa : -1 ); ?>
		</td>
	 </tr>
	 <tr>
		<td align="left"><?php echo $AppUI->_('PCR1');?>:</td>
		<td>&nbsp;</td>
		<td align="left">&nbsp;&nbsp;
		<?php 
			echo drawDateCalendar("counselling[counselling_pcr1_date]",$pcr1_date ? $pcr1_date->format($df) : "",false,'id="counselling_pcr1_date"');
			//<input type="text" class="text" name="counselling[counselling_pcr1_date]" id="counselling_pcr1_date" value="<?php echo $pcr1_date ? $pcr1_date->format($df) : "";" maxlength="150" size="20"/>&nbsp;dd/mm/yyyy</td>
		?>		
		<td align="left">&nbsp;&nbsp;
		<?php echo arraySelectRadio($pcrResultsType, "counselling[counselling_pcr1]", 'onclick=toggleButtons()', $counsellingObj->counselling_pcr1 ? $counsellingObj->counselling_pcr1 : -1 ); ?>
		</td>
	 </tr>
	 <tr>
		<td align="left"><?php echo $AppUI->_('PCR2');?>:</td>
		<td>&nbsp;</td>
		<td align="left">&nbsp;&nbsp;
		<?php 
			echo drawDateCalendar("counselling[counselling_pcr2_date]",$pcr2_date ? $pcr2_date->format($df) : "",false,'id="counselling_pcr2_date"');
			//<input type="text" class="text" name="counselling[counselling_pcr2_date]" id="counselling_pcr2_date" value="<?php echo $pcr2_date ? $pcr2_date->format($df) : "";" maxlength="150" size="20"/>&nbsp;dd/mm/yyyy</td>
		?>		
		<td align="left">&nbsp;&nbsp;
		<?php echo arraySelectRadio($pcrResultsType, "counselling[counselling_pcr2]", 'onclick=toggleButtons()', $counsellingObj->counselling_pcr2 ? $counsellingObj->counselling_pcr2 : -1 ); ?>
		</td>
	 </tr>
	 <tr>
		<td align="left"><?php echo $AppUI->_('Rapid @ 12 months');?>:</td>
		<td>&nbsp;</td>
		<td align="left">&nbsp;&nbsp;
		<?php 
			echo drawDateCalendar("counselling[counselling_rapid12_date]",$rapid12_date ? $rapid12_date->format($df) : "",false, 'id="counselling_rapid12_date"');
			//<input type="text" class="text" name="counselling[counselling_rapid12_date]" id="counselling_rapid12_date" value="<?php echo $rapid12_date ? $rapid12_date->format($df) : ""; " maxlength="150" size="20"/>&nbsp;dd/mm/yyyy</td>
		?>		
		<td align="left">&nbsp;&nbsp;
		<?php echo arraySelectRadio($rapidResultsType, "counselling[counselling_rapid12]", 'onclick=toggleButtons()', $counsellingObj->counselling_rapid12 ? $counsellingObj->counselling_rapid12 : -1 ); ?>
		</td>
	 </tr>
	 <tr>
		<td align="left"><?php echo $AppUI->_('Rapid @ 18 months');?>:</td>
		<td>&nbsp;</td>
		<td align="left">&nbsp;&nbsp;
		<?php 
			echo drawDateCalendar("counselling[counselling_rapid18_date]",$rapid18_date ? $rapid18_date->format($df) : "",false, 'id="counselling_rapid18_date"');
			//<input type="text" class="text" name="counselling[counselling_rapid18_date]" id="counselling_rapid18_date" value="<?php echo $rapid18_date ? $rapid18_date->format($df) : "";" maxlength="150" size="20"/>&nbsp;dd/mm/yyyy</td>
		?>		
		<td align="left">&nbsp;&nbsp;
		<?php echo arraySelectRadio($rapidResultsType, "counselling[counselling_rapid18]", 'onclick=toggleButtons()', $counsellingObj->counselling_rapid18 ? $counsellingObj->counselling_rapid18 : -1 ); ?>
		</td>
	 </tr>
	 <tr>
		<td align="left"><?php echo $AppUI->_('Other');?>:
		</td>		
		<td align="left">&nbsp;&nbsp;<input type="text" class="text" name="counselling[counselling_other_notes]" id="counselling_other_notes" value="<?php echo $counsellingObj->counselling_other_notes;?>" maxlength="150" size="20"/>
		</td>
		<td align="left">&nbsp;&nbsp;
			<?php 
				echo drawDateCalendar("counselling[counselling_other_date]",$other_date ? $other_date->format($df) : "",false, 'id="counselling_other_date"');
				//<input type="text" class="text" name="counselling[counselling_other_date]" id="counselling_other_date" value="<?php echo $other_date ? $other_date->format($df) : "";" maxlength="150" size="20"/>&nbsp;dd/mm/yyyy
			?>			
		</td>
		<td align="left">&nbsp;&nbsp;<input type="text" class="text" name="counselling[counselling_other]" id="counselling_other" value="<?php echo $counsellingObj->counselling_other;?>" maxlength="150" size="20"/></td>	
	 </tr>
	 <tr>
		<td align="left"><?php echo $AppUI->_('Final Results');?>:</td>
		<td align="left" colspan="3">&nbsp;&nbsp;
			<?php echo arraySelectRadio($pcrResultsType, "counselling[counselling_final]", 'onclick=toggleButtons()', $counsellingObj->counselling_final ? $counsellingObj->counselling_final : -1 ); ?>
		</td>
	 </tr>
	 <tr>
		<td align="left"><?php echo $AppUI->_('Discordant Couple');?>:</td>
		<td align="left" colspan="3">&nbsp;&nbsp;
			<?php echo arraySelectRadio($boolTypes, "counselling[counselling_dis_couple]", 'onclick=toggleButtons()', $counsellingObj->counselling_dis_couple ? $counsellingObj->counselling_dis_couple : -1 ); ?>
		</td>
	 </tr>
	 <tr>
		<td align="left"><?php echo $AppUI->_('If positive, referred to');?>:</td>
		<td align="left" colspan="3">&nbsp;&nbsp;
			<?php echo arraySelectRadio($posRef, "counselling[counselling_positive_ref]", 'onclick=toggleButtons()', $counsellingObj->counselling_positive_ref ? $counsellingObj->counselling_positive_ref : -1 ); ?>
		</td>
	 </tr>
	 <tr>
		<td align="left">...<?php echo $AppUI->_('Other (specify)');?>:</td>
		<td align="left" colspan="3">&nbsp;&nbsp;
			<input type="text" class="text" name="counselling[counselling_positive_ref_notes]" id="counselling_positive_ref_other" value="<?php echo $counsellingObj->counselling_positive_ref_notes ;?>" maxlength="150" size="20"/>
		</td>
	 </tr>
   

      
	  
	</table>
    </td>


	</table>
 </td>
</tr>
</form>
</table>
<script language="javascript">
 subForm.push(new FormDefinition(<?php echo $tab;?>, document.counsellingInfoFrm, checkDetail, saveCounsellingInfo));
</script>