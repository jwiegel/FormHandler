<?php

declare(strict_types=1);

final class formhandler_jsDateTextFieldTest extends FormhandlerTestCase
{
    public function test_new(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->jsDateTextField("jsDatejsdatetextfield", "jsdatetextfield");

        static::assertEmpty($form->getValue("jsdatetextfield"));

        static::assertFormFlushContains($form, [
            'FHTML/js/datepicker.js',
            'jsDatejsdatetextfield:<input type="text" name="jsdatetextfield" id="jsdatetextfield" value="" size="20" />',
            '<a href=\'javascript:;\' onclick="cal_jsdatetextfield.show(this, FH_DatePicker.parse(\'d-m-Y\', document.forms[\'FormHandler\'].elements[\'jsdatetextfield\'].value)); return false;"  name=\'anchor_jsdatetextfield\' id=\'anchor_jsdatetextfield\'>',
            'FHTML/images/calendar.gif\' border=\'0\' alt=\'Select Date\' /></a>' . "\n" . 'error_jsdatetextfield',
            "var cal_jsdatetextfield = new FH_DatePicker(null, null, function(y, m, d) {\n" .
                "    document.forms['FormHandler'].elements['jsdatetextfield'].value = FH_DatePicker.format('d-m-Y', y, m, d);\n" .
                "});\n"
        ]);
    }

    public function test_mask(): void
    {
        $form = new FormHandler();

        // the date picker writes the date in the mask of the field (D and M: without leading zero)
        $form->jsDateTextField("jsDatejsdatetextfield", "jsdatetextfield", null, "D.M.Y");

        static::assertFormFlushContains($form, [
            "FH_DatePicker.parse('j.n.Y', ",
            "FH_DatePicker.format('j.n.Y', y, m, d);"
        ]);
    }

    public function test_posted(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['jsdatetextfield'] = "14-04-2020";
        $_POST['jsdatetextfield2'] = "14.04.2020";
        $_POST['jsdatetextfield3'] = "14.04.2020";
        $_POST['jsdatetextfield4'] = "14.04.2020";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->jsDateTextField("jsDatejsdatetextfield", "jsdatetextfield");
        $form->jsDateTextField("jsDatejsdatetextfield2", "jsdatetextfield2");
        $form->jsDateTextField("jsDatejsdatetextfield3", "jsdatetextfield3", null, "d.m.Y");
        $form->jsDateTextField("jsDatejsdatetextfield4", "jsdatetextfield4", null, null, true);

        static::assertEquals("14-04-2020", $form->getValue("jsdatetextfield"));
        static::assertEquals("14.04.2020", $form->getValue("jsdatetextfield2"));
        static::assertEquals("14.04.2020", $form->getValue("jsdatetextfield3"));
        static::assertEquals("14-04-2020", $form->getValue("jsdatetextfield4"));  // already parsed into correct presentation!

        static::assertEquals([2020, 4, 14], $form->getAsArray("jsdatetextfield"));
        static::assertNull($form->getAsArray("jsdatetextfield2"));
        $this->assertTriggertError("Value is not a valid date [14.04.2020]", E_USER_WARNING);
        static::assertEquals([2020, 4, 14], $form->getAsArray("jsdatetextfield3"));
        static::assertEquals([2020, 4, 14], $form->getAsArray("jsdatetextfield4"));

        $e = $form->catchErrors();

        static::assertEquals(1, sizeof($e));
        static::assertEquals('<span id="error_jsdatetextfield2" class="error">You did not enter a correct value for this field!</span>', $e['jsdatetextfield2']);
    }

    public function test_posted_fillvalue_byinvalid(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['jsdatetextfield'] = "14-04-2020";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->jsDateTextField("jsDatejsdatetextfield", "jsdatetextfield");

        static::assertEquals("14-04-2020", $form->getValue("jsdatetextfield"));

        $form->setError("jsdatetextfield", "forcedError");

        static::assertFormFlushContains($form, [
            'jsDatejsdatetextfield:<input type="text" name="jsdatetextfield" id="jsdatetextfield" value="14-04-2020" size="20" class="error" />',
            '<a href=\'javascript:;\' onclick="cal_jsdatetextfield.show(this, FH_DatePicker.parse(\'d-m-Y\', document.forms[\'FormHandler\'].elements[\'jsdatetextfield\'].value)); return false;"  name=\'anchor_jsdatetextfield\' id=\'anchor_jsdatetextfield\'>',
            'FHTML/images/calendar.gif\' border=\'0\' alt=\'Select Date\' class="error" /></a>',
            'error_jsdatetextfield<span id="error_jsdatetextfield" class="error">forcedError</span>',
            'var cal_jsdatetextfield = new FH_DatePicker(null, null, function(y, m, d) {'
        ]);
    }

    public function test_new_extra(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->jsDateTextField("jsDatejsdatetextfield", "jsdatetextfield", null, null, null, 'data-old="123"');

        static::assertEmpty($form->getValue("jsdatetextfield"));

        static::assertFormFlushContains($form, ['jsDatejsdatetextfield:<input type="text" name="jsdatetextfield" id="jsdatetextfield" value="" size="20"  data-old="123"']);
    }
};
