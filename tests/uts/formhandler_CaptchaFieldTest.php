<?php

declare(strict_types=1);

final class formhandler_CaptchaFieldTest extends FormhandlerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // start the session first: session_start() replaces $_SESSION
        if (session_status() === PHP_SESSION_NONE)
        {
            session_start();
        }
        unset($_SESSION['FH_CAPTCHA'], $_SESSION['FH_CAPTCHA_POSTED']);
    }

    public function test_new(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->CaptchaField("Captchafield", "captchafield");

        static::assertEmpty($form->getValue("captchafield"));
        static::assertMatchesRegularExpression('/^[a-zA-Z1-9]{6}$/', $_SESSION['FH_CAPTCHA'], 'FH_CAPTCHA_LENGTH');

        static::assertFormFlushContains($form, [
            '<input type="image" src="data:image/jpeg;base64,',
            'name="button1" id="button1" onclick="return false;" style="cursor:default;" />error_button1Captchafield:<input type="text" name="captchafield" id="captchafield" value="" size="20" />error_captchafield'
        ]);
    }

    public function test_posted(): void
    {
        $_SESSION['FH_CAPTCHA'] = "aBc4eF";
        $_POST['FormHandler_submit'] = "1";
        $_POST['captchafield'] = "textvalue";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->CaptchaField("Captchafield", "captchafield");

        static::assertFalse($form->isCorrect());

        $t = $form->catchErrors(false);

        static::assertEquals(
            '<span id="error_captchafield" class="error">You did not enter a correct value for this field!</span>',
            $t['captchafield']
        );
    }

    public function test_posted_correct(): void
    {
        $_SESSION['FH_CAPTCHA'] = "aBc4eF";
        $_POST['FormHandler_submit'] = "1";
        $_POST['captchafield'] = "ABC4EF";

        $form = new FormHandler();
        $form->CaptchaField("Captchafield", "captchafield");

        static::assertTrue($form->isCorrect(), 'the code is not case sensitive');

        // the next request needs the code of the new image
        static::assertNotEquals("aBc4eF", $_SESSION['FH_CAPTCHA']);
    }

    public function test_posted_withoutImage(): void
    {
        // no image was shown in this session (or it expired)
        $_POST['FormHandler_submit'] = "1";
        $_POST['captchafield'] = "";

        $form = new FormHandler();
        $form->CaptchaField("Captchafield", "captchafield");

        static::assertFalse($form->isCorrect());
    }
}
