<?php

declare(strict_types=1);

final class formhandler_DateFieldTest extends FormhandlerTestCase
{
    private function getDays($days, $daySelected = null): array
    {
        $r = [];
        for ($i = 1; $i <= 31; $i++)
        {
            if ($daySelected == $i)
                $r[] = sprintf("<option  value=\"%02d\"  selected=\"selected\">%02d</option>", $i, $i);
            else
                $r[] = sprintf("<option  value=\"%02d\" >%02d</option>", $i, $i);
        }

        return $r;
    }
    private function getMonths($monthSelected = null): array
    {
        $aMonthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $r = [];
        for ($i = 1; $i <= 12; $i++)
        {
            if ($monthSelected == $i)
                $r[] = sprintf("<option  value=\"%02d\"  selected=\"selected\">%s</option>", $i, $aMonthNames[$i - 1]);
            else
                $r[] = sprintf("<option  value=\"%02d\" >%s</option>", $i, $aMonthNames[$i - 1]);
        }

        return $r;
    }
    private function getYears($startYear, $endYear, $yearSelected = null): array
    {
        $r = [];
        for ($i = $startYear; $i >= $endYear; $i--)
        {
            if ($yearSelected == $i)
                $r[] = sprintf("<option  value=\"%d\"  selected=\"selected\">%d</option>", $i, $i);
            else
                $r[] = sprintf("<option  value=\"%d\" >%d</option>", $i, $i);
        }

        return $r;
    }


    public function test_new(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->dateField("Datefield", "datefield");

        $aExpected = [];
        $aExpected[] = 'Datefield: <select name="datefield_day" id="datefield_day" size="1">	<option  value="" ></option>';
        $aExpected = array_merge($aExpected, $this->getDays(31, date('d')));
        $aExpected[] = '</select> - <select name="datefield_month" id="datefield_month" size="1">	<option  value="" ></option>';
        $aExpected = array_merge($aExpected, $this->getMonths(date('m')));
        $aExpected[] = '</select> - <select name="datefield_year" id="datefield_year" size="1">	<option  value="" ></option>';
        $startYear = date('Y');
        $endYear = $startYear - 89;
        $aExpected = array_merge($aExpected, $this->getYears($startYear, $endYear, date('Y')));
        $aExpected[] = '</select> error_datefield';

        static::assertFormFlushContains($form, $aExpected);
    }

    public function test_new_required(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->dateField("Datefield", "datefield", null, true);

        $aExpected = [];
        $aExpected[] = 'Datefield: <select name="datefield_day" id="datefield_day" size="1">';
        $aExpected = array_merge($aExpected, $this->getDays(31, date('d')));
        $aExpected[] = '</select> - <select name="datefield_month" id="datefield_month" size="1">';
        $aExpected = array_merge($aExpected, $this->getMonths(date('m')));
        $aExpected[] = '</select> - <select name="datefield_year" id="datefield_year" size="1">';
        $startYear = date('Y');
        $endYear = $startYear - 89;
        $aExpected = array_merge($aExpected, $this->getYears($startYear, $endYear, date('Y')));
        $aExpected[] = '</select> error_datefield';

        $t = static::assertFormFlushContains($form, $aExpected);
        static::assertFalse(strpos($t, '<option  value="" ></option>'));
    }

    public function test_new_no_default(): void
    {
        define('FH_DATEFIELD_SET_CUR_DATE', false);
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->dateField("Datefield", "datefield");

        $aExpected = [];
        $aExpected[] = 'Datefield: <select name="datefield_day" id="datefield_day" size="1">	<option  value="" ></option>';
        $aExpected = array_merge($aExpected, $this->getDays(31));
        $aExpected[] = '</select> - <select name="datefield_month" id="datefield_month" size="1">	<option  value="" ></option>';
        $aExpected = array_merge($aExpected, $this->getMonths());
        $aExpected[] = '</select> - <select name="datefield_year" id="datefield_year" size="1">	<option  value="" ></option>';
        $startYear = date('Y');
        $endYear = $startYear - 89;
        $aExpected = array_merge($aExpected, $this->getYears($startYear, $endYear));
        $aExpected[] = '</select> error_datefield';

        static::assertFormFlushContains($form, $aExpected);
    }

