<?php

/**
 * class BrowserField
 *
 * Create a browserfield: since 4.7 a text field without the button of the file manager
 * (the file manager is removed)
 *
 * @deprecated since 4.7, use a textField() (or an uploadField())
 * @author Johan Wiegel
 * @package FormHandler
 * @subpackage Fields
 */
class BrowserField extends Field
{
    /** @var int|null the size of the field */
    private ?int $_iSize = null;

    /**
     * TextField::BrowserField()
     *
     * Constructor: Create a new textfield object
     *
     * @param object &$oForm: The form where this field is located on
     * @param string $sName: The name of the field
     * @param string $sPath: not used any more (it was the path of the file manager)
     * @return BrowserField
     * @author Johan Wiegel
     * @access public
     */
    // @phpstan-ignore constructor.unusedParameter ($sPath stays for existing calls)
    public function __construct(&$oForm, $sName, $sPath)
    {
        // call the constructor of the Field class
        parent::__construct($oForm, $sName);
        $this->setSize(20);
    }

    /**
     * TextField::setSize()
     *
     * Set the new size of the field
     *
     * @param integer $iSize: the new size
     * @return void
     * @author Teye Heimans
     * @access public
     */
    public function setSize($iSize)
    {
        $this->_iSize = $iSize;
    }

    /**
     * TextField::getField()
     *
     * Return the HTML of the field
     *
     * @return string the html
     * @access public
     * @author Johan Wiegel
     */
    public function getField()
    {
        // view mode enabled ?
        if ($this->getViewMode())
        {
            // get the view value..
            return $this->_getViewValue();
        }

        return sprintf(
            '<input type="text" name="%s" id="%1$s" value="%s" size="%d" %s' . FH_XHTML_CLOSE . '>%s',
            $this->_sName,
            (isset($this->_mValue) ? htmlspecialchars($this->_mValue, ENT_COMPAT | ENT_HTML401, FH_HTML_ENCODING) : ''),
            $this->_iSize,
            (isset($this->_iTabIndex) ? 'tabindex="' . $this->_iTabIndex . '" ' : '') .
                (isset($this->_sExtra) ? ' ' . $this->_sExtra . ' ' : ''),
            (isset($this->_sExtraAfter) ? $this->_sExtraAfter : '')
        );
    }
}
