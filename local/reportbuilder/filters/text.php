<?php //$Id$
require_once($CFG->dirroot.'/local/reportbuilder/filters/lib.php');

/**
 * Generic filter for text fields.
 */
class filter_text extends filter_type {
    var $_field;
    var $_query;

    /**
     * Constructor
     * @param string $name the name of the filter instance
     * @param string $label the label of the filter instance
     * @param boolean $advanced advanced form element flag
     * @param string $field table field name
     */
    function filter_text($name, $label, $advanced, $filtername, $field, $query) {
        parent::filter_type($name, $label, $advanced, $filtername);
        $this->_field = $field;
        $this->_query = $query;
    }

    /**
     * Returns an array of comparison operators
     * @return array of comparison operators
     */
    function getOperators() {
        return array(0 => get_string('contains', 'filters'),
                     1 => get_string('doesnotcontain','filters'),
                     2 => get_string('isequalto','filters'),
                     3 => get_string('startswith','filters'),
                     4 => get_string('endswith','filters'),
                     5 => get_string('isempty','filters'));
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
        $mform->setType($this->_name, PARAM_TEXT);
        $grp =& $mform->addElement('group', $this->_name.'_grp', $this->_label, $objs, '', false);
        $grp->setHelpButton(array('text',$this->_label,'filters'));
        $mform->disabledIf($this->_name, $this->_name.'_op', 'eq', 5);
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
            if ($formdata->$operator != 5 and $value == '') {
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
        $value    = addslashes($data['value']);
        $field    = $this->_field;
        $query    = $this->_query;

        if ($operator != 5 and $value === '') {
            return '';
        }

        $ilike = sql_ilike();

        switch($operator) {
            case 0: // contains
                $res = "$ilike '%$value%'"; break;
            case 1: // does not contain
                $res = "NOT $ilike '%$value%'"; break;
            case 2: // equal to
                $res = "$ilike '$value'"; break;
            case 3: // starts with
                $res = "$ilike '$value%'"; break;
            case 4: // ends with
                $res = "$ilike '%$value'"; break;
            case 5: // empty - may also be null
                // hack required to get query to user
                // correct operator precendence
                // result should be:
                // ( query = '' OR (query) IS NULL )
                $res = "='' OR ($query) IS NULL )";
                $query = "($query"; break;
            default:
                return '';
        }
        return $query.' '.$res;
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
                return get_string('textlabel', 'filters', $a);
            case 5: // empty
                return get_string('textlabelnovalue', 'filters', $a);
        }

        return '';
    }
}
