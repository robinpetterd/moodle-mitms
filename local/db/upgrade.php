<?php
/*
 * Moodle - Modular Object-Oriented Dynamic Learning Environment
 *          http://moodle.org
 * Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    moodle
 * @subpackage local
 * @author     Jonathan Newman <jonathan.newman@catalyst.net.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * local db upgrades for MITMS
 */
function xmldb_local_upgrade($oldversion) {
    global $CFG, $db;

    $result = true;
    if ($result && $oldversion < 2009091000) {

    /// Define field enablecompletion to be added to course
        $table = new XMLDBTable('course');
        $field = new XMLDBField('enablecompletion');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'defaultrole');

    /// Launch add field enablecompletion
        $result = $result && add_field($table, $field);

    /// Define field completionstartonenrol to be added to course
        $field = new XMLDBField('completionstartonenrol');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'enablecompletion');

    /// Launch add field enablecompletion
        $result = $result && add_field($table, $field);

    /// Define field completion to be added to course_modules
        $table = new XMLDBTable('course_modules');
        $field = new XMLDBField('completion');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'groupmembersonly');

    /// Launch add field completion
        $result = $result && add_field($table, $field);

    /// Define field completiongradeitemnumber to be added to course_modules
        $field = new XMLDBField('completiongradeitemnumber');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null, 'completion');

    /// Launch add field completiongradeitemnumber
        $result = $result && add_field($table, $field);

    /// Define field completionview to be added to course_modules
        $field = new XMLDBField('completionview');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'completiongradeitemnumber');

    /// Launch add field completionview
        $result = $result && add_field($table, $field);

    /// Define field completionexpected to be added to course_modules
        $field = new XMLDBField('completionexpected');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'completionview');

    /// Launch add field completionexpected
        $result = $result && add_field($table, $field);

   /// Define table course_modules_completion to be created
        $table = new XMLDBTable('course_modules_completion');
        if(!table_exists($table)) {

        /// Adding fields to table course_modules_completion
            $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
            $table->addFieldInfo('coursemoduleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('completionstate', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('viewed', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);

        /// Adding keys to table course_modules_completion
            $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

        /// Adding indexes to table course_modules_completion
            $table->addIndexInfo('coursemoduleid', XMLDB_INDEX_NOTUNIQUE, array('coursemoduleid'));
            $table->addIndexInfo('userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));

        /// Launch create table for course_modules_completion
            create_table($table);
        }

    /// Define field availablefrom to be added to course_modules
        $table = new XMLDBTable('course_modules');
        $field = new XMLDBField('availablefrom');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'completionexpected');

    /// Conditionally launch add field availablefrom
        $result = $result && add_field($table, $field);

    /// Define field availableuntil to be added to course_modules
        $field = new XMLDBField('availableuntil');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'availablefrom');

    /// Conditionally launch add field availableuntil
        $result = $result && add_field($table, $field);

    /// Define field showavailability to be added to course_modules
        $field = new XMLDBField('showavailability');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'availableuntil');

    /// Conditionally launch add field showavailability
        $result = $result && add_field($table, $field);

    /// Define table course_modules_availability to be created
        $table = new XMLDBTable('course_modules_availability');

    /// Adding fields to table course_modules_availability
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('coursemoduleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('sourcecmid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('requiredcompletion', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('gradeitemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('grademin', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null, null, null);
        $table->addFieldInfo('grademax', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null, null, null);

    /// Adding keys to table course_modules_availability
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('coursemoduleid', XMLDB_KEY_FOREIGN, array('coursemoduleid'), 'course_modules', array('id'));
        $table->addKeyInfo('sourcecmid', XMLDB_KEY_FOREIGN, array('sourcecmid'), 'course_modules', array('id'));
        $table->addKeyInfo('gradeitemid', XMLDB_KEY_FOREIGN, array('gradeitemid'), 'grade_items', array('id'));

    /// Conditionally launch create table for course_modules_availability
        if (!table_exists($table)) {
            create_table($table);
        }

    /// Changes to modinfo mean we need to rebuild course cache
        rebuild_course_cache(0,true);

    /// Add course completion tables
    /// Define table course_completion_aggr_methd to be created
        $table = new XMLDBTable('course_completion_aggr_methd');

    /// Adding fields to table course_completion_aggr_methd
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->addFieldInfo('criteriatype', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('method', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->addFieldInfo('value', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null);

    /// Adding keys to table course_completion_aggr_methd
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Adding indexes to table course_completion_aggr_methd
        $table->addIndexInfo('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
        $table->addIndexInfo('criteriatype', XMLDB_INDEX_NOTUNIQUE, array('criteriatype'));

    /// Conditionally launch create table for course_completion_aggr_methd
        if (!table_exists($table)) {
            create_table($table);
        }

    /// Define table course_completion_criteria to be created
        $table = new XMLDBTable('course_completion_criteria');

    /// Adding fields to table course_completion_criteria
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->addFieldInfo('criteriatype', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->addFieldInfo('module', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->addFieldInfo('moduleinstance', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('enrolperiod', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('date', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('gradepass', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null);
        $table->addFieldInfo('role', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('lock', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);

    /// Adding keys to table course_completion_criteria
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Adding indexes to table course_completion_criteria
        $table->addIndexInfo('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
        $table->addIndexInfo('lock', XMLDB_INDEX_NOTUNIQUE, array('lock'));

    /// Conditionally launch create table for course_completion_criteria
        if (!table_exists($table)) {
            create_table($table);
        }


    /// Define table course_completion_crit_compl to be created
        $table = new XMLDBTable('course_completion_crit_compl');

    /// Adding fields to table course_completion_crit_compl
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->addFieldInfo('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->addFieldInfo('criteriaid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->addFieldInfo('gradefinal', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null);
        $table->addFieldInfo('unenroled', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('deleted', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('timecompleted', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);

    /// Adding keys to table course_completion_crit_compl
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Adding indexes to table course_completion_crit_compl
        $table->addIndexInfo('userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));
        $table->addIndexInfo('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
        $table->addIndexInfo('criteriaid', XMLDB_INDEX_NOTUNIQUE, array('criteriaid'));
        $table->addIndexInfo('timecompleted', XMLDB_INDEX_NOTUNIQUE, array('timecompleted'));

    /// Conditionally launch create table for course_completion_crit_compl
        if (!table_exists($table)) {
            create_table($table);
        }


    /// Define table course_completion_notify to be created
        $table = new XMLDBTable('course_completion_notify');

    /// Adding fields to table course_completion_notify
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->addFieldInfo('role', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->addFieldInfo('message', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timesent', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

    /// Adding keys to table course_completion_notify
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Adding indexes to table course_completion_notify
        $table->addIndexInfo('course', XMLDB_INDEX_NOTUNIQUE, array('course'));

    /// Conditionally launch create table for course_completion_notify
        if (!table_exists($table)) {
            create_table($table);
        }

    /// Define table course_completions to be created
        $table = new XMLDBTable('course_completions');

    /// Adding fields to table course_completions
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->addFieldInfo('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->addFieldInfo('deleted', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('timenotified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('timeenroled', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->addFieldInfo('timecompleted', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);

    /// Adding keys to table course_completions
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Adding indexes to table course_completions
        $table->addIndexInfo('userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));
        $table->addIndexInfo('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
        $table->addIndexInfo('timecompleted', XMLDB_INDEX_NOTUNIQUE, array('timecompleted'));

    /// Conditionally launch create table for course_completions
        if (!table_exists($table)) {
            create_table($table);
        }


    /// Add cols to course table
    /// Define field enablecompletion to be added to course
        $table = new XMLDBTable('course');
        $field = new XMLDBField('enablecompletion');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'defaultrole');

    /// Conditionally launch add field enablecompletion
        if (!field_exists($table, $field)) {
            add_field($table, $field);
        }

    /// Add cols to course completion criteria table
        $table = new XMLDBTable('course_completion_criteria');
        $field = new XMLDBField('courseinstance');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0', 'moduleinstance');

        if (!field_exists($table, $field)) {
            add_field($table, $field);
        }

        set_config('theme', 'mitms');
        set_config('defaulthtmleditor', 'tinymce');

    /// Create table competency_framework
        $table = new XMLDBTable('competency_framework');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('fullname', XMLDB_TYPE_TEXT, 'medium', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('shortname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('idnumber', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('isdefault', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('sortorder', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('visible', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('hidecustomfields', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('showitemfullname', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('showdepthfullname', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addIndexInfo('sortorder', XMLDB_INDEX_UNIQUE, array('sortorder'));
        $result = $result && create_table($table);

    /// Create table competency_depth
        $table = new XMLDBTable('competency_depth');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('fullname', XMLDB_TYPE_TEXT, 'medium', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('shortname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('depthlevel', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('frameworkid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

    /// Create table competency_depth_info_category
        $table = new XMLDBTable('competency_depth_info_category');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('sortorder', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('depthid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

    /// Create table competency_depth_info_field
        $table = new XMLDBTable('competency_depth_info_field');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('fullname', XMLDB_TYPE_TEXT, 'medium', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('shortname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('depthid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('datatype', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('sortorder', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('categoryid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('hidden', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('locked', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('required', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('forceunique', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('defaultdata', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('param1', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('param2', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('param3', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('param4', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('param5', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

    /// Create table competency
        $table = new XMLDBTable('competency');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('fullname', XMLDB_TYPE_TEXT, 'medium', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('shortname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('idnumber', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->addFieldInfo('frameworkid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('path', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('depthid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('parentid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('sortorder', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('visible', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('aggregationmethod', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('scaleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('proficiencyexpected', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('evidencecount', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

    /// Create table competency_depth_info_data
        $table = new XMLDBTable('competency_depth_info_data');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('fieldid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('competencyid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('data', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

    /// Create table competency_relations
        $table = new XMLDBTable('competency_relations');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('id1', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('id2', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

    /// Create table competency_evidence_items
        $table = new XMLDBTable('competency_evidence_items');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('competencyid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('itemtype', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('itemmodule', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('iteminstance', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

    /// Create table competency_scale
        $table = new XMLDBTable('competency_scale');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

    /// Create table competency_scale_values
        $table = new XMLDBTable('competency_scale_values');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('name', XMLDB_TYPE_TEXT, 'medium', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('idnumber', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('scaleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('numericscore', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null);
        $table->addFieldInfo('sortorder', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

    /// Create table competency_scale_assignments
        $table = new XMLDBTable('competency_scale_assignments');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('scaleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('frameworkid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

    /// Create table competency_template
        $table = new XMLDBTable('competency_template');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('frameworkid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('fullname', XMLDB_TYPE_TEXT, 'medium', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('shortname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('visible', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('competencycount', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

    /// Create table competency_template_assignment
        $table = new XMLDBTable('competency_template_assignment');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('templateid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('type', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('instanceid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

    /// Create table competency_template_competencies
        $table = new XMLDBTable('competency_template_competencies');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('templateid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('competencyid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('priority', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('proficiencyexpected', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('hidden', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

    /// Create table organisation_framework
        $table = new XMLDBTable('organisation_framework');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('fullname', XMLDB_TYPE_TEXT, 'medium', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('shortname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('idnumber', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('isdefault', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('sortorder', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('visible', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('hidecustomfields', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('showitemfullname', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('showdepthfullname', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addIndexInfo('sortorder', XMLDB_INDEX_UNIQUE, array('sortorder'));
        $result = $result && create_table($table);

    /// Create table organisation_depth
        $table = new XMLDBTable('organisation_depth');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('fullname', XMLDB_TYPE_TEXT, 'medium', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('shortname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('depthlevel', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('frameworkid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

    /// Create table organisation_depth_info_category
        $table = new XMLDBTable('organisation_depth_info_category');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('sortorder', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('depthid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

    /// Create table organisation_depth_info_field
        $table = new XMLDBTable('organisation_depth_info_field');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('fullname', XMLDB_TYPE_TEXT, 'medium', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('shortname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('depthid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('datatype', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('sortorder', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('categoryid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('hidden', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('locked', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('required', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('forceunique', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('defaultdata', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('param1', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('param2', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('param3', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('param4', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('param5', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

    /// Create table organisation
        $table = new XMLDBTable('organisation');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('fullname', XMLDB_TYPE_TEXT, 'medium', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('shortname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('idnumber', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->addFieldInfo('frameworkid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('path', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('depthid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('parentid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('sortorder', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('visible', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

    /// Create table organisation_depth_info_data
        $table = new XMLDBTable('organisation_depth_info_data');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('fieldid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('organisationid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('data', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

    /// Create table organisation_relations
        $table = new XMLDBTable('organisation_relations');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('id1', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('id2', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

    /// Create table position_framework
        $table = new XMLDBTable('position_framework');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('fullname', XMLDB_TYPE_TEXT, 'medium', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('shortname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('idnumber', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('isdefault', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('sortorder', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('visible', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('hidecustomfields', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('showitemfullname', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('showdepthfullname', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addIndexInfo('sortorder', XMLDB_INDEX_UNIQUE, array('sortorder'));
        $result = $result && create_table($table);

    /// Create table position_depth
        $table = new XMLDBTable('position_depth');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('fullname', XMLDB_TYPE_TEXT, 'medium', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('shortname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('depthlevel', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('frameworkid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

    /// Create table position_depth_info_category
        $table = new XMLDBTable('position_depth_info_category');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('sortorder', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('depthid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

    /// Create table position_depth_info_field
        $table = new XMLDBTable('position_depth_info_field');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('fullname', XMLDB_TYPE_TEXT, 'medium', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('shortname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('depthid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('datatype', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('sortorder', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('categoryid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('hidden', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('locked', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('required', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('forceunique', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('defaultdata', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('param1', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('param2', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('param3', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('param4', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('param5', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

    /// Create table position
        $table = new XMLDBTable('position');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('fullname', XMLDB_TYPE_TEXT, 'medium', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('shortname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('idnumber', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('frameworkid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('path', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('depthid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('parentid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('sortorder', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('visible', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timevalidfrom', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('timevalidto', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

    /// Create table position_depth_info_data
        $table = new XMLDBTable('position_depth_info_data');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('fieldid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('positionid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('data', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

    /// Create table position_relations
        $table = new XMLDBTable('position_relations');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('id1', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('id2', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

    /// Create table position_assignment
        $table = new XMLDBTable('position_assignment');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('fullname', XMLDB_TYPE_TEXT, 'medium', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('shortname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('idnumber', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('positionid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('reportstoid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('type', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timevalidfrom', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('timevalidto', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

    /// Create table position_assignment_history
        $table = new XMLDBTable('position_assignment_history');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('fullname', XMLDB_TYPE_TEXT, 'medium', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('shortname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('idnumber', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('positionid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('reportstoid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('type', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timevalidfrom', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('timevalidto', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timefinished', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

    /// Create table competency_evidence
        $table = new XMLDBTable('competency_evidence');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('competencyid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('positionid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('organisationid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('assessorid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('assessorname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('assessmenttype', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('proficiency', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

    }

    if ($result && $oldversion < 2010011000) {

    /// Create table idp
        $table = new XMLDBTable('idp');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('name', XMLDB_TYPE_TEXT, 'medium', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('startdate', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('enddate', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addIndexInfo('userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));
        $result = $result && create_table($table);

    /// Create table idp_revision
        $table = new XMLDBTable('idp_revision');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('idp', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('ctime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('mtime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('submittedtime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('withdrawntime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('evaluatedtime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('evaluationcomment', XMLDB_TYPE_TEXT, 'medium', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('visible', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addIndexInfo('idp', XMLDB_INDEX_NOTUNIQUE, array('idp'));
        $result = $result && create_table($table);

    /// Create table idp_approval
        $table = new XMLDBTable('idp_approval');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('revision', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('approvedby', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('onbehalfof', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('ctime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addIndexInfo('revision', XMLDB_INDEX_NOTUNIQUE, array('revision'));
        $result = $result && create_table($table);

    /// Create table idp_revision_competency
        $table = new XMLDBTable('idp_revision_competency');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('revision', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('competency', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('ctime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('grade', XMLDB_TYPE_INTEGER, '8', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('postapproval', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addIndexInfo('revision', XMLDB_INDEX_NOTUNIQUE, array('revision'));
        $result = $result && create_table($table);

    /// Create table idp_revision_course
        $table = new XMLDBTable('idp_revision_course');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('revision', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('ctime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('grade', XMLDB_TYPE_INTEGER, '8', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('postapproval', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addIndexInfo('revision', XMLDB_INDEX_NOTUNIQUE, array('revision'));
        $result = $result && create_table($table);

    /// Create table idp_revision_comment
        $table = new XMLDBTable('idp_revision_comment');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('revision', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('author', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('contents', XMLDB_TYPE_TEXT, 'medium', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('ctime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addIndexInfo('revision', XMLDB_INDEX_NOTUNIQUE, array('revision'));
        $result = $result && create_table($table);

    /// Create table idp_list_item
        $table = new XMLDBTable('idp_list_item');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('revision', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('author', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('contents', XMLDB_TYPE_TEXT, 'medium', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addIndexInfo('revision', XMLDB_INDEX_NOTUNIQUE, array('revision'));
        $result = $result && create_table($table);
    }

    if ($result && $oldversion < 2010011001) {
    /// Create table idp_revision_competency
        $table = new XMLDBTable('idp_revision_competencytemplate');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('revision', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('competencytemplate', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('ctime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('grade', XMLDB_TYPE_INTEGER, '8', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('postapproval', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addIndexInfo('revision', XMLDB_INDEX_NOTUNIQUE, array('revision'));
        $result = $result && create_table($table);
    }

    if ($result && $oldversion < 2010021102) {
    // Add missing fields
        $table = new XMLDBTable('position_assignment');

        $field = new XMLDBField('organisationid');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $result = $result && add_field($table, $field);

        $table = new XMLDBTable('position_assignment_history');

        $field = new XMLDBField('organisationid');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $result = $result && add_field($table, $field);


    /// Fix sequences and nullables
        $table = new XMLDBTable('position_assignment');

        $field = new XMLDBField('userid');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $result = $result && change_field_type($table, $field);

        $field = new XMLDBField('positionid');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $result = $result && change_field_type($table, $field);

        $field = new XMLDBField('reportstoid');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $result = $result && change_field_type($table, $field);

        $field = new XMLDBField('type');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $result = $result && change_field_type($table, $field);


        $table = new XMLDBTable('competency_depth_info_data');

        $field = new XMLDBField('fieldid');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $result = $result && change_field_type($table, $field);

        $field = new XMLDBField('competencyid');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $result = $result && change_field_type($table, $field);


        $table = new XMLDBTable('organisation_depth_info_data');

        $field = new XMLDBField('fieldid');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $result = $result && change_field_type($table, $field);

        $field = new XMLDBField('organisationid');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $result = $result && change_field_type($table, $field);


        $table = new XMLDBTable('position_assignment_history');

        $field = new XMLDBField('userid');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $result = $result && change_field_type($table, $field);

        $field = new XMLDBField('positionid');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $result = $result && change_field_type($table, $field);

        $field = new XMLDBField('reportstoid');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $result = $result && change_field_type($table, $field);

        $field = new XMLDBField('type');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $result = $result && change_field_type($table, $field);


        $table = new XMLDBTable('idp');

        $field = new XMLDBField('userid');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $result = $result && change_field_type($table, $field);


        $table = new XMLDBTable('idp_revision');

        $field = new XMLDBField('idp');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $result = $result && change_field_type($table, $field);


        $table = new XMLDBTable('idp_approval');

        $field = new XMLDBField('revision');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $result = $result && change_field_type($table, $field);

        $field = new XMLDBField('approvedby');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $result = $result && change_field_type($table, $field);

        $field = new XMLDBField('onbehalfof');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $result = $result && change_field_type($table, $field);


        $table = new XMLDBTable('idp_revision_competency');

        $field = new XMLDBField('revision');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $result = $result && change_field_type($table, $field);

        $field = new XMLDBField('competency');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $result = $result && change_field_type($table, $field);


        $table = new XMLDBTable('idp_revision_course');

        $field = new XMLDBField('revision');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $result = $result && change_field_type($table, $field);

        $field = new XMLDBField('course');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $result = $result && change_field_type($table, $field);


        $table = new XMLDBTable('idp_revision_comment');

        $field = new XMLDBField('revision');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $result = $result && change_field_type($table, $field);

        $field = new XMLDBField('author');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $result = $result && change_field_type($table, $field);


        $table = new XMLDBTable('idp_list_item');

        $field = new XMLDBField('revision');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $result = $result && change_field_type($table, $field);

        $field = new XMLDBField('author');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $result = $result && change_field_type($table, $field);
    }

    if ($result && $oldversion < 2010012800) {
    /// Create table learning_report
        $table = new XMLDBTable('learning_report');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('fullname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('shortname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('source', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('restriction', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->addFieldInfo('filters', XMLDB_TYPE_TEXT, 'medium', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('columns', XMLDB_TYPE_TEXT, 'medium', XMLDB_UNSIGNED, null, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addIndexInfo('shortname', XMLDB_INDEX_UNIQUE, array('shortname'));
        $result = $result && create_table($table);
    }

    if ($result && $oldversion < 2010020500) {
    // increase space for restriction data
        $table = new XMLDBTable('learning_report');
        $field = new XMLDBField('restriction');
        $result = $result && drop_field($table, $field);
        $field = new XMLDBField('restriction');
        $field->setAttributes(XMLDB_TYPE_TEXT, 'medium', XMLDB_UNSIGNED, null, null, null);
        $result = $result && add_field($table, $field);
    }

    if ($result && $oldversion < 2010020800) {
        // rename learning_report to report_builder
        $table = new XMLDBTable('learning_report');
        $result = $result && rename_table($table, 'report_builder');
    }

    if ($result && $oldversion < 2010021700) {
    /// Create table competency_evidence_items_evidence
        $table = new XMLDBTable('competency_evidence_items_evidence');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('competencyid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('itemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('status', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('proficiencymeasured', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);
    }

    if ($result && $oldversion < 2010021701) {
        // add hidden field to report builder table
        $table = new XMLDBTable('report_builder');
        $field = new XMLDBField('hidden');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, 0);
        $result = $result && add_field($table, $field);
    }

    if ($result && $oldversion < 2010022401) {
    /// Create table competency_evidence_items
        $table = new XMLDBTable('position_competencies');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('positionid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('competencyid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('templateid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);
    }

    if ($result && $oldversion < 2010022601) {
        // Limit RPL field to 255 characters
        $table = new XMLDBTable('course_completions');
        $field = new XMLDBField('rpl');
        $field->setType(XMLDB_TYPE_CHAR);
        $field->setLength(255);
        $result = $result && change_field_type($table, $field, true, true);

    }

    if ($result && $oldversion < 2010030600) {
        mitms_reset_stickyblocks(true);
    }

    if ($result && $oldversion < 2010031000) {
        // Remove not-null constraint from idp_approval.onbehalfof
        $table = new XMLDBTable('idp_approval');
        $field = new XMLDBField('onbehalfof');
        $field->setType(XMLDB_TYPE_INTEGER);
        $field->setNotNull(false);
        $result = $result && change_field_notnull($table, $field, true, true);
    }

    if ($result && $oldversion < 2010031100) {
        // Add due dates to IDP components
        $table = new XMLDBTable('idp_revision_course');
        $field = new XMLDBField('duedate');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $result = $result && add_field($table, $field);

        $table = new XMLDBTable('idp_revision_competency');
        $field = new XMLDBField('duedate');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $result = $result && add_field($table, $field);

        $table = new XMLDBTable('idp_revision_competencytemplate');
        $field = new XMLDBField('duedate');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $result = $result && add_field($table, $field);
    }

    if ($result && $oldversion < 2010031200) {
    /// Add reaggregate to competency evidence table
        $table = new XMLDBTable('competency_evidence');
        $field = new XMLDBField('reaggregate');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'timemodified');
        $result = $result && add_field($table, $field);
    }

    if ($result && $oldversion < 2010031500) {
    /// Add proficient to competency scale table
        $table = new XMLDBTable('competency_scale');
        $field = new XMLDBField('proficient');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, null, null, null, 'description');
        $result = $result && add_field($table, $field);
    }

    if ($result && $oldversion < 2010031600) {
    /// Add manual flag to competency evidence table
        $table = new XMLDBTable('competency_evidence');
        $field = new XMLDBField('manual');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $result = $result && add_field($table, $field);

    /// Make sure all existing evidence are set to manual
        execute_sql("
            UPDATE
                {$CFG->prefix}competency_evidence
            SET
                manual = 1
        ");
    }

    if ($result && $oldversion < 2010031601) {
    /// Add default to competency scale table
        $table = new XMLDBTable('competency_scale');
        $field = new XMLDBField('defaultid');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, null, null, null, 'proficient');
        $result = $result && add_field($table, $field);
    }

    if ($result && $oldversion < 2010031800) {
    /// Add missing indexes
        $table = new XMLDBTable('competency');

        $index = new XMLDBIndex('parentid');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('parentid'));
        $result = $result && add_index($table, $index);

        $index = new XMLDBIndex('frameworkid');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('frameworkid'));
        $result = $result && add_index($table, $index);

        $index = new XMLDBIndex('depthid');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('depthid'));
        $result = $result && add_index($table, $index);

        $index = new XMLDBIndex('scaleid');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('scaleid'));
        $result = $result && add_index($table, $index);


        $table = new XMLDBTable('competency_evidence');

        $index = new XMLDBIndex('competencyid');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('competencyid'));
        $result = $result && add_index($table, $index);

        $index = new XMLDBIndex('userid');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('userid'));
        $result = $result && add_index($table, $index);

        $index = new XMLDBIndex('reaggregate');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('reaggregate'));
        $result = $result && add_index($table, $index);

        $index = new XMLDBIndex('manual');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('manual'));
        $result = $result && add_index($table, $index);


        $table = new XMLDBTable('competency_evidence_items');

        $index = new XMLDBIndex('competencyid');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('competencyid'));
        $result = $result && add_index($table, $index);

        $index = new XMLDBIndex('itemtype');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('itemtype'));
        $result = $result && add_index($table, $index);

        $index = new XMLDBIndex('iteminstance');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('iteminstance'));
        $result = $result && add_index($table, $index);


        $table = new XMLDBTable('competency_evidence_items_evidence');

        $index = new XMLDBIndex('itemid');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('itemid'));
        $result = $result && add_index($table, $index);

        $index = new XMLDBIndex('userid');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('userid'));
        $result = $result && add_index($table, $index);

        $index = new XMLDBIndex('proficiencymeasured');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('proficiencymeasured'));
        $result = $result && add_index($table, $index);

        $index = new XMLDBIndex('timemodified');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('timemodified'));
        $result = $result && add_index($table, $index);


        $table = new XMLDBTable('competency_scale');

        $index = new XMLDBIndex('proficient');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('proficient'));
        $result = $result && add_index($table, $index);
    }

    if ($result && $oldversion < 2010031801) {
    /// Remove not null constraints from competency_evidence
        $table = new XMLDBTable('competency_evidence');

        $field = new XMLDBField('assessorname');
        $field->setAttributes(XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $result = $result && change_field_type($table, $field);

        $field = new XMLDBField('assessmenttype');
        $field->setAttributes(XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $result = $result && change_field_type($table, $field);
    }

    if ($result && $oldversion < 2010032401) {
    /// Update existing IDP capability assignments
        $caps = array(
            'viewowncomment',
            'viewownlist',
            'viewownplan',
            'withdrawownplan',
            'submitownplan',
            'editownplan',
            'addowncomment'
        );

        $system_context = get_context_instance(CONTEXT_SYSTEM);
        $user_role = get_record('role', 'shortname', 'user');

        foreach ($caps as $cap) {
            assign_capability(
                'moodle/local:'.$cap,
                CAP_ALLOW,
                $user_role->id,
                $system_context->id,
                true
            );
        }
    }

    if ($result && $oldversion < 2010033000) {
    // increase space for restriction data
        $table = new XMLDBTable('competency_framework');
        $field = new XMLDBField('isdefault');
        $result = $result && drop_field($table, $field);

        $table = new XMLDBTable('organisation_framework');
        $field = new XMLDBField('isdefault');
        $result = $result && drop_field($table, $field);

        $table = new XMLDBTable('position_framework');
        $field = new XMLDBField('isdefault');
        $result = $result && drop_field($table, $field);
    }

    if ($result && $oldversion < 2010040800) {
        // Create new table idp_competency_eval
        $table = new XMLDBTable('idp_competency_eval');
        if(!table_exists($table)) {

            // Adding fields to table
            $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
            $table->addFieldInfo('revisionid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('competencyid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('scalevalueid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);

            // Adding keys to table
            $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

            // Adding indexes to table
            $table->addIndexInfo('revisionid', XMLDB_INDEX_NOTUNIQUE, array('revisionid'));

            // Launch create table for course_modules_completion
            $result = $result && create_table($table);
        }

        // Drop obsolete column idp_revision_competency.grade
        $table = new XMLDBTable('idp_revision_competency');
        $field = new XMLDBField('grade');
        $result = $result && drop_field($table, $field);

        // Drop obsolete column idp_revision_competencytemplate.grade
        $table = new XMLDBTable('idp_revision_competencytemplate');
        $field = new XMLDBField('grade');
        $result = $result && drop_field($table, $field);
    }

    // Reorganizing the IDP-related capabilities
    if ($result && $oldversion < 2010041400){
        // Delete these because they're not in use
        $deletelist = array(
            'moodle/local:editownfavourite',
            'moodle/local:receivenotification',
            'moodle/local:viewfavourite',
            'moodle/local:viewownfavourite',
            'moodle/local:withdrawownplan'
        );
        foreach ( $deletelist as $deletecap ){
            if ( false === delete_records('role_capabilities','capability',$deletecap) ){
                $result = false;
            }
            if ( false === delete_records('capabilities','name',$deletecap) ){
                $result = false;
            }
        }

        // These are having their names changed.
        $oldnewmap = array(
            'moodle/local:addcomment' => 'moodle/local:idpaddcomment',
            'moodle/local:addowncomment' => 'moodle/local:idpaddowncomment',
            'moodle/local:approveplan' => 'moodle/local:idpapproveplan',
            'moodle/local:approveplanonbehalf' => 'moodle/local:idpapproveplanonbehalf',
            'moodle/local:editownplan' => 'moodle/local:idpeditownplan',
            'moodle/local:submitownplan' => 'moodle/local:idpsubmitownplan',
            'moodle/local:manageroverview' => 'moodle/local:idpmanageroverview',
            'moodle/local:managerownoverview' => 'moodle/local:idpmanagerownoverview',
            'moodle/local:traineeoverview' => 'moodle/local:idptraineeoverview',
            'moodle/local:traineeownoverview' => 'moodle/local:idptraineeownoverview',
            'moodle/local:viewcomment' => 'moodle/local:idpviewcomment',
            'moodle/local:viewlist' => 'moodle/local:idpviewlist',
            'moodle/local:viewowncomment' => 'moodle/local:idpviewowncomment',
            'moodle/local:viewownlist' => 'moodle/local:idpviewownlist',
            'moodle/local:viewownplan' => 'moodle/local:idpviewownplan',
            'moodle/local:viewplan' => 'moodle/local:idpviewplan',
        );

        foreach ( $oldnewmap as $oldname => $newname ){
            // Delete the old named capability (the new one will be created when
            // access.php is processed)
            if ( false === delete_records('capabilities','name',$oldname) ){
                $result = false;
            }
            // Update the role->capability mappings to use the new name
            if ( false === execute_sql("
                update {$CFG->prefix}role_capabilities
                set capability='{$newname}'
                where capability='{$oldname}'
            ") ){
                $result = false;
            }
        }
    }

    if ($result && $oldversion < 2010041900){

        // Renaming hierarchy items types to be shorter, so the tables named
        // after them won't break Oracle's 30-char size limit
        $substlist = array(
            'competency' => 'comp',
            'organisation' => 'org',
            'position' => 'pos'
        );

        $tablelist = array(
            'competency',
            'competency_depth',
            'competency_depth_info_category',
            'competency_depth_info_data',
            'competency_depth_info_field',
            'competency_evidence',
            'competency_evidence_items',
            'competency_evidence_items_evidence',
            'competency_framework',
            'competency_relations',
            'competency_scale',
            'competency_scale_assignments',
            'competency_scale_values',
            'competency_template',
            'competency_template_assignment',
            'competency_template_competencies',
            'organisation',
            'organisation_depth',
            'organisation_depth_info_category',
            'organisation_depth_info_data',
            'organisation_depth_info_field',
            'organisation_framework',
            'organisation_relations',
            'position',
            'position_assignment',
            'position_assignment_history',
            'position_competencies',
            'position_depth',
            'position_depth_info_category',
            'position_depth_info_data',
            'position_depth_info_field',
            'position_framework',
            'position_relations'
        );

        foreach( $tablelist as $oldtablename ){
            $newtablename = $oldtablename;
            foreach( $substlist as $oldtype => $newtype ){
                $newtablename = str_replace($oldtype, $newtype, $newtablename);
            }
            $table = new XMLDBTable($oldtablename);
            $result = $result && rename_table($table, $newtablename);
        }

        // Rename the one IDP table whose name is too long
        $table = new XMLDBTable('idp_revision_competencytemplate');
        $result = $result && rename_table($table, 'idp_revision_competencytmpl');
    }

    if ($result && $oldversion < 2010042800){
            // Delete related competency records where a competency is being related to itself
            $result = $result && execute_sql("delete from {$CFG->prefix}comp_relations where id1=id2");

            // Delete duplicate related competency records
            $sql = 'select count(*), min(id) as m, least(id1,id2) as a, greatest(id1,id2) as b ';
            $sql .= "from {$CFG->prefix}comp_relations ";
            $sql .= 'group by least(id1,id2), greatest(id1,id2) ';
            $sql .= 'having count(*) > 1';
            $duperecords = get_records_sql($sql);
            if ( is_array( $duperecords ) ){
                foreach ($duperecords as $duperec){
                    $result = $result && execute_sql(
                            "delete from {$CFG->prefix}comp_relations where id<>{$duperec->m} and "
                            . "((id1={$duperec->a} and id2={$duperec->b}) or (id1={$duperec->b} and  id2={$duperec->a}))"
                    );
                }
            }
    }

    if ($result && $oldversion < 2010050700) {

        // add indexes to speed up report performance
        $table = new XMLDBTable('pos_assignment');
        $index = new XMLDBIndex('userid');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('userid'));
        $result = $result && add_index($table, $index);

        $table = new XMLDBTable('facetoface_session_data');
        $index = new XMLDBIndex('sessionid');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('sessionid'));
        $result = $result && add_index($table, $index);

        $table = new XMLDBTable('facetoface_session_data');
        $index = new XMLDBIndex('fieldid');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('fieldid'));
        $result = $result && add_index($table, $index);

        // lots of changes to report builder db structure
        mitms_migrate_old_report_builder_reports($result);

    }


    if ($result && $oldversion < 2010051100) {
        $table = new XMLDBTable('report_builder_saved');
        if(!table_exists($table)) {

        /// Adding fields to table report_builder_saved
            $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
            $table->addFieldInfo('reportid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
            $table->addFieldInfo('search', XMLDB_TYPE_TEXT, 'medium', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
            $table->addFieldInfo('public', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);

        /// Adding keys to table report_builder_saved
            $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

        /// Adding indexes to table report_builder_saved
            $table->addIndexInfo('reportid', XMLDB_INDEX_NOTUNIQUE, array('reportid'));
            $table->addIndexInfo('userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));

        /// Launch create table for report_builder_saved
            create_table($table);
        }
    }

    // rename field to avoid mysql reserved word
    if ($result && $oldversion < 2010051900) {
        $table = new XMLDBTable('comp_scale_values');
        $field = new XMLDBField('numeric');
        $field->setAttributes(XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null);
        if(field_exists($table, $field)) {
            $result = $result && rename_field($table, $field, 'numericscore');
        }
    }

    if ($result && $oldversion < 2010052600) {
    /// Create table report_heading_items
        $table = new XMLDBTable('report_heading_items');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('type', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('heading', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('defaultvalue', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->addFieldInfo('sortorder', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);
    }

    if ($result && $oldversion < 2010062300) {
        // Add pos/org data to course_completions
        // (to bring it up to spec with upstream)
        $table = new XMLDBTable('course_completions');

        $field = new XMLDBField('organisationid');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null, 'rpl');

        if (!field_exists($table, $field)) {
            add_field($table, $field);
        }

        $field = new XMLDBField('positionid');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null, 'organisationid');

        if (!field_exists($table, $field)) {
            add_field($table, $field);
        }
    }

    if ($result && $oldversion < 2010070900) {

        // Rename misspelled column if exists
        $table = new XMLDBTable('course');
        $field = new XMLDBField('compleitonstartonenrol');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'enablecompletion');

        if(field_exists($table, $field)) {
            $result = $result && rename_field($table, $field, 'completionstartonenrol');
        }

        // Add completion setting to course table
        $table = new XMLDBTable('course_completions');
        $field = new XMLDBField('timestarted');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null, 'timeenrolled');

        if (!field_exists($table, $field)) {
            add_field($table, $field);
        }

        // Add reaggregate field
        $field = new XMLDBField('reaggregate');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'rpl');

        if (!field_exists($table, $field)) {
            add_field($table, $field);
        }
    }

    if ($result && $oldversion < 2010070901) {
        $table = new XMLDBTable('block_guides_guide');
        $field = new XMLDBField('identifier');
        $field->setAttributes(XMLDB_TYPE_CHAR, '50', null, null, null, null, null);
        $result = $result && add_field($table, $field);
    }

    if ($result && $oldversion < 2010070902) {
        $success = true;
        $guides = get_records('block_guides_guide');
        if (!$guides) {
            $guides = array();
        }

        $navlinks = array();
        $strguides = get_string('guides','block/guides');
        $navlinks[] = array('name' => $strguides, 'link' => "index.php", 'type' => 'misc');
        $navlinks[] = array('name' => 'addguides', 'link' => null, 'type' => 'misc');
        $navigation = build_navigation($navlinks);
        print_header('Add Guides: ', 'Add Guides: ', $navigation, "", "", true);

        # TODO: opendir, readdir, if !exists, require

        $dir = $CFG->dirroot . '/guides/guidedata/';
        $files = scandir($dir);
        foreach ($files as $file){
            if (strpos($file, '.') === 0) {
                continue;
            }
            if (!is_file($dir . $file)) {
                // Not interested in directories etc
                continue;
            }
            $matches = array();
            if (!preg_match('/[0-9]*_([A-Za-z_-][A-Za-z0-9_\ -]*)\.php/', $file, $matches)) {
                continue;
            }
            $basename = $matches[1];
            $found = false;
            foreach ($guides as $guide) {
                if ($guide->identifier == $basename) {
                    $found = true;
                }
            }
            if ($found) {
                # We already know about that guide
                continue;
            }
            unset($guide);
            require_once($dir . $file);
            print "New guide found - adding $guide->name <br />\n";
            if(!insert_record("block_guides_guide",addslashes_object($guide)))
                $success = false;
        }

        $result = $result && $success;

    }

    return $result;
}
