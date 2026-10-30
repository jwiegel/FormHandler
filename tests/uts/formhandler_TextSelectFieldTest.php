<?php

declare(strict_types=1);

define('FH_TEXTSELECT_MASK', '%s-%s-%d-%s-%s-%s');
define('FH_TEXTSELECT_OPTION_MASK', '%s-%s');

final class formhandler_TextSelectFieldTest extends FormhandlerTestCase
{
    private $aOptions = [
        "o1" => "Option1",
        "o2" => "Option2",
        "o3" => "Option3"
    ];

    public function test_new(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->textSelectField("Textselectfield", "textselectfield", $this->aOptions);

        static::assertEmpty($form->getValue("textselectfield"));

        // the list comes from autocomplete.js; the old functions only for old own masks
        $html = static::assertFormFlushContains($form, [
            'FHTML/js/autocomplete.js',
            'Textselectfield:textselectfield--20---textselectfield-Option1textselectfield-Option2textselectfield-Option3error_textselectfield'
        ]);
        static::assertStringNotContainsString('FH_SET_TEXTSELECT', $html);
    }

    public function test_escaped(): void
    {
        $form = new FormHandler();

        // the options (e.g. from the database) are escaped as HTML
        $form->textSelectField("Textselectfield", "textselectfield", array("<b>\"x\"</b>"));

        static::assertFormFlushContains($form, ['textselectfield-&lt;b&gt;&quot;x&quot;&lt;/b&gt;error_textselectfield']);
    }

    public function test_posted(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['textselectfield'] = "Option2";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->textSelectField("Textselectfield", "textselectfield", $this->aOptions);

        static::assertEquals("Option2", $form->getValue("textselectfield"));

        static::assertTrue($form->isCorrect());
    }

    public function test_posted_fillvalue_byinvalid(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['textselectfield'] = "Option2";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->textSelectField("Textselectfield", "textselectfield", $this->aOptions);

        static::assertEquals("Option2", $form->getValue("textselectfield"));

        $form->setError("textselectfield", "forcedError");

        static::assertFormFlushContains($form, ['Textselectfield:textselectfield-Option2-20---textselectfield-Option1textselectfield-Option2textselectfield-Option3error_textselectfield<span id="error_textselectfield" class="error">forcedError</span>']);
    }

    public function test_new_size(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->textSelectField("Textselectfield", "textselectfield", $this->aOptions, null, 123);

        static::assertEmpty($form->getValue("textselectfield"));

        static::assertFormFlushContains($form, ['Textselectfield:textselectfield--123---textselectfield-Option1textselectfield-Option2textselectfield-Option3error_textselectfield']);
    }

    public function test_new_maxlenght(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->textSelectField("Textselectfield", "textselectfield", $this->aOptions, null, null, 456);

        static::assertEmpty($form->getValue("textselectfield"));

        static::assertFormFlushContains($form, ['Textselectfield:textselectfield--20-maxlength="456" --textselectfield-Option1textselectfield-Option2textselectfield-Option3error_textselectfield']);
    }

    public function test_new_extra(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->textSelectField("Textselectfield", "textselectfield", $this->aOptions, null, null, null, 'data-old="123"');

        static::assertEmpty($form->getValue("textselectfield"));

        static::assertFormFlushContains($form, ['Textselectfield:textselectfield--20- data-old="123" --textselectfield-Option1textselectfield-Option2textselectfield-Option3error_textselectfield']);
    }
};
