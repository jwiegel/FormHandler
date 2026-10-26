<?php

declare(strict_types=1);

final class formhandler_TextFieldTest extends FormhandlerTestCase
{
    public function test_new(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->textField("Textfield", "textfield");

        static::assertEmpty($form->getValue("textfield"));

        static::assertFormFlushContains($form, ['Textfield:<input type="text" name="textfield" id="textfield" value="" size="20" />error_textfield']);
    }

    public function test_posted(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['textfield'] = "textvalue";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->textField("Textfield", "textfield");

        static::assertEquals("textvalue", $form->getValue("textfield"));
    }

    public function test_posted_fillvalue_byinvalid(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['textfield'] = "textvalue";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->textField("Textfield", "textfield");

        static::assertEquals("textvalue", $form->getValue("textfield"));

        $form->setError("textfield", "forcedError");

        static::assertFormFlushContains($form, [
            'Textfield:<input type="text" name="textfield" id="textfield" value="textvalue" size="20" class="error" />error_textfield',
            '<span id="error_textfield" class="error">forcedError</span>'
        ]);
    }

    public function test_validator(): void
    {
        $_POST['FormHandler_submit'] = "1";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->textField("Textfield", "textfield", FH_NOT_EMPTY);

        static::assertEmpty($form->getValue("textfield"));

        $t = $form->catchErrors(false);

        static::assertEquals(
            '<span id="error_textfield" class="error">You did not enter a correct value for this field!</span>',
            $t['textfield']
        );
    }

    public function test_new_size(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->textField("Textfield", "textfield", null, 123);

        static::assertEmpty($form->getValue("textfield"));

        static::assertFormFlushContains($form, ['Textfield:<input type="text" name="textfield" id="textfield" value="" size="123" />error_textfield']);
    }

    public function test_new_maxlength(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->textField("Textfield", "textfield", null, null, 123);

        static::assertEmpty($form->getValue("textfield"));

        static::assertFormFlushContains($form, ['Textfield:<input type="text" name="textfield" id="textfield" value="" size="20" maxlength="123" />error_textfield']);
    }

    public function test_new_extra(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->textField("Textfield", "textfield", null, null, null, 'data-old="123"');

        static::assertEmpty($form->getValue("textfield"));

        static::assertFormFlushContains($form, ['Textfield:<input type="text" name="textfield" id="textfield" value="" size="20"  data-old="123"']);
    }
};
