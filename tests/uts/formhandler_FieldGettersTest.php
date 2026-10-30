<?php

declare(strict_types=1);

/**
 * The getters which replace the public properties of the fields (protected in 5.0)
 */
final class formhandler_FieldGettersTest extends FormhandlerTestCase
{
    public function test_getField(): void
    {
        $form = new FormHandler();
        $form->textField("Name", "name");

        static::assertInstanceOf(TextField::class, $form->getField("name"));

        $this->expectException(FormHandlerException::class);
        $form->getField("nothing");
    }

    public function test_errorMessage(): void
    {
        $form = new FormHandler();
        $form->textField("Name", "name");

        $field = $form->getField("name");
        static::assertNull($field->getErrorMessage());

        $form->setError("name", "Wrong name");

        // without the FH_ERROR_MASK of getError()
        static::assertSame("Wrong name", $field->getErrorMessage());
        static::assertStringContainsString("Wrong name</span>", $field->getError());
    }

    public function test_options(): void
    {
        $form = new FormHandler();
        $form->selectField("Select", "select", array("a" => "A", "b" => "B"));
        $form->radioButton("Radio", "radio", array(1 => "One", 2 => "Two"));

        static::assertSame(array("a" => "A", "b" => "B"), $form->getField("select")->getOptions());
        static::assertSame(array(1 => "One", 2 => "Two"), $form->getField("radio")->getOptions());
    }

    public function test_timeField(): void
    {
        $form = new FormHandler();
        $form->timeField("Time", "time");
        $form->setValue("time", "14:30");

        $field = $form->getField("time");
        static::assertInstanceOf(SelectField::class, $field->getHourField());
        static::assertEquals("14", $field->getHourField()->getValue());
        static::assertEquals("30", $field->getMinuteField()->getValue());
    }
}
