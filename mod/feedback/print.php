<?php // $Id: print.php,v 1.1.4.2 2008/04/04 10:38:00 agrabs Exp $
/**
* print a printview of feedback-items
*
* @version $Id: print.php,v 1.1.4.2 2008/04/04 10:38:00 agrabs Exp $
* @author Andreas Grabs
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package feedback
*/

    require_once("../../config.php");
    require_once("lib.php");

    $id = required_param('id', PARAM_INT); 

    $formdata = data_submitted('nomatch');
 
    if ($id) {
        if (! $cm = get_coursemodule_from_id('feedback', $id)) {
            error("Course Module ID was incorrect");
        }
     
        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }
     
        if (! $feedback = get_record("feedback", "id", $cm->instance)) {
            error("Course module is incorrect");
        }
    }
    $capabilities = feedback_load_capabilities($cm->id);

    require_login($course->id);
    
    if(!$capabilities->edititems){
        error(get_string('error'));
    }
    
    $strfeedbacks = get_string("modulenameplural", "feedback");
    $strfeedback  = get_string("modulename", "feedback");

    print_header();


    /// Print the main part of the page
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    print_heading(format_text($feedback->name));

    feedback_print_errors();
    
    $feedbackitems = get_records('feedback_item', 'feedback', $feedback->id, 'position');
    if(is_array($feedbackitems)){
        $itemnr = 0;
        
        // print_simple_box_start('center', '80%');
        print_box_start('generalbox boxaligncenter boxwidthwide');
        echo '<div align="center" class="printview"><table>';
        //print the inserted items
        $itempos = 0;
        foreach($feedbackitems as $feedbackitem){
            $itempos++;
            echo '<tr>';
            //Items without value only are labels
            if($feedbackitem->hasvalue == 1) {
                $itemnr++;
                echo '<td valign="top">' . $itemnr . '.)&nbsp;</td>';
            } else {
                echo '<td>&nbsp;</td>';
            }
            if($feedbackitem->typ != 'pagebreak') {
                feedback_print_item($feedbackitem, false, false, true);
            }else {
                echo '<td class="feedback_print_pagebreak" colspan="2">&nbsp;</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
        echo '<font color="red">(*)' . get_string('items_are_required', 'feedback') . '</font>';
        echo '</div>';
        // print_simple_box_end();
        print_box_end();
    }else{
        // print_simple_box(get_string('no_items_available_yet','feedback'),"center");
        print_box(get_string('no_items_available_yet','feedback'),'generalbox boxaligncenter boxwidthwide');
    }
    print_continue('view.php?id='.$id);
    /// Finish the page
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////

    print_footer($course);

?>
