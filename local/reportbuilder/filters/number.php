<?php //$Id$
require_once($CFG->dirroot.'/local/reportbuilder/filters/lib.php');

/**
 * Generic filter for numbers.
 */
class filter_number extends filter_type {
    var $_field;
    var $_query;

    /**
     * Constructor
     * @param string $name the name of the filter instance
     * @param string $label the label of the filter instance
     * @param boolean $advanced advanced form element flag
     * @param string $field table field name
     */
    function filter_number($name, $label, $advanced, $filtername, $field, $query) {
        parent::filter_type($name, $label, $advanced, $filtername);
        $this->_field = $field;
        $this->_query = $query;
    }

    /**
     * Returns an array of comparison operators
     * @return array of comparison operators
     */
    function getOperators() {
        return array(0 => get_string('isequalto', 'filters'),
                     1 => get_string('isnotequalto','filters'),
                     2 => get_string('isgreaterthan','filters'),
                     3 => get_string('islessthan','filters'),
                     4 => get_string('isgreaterorequalto','filters'),
                     5 => get_string('islessthanorequalto','filters'));
    }

    /**
     * Adds controls specific to this filter in the form.
     * @param object $mform a MoodleForm object to setup
     */
    function setupForm(&$mform) {
        global $SESSION;
        $filtername=$this->_filtername;
        $objs = array();
        $objs[] =& $mform->createElement('select', $this->_name.'_op', null, $this->getOperators());
        $objs[] =& $mform->createElement('text', $this->_name, null);
        $grp =& $mform->addElement('group', $this->_name.'_grp', $this->_label, $objs, '', false);
        $grp->setHelpButton(array('number',$this->_label,'filters'));
        if ($this->_advanced) {
            $mform->setAdvanced($this->_name.'_grp');
        }

        // set default values
        if(array_key_exists($this->_name,$SESSION->{$filtername})) {
            $defaults = $SESSION->{$filtername}[$this->_name];
        }
        // TODO get rid of need for [0]
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
        $value = (isset($formdata->$field)) ? $formdata->$field : '';
        if (array_key_exists($operator, $formdata)) {
            if ($value == '') {
                // no data - no change except for empty filter
                return false;
            }
            return array('operator'=>(int)$formdata->$operator, 'value'=>$value);
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
        $value    = (float) addslashes($data['value']);
        $field    = $this->_field;
        $query    = $this->_query;

        if ($value === '') {
            return '';
        }

        switch($operator) {
            case 0: // equal
                $res = "= $value"; break;
            case 1: // not equal
                $res = "!= $value"; break;
            case 2: // greater than
                $res = "> $value"; break;
            case 3: // less than
                $res = "< $value"; break;
            case 4: // greater or equal to
                $res = ">= $value"; break;
            case 5: // less than or equal to
                $res = "<= $value"; break;
            default:
                return '';
        }
        // this will cope with empty values but not anything that can't be cast to a float
        // make sure the source column only contains numbers!
        return 'CASE WHEN CAST('.$query.' AS varchar) = \'\' THEN 0 ELSE CAST('.$query.' AS float) END '.$res;
    }

    /**
     * Returns a human friendly description of the filter used as label.
     * @param array $data filter settings
     * @return string active filter label
     */
    function get_label($data) {
        $operator  = $data['operator'];
        $value     = $data['value'];
        $operators = $this->getOperators();

        $a = new object();
        $a->label    = $this->_label;
        $a->value    = '"'.s($value).'"';
        $a->operator = $operators[$operator];


        switch ($operator) {
            case 0: // contains
            case 1: // doesn't contain
            case 2: // equal to
            case 3: // starts with
            case 4: // ends with
            case 5: // empty
                return get_string('textlabel', 'filters', $a);
        }

        return '';
    }
}
