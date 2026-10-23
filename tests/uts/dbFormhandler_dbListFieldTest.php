<?php

declare(strict_types=1);

final class dbFormhandler_dbListFieldTest extends dbFormhandlerTestCase
{
    private function addListField(dbFormHandler $form): void
    {
        $form->dbListField(
            'Options from a table',
            'saveInFieldString',
            'loadFromTable',
            array('keyField', 'valueField'),
            'ORDER BY keyField'
        );
    }

    public function test_new(): void
    {
        $form = new dbFormHandler();

        $this->setConnectedTable($form, "test");
        $this->addListField($form);

        static::assertFormFlushContains($form, [
            '<input type="hidden" name="saveInFieldString" id="saveInFieldString" value="" />',
            '<select name="saveInFieldString_ListOn[]" id="saveInFieldString_ListOn" size="4" multiple="multiple"  ondblclick="changeValue(\'saveInFieldString\', false)">	<option>&nbsp;</option>',
            '<option  value="1" >foo</option>',
            '<option  value="2" >bar</option>',
        ]);
    }

    public function test_edit(): void
    {
        $this->insertRecord(['id' => 123, 'saveInFieldString' => '2']);

        $_GET['id'] = "123";

        $form = new dbFormHandler();

        static::assertTrue($form->edit);

        $this->setConnectedTable($form, "test");
        $this->addListField($form);

        static::assertEquals(['2'], $form->getValue("saveInFieldString"));

        static::assertFormFlushContains($form, [
            '<input type="hidden" name="saveInFieldString" id="saveInFieldString" value="2" />',
            'id="saveInFieldString_ListOn" size="4" multiple="multiple"  ondblclick="changeValue(\'saveInFieldString\', false)">	<option  value="2" >bar</option>',
            'id="saveInFieldString_ListOff" size="4" multiple="multiple"  ondblclick="changeValue(\'saveInFieldString\', true)">	<option  value="1" >foo</option>',
        ]);
    }

    public function test_insert(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['saveInFieldString'] = ['2', '1'];

        $form = new dbFormHandler();

        $this->setConnectedTable($form, "test");
        $this->addListField($form);

        $this->setCallbackOnSaved($form);

        static::assertEmpty($form->flush(true));
        $this->assertSavedId(1);
        $this->assertSavedValue(['2', '1'], 'saveInFieldString');
        $this->assertRecord(1, ['saveInFieldString' => '2, 1']);
    }

    public function test_update(): void
    {
        $this->insertRecord(['id' => 123, 'saveInFieldString' => '2, 1']);

        $_POST['FormHandler_submit'] = "1";
        $_POST['saveInFieldString'] = ['1'];
        $_GET['id'] = "123";

        $form = new dbFormHandler();

        $this->setConnectedTable($form, "test");
        $this->addListField($form);

        $this->setCallbackOnSaved($form);

        static::assertEmpty($form->flush(true));
        $this->assertSavedId(123);
        $this->assertRecord(123, ['saveInFieldString' => '1']);
    }
};
