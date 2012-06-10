<?php
require_once ($CFG->dirroot . '/guides/steps/default.php');
class guide_mitms_add_competencies_step extends guide_default_step {
    ## Return the content the step should display when it is the active step in a guide:
    function content_step_active () {
        global $CFG;
        return '<p>This step requires you to set up the competencies for the competency framework.</p>
            <p>When you are ready to start click <a href="' . $CFG->wwwroot . '/hierarchy/index.php?type=competency">Manage competencies</a>.</p>
            <p>Refer to the ' .
            mitms_captivate_popup('Setting up Competencies help file', 'setting up competencies') .
            ' for further instructions.</p>
            <p>On completing your competency set up, return to this guide and click <b>Finish step</b>.</p>';
    }

    # Return content the step should display when it is not active, and is not complete:
    function content_step_pending () {
        return '<p>Competencies are the specific skills, knowledge and behavioural competencies you expect staff to achieve.</p>';
    }

    # Return content the step should display when it is not active, and is complete:
    function content_step_complete () {
        return '<p>This step has been completed.</p>
            <p>You add more competencies to the competency framework at any stage by clicking <b>Add new competency</b> from the manage organisation screen.</p>';
    }

    ## An indication of how involved this step is - default to 100 points
    ## May want to be overridden with a lower number to indicate step is trivial,
    ## or a higher number to indicate that the step is involved.
    #function effort() {
    #    return 100;
    #}

    ## An indication of how complete this step is default to 0 progress
    ## may want to over-ride if we can examine the system/db to assess how complete the step is
    #function percent_complete() {
    #    return 0;
    #}

    ##Step initialization routine
    ## By default don't do anything, but return true to indicate successful completion
    #function start_step() {
    #    return true;
    #}

    ## Step tidy-up routine
    ## By default don't do anything, but return true to indicate successful completion
    #function finish_step() {
    #    return true;
    #}

    ## Is this step ready to be finished?
    ## By default, we don't deny any request,
    ## But may want to overrided to check the database to see if required actions have been taken.
    #function is_completeable() {
    #    return true;
    #}

    ## Should this step be automatically completed as soon as it's ready?
    ## By default, no - we require the user to click to confirm that they're really done.
    ## This can be overridden so that the user flows thru the guide as actions are completed
    #function autocomplete() {
    #    return false;
    #}

}
?>
