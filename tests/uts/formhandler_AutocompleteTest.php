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

        // autocomplete.js shows the options of the datalist (without javascript: the browser)
        static::assertFormFlushContains($form, [
            'FHTML/js/autocomplete.js',
            'Textfield:<input type="text" name="textfield" id="textfield" value="" size="20"   list="textfield_list" />' .
                '<datalist id="textfield_list" data-fh-autocomplete><option value="first"></option><option value="second"></option><option value="third"></option></datalist>error_textfield'
        ]);
    }

    public function test_escaped(): void
    {
        $form = new FormHandler();

        $form->textField("Textfield", "textfield");
        $form->setAutoComplete("textfield", ["O'Hare \"x\" <b>"]);

        static::assertFormFlushContains($form, ['<option value="O&#039;Hare &quot;x&quot; &lt;b&gt;"></option>']);
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

        // autocomplete.js suggests the text up to "@" followed by the options
        static::assertFormFlushContains($form, [
            'FHTML/js/autocomplete.js',
            'Textfield:<input type="text" name="textfield" id="textfield" value="" size="20"   list="textfield_list" />' .
                '<datalist id="textfield_list" data-fh-autocomplete data-fh-after="@" data-fh-values="[&quot;first&quot;,&quot;second&quot;,&quot;third&quot;]"></datalist>error_textfield'
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
