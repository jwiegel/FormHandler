<?php

declare(strict_types=1);

final class formhandler_AjaxValidatorTest extends FormhandlerTestCase
{
    /**
     * Only one test: AjaxValidator adds its javascript only once per request
     */
    public function test_observers(): void
    {
        $form = new FormHandler();
        $form->enableAjaxValidator();

        $form->textField("Name", "name", FH_STRING);
        $form->textField("City", "city", FH_STRING);
        $form->setErrorMessage("city", "Please enter a city");

        $html = $form->flush(true);

        static::assertStringContainsString("js/ajax_validator.js", $html);
        static::assertStringContainsString("jQuery('#name').live( 'blur', function(){FH_VALIDATE(", $html);
        static::assertStringContainsString("jQuery('#city').live( 'blur', function(){FH_VALIDATE(", $html);
        static::assertStringContainsString("'Please enter a city' )});", $html);
    }
}
