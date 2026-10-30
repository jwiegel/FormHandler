<?php

/**
 * class DateTextField
 *
 * Create a DateTextfield
 *
 * @author Thomas Branius
 * @since 16-03-2010
 * @package FormHandler
 * @subpackage Fields
 * 
 *  validators added by Johan Wiegel
 */
class DateTextField extends TextField
{
    /** @var string how to display the fields with mask */
    protected $_sDateMask;
    /** @var string how to parse the value */
    protected $_sValParseRegExpr;
    /** @var int position of day in regular expression */
    protected $_iDayPos;
    /** @var int position of month in regular expression */
    protected $_iMonthPos;
    /** @var int position of year in regular expression */
    protected $_iYearPos;
    /** @var bool try to parse other presentations of dateformat */
    protected $_bParseOtherPresentations;

    /**
     * Constructor: create a new dateTextField object
     *
     * @param object &$oForm: the form where the datefield is located on
     * @param string $sName: the name of the datefield
     * @param string $sMask: the mask which is used to display the fields
     * @param bool $bParseOtherPresentations: try to parse other presentations of dateformat
     * @access public
     * @author Thomas Branius
     * @since 16-03-2010
     */
    public function __construct(&$oForm, $sName, $sMask = null, $bParseOtherPresentations = false)
    {
        // set the default date display
        $this->setMask(!is_null($sMask) ? $sMask : FH_DATETEXTFIELD_DEFAULT_DISPLAY);

        $this->_bParseOtherPresentations = $bParseOtherPresentations;

        //$this->setValidator(array(&$this, "validate"));

        // call the constructor of the Field class
        parent::__construct($oForm, $sName);
    }

    /**
     * Set the display of the fields
     *
     * @param string $sMask: how we have to display the datefield (day-month-year combination)
     * @return void
     * @access public
     * @author Thomas Branius]
     * @since 16-03-2010
     */
    function setMask($sMask)
    {
        // validate the mask
        $regex = '/^([dDmMyY])([\.\-\/])([dDmMyY])\2([dDmMyY])$/';

        if (
            preg_match($regex, $sMask, $data) == 0
            || strtolower($data[1]) == strtolower($data[3])
            || strtolower($data[1]) == strtolower($data[4])
            || strtolower($data[3]) == strtolower($data[4])
        )
            throw new FormHandlerException("Invalid mask ['{$sMask}']. Useable chars: d, D, m, M, y, Y, ., -, /");

        // set postion of day, month and year
        for ($i = 1; $i < 5; $i++)
        {
            if (strtolower($data[$i]) == "d")
                $this->_iDayPos = $i;
            else if (strtolower($data[$i]) == "m")
                $this->_iMonthPos = $i;
            else if (strtolower($data[$i]) == "y")
                $this->_iYearPos = $i;
        }

        $seperator = str_replace(array('/', '.'), array('\/', '\.'), $data[2]);
        $regExDay = '[0-9]{' . ($data[$this->_iDayPos] == 'D' ? '1,2' : '2') . '}';
        $regExMonth = '[0-9]{' . ($data[$this->_iMonthPos] == 'M' ? '1,2' : '2') . '}';
        $regExYear = '[0-9]{' . ($data[$this->_iYearPos] == 'y' ? '2' : '4') . '}';

        $this->_iDayPos = $this->_iDayPos > 1 ? $this->_iDayPos - 1 : $this->_iDayPos;
        $this->_iMonthPos = $this->_iMonthPos > 1 ? $this->_iMonthPos - 1 : $this->_iMonthPos;
        $this->_iYearPos = $this->_iYearPos > 1 ? $this->_iYearPos - 1 : $this->_iYearPos;

        if ($this->_iYearPos == 1)
        {
            if ($this->_iDayPos == 2)
                $this->_sValParseRegExpr = "/^({$regExYear}){$seperator}({$regExDay}){$seperator}({$regExMonth})$/";
            else
                $this->_sValParseRegExpr = "/^({$regExYear}){$seperator}({$regExMonth}){$seperator}({$regExDay})$/";
        }
        else if ($this->_iYearPos == 2)
        {
            if ($this->_iDayPos == 1)
                $this->_sValParseRegExpr = "/^({$regExDay}){$seperator}({$regExYear}){$seperator}({$regExMonth})$/";
            else
                $this->_sValParseRegExpr = "/^({$regExMonth}){$seperator}({$regExYear}){$seperator}({$regExDay})$/";
        }
        else if ($this->_iYearPos == 3)
        {
            if ($this->_iDayPos == 1)
                $this->_sValParseRegExpr = "/^({$regExDay}){$seperator}({$regExMonth}){$seperator}({$regExYear})$/";
            else
                $this->_sValParseRegExpr = "/^({$regExMonth}){$seperator}({$regExDay}){$seperator}({$regExYear})$/";
        }


        // mask for date-function
        $this->_sDateMask = str_replace(array("D", "M"), array("j", "n"), $sMask);
    }

