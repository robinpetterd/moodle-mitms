<?php  // $Id: lib.php,v 1.1.4.3 2008/04/03 13:52:23 agrabs Exp $
defined('FEEDBACK_INCLUDE_TEST') OR die('not allowed');
require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_class.php');

class feedback_item_textarea extends feedback_item_base {
    var $type = "textarea";
    function init() {
    
    }
    
    function show_edit($item, $usehtmleditor = false) {

        $item->presentation=empty($item->presentation)?'':$item->presentation;

    ?>
        <table>
            <tr>
                <th colspan="2"><?php print_string('textarea', 'feedback');?>
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
                <td><?php print_string('textarea_width', 'feedback');?></td>
                <td>
                    <select name="itemwidth">
    <?php
                        //Dropdown-Items fuer die Textareabreite
                        $widthAndHeight = explode('|',$item->presentation);
                        feedback_print_numeric_option_list(5, 80, $widthAndHeight[0]?$widthAndHeight[0]:30, 5);
    ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><?php print_string('textarea_height', 'feedback');?></td>
                <td>
                    <select name="itemheight">
    <?php
                        //Dropdown-Items fuer die Textareahoehe
                        feedback_print_numeric_option_list(5, 40, $widthAndHeight[1], 5);
    ?>
                    </select>
                </td>
            </tr>
        </table>
    <?php
    }

    //liefert eine Struktur ->name, ->data = array(mit Antworten)
    function get_analysed($item, $groupid, $courseid = false, $facetofacesessionid = false) {
        $aVal = null;
        $aVal->name = $item->name;
        //$values = get_records('feedback_value', 'item', $item->id);
        $values = feedback_get_group_values($item, $groupid, $courseid, $facetofacesessionid);
        if($values) {
            $data = array();
            foreach($values as $value) {
                $data[] = str_replace("\n", '<br />', $value->value);
            }
            $aVal->data = $data;
        }
        return $aVal;
    }

    function get_printval($item, $value) {
        
        if(!isset($value->value)) return '';

        return $value->value;
    }

    function print_analysed($item, $itemnr = 0, $groupid = false, $courseid = false) {
        $values = feedback_get_group_values($item, $groupid, $courseid);
        if($values) {
            //echo '<table>';2
            $itemnr++;
            echo '<tr><th colspan="2" align="left">'. $itemnr . '.)&nbsp;' . stripslashes_safe($item->name) .'</th></tr>';
            foreach($values as $value) {
                echo '<tr><td valign="top" align="left">-&nbsp;&nbsp;</td><td align="left" valign="top">' . str_replace("\n", '<br />', $value->value) . '</td></tr>';
            }
            //echo '</table>';
        }
        return $itemnr;
    }

    function excelprint_item(&$worksheet, $rowOffset, $item, $groupid, $courseid = false, $colOffset = 0, $facetofacesessionid = false) {
        $analysed_item = $this->get_analysed($item, $groupid, $courseid, $facetofacesessionid);

        $worksheet->setFormat("<l><f><ro2><vo><c:green>");
        $worksheet->write_string($rowOffset++, $colOffset, stripslashes_safe($item->name));
        $data = $analysed_item->data;
        if(is_array($data)) {
            $worksheet->setFormat("<l><ro2><vo>");
            $worksheet->write_string($rowOffset++, $colOffset, $data[0]);
            for($i = 1; $i < sizeof($data); $i++) {
                $worksheet->setFormat("<l><vo>");
                $worksheet->write_string($rowOffset++, $colOffset, $data[$i]);
            }
        }
        return $rowOffset;
    }

    function print_item($item, $value = false, $readonly = false, $edit = false, $highlightrequire = false){
        $align = get_string('thisdirection') == 'ltr' ? 'left' : 'right';
        
        $presentation = explode ("|", $item->presentation);
        if($highlightrequire AND $item->required AND strval($value) == '') {
            $highlight = 'bgcolor="#FFAAAA" class="missingrequire"';
        }else {
            $highlight = '';
        }
        $requiredmark =  ($item->required == 1)?'<font color="red">*</font>':'';
    ?>
        <td <?php echo $highlight;?> valign="top" align="<?php echo $align;?>"><?php echo format_text(stripslashes_safe($item->name) . $requiredmark, true, false, false);?></td>
        <td valign="top" align="<?php echo $align;?>">
    <?php
        if($readonly){
            // print_simple_box_start($align);
            print_box_start('generalbox boxalign'.$align);
            echo $value?str_replace("\n",'<br />',$value):'&nbsp;';
            // print_simple_box_end();
            print_box_end();
        }else {
    ?>
            <textarea name="<?php echo $item->typ . '_' . $item->id;?>"
                        cols="<?php echo $presentation[0];?>"
                        rows="<?php echo $presentation[1];?>"><?php echo $value?htmlspecialchars($value):'';?></textarea>
    <?php
        }
    ?>
        </td>
    <?php
    }

    function check_value($value, $item) {
        //if the item is not required, so the check is true if no value is given
        if((!isset($value) OR $value == '') AND $item->required != 1) return true;
        if($value == "")return false;
        return true;
    }

    function create_value($data) {
        $data = addslashes(clean_text($data));
        return $data;
    }

    function get_presentation($data) {
        return $data->itemwidth . '|'. $data->itemheight;
    }

    function get_hasvalue() {
        return 1;
    }
}
?>
