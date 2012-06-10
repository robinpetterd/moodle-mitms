<?php  // $Id: lib.php,v 1.1.4.2 2008/01/15 23:53:28 agrabs Exp $
defined('FEEDBACK_INCLUDE_TEST') OR die('not allowed');
require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_class.php');

define('FEEDBACK_RADIORATED_MAXCOUNT', 10); //count of possible items
define('FEEDBACK_RADIORATED_LINE_SEP', '|');
define('FEEDBACK_RADIORATED_VALUE_SEP', '####');
define('FEEDBACK_RADIORATED_ADJUST_SEP', '<<<<<');

class feedback_item_radiorated extends feedback_item_base {
    var $type = "radiorated";
    function init() {
    
    }
    
    function show_edit($item, $usehtmleditor = false) {

        $item->presentation = empty($item->presentation) ? '' : $item->presentation;

        //check, whether the buttons are vertical or horizontal
        $presentation = $horizontal = '';
        @list($presentation, $horizontal) = explode(FEEDBACK_RADIO_ADJUST_SEP, $item->presentation);
        if(isset($horizontal) AND $horizontal == 1) {
            $horizontal = true;
        }else {
            $horizontal = false;
        }
    ?>
        <table>
            <tr>
                <th colspan="2"><?php print_string('radiorated', 'feedback');?>
                    &nbsp;(<input type="checkbox" name="required" value="1" <?php
                    $item->required=isset($item->required)?$item->required:0;
                    echo ($item->required == 1?'checked="checked"':'');
                    ?> />&nbsp;<?php print_string('required', 'feedback');?>)
                </th>
            </tr>
            <tr>
                <td colspan="2">
                    <?php print_string('adjustment', 'feedback');?>:
                    &nbsp;<?php print_string('vertical', 'feedback');?><input type="radio" name="horizontal" value="0" <?php echo $horizontal ? '' : 'checked="checked"';?> />
                    &nbsp;<?php print_string('horizontal', 'feedback');?><input type="radio" name="horizontal" value="1" <?php echo $horizontal ? 'checked="checked"' : '';?> />
                </td>
            </tr>
            <tr>
                <td><?php print_string('item_name', 'feedback');?></td>
                <td><input type="text" id="itemname" name="itemname" size="40" maxlength="255" value="<?php echo isset($item->name)?htmlspecialchars(stripslashes_safe($item->name)):'';?>" /></td>
            </tr>
            <tr>
                <td>
                    <?php print_string('radio_values', 'feedback');?>
                </td>
                <td>
    <?php
                    //$itemvalues = str_replace('|', "\n", stripslashes_safe($item->presentation));
                    if($itemvalues = explode(FEEDBACK_RADIORATED_LINE_SEP, stripslashes_safe($presentation), FEEDBACK_RADIORATED_MAXCOUNT)){
                        echo '<table>';
                        echo '<tr>';
                        echo '<td><b>'.get_string('line_values', 'feedback').'</b></td><td><b>'.get_string('line_labels', 'feedback').'</b></td>';
                        echo '</tr>';
                        for ($i = 0; $i < FEEDBACK_RADIORATED_MAXCOUNT; $i++) {
                            if(count($itemvalues) > $i) {
                                $value = explode(FEEDBACK_RADIORATED_VALUE_SEP, $itemvalues[$i]);
                                if(count($value) <= 1) {
                                    $value[0] = ''; $value[1] = '';
                                }
                            }else {
                                $value[0] = ''; $value[1] = '';
                            }
                            echo '<tr>';
                            echo '<td><input type="text" name="fr_val[]" size="4" maxlength="4" value="'.$value[0].'" /></td>';
                            echo '<td><input type="text" name="fr_label[]" size="40" maxlength="255" value="'.$value[1].'" /></td>';
                            echo '</tr>';
                        }
                        echo '</table>';
                    }
                    
    ?>
                </td>
            </tr>
        </table>
    <?php
    }