    public function test_interval(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->dateField("Datefield", "datefield", null, null, null, '20:20');

        $aExpected = [];
        $aExpected[] = 'Datefield: <select name="datefield_day" id="datefield_day" size="1">	<option  value="" ></option>';
        $aExpected = array_merge($aExpected, $this->getDays(31, date('d')));
        $aExpected[] = '</select> - <select name="datefield_month" id="datefield_month" size="1">	<option  value="" ></option>';
        $aExpected = array_merge($aExpected, $this->getMonths(date('m')));
        $aExpected[] = '</select> - <select name="datefield_year" id="datefield_year" size="1">	<option  value="" ></option>';
        $startYear = date('Y') + 20;
        $endYear = $startYear - 39;
        $aExpected = array_merge($aExpected, $this->getYears($startYear, $endYear, date('Y')));
        $aExpected[] = '</select> error_datefield';

        static::assertFormFlushContains($form, $aExpected);
    }

    public function test_posted(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['datefield_day'] = "03";
        $_POST['datefield_month'] = "04";
        $_POST['datefield_year'] = "2018";
        $_POST['datefield2_day'] = "03";
        $_POST['datefield2_month'] = "04";
        $_POST['datefield2_year'] = "2018";
        $_POST['datefield3_day'] = "03";
        $_POST['datefield3_month'] = "04";
        $_POST['datefield3_year'] = "2018";
        $_POST['datefield4_day'] = "03";
        $_POST['datefield4_month'] = "04";
        $_POST['datefield4_year'] = "2018";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->dateField("Datefield", "datefield");
        $form->dateField("Datefield2", "datefield2", null, null, "d.m.y");
        $form->dateField("Datefield3", "datefield3", null, null, "y/m/d");
        $form->dateField("Datefield4", "datefield4", null, null, "D-M-Y");

        static::assertEquals("03-04-2018", $form->getValue("datefield"));
        static::assertEquals("03.04.2018", $form->getValue("datefield2"));
        static::assertEquals("2018/04/03", $form->getValue("datefield3"));
        static::assertEquals("03-04-2018", $form->getValue("datefield4"));

        static::assertEquals([2018, 4, 3], $form->getAsArray("datefield"));
        static::assertEquals([2018, 4, 3], $form->getAsArray("datefield2"));
        static::assertEquals([2018, 4, 3], $form->getAsArray("datefield3"));
        static::assertEquals([2018, 4, 3], $form->getAsArray("datefield4"));
    }

    public function different_masks(): array
    {
        return [
            [["d.m.y", "03.04.2018"]],
            [["D.M.Y", "03.04.2018"]],
            [["m.y", "04.2018"]],
            [["M.Y", "04.2018"]],
            [["d.y", "03.2018"]],
            [["D.Y", "03.2018"]],
            [["d.m", "03.04"]],
            [["D.M", "03.04"]],
            [["d", "03"]],
            [["D", "03"]],
            [["m", "04"]],
            [["M", "04"]],
            [["y", "2018"]],
            [["Y", "2018"]]
        ];
    }
    /**
     * @dataProvider different_masks
     */
    public function test_getValue($different_masks): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['datefield_day'] = "03";
        $_POST['datefield_month'] = "04";
        $_POST['datefield_year'] = "2018";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->dateField("Datefield", "datefield", null, null, $different_masks[0]);

