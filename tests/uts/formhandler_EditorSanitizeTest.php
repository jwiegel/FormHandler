<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * editor with the style attribute allowed, like a project would configure it
 */
final class EditorWithStyle extends Editor
{
    protected function isStyleAllowed()
    {
        return true;
    }
}

final class formhandler_EditorSanitizeTest extends FormhandlerTestCase
{
    public static function dataXss(): array
    {
        return [
            'event handler'     => ['<p onmouseover="alert(1)">Hallo</p>', '<p>Hallo</p>'],
            'script'            => ['<p>a</p><script>alert(2)</script>', '<p>a</p>'],
            'img onerror'       => ['<img src="x" onerror="alert(3)">', '<img src="x" />'],
            'javascript link'   => ['<a href="javascript:alert(4)">js</a>', '<a>js</a>'],
            'mixed case scheme' => ['<a href="JaVaScRiPt:alert(5)">js</a>', '<a>js</a>'],
            'data link'         => ['<a href="data:text/html,<script>alert(6)</script>">x</a>', '<a>x</a>'],
            'iframe'            => ['<iframe src="https://example.org"></iframe>', ''],
            'svg'               => ['<svg onload="alert(7)"></svg>', ''],
            'form'              => ['<form action="https://example.org"><input name="x"></form>', ''],
            'style element'     => ['<style>body{display:none}</style><p>a</p>', '<p>a</p>'],
            'style attribute'   => ['<p style="color:red">a</p>', '<p>a</p>'],
        ];
    }

    #[DataProvider('dataXss')]
    public function test_posted_xss(string $posted, string $expected): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['editor'] = $posted;

        $form = new FormHandler();
        $form->editor("Editor", "editor");

        static::assertSame($expected, $form->getValue("editor"));
    }

    public function test_keepsEditorContent(): void
    {
        $html = '<h2>Titel</h2><p><strong>fett</strong> <em>kursiv</em> <u>unter</u> <s>durch</s> x<sub>2</sub></p>'
            . '<ol><li>eins</li></ol><ul><li>zwei</li></ul><blockquote><p>Zitat</p></blockquote>'
            . '<p><a href="https://example.org" target="_blank" title="t">Link</a> <a href="/intern.php">relativ</a></p>'
            . '<p><img alt="Bild" src="/bilder/x.jpg" /></p>'
            . '<table><caption>Tab</caption><tbody><tr><th scope="col">A</th><td colspan="2">1</td></tr></tbody></table><hr />';

        $_POST['FormHandler_submit'] = "1";
        $_POST['editor'] = $html;

        $form = new FormHandler();
        $form->editor("Editor", "editor");

        static::assertSame($html, $form->getValue("editor"));
    }

    public function test_longText(): void
    {
        // the sanitizer cuts the input to 20000 chars by default
        $html = str_repeat('<p>Ein langer Spielbericht.</p>', 2000);

        $_POST['FormHandler_submit'] = "1";
        $_POST['editor'] = $html;

        $form = new FormHandler();
        $form->editor("Editor", "editor");

        static::assertSame($html, $form->getValue("editor"));
    }

    public function test_setValue(): void
    {
        $form = new FormHandler();
        $form->editor("Editor", "editor");

        $form->setValue("editor", '<p onclick="alert(1)">a</p>');

        static::assertSame('<p>a</p>', $form->getValue("editor"));
    }

    public function test_viewMode(): void
    {
        $form = new FormHandler();
        $form->editor("Editor", "editor");
        $form->setValue("editor", '<p>sicher</p><script>alert(1)</script>');
        $form->enableViewMode();

        $html = static::assertFormFlushContains($form, ['Editor:<p>sicher</p>']);
        static::assertStringNotContainsString('alert(1)', $html);
    }

    public static function dataStyle(): array
    {
        return [
            'color'          => ['<p style="color:#ff0000">a</p>', '<p style="color:#ff0000">a</p>'],
            'font and align' => ['<p style="font-size:18px; text-align:center">a</p>', '<p style="font-size:18px; text-align:center">a</p>'],
            'url'            => ['<p style="background:url(https://example.org/x.png)">a</p>', '<p>a</p>'],
            'javascript url' => ['<p style="background:url(javascript:alert(1))">a</p>', '<p>a</p>'],
            'expression'     => ['<p style="width:expression(alert(1))">a</p>', '<p>a</p>'],
            'import'         => ['<p style="@import \'x.css\'">a</p>', '<p>a</p>'],
            'css escape'     => ['<p style="background:u\\72l(x)">a</p>', '<p>a</p>'],
            'event handler'  => ['<p style="color:red" onclick="alert(1)">a</p>', '<p style="color:red">a</p>'],
        ];
    }

    #[DataProvider('dataStyle')]
    public function test_styleAllowed(string $posted, string $expected): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['editor'] = $posted;

        $form = new FormHandler();
        $field = new EditorWithStyle($form, "editor");

        static::assertSame($expected, $field->getValue());
    }
}
