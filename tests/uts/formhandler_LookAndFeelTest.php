<?php

declare(strict_types=1);

final class formhandler_LookAndFeelTest extends FormhandlerTestCase
{
    public function test_addHTML(): void
    {
        $form = new FormHandler();

        $form->addHTML(
            "  <tr>" .
                "    <td colspan='3'><hr size='1' /></td>" .
                "  </tr>"
        );

        static::assertFormFlushContains($form, "<td colspan='3'><hr size='1' /></td>");
    }

    public function test_addLine(): void
    {
        $form = new FormHandler();

        $form->addLine();
        $form->addLine("theline");

        static::assertFormFlushContains($form, [
            "<tr><td>&nbsp;</td><td>&nbsp;</td><td></td></tr>",
            "<tr><td>&nbsp;</td><td>&nbsp;</td><td>theline</td></tr>"
        ]);
    }

    public function test_useTable(): void
    {
        $form = new FormHandler();

        $form->useTable(false);

        $t = (string)$form->flush(true);

        static::assertNotEquals("table", $t);
    }

    public function test_setMask(): void
    {
        $form = new FormHandler();

        $form->setMask("%title%||%seperator%||%field%||%name%||%help%||%error_id%||%error%", false);
        $form->textField("Textfield", "textfield");
        $form->setValue("textfield", "value");
        $form->textField("Textfield2", "textfield2");
        $form->setValue("textfield2", "value2");

        $form->setMask("%title%--%seperator%--%field%--%name--%help%--%error_id%--%error%", true);
        $form->textField("Textfield3", "textfield3");
        $form->setValue("textfield3", "value3");
        $form->textField("Textfield4", "textfield4");
        $form->setValue("textfield4", "value4");

        static::assertFormFlushContains($form, [
            'Textfield||:||<input type="text" name="textfield" id="textfield" value="value" size="20" />||textfield||||error_textfield||',
            'Textfield2:<input type="text" name="textfield2" id="textfield2" value="value2" size="20" />error_textfield2',
            'Textfield3--:--<input type="text" name="textfield3" id="textfield3" value="value3" size="20" />--%name----error_textfield3--',
            'Textfield4--:--<input type="text" name="textfield4" id="textfield4" value="value4" size="20" />--%name----error_textfield4'
        ]);
    }

    public function test_setErrorMessage(): void
    {
        $form = new FormHandler();

        $form->textField("Textfield", "textfield");
        $form->setErrorMessage("textfield", "this is an individual errormessage");
        $form->setError("textfield", "error");
        $form->textField("Textfield2", "textfield2");
        $form->setErrorMessage("textfield2", "this is another individual errormessage", false);
        $form->setError("textfield2", "error2");

        static::assertFormFlushContains($form, [
            'Textfield:<input type="text" name="textfield" id="textfield" value="" size="20" />error_textfield<span id="error_textfield" class="error">this is an individual errormessage</span>',
            'Textfield2:<input type="text" name="textfield2" id="textfield2" value="" size="20" />error_textfield2this is another individual errormessage'
        ]);
    }

    public function test_setLanguage(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['textfield'] = "textvalue";

        $form = new FormHandler();
        $form->setLanguage('nl');

        static::assertTrue($form->isPosted());

        $form->textField("Textfield", "textfield", FH_DIGIT);

        static::assertFormFlushContains($form, ['De opgegeven waarde is ongeldig!']);
    }

    public function test_catchErrors(): void
    {
        define('FH_ERROR_MASK', '%s-%s');
        $_POST['FormHandler_submit'] = "1";
        $_POST['textfield'] = "textvalue";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->textField("Textfield", "textfield", FH_DIGIT);

        $errors = $form->catchErrors();

        static::assertEquals('textfield-You did not enter a correct value for this field!', $errors['textfield']);

        $t = (string)$form->flush(true);

        static::assertFalse(strpos($t, "You did not enter a correct value for this field!"));
    }

    public function test_catchErrors2(): void
    {
        define('FH_ERROR_MASK', '%s-%s');
        $_POST['FormHandler_submit'] = "1";
        $_POST['textfield'] = "textvalue";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->textField("Textfield", "textfield", FH_DIGIT);

        $errors = $form->catchErrors(true);

        static::assertEquals('textfield-You did not enter a correct value for this field!', $errors['textfield']);

        static::assertFormFlushContains($form, ['Textfield:<input type="text" name="textfield" id="textfield" value="textvalue" size="20" class="error" />error_textfieldtextfield-You did not enter a correct value for this field!']);
    }

    public function test_setTableSettings(): void
    {
        $form = new FormHandler();

        $form->setTableSettings(400, 2, 2, 0, " style='border: 1px solid green'");

        $form->textField("Textfield", "textfield", FH_DIGIT);

        static::assertFormFlushContains($form, ["<table border='0' cellspacing='2' cellpadding='2' width='400'  style='border: 1px solid green'>"]);
    }
};
