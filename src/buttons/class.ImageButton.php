<?php

/**
 * class ImageButton
 *
 * Create a image button on the given form
 *
 * @author Teye Heimans
 * @package FormHandler
 * @subpackage Buttons
 */
class ImageButton extends Button
{
    /** @var string */
    private string $_sImage;

    /**
     * ImageButton::ImageButton()
     *
     * Constructor: Create a new ImageButton object
     *
     * @param object $oForm: the form where the image button is located on
     * @param string $sName: the name of the button
     * @param string $sImage: the image we have to use as button
     * @access public
     * @author Teye Heimans
     */
    public function __construct(&$oForm, $sName, $sImage)
    {
        parent::__construct($oForm, $sName);

        // set the image we use
        $this->_sImage = $sImage;
    }

    /**
     * ImageButton::getButton()
     *
     * Return the HTML of the button
     *
     * @return string the HTML of the button
     * @access public
     * @author Teye Heimans
     */
    public function getButton()
    {
        // return the button
        return sprintf(
            '<input type="image" src="%s" name="%s" id="%2$s"%s ' . FH_XHTML_CLOSE . '>',
            $this->_sImage,
            $this->_sName,
            (isset($this->_sExtra) ? ' ' . $this->_sExtra : '') .
                (isset($this->_iTabIndex) ? ' tabindex="' . $this->_iTabIndex . '"' : '')
        );
    }
}
