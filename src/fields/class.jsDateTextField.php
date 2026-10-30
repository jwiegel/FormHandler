<?php

/**
 * class jsDateTextField
 *
 * Create a jsDateTextField: a DateTextField with a calendar (the date picker of the
 * browser, js/datepicker.js). New forms should rather use html5DateField().
 *
 * @author Thomas Branius
 * @package FormHandler
 * @subpackage Fields
 * @since 16-03-2010
 */
class jsDateTextField extends DateTextField
{
    /** @var string|null the mask for the CalendarPopup of FormHandler 4.6 and older (not used any more) */
    protected $_sJsMask;

    /**
     * Constructor: create a new jsDateTextField object
     *
     * @param object &$oForm: the form where the datefield is located on
     * @param string $sName: the name of the datefield
     * @param string $sMask: the mask which is used to display the fields
     * @param bool $bParseOtherPresentations: try to parse other presentations of dateformat
     * @param bool $bIncludeJS
     * @return jsDateTextField
     * @access public
     * @author Thomas Branius
     * @since 16-03-2010
     */
    public function __construct(&$oForm, $sName, $sMask = null, $bParseOtherPresentations = false, $bIncludeJS = true)
    {
        parent::__construct($oForm, $sName, $sMask, $bParseOtherPresentations);

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
     * Set the display of the fields
     *
     * @param string $sMask: how we have to display the datefield (day-month-year combination)
     * @return void
     * @access public
     * @author Thomas Branius
     * @since 16-03-2010
     */
    public function setMask($sMask)
    {
        parent::setMask($sMask);

        $this->_sJsMask = $sMask;

        // convert to Javascript-Mask
        $this->_sJsMask = str_replace('d', 'dd', $this->_sJsMask);
        $this->_sJsMask = str_replace('D', 'd', $this->_sJsMask);
        $this->_sJsMask = str_replace('m', 'MM', $this->_sJsMask);
        $this->_sJsMask = str_replace('y', 'yy', $this->_sJsMask);
        $this->_sJsMask = str_replace('Y', 'yyyy', $this->_sJsMask);
    }

    /**
     * return the field
     *
     * @return string the field
     * @author Thomas Branius
     * @access public
     * @since 16-03-2010
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

        $sName = $this->_sName;
        $sField = "document.forms['" . $this->_oForm->getName() . "'].elements['" . $sName . "']";

        // the calendar: the date picker of the browser writes the date in the mask of the
        // field (the mask has only the characters d, j, m, n, y, Y and . - /, see setMask())
        $this->_oForm->_setJS(
            "// calendar of the date field '" . $sName . "'\n" .
                "var cal_" . $sName . " = new FH_DatePicker(null, null, function(y, m, d) {\n" .
                "    " . $sField . ".value = FH_DatePicker.format('" . $this->_sDateMask . "', y, m, d);\n" .
                "});\n",
            false,
            false
        );

        $html .=
            "<a href='javascript:;' " .
            "onclick=\"cal_" . $sName . ".show(this, FH_DatePicker.parse('" . $this->_sDateMask . "', " . $sField . ".value)); return false;\" " .
            " name='anchor_" . $sName . "' id='anchor_" . $sName . "'>" .
            "<img src='" . FH_FHTML_DIR . "images/calendar.gif' border='0' alt='Select Date' " . FH_XHTML_CLOSE . "></a>\n";

        return $html;
    }
}
