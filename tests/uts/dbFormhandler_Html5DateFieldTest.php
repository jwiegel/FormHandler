<?php

declare(strict_types=1);

final class dbFormhandler_Html5DateFieldTest extends dbFormhandlerTestCase
{
    public function test_edit(): void
    {
        $this->insertRecord(['id' => 100, 'dateNullable' => '2020-04-14']);

        $_GET['id'] = "100";

        $form = new dbFormHandler();

        $this->setConnectedTable($form, "test");

        $form->html5DateField("Date", "dateNullable");

        static::assertSame("2020-04-14", $form->getValue("dateNullable"));

        static::assertFormFlushContains($form, ['<input type="date" name="dateNullable" id="dateNullable" value="2020-04-14"']);
    }

    public function test_insert(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['dateNullable'] = "2020-04-14";

        $form = new dbFormHandler();

        $this->setConnectedTable($form, "test");

        $form->html5DateField("Date", "dateNullable");

        $this->setCallbackOnSaved($form);

        static::assertEmpty($form->flush(true));
        $this->assertSavedId(1);
        $this->assertRecord(1, ['dateNullable' => '2020-04-14']);
    }

    public function test_insert_empty(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['dateNullable'] = "";

        $form = new dbFormHandler();

        $this->setConnectedTable($form, "test");

        $form->html5DateField("Date", "dateNullable");

        $this->setCallbackOnSaved($form);

        static::assertEmpty($form->flush(true));
        $this->assertSavedId(1);
        $this->assertRecord(1, ['dateNullable' => null]);
    }
}
