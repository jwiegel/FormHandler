<?php

/**
 * class CheckBox
 *
 * Create a checkbox on the given form object
 *
 * @author Teye Heimans
 * @package FormHandler
 * @subpackage Fields
 */

class CheckBox extends Field
{
    /** @var array<int|string, mixed>|string contains all the options! */
    protected $_aOptions;
    // $this->_mValue contains the values which are selected!
    /** @var bool|null if the keys of the array should be used as values */
    private ?bool $_bUseArrayKeyAsValue = null;
    /** @var string|null what kind of "glue" should be used to merge the checkboxes */
    private ?string $_sMask = null;
    /** @var MaskLoader|null The maskLoader */
    private ?MaskLoader $_oLoader = null;

    /**
     * CheckBox::CheckBox()
     *
     * Constructor: Create a new checkbox object
     *
     * @param object $oForm: The form where this field is located on
     * @param string $sName: The name of the field
     * @param array<int|string, mixed>|string $aOptions - The options for the field
     * @return CheckBox
     * @access public
     * @author Teye Heimans
     */
    public function __construct(&$oForm, $sName, $aOptions)
    {
        $this->_mValue = '';
        $sName = str_replace('[]', '', $sName);

        $this->_aOptions = $aOptions;

        // call the constructor of the Field class
        parent::__construct($oForm, $sName);

        $this->setMask(FH_DEFAULT_GLUE_MASK);
        $this->useArrayKeyAsValue(FH_DEFAULT_USEARRAYKEY);
    }

    /**
     * CheckBox::setValue()
     *
     * Set the value of the field
     *
     * @param array<int|string, mixed>|string|null $aValue: the value to set
     * @return void
     * @access public
     * @author Teye Heimans
     */
    public function setValue($aValue)
    {
        // make an array from the value
        if (!is_array($aValue) && is_array($this->_aOptions))
        {
            $aValue = explode(',', $aValue);
            foreach ($aValue as $iKey => $sValue)
            {
                $sValue = trim($sValue);

                // dont save an empty value when it does not exists in the
                // options array!
                if (
                    !empty($sValue)  ||
                    in_array($sValue, $this->_aOptions) ||
                    array_key_exists($sValue, $this->_aOptions)
                )
                {
                    $aValue[$iKey] = $sValue;
                }
                else
                {
                    unset($aValue[$iKey]);
                }
            }
        }

        $this->_mValue = $aValue;
    }

    /**
     * CheckBox::useArrayKeyAsValue()
     *
     * Set if the array keys of the options has to be used as values for the field
     *
     * @param boolean $bMode
     * @return void
     * @access public
     * @author Teye Heimans
     */
    public function useArrayKeyAsValue($bMode)
    {
        $this->_bUseArrayKeyAsValue = $bMode;
    }

    /**
     * CheckBox::setMask()
     *
     * Set the glue used to glue multiple checkboxes. This can be a mask
     * where %field% is replaced with a checkbox!
     *
     * @param string $sMask
     * @return void
     * @author Teye Heimans
     * @access Public
     */
    public function setMask($sMask)
    {
        // when there is no %field% used, put it in front of the mask/glue
        if (strpos($sMask, '%field%') === false)
        {
            $sMask = '%field%' . $sMask;
        }

        $this->_sMask = $sMask;
    }

    /**
     * CheckBox::getField()
     *
     * Return the HTML of the field
     *
     * @return string the html of the field
     * @access Public
     * @author Teye Heimans
     */
    public function getField()
    {
        // view mode enabled ?
        if ($this->getViewMode())
        {
            // get the view value..
            return $this->_getViewValue();
        }

        // multiple checkboxes ?
        if (is_array($this->_aOptions) && count($this->_aOptions) > 0)
        {
            $sResult = '';

            // get the checkboxes
            foreach ($this->_aOptions as $iKey => $sValue)
            {
                // use the array key as value?
                if (!$this->_bUseArrayKeyAsValue)
                {
                    $iKey = $sValue;
                }

                // get the checbox
                $sResult .= $this->_getCheckBox($iKey, $sValue, true);
            }

            // get a possible half filled mask
            $sResult .= $this->_oLoader->fill();
        }
        elseif (is_array($this->_aOptions) && count($this->_aOptions) === 0)
        {
            $sResult = '';
        }

        // just 1 checkbox...
        else
        {
            $sResult = $this->_getCheckBox($this->_aOptions, '');
        }

        return $sResult .
            (isset($this->_sExtraAfter) ? $this->_sExtraAfter : '');
    }

    /**
     * CheckBox::_getCheckBox()
     *
     * Return an option of the checkbox with the given value
     *
     * @param string $sValue: the value for the checkbox
     * @param string $sTitle: the title for the checkbox
     * @param bool $bUseMask: do we have to use the mask after the field?
     * @return string the HTML for the checkbox
     * @access private
     * @author Teye Heimans
     */
    private function _getCheckBox(mixed $sValue, mixed $sTitle, bool $bUseMask = false): string
    {
        static $iCounter = 1;

        // create a MaskLoader object when it does not exists yet
        if (!isset($this->_oLoader))
        {
            $this->_oLoader = new MaskLoader();
            $this->_oLoader->setMask($this->_sMask);
            $this->_oLoader->setSearch('/%field%/');
        }

        // remove unwanted spaces
        $sValue = trim($sValue);
        $sTitle = trim($sTitle);

        // get the field HTML
        if ($sTitle == '')
        {
            $sField = sprintf(
                '<input type="checkbox" name="%s" id="%s_%d" value="%s" %s' . FH_XHTML_CLOSE . '>',
                $this->_sName . (is_array($this->_aOptions) ? '[]' : ''),
                $this->_sName,
                $iCounter++,
                htmlspecialchars($sValue, ENT_COMPAT | ENT_HTML401, FH_HTML_ENCODING),
                (isset($this->_iTabIndex) ? 'tabindex="' . $this->_iTabIndex . '" ' : '') .
                    ((isset($this->_mValue) && ((is_array($this->_mValue) && in_array($sValue, $this->_mValue)) || $sValue == $this->_mValue)) ?
                        'checked="checked" ' : '') .
                    (isset($this->_sExtra) ? $this->_sExtra . ' ' : ''),
                $sTitle
            );
        }
        else
        {
            $sField = sprintf(
                '<input type="checkbox" name="%s" id="%s_%d" value="%s" %s' . FH_XHTML_CLOSE . '><label for="%2$s_%3$d" class="noStyle">%s</label>',
                $this->_sName . (is_array($this->_aOptions) ? '[]' : ''),
                $this->_sName,
                $iCounter++,
                htmlspecialchars($sValue, ENT_COMPAT | ENT_HTML401, FH_HTML_ENCODING),
                (isset($this->_iTabIndex) ? 'tabindex="' . $this->_iTabIndex . '" ' : '') .
                    ((isset($this->_mValue) && ((is_array($this->_mValue) && in_array($sValue, $this->_mValue)) || $sValue == $this->_mValue)) ?
                        'checked="checked" ' : '') .
                    (isset($this->_sExtra) ? $this->_sExtra . ' ' : ''),
                $sTitle
            );
        }
        // do we have to use the mask ?
        if ($bUseMask)
        {
            $sField = $this->_oLoader->fill($sField);
        }

        return $sField;
    }
}
