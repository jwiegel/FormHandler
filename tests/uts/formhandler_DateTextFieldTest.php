<?php

declare(strict_types=1);

final class formhandler_DateTextFieldTest extends FormhandlerTestCase
{
    public function test_new(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->dateTextField("DateTextField", "datetextfield");

        static::assertEmpty($form->getValue("datetextfield"));

        static::assertFormFlushContains($form, ['DateTextField:<input type="text" name="datetextfield" id="datetextfield" value="" size="20" />error_datetextfield']);
    }

    public function test_posted(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['datetextfield'] = "14-04-2020";
        $_POST['datetextfield2'] = "14.04.2020";
        $_POST['datetextfield3'] = "14.04.2020";
        $_POST['datetextfield4'] = "14.04.2020";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->dateTextField("Datetextfield", "datetextfield");
        $form->dateTextField("Datetextfield2", "datetextfield2");
        $form->dateTextField("Datetextfield3", "datetextfield3", null, "d.m.Y");
        $form->dateTextField("Datetextfield4", "datetextfield4", null, null, true);

        static::assertEquals("14-04-2020", $form->getValue("datetextfield"));
        static::assertEquals("14.04.2020", $form->getValue("datetextfield2"));
        static::assertEquals("14.04.2020", $form->getValue("datetextfield3"));
        static::assertEquals("14-04-2020", $form->getValue("datetextfield4"));  // already parsed into correct presentation!

        static::assertEquals([2020, 4, 14], $form->getAsArray("datetextfield"));
        static::assertNull($form->getAsArray("datetextfield2"));
        $this->assertTriggertError("Value is not a valid date [14.04.2020]", E_USER_ERROR);
        static::assertEquals([2020, 4, 14], $form->getAsArray("datetextfield3"));
        static::assertEquals([2020, 4, 14], $form->getAsArray("datetextfield4"));

        $e = $form->catchErrors();

        static::assertEquals(1, sizeof($e));
        static::assertEquals('<span id="error_datetextfield2" class="error">You did not enter a correct value for this field!</span>', $e['datetextfield2']);
    }

    public function test_posted_fillvalue_byinvalid(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['datetextfield'] = "14-04-2020";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->dateTextField("DateTextField", "datetextfield");

        static::assertEquals("14-04-2020", $form->getValue("datetextfield"));

        $form->setError("datetextfield", "forcedError");

        static::assertFormFlushContains($form, [
            'DateTextField:<input type="text" name="datetextfield" id="datetextfield" value="14-04-2020" size="20" class="error" />error_datetextfield',
            '<span id="error_datetextfield" class="error">forcedError</span>'
        ]);
    }

    public function test_validator(): void
    {
        $_POST['FormHandler_submit'] = "1";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->dateTextField("DateTextField", "datetextfield", FH_NOT_EMPTY);

        static::assertEmpty($form->getValue("datetextfield"));

        $t = $form->catchErrors(false);

        static::assertEquals(
            '<span id="error_datetextfield" class="error">You did not enter a correct value for this field!</span>',
            $t['datetextfield']
        );
    }

    public function test_new_extra(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->dateTextField("DateTextField", "datetextfield", null, null, null, 'data-old="123"');

        static::assertEmpty($form->getValue("datetextfield"));

        static::assertFormFlushContains($form, ['DateTextField:<input type="text" name="datetextfield" id="datetextfield" value="" size="20"  data-old="123"']);
    }

