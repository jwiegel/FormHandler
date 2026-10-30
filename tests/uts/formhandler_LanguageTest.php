<?php

declare(strict_types=1);

/**
 * The language files are UTF-8; FormHandler converts them for pages in another encoding
 */
final class formhandler_LanguageTest extends FormhandlerTestCase
{
    public function test_filesAreUtf8(): void
    {
        foreach (glob(dirname(__DIR__, 2) . '/src/language/*.php') as $file)
        {
            $content = (string) file_get_contents($file);
            static::assertTrue(mb_check_encoding($content, 'UTF-8'), basename($file) . ' is not UTF-8');
            static::assertStringNotContainsString("\u{FFFD}", $content, basename($file) . ' has replacement characters');
        }
    }

    public function test_germanMessage(): void
    {
        $_POST['FormHandler_submit'] = "1";

        $form = new FormHandler();
        $form->setLanguage("de");
        $form->textField("Name", "name", FH_NOT_EMPTY);

        static::assertFormFlushContains($form, ['<span id="error_name" class="error">Der eingegebene Wert ist ungültig!</span>']);
    }

    public function test_otherPageEncoding(): void
    {
        define('FH_HTML_ENCODING', 'ISO-8859-1');
        $_POST['FormHandler_submit'] = "1";

        $form = new FormHandler();
        $form->setLanguage("de");
        $form->textField("Name", "name", FH_NOT_EMPTY);

        // the text is converted for the page
        static::assertFormFlushContains($form, ['Der eingegebene Wert ist ung' . "\xFC" . 'ltig!']);
    }
}
