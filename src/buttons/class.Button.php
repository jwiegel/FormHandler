<?php

/**
 * class Button
 *
 * Create a button on the given form
 *
 * @author Teye Heimans
 * @package FormHandler
 * @subpackage Buttons
 */
class Button
{
    /** @var string */
    protected $_sName;
    /** @var string|null */
    protected $_sExtra;
    /** @var string|null */
    protected $_sCaption;
    /** @var int|string|null */
    protected $_iTabIndex;

    /**
     * Button::Button()
     *
     * Constructor: create a new Button object
     *
     * @param object $oForm: the form where the button is located on
     * @param string $sName: the name of the button
     * @access public
     * @author Teye Heimans
     */
    public function __construct(&$oForm, $sName)
    {
        if (!is_object($oForm))
        {
            throw new FormHandlerException(
                'Error: invalid form object given to Button constructor'
            );
        }

        // set the button name and caption
        $this->_sName    = $sName;
    }

    /**
     * Field::setTabIndex()
     *
     * set the tabindex of the field
     *
     * @param int $iIndex
     * @return void
     * @access public
     * @author Teye Heimans
     */
    public function setTabIndex($iIndex)
    {
        $this->_iTabIndex = $iIndex;
    }


    /**
     * Button::setCaption()
     *
     * Set the caption of the button
     *
     * @param string $sCaption: The caption of the button
     * @return void
     * @access public
     * @author Teye Heimans
     */
    public function setCaption($sCaption)
    {
        $this->_sCaption = $sCaption;
    }

    /**
     * Button::getButton()
     *
     * Return the HTML of the button
     *
     * @return string the button
     * @access public
     * @author Teye Heimans
     */
    public function getButton()
    {
        return sprintf(
            '<input type="button" name="%s" id="%1$s" value="%s"%s ' . FH_XHTML_CLOSE . '>',
            $this->_sName,
            $this->_sCaption,
            (isset($this->_sExtra) ? ' ' . $this->_sExtra : '') .
                (isset($this->_iTabIndex) ? ' tabindex="' . $this->_iTabIndex . '"' : '')
        );
    }

    /**
     * Button::setExtra()
     *
     * Set extra tag information, like CSS or Javascript
     *
     * @param string $sExtra: the CSS, JS or other extra tag info
     * @return void
     * @access public
     * @author Teye Heimans
     */
    public function setExtra($sExtra)
    {
        $this->_sExtra = $sExtra;
    }
}