    //liefert ein eindimensionales Array mit drei Werten(typ, name, XXX)
    //XXX ist ein eindimensionales Array (Mittelwert der Werte der Antworten bei Typ Radio_rated) Jedes Element ist eine Struktur (answertext, avg)
    function get_analysed($item, $groupid = false, $courseid = false, $facetofacesessionid = false) {
        $analysedItem = array();
        $analysedItem[] = $item->typ;
        $analysedItem[] = $item->name;
        //die moeglichen Antworten extrahieren
        $lines = null;
        $presentation = '';
        @list($presentation) = explode(FEEDBACK_RADIORATED_ADJUST_SEP, $item->presentation); //remove the adjustment-info
        $lines = explode (FEEDBACK_RADIORATED_LINE_SEP, stripslashes_safe($presentation));
        if(!is_array($lines)) return null;

        //die Werte holen
        //$values = get_records('feedback_value', 'item', $item->id);
   $values = feedback_get_group_values($item, $groupid, $courseid, $facetofacesessionid);
   if(!$values && !$facetofacesessionid) return null;
        //schleife ueber den Werten und ueber die Antwortmoeglichkeiten
        
        $analysedAnswer = array();

        for($i = 1; $i <= sizeof($lines); $i++) {
            $item_values = explode(FEEDBACK_RADIORATED_VALUE_SEP, $lines[$i-1]);
            $ans = null;
            $ans->answertext = $item_values[1];
            $avg = 0.0;
            $anscount = 0;
        if($values){
            foreach($values as $value) {
                //ist die Antwort gleich dem index der Antworten + 1?
                if ($value->value == $i) {
                    $avg += $item_values[0]; //erst alle Werte aufsummieren
                    $anscount++;
                }
            }
        }
            $ans->answercount = $anscount;
            $ans->avg = doubleval($avg) / doubleval(sizeof($values));
            $ans->value = $item_values[0];
            $ans->quotient = $ans->answercount / sizeof($values);
            $analysedAnswer[] = $ans;
        }
        $analysedItem[] = $analysedAnswer;
        return $analysedItem;
    }

    function get_printval($item, $value) {
        $printval = '';
        
        if(!isset($value->value)) return $printval;
                
        @list($presentation) = explode(FEEDBACK_RADIORATED_ADJUST_SEP, $item->presentation); //remove the adjustment-info

        $presentation = explode (FEEDBACK_RADIORATED_LINE_SEP, stripslashes_safe($presentation));
        $index = 1;
        foreach($presentation as $pres){
            if($value->value == $index){
                $radio_label = explode(FEEDBACK_RADIORATED_VALUE_SEP, $pres);
                $printval = $radio_label[1];
                break;
            }
            $index++;
        }
        return $printval;
    }

    function print_analysed($item, $itemnr = 0, $groupid = false, $courseid = false) {
        $sep_dec = get_string('separator_decimal', 'feedback');
        if(substr($sep_dec, 0, 2) == '[['){
            $sep_dec = FEEDBACK_DECIMAL;
        }
        
        $sep_thous = get_string('separator_thousand', 'feedback');
        if(substr($sep_thous, 0, 2) == '[['){
            $sep_thous = FEEDBACK_THOUSAND;
        }
            
        $analysedItem = $this->get_analysed($item, $groupid, $courseid);
        if($analysedItem) {
            //echo '<table>';
            $itemnr++;
            echo '<tr><th colspan="2" align="left">'. $itemnr . '.)&nbsp;' . $analysedItem[1] .'</th></tr>';
            $analysedVals = $analysedItem[2];
            $pixnr = 0;
            $avg = 0.0;
            foreach($analysedVals as $val) {
                
                if( function_exists("bcmod")) {
                    $pix = 'pics/' . bcmod($pixnr, 10) . '.gif';
                }else {
                    $pix = 'pics/0.gif';
                }
                $pixnr++;
                $pixwidth = intval($val->quotient * FEEDBACK_MAX_PIX_LENGTH);
                
                $avg += $val->avg;
                $quotient = number_format(($val->quotient * 100), 2, $sep_dec, $sep_thous);
                echo '<tr><td align="left" valign="top">-&nbsp;&nbsp;' . trim($val->answertext) . ' ('.$val->value.'):</td><td align="left" width="'.FEEDBACK_MAX_PIX_LENGTH.'"><img style=" vertical-align: baseline;" src="'.$pix.'" height="5" width="'.$pixwidth.'" />' . $val->answercount. (($val->quotient > 0)?'&nbsp;('. $quotient . '&nbsp;%)':'') . '</td></tr>';
            }
            $avg = number_format(($avg), 2, $sep_dec, $sep_thous);
            echo '<tr><td align="left" colspan="2"><b>'.get_string('average', 'feedback').': '.$avg.'</b></td></tr>';
            //echo '</table>';
        }
        return $itemnr;
    }

