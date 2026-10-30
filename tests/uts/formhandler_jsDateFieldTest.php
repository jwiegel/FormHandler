<?php

declare(strict_types=1);

final class formhandler_jsDateFieldTest extends FormhandlerTestCase
{
    public function test_new(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->jsDateField("Datefield", "datefield");

        $year = (int) date("Y");
        $min = $year - 90;

        static::assertFormFlushContains($form, [
            'FHTML/js/datepicker.js',
            "</select> <a href='javascript:;' onclick=\"cal_datefield.show(this, FH_DatePicker.fromFields('FormHandler', 'datefield_year', 'datefield_month', 'datefield_day')); return false;\"  name='anchor_datefield' id='anchor_datefield'><img src=",
            "FHTML/images/calendar.gif' border='0' alt='Select Date' /></a>\n" .
                "error_datefield",
            "var cal_datefield = new FH_DatePicker('{$min}-01-01', '{$year}-12-31');\n" .
                "cal_datefield.setReturnFunction('setdatefieldValues');\n" .
                "function setdatefieldValues(y, m, d) {\n" .
                "    document.forms['FormHandler'].elements['datefield_day'].value   = LZ(d);\n" .
                "    document.forms['FormHandler'].elements['datefield_month'].value = LZ(m);\n" .
                "    document.forms['FormHandler'].elements['datefield_year'].value  = y;\n" .
                "}\n",
        ]);
    }

    public function test_interval(): void
    {
        $form = new FormHandler();

        // 10 years back, 5 years in the future
        $form->jsDateField("Datefield", "datefield", null, null, null, "10:5");

        $year = (int) date("Y");

        static::assertFormFlushContains($form, [
            "var cal_datefield = new FH_DatePicker('" . ($year - 10) . "-01-01', '" . ($year + 5) . "-12-31');\n",
        ]);
    }

    public function test_noCalendarPopup(): void
    {
        $form = new FormHandler();

        $form->jsDateField("Datefield", "datefield");

        $html = $form->flush(true);

        static::assertStringNotContainsString('calendar_popup.js', $html);
        static::assertStringNotContainsString('CalendarPopup', $html);
    }
};
