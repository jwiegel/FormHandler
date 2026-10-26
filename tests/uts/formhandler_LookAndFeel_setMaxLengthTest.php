<?php

declare(strict_types=1);

final class formhandler_LookAndFeel_setMaxLengthTest extends FormhandlerTestCase
{
    public function test(): void
    {
        $form = new FormHandler();

        $form->textArea("Textarea", "textarea");
        $form->setMaxLength("textarea", 123);

        static::assertFormFlushContains($form, [
            'FHTML/js/maxlength.js',
            'Textarea:<textarea name="textarea" id="textarea" cols="40" rows="7"',
            'onkeyup="displayLimit(\'FormHandler\', \'textarea\', 123, true, \'&lt;b&gt;%d&lt;/b&gt; characters remaining on your input limit\');">'
        ]);
    }

    public function test_noDisplayMessage(): void
    {
        $form = new FormHandler();

        $form->textArea("Textarea", "textarea");
        $form->setMaxLength("textarea", 123, false);

        static::assertFormFlushContains($form, [
            'FHTML/js/maxlength.js',
            'Textarea:<textarea name="textarea" id="textarea" cols="40" rows="7"',
            'onkeyup="displayLimit(\'FormHandler\', \'textarea\', 123, false, \'&lt;b&gt;%d&lt;/b&gt; characters remaining on your input limit\');">'
        ]);
    }

    public function test_noField(): void
    {
        $form = new FormHandler();

        $form->setMaxLength("textarea", 123);
        $this->assertTriggertError('You have to declare the textarea first! The field "textarea" does not exists in the form!', E_USER_WARNING);
    }

    public function test_wrongField(): void
    {
        $form = new FormHandler();

        $form->textField("Textfield", "textfield");

        $form->setMaxLength("textarea", 123);
        $this->assertTriggertError('You have to declare the textarea first! The field "textarea" does not exists in the form!', E_USER_WARNING);
    }
};