    function excelprint_item(&$worksheet, $rowOffset, $item, $groupid, $courseid = false, $colOffset = 0, $facetofacesessionid = false) {
        $analysed_item = $this->get_analysed($item, $groupid, $courseid, $facetofacesessionid);


        $data = $analysed_item[2];

        $worksheet->setFormat("<l><f><ro2><vo><c:green>");
        //frage schreiben
        $worksheet->write_string($rowOffset, $colOffset, $analysed_item[1]);
        if(is_array($data)) {
            $avg = 0.0;
            for($i = 0; $i < sizeof($data); $i++) {
                $aData = $data[$i];
                
                $worksheet->setFormat("<l><f><ro2><vo><c:blue>");
                $worksheet->write_string($rowOffset, $colOffset + $i + 1, trim($aData->answertext).' ('.$aData->value.')');
                
                $worksheet->setFormat("<l><vo>");
                $worksheet->write_number($rowOffset + 1, $colOffset + $i + 1, $aData->answercount);
                //$worksheet->setFormat("<l><f><vo>");
                //$worksheet->write_number($rowOffset + 2, $i + 1, $aData->avg);
                $avg += $aData->avg;
            }
            //mittelwert anzeigen
            $worksheet->setFormat("<l><f><ro2><vo><c:red>");
            $worksheet->write_string($rowOffset + 2, sizeof($data) + $colOffset + 1, get_string('average', 'feedback'));
            
            $worksheet->setFormat("<l><f><vo>");
            $worksheet->write_number($rowOffset + 3, sizeof($data) + $colOffset + 1, $avg);
        }
        $rowOffset +=4 ;
        return $rowOffset;
    }

