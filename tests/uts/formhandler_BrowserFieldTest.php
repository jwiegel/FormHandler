<?php

declare(strict_types=1);

final class formhandler_BrowserFieldTest extends FormhandlerTestCase
{
    public function test_new(): void
    {
        $form = new FormHandler();

        $form->browserField("BrowserField", "browserfield", "a/path");

        // since 4.7 a text field: the file manager behind the button is removed
        $html = static::assertFormFlushContains($form, [
            'BrowserField:<input type="text" name="browserfield" id="browserfield" value="" size="20" />error_browserfield'
        ]);
        static::assertStringNotContainsString('filemanager', $html);
    }

    public function test_viewmode(): void
    {
        $form = new FormHandler();

        $form->browserField("BrowserField", "browserfield", "a/path");
        $form->setFieldViewMode("browserfield");

        static::assertFormFlushContains($form, [
            'BrowserField:error_browserfield'
        ]);
    }

    public function test_posted(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['browserfield'] = "o1";

        $form = new FormHandler();

        $form->browserField("BrowserField", "browserfield", "a/path");

        static::assertEquals("o1", $form->getValue("browserfield"));

        static::assertTrue($form->isCorrect());
    }


};