    /**
     * Get the date value as an array: array(y,m,d)
     *
     * @return array<int, mixed>|null null (and a warning) when the value is not a valid date
     * @access public
     * @author Thomas Branius
     * @since 16-03-2010
     */
    public function getAsArray()
    {
        if ($this->getValue() == "")
        {
            return array("", "", "");
        }
        if (preg_match($this->_sValParseRegExpr, $this->getValue(), $data) == 0)
        {
            // the value can come from the user, so no exception here
            trigger_error("Value is not a valid date [" . $this->getValue() . "]", E_USER_WARNING);
            return null;
        }
        if ($data[$this->_iYearPos] <= 50)
            $data[$this->_iYearPos] = $data[$this->_iYearPos] + 2000;
        if ($data[$this->_iYearPos] <= 100)
            $data[$this->_iYearPos] = $data[$this->_iYearPos] + 1900;

        return array($data[$this->_iYearPos], $data[$this->_iMonthPos],  $data[$this->_iDayPos]);
    }

    /**
     * Return the value of the field
     *
     * @return mixed the value of the field
     * @access public
     * @author Thomas Branius
     * @since 16-03-2010
     */
    public function getValue()
    {
        $sValue = parent::getValue();

        if (preg_match($this->_sValParseRegExpr, $sValue))
            return $sValue;

        if ($this->_bParseOtherPresentations)
            $sValue = $this->parseOtherPresentations($sValue);

        return $sValue;
    }

    /**
     * Set the value of the field
     *
     * @param mixed $mValue: The new value for the field
     * @return void
     * @access public
     * @author Thomas Branius
     * @since 16-03-2010
     */
    public function setValue($mValue)
    {
        if ($this->_oForm->isPosted())
        {
            parent::setValue($mValue);
            return;
        }

        // parse value from db
        $regex = '/([0-9]{4})-([0-9]{2})-([0-9]{2})/';

        if (preg_match("/0000-00-00/", $mValue))
        {
            $this->_mValue = null;
        }
        else if (preg_match($regex, $mValue, $data))
        {
            $timestamp = mktime(0, 0, 0, (int) $data[2], (int) $data[3], (int) $data[1]);
            $this->_mValue = date($this->_sDateMask, $timestamp);
        }
        else
        {
            $this->_mValue = $mValue;
        }
    }

    /**
     * try to parse other presentations of dateformat
     *
     * @param mixed $sValue
     * @return mixed the value of the field
     * @access public
     * @author Thomas Branius
     * @since 16-03-2010
     */
    public function parseOtherPresentations($sValue)
    {
        // dd.mm.YYYY, dd/mm/YYYY, dd-mm-YYYY
        // dd.mm.YY, dd/mm/YY, dd-mm-YY
        // d.m.YYYY, d/m/YYYY, d-m-YYYY
        // d.m.YY, d/m/YY, d-m-YY
        $regex1 = '^([0-3]?\d)([\-\.\/])([01]?\d)\2([0-9]{2})(\d\d){0,1}$';

        // the value is null if the field has no value yet
        $sTrimmed = str_replace(' ', '', (string) $this->_mValue);

        if (preg_match("/$regex1/", $sTrimmed, $data))
        {
            if (isset($data[5]))
                $year = $data[4] * 100 + $data[5];
            else if ($data[4] <= 50)
                $year = 2000 + $data[4];
            else
                $year = 1900 + $data[4];

            $day = $data[1];
            $month = $data[3];

            $timestamp = mktime(0, 0, 0, (int) $month, (int) $day, (int) $year);
            $this->_mValue = date($this->_sDateMask, $timestamp);
            return date($this->_sDateMask, $timestamp);
        }

        // YYYY/mm/dd, YYYY-mm-dd
        // YY/mm/dd, YY-mm-dd
        // YYYY/m/d, YYYY-m-y
        // YY/m/d, YY-m-y
        $regex2 = '^([0-9]{2})(\d\d){0,1}([\-\/])([01]?\d)\3([0-3]?\d)$';

        if (preg_match("/$regex2/", $sTrimmed, $data))
        {
            if ($data[2] !== '')
                $year = $data[1] * 100 + $data[2];
            else if ($data[1] <= 50)
                $year = 2000 + $data[1];
            else
                $year = 1900 + $data[1];

            $day = $data[5];
            $month = $data[4];

            $timestamp = mktime(0, 0, 0, (int) $month, (int) $day, (int) $year);
            $this->_mValue = date($this->_sDateMask, $timestamp);
            return date($this->_sDateMask, $timestamp);
        }

        return $sValue;
    }

    /**
     * Check if the date is valid (eg not 31-02-2003)
     *
     * @return boolean true if the field is correct, false if not
     * @access public
     * @author Thomas Branius
     */
    public function isValid()
    {
        // the result has been requested before..
        if (isset($this->_isValid))
        {
            return $this->_isValid;
        }

        if ($this->getValue() != "")
        {
            if (preg_match($this->_sValParseRegExpr, $this->getValue(), $data))
            {
                $data = $this->getAsArray();
                if (checkdate($data[1], $data[2], $data[0]) == false)
                {
                    $this->_isValid = false;
                }
                else
                {
                    $timestamp = mktime(0, 0, 0, $data[1], $data[2], $data[0]);
                    $this->_mValue = date($this->_sDateMask, $timestamp);
                }
            }
            else
            {
                $this->_isValid = false;
            }
        }

        if (isset($this->_isValid))
        {
            // set the error message
            $this->_sError = $this->_oForm->_text(14);
        }

        return parent::isValid();
    }
}
