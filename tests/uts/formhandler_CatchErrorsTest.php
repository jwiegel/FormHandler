<?php

declare(strict_types=1);

final class formhandler_CatchErrorsTest extends FormhandlerTestCase
{
    public function test_saves_error(): void
    {
        // the constant E_STRICT is deprecated since PHP 8.4, catchErrors() must not use it
        catchErrors(E_USER_NOTICE, 'notice', 'file.php', 12);

        $errors = catchErrors();

        static::assertCount(1, $errors);
        static::assertEquals(E_USER_NOTICE, $errors[0]['no']);
        static::assertEquals('notice', $errors[0]['text']);
    }

    public function test_ignores_strict(): void
    {
        // 2048 is the value of the former E_STRICT
        catchErrors(2048, 'strict', 'file.php', 12);

        static::assertCount(0, catchErrors());
    }
}
