<?php

declare(strict_types=1);

final class formhandler_TimeFieldTest extends FormhandlerTestCase
{
    private function getNearestMinute(int &$minute, int $intervall): int
    {
        // get the nearest value at the minutes...
        for ($i = 0; $i < $minute; $i += $intervall);

        $i = abs($minute - $i) < abs($minute - ($i - $intervall)) ?     $i : ($i - $intervall);

        $minute = $i;

        if ($minute == 60)
        {
            $minute = 0;
            return 1;
        }
        else
        {
            return 0;
        }
    }

    private function getMinutes($intervall = 1, $minuteSelected = -1): array
    {
        $r = [];

        for ($i = 0; $i < 60; $i += $intervall)
        {
            if ($minuteSelected == $i)
                $r[] = sprintf("<option  value=\"%02d\"  selected=\"selected\">%02d</option>", $i, $i);
            else
                $r[] = sprintf("<option  value=\"%02d\" >%02d</option>", $i, $i);
        }

        return $r;
    }
    private function getHours(bool $format24h, $hourSelected = -1): array
    {
        $r = [];
        for ($i = 0; $i < ($format24h ? 24 : 12); $i++)
        {
            if ($hourSelected == $i)
                $r[] = sprintf("<option  value=\"%02d\"  selected=\"selected\">%02d</option>", $i, $i);
            else
                $r[] = sprintf("<option  value=\"%02d\" >%02d</option>", $i, $i);
        }

        return $r;
    }

    public function timeintervall(): array
    {
        return [[1], [5], [10], [15], [20], [30]];
    }

    /**
     * @dataProvider timeintervall
     */
    // public function test_new($timeintervall): void
    // {
    //     define('FH_TIMEFIELD_SET_CUR_TIME', false);
    //     define('FH_TIMEFIELD_MINUTE_STEPS', $timeintervall);
    //     $form = new FormHandler();

    //     static::assertFalse($form->isPosted());

    //     $form->timeField("Timefield", "timefield", null, false);

    //     static::assertEmpty($form->getValue("timefield"));

    //     $aExpected = [];
    //     $aExpected[] = 'Timefield:<select name="timefield_hour" id="timefield_hour" size="1">	<option  value="" ></option>';

    //     $aExpected[] = implode("\n\t", $this->getHours(true));
    //     $aExpected[] = '</select> : <select name="timefield_minute" id="timefield_minute" size="1">	<option  value="" ></option>';
    //     $aExpected[] = implode("\n\t", $this->getMinutes($timeintervall));
    //     $aExpected[] = '</select>error_timefield';

    //     static::assertFormFlushContains($form, $aExpected);
    // }

    /**
     * @dataProvider timeintervall
     */
    // public function test_new_currenttime($timeintervall): void
    // {
    //     define('FH_TIMEFIELD_SET_CUR_TIME', true);
    //     define('FH_TIMEFIELD_MINUTE_STEPS', $timeintervall);
    //     $form = new FormHandler();

    //     static::assertFalse($form->isPosted());

    //     $form->timeField("Timefield", "timefield", null, true);

    //     static::assertEmpty($form->getValue("timefield"));

    //     $hour = (int)date('H');
    //     $minute = (int)date('i');
    //     if ($this->getNearestMinute($minute, $timeintervall))
    //         $hour--;

    //     $aExpected = [];
    //     $aExpected[] = 'Timefield:<select name="timefield_hour" id="timefield_hour" size="1">';

    //     $aExpected[] = implode("\n\t", $this->getHours(true, $hour));
    //     $aExpected[] = '</select> : <select name="timefield_minute" id="timefield_minute" size="1">';
    //     $aExpected[] = implode("\n\t", $this->getMinutes($timeintervall, $minute));
    //     $aExpected[] = '</select>error_timefield';

    //     static::assertFormFlushContains($form, $aExpected);
    // }

    public function test_new_required(): void
    {
        define('FH_TIMEFIELD_SET_CUR_TIME', false);
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->timeField("Timefield", "timefield", null);

        static::assertEmpty($form->getValue("timefield"));

        $aExpected = [];
        $aExpected[] = 'Timefield:<select name="timefield_hour" id="timefield_hour" size="1">	<option  value="00"  selected="selected">00</option>';
        $aExpected[] = implode("\n\t", $this->getHours(true, 0));
        $aExpected[] = '</select> : <select name="timefield_minute" id="timefield_minute" size="1">	<option  value="00"  selected="selected">00</option>';
        $aExpected[] = implode("\n\t", $this->getMinutes(10, 0));
        $aExpected[] = '</select>error_timefield';

        static::assertFormFlushContains($form, $aExpected);
    }

