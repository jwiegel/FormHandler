<?php

/**
 * class ResetButton
 *
 * Create a resetbutton on the given form
 *
 * @author Teye Heimans
 * @package FormHandler
 * @subpackage Buttons
 */
class ResetButton extends Button
{
    /**
     * ResetButton::ResetButton()
     *
     * constructor: Create a new reset button object
     *
     * @param object $oForm: the form where the button is located on
     * @param string $sName: the name of the button
     * @access public
     * @author Teye Heimans
     */
    public function __construct(&$oForm, $sName)
    {
        parent::__construct($oForm, $sName);

        $this->setCaption($oForm->_text(27));
    }

    /**
     * ResetButton::getButton()
     *
     * Return the HTMl of the button
     *
     * @return string the html of the button
     * @access public
     * @author Teye Heimans
     */
    public function getButton()
    {
        return sprintf(
            '<input type="reset" value="%s" name="%s" id="%2$s"%s ' . FH_XHTML_CLOSE . '>',
            $this->_sCaption,
            $this->_sName,
            (isset($this->_sExtra) ? ' ' . $this->_sExtra : '') .
                (isset($this->_iTabIndex) ? ' tabindex="' . $this->_iTabIndex . '"' : '')
        );
    }
}
