<?php

declare(strict_types=1);

final class formhandler_AjaxValidatorTest extends FormhandlerTestCase
{
    public function test_observers(): void
    {
        $form = new FormHandler();
        $form->enableAjaxValidator();

        $form->textField("Name", "name", FH_STRING);
        $form->textField("City", "city", FH_STRING);
        $form->setErrorMessage("city", "Please enter a city");

        $html = $form->flush(true);

        static::assertStringContainsString("js/ajax_validator.js", $html);
        static::assertStringNotContainsString("jquery", $html);
        static::assertMatchesRegularExpression('/FH_OBSERVE\("blur", "\w+", "name", "[^"]*", "You did not enter a correct value for this field!"\);/', $html);
        static::assertMatchesRegularExpression('/FH_OBSERVE\("blur", "\w+", "city", "[^"]*", "Please enter a city"\);/', $html);
    }

    public function test_messageInTheScript(): void
    {
        $form = new FormHandler();
        $form->enableAjaxValidator();

        $form->textField("City", "city", FH_STRING);
        $form->setErrorMessage("city", "</script><b>'City'</b>");

        $html = $form->flush(true);

        static::assertStringNotContainsString("</script><b>", $html);
        // the message is a javascript string without < > ' (JSON_HEX_*), the same text
        static::assertMatchesRegularExpression('/FH_OBSERVE\("blur", "\w+", "city", "[^"]*", ("[^"<>\']*")\);/', $html);
        preg_match('/FH_OBSERVE\("blur", "\w+", "city", "[^"]*", ("[^"]*")\);/', $html, $match);
        static::assertSame("</script><b>'City'</b>", json_decode($match[1]));
    }

    public function test_posted(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['name'] = "abc";

        $form = new FormHandler();
        $form->enableAjaxValidator();

        // an invalid value: the form is shown again
        $form->textField("Name", "name", FH_DIGIT);

        // after a post the fields are checked at once
        static::assertMatchesRegularExpression('/FH_CHECK\("\w+", "name", "[^"]*", "You did not enter a correct value for this field!"\);/', $form->flush(true));
    }
}
