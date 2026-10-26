<?php

declare(strict_types=1);

final class formhandler_ViewModeTest extends FormhandlerTestCase
{
    public function test(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['textfield'] = "textvalue";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->textField("Textfield", "textfield");
        $form->textField("Textfield2", "textfield2");

        static::assertFalse($form->isViewMode());

        $form->enableViewMode();

        static::assertTrue($form->isViewMode());

        static::assertFormFlushContains($form, ['Textfield:textvalueerror_textfieldTextfield2:error_textfield2']);
    }

    public function test_fieldViewMode(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['textfield'] = "textvalue";
        $_POST['textfield2'] = "textvalue2";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->textField("Textfield", "textfield");
        $form->textField("Textfield2", "textfield2");

        static::assertFalse($form->isFieldViewMode("textfield"));
        static::assertFalse($form->isFieldViewMode("textfield2"));

        $form->setFieldViewMode("textfield");

        static::assertTrue($form->isFieldViewMode("textfield"));
        static::assertFalse($form->isFieldViewMode("textfield2"));

        $form->setError("textfield", "forcedError");

        static::assertFormFlushContains($form, [
            'Textfield:textvalueerror_textfield<span id="error_textfield" class="error">forcedError</span>',
            'Textfield2:<input type="text" name="textfield2" id="textfield2" value="textvalue2" size="20" />error_textfield2'
        ]);
    }

    public function test_fieldViewMode_error_fieldNotExist(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['textfield'] = "textvalue";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->textField("Textfield", "textfield");

        $form->setFieldViewMode("textfield2");

        $this->assertTriggertError('Error, could not find field "textfield2"! Please define the field first!', E_USER_NOTICE);
    }

    public function test_fieldViewMode_error_NotAField(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['textfield'] = "textvalue";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->textField("Textfield", "textfield");

        $form->isFieldViewMode(0);

        $this->assertTriggertError('Error, could not find field "0"! Please define the field first!', E_USER_NOTICE);
    }
};
