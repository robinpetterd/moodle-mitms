<?php //$Id$

require_once($CFG->dirroot.'/local/reportbuilder/filters/lib.php');

/**
 * Generic filter based on a list of values.
 */
class filter_select extends filter_type {
    /**
     * options for the list values
     */
    var $_options;

    var $_field;

    var $_default;

    /**
     * Constructor
     * @param string $name the name of the filter instance
     * @param string $label the label of the filter instance
     * @param boolean $advanced advanced form element flag
     * @param string $field user table filed name
     * @param array $options select options
     * @param mixed $default option
     */
    function filter_select($name, $label, $advanced, $filtertype, $field, $query, $options, $default=null, $attributes=null) {
        parent::filter_type($name, $label, $advanced, $filtertype);
        $this->_field   = $field;
        $this->_query   = $query;
        $this->_options = $options;
        $this->_default = $default;
        $this->_attributes = $attributes;
    }

    /**
     * Returns an array of comparison operators
     * @return array of comparison operators
     */
    function get_operators() {
        return array(0 => get_string('isanyvalue','filters'),
                     1 => get_string('isequalto','filters'),
                     2 => get_string('isnotequalto','filters'));
    }

    /**
     * Adds controls specific to this filter in the form.
     * @param object $mform a MoodleForm object to setup
     */
    function setupForm(&$mform) {
        global $SESSION;
        $filtername = $this->_filtername;
        $objs = array();
        $objs[] =& $mform->createElement('select', $this->_name.'_op', null, $this->get_operators());
        $objs[] =& $mform->createElement('select', $this->_name, null, $this->_options, $this->_attributes);
        $grp =& $mform->addElement('group', $this->_name.'_grp', $this->_label, $objs, '', false);
        $grp->setHelpButton(array('select', $this->_label, 'filters'));
        $mform->disabledIf($this->_name, $this->_name.'_op', 'eq', 0);
        if (!is_null($this->_default)) {
            $mform->setDefault($this->_name, $this->_default);
        }
        if ($this->_advanced) {
            $mform->setAdvanced($this->_name.'_grp');
        }

        // set default values
        if(array_key_exists($this->_name, $SESSION->{$filtername})) {
            $defaults = $SESSION->{$filtername}[$this->_name];
        }
        //TODO get rid of need for [0]
        if(isset($defaults[0]['operator'])) {
            $mform->setDefault($this->_name.'_op', $defaults[0]['operator']);
        }
        if(isset($defaults[0]['value'])) {
            $mform->setDefault($this->_name, $defaults[0]['value']);
        }

    }

    /**
     * Retrieves data from the form data
     * @param object $formdata data submited with the form
     * @return mixed array filter data or false when filter not set
     */
    function check_data($formdata) {
        $field    = $this->_name;
        $operator = $field.'_op';

        if (array_key_exists($field, $formdata) ) {
            return array('operator' => (int)$formdata->$operator,
                         'value'    => (string)$formdata->$field);
        }

        return false;
    }

    /**
     * Returns the condition to be used with SQL where
     * @param array $data filter settings
     * @return string the filtering condition or null if the filter is disabled
     */
    function get_sql_filter($data) {
        $operator = $data['operator'];
        $value    = addslashes($data['value']);
        $field    = $this->_field;
        $query    = $this->_query;

        switch($operator) {
            case 1:
                $token = ' = ';
                $glue = ' OR ';
                break;
            case 2:
                $token = ' <> ';
                $glue = ' AND ';
                break;
            default:
                return ' TRUE ';
        }

        // split by comma and look for any items
        // within list
        $items = explode(',',$value);
        $res = array();
        foreach($items as $item) {
            $res[] = $query.$token."'$item'";
        }
        return '('.implode($glue,$res).')';
    }

    /**
     * Returns a human friendly description of the filter used as label.
     * @param array $data filter settings
     * @return string active filter label
     */
    function get_label($data) {
        $operators = $this->get_operators();
        $operator  = $data['operator'];
        $value     = $data['value'];

        if (empty($operator)) {
            return '';
        }

        $a = new object();
        $a->label    = $this->_label;
        $a->value    = '"'.s($this->_options[$value]).'"';
        $a->operator = $operators[$operator];

        return get_string('selectlabel', 'filters', $a);
    }
}

