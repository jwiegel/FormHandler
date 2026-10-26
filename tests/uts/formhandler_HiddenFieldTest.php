<?php

declare(strict_types=1);

final class formhandler_HiddenFieldTest extends FormhandlerTestCase
{
    public function test_new(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->hiddenField("hiddenfield", "thevalue");

        static::assertEquals("thevalue", $form->getValue("hiddenfield"));

        static::assertFormFlushContains($form, ['<input type="hidden" name="hiddenfield" id="hiddenfield" value="thevalue" />']);
    }

    public function test_posted(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['hiddenfield'] = "hiddenvalue";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->hiddenField("hiddenfield");

        static::assertEquals("hiddenvalue", $form->getValue("hiddenfield"));
    }

    public function test_posted_fillvalue_byinvalid(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['hiddenfield'] = "hiddenvalue";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->hiddenField("hiddenfield");

        static::assertEquals("hiddenvalue", $form->getValue("hiddenfield"));

        $form->setError("hiddenfield", "forcedError");

        static::assertFormFlushContains($form, [
            '<input type="hidden" name="hiddenfield" id="hiddenfield" value="hiddenvalue" />',
            '<span id="error_hiddenfield" class="error">forcedError</span>'
        ]);
    }

    public function test_validator(): void
    {
        $_POST['FormHandler_submit'] = "1";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->hiddenField("hiddenfield", "", FH_NOT_EMPTY);

        static::assertEmpty($form->getValue("hiddenfield"));

        $t = $form->catchErrors(false);

        static::assertEquals(
            '<span id="error_hiddenfield" class="error">You did not enter a correct value for this field!</span>',
            $t['hiddenfield']
        );
    }

    public function test_new_extra(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->hiddenField("hiddenfield", "", null, 'data-old="123"');

        static::assertEmpty($form->getValue("hiddenfield"));

        static::assertFormFlushContains($form, ['<input type="hidden" name="hiddenfield" id="hiddenfield" value="" data-old="123" />']);
    }
};
