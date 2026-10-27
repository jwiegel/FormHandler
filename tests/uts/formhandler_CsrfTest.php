<?php

declare(strict_types=1);

/**
 * onCorrect callback of test_onCorrectGetsNoToken (FormHandler takes no closures)
 */
function csrfTest_onCorrect(array $data): bool
{
    $GLOBALS['csrfTest_data'] = $data;
    return true;
}

/**
 * The protection against cross-site request forgery. The other tests turn it off
 * with FH_CSRF_PROTECTION (phpunit-prepend.php), so every test here turns it on.
 */
final class formhandler_CsrfTest extends FormhandlerTestCase
{
    private const TOKEN = 'a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3f4a5b6c7d8e9f0a1b2';

    protected function setUp(): void
    {
        parent::setUp();

        // start the session first: session_start() replaces $_SESSION
        if (session_status() === PHP_SESSION_NONE)
        {
            session_start();
        }
        $_SESSION['FH_CSRF_TOKEN'] = self::TOKEN;
    }

    private function postedForm(?string $token): FormHandler
    {
        $_POST['FormHandler_submit'] = '1';
        $_POST['name'] = 'Anna';
        if ($token !== null)
        {
            $_POST['FormHandler_csrf'] = $token;
        }

        $form = new FormHandler();
        $form->setCsrfProtection(true);
        $form->textField('Name', 'name');

        return $form;
    }

    public function test_tokenInForm(): void
    {
        $form = new FormHandler();
        $form->setCsrfProtection(true);
        $form->textField('Name', 'name');

        static::assertSame(self::TOKEN, $form->getCsrfToken());
        static::assertFormFlushContains($form, ['name="FormHandler_csrf" id="FormHandler_csrf" value="' . self::TOKEN . '"']);
    }

    public function test_newTokenPerSession(): void
    {
        unset($_SESSION['FH_CSRF_TOKEN']);

        $form = new FormHandler();
        $form->setCsrfProtection(true);

        static::assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $form->getCsrfToken());
        static::assertSame($_SESSION['FH_CSRF_TOKEN'], $form->getCsrfToken());
    }

    public function test_postedWithToken(): void
    {
        $form = $this->postedForm(self::TOKEN);

        static::assertTrue($form->isCorrect());
    }

    public function test_postedWithoutToken(): void
    {
        $form = $this->postedForm(null);

        static::assertFalse($form->isCorrect());
        static::assertArrayHasKey('FormHandler_csrf', $form->catchErrors());
    }

    public function test_postedWithWrongToken(): void
    {
        $form = $this->postedForm(str_repeat('0', 64));

        static::assertFalse($form->isCorrect());

        // the form is shown again with the token of the session (not the posted one) and the
        // message, which FormHandler displays behind the hidden field
        $html = static::assertFormFlushContains($form, [
            'value="' . self::TOKEN . '"',
            'The form has expired or was not sent from this page. Please submit it again.',
        ]);
        static::assertStringNotContainsString(str_repeat('0', 64), $html);
    }

    public function test_postedWithTokenArray(): void
    {
        $_POST['FormHandler_csrf'] = array(self::TOKEN);
        $form = $this->postedForm(null);

        static::assertFalse($form->isCorrect());
    }

    public function test_onCorrectGetsNoToken(): void
    {
        $form = $this->postedForm(self::TOKEN);

        $form->onCorrect('csrfTest_onCorrect');
        $form->flush(true);

        static::assertSame(array('name' => 'Anna'), $GLOBALS['csrfTest_data']);
    }

    public function test_turnedOff(): void
    {
        $form = new FormHandler();
        $form->setCsrfProtection(true);
        $form->setCsrfProtection(false);

        static::assertNull($form->getCsrfToken());
        static::assertStringNotContainsString('FormHandler_csrf', (string) $form->flush(true));
    }
}