    function print_item($item, $value = false, $readonly = false, $edit = false, $highlightrequire = false){
        $align = get_string('thisdirection') == 'ltr' ? 'left' : 'right';
        
        //extract the adjustment-info
        $presentation = $horizontal = '';
        @list($presentation, $horizontal) = explode(FEEDBACK_RADIORATED_ADJUST_SEP, $item->presentation);
        if(isset($horizontal) AND $horizontal == 1) {
            $horizontal = true;
        }else {
            $horizontal = false;
        }
        
        $lines = explode (FEEDBACK_RADIORATED_LINE_SEP, stripslashes_safe($presentation));
        if($highlightrequire AND $item->required AND intval($value) <= 0) {
            $highlight = 'bgcolor="#FFAAAA" class="missingrequire"';
        }else {
            $highlight = '';
        }
        $requiredmark =  ($item->required == 1)?'<font color="red">*</font>':'';
    ?>
        <td <?php echo $highlight;?> valign="top" align="<?php echo $align;?>"><?php echo format_text(stripslashes_safe($item->name) . $requiredmark, true, false, false);?></td>
        <td valign="top" align="<?php echo $align;?>">
    <?php
        $index = 1;
        $checked = '';
        if($readonly){
            foreach($lines as $line){
                if($value == $index){
                    $radio_value = explode(FEEDBACK_RADIORATED_VALUE_SEP, $line);
                    // print_simple_box_start('left');
                    print_box_start('generalbox boxalign'.$align);
                    echo text_to_html($radio_value[1], true, false, false);
                    // print_simple_box_end();
                    print_box_end();
                    break;
                }
                $index++;
            }
        } else {
    ?>
            <table><tr>
            <td valign="top" align="<?php echo $align;?>"><input type="radio"
                    name="<?php echo $item->typ . '_' . $item->id ;?>"
                    id="<?php echo $item->typ.'_'.$item->id.'_xxx';?>"
                    value="" <?php echo $value ? '' : 'checked="checked"';?> />
            </td>
            <td align="<?php echo $align;?>">
                <label for="<?php echo $item->typ.'_'.$item->id.'_xxx';?>"><?php print_string('not_selected', 'feedback');?>&nbsp;</label>
            </td>
            </tr></table>
    <?php
            if($horizontal) {
                echo '<table><tr>';
            }
            foreach($lines as $line){
                if($value == $index){
                    $checked = 'checked="checked"';
                }else{
                    $checked = '';
                }
                $radio_value = explode(FEEDBACK_RADIORATED_VALUE_SEP, $line);
                $inputname = $item->typ . '_' . $item->id;
                $inputid = $inputname.'_'.$index;
                if($horizontal) {
    ?>
                    <td valign="top" align="<?php echo $align;?>"><input type="radio"
                            name="<?php echo $inputname;?>"
                            id="<?php echo $inputid;?>"
                            value="<?php echo $index;?>" <?php echo $checked;?> />
                    </td><td align="<?php echo $align;?>"><label for="<?php echo $inputid;?>"><?php
                                    if($edit) {
                                        echo text_to_html('('.$radio_value[0].') '.$radio_value[1], true, false, false);
                                    }else {
                                        echo text_to_html($radio_value[1], true, false, false);
                                    }
                                ?>&nbsp;</label>
                    </td>
    <?php
                }else {
    ?>
                    <table><tr>
                    <td valign="top" align="<?php echo $align;?>"><input type="radio"
                            name="<?php echo $inputname;?>"
                            id="<?php echo $inputid;?>"
                            value="<?php echo $index;?>" <?php echo $checked;?> />
                    </td><td align="<?php echo $align;?>"><label for="<?php echo $inputid;?>"><?php
                                    if($edit) {
                                        echo text_to_html('('.$radio_value[0].') '.$radio_value[1], true, false, false);
                                    }else {
                                        echo text_to_html($radio_value[1], true, false, false);
                                    }
                                ?>&nbsp;</label>
                    </td></tr></table>
    <?php
                }
                $index++;
            }
            if($horizontal) {
                echo '</tr></table>';
            }
            /*
            if($item->required == 1) {
                echo '<input type="hidden" name="'.$item->typ . '_' . $item->id.'" value="1" />';
            }
            */
        }
    ?>
        </td>
    <?php
    }

    function check_value($value, $item) {
        if((!isset($value) OR $value == '' OR $value == 0) AND $item->required != 1) return true;
        if(intval($value) > 0)return true;
        return false;
    }

    function create_value($data) {
        $data = clean_param($data, PARAM_INT);
        return $data;
    }

    function get_presentation($data) {
        $valuelines = $data->fr_val;
        $labellines = $data->fr_label;
        $present = '';
        if(!is_array($valuelines) AND !is_array($labellines)) {
            return $present;
        }
        
        //if( trim($valuelines[0]) != ''){
            $value = intval($valuelines[0]);
            $label = $labellines[0];
            $present .= $value.FEEDBACK_RADIORATED_VALUE_SEP.$label;
        //}
        
        for($i = 1; $i < FEEDBACK_RADIORATED_MAXCOUNT; $i++) {
            if( (trim($valuelines[$i]) == '') AND (trim($labellines[$i]) == ''))continue;
            
            $value = intval($valuelines[$i]);
            $label = $labellines[$i];
            $present .= FEEDBACK_RADIORATED_LINE_SEP.$value.FEEDBACK_RADIORATED_VALUE_SEP.$label;
        }
        //$present = str_replace("\n", '|', trim($data->itemvalues));
        if($data->horizontal == 1) {
            $present .= FEEDBACK_RADIORATED_ADJUST_SEP.'1';
        }
        return $present;
    }

    function get_hasvalue() {
        return 1;
    }
}
?>