    public function test_posted(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['timefield_hour'] = "12";
        $_POST['timefield_minute'] = "34";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->timeField("Timefield", "timefield");

        static::assertEquals("12:34", $form->getValue("timefield"));
    }

    public function test_posted_fillvalue_byinvalid(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['timefield_hour'] = "12";
        $_POST['timefield_minute'] = "30";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->timeField("Timefield", "timefield", null, false);

        static::assertEquals("12:30", $form->getValue("timefield"));

        $form->setError("timefield", "forcedError");

        $aExpected = [];
        $aExpected[] = 'Timefield:<select class="error" name="timefield_hour" id="timefield_hour" size="1">	<option  value="" ></option>';

        $aExpected[] = implode("\n\t", $this->getHours(true, 12));
        $aExpected[] = '</select> : <select class="error" name="timefield_minute" id="timefield_minute" size="1">	<option  value="" ></option>';
        $aExpected[] = implode("\n\t", $this->getMinutes(10, 30));
        $aExpected[] = '</select>error_timefield';

        static::assertFormFlushContains($form, $aExpected);
    }

    public function test_validator(): void
    {
        $_POST['FormHandler_submit'] = "1";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->timeField("Timefield", "timefield", FH_NOT_EMPTY);

        static::assertEmpty($form->getValue("timefield"));

        $t = $form->catchErrors(false);

        static::assertEquals(
            '<span id="error_timefield" class="error">You did not enter a correct value for this field!</span>',
            $t['timefield']
        );
    }

    public function test_new_format(): void
    {
        define('FH_TIMEFIELD_SET_CUR_TIME', false);
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->timeField("Timefield", "timefield", null, false, 12);

        static::assertEmpty($form->getValue("timefield"));

        $aExpected = [];
        $aExpected[] = 'Timefield:<select name="timefield_hour" id="timefield_hour" size="1">	<option  value="" ></option>';

        $aExpected[] = implode("\n\t", $this->getHours(false));
        $aExpected[] = '</select> : <select name="timefield_minute" id="timefield_minute" size="1">	<option  value="" ></option>';
        $aExpected[] = implode("\n\t", $this->getMinutes(10));
        $aExpected[] = '</select>error_timefield';

        static::assertFormFlushContains($form, $aExpected);
    }

    public function test_new_format_expected_formaterror(): void
    {
        define('FH_TIMEFIELD_SET_CUR_TIME', false);
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->timeField("Timefield", "timefield", null, false, 13);

        $this->assertTriggertError("Invalid value as hour format! Only 12 or 24 are allowed!", E_USER_WARNING);
    }

    /**
     * @dataProvider timeintervall
     */
    public function test_new_timefieldsteps($timeintervall): void
    {
        define('FH_TIMEFIELD_MINUTE_STEPS', $timeintervall);
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->timeField("Timefield", "timefield", null, false);

        static::assertEmpty($form->getValue("timefield"));

        $aExpected = [];
        $aExpected[] = 'Timefield:<select name="timefield_hour" id="timefield_hour" size="1">	<option  value="" ></option>';
        $aExpected[] = implode("\n\t", $this->getHours(true));
        $aExpected[] = '</select> : <select name="timefield_minute" id="timefield_minute" size="1">	<option  value="" ></option>';
        $aExpected[] = implode("\n\t", $this->getMinutes($timeintervall));
        $aExpected[] = '</select>error_timefield';

        static::assertFormFlushContains($form, $aExpected);
    }

    public function test_new_extra(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->timeField("Timefield", "timefield", null, false, null, 'data-old="123"');

        static::assertEmpty($form->getValue("timefield"));

        $aExpected = [];
        $aExpected[] = 'Timefield:<select name="timefield_hour" id="timefield_hour" size="1" data-old="123">	<option  value="" ></option>';
        $aExpected[] = implode("\n\t", $this->getHours(true));
        $aExpected[] = '</select> : <select name="timefield_minute" id="timefield_minute" size="1" data-old="123">	<option  value="" ></option>';
        $aExpected[] = implode("\n\t", $this->getMinutes(10));
        $aExpected[] = '</select>error_timefield';

        static::assertFormFlushContains($form, $aExpected);
    }
};
