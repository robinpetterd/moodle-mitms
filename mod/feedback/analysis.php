<?php // $Id: analysis.php,v 1.1.4.1 2008/01/15 23:53:23 agrabs Exp $
/**
* shows an analysed view of feedback
*
* @version $Id: analysis.php,v 1.1.4.1 2008/01/15 23:53:23 agrabs Exp $
* @author Andreas Grabs
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package feedback
*/

    require_once("../../config.php");
    require_once("lib.php");

    $SESSION->feedback->current_tab = 'analysis';

    $id = required_param('id', PARAM_INT);  //the POST dominated the GET
    $courseid = optional_param('courseid', false, PARAM_INT);
    $lstgroupid = optional_param('lstgroupid', -2, PARAM_INT); //groupid (aus der Listbox gewaehlt)


    //check, whether a group is selected
    if($lstgroupid == -1) {
        $SESSION->feedback->lstgroupid = false;
    }else {
        if((!isset($SESSION->feedback->lstgroupid)) || $lstgroupid != -2)
            $SESSION->feedback->lstgroupid = $lstgroupid;
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

    if(!empty($SESSION->feedback->lstgroupid)) 
    {
        if($tmpgroup = groups_get_group($SESSION->feedback->lstgroupid)) {
            if($tmpgroup->courseid != $course->id) {
                $SESSION->feedback->lstgroupid = false;
            }
        }else {
            $SESSION->feedback->lstgroupid = false;
        }
    }
    $capabilities = feedback_load_capabilities($cm->id);

    require_login($course->id);

    if( !( (intval($feedback->publish_stats) == 1) || $capabilities->viewreports)) {
        error(get_string('error'));
    }

    /// Print the page header


    $strfeedbacks = get_string("modulenameplural", "feedback");
    $strfeedback  = get_string("modulename", "feedback");
    $navigation = '';

    $feedbackindex = "<a href=\"index.php?id=$course->id\">$strfeedbacks</a> ->";
    if ($course->category) {
        $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
    }else if ($courseid > 0 AND $courseid != SITEID) {
        $usercourse = get_record('course', 'id', $courseid);
        $navigation = "<a href=\"../../course/view.php?id=$usercourse->id\">$usercourse->shortname</a> ->";
        $feedbackindex = '';
    }

    print_header("$course->shortname: $feedback->name", "$course->fullname",
                     "$navigation $feedbackindex $feedback->name", 
                     "", "", true, update_module_button($cm->id, $course->id, $strfeedback), 
                     navmenu($course, $cm));

    /// print the tabs
    include('tabs.php');


    //print analysed items
    // print_simple_box_start("center", '80%');
    print_box_start('generalbox boxaligncenter boxwidthwide');

    //get the groupid
    //lstgroupid is the choosen id
    $mygroupid = $SESSION->feedback->lstgroupid;

    if( $capabilities->viewreports ) {

        //available group modes (NOGROUPS, SEPARATEGROUPS or VISIBLEGROUPS)
        $feedbackgroups = get_groups($course->id);
        //get the effective groupmode of this course and module
        $groupmode = groupmode($course, $cm);
        if(is_array($feedbackgroups) && $groupmode > 0){
            require_once('choose_group_form.php');
            //the use_template-form
            $choose_group_form = new feedback_choose_group_form();
            $choose_group_form->set_feedbackdata(array('groups'=>$feedbackgroups, 'mygroupid'=>$mygroupid));
            $choose_group_form->set_form_elements();
            $choose_group_form->set_data(array('id'=>$id, 'lstgroupid'=>$SESSION->feedback->lstgroupid));
            $choose_group_form->display();
        }

        //button "export to excel"
        echo '<div align="center">';
        $export_button_link = 'analysis_to_excel.php';
        $export_button_options = array('sesskey'=>$USER->sesskey, 'id'=>$id);
        $export_button_label = get_string('export_to_excel', 'feedback');
        print_single_button($export_button_link, $export_button_options, $export_button_label, 'post');
        echo '</div>';
    }

    //get completed feedbacks
    $completedscount = feedback_get_completeds_group_count($feedback, $mygroupid);

    //show the group, if available
    if(!empty($mygroupid) && $group = get_record('groups', 'id', $mygroupid)) {
        echo '<b>'.get_string('group').': '.$group->name. '</b><br />';
    }
    //show the count
    echo '<b>'.get_string('completed_feedbacks', 'feedback').': '.$completedscount. '</b><br />';

    // get the items of the feedback
    $items = get_records_select('feedback_item', 'feedback = '. $feedback->id . ' AND hasvalue = 1', 'position');
    //show the count
    if(is_array($items)){
        echo '<b>'.get_string('questions', 'feedback').': ' .sizeof($items). ' </b><hr />';
    } else {
        $items=array();
    }
    $check_anonymously = true;
    if($mygroupid > 0 AND $feedback->anonymous = FEEDBACK_ANONYMOUS_YES) {
        if($completedscount < FEEDBACK_MIN_ANONYMOUS_COUNT_IN_GROUP) {
            $check_anonymously = false;
        }
    }
    echo '<div align="center"><table width="80%" cellpadding="10"><tr><td>';
    if($check_anonymously) {
        $itemnr = 0;
        //print the items in an analysed form
        foreach($items as $item) {
            if($item->hasvalue == 0) continue;
            echo '<table width="100%" class="generalbox">';
            //get the class of item-typ
            $itemclass = 'feedback_item_'.$item->typ;
            //get the instance of the item-class
            $itemobj = new $itemclass();
            $itemnr = $itemobj->print_analysed($item, $itemnr, $mygroupid);
            echo '</table>';
        }
    }else {
        print_heading_with_help(get_string('insufficient_responses_for_this_group', 'feedback'), 'insufficient_responses', 'feedback');
    }
    echo '</td></tr></table></div>';
    // print_simple_box_end();
    print_box_end();

    print_footer($course);

?>
