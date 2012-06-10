<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Course completion critieria - completion on course completion
 *
 * This course completion criteria depends on another course with
 * completion enabled to be marked as complete for this user
 *
 * @package   moodlecore
 * @copyright 2009 Catalyst IT Ltd
 * @author    Aaron Barnes <aaronb@catalyst.net.nz>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class completion_criteria_course extends completion_criteria {

    /**
     * Criteria type constant
     * @var int
     */
    public $criteriatype = COMPLETION_CRITERIA_TYPE_COURSE;

    /**
     * Finds and returns a data_object instance based on params.
     * @static abstract
     *
     * @param array $params associative arrays varname=>value
     * @return object data_object instance or false if none found.
     */
    public static function fetch($params) {
        $params['criteriatype'] = COMPLETION_CRITERIA_TYPE_COURSE;
        return self::fetch_helper('course_completion_criteria', __CLASS__, $params);
    }

    /**
     * Add appropriate form elements to the critieria form
     * @access  public
     * @param   object  $mform  Moodle forms object
     * @param   mixed   $data   optional
     * @return  void
     */
    public function config_form_display(&$mform, $data = null) {
        global $CFG;

        $link = "<a href=\"{$CFG->wwwroot}/course/view.php?id={$data->id}\">".s($data->fullname).'</a>';
        $mform->addElement('checkbox', 'criteria_course['.$data->id.']', $link);

        if ($this->id) {
            $mform->setDefault('criteria_course['.$data->id.']', 1);
        }
    }

    /**
     * Update the criteria information stored in the database
     * @access  public
     * @param   array   $data   Form data
     * @return  void
     */
    public function update_config(&$data) {
        global $js_enabled;

        if (!empty($data->criteria_course) && is_array($data->criteria_course)) {

            $this->course = $data->id;

            foreach ($data->criteria_course as $key => $course) {

                // Use correct index, depending on use of checkbox or select
                $this->courseinstance = $js_enabled ? $key : $course;
                $this->id = NULL;
                $this->insert();
            }
        }
    }

    /**
     * Review this criteria and decide if the user has completed
     * @access  public
     * @param   object  $completion     The user's completion record
     * @param   boolean $mark           Optionally set false to not save changes to database
     * @return  boolean
     */
    public function review($completion, $mark = true) {

        $course = get_record('course', 'id', $this->courseinstance);
        $info = new completion_info($course);

        // If the course is complete
        if ($info->is_course_complete($completion->userid)) {

            if ($mark) {
                $completion->mark_complete();
            }

            return true;
        }

        return false;
    }

    /**
     * Return criteria title for display in reports
     * @access  public
     * @return  string
     */
    public function get_title() {
        return get_string('prerequisitescompleted', 'completion');
    }

    /**
     * Return a more detailed criteria title for display in reports
     * @access  public
     * @return  string
     */
    public function get_title_detailed() {
        $prereq = get_record('course', 'id', $this->courseinstance);
        return shorten_text(urldecode($prereq->fullname));
    }

    /**
     * Return criteria type title for display in reports
     * @access  public
     * @return  string
     */
    public function get_type_title() {
        return get_string('prerequisites', 'completion');
    }

    /**
     * Find user's who have completed this criteria
     * @access  public
     * @return  void
     */
    public function cron() {

        global $CFG;

        // Get all users who meet this criteria
        $sql = "
            SELECT DISTINCT
                c.id AS course,
                cr.id AS criteriaid,
                ra.userid AS userid,
                cc.timecompleted AS timecompleted
            FROM
                {$CFG->prefix}course_completion_criteria cr
            INNER JOIN
                {$CFG->prefix}course c
             ON cr.course = c.id
            INNER JOIN
                {$CFG->prefix}context con
             ON con.instanceid = c.id
            INNER JOIN
                {$CFG->prefix}role_assignments ra
              ON ra.contextid = con.id
            INNER JOIN
                {$CFG->prefix}course_completions cc
             ON cc.course = cr.courseinstance
            AND cc.userid = ra.userid
            LEFT JOIN
                {$CFG->prefix}course_completion_crit_compl ccc
             ON ccc.criteriaid = cr.id
            AND ccc.userid = ra.userid
            WHERE
                cr.criteriatype = ".COMPLETION_CRITERIA_TYPE_COURSE."
            AND con.contextlevel = ".CONTEXT_COURSE."
            AND c.enablecompletion = 1
            AND ccc.id IS NULL
            AND cc.timecompleted IS NOT NULL
        ";

        // Loop through completions, and mark as complete
        if ($rs = get_recordset_sql($sql)) {
            foreach ($rs as $record) {
                $completion = new completion_criteria_completion((array)$record);
                $completion->mark_complete($record->timecompleted);
            }

            $rs->close();
        }
    }

    /**
     * Return criteria progress details for display in reports
     * @access  public
     * @param   object  $completion     The user's completion record
     * @return  array
     */
    public function get_details($completion) {
        global $CFG;

        // Get completion info
        $course = new object();
        $course->id = $completion->course;
        $info = new completion_info($course);

        $prereq = get_record('course', 'id', $this->courseinstance);
        $prereq_info = new completion_info($prereq);

        $details = array();
        $details['type'] = $this->get_title();
        $details['criteria'] = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$this->courseinstance.'">'.s($prereq->fullname).'</a>';
        $details['requirement'] = get_string('coursecompleted', 'completion');
        $details['status'] = '<a href="'.$CFG->wwwroot.'/blocks/completionstatus/details.php?course='.$this->courseinstance.'">'.get_string('seedetails', 'completion').'</a>';

        return $details;
    }
}
