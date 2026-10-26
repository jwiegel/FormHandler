<?php

declare(strict_types=1);

final class formhandler_RadiobuttonTest extends FormhandlerTestCase
{
    public function test_new(): void
    {
        $aRadiobuttons = array(
            "a" => "Button1",
            "b" => "Button2",
            "c" => "Button3"
        );

        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->radioButton("Radiobutton", "radiobutton", $aRadiobuttons);

        static::assertEmpty($form->getValue("radiobutton"));

        static::assertFormFlushContains($form, [
            'Radiobutton:<input type="radio" name="radiobutton" id="radiobutton_1" value="a" /><label for="radiobutton_1" class="noStyle">Button1</label>',
            '<input type="radio" name="radiobutton" id="radiobutton_2" value="b" /><label for="radiobutton_2" class="noStyle">Button2</label>',
            '<input type="radio" name="radiobutton" id="radiobutton_3" value="c" /><label for="radiobutton_3" class="noStyle">Button3</label>',
            'error_radiobutton'
        ]);
    }
    public function test_new_defaultValue(): void
    {
        $aRadiobuttons = array(
            "a" => "Button1",
            "b" => "Button2",
            "c" => "Button3"
        );

        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->radioButton("Radiobutton", "radiobutton", $aRadiobuttons);
        $form->setValue("radiobutton", "c");

        static::assertFormFlushContains($form, [
            'Radiobutton:<input type="radio" name="radiobutton" id="radiobutton_1" value="a" /><label for="radiobutton_1" class="noStyle">Button1</label>',
            '<input type="radio" name="radiobutton" id="radiobutton_2" value="b" /><label for="radiobutton_2" class="noStyle">Button2</label>',
            '<input type="radio" name="radiobutton" id="radiobutton_3" value="c" checked="checked" /><label for="radiobutton_3" class="noStyle">Button3</label>',
            'error_radiobutton'
        ]);
    }

    public function test_new_useArrayKeyAsValueFalse(): void
    {
        $aRadiobuttons = array(
            "a" => "Button1",
            "b" => "Button2",
            "c" => "Button3"
        );

        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->radioButton("Radiobutton", "radiobutton", $aRadiobuttons, null, false);
        $form->setValue("radiobutton", "Button2");

        static::assertFormFlushContains($form, [
            'Radiobutton:<input type="radio" name="radiobutton" id="radiobutton_1" value="Button1" /><label for="radiobutton_1" class="noStyle">Button1</label>',
            '<input type="radio" name="radiobutton" id="radiobutton_2" value="Button2" checked="checked" /><label for="radiobutton_2" class="noStyle">Button2</label>',
            '<input type="radio" name="radiobutton" id="radiobutton_3" value="Button3" /><label for="radiobutton_3" class="noStyle">Button3</label>',
            'error_radiobutton'
        ]);
    }

    public function test_new_useArrayKeyAsValueFalse_defaultValue(): void
    {
        $aRadiobuttons = array(
            "a" => "Button1",
            "b" => "Button2",
            "c" => "Button3"
        );

        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->radioButton("Radiobutton", "radiobutton", $aRadiobuttons);
        $form->setValue("radiobutton", "c");

        static::assertFormFlushContains($form, [
            'Radiobutton:<input type="radio" name="radiobutton" id="radiobutton_1" value="a" /><label for="radiobutton_1" class="noStyle">Button1</label>',
            '<input type="radio" name="radiobutton" id="radiobutton_2" value="b" /><label for="radiobutton_2" class="noStyle">Button2</label>',
            '<input type="radio" name="radiobutton" id="radiobutton_3" value="c" checked="checked" /><label for="radiobutton_3" class="noStyle">Button3</label>',
            'error_radiobutton'
        ]);
    }


    public function test_new_extra(): void
    {
        $aRadiobuttons = array(
            "a" => "Button1",
            "b" => "Button2",
            "c" => "Button3"
        );

        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->radioButton("Radiobutton", "radiobutton", $aRadiobuttons, null, null, 'data-extra="true"');

        static::assertEmpty($form->getValue("radiobutton"));

        static::assertFormFlushContains($form, [
            'Radiobutton:<input type="radio" name="radiobutton" id="radiobutton_1" value="a" data-extra="true" /><label for="radiobutton_1" class="noStyle">Button1</label>',
            '<input type="radio" name="radiobutton" id="radiobutton_2" value="b" data-extra="true" /><label for="radiobutton_2" class="noStyle">Button2</label>',
            '<input type="radio" name="radiobutton" id="radiobutton_3" value="c" data-extra="true" /><label for="radiobutton_3" class="noStyle">Button3</label>',
            'error_radiobutton'
        ]);
    }

