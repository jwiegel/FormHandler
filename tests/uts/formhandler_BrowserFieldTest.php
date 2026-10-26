<?php

declare(strict_types=1);

final class formhandler_BrowserFieldTest extends FormhandlerTestCase
{
    public function test_new(): void
    {
        $form = new FormHandler();

        $form->browserField("BrowserField", "browserfield", "a/path");

        static::assertFormFlushContains($form, [
            'BrowserField:<input type="text" name="browserfield" id="browserfield" value="" size="20" /> <input type="button" name="Bladeren" id="Bladeren" value="Bladeren"',
            'onclick="window.open(',
            'FHTML/filemanager/browser/default/browser.html?Type=File&naam=browserfield&Connector=../../connectors/php/connector.php?ServerPath=a/path\',\'\',\'modal=yes,width=650,height=400\');" /> error_browserfield'
        ]);
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
