<?php

/**
 * class ColorPicker
 *
 * Allows the user to pick a color: a text field with the color as hex value
 * (e.g. FFCC00, empty when no color is chosen) and the color input of the browser
 *
 * @author Rick den Haan
 * @package FormHandler
 * @subpackage Fields
 * @since 02-07-2008
 */
class ColorPicker extends TextField
{
    /** @var string not used any more (it was added to the title), removed in 5.0 */
    public $sTitleAdd = "";

    /**
     * ColorPicker::ColorPicker()
     *
     * Constructor: Create a new ColorPicker object
     *
     * @param object &$oForm: The form where this field is located on
     * @param string $sName: The name of the field
     * @return ColorPicker
     * @access public
     * @author Rick den Haan
     */
    public function __construct(&$oForm, $sName)
    {
        parent::__construct($oForm, $sName);

        static $bSetJS = false;

        // needed javascript included yet ?
        if (!$bSetJS)
        {
            // include the needed javascript
            $bSetJS = true;
            $oForm->_setJS(FH_FHTML_DIR . "js/colorpicker.js", true);
        }
    }
    /**
     * ColorPicker::getField()
     *
     * Return the HTML of the field
     *
     * @return string the html of the field
     * @access public
     * @author Rick den Haan
     */
    public function getField()
    {
        // view mode enabled ?
        if ($this->getViewMode())
        {
            // get the view value..
            return $this->_getViewValue();
        }

        // check if the user set a class
        if (isset($this->_sExtra) && preg_match("/class *= *('|\")(.*)$/i", $this->_sExtra))
        {
            // put the function into a onchange tag if set
            $this->_sExtra = preg_replace("/class *= *('|\")(.*)$/i", "class=\"color \\2", $this->_sExtra);
        }
        else
        {
            $this->_sExtra = "class=\"color\"" . (isset($this->_sExtra) ? $this->_sExtra : '');
        }

        // the color input of the browser can not be empty, so the value stays in the text
        // field and the color input (without a name) only picks it (js/colorpicker.js)
        $sValue = isset($this->_mValue) && is_string($this->_mValue) ? $this->_mValue : '';
        $sColor = preg_match('/^#?([0-9a-fA-F]{6})$/', $sValue, $aMatch) ? '#' . strtolower($aMatch[1]) : '#000000';

        return sprintf(
            '<input type="text" name="%s" id="%1$s" value="%s" size="%d" %s' . FH_XHTML_CLOSE . '>' .
                '<input type="color" id="%1$s_picker" value="%s" data-fh-color="%1$s" ' . FH_XHTML_CLOSE . '>%s',
            $this->_sName,
            htmlspecialchars($sValue, ENT_COMPAT | ENT_HTML401, FH_HTML_ENCODING),
            $this->_iSize,
            (!empty($this->_iMaxlength) ? 'maxlength="' . $this->_iMaxlength . '" ' : '') .
                (isset($this->_iTabIndex) ? 'tabindex="' . $this->_iTabIndex . '" ' : '') .
                (isset($this->_sExtra) ? ' ' . $this->_sExtra . ' ' : ''),
            $sColor,
            (isset($this->_sExtraAfter) ? $this->_sExtraAfter : '')
        );
    }
}
