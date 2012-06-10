<?php // $Id: delete_template.php,v 1.1.4.1 2008/01/15 23:53:24 agrabs Exp $
/**
* deletes a template
*
* @version $Id: delete_template.php,v 1.1.4.1 2008/01/15 23:53:24 agrabs Exp $
* @author Andreas Grabs
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package feedback
*/

    require_once("../../config.php");
    require_once("lib.php");
    $SESSION->feedback->current_tab = 'templates';

    $id = required_param('id', PARAM_INT);

    $formdata = data_submitted('nomatch');
    
    if(isset($formdata->canceldelete) && $formdata->canceldelete == 1){
        redirect(htmlspecialchars('edit.php?id='.$id.'&do_show=templates'));
    }

    if(isset($formdata->cancelconfirm) && $formdata->cancelconfirm == 1){
        redirect(htmlspecialchars('delete_template.php?id='.$id));
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
    
    if(!$capabilities->deletetemplate){
        error(get_string('error'));
    }
    
    //delete template
    if(isset($formdata->confirmdelete) && $formdata->confirmdelete == 1){
        feedback_delete_template($formdata->deletetempl);
        redirect(htmlspecialchars('delete_template.php?id=' . $id));
    }


    $strfeedbacks = get_string("modulenameplural", "feedback");
    $strfeedback  = get_string("modulename", "feedback");
	
	$navigation=isset($navigation)?$navigation:'';
    print_header($course->shortname.': '.$feedback->name, $course->fullname,
                      $navigation.' <a href="'.htmlspecialchars('index.php?id='.$course->id).'">'.$strfeedbacks.'</a> -> '.$feedback->name, 
                        '', '', true, update_module_button($cm->id, $course->id, $strfeedback), 
                        navmenu($course, $cm));

    /// Print the main part of the page
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    print_heading(get_string('delete_template','feedback'));
    if(isset($formdata->shoulddelete) && $formdata->shoulddelete == 1) {
    
        // print_simple_box_start("center", "60%", "#FFAAAA", 20, "noticebox");
        print_box_start('generalbox errorboxcontent boxaligncenter boxwidthnormal');
        print_heading(get_string('are_you_sure_to_delete_this_template', 'feedback'));
        echo '<div align="center">';
?>
        <p>&nbsp;</p>
        <form style="display:inline;" name="frm" action="<?php echo $ME;?>" method="post">
            <input type="hidden" name="sesskey" value="<?php echo $USER->sesskey;?>" />
            <input type="hidden" name="id" value="<?php echo $id;?>" />
            <input type="hidden" name="deletetempl" value="<?php echo $formdata->deletetempl;?>" />
            <input type="hidden" name="confirmdelete" value="1" />
            <button type="submit"><?php print_string('delete');?></button>
        </form>
        
        <form style="display:inline;" name="frm" action="<?php echo $ME;?>" method="post">
            <input type="hidden" name="sesskey" value="<?php echo $USER->sesskey;?>" />
            <input type="hidden" name="id" value="<?php echo $id;?>" />
            <input type="hidden" name="cancelconfirm" value="1" />
            <button type="submit"><?php print_string('cancel');?></button>
        </form>
        <div style="clear:both">&nbsp;</div>
<?php        
        echo '</div>';
        // print_simple_box_end();
        print_box_end();
    }else {
        $templates = feedback_get_template_list($course, true);
        echo '<div align="center">';
        if(!is_array($templates)) {
            // print_simple_box(get_string('no_templates_available_yet', 'feedback'), "center");
            print_box(get_string('no_templates_available_yet', 'feedback'), 'generalbox boxaligncenter');
        }else {
            echo '<table width="30%">';
            echo '<tr><th>'.get_string('templates', 'feedback').'</th><th>&nbsp;</th></tr>';
            foreach($templates as $template) {
                echo '<tr><td align="center">'.$template->name.'</td>';
                echo '<td align="center">';
                echo '<form action="'.$ME.'" method="post">';
                echo '<input title="'.get_string('delete_template','feedback').'" type="image" src="'.$CFG->pixpath .'/t/delete.gif" hspace="1" height="11" width="11" border="0" />';
                echo '<input type="hidden" name="deletetempl" value="'.$template->id.'" />';
                echo '<input type="hidden" name="shoulddelete" value="1" />';
                echo '<input type="hidden" name="id" value="'.$id.'" />';
                echo '<input type="hidden" name="sesskey" value="' . $USER->sesskey . '" />';
                echo '</form>';
                echo '</td></tr>';
            }
            echo '</table>';
        }
?>
        <form name="frm" action="<?php echo $ME;?>" method="post">
            <input type="hidden" name="sesskey" value="<?php echo $USER->sesskey;?>" />
            <input type="hidden" name="id" value="<?php echo $id;?>" />
            <input type="hidden" name="canceldelete" value="0" />
            <button type="button" onclick="this.form.canceldelete.value=1;this.form.submit();"><?php print_string('cancel');?></button>
        </form>
        </div>
<?php
    }

    print_footer($course);

?>
