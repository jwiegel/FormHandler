<?php

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * class Editor
 *
 * Create a Editor on the given form with CKEditor 4.22.
 *
 * The HTML is sanitized on the server (symfony/html-sanitizer): the editor can be
 * bypassed by sending the request by hand, so every value (posted, loaded from the
 * database or set with setValue()) only keeps safe elements and attributes.
 * Override getSanitizerConfig() to change the allow list.
 *
 * @author Johan Wiegel
 * @package FormHandler
 * @subpackage Fields
 * @since 2013-12-17
 */

class Editor extends TextArea
{
    /** @var \stdClass */
    private \stdClass $_oEditor;
    /** @var HtmlSanitizer|null HtmlSanitizer, created on first use */
    private ?HtmlSanitizer $_oSanitizer = null;

    /**
     * @param FormHandler $oForm
     * @param string $sName
     */
    public function __construct($oForm, $sName)
    {
        parent::__construct($oForm, $sName);

        static $bSetJS = false;

        // needed javascript included yet ?
        if (!$bSetJS)
        {

            $bSetJS = true;
            $oForm->_setJS(
                FH_FHTML_DIR . "ckeditor/ckeditor.js",
                true
            );
        }

        $this->_oEditor = new stdClass();
        $this->_oEditor->basePath = FH_FHTML_DIR . 'ckeditor/';
        $this->_oEditor->Value = isset($this->_mValue) ? $this->_mValue : '';

        $this->setToolbar('Default'); // Default or Basic

        // set the language
        $this->_oEditor->config['language']  = str_replace('-utf8', '', $oForm->GetLang());

        // default height & width
        $this->setWidth(720);
        $this->setHeight(400);

        // the skin of the bundled CKEditor 4.22
        $this->setSkin('moono-lisa');
    }

    /**
     * Editor::setHeight()
     *
     * Set the height of the editor (in pixels!)
     *
     * @param integer $iHeight: the height
     * @return void
     * @access public
     * @author Teye Heimans
     */
    public function setHeight($iHeight)
    {
        $this->_oEditor->config['height'] = $iHeight;
    }

    /**
     * Editor::setValue()
     *
     * Set the value of the field. The HTML is sanitized, see getSanitizerConfig().
     *
     * @param string|null $sValue: The html to set into the field
     * @return void
     * @access public
     * @author Teye Heimans
     */
    public function setValue($sValue)
    {
        $this->_mValue = is_null($sValue) ? null : $this->_getSanitizer()->sanitize((string) $sValue);
    }

    /**
     * Editor::getSanitizerConfig()
     *
     * The allow list for the HTML of the editor: the safe elements and attributes
     * (no scripts, event handlers, javascript: or data: links, iframes or forms),
     * absolute and relative links and images. With isStyleAllowed() also the
     * style attribute (for colors and font sizes), checked by EditorStyleSanitizer.
     * Override it in a subclass for another allow list.
     *
     * @return HtmlSanitizerConfig
     * @access protected
     */
    protected function getSanitizerConfig()
    {
        $config = (new HtmlSanitizerConfig())
            ->allowSafeElements()
            ->allowRelativeLinks()
            ->allowRelativeMedias()
            // no limit: the sanitizer cuts longer input to 20000 chars by default
            ->withMaxInputLength(-1);

        if ($this->isStyleAllowed())
        {
            $config = $config
                ->allowAttribute('style', '*')
                ->withAttributeSanitizer(new EditorStyleSanitizer());
        }

        return $config;
    }

    /**
     * Editor::isStyleAllowed()
     *
     * Keep the style attribute (colors, font sizes, alignment)? Default: FH_EDITOR_ALLOW_STYLE.
     * Set it for all editors with fh_conf('FH_EDITOR_ALLOW_STYLE', true) before the first form
     * is created, or override this method in a subclass.
     *
     * @return bool
     * @access protected
     */
    protected function isStyleAllowed()
    {
        return (bool) FH_EDITOR_ALLOW_STYLE;
    }

    /**
     * Editor::_getSanitizer()
     *
     * @return HtmlSanitizer
     * @access private
     */
    private function _getSanitizer()
    {
        if ($this->_oSanitizer === null)
        {
            $this->_oSanitizer = new HtmlSanitizer($this->getSanitizerConfig());
        }

        return $this->_oSanitizer;
    }


    /**
     * Editor::setWidth()
     *
     * Set the width of the editor  (in pixels!)
     *
     * @param integer $iWidth: the width
     * @return void
     * @access public
     * @author Teye Heimans
     */
    public function setWidth($iWidth)
    {
        $this->_oEditor->config['width'] = $iWidth;
    }

    /**
     * Editor::setToolbar()
     *
     * Set the toolbar we should use for the editor
     *
     * @param string $sToolbar: The toolbar we should use
     * @return void
     * @access public
     * @author Teye Heimans
     */
    public function setToolbar($sToolbar)
    {
        $this->_oEditor->config['toolbar'] = $sToolbar;
    }


    /**
     * Editor::setConfig()
     *
     * Set extra config options for the editor
     *
     * @param array<string, mixed> $config: The config array with extra config options to set for the fckeditor
     * @return void
     * @access public
     * @author Teye Heimans
     */
    public function setConfig($config)
    {
        $this->_oEditor->config = array_merge($this->_oEditor->config, $config);
    }

    /**
     * Editor::setServerPath()
     *
     * Not used any more: it linked the file manager of FormHandler for browsing and
     * uploading images, which is removed since 4.7. A project can add an own file
     * browser with setConfig(array('filebrowserBrowseUrl' => ...)) or a CKEditor plugin.
     *
     * @param string|false $sPath: The path
     * @return void
     * @access public
     * @deprecated since 4.7
     * @author Teye Heimans
     */
    public function setServerPath($sPath)
    {
    }

    /**
     * Editor::setSkin()
     *
     * Set the skin used for the CKEditor
     *
     * @param string $sSkin
     * @return void
     * @access public
     * @author Teye Heimans
     */
    public function setSkin($sSkin)
    {
        // CKEditor 4.22 no longer ships "moono", its successor is "moono-lisa"
        $this->_oEditor->config['skin'] = $sSkin == 'moono' ? 'moono-lisa' : $sSkin;
    }

    /**
     * Editor::getField()
     *
     * return the field
     *
     * @return string the field
     * @author Teye Heimans
     * @access public
     */
    public function getField()
    {
        // view mode enabled ?
        if ($this->getViewMode())
        {
            // get the view value..
            return $this->_getViewValue();
        }

        $html = parent::getField();

        // add the javascript needed for the js calendar field
        $this->_oForm->_setJS(
            "
            CKEDITOR.replace( '" . $this->_sName . "', " . json_encode($this->_oEditor->config) . " );		
        ",
            false,
            false
        );

        return $html;
    }
}
