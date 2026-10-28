<?php

/**
 * class Html5DateField
 *
 * A date field rendered as HTML5 <input type="date">, so the browser shows its own
 * date picker. The value is always yyyy-mm-dd, like the date columns of a database.
 * The mask is only used to display the date in view mode.
 *
 * @package FormHandler
 * @subpackage Fields
 */
class Html5DateField extends TextField
{
    /** @var string date() format for the view mode */
    private $_sDateMask;
    /** @var bool set the current date in a new, empty form */
    private $_bSetCurDate;

    /**
     * Constructor: create a new Html5DateField object
     *
     * @param object &$oForm: the form where the field is located on
     * @param string $sName: the name of the field
     * @param string|null $sMask: how to display the date in view mode (chars d, D, m, M, y, Y)
     * @param bool $bSetCurDate: set the current date when a new form is shown
     * @access public
     */
    public function __construct(&$oForm, $sName, $sMask = null, $bSetCurDate = false)
    {
        parent::__construct($oForm, $sName);

        $sMask = !is_null($sMask) ? $sMask : FH_DATETEXTFIELD_DEFAULT_DISPLAY;

        // same mask chars as DateTextField: D = day and M = month without leading zero
        $this->_sDateMask = str_replace(array('D', 'M'), array('j', 'n'), $sMask);
        $this->_bSetCurDate = $bSetCurDate;
    }

    /**
     * Html5DateField::setValue()
     *
     * Set the value of the field. A date (yyyy-mm-dd, also with a time behind it) is
     * stored as yyyy-mm-dd, 0000-00-00 and an empty value as no value. Anything else
     * is kept as it is, so that isValid() reports it.
     *
     * @param mixed $mValue: the new value
     * @return void
     * @access public
     */
    public function setValue($mValue)
    {
        $sValue = trim((string) $mValue);

        if ($sValue === '' || strpos($sValue, '0000-00-00') === 0)
        {
            $this->_mValue = null;
        }
        else if (preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})/', $sValue, $data))
        {
            $this->_mValue = $data[1] . '-' . $data[2] . '-' . $data[3];
        }
        else
        {
            $this->_mValue = $sValue;
        }
    }

    /**
     * Html5DateField::getValue()
     *
     * Return the value: yyyy-mm-dd, in view mode formatted with the mask
     *
     * @return string
     * @access public
     */
    public function getValue()
    {
        $sValue = (string) parent::getValue();

        if ($this->getViewMode() && $this->_isDate($sValue))
        {
            list($y, $m, $d) = explode('-', $sValue);
            return date($this->_sDateMask, mktime(0, 0, 0, (int) $m, (int) $d, (int) $y));
        }

        return $sValue;
    }

    /**
     * Html5DateField::getAsArray()
     *
     * Get the date value as an array: array(y,m,d)
     *
     * @return array<int, string>|null null (and a warning) when the value is not a valid date
     * @access public
     */
    public function getAsArray()
    {
        $sValue = (string) $this->_mValue;

        if ($sValue === '')
        {
            return array('', '', '');
        }

        if (!$this->_isDate($sValue))
        {
            // the value can come from the user, so no exception here
            trigger_error('Value is not a valid date [' . $sValue . ']', E_USER_WARNING);
            return null;
        }

        return explode('-', $sValue);
    }

    /**
     * Html5DateField::isValid()
     *
     * Check if the value is a valid date (eg not 2003-02-31)
     *
     * @return boolean
     * @access public
     */
    public function isValid()
    {
        // the result has been requested before..
        if (isset($this->_isValid))
        {
            return $this->_isValid;
        }

        $sValue = (string) $this->_mValue;
        if ($sValue !== '' && !$this->_isDate($sValue))
        {
            $this->_isValid = false;
            $this->_sError = $this->_oForm->_text(14);

            return $this->_isValid;
        }

        return parent::isValid();
    }

    /**
     * Html5DateField::getField()
     *
     * Return the HTML of the field
     *
     * @return string
     * @access public
     */
    public function getField()
    {
        // the current date only in a new form which is shown for the first time
        if (
            $this->_bSetCurDate &&
            !$this->_oForm->isPosted() &&
            (!isset($this->_oForm->edit) || !$this->_oForm->edit) &&
            (string) $this->_mValue === ''
        )
        {
            $this->setValue(date('Y-m-d'));
        }

        return $this->getTypedField('date');
    }

    /**
     * Html5DateField::_isDate()
     *
     * Is the value an existing date in the format yyyy-mm-dd?
     *
     * @param string $sValue
     * @return boolean
     * @access private
     */
    private function _isDate($sValue)
    {
        return preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $sValue, $data) === 1
            && checkdate((int) $data[2], (int) $data[3], (int) $data[1]);
    }
}
