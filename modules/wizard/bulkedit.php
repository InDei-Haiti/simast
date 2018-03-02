<?php
/**
 * Created by PhpStorm.
 * User: emmanuel.suy
 * Date: 2/21/2018
 * Time: 4:47 PM
 */

$fuid = (int)$_GET['fid'];
$wz = new Wizard("edit", $task_id, $client_id, $useID);
echo '<br /><br /> <div class="card">';
if ($fuid > 0) {
    $dvals = array();
    $wz->loadFormInfo($fuid);
    $blist = '';
    $wz->tableWrap();
    $blist .= '<table cellspacing="1" cellpadding="1" border="0"  width="auto" class="mtab">
					<tbody><tr>
						<td width="100%" valign="top">
						<table class="mtab"><tbody>';
    foreach ($wz->fields as $fld_id => $fld) {
        $ftype = $fld['type'];
        if($ftype=='select') {
            /*echo '<pre>';
            var_dump($fld);
            echo '</pre>';*/
            //$blist .= $wz->outputField($fld_id, str_replace('fld_', '', $fld['dbfld']), $fld, null);
            //echo '<br/>';
            $blist .= '<tr>';
            $blist .= "<td>".$fld['name']."</td>";
            $code = $wz->buildSelectList($index,$alist, $fld, $fld_id, $dvalue, $fldClass);
            switch ($pftype) {
                case 'date':
                    //$name,$value,$hidden=false,$tags='',$yearCase = false,$length=20,$hvalue=false,$extraEvent=''
                    /*$code = DateCalendar('fld_' . $fld_id, printDate($dvalue), false, 'class=" ' . $fldClass . ' ' . $obligate . '"', false, 10, false,
                        ($obligate != '' ? '$j(this).trigger("focusout");' : ''), $fld['range']['start'], $fld['range']['end']);*/

                    $code = drawDateCalendar('fld_' . $fld_id, $dvalue, false, 'class=" ' . $fldClass . ' ' . $obligate . '"', false, 10, false,
                            ($obligate != '' ? '$j(this).trigger("focusout");' : ''), $fld['range']['start'], $fld['range']['end'])
                        . '<input type="hidden" name="client_id" value="' . $this->client_id . '">'
                        . '<input type="hidden" name="id" value="' . $this->entryId . '">';
                    break;

                case 'entry_date':
                    //$name,$value,$hidden=false,$tags='',$yearCase = false,$length=20,$hvalue=false,$extraEvent=''
                    $code = drawDateCalendar('entry_date', printDate($dvalue), false, 'class=" ' . $fldClass . ' ' . $obligate . '"', false, 10, false,
                            ($obligate != '' ? '$j(this).trigger("focusout");' : ''), $fld['range']['start'], $fld['range']['end'])
                        . '<input type="hidden" name="client_id" value="' . $this->client_id . '">'
                        . '<input type="hidden" name="id" value="' . $this->entryId . '">';

                    break;
                case 'times':
                    $code = drawTimePicker('fld_' . $fld_id, $dvalue, ($ftype === 'datetime' ? true : false), $obligate);
                    break;
                case 'radio':
                    if ($tabout === false) {
                        $code = arraySelectRadio($alist, 'fld_' . $fld_id, 'class="' . $fldClass . ' ' . $obligate . '" ', $dvalue);
                    }
                    if ($obligate !== '')
                        $blist = str_replace('</td><td', '</td><td class="radioMandat" ', $blist);
                    ++$this->radioID;
                    break;
                case 'checkbox':
                    if ($tabout === false) {
                        $code = arraySelectCheckbox($alist, 'fld_' . $fld_id . '[]', 'class="' . $fldClass . ' ' . $obligate . '" ', $dvalue);
                    } else {
                        $code = arraySelectCheckboxMultiCol($alist, 'fld_' . $fld_id . '[]', 'class="' . $fldClass . ' ' . $obligate . '" ', $dvalue, false, 1, false);
                    }
                    break;
                case 'bigText':
                    $code = '<textarea name="fld_' . $fld_id . '" cols=40 rows=5 class="text ' . $fldClass . ' ' . $obligate . '">' . nl2br(stripslashes($dvalue)) . '</textarea>';
                    break;
                /*case 'current_user':
                    $code = '<input class="text ' . $obligate . ' ' . $fldClass . ' " name="fld_' . $fld_id . '" id="trow_' . $this->textRowId++ . '" size="30" value="' . $AppUI->user_id . '" readonly="readonly">';
                    break;
                case 'current_user_fix':
                    $code = '<input class="text ' . $obligate . ' ' . $fldClass . ' " name="fld_' . $fld_id . '" id="trow_' . $this->textRowId++ . '" size="30" value="' . $AppUI->user_id . '"  readonly="readonly">';
                    break;*/
                default:
                    if ($pftype != 'plain') {
                        if (is_array($fld['range'])) {
                            $obligate .= "numeric inrange\" data-rng='" . ($fld['range']['start'] . '|' . $fld['range']['end']) . "' ";
                        } else {
                            $obligate .= ' strictz numeric';
                        }
                    }
                    break;
            }
            $blist .='<td>'.$code.'</td>';
            $blist .= '</tr>';
        }
    }
    $blist .= '
				<tr>
					<td colspan="2" align="right">
						<input type="button" onclick="frm.checkForm()" class="button" value="'.$AppUI->_('submit').'">
						<input type="button" onclick="history.back(-1);" class="button" value="'.$AppUI->_('back').'">
					</td>
				</tr>
				 </table>
				 </td>
				 </tr>
				 </tbody>
				 </table>
				';
    echo $blist;
}
echo '</div>';
?>