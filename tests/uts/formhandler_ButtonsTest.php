<?php

declare(strict_types=1);

final class formhandler_ButtonsTest extends FormhandlerTestCase
{
    public function test_button(): void
    {
        $form = new FormHandler();

        $form->button("Button");
        $form->button("Button2", "button4711");
        $form->button("Button3", null, 'data-old="123"');

        static::assertFormFlushContains($form, [
            '<input type="button" name="button1" id="button1" value="Button" />error_button1',
            '<input type="button" name="button4711" id="button4711" value="Button2" />error_button4711',
            '<input type="button" name="button2" id="button2" value="Button3" data-old="123" />error_button2'
        ]);
    }

    public function test_button_setIndex(): void
    {
        $form = new FormHandler();

        $form->button("Button", "but1");
        $form->button("Button2", "but2");
        $form->button("Button3", "but3");

        $form->setTabIndex(array("but2", "but1", "but3"));

        static::assertFormFlushContains($form, [
            '<input type="button" name="but1" id="but1" value="Button" tabindex="2" />error_but1',
            '<input type="button" name="but2" id="but2" value="Button2" tabindex="1" />error_but2',
            '<input type="button" name="but3" id="but3" value="Button3" tabindex="3" />error_but3'
        ]);
    }

    public function test_submitButtom(): void
    {
        $form = new FormHandler();

        $form->submitButton();
        $form->submitButton("Caption");
        $form->submitButton(null, "submitbutton");
        $form->submitButton(null, null, 'data-old="123"');
        $form->submitButton(null, null, null, false);

        static::assertFormFlushContains($form, [
            '<input type="submit" value="Submit" name="button1" id="button1"  onclick=" if (this.form.querySelector(\':invalid\') == null) { this.form.submit();this.disabled=true;}"  />error_button1',
            '<input type="submit" value="Caption" name="button2" id="button2"  onclick=" if (this.form.querySelector(\':invalid\') == null) { this.form.submit();this.disabled=true;}"  />error_button2',
            '<input type="submit" value="Submit" name="submitbutton" id="submitbutton"  onclick=" if (this.form.querySelector(\':invalid\') == null) { this.form.submit();this.disabled=true;}"  />error_submitbutton',
            '<input type="submit" value="Submit" name="button3" id="button3"  onclick=" if (this.form.querySelector(\':invalid\') == null) { this.form.submit();this.disabled=true;}" data-old="123" />error_button3',
            '<input type="submit" value="Submit" name="button4" id="button4"  />error_button4'
        ]);
    }

    public function test_submitButtom_onClick(): void
    {
        $form = new FormHandler();

        $form->submitButton("Caption", "submitbutton", "onClick='doIt()'");

        static::assertFormFlushContains($form, ['<input type="submit" value="Caption" name="submitbutton" id="submitbutton"  onclick=\'this.form.submit();this.disabled=true;doIt()\' />error_submitbutton']);
    }

    public function test_imageButton(): void
    {
        $form = new FormHandler();

        $form->imageButton("image.gif");
        $form->imageButton("image.gif", "imagebutton");
        $form->imageButton("image.gif", null, 'data-old="123"');

        static::assertFormFlushContains($form, [
            '<input type="image" src="image.gif" name="button1" id="button1" />error_button1',
            '<input type="image" src="image.gif" name="imagebutton" id="imagebutton" />error_imagebutton',
            '<input type="image" src="image.gif" name="button2" id="button2" data-old="123" />error_button2'
        ]);
    }

    public function test_resetButton(): void
    {
        $form = new FormHandler();

        $form->resetButton();
        $form->resetButton("Resetbutton");
        $form->resetButton(null, "resetbutton");
        $form->resetButton(null, null, 'data-old="123"');

        static::assertFormFlushContains($form, [
            '<input type="reset" value="Reset" name="button1" id="button1" />error_button1',
            '<input type="reset" value="Resetbutton" name="button2" id="button2" />error_button2',
            '<input type="reset" value="Reset" name="resetbutton" id="resetbutton" />error_resetbutton',
            '<input type="reset" value="Reset" name="button3" id="button3" data-old="123" />error_button3'
        ]);
    }

    public function test_cancelButton(): void
    {
        $form = new FormHandler();

        $form->cancelButton();
        $form->cancelButton("CancelButton");
        $form->cancelButton(null, "backurl");
        $form->cancelButton(null, null, "cancelbutton");
        $form->cancelButton(null, null, null, 'data-old="123"');

        static::assertFormFlushContains($form, [
            '<input type="button" name="button1" id="button1" value="Cancel"  onclick="history.back(-1)" />error_button1',
            '<input type="button" name="button2" id="button2" value="CancelButton"  onclick="history.back(-1)" />error_button2',
            '<input type="button" name="button3" id="button3" value="Cancel"  onclick="document.location.href=\'backurl\'" />error_button3',
            '<input type="button" name="cancelbutton" id="cancelbutton" value="Cancel"  onclick="history.back(-1)" />error_cancelbutton',
            '<input type="button" name="button4" id="button4" value="Cancel" data-old="123" onclick="history.back(-1)" />error_button4'
        ]);
    }

    public function test_backButton(): void
    {
        $form = new FormHandler();

        $form->backButton();
        $form->backButton("BackButton");
        $form->backButton(null, "backbutton");
        $form->backButton(null, null, 'data-old="123"');

        static::assertFormFlushContains($form, [
            '<input type="button" name="button1" id="button1" value="Back"  onclick="pageBack(document.forms[\'FormHandler\']);" />error_button1',
            '<input type="button" name="button2" id="button2" value="BackButton"  onclick="pageBack(document.forms[\'FormHandler\']);" />error_button2',
            '<input type="button" name="backbutton" id="backbutton" value="Back"  onclick="pageBack(document.forms[\'FormHandler\']);" />error_backbutton',
            '<input type="button" name="button3" id="button3" value="Back" data-old="123" onclick="pageBack(document.forms[\'FormHandler\']);" />error_button3'
        ]);
    }
};
