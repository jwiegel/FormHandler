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
            'FHTML/overlib/overlib.js',
            'FHTML/overlib/overlib_hideform.js',
            'Textfield:<input type="text" name="textfield" id="textfield" value="" size="20" /><img src="',
            'FHTML/images/helpicon.gif" border="0" onmouseover="return overlib(\'This ist a help text\', DELAY, \'400\', FGCOLOR, \'#CCCCCC\', BGCOLOR, \'#666666\', TEXTCOLOR, \'#666666\', TEXTFONT, \'Verdana\', TEXTSIZE, \'12px\', CELLPAD, 8, BORDER, 1, CAPTION, \'&nbsp;Textfield - Help\', CAPTIONSIZE, \'12px\');" onmouseout="return nd();" style="color:333333;cursor:help;" />error_textfield'
        ]);
    }

    public function test_helpTitle(): void
    {
        $form = new FormHandler();

        $form->textField("Textfield", "textfield");
        $form->setHelpText("textfield", "This ist a help text", "The helptitle");

        static::assertFormFlushContains($form, [
            'FHTML/overlib/overlib.js',
            'FHTML/overlib/overlib_hideform.js',
            'Textfield:<input type="text" name="textfield" id="textfield" value="" size="20" /><img src="',
            'FHTML/images/helpicon.gif" border="0" onmouseover="return overlib(\'This ist a help text\', DELAY, \'400\', FGCOLOR, \'#CCCCCC\', BGCOLOR, \'#666666\', TEXTCOLOR, \'#666666\', TEXTFONT, \'Verdana\', TEXTSIZE, \'12px\', CELLPAD, 8, BORDER, 1, CAPTION, \'&nbsp;The helptitle\', CAPTIONSIZE, \'12px\');" onmouseout="return nd();" style="color:333333;cursor:help;" />error_textfield'
        ]);
    }

    public function test_helpIcon(): void
    {
        $form = new FormHandler();

        $form->textField("Textfield", "textfield");
        $form->setHelpText("textfield", "This ist a help text");
        $form->setHelpIcon("theicon.gif");

        static::assertFormFlushContains($form, [
            'FHTML/overlib/overlib.js',
            'FHTML/overlib/overlib_hideform.js',
            'Textfield:<input type="text" name="textfield" id="textfield" value="" size="20" /><img src="theicon.gif" border="0" onmouseover="return overlib(\'This ist a help text\', DELAY, \'400\', FGCOLOR, \'#CCCCCC\', BGCOLOR, \'#666666\', TEXTCOLOR, \'#666666\', TEXTFONT, \'Verdana\', TEXTSIZE, \'12px\', CELLPAD, 8, BORDER, 1, CAPTION, \'&nbsp;Textfield - Help\', CAPTIONSIZE, \'12px\');" onmouseout="return nd();" style="color:333333;cursor:help;" />error_textfield'
        ]);
    }
};
