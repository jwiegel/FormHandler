<?php

declare(strict_types=1);

final class formhandler_DataHandlingTest extends FormhandlerTestCase
{
    final protected function getFormhandlerType(): string
    {
        return "Formhandler";
    }
    public function testGetAsArray(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['date_year'] = "2020";
        $_POST['date_month'] = "3";
        $_POST['date_day'] = "21";

        $form = new FormHandler();

        $form->dateField('Date', 'date');

        static::assertTrue($form->isPosted());

        list($year, $month, $day) = $form->getAsArray('date');

        static::assertEquals("2020", $year);
        static::assertEquals("3", $month);
        static::assertEquals("21", $day);
    }

    public function testGetAsArray_expectedError(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->getAsArray('date'));

        $this->assertTriggertError('The datefield "date" does not exists!', E_USER_NOTICE);
    }

    public function testGetValue(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['name'] = 'Text';

        $form = new FormHandler();

        $form->textField("Name", "name");

        static::assertTrue($form->isPosted());

        static::assertEquals("Text", $form->getValue("name"));
        static::assertEquals("Text", $form->value("name"));
    }

    public function testSetValue(): void
    {
        $form = new FormHandler();

        $form->textField("Name", "name", FH_STRING);
        $form->setValue("name", "defaultValue");

        static::assertFalse($form->isPosted());

        $expected  = 'value="defaultValue"';

        static::assertStringContainsString($expected, $form->flush(true));
    }

    public function testAddValue(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['name'] = 'Text';

        $form = new FormHandler();

        $form->textField("Name", "name", FH_STRING);

        static::assertTrue($form->isPosted());

        static::assertEquals("Text", $form->getValue("name"));

        $form->addValue("name", "Code");

        $form->onCorrect("doRun_ReturnDataName");

        $expected  = "Code";
        static::assertStringContainsString($expected, $form->flush(true));
    }

    public function testOnCorrect(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['name'] = 'Text';

        $form = new FormHandler();

        $form->textField("Name", "name", FH_STRING);

        $form->onCorrect("doRun_ReturnDataName");

        $expected  = $_POST['name'];
        static::assertEquals($expected, $form->flush(true));
    }

    public function testOnCorrect_Class(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['name'] = 'Text';

        $example = new Example();

        $form = new FormHandler();

        $form->textField("Name", "name");

        // set the method which we should call when the form is saved
        $form->onCorrect(
            // note: the & caracter is needed!
            array(&$example, "doRun")
        );

        $expected  = $_POST['name'];
        static::assertEquals($expected, $form->flush(true));
    }

    public function testOnCorrect_Validation_false(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['name'] = 'Te';

        $form = new FormHandler();

        $form->textField("Name", "name");

        $form->onCorrect("doRun_testOnCorrect_Validation");

        $expected  = '<span id="error_name" class="error">Your name has to be at least 3 characters!</span>';

        static::assertStringContainsString($expected, $form->flush(true));
    }

    public function testOnCorrect_Validation_true(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['name'] = 'Test';

        $form = new FormHandler();

        $form->textField("Name", "name");

        $form->onCorrect("doRun_testOnCorrect_Validation");

        $expected  = $_POST['name'];
        static::assertEquals($expected, $form->flush(true));
    }

    public function testSetError(): void
    {
        $form = new FormHandler();

        $form->textField("Name", "name", FH_STRING);

        $form->setError("name", "This is an error");

        $expected  = '<span id="error_name" class="error">This is an error</span>';
        static::assertStringContainsString($expected, $form->flush(true));
    }
}

function doRun_ReturnDataName($data, &$form): string
{
    return $data["name"];
}

/**** Simple example class!! *******/
class Example
{
    // this is the method we are going to call
    // when the form is correct!
    function doRun($data, &$form): string
    {
        return $data["name"];
    }
}

// the oncorrect function
function doRun_testOnCorrect_Validation(array $data, FormHandler &$form)
{
    // is the name shorter then 3 characters ?
    if (strlen($data['name']) < 3)
    {
        // set an error message for the name field
        $form->setError(
            'name',
            'Your name has to be at least 3 characters!'
        );

        // display the form again
        return false;
    }
    else
    {
        return $data["name"];
    }
}