    public function dataTestGetAsArray(): array
    {
        // The default display of the date fields useage:
        // d = day (2 digits with leading zeros)
        // D = day
        // m = month (2 digits with leading zeros)
        // M = month
        // y = year (two digits)
        // Y = year (four digits)
        return [
            [["mask" => 'd-m-Y', "value" => "",           "result" => ['day' => "",   'month' => "",   'year' => ""]]],
            [["mask" => 'd-m-Y', "value" => "31-03-2020", "result" => ['day' => "31", 'month' => "03", 'year' => "2020"]]],
            [["mask" => 'D-M-Y', "value" => "1-3-2020",   "result" => ['day' => "1",  'month' => "3",  'year' => "2020"]]],
            [["mask" => 'Y-m-d', "value" => "2020-03-31", "result" => ['day' => "31", 'month' => "03", 'year' => "2020"]]],
            [["mask" => 'Y-M-D', "value" => "2020-3-1",   "result" => ['day' => "1",  'month' => "3",  'year' => "2020"]]],
            [["mask" => 'd.m.Y', "value" => "31.03.2020", "result" => ['day' => "31", 'month' => "03", 'year' => "2020"]]],
            [["mask" => 'D.M.Y', "value" => "1.3.2020",   "result" => ['day' => "1",  'month' => "3",  'year' => "2020"]]],
            [["mask" => 'Y/m/d', "value" => "2020/03/31", "result" => ['day' => "31", 'month' => "03", 'year' => "2020"]]],
            [["mask" => 'Y/M/D', "value" => "2020/3/1",   "result" => ['day' => "1",  'month' => "3",  'year' => "2020"]]],
            [["mask" => 'd/m/Y', "value" => "31/03/2020", "result" => ['day' => "31", 'month' => "03", 'year' => "2020"]]],
            [["mask" => 'D/M/Y', "value" => "1/3/2020",   "result" => ['day' => "1",  'month' => "3",  'year' => "2020"]]],
            [["mask" => 'd-m-y', "value" => "31-03-20",   "result" => ['day' => "31", 'month' => "03", 'year' => "2020"]]],
            [["mask" => 'D-M-y', "value" => "1-3-20",     "result" => ['day' => "1",  'month' => "3",  'year' => "2020"]]],
            [["mask" => 'd.m.y', "value" => "31.03.20",   "result" => ['day' => "31", 'month' => "03", 'year' => "2020"]]],
            [["mask" => 'D.M.y', "value" => "1.3.20",     "result" => ['day' => "1",  'month' => "3",  'year' => "2020"]]],
            [["mask" => 'd/m/y', "value" => "31/03/20",   "result" => ['day' => "31", 'month' => "03", 'year' => "2020"]]],
            [["mask" => 'D/M/y', "value" => "1/3/20",     "result" => ['day' => "1",  'month' => "3",  'year' => "2020"]]],
        ];
    }
    /**
     * @dataProvider dataTestGetAsArray
     */
    public function testGetAsArray($dataTestGetAsArray): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['datetextfield'] = $dataTestGetAsArray['value'];

        $form = new FormHandler();

        $form->dateTextField("DateTextField", "datetextfield", null, $dataTestGetAsArray['mask']);

        static::assertTrue($form->isPosted());

        list($year, $month, $day) = $form->getAsArray('datetextfield');

        static::assertEquals($dataTestGetAsArray['result']['year'], $year);
        static::assertEquals($dataTestGetAsArray['result']['month'], $month);
        static::assertEquals($dataTestGetAsArray['result']['day'], $day);
    }

    /**
     * @dataProvider dataTestGetAsArray
     */
    public function testParseOtherRepresentaions($dataTestGetAsArray): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['datetextfield'] = $dataTestGetAsArray['value'];
        $_POST['datetextfield2'] = $dataTestGetAsArray['value'];

        $form = new FormHandler();

        $form->dateTextField("DateTextField", "datetextfield", null, null, false);
        $form->dateTextField("DateTextField2", "datetextfield2", null, null, true);

        static::assertTrue($form->isPosted());

        if ($dataTestGetAsArray['mask'] != FH_DATETEXTFIELD_DEFAULT_DISPLAY)
        {
            static::assertNull($form->getAsArray('datetextfield'));
            $this->assertTriggertError("Value is not a valid date [" . $dataTestGetAsArray['value'] . "]", E_USER_ERROR);
        }
        else
        {
            list($year, $month, $day) = $form->getAsArray('datetextfield');

            static::assertEquals($dataTestGetAsArray['result']['year'], $year);
            static::assertEquals($dataTestGetAsArray['result']['month'], $month);
            static::assertEquals($dataTestGetAsArray['result']['day'], $day);

            list($year2, $month2, $day2) = $form->getAsArray('datetextfield2');

            static::assertEquals($dataTestGetAsArray['result']['year'], $year2);
            static::assertEquals($dataTestGetAsArray['result']['month'], $month2);
            static::assertEquals($dataTestGetAsArray['result']['day'], $day2);
        }
    }
};
