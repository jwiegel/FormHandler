<?php

/**
 * class TextSelectField
 *
 * Create a textselectfield
 *
 * @author Johan Wiegel
 * @since 22-10-2008
 * @package FormHandler
 * @subpackage Fields
 */
class TextSelectField extends TextField
{
    /** @var string|null */
    private ?string $_sOptions = null;

    /**
     * TextSelectField::TextSelectField()
     *
     * Constructor: Create a new textfield object
     *
     * @param object &$oForm: The form where this field is located on
     * @param string $sName: The name of the field
     * @param array<int|string, mixed> $aOptions
     * @return TextField
     * @author Teye Heimans
     * @access public
     */

    public function __construct(&$oForm, $sName, $aOptions)
    {
        parent::__construct($oForm, $sName);

        static $bSetJS = false;

        // needed javascript included yet ?
        if (!$bSetJS)
        {
            $bSetJS = true;

            // the list of the options (FH_TEXTSELECT_MASK)
            $oForm->_setJS(FH_FHTML_DIR . 'js/autocomplete.js', true);
        }

        // own masks of FormHandler 4.6 and older call these functions
        static $bSetOldJS = false;
        if (!$bSetOldJS && strpos(FH_TEXTSELECT_MASK . FH_TEXTSELECT_OPTION_MASK, '_TEXTSELECT(') !== false)
        {
            $bSetOldJS = true;
            $oForm->_setJS(
                "function FH_CLOSE_TEXTSELECT( id )" . "\n" .
                    "{" . "\n" .
                    "  setTimeout( 'document.getElementById(\"'+id+'\").style.display=\"none\"', 110 );" . "\n" .
                    "}" . "\n\n" .
                    "function FH_SET_TEXTSELECT( id, waarde )" . "\n" .
                    "{" . "\n" .
                    "  document.getElementById(id).value=waarde;" . "\n" .
                    "  FH_CLOSE_TEXTSELECT( 'FHSpan_'+id );return false;" . "\n" .
                    "}" . "\n\n"
            );
        }

        foreach ($aOptions as $key => $value)
        {
            $this->_sOptions .= sprintf(FH_TEXTSELECT_OPTION_MASK, $sName, htmlspecialchars((string) $value, ENT_QUOTES, FH_HTML_ENCODING));
        }

        $this->setSize(20);
        $this->setMaxlength(0);
    }

    public function getField()
    {
        // view mode enabled ?
        if ($this->getViewMode())
        {
            // get the view value..
            return $this->_getViewValue();
        }

        return sprintf(
            FH_TEXTSELECT_MASK,
            $this->_sName,
            (isset($this->_mValue) ? htmlspecialchars($this->_mValue, ENT_COMPAT | ENT_HTML401, FH_HTML_ENCODING) : ''),
            $this->_iSize,
            (!empty($this->_iMaxlength) ? 'maxlength="' . $this->_iMaxlength . '" ' : '') .
                (isset($this->_iTabIndex) ? 'tabindex="' . $this->_iTabIndex . '" ' : '') .
                (isset($this->_sExtra) ? ' ' . $this->_sExtra . ' ' : ''),
            (isset($this->_sExtraAfter) ? $this->_sExtraAfter : ''),
            $this->_sOptions
        );
    }
}
