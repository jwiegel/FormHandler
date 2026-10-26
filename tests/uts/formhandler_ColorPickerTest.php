<?php

declare(strict_types=1);

final class formhandler_ColorPickerTest extends FormhandlerTestCase
{
    public function test_new(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->colorPicker("Colorpicker", "colorpicker");

        static::assertEmpty($form->getValue("colorpicker"));

        static::assertFormFlushContains($form, [
            'FHTML/js/jscolor/jscolor.js',
            'Colorpicker:<input type="text" name="colorpicker" id="colorpicker" value="" size="20"  class="color" />error_colorpicker'
        ]);
    }

    public function test_viewMode(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->colorPicker("Colorpicker", "colorpicker");

        $form->setFieldViewMode("colorpicker");

        static::assertFormFlushContains($form, [
            'FHTML/js/jscolor/jscolor.js',
            'Colorpicker:error_colorpicker'
        ]);
    }

    public function test_extraClass(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->colorPicker("Colorpicker", "colorpicker", null, null, null, "class=\"dummy\"");

        static::assertFormFlushContains($form, [
            'FHTML/js/jscolor/jscolor.js',
            'Colorpicker:<input type="text" name="colorpicker" id="colorpicker" value="" size="20"  class="color dummy" />error_colorpicker'
        ]);
    }

    public function test_posted(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['colorpicker'] = "FFCC00";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->colorPicker("Colorpicker", "colorpicker");

        static::assertEquals("FFCC00", $form->getValue("colorpicker"));
    }

    public function test_posted_fillvalue_byinvalid(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['colorpicker'] = "FFCC00";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->colorPicker("Colorpicker", "colorpicker");

        static::assertEquals("FFCC00", $form->getValue("colorpicker"));

        $form->setError("colorpicker", "forcedError");

        static::assertFormFlushContains($form, [
            'Colorpicker:<input type="text" name="colorpicker" id="colorpicker" value="FFCC00" size="20"  class="error color" />error_colorpicker',
            '<span id="error_colorpicker" class="error">forcedError</span>'
        ]);
    }
};