    public function test_new_mask(): void
    {
        $aRadiobuttons = array(
            "a" => "Button1",
            "b" => "Button2",
            "c" => "Button3"
        );

        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->radioButton("Radiobutton", "radiobutton", $aRadiobuttons, null, null, null, "%field%ABC");

        static::assertEmpty($form->getValue("radiobutton"));

        static::assertFormFlushContains($form, [
            'Radiobutton:<input type="radio" name="radiobutton" id="radiobutton_1" value="a" /><label for="radiobutton_1" class="noStyle">Button1</label>ABC',
            '<input type="radio" name="radiobutton" id="radiobutton_2" value="b" /><label for="radiobutton_2" class="noStyle">Button2</label>ABC',
            '<input type="radio" name="radiobutton" id="radiobutton_3" value="c" /><label for="radiobutton_3" class="noStyle">Button3</label>ABC',
            'error_radiobutton'
        ]);
    }

    public function test_posted(): void
    {
        $aRadiobuttons = array(
            "a" => "Button1",
            "b" => "Button2",
            "c" => "Button3"
        );

        $_POST['FormHandler_submit'] = "1";
        $_POST['radiobutton'] = "b";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->radioButton("Radiobutton", "radiobutton", $aRadiobuttons);

        static::assertEquals("b", $form->getValue("radiobutton"));
    }

    public function test_posted_fillvalue_byinvalid(): void
    {
        $aRadiobuttons = array(
            "a" => "Button1",
            "b" => "Button2",
            "c" => "Button3"
        );

        $_POST['FormHandler_submit'] = "1";
        $_POST['radiobutton'] = "b";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->radioButton("Radiobutton", "radiobutton", $aRadiobuttons);

        static::assertEquals("b", $form->getValue("radiobutton"));

        $form->setError("radiobutton", "forcedError");
        static::assertFormFlushContains($form, [
            'Radiobutton:<input type="radio" name="radiobutton" id="radiobutton_1" value="a" /><label for="radiobutton_1" class="error noStyle">Button1</label>',
            '<input type="radio" name="radiobutton" id="radiobutton_2" value="b" checked="checked" /><label for="radiobutton_2" class="error noStyle">Button2</label>',
            '<input type="radio" name="radiobutton" id="radiobutton_3" value="c" /><label for="radiobutton_3" class="error noStyle">Button3</label>',
            'error_radiobutton<span id="error_radiobutton" class="error">forcedError</span>'
        ]);
    }

    public function test_posted_fillvalue_byinvalid_useArrayKeyAsValueFalse(): void
    {
        $aRadiobuttons = array(
            "a" => "Button1",
            "b" => "Button2",
            "c" => "Button3"
        );

        $_POST['FormHandler_submit'] = "1";
        $_POST['radiobutton'] = "Button2";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->radioButton("Radiobutton", "radiobutton", $aRadiobuttons, null, false);

        static::assertEquals("Button2", $form->getValue("radiobutton"));

        $form->setError("radiobutton", "forcedError");
        static::assertFormFlushContains($form, [
            'Radiobutton:<input type="radio" name="radiobutton" id="radiobutton_1" value="Button1" /><label for="radiobutton_1" class="error noStyle">Button1</label>',
            '<input type="radio" name="radiobutton" id="radiobutton_2" value="Button2" checked="checked" /><label for="radiobutton_2" class="error noStyle">Button2</label>',
            '<input type="radio" name="radiobutton" id="radiobutton_3" value="Button3" /><label for="radiobutton_3" class="error noStyle">Button3</label>',
            'error_radiobutton<span id="error_radiobutton" class="error">forcedError</span>'
        ]);
    }

    public function test_validator(): void
    {
        $aRadiobuttons = array(
            "a" => "Button1",
            "b" => "Button2",
            "c" => "Button3"
        );

        $_POST['FormHandler_submit'] = "1";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->radioButton("Radiobutton", "radiobutton", $aRadiobuttons, FH_NOT_EMPTY);

        static::assertEmpty($form->getValue("radiobutton"));

        $t = $form->catchErrors(false);

        static::assertEquals(
            '<span id="error_radiobutton" class="error">You did not enter a correct value for this field!</span>',
            $t['radiobutton']
        );
    }

    public function test_expected_error_options_no_array(): void
    {
        $form = new FormHandler();

        $form->radioButton("Radiobutton", "radiobutton", "noarray");
        $this->assertTriggertError("You have to give an array as value with the radiobutton 'radiobutton'", E_USER_WARNING);
    }
};
