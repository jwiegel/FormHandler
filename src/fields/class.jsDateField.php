<?php

/**
 * class jsDateField
 *
 * Create a datefield with a calendar (the date picker of the browser, js/datepicker.js).
 * New forms should rather use html5DateField(): one date input, the value is yyyy-mm-dd.
 *
 * @author Teye Heimans *
 * @package FormHandler
 * @subpackage Fields
 */
class jsDateField extends DateField
{
    /** @var bool is the mask containing all 3 fields (so that the js calendar can be used) */
    private bool $_isOk;

    /**
     * jsDateField::jsDateField()
     *
     * Constructor: create a new jsdatefield object
     *
     * @param object &$oForm: the form where the datefield is located on
     * @param string $sName: the name of the datefield
     * @param string $sMask:
     * @param boolean $bRequired:
     * @param string $sInterval:
     * @param boolean $bIncludeJS: do we include the js file
     * @return jsDateField
     * @author Teye Heimans
     */
    public function __construct(&$oForm, $sName, $sMask = null, $bRequired = null, $sInterval = null, $bIncludeJS  = null)
    {
        // call the constructor of the datefield
        parent::__construct($oForm, $sName, $sMask, $bRequired, $sInterval);

        // check if the mask contains all three fields..
        $str = $this->_getFieldsFromMask();

        // is the mask ok (can we use a js datefield ?
        $this->_isOk = (strlen($str) == 3 && strpos($str, 'd') !== false && strpos($str, 'm') !== false && strpos($str, 'y') !== false);

        // when not OK, just return a datefield
        if (!$this->_isOk)
        {
            return;
        }

        static $bSetJS = false;

        // needed javascript included yet ?
        if (!$bSetJS)
        {

            $bSetJS = true;
            // add the needed javascript
            if ($bIncludeJS == true)
            {
                $oForm->_setJS(FH_FHTML_DIR . "js/datepicker.js", true);
            }
        }
    }

    /**
     * DateField::getField()
     *
     * return the field
     *
     * @return string the field
     * @author Teye Heimans
     * @access public
     */
    public function getField()
    {
        // view mode enabled ?
        if ($this->getViewMode())
        {
            // get the view value..
            return $this->_getViewValue();
        }

        $html = parent::getField();

        // when not OK, just return a datefield
        if (!$this->_isOk)
        {
            return $html;
        }

        list($iStart, $iEnd) = $this->_getYearInterval();

        /* switch dates when in inverse */
        if ($iStart < $iEnd)
        {
            $tmp = $iEnd;
            $iEnd = -$iStart;
            $iStart = $tmp;
        }

        $sName = $this->_sName;
        $sForm = $this->_oForm->getFormName();

        // the calendar: the date picker of the browser, only the years of the interval.
        // cal_<name> and set<name>Values() are kept for projects which use them.
        $this->_oForm->_setJS(
            "// calendar of the date field '" . $sName . "'\n" .
                "var cal_" . $sName . " = new FH_DatePicker('" . (date('Y') - $iStart) . "-01-01', '" . (date('Y') + $iEnd) . "-12-31');\n" .
                "cal_" . $sName . ".setReturnFunction('set" . $sName . "Values');\n" .
                "function set" . $sName . "Values(y, m, d) {\n" .
                "    document.forms['" . $sForm . "'].elements['" . $sName . "_day'].value   = LZ(d);\n" .
                "    document.forms['" . $sForm . "'].elements['" . $sName . "_month'].value = LZ(m);\n" .
                "    document.forms['" . $sForm . "'].elements['" . $sName . "_year'].value  = y;\n" .
                "}\n",
            false,
            false
        );

        $html .=
            "<a href='javascript:;' " .
            "onclick=\"cal_" . $sName . ".show(this, FH_DatePicker.fromFields('" . $sForm . "', '" . $sName . "_year', '" . $sName . "_month', '" . $sName . "_day')); return false;\" " .
            " name='anchor_" . $sName . "' id='anchor_" . $sName . "'>" .
            "<img src='" . FH_FHTML_DIR . "images/calendar.gif' border='0' alt='Select Date' " . FH_XHTML_CLOSE . "></a>\n";

        return $html;
    }
}