         static::assertEquals($different_masks[1], $form->getValue("datefield"));
    }

    public function test_new_mask1(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->dateField("Datefield", "datefield", null, null, "y-m-d");

        $aExpected = [];
        $aExpected[] = 'Datefield: <select name="datefield_year" id="datefield_year" size="1">	<option  value="" ></option>';
        $startYear = date('Y');
        $endYear = $startYear - 89;
        $aExpected = array_merge($aExpected, $this->getYears($startYear, $endYear, date('Y')));
        $aExpected[] = '</select> - <select name="datefield_month" id="datefield_month" size="1">	<option  value="" ></option>';
        $aExpected = array_merge($aExpected, $this->getMonths(date('m')));
        $aExpected[] = '</select> - <select name="datefield_day" id="datefield_day" size="1">	<option  value="" ></option>';
        $aExpected = array_merge($aExpected, $this->getDays(31, date('d')));
        $aExpected[] = '</select> error_datefield';

        static::assertFormFlushContains($form, $aExpected);
    }

    public function test_new_mask2(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->dateField("Datefield", "datefield", null, null, "d.m.y");

        $aExpected = [];
        $aExpected[] = 'Datefield: <select name="datefield_day" id="datefield_day" size="1">	<option  value="" ></option>';
        $aExpected = array_merge($aExpected, $this->getDays(31, date('d')));
        $aExpected[] = '</select> . <select name="datefield_month" id="datefield_month" size="1">	<option  value="" ></option>';
        $aExpected = array_merge($aExpected, $this->getMonths(date('m')));
        $aExpected[] = '</select> . <select name="datefield_year" id="datefield_year" size="1">	<option  value="" ></option>';
        $startYear = date('Y');
        $endYear = $startYear - 89;
        $aExpected = array_merge($aExpected, $this->getYears($startYear, $endYear, date('Y')));
        $aExpected[] = '</select> error_datefield';

        static::assertFormFlushContains($form, $aExpected);
    }

    public function test_new_editfields(): void
    {
        define('FH_DATEFIELD_SET_CUR_DATE', false);
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->dateField("Datefield", "datefield", null, null, "D-M-Y");
        $form->dateField("Datefield2", "datefield2", null, null, "Y.M-D");

        static::assertFormFlushContains($form, [
            'Datefield: <input type="text" name="datefield_day" id="datefield_day" value="" size="2" maxlength="2" /> - <input type="text" name="datefield_month" id="datefield_month" value="" size="2" maxlength="2" /> - <input type="text" name="datefield_year" id="datefield_year" value="" size="4" maxlength="4" /> error_datefield',
            'Datefield2: <input type="text" name="datefield2_year" id="datefield2_year" value="" size="4" maxlength="4" /> . <input type="text" name="datefield2_month" id="datefield2_month" value="" size="2" maxlength="2" /> - <input type="text" name="datefield2_day" id="datefield2_day" value="" size="2" maxlength="2" /> error_datefield2'
        ]);
    }

    public function test_posted_editfields_fillvalue_byinvalid(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['datefield_day'] = "03";
        $_POST['datefield_month'] = "04";
        $_POST['datefield_year'] = "2018";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->dateField("Datefield", "datefield", null, null, "D-M-Y");

        static::assertEquals("03-04-2018", $form->getValue("datefield"));

        $form->setError("datefield", "forcedError");

        static::assertFormFlushContains($form, 'Datefield: <input type="text" name="datefield_day" id="datefield_day" value="03" size="2" maxlength="2" class="error" />' .
            ' - <input type="text" name="datefield_month" id="datefield_month" value="04" size="2" maxlength="2" class="error" />' .
            ' - <input type="text" name="datefield_year" id="datefield_year" value="2018" size="4" maxlength="4" class="error" />' .
            ' error_datefield<span id="error_datefield" class="error">forcedError');
    }

    public function test_posted_editfields_invalidDate(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['datefield_day'] = "43";
        $_POST['datefield_month'] = "04";
        $_POST['datefield_year'] = "2018";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->dateField("Datefield", "datefield", null, null, "D-M-Y");

        static::assertEquals("43-04-2018", $form->getValue("datefield"));

        static::assertFormFlushContains($form, 'Datefield: <input type="text" name="datefield_day" id="datefield_day" value="43" size="2" maxlength="2" class="error" />' .
            ' - <input type="text" name="datefield_month" id="datefield_month" value="04" size="2" maxlength="2" class="error" />' .
            ' - <input type="text" name="datefield_year" id="datefield_year" value="2018" size="4" maxlength="4" class="error" />' .
            ' error_datefield<span id="error_datefield" class="error">The given date is not valid!</span>'
        );
    }

    public function test_posted_editfields_invalidDay(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['datefield_day'] = "A";
        $_POST['datefield_month'] = "04";
        $_POST['datefield_year'] = "2018";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->dateField("Datefield", "datefield", null, null, "D-M-Y");

        static::assertFormFlushContains($form, 'Datefield: <input type="text" name="datefield_day" id="datefield_day" value="A" size="2" maxlength="2" class="error" />' .
            ' - <input type="text" name="datefield_month" id="datefield_month" value="04" size="2" maxlength="2" class="error" />' .
            ' - <input type="text" name="datefield_year" id="datefield_year" value="2018" size="4" maxlength="4" class="error" />' .
            ' error_datefield<span id="error_datefield" class="error"><span id="error_datefield_day" class="error">You did not enter a correct value for this field!</span></span>'
        );
    }

    public function test_posted_editfields_invalidMonth(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['datefield_day'] = "01";
        $_POST['datefield_month'] = "A";
        $_POST['datefield_year'] = "2018";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->dateField("Datefield", "datefield", null, null, "D-M-Y");

        static::assertFormFlushContains($form, 'Datefield: <input type="text" name="datefield_day" id="datefield_day" value="01" size="2" maxlength="2" class="error" />' .
            ' - <input type="text" name="datefield_month" id="datefield_month" value="A" size="2" maxlength="2" class="error" />' .
            ' - <input type="text" name="datefield_year" id="datefield_year" value="2018" size="4" maxlength="4" class="error" />' .
            ' error_datefield<span id="error_datefield" class="error"><span id="error_datefield_month" class="error">You did not enter a correct value for this field!</span>'
        );
    }

    public function test_posted_editfields_invalidYear1(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['datefield_day'] = "01";
        $_POST['datefield_month'] = "04";
        $_POST['datefield_year'] = "A";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->dateField("Datefield", "datefield", null, null, "D-M-Y");

        static::assertFormFlushContains($form, 'Datefield: <input type="text" name="datefield_day" id="datefield_day" value="01" size="2" maxlength="2" class="error" />' .
            ' - <input type="text" name="datefield_month" id="datefield_month" value="04" size="2" maxlength="2" class="error" />' .
            ' - <input type="text" name="datefield_year" id="datefield_year" value="A" size="4" maxlength="4" class="error" />' .
            ' error_datefield<span id="error_datefield" class="error"><span id="error_datefield_year" class="error">You did not enter a correct value for this field!</span></span>'
        );
    }

    public function test_posted_editfields_invalidYear2(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['datefield_day'] = "01";
        $_POST['datefield_month'] = "04";
        $_POST['datefield_year'] = "123";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->dateField("Datefield", "datefield", null, null, "D-M-Y");

        static::assertFormFlushContains($form, 'Datefield: <input type="text" name="datefield_day" id="datefield_day" value="01" size="2" maxlength="2" class="error" />' .
            ' - <input type="text" name="datefield_month" id="datefield_month" value="04" size="2" maxlength="2" class="error" />' .
            ' - <input type="text" name="datefield_year" id="datefield_year" value="123" size="4" maxlength="4" class="error" />' .
            ' error_datefield<span id="error_datefield" class="error">The given date is not valid!</span>'
        );
    }

    public function test_new_extra(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->dateField("Datefield", "datefield", null, null, null, null, 'data-old="123"');

        $aExpected = [];
        $aExpected[] = 'Datefield: <select name="datefield_day" id="datefield_day" size="1" data-old="123">	<option  value="" ></option>';
        $aExpected = array_merge($aExpected, $this->getDays(31, date('d')));
        $aExpected[] = '</select> - <select name="datefield_month" id="datefield_month" size="1" data-old="123">	<option  value="" ></option>';
        $aExpected = array_merge($aExpected, $this->getMonths(date('m')));
        $aExpected[] = '</select> - <select name="datefield_year" id="datefield_year" size="1" data-old="123">	<option  value="" ></option>';
        $startYear = date('Y');
        $endYear = $startYear - 89;
        $aExpected = array_merge($aExpected, $this->getYears($startYear, $endYear, date('Y')));
        $aExpected[] = '</select> error_datefield';

        static::assertFormFlushContains($form, $aExpected);
    }
};
