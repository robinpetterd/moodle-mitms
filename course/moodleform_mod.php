<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once ($CFG->libdir.'/formslib.php');

if(!empty($CFG->enableavailability)) {
    require_once($CFG->libdir.'/conditionlib.php');
}
/**
 * This class adds extra methods to form wrapper specific to be used for module
 * add / update forms (mod/{modname}.mod_form.php replaces deprecated mod/{modname}/mod.html
 *
 */
class moodleform_mod extends moodleform {
    /**
     * Instance of the module that is being updated. This is the id of the {prefix}{modulename}
     * record. Can be used in form definition. Will be "" if this is an 'add' form and not an
     * update one.
     *
     * @var mixed
     */
    var $_instance;
    /**
     * Section of course that module instance will be put in or is in.
     * This is always the section number itself (column 'section' from 'course_sections' table).
     *
     * @var mixed
     */
    var $_section;
    /**
     * Coursemodle record of the module that is being updated. Will be null if this is an 'add' form and not an
     * update one.
      *
     * @var mixed
     */
    var $_cm;
    /**
     * List of modform features
     */
    var $_features;

    /**
     * @var array Custom completion-rule elements, if enabled
     */
    var $_customcompletionelements;

    function moodleform_mod($instance, $section, $cm) {
        $this->_instance = $instance;
        $this->_section = $section;
        $this->_cm = $cm;
        parent::moodleform('modedit.php');
    }

    /**
     * Only available on moodleform_mod.
     *
     * @param array $default_values passed by reference
     */
    function data_preprocessing(&$default_values){
    }

    /**
     * Each module which defines definition_after_data() must call this method using parent::definition_after_data();
     */
    function definition_after_data() {
        global $CFG, $COURSE;
        $mform =& $this->_form;

        if ($id = $mform->getElementValue('update')) {
            $modulename = $mform->getElementValue('modulename');
            $instance   = $mform->getElementValue('instance');

            if ($this->_features->gradecat) {
                $gradecat = false;
                if (!empty($CFG->enableoutcomes) and $this->_features->outcomes) {
                    if ($outcomes = grade_outcome::fetch_all_available($COURSE->id)) {
                        $gradecat = true;
                    }
                }
                if ($items = grade_item::fetch_all(array('itemtype'=>'mod', 'itemmodule'=>$modulename,
                                                   'iteminstance'=>$instance, 'courseid'=>$COURSE->id))) {
                    foreach ($items as $item) {
                        if (!empty($item->outcomeid)) {
                            $elname = 'outcome_'.$item->outcomeid;
                            if ($mform->elementExists($elname)) {
                                $mform->hardFreeze($elname); // prevent removing of existing outcomes
                            }
                        }
                    }
                    foreach ($items as $item) {
                        if (is_bool($gradecat)) {
                            $gradecat = $item->categoryid;
                            continue;
                        }
                        if ($gradecat != $item->categoryid) {
                            //mixed categories
                            $gradecat = false;
                            break;
                        }
                    }
                }

                if ($gradecat === false) {
                    // items and outcomes in different categories - remove the option
                    // TODO: it might be better to add a "Mixed categories" text instead
                    if ($mform->elementExists('gradecat')) {
                        $mform->removeElement('gradecat');
                    }
                }
            }
        }

        if ($COURSE->groupmodeforce) {
            if ($mform->elementExists('groupmode')) {
                $mform->hardFreeze('groupmode'); // groupmode can not be changed if forced from course settings
            }
        }

        if ($mform->elementExists('groupmode') and !$mform->elementExists('groupmembersonly') and empty($COURSE->groupmodeforce)) {
            $mform->disabledIf('groupingid', 'groupmode', 'eq', NOGROUPS);

        } else if (!$mform->elementExists('groupmode') and $mform->elementExists('groupmembersonly')) {
            $mform->disabledIf('groupingid', 'groupmembersonly', 'notchecked');

        } else if (!$mform->elementExists('groupmode') and !$mform->elementExists('groupmembersonly')) {
            // groupings have no use without groupmode or groupmembersonly
            if ($mform->elementExists('groupingid')) {
                $mform->removeElement('groupingid');
            }
        }

        // Completion: If necessary, freeze fields
        $completion=new completion_info($COURSE);
        if($completion->is_enabled()) {
            // If anybody has completed the activity, these options will be 'locked'
            $completedcount = empty($this->_cm)
                ? 0
                : $completion->count_user_data($this->_cm);

            $freeze=false;
            if(!$completedcount) {
                if($mform->elementExists('unlockcompletion')) {
                    $mform->removeElement('unlockcompletion');
                }
            } else {
                // Has the element been unlocked?
                if($mform->exportValue('unlockcompletion')) {
                    // Yes, add in warning text and set the hidden variable
                    $mform->insertElementBefore(
                        $mform->createElement('static','completedunlocked',
                            get_string('completedunlocked','completion'),
                            get_string('completedunlockedtext','completion')),
                        'unlockcompletion');
                    $mform->removeElement('unlockcompletion');
                    $mform->getElement('completionunlocked')->setValue(1);
                } else {
                    // No, add in the warning text with the count (now we know
                    // it) before the unlock button
                    $mform->insertElementBefore(
                        $mform->createElement('static','completedwarning',
                            get_string('completedwarning','completion'),
                            get_string('completedwarningtext','completion',$completedcount)),
                        'unlockcompletion');
                    $mform->setHelpButton('completedwarning', array('completionlocked', get_string('help_completionlocked', 'completion'), 'completion'));
                            
                    $freeze=true;
                }
            } 

            if($freeze) {
                $mform->freeze('completion');
                if($mform->elementExists('completionview')) {
                    $mform->freeze('completionview'); // don't use hardFreeze or checkbox value gets lost
                }
                if($mform->elementExists('completionusegrade')) {
                    $mform->freeze('completionusegrade');
                }
                $mform->freeze($this->_customcompletionelements);
            } 
        }

        // Availability conditions
        if (!empty($CFG->enableavailability) && $this->_cm) {
            $ci = new condition_info($this->_cm);
            $fullcm=$ci->get_full_course_module();

            $num=0;
            foreach($fullcm->conditionsgrade as $gradeitemid=>$minmax) {
                $groupelements=$mform->getElement('conditiongradegroup['.$num.']')->getElements();
                $groupelements[0]->setValue($gradeitemid);
                // These numbers are always in the format 0.00000 - the rtrims remove any final zeros and,
                // if it is a whole number, the decimal place.
                $groupelements[2]->setValue(is_null($minmax->min)?'':rtrim(rtrim($minmax->min,'0'),'.'));
                $groupelements[4]->setValue(is_null($minmax->max)?'':rtrim(rtrim($minmax->max,'0'),'.'));
                $num++;
            }

            if ($completion->is_enabled()) {
                $num=0;
                foreach($fullcm->conditionscompletion as $othercmid=>$state) {
                    $groupelements=$mform->getElement('conditioncompletiongroup['.$num.']')->getElements();
                    $groupelements[0]->setValue($othercmid);
                    $groupelements[1]->setValue($state);
                    $num++;
                }
            }
        }
    }

    // form verification
    function validation($data, $files) {
        global $COURSE;
        $errors = parent::validation($data, $files);

        $mform =& $this->_form;

        $errors = array();

        if ($mform->elementExists('name')) {
            $name = trim($data['name']);
            if ($name == '') {
                $errors['name'] = get_string('required');
            }
        }

        $grade_item = grade_item::fetch(array('itemtype'=>'mod', 'itemmodule'=>$data['modulename'],
                     'iteminstance'=>$data['instance'], 'itemnumber'=>0, 'courseid'=>$COURSE->id));
        if ($data['coursemodule']) {
            $cm = get_record('course_modules', 'id', $data['coursemodule']);
        } else {
            $cm = null;
        }

        if ($mform->elementExists('cmidnumber')) {
            // verify the idnumber
            if (!grade_verify_idnumber($data['cmidnumber'], $COURSE->id, $grade_item, $cm)) {
                $errors['cmidnumber'] = get_string('idnumbertaken');
            }
        }
        
        // Completion: Don't let them choose automatic completion without turning
        // on some conditions
        if(array_key_exists('completion',$data) && $data['completion']==COMPLETION_TRACKING_AUTOMATIC) {
            if(empty($data['completionview']) && empty($data['completionusegrade']) &&
                !$this->completion_rule_enabled($data)) {
                $errors['completion']=get_string('badautocompletion','completion');
            }
        }

        return $errors;
    }

    /**
     * Load in existing data as form defaults. Usually new entry defaults are stored directly in
     * form definition (new entry form); this function is used to load in data where values
     * already exist and data is being edited (edit entry form).
     *
     * @param mixed $default_values object or array of default values
     */
    function set_data($default_values) {
        if (is_object($default_values)) {
            $default_values = (array)$default_values;
        }
        $this->data_preprocessing($default_values);
        parent::set_data($default_values);
    }

    /**
     * Adds all the standard elements to a form to edit the settings for an activity module.
     *
     * @param mixed $features array or object describing supported features - groups, groupings, groupmembersonly, etc.
     * @param string $modname Name of module e.g. 'label'
     */
    function standard_coursemodule_elements($features=null, $modname=null){
        global $COURSE, $CFG;
        $mform =& $this->_form;

        // Guess module name if not supplied
        if(!$modname) {
            $matches=array();
            if(!preg_match('/^mod_([^_]+)_mod_form$/',$this->_formname,$matches)) {
                debugging('Use $modname parameter or rename form to mod_xx_mod_form, where xx is name of your module');
                error('Unknown module name for form');
            }
            $modname=$matches[1];
        }

        // deal with legacy $supportgroups param
        if ($features === true or $features === false) {
            $groupmode = $features;
            $this->_features = new object();
            $this->_features->groups = $groupmode;

        } else if (is_array($features)) {
            $this->_features = (object)$features;

        } else if (empty($features)) {
            $this->_features = new object();

        } else {
            $this->_features = $features;
        }

        if (!isset($this->_features->groups)) {
            $this->_features->groups = true;
        }

        if (!isset($this->_features->groupings)) {
            $this->_features->groupings = false;
        }

        if (!isset($this->_features->groupmembersonly)) {
            $this->_features->groupmembersonly = false;
        }

        if (!isset($this->_features->outcomes)) {
            $this->_features->outcomes = true;
        }

        if (!isset($this->_features->gradecat)) {
            $this->_features->gradecat = true;
        }

        if (!isset($this->_features->idnumber)) {
            $this->_features->idnumber = true;
        }

        if(!isset($this->_features->defaultcompletion)) {
            $this->_features->defaultcompletion = true;
        }

        $outcomesused = false;
        if (!empty($CFG->enableoutcomes) and $this->_features->outcomes) {
            if ($outcomes = grade_outcome::fetch_all_available($COURSE->id)) {
                $outcomesused = true;
                $mform->addElement('header', 'modoutcomes', get_string('outcomes', 'grades'));
                foreach($outcomes as $outcome) {
                    $mform->addElement('advcheckbox', 'outcome_'.$outcome->id, $outcome->get_name());
                }
            }
        }

        $mform->addElement('header', 'modstandardelshdr', get_string('modstandardels', 'form'));
        if ($this->_features->groups) {
            $options = array(NOGROUPS       => get_string('groupsnone'),
                             SEPARATEGROUPS => get_string('groupsseparate'),
                             VISIBLEGROUPS  => get_string('groupsvisible'));
            $mform->addElement('select', 'groupmode', get_string('groupmode'), $options, NOGROUPS);
            $mform->setHelpButton('groupmode', array('groupmode', get_string('groupmode')));
        }

        if (!empty($CFG->enablegroupings)) {
            if ($this->_features->groupings or $this->_features->groupmembersonly) {
                //groupings selector - used for normal grouping mode or also when restricting access with groupmembersonly
                $options = array();
                $options[0] = get_string('none');
                if ($groupings = get_records('groupings', 'courseid', $COURSE->id)) {
                    foreach ($groupings as $grouping) {
                        $options[$grouping->id] = format_string($grouping->name);
                    }
                }
                $mform->addElement('select', 'groupingid', get_string('grouping', 'group'), $options);
                $mform->setHelpButton('groupingid', array('grouping', get_string('grouping', 'group')));
                $mform->setAdvanced('groupingid');
            }

            if ($this->_features->groupmembersonly) {
                $mform->addElement('checkbox', 'groupmembersonly', get_string('groupmembersonly', 'group'));
                $mform->setHelpButton('groupmembersonly', array('groupmembersonly', get_string('groupmembersonly', 'group')));
                $mform->setAdvanced('groupmembersonly');
            }
        }

        $mform->addElement('modvisible', 'visible', get_string('visible'));

        if ($this->_features->idnumber) {
            $mform->addElement('text', 'cmidnumber', get_string('idnumbermod'));
            $mform->setHelpButton('cmidnumber', array('cmidnumber', get_string('idnumbermod')), true);
        }

        if ($this->_features->gradecat) {
            $categories = grade_get_categories_menu($COURSE->id, $outcomesused);
            $mform->addElement('select', 'gradecat', get_string('gradecategory', 'grades'), $categories);
        }

        if (!empty($CFG->enableavailability)) {
            // Conditional availability
            $mform->addElement('header', '', get_string('availabilityconditions', 'condition'));
            $mform->addElement('date_selector', 'availablefrom', get_string('availablefrom', 'condition'), array('optional'=>true));
            $mform->setHelpButton('availablefrom', array('conditiondates', get_string('help_conditiondates', 'condition'), 'condition'));
            $mform->addElement('date_selector', 'availableuntil', get_string('availableuntil', 'condition'), array('optional'=>true));
            $mform->setHelpButton('availableuntil', array('conditiondates', get_string('help_conditiondates', 'condition'), 'condition'));

            // Conditions based on grades
            $gradeoptions=array();
            $items=grade_item::fetch_all(array('courseid'=>$COURSE->id));
            $items = $items ? $items : array();
            foreach($items as $id=>$item) {
                $gradeoptions[$id]=$item->get_name();
            }
            asort($gradeoptions);
            $gradeoptions=array(0=>get_string('none','condition'))+$gradeoptions;

            $grouparray=array();
            $grouparray[] =& $mform->createElement('select','conditiongradeitemid','',$gradeoptions);
            $grouparray[] =& $mform->createElement('static', '', '',' '.get_string('grade_atleast','condition').' ');
            $grouparray[] =& $mform->createElement('text', 'conditiongrademin','',array('size'=>3));
            $grouparray[] =& $mform->createElement('static', '', '',' '.get_string('grade_upto','condition').' ');
            $grouparray[] =& $mform->createElement('text', 'conditiongrademax','',array('size'=>3));
            $mform->setType('conditiongrademin',PARAM_INT);
            $mform->setType('conditiongrademax',PARAM_INT);
            $group = $mform->createElement('group','conditiongradegroup',
                get_string('gradecondition', 'condition'),$grouparray);

            // Get version with condition info and store it so we don't ask
            // twice
            if(!empty($this->_cm)) {
                $ci = new condition_info($this->_cm,CONDITION_MISSING_EXTRATABLE);
                $this->_cm=$ci->get_full_course_module();
                $count=count($this->_cm->conditionsgrade)+1;
            } else {
                $count=1;
            }

            $this->repeat_elements(array($group),$count,array(),'conditiongraderepeats','conditiongradeadds',2,
                get_string('addgrades','condition'),true);
            $mform->setHelpButton('conditiongradegroup[0]', array('gradecondition', get_string('help_gradecondition', 'condition'), 'condition'));

            // Conditions based on completion
            $completion = new completion_info($COURSE);
            if ($completion->is_enabled()) {
                $completionoptions=array();
                $modinfo=get_fast_modinfo($COURSE);
                foreach($modinfo->cms as $id=>$cm) {
                    if ($cm->completion) {
                        $completionoptions[$id]=$cm->name;
                    }
                }
                asort($completionoptions);
                $completionoptions=array(0=>get_string('none','condition'))+$completionoptions;

                $completionvalues=array(
                    COMPLETION_COMPLETE=>get_string('completion_complete','condition'),
                    COMPLETION_INCOMPLETE=>get_string('completion_incomplete','condition'),
                    COMPLETION_COMPLETE_PASS=>get_string('completion_pass','condition'),
                    COMPLETION_COMPLETE_FAIL=>get_string('completion_fail','condition'));

                $grouparray=array();
                $grouparray[] =& $mform->createElement('select','conditionsourcecmid','',$completionoptions);
                $grouparray[] =& $mform->createElement('select','conditionrequiredcompletion','',$completionvalues);
                $group = $mform->createElement('group','conditioncompletiongroup',
                    get_string('completioncondition', 'condition'),$grouparray);

                $count=empty($this->_cm) ? 1 : count($this->_cm->conditionscompletion)+1;
                $this->repeat_elements(array($group),$count,array(),
                    'conditioncompletionrepeats','conditioncompletionadds',2,
                    get_string('addcompletions','condition'),true);
                $mform->setHelpButton('conditioncompletiongroup[0]', array('completioncondition', get_string('help_completioncondition', 'condition'), 'condition'));
            }

            // Do we display availability info to students?
            $mform->addElement('select', 'showavailability', get_string('showavailability', 'condition'),
                    array(CONDITION_STUDENTVIEW_SHOW=>get_string('showavailability_show', 'condition'),
                    CONDITION_STUDENTVIEW_HIDE=>get_string('showavailability_hide', 'condition')));
            $mform->setDefault('showavailability', CONDITION_STUDENTVIEW_SHOW);
            $mform->setHelpButton('showavailability', array('showavailability', get_string('help_showavailability', 'condition'), 'condition'));
        }

        // Conditional activities: completion tracking section 
        require_once($CFG->libdir.'/completionlib.php');

        if (!isset($completion)) {
            $completion=new completion_info($COURSE);
        }

        if($completion->is_enabled()) {
            $mform->addElement('header', '', get_string('activitycompletion', 'completion'));

            // Unlock button for if people have completed it (will
            // be removed in definition_after_data if they haven't)
            $mform->addElement('submit','unlockcompletion',get_string('unlockcompletion','completion'));
            $mform->registerNoSubmitButton('unlockcompletion');
            $mform->addElement('hidden','completionunlocked',0);
            
            $mform->addElement('select', 'completion', get_string('completion','completion'), 
                array(COMPLETION_TRACKING_NONE=>get_string('completion_none','completion'), 
                COMPLETION_TRACKING_MANUAL=>get_string('completion_manual','completion')));
            $mform->setHelpButton('completion', array('completion', get_string('help_completion', 'completion'), 'completion'));
            $mform->setDefault('completion',$this->_features->defaultcompletion
                ? COMPLETION_TRACKING_MANUAL
                : COMPLETION_TRACKING_NONE);

            // Automatic completion once you view it
            $gotcompletionoptions=false;
            if(plugin_supports('mod',$modname,FEATURE_COMPLETION_TRACKS_VIEWS)) {
                $mform->addElement('checkbox', 'completionview', get_string('completionview','completion'),
                    get_string('completionview_text','completion'));
                $mform->setHelpButton('completionview', array('completionview', get_string('help_completionview', 'completion'), 'completion'));
                $mform->disabledIf('completionview','completion','ne',COMPLETION_TRACKING_AUTOMATIC);
                $gotcompletionoptions=true;
            }

            // Automatic completion once it's graded
            if(plugin_supports('mod',$modname,FEATURE_GRADE_HAS_GRADE)) {
                $mform->addElement('checkbox', 'completionusegrade', get_string('completionusegrade','completion'),
                    get_string('completionusegrade_text','completion'));
                $mform->setHelpButton('completionusegrade', array('completionusegrade', get_string('help_completionusegrade', 'completion'), 'completion'));
                $mform->disabledIf('completionusegrade','completion','ne',COMPLETION_TRACKING_AUTOMATIC);
                $gotcompletionoptions=true;
            }

            // Automatic completion according to module-specific rules
            $this->_customcompletionelements = $this->add_completion_rules();
            foreach($this->_customcompletionelements as $element) {
                $mform->disabledIf($element,'completion','ne',COMPLETION_TRACKING_AUTOMATIC);                
            }

            $gotcompletionoptions = $gotcompletionoptions ||
                count($this->_customcompletionelements)>0;

            // Automatic option only appears if possible
            if($gotcompletionoptions) {
                $mform->getElement('completion')->addOption(
                    get_string('completion_automatic','completion'),
                    COMPLETION_TRACKING_AUTOMATIC);
            } 

            // Completion expected at particular date? (For progress tracking)
            $mform->addElement('date_selector', 'completionexpected', get_string('completionexpected','completion'), array('optional'=>true));
            $mform->setHelpButton('completionexpected', array('completionexpected', get_string('help_completionexpected', 'completion'), 'completion'));
            $mform->disabledIf('completionexpected','completion','eq',COMPLETION_TRACKING_NONE);    
        }

        $this->standard_hidden_coursemodule_elements();
    }
    
    /**
     * Can be overridden to add custom completion rules if the module wishes
     * them. If overriding this, you should also override completion_rule_enabled.
     * <p>
     * Just add elements to the form as needed and return the list of IDs. The
     * system will call disabledIf and handle other behaviour for each returned
     * ID.
     * @return array Array of string IDs of added items, empty array if none
     */
    function add_completion_rules() {
        return array();
    }

    /**
     * Called during validation. Override to indicate, based on the data, whether
     * a custom completion rule is enabled (selected).
     *
     * @param array $data Input data (not yet validated)
     * @return bool True if one or more rules is enabled, false if none are;
     *   default returns false
     */
    function completion_rule_enabled(&$data) {
        return false;
    }

    function standard_hidden_coursemodule_elements(){
        $mform =& $this->_form;
        $mform->addElement('hidden', 'course', 0);
        $mform->setType('course', PARAM_INT);

        $mform->addElement('hidden', 'coursemodule', 0);
        $mform->setType('coursemodule', PARAM_INT);

        $mform->addElement('hidden', 'section', 0);
        $mform->setType('section', PARAM_INT);

        $mform->addElement('hidden', 'module', 0);
        $mform->setType('module', PARAM_INT);

        $mform->addElement('hidden', 'modulename', '');
        $mform->setType('modulename', PARAM_SAFEDIR);

        $mform->addElement('hidden', 'instance', 0);
        $mform->setType('instance', PARAM_INT);

        $mform->addElement('hidden', 'add', 0);
        $mform->setType('add', PARAM_ALPHA);

        $mform->addElement('hidden', 'update', 0);
        $mform->setType('update', PARAM_INT);

        $mform->addElement('hidden', 'return', 0);
        $mform->setType('return', PARAM_BOOL);
    }

    /**
     * Overriding formslib's add_action_buttons() method, to add an extra submit "save changes and return" button.
     *
     * @param bool $cancel show cancel button
     * @param string $submitlabel null means default, false means none, string is label text
     * @param string $submit2label  null means default, false means none, string is label text
     * @return void
     */
    function add_action_buttons($cancel=true, $submitlabel=null, $submit2label=null) {
        if (is_null($submitlabel)) {
            $submitlabel = get_string('savechangesanddisplay');
        }

        if (is_null($submit2label)) {
            $submit2label = get_string('savechangesandreturntocourse');
        }

        $mform =& $this->_form;

        // elements in a row need a group
        $buttonarray = array();

        if ($submit2label !== false) {
            $buttonarray[] = &$mform->createElement('submit', 'submitbutton2', $submit2label);
        }

        if ($submitlabel !== false) {
            $buttonarray[] = &$mform->createElement('submit', 'submitbutton', $submitlabel);
        }

        if ($cancel) {
            $buttonarray[] = &$mform->createElement('cancel');
        }

        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->setType('buttonar', PARAM_RAW);
        $mform->closeHeaderBefore('buttonar');
    }
}

?>
