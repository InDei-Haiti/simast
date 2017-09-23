<?php
global $useID,$idIns,$wz,$sections,$tab;
//var_dump($sections[0]);
$blistsec = "<table border='0' cellpadding='2' cellspacing='2' class='tbl usub'>
					<thead><tr>";

$headsind = 0;
$fld = $sections[$tab];
foreach ($fld['subs'] as &$fsub) {
	$headsind++;
	$blistsec .= "<th>" . $fsub['name'] . "</th>";
	/* if($xmode==='view')
	 $pdfhtml .= "<th>" . $fsub['name'] . "</th>"; */
}
$blistsec .= ($xmode !== 'view' ? '<th>&nbsp;</th>' : '') . '</tr></thead><tbody>';
//$pdfhtml .= '</tr></thead><tbody>';
if ($useID > 0) {
	$sql = 'select * from ' . $fld['table'] . ' where wf_id="' . $idIns . '"';
	$res = mysql_query($sql);
	if ($res && mysql_num_rows($res) > 0) {
		while ($srow = mysql_fetch_assoc($res)) {
			$subRowSet[] = $srow;
		}
	}
}
//echo $sql;
//var_dump($subRowSet);
	
$fldprefix = str_replace('fld_', '', $fld['dbfld']);
$tlist = '';
if (count($subRowSet) > 0) {
	$preI = $wz->preIndex();
	foreach ($subRowSet as $sy => &$srset) {
		$tlist .= '<tr>';
		$wz->postIndex($preI);
		foreach ($fld['subs'] as $sid => &$fsub) {
			$tlist .= $wz->outputField($fld_id,$fldprefix . '[' . $sy . '][' . $fsub['dbfld'] . ']', $fsub, $srset[$fsub['dbfld']], true);

			/* if($xmode==='view')
			 $pdfhtml .= $wz->outputField($fldprefix . '[' . $sy . '][' . $fsub['dbfld'] . ']', $fsub, $srset[$fsub['dbfld']], true); */
		}
		$tlist .= ($xmode !== 'view' ? '<td><div class="fbutton delRow"></div></td>' : '') . '</tr>';
		//$pdfhtml .= $tlist;
	}
}else{
	$tlist .= '<tr><td colspan="'.($headsind+1).'" align="center">'.$AppUI->_('No Entries').'</td>';
}
++$subCnt;
$blistsec .= $tlist . '</tbody></table>';
/* .
		($xmode != 'view' ? '<br>
									<input type="button" onclick="frm.addSubRow(this);" value="new entry" class="text">'
				: '');*/
/* $blistsec .= $tlist . '</tbody></table>' .
 ($xmode != 'view' ? '<br>
 <input type="button" onclick="frm.addSubRow(this);" value="new entry" class="text">
 </td></tr>'
: ''); */
/* if($xmode==='view')
 $pdfhtml .= '</tbody></table></td></tr>'; */
echo $blistsec;
?>