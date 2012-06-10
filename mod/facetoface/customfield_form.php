<?php

require_once "$CFG->dirroot/lib/formslib.php";
require_once "$CFG->dirroot/mod/facetoface/lib.php";

class mod_facetoface_customfield_form extends moodleform {

    function definition()
    {
        $mform =& $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('hidden', 'id', $this->_customdata['id']);

        $mform->addElement('text', 'name', get_string('name'), 'maxlength="255" size="50"');
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->setType('name', PARAM_MULTILANG);

        $mform->addElement('text', 'shortname', get_string('shortname'), 'maxlength="255" size="25"');
        $mform->addRule('shortname', null, 'required', null, 'client');
        $mform->setType('shortname', PARAM_ALPHANUM);

        $options = array(CUSTOMFIELD_TYPE_TEXT        => get_string('field:text', 'facetoface'),
                         CUSTOMFIELD_TYPE_SELECT      => get_string('field:select', 'facetoface'),
                         CUSTOMFIELD_TYPE_MULTISELECT => get_string('field:multiselect', 'facetoface'),
                         );
        $mform->addElement('select', 'type', get_string('setting:type', 'facetoface'), $options);
        $mform->addRule('type', null, 'required', null, 'client');
        $mform->setDefault('type', 0);

        $mform->addElement('text', 'defaultvalue', get_string('setting:defaultvalue', 'facetoface'), 'maxlength="255" size="30"');
        $mform->setType('defaultvalue', PARAM_MULTILANG);

        $mform->addElement('text', 'possiblevalues', get_string('setting:possiblevalues', 'facetoface'), 'size="50"');
        $mform->setType('possiblevalues', PARAM_MULTILANG);
        $mform->disabledIf('possiblevalues', 'type', 'eq', 0);

        $mform->addElement('checkbox', 'required', get_string('required'));
        $mform->setDefault('required', false);
        $mform->addElement('checkbox', 'isfilter', get_string('setting:isfilter', 'facetoface'));
        $mform->setDefault('isfilter', false);
        $mform->addElement('checkbox', 'showinsummary', get_string('setting:showinsummary', 'facetoface'));
        $mform->setDefault('showinsummary', true);

        $this->add_action_buttons();
    }
}
