<?php  //$Id$

    if (!defined('MOODLE_INTERNAL')) {
        die('Direct access to this script is forbidden.'); // It must be included from a Moodle page
    }

    if (has_capability('coursereport/progress:view', $context)) {
        $completion = new completion_info($course);
        if ($completion->is_enabled()) {
            echo '<p>';
            echo '<a href="'.$CFG->wwwroot.'/course/report/progress/index.php?course='.$course->id.'">'.get_string('activitycompletionreport','completion').'</a>';
            echo '</p>';
        }
    }
?>
