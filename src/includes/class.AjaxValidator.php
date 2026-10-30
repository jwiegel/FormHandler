<?php

/**
 * Class AjaxValidator
 * 
 * Uses the validator class to validate FormHandler fields on the fly.
 * 
 * @since 03-12-2008
 * @author Johan Wiegel @ PHP-GLOBE
 * @package FormHandler
 * 
 */

class AjaxValidator
{
    /** @var bool not used any more: the javascript needs no library since 4.7 */
    static $bScript = true;

    /**
     * @param bool $bScript not used any more (it included jQuery)
     */
    public function __construct($bScript = true)
    {
        self::$bScript = $bScript;
    }

    /**
     * Add the javascript which validates the fields with a validator when they lose
     * the focus (select fields: when they change)
     *
     * @param FormHandler $oForm
     * @param array<int|string, array<int, mixed>> $aFields the fields of the form
     * @param array<string, array{0: string, 1: mixed}> $aCustomMsg the custom messages of the fields
     * @return void
     */
    public function CreateObservers($oForm, $aFields, $aCustomMsg)
    {
        static $bSetJS = false;

        // needed javascript included yet ?
        if (!$bSetJS)
        {
            $bSetJS = true;

            $oForm->_setJS(FH_FHTML_DIR . "js/ajax_validator.js", true, false);

            // create observers for all fields with a validator
            $sScript = "";
            $sScript2 = "";

            foreach ($aFields as $sField => $aField)
            {
                // only the validators of the Validator class, not own callbacks
                if (!is_object($aField[1]) || !method_exists($aField[1], 'getValidator') || !is_string($aField[1]->getValidator()) || $aField[1]->getValidator() == '')
                {
                    continue;
                }

                if (isset($aCustomMsg[$sField][0]) and $aCustomMsg[$sField][0] != '')
                {
                    $sMsg = $aCustomMsg[$sField][0];
                }
                else
                {
                    $sMsg = $oForm->_text(14);
                }

                if (is_a($aField[1], 'selectField'))
                {
                    $sEvent = 'change';
                }
                elseif (is_a($aField[1], 'timeField'))
                {
                    $sField .= "_hour";
                    $sEvent = 'blur';
                }
                elseif (is_a($aField[1], 'checkBox') || is_a($aField[1], 'radioButton'))
                {
                    // no on the fly checking yet
                    continue;
                }
                else
                {
                    $sEvent = 'blur';
                }

                $sArgs = implode(', ', array_map(array($this, '_js'), array($aField[1]->getValidator(), (string) $sField, FH_FHTML_DIR, $sMsg)));
                $sScript .= "FH_OBSERVE(" . $this->_js($sEvent) . ", " . $sArgs . ");\n";
                if ($aField[1]->getValidator() <> 'FH_CHECK_DOMAIN')
                {
                    $sScript2 .= "FH_CHECK(" . $sArgs . ");\n";
                }
            }

            $oForm->_setJS($sScript, false, false);

            // after a post the fields are checked at once, to show their state
            if ($oForm->isPosted())
            {
                $oForm->_setJS($sScript2, false, false);
            }
        }
    }

    /**
     * A value as javascript string, safe inside a script tag
     *
     * @param string $sValue
     * @return string
     */
    private function _js(string $sValue): string
    {
        return (string) json_encode($sValue, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }

    /**
     * Validate a value for ajax_validator.js (src/FHTML/ajax/validate.php)
     *
     * @param array<string, mixed> $aRequest the request: validator ("|" between more), value and msg
     * @param object $oValidator
     * @return string JSON {"status": "error"|"mandatory"|"ok", "msg": message}, or "" for a wrong request
     */
    public function Validate($aRequest, $oValidator)
    {
        foreach (array('validator', 'msg', 'value') as $sKey)
        {
            if (!isset($aRequest[$sKey]) || !is_string($aRequest[$sKey]))
            {
                return '';
            }
        }

        $bChecked = false;
        foreach (explode('|', $aRequest['validator']) as $sValidator)
        {
            // CAPTCHA can not be validated by AJAX
            if ($sValidator == '' || $sValidator == 'FH_CAPTCHA' || !method_exists($oValidator, $sValidator))
            {
                continue;
            }

            $bChecked = true;
            if ($oValidator->$sValidator($aRequest['value']) == false)
            {
                return (string) json_encode(array('status' => 'error', 'msg' => $aRequest['msg']));
            }
        }

        if (!$bChecked)
        {
            return '';
        }

        return (string) json_encode(array('status' => $aRequest['value'] === '' ? 'mandatory' : 'ok', 'msg' => ''));
    }
}
