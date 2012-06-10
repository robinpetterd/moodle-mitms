<?php  // $Id: lib.php,v 1.1.4.2 2008/01/15 23:53:27 agrabs Exp $
defined('FEEDBACK_INCLUDE_TEST') OR die('not allowed');
require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_class.php');

class feedback_item_numeric extends feedback_item_base {
    var $type = "numeric";
    function init() {
    
    }
    
    function show_edit($item, $usehtmleditor = false) {

        $item->presentation=empty($item->presentation)?'':$item->presentation;

    ?>
        <table>
            <tr>
                <th colspan="2"><?php print_string('numeric', 'feedback');?>
                    &nbsp;(<input type="checkbox" name="required" value="1" <?php 
                $item->required=isset($item->required)?$item->required:0;
                echo ($item->required == 1?'checked="checked"':'');
                ?> />&nbsp;<?php print_string('required', 'feedback');?>)
                </th>
            </tr>
            <tr>
                <td><?php print_string('item_name', 'feedback');?></td>
                <td><input type="text" id="itemname" name="itemname" size="40" maxlength="255" value="<?php echo isset($item->name)?htmlspecialchars(stripslashes_safe($item->name)):'';?>" /></td>
            </tr>
            <tr>
                <?php
                        //Dropdown-Items fuer die Textfeldbreite
                        $range_from_to = explode('|',$item->presentation);
                        $range_from = isset($range_from_to[0]) ? intval($range_from_to[0]) : 0;
                        $range_to = isset($range_from_to[1]) ? intval($range_from_to[1]) : 0;
                ?>
                <td><?php print_string('numeric_range_from', 'feedback');?></td>
                <td>
                    <input type="text" name="numericrangefrom" value="<?php echo $range_from;?>" />
                </td>
            </tr>
            <tr>
                <td><?php print_string('numeric_range_to', 'feedback');?></td>
                <td>
                    <input type="text" name="numericrangeto" value="<?php echo $range_to;?>" />
                </td>
            </tr>
        </table>
    <?php
    }

    //liefert eine Struktur ->name, ->data = array(mit Antworten)
    function get_analysed($item, $groupid = false, $courseid = false) {
        $analysed = null;
        $analysed->name = $item->name;
        //$values = get_records('feedback_value', 'item', $item->id);
        $values = feedback_get_group_values($item, $groupid, $courseid);
        
        $avg = 0.0;
        $counter = 0;
        if($values) {
            $data = array();
            foreach($values as $value) {
                if(is_numeric($value->value)) {
                    $data[] = $value->value;
                    $avg += $value->value;
                    $counter++;
                }
            }
            $avg = $counter > 0 ? $avg / $counter : 0;
            $analysed->data = $data;
            $analysed->avg = $avg;
        }
        return $analysed;
    }

    function get_printval($item, $value) {
        if(!isset($value->value)) return '';
        
        return $value->value;
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
        
        // $values = feedback_get_group_values($item, $groupid, $courseid);
        $values = $this->get_analysed($item, $groupid, $courseid);

        if(isset($values->data) AND is_array($values->data)) {
            //echo '<table>';2
            $itemnr++;
            echo '<tr><th colspan="2" align="left">'. $itemnr . '.)&nbsp;' . stripslashes($item->name) .'</th></tr>';
            foreach($values->data as $value) {
                echo '<tr><td colspan="2" valign="top" align="left">-&nbsp;&nbsp;' . $value . '</td></tr>';
            }
            //echo '</table>';
            $avg = number_format($values->avg, 2, $sep_dec, $sep_thous);
            echo '<tr><td align="left" colspan="2"><b>'.get_string('average', 'feedback').': '.$avg.'</b></td></tr>';
        }
        return $itemnr;
    }

