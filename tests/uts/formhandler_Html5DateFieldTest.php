<?php

declare(strict_types=1);

final class formhandler_Html5DateFieldTest extends FormhandlerTestCase
{
    public function test_new(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->html5DateField("Date", "date");

        static::assertSame("", $form->getValue("date"));

        static::assertFormFlushContains($form, ['Date:<input type="date" name="date" id="date" value="" size="20" />error_date']);
    }

    public function test_new_setCurDate(): void
    {
        $form = new FormHandler();

        $form->html5DateField("Date", "date", null, null, true);

        static::assertFormFlushContains($form, ['<input type="date" name="date" id="date" value="' . date('Y-m-d') . '"']);
    }

    public function test_new_extra(): void
    {
        $form = new FormHandler();

        $form->html5DateField("Date", "date", null, null, false, 'min="2020-01-01"');

        static::assertFormFlushContains($form, ['<input type="date" name="date" id="date" value="" size="20"  min="2020-01-01"']);
    }

    public function test_setValue(): void
    {
        $form = new FormHandler();

        $form->html5DateField("Date", "date");
        $form->html5DateField("DateTime", "datetime");
        $form->html5DateField("Zero", "zero");

        $form->setValue("date", "2020-04-14");
        $form->setValue("datetime", "2020-04-14 13:45:00");
        $form->setValue("zero", "0000-00-00");

        static::assertSame("2020-04-14", $form->getValue("date"));
        static::assertSame("2020-04-14", $form->getValue("datetime"));
        static::assertSame("", $form->getValue("zero"));
        static::assertEquals(["2020", "04", "14"], $form->getAsArray("date"));
        static::assertEquals(["", "", ""], $form->getAsArray("zero"));
    }

    public function test_posted(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['date'] = "2020-04-14";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->html5DateField("Date", "date");

        static::assertSame("2020-04-14", $form->getValue("date"));
        static::assertEquals(["2020", "04", "14"], $form->getAsArray("date"));
        static::assertTrue($form->isCorrect());
    }

    public function test_posted_invalid(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['notExisting'] = "2020-02-31";
        $_POST['noDate'] = "14.04.2020";

        $form = new FormHandler();

        $form->html5DateField("NotExisting", "notExisting");
        $form->html5DateField("NoDate", "noDate");

        static::assertFalse($form->isCorrect());

        $e = $form->catchErrors();
        static::assertEquals('<span id="error_notExisting" class="error">You did not enter a correct value for this field!</span>', $e['notExisting']);
        static::assertEquals('<span id="error_noDate" class="error">You did not enter a correct value for this field!</span>', $e['noDate']);

        // the value comes from the user: a warning, no stop
        static::assertNull($form->getAsArray("noDate"));
        $this->assertTriggertError("Value is not a valid date [14.04.2020]", E_USER_WARNING);
    }

    public function test_posted_validator(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['date'] = "";

        $form = new FormHandler();

        $form->html5DateField("Date", "date", FH_NOT_EMPTY);

        static::assertFalse($form->isCorrect());
        static::assertArrayHasKey('date', $form->catchErrors());
    }

    public function test_viewMode(): void
    {
        $form = new FormHandler();

        $form->html5DateField("Default", "default");
        $form->html5DateField("Mask", "mask", null, "D.M.Y");

        $form->setValue("default", "2020-04-04");
        $form->setValue("mask", "2020-04-04");

        $form->enableViewMode();

        static::assertFormFlushContains($form, ['Default:04-04-2020', 'Mask:4.4.2020']);
    }
}
