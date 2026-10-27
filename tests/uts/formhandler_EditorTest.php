<?php

declare(strict_types=1);

final class formhandler_EditorTest extends FormhandlerTestCase
{
    public function test_new(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->editor("Editor", "editor");

        static::assertEmpty($form->getValue("editor"));

        static::assertFormFlushContains($form, [
            'FHTML/ckeditor/ckeditor.js',
            'Editor:<textarea name="editor" id="editor" cols="40" rows="7"></textarea>error_editor',
            'CKEDITOR.replace( \'editor\', {"toolbar":"Default"',
            '"language":"en","width":720,"height":400,"skin":"moono-lisa"}'
        ]);
    }

    public function test_posted(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['editor'] = "textvalue";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->editor("Editor", "editor");

        static::assertEquals("textvalue", $form->getValue("editor"));
    }

    public function test_posted_fillvalue_byinvalid(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['editor'] = "textvalue";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->editor("Editor", "editor");

        static::assertEquals("textvalue", $form->getValue("editor"));

        $form->setError("editor", "forcedError");

        static::assertFormFlushContains($form, [
            'FHTML/ckeditor/ckeditor.js',
            'Editor:<textarea class="error" name="editor" id="editor" cols="40" rows="7">textvalue</textarea>error_editor',
            'CKEDITOR.replace( \'editor\', {"toolbar":"Default"'
        ]);
    }

    public function test_path(): void
    {
        $form = new FormHandler();

        $form->editor("Editor", "editor", null, "thisisthepath");

        static::assertFormFlushContains($form, [
            'FHTML/ckeditor/ckeditor.js',
            'Editor:<textarea name="editor" id="editor" cols="40" rows="7"></textarea>error_editor',
            'CKEDITOR.replace( \'editor\', {"toolbar":"Default"',
            'thisisthepath","filebrowserUploadUrl"',
            'thisisthepath","language":"en","width":720,"height":400,"skin":"moono-lisa"}'
        ]);
    }

    public function test_path_false(): void
    {
        $form = new FormHandler();

        // false turns the file browser of the editor off
        $form->editor("Editor", "editor", null, false);

        $html = static::assertFormFlushContains($form, ['CKEDITOR.replace( \'editor\', {"toolbar":"Default","language":"en"']);
        static::assertStringNotContainsString('filebrowser', $html);
    }

    public function test_toolbar(): void
    {
        $form = new FormHandler();

        $form->editor("Editor", "editor", null, null, "thetoolbar");

        static::assertFormFlushContains($form, [
            'FHTML/ckeditor/ckeditor.js',
            'Editor:<textarea name="editor" id="editor" cols="40" rows="7"></textarea>error_editor',
            'CKEDITOR.replace( \'editor\', {"toolbar":"thetoolbar"',
            '"language":"en","width":720,"height":400,"skin":"moono-lisa"}'
        ]);
    }

    public function test_skin(): void
    {
        $form = new FormHandler();

        $form->editor("Editor", "editor", null, null, null, "theskin");

        static::assertFormFlushContains($form, [
            'FHTML/ckeditor/ckeditor.js',
            'Editor:<textarea name="editor" id="editor" cols="40" rows="7"></textarea>error_editor',
            'CKEDITOR.replace( \'editor\', {"toolbar":"Default"',
            '"language":"en","width":720,"height":400,"skin":"theskin"}'
        ]);
    }

    public function test_size(): void
    {
        $form = new FormHandler();

        $form->editor("Editor", "editor", null, null, null, null, 123, 456);

        static::assertFormFlushContains($form, [
            'FHTML/ckeditor/ckeditor.js',
            'Editor:<textarea name="editor" id="editor" cols="40" rows="7"></textarea>error_editor',
            'CKEDITOR.replace( \'editor\', {"toolbar":"Default"',
            '"language":"en","width":123,"height":456,"skin":"moono-lisa"}'
        ]);
    }

    public function test_config(): void
    {
        $form = new FormHandler();

        $form->editor("Editor", "editor", null, null, null, null, null, null, ["theconfig" => "vals"]);

        static::assertFormFlushContains($form, [
            'FHTML/ckeditor/ckeditor.js',
            'Editor:<textarea name="editor" id="editor" cols="40" rows="7"></textarea>error_editor',
            'CKEDITOR.replace( \'editor\', {"toolbar":"Default"',
            '"language":"en","width":720,"height":400,"skin":"moono-lisa","theconfig":"vals"}'
        ]);
    }

    public function test_skin_oldMoonoIsMapped(): void
    {
        $form = new FormHandler();

        $form->editor("Editor", "editor", null, null, null, "moono");

        static::assertFormFlushContains($form, ['"skin":"moono-lisa"}']);
    }

    public function test_defaultSkinIsBundled(): void
    {
        static::assertFileExists(__DIR__ . '/../../src/FHTML/ckeditor/skins/moono-lisa/editor.css');
    }

    public function test_editorLanguagesAreBundled(): void
    {
        // The editor uses the form language without "-utf8". CKEditor falls back to English
        // for a language it doesn't know, but fails to load when it knows the language and
        // its file is missing. So every known language must have its file.
        $ckeditor = __DIR__ . '/../../src/FHTML/ckeditor/';
        static::assertSame(1, preg_match('/languages:\{([^}]+)\}/', file_get_contents($ckeditor . 'ckeditor.js'), $match));
        $known = array_map(fn($l) => trim(explode(':', $l)[0], '"'), explode(',', $match[1]));

        foreach (glob(__DIR__ . '/../../src/language/*.php') as $file)
        {
            $language = str_replace('-utf8', '', basename($file, '.php'));
            if (in_array($language, $known, true))
            {
                static::assertFileExists($ckeditor . 'lang/' . $language . '.js', "CKEditor language file for $language");
            }
        }
    }
};