    function excelprint_item(&$worksheet, $rowOffset, $item, $groupid, $courseid = false) {
        $analysed_item = $this->get_analysed($item, $groupid, $courseid);

        $worksheet->setFormat("<l><f><ro2><vo><c:green>");
        $worksheet->write_string($rowOffset, 0, stripslashes($item->name));
        $data = $analysed_item->data;
        if(is_array($data)) {
            // $worksheet->setFormat("<l><ro2><vo>");
            // $worksheet->write_number($rowOffset, 1, $data[0]);
            // $rowOffset++;
            // for($i = 1; $i < sizeof($data); $i++) {
                // $worksheet->setFormat("<l><vo>");
                // $worksheet->write_number($rowOffset, 1, $data[$i]);
                // $rowOffset++;
            // }
        
            //mittelwert anzeigen
            $worksheet->setFormat("<l><f><ro2><vo><c:red>");
            $worksheet->write_string($rowOffset, 1, get_string('average', 'feedback'));
            
            $worksheet->setFormat("<l><f><vo>");
            $worksheet->write_number($rowOffset + 1, 1, $analysed_item->avg);
            $rowOffset++;
        }
        $rowOffset++;
        return $rowOffset;
    }

    function print_item($item, $value = false, $readonly = false, $edit = false, $highlightrequire = false){
        $align = get_string('thisdirection') == 'ltr' ? 'left' : 'right';
        
        //get the range
        $range_from_to = explode('|',$item->presentation);
        //get the min-value
        $range_from = isset($range_from_to[0]) ? intval($range_from_to[0]) : 0;
        //get the max-value
        $range_to = isset($range_from_to[1]) ? intval($range_from_to[1]) : 0;
        if($highlightrequire AND (!$this->check_value($value, $item))) {
            $highlight = 'bgcolor="#FFAAAA" class="missingrequire"';
        }else {
            $highlight = '';
        }
        $requiredmark =  ($item->required == 1)?'<font color="red">*</font>':'';
    ?>
        <td <?php echo $highlight;?> valign="top" align="<?php echo $align;?>">
            <?php 
                echo format_text(stripslashes_safe($item->name) . $requiredmark, true, false, false);
                switch(true) {
                    case ($range_from === 0 AND $range_to > 0):
                        echo ' ('.get_string('maximal', 'feedback').': '.$range_to.')';
                        break;
                    case ($range_from > 0 AND $range_to === 0):
                        echo ' ('.get_string('minimal', 'feedback').': '.$range_from.')';
                        break;
                    case ($range_from === 0 AND $range_to === 0):
                        break;
                    default:
                        echo ' ('.$range_from.'-'.$range_to.')';
                        break;
                }
            ?>
        </td>
        <td valign="top" align="<?php echo $align;?>">
    <?php
        if($readonly){
            // print_simple_box_start($align);
            print_box_start('generalbox boxalign'.$align);
            echo $value ? $value : '&nbsp;';
            // print_simple_box_end();
            print_box_end();
        }else {
    ?>
            <input type="text" name="<?php echo $item->typ.'_'.$item->id; ?>"
                                    size="10"
                                    maxlength="10"
                                    value="<?php echo $value ? $value : ''; ?>" />
    <?php
        }
    ?>
        </td>
    <?php
    }

    function check_value($value, $item) {
        //if the item is not required, so the check is true if no value is given
        if((!isset($value) OR $value == '') AND $item->required != 1) return true;
        if(!is_numeric($value))return false;
        
        $range_from_to = explode('|',$item->presentation);
        $range_from = isset($range_from_to[0]) ? intval($range_from_to[0]) : 0;
        $range_to = isset($range_from_to[1]) ? intval($range_from_to[1]) : 0;
        
        switch(true) {
            case ($range_from === 0 AND $range_to > 0):
                if(intval($value) <= $range_to) return true;
                break;
            case ($range_from > 0 AND $range_to === 0):
                if(intval($value) >= $range_from) return true;
                break;
            case ($range_from === 0 AND $range_to === 0):
                return true;
                break;
            default:
                if(intval($value) >= $range_from AND intval($value) <= $range_to) return true;
                break;
        }
        
        return false;
    }

    function create_value($data) {
        if($data AND $data != '') {
            $data = intval($data);
        }else {
            $data = '';
        }
        return $data;
    }

    function get_presentation($data) {
        return $data->numericrangefrom . '|'. $data->numericrangeto;
    }

    function get_hasvalue() {
        return 1;
    }
}
?>
