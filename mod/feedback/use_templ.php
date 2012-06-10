<?php // $Id: use_templ.php,v 1.1.4.2 2008/04/04 10:38:00 agrabs Exp $
/**
* print the confirm dialog to use template and create new items from template
*
* @version $Id: use_templ.php,v 1.1.4.2 2008/04/04 10:38:00 agrabs Exp $
* @author Andreas Grabs
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package feedback
*/

    require_once("../../config.php");
    require_once("lib.php");
    require_once('use_templ_form.php');

    $id = required_param('id', PARAM_INT); 
    $templateid = optional_param('templateid', false, PARAM_INT);
    $deleteolditems = optional_param('deleteolditems', 0, PARAM_INT);
   
    if(!$templateid) {
        redirect('edit.php?id='.$id);
    }

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
    
    $mform = new mod_feedback_use_templ_form();
    $newformdata = array('id'=>$id,
                        'templateid'=>$templateid,
                        'confirmadd'=>'1',
                        'deleteolditems'=>'1',
                        'do_show'=>'edit');
    $mform->set_data($newformdata);
    $formdata = $mform->get_data();
    
    if ($mform->is_cancelled()) {
        redirect('edit.php?id='.$id.'&do_show=templates');
    }
    
    if(isset($formdata->confirmadd) AND $formdata->confirmadd == 1){
        feedback_items_from_template($feedback, $templateid, $deleteolditems);
        redirect('edit.php?id=' . $id);
    }

    $navigation = '';
    if ($course->category) {
        $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
    }

    $strfeedbacks = get_string("modulenameplural", "feedback");
    $strfeedback  = get_string("modulename", "feedback");

    print_header("$course->shortname: $feedback->name", "$course->fullname",
                "$navigation <a href=\"index.php?id=$course->id\">$strfeedbacks</a> -> $feedback->name", 
                "", "", true, update_module_button($cm->id, $course->id, $strfeedback), 
                navmenu($course, $cm));

    /// Print the main part of the page
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    print_heading(format_text($feedback->name));
    
    // print_simple_box_start("center", "60%", "#FFAAAA", 20, "noticebox");
    print_box_start('generalbox errorboxcontent boxaligncenter boxwidthnormal');
    print_heading(get_string('are_you_sure_to_use_this_template', 'feedback'));
    
    $mform->display();

    // print_simple_box_end();
    print_box_end();

    $templateitems = get_records('feedback_item', 'template', $templateid, 'position');
    if(is_array($templateitems)){
        $templateitems = array_values($templateitems);
    }

    if(is_array($templateitems)){
        $itemnr = 0;
        echo '<p align="center">'.get_string('preview', 'feedback').'</p>';
        // print_simple_box_start('center', '75%');
        print_box_start('generalbox boxaligncenter boxwidthwide');
        echo '<div align="center"><table>';
        foreach($templateitems as $templateitem){
            echo '<tr>';
            if($templateitem->hasvalue == 1) {
                $itemnr++;
                echo '<td valign="top">' . $itemnr . '.)&nbsp;</td>';
            } else {
                echo '<td>&nbsp;</td>';
            }
            if($templateitem->typ != 'pagebreak') {
                feedback_print_item($templateitem);
            }else {
                echo '<td><hr /></td><td>'.get_string('pagebreak', 'feedback').'</td>';
            }
            echo '</tr>';
            echo '<tr><td>&nbsp;</td></tr>';
        }
        echo '</table></div>';
        // print_simple_box_end();
        print_box_end();
    }else{
        // print_simple_box(get_string('no_items_available_at_this_template','feedback'),"center");
        print_box(get_string('no_items_available_at_this_template','feedback'),'generalbox boxaligncenter boxwidthwide');
    }

    print_footer($course);

?>
