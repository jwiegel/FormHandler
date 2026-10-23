<?php

declare(strict_types=1);

final class formhandler_CheckPasswordTest extends FormhandlerTestCase
{
    private function createForm(): FormHandler
    {
        $form = new FormHandler();
        $form->passField("Password", "pass");
        $form->passField("Repeat password", "pass2");
        $form->checkPassword("pass", "pass2");

        return $form;
    }

    public function test_new(): void
    {
        $form = $this->createForm();

        static::assertFalse($form->isPosted());
        static::assertFormFlushContains($form, [
            'Password:<input type="password" name="pass" id="pass" size="20" />error_pass',
            'Repeat password:<input type="password" name="pass2" id="pass2" size="20" />error_pass2'
        ]);
    }

    public function test_equal(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['pass'] = "secret1";
        $_POST['pass2'] = "secret1";

        $form = $this->createForm();

        static::assertTrue($form->isCorrect());
        static::assertEquals("secret1", $form->getValue("pass"));
    }

    public function test_notEqual(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['pass'] = "secret1";
        $_POST['pass2'] = "secret2";

        $form = $this->createForm();

        static::assertFalse($form->isCorrect());
        static::assertStringContainsString('The passwords are not the same!', $form->catchErrors()['pass']);
    }

    public function test_empty(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['pass'] = "";
        $_POST['pass2'] = "";

        $form = $this->createForm();

        static::assertFalse($form->isCorrect());
        static::assertStringContainsString('These fields are required!', $form->catchErrors()['pass']);
    }

    public function test_tooShort(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['pass'] = "abc";
        $_POST['pass2'] = "abc";

        $form = $this->createForm();

        static::assertFalse($form->isCorrect());
        static::assertStringContainsString(
            sprintf('The password has to be at least %d characters long!', FH_MIN_PASSWORD_LENGTH),
            $form->catchErrors()['pass']
        );
    }

    public function test_unknownField(): void
    {
        $form = new FormHandler();
        $form->passField("Password", "pass");
        $form->textField("Text", "text");

        $form->checkPassword("pass", "text");

        $this->assertTriggertError('Error: unknown field used in checkPassword!', E_USER_NOTICE);
    }
};
