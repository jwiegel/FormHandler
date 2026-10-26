<?php

declare(strict_types=1);

final class formhandler_AutocompleteTest extends FormhandlerTestCase
{
    public function test_show(): void
    {
        $form = new FormHandler();

        $form->textField("Textfield", "textfield");

        $aOptions = ["first", "second", "third"];

        $form->setAutoComplete("textfield", $aOptions);

        static::assertFormFlushContains($form, [
            'FHTML/js/autocomplete.js',
            'textfield_values = ["first", "second", "third"];',
            'Textfield:<input type="text" name="textfield" id="textfield" value="" size="20"   onkeypress=\'return FH_autocomplete(this, event, textfield_values);\'  />error_textfield'
        ]);
    }

    public function test_no_textfield(): void
    {
        $form = new FormHandler();

        $aOptions = ["first", "second", "third"];

        $form->setAutoComplete("textfield", $aOptions);

        $this->assertTriggertError('You have to declare the textfield first! The field "textfield" does not exists in the form!', E_USER_WARNING);
    }

    public function test_no_optionarray(): void
    {
        $form = new FormHandler();

        $form->textField("Textfield", "textfield");

        $form->setAutoComplete("textfield", "nooptions");

        $this->assertTriggertError('You have to give an array as options!', E_USER_WARNING);
    }

    public function test_after_show(): void
    {
        $form = new FormHandler();

        $form->textField("Textfield", "textfield");

        $aOptions = ["first", "second", "third"];

        $form->setAutoCompleteAfter("textfield", "@", $aOptions);

        static::assertFormFlushContains($form, [
            'FHTML/js/autocomplete.js',
            'textfield_values = ["first", "second", "third"];',
            'Textfield:<input type="text" name="textfield" id="textfield" value="" size="20"   onkeypress=\'return autocompleteafter(this, event,"@", textfield_values);\'  />error_textfield'
        ]);
    }

    public function test_after_no_textfield(): void
    {
        $form = new FormHandler();

        $aOptions = ["first", "second", "third"];

        $form->setAutoCompleteAfter("textfield", "@", $aOptions);

        $this->assertTriggertError('You have to declare the textfield first! The field "textfield" does not exists in the form!', E_USER_WARNING);
    }

    public function test_after_no_optionarray(): void
    {
        $form = new FormHandler();

        $form->textField("Textfield", "textfield");

        $form->setAutoCompleteAfter("textfield", "@", "nooptions");

        $this->assertTriggertError('You have to give an array as options!', E_USER_WARNING);
    }
};
