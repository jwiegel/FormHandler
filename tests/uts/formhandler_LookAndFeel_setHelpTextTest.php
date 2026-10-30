<?php

declare(strict_types=1);

final class formhandler_LookAndFeel_setHelpTextTest extends FormhandlerTestCase
{
    public function test(): void
    {
        $form = new FormHandler();

        $form->textField("Textfield", "textfield");
        $form->setHelpText("textfield", "This ist a help text");

        static::assertFormFlushContains($form, [
            'FHTML/js/helptext.js',
            'Textfield:<input type="text" name="textfield" id="textfield" value="" size="20" /><img src="',
            'FHTML/images/helpicon.gif" alt="?" border="0" tabindex="0" data-fh-help="This ist a help text" data-fh-help-title="Textfield - Help" style="cursor:help;" />error_textfield'
        ]);
    }

    public function test_helpTitle(): void
    {
        $form = new FormHandler();

        $form->textField("Textfield", "textfield");
        $form->setHelpText("textfield", "This ist a help text", "The helptitle");

        static::assertFormFlushContains($form, [
            'FHTML/js/helptext.js',
            'data-fh-help="This ist a help text" data-fh-help-title="The helptitle" style="cursor:help;" />error_textfield'
        ]);
    }

    public function test_helpIcon(): void
    {
        $form = new FormHandler();

        $form->textField("Textfield", "textfield");
        $form->setHelpText("textfield", "This ist a help text");
        $form->setHelpIcon("theicon.gif");

        static::assertFormFlushContains($form, [
            'Textfield:<input type="text" name="textfield" id="textfield" value="" size="20" /><img src="theicon.gif" alt="?" border="0" tabindex="0" data-fh-help="This ist a help text"'
        ]);
    }

    public function test_html(): void
    {
        $form = new FormHandler();

        $form->textField("Textfield", "textfield");
        $form->setHelpText("textfield", "Line one<br>\"two\" & 'three'", "It's <b>bold</b>");

        // escaped for the attribute; helptext.js shows the text as HTML and the title as text
        static::assertFormFlushContains($form, [
            'data-fh-help="Line one&lt;br&gt;&quot;two&quot; &amp; &#039;three&#039;" data-fh-help-title="It&#039;s &lt;b&gt;bold&lt;/b&gt;"'
        ]);
    }

    public function test_noJavascript(): void
    {
        $form = new FormHandler();

        $form->textField("Textfield", "textfield");
        $form->setHelpText("textfield", "This ist a help text");

        static::assertStringNotContainsString('overlib', $form->flush(true));
    }
};
