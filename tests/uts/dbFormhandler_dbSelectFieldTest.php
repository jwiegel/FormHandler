<?php

declare(strict_types=1);

final class dbFormhandler_dbSelectFieldTest extends dbFormhandlerTestCase
{
    private function addSelectField(dbFormHandler $form, string $name, bool $multiple = false): void
    {
        $form->dbSelectField(
            'Options from a table',
            $name,
            'loadFromTable',
            array('keyField', 'valueField'),
            'ORDER BY valueField',
            FH_NOT_EMPTY,
            $multiple ? true : null
        );
    }

    public function test_new(): void
    {
        $form = new dbFormHandler();

        $this->setConnectedTable($form, "test");
        $this->addSelectField($form, 'saveInField');

        $aExpected = [
            'Options from a table:<select name="saveInField" id="saveInField" size="1">',
            '<option  value="2" >bar</option>',
            '<option  value="1" >foo</option>',
            '</select>error_saveInField'
        ];

        static::assertFormFlushContains($form, $aExpected);
    }

    public function test_edit(): void
    {
        $this->insertRecord(['id' => 123, 'saveInField' => 2]);

        $_GET['id'] = "123";

        $form = new dbFormHandler();

        static::assertFalse($form->insert);
        static::assertTrue($form->edit);
        static::assertFalse($form->isPosted());

        $this->setConnectedTable($form, "test");
        $this->addSelectField($form, 'saveInField');

        static::assertEquals("2", $form->getValue("saveInField"));

        $aExpected = [
            'Options from a table:<select name="saveInField" id="saveInField" size="1">',
            '<option  value="2"  selected="selected">bar</option>',
            '<option  value="1" >foo</option>',
            '</select>error_saveInField'
        ];

        static::assertFormFlushContains($form, $aExpected);
    }

    public function test_insert(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['saveInField'] = "2";

        $form = new dbFormHandler();

        $this->setConnectedTable($form, "test");

        static::assertTrue($form->isPosted());

        $this->addSelectField($form, 'saveInField');

        $this->setCallbackOnSaved($form);

        $r = $form->flush(true);

        static::assertEquals("", $r);
        $this->assertSavedId(1);
        $this->assertSavedValue('2', 'saveInField');
        $this->assertRecord(1, ['saveInField' => 2]);
    }

    public function test_update(): void
    {
        $this->insertRecord(['id' => 123, 'saveInField' => 1]);

        $_POST['FormHandler_submit'] = "1";
        $_GET['id'] = "123";
        $_POST['saveInField'] = "2";

        $form = new dbFormHandler();

        static::assertFalse($form->insert);
        static::assertTrue($form->edit);
        static::assertTrue($form->isPosted());

        $this->setConnectedTable($form, "test");
        $this->addSelectField($form, 'saveInField');

        $this->setCallbackOnSaved($form);

        $r = $form->flush(true);

        static::assertEquals("", $r);
        $this->assertSavedId(123);
        $this->assertSavedValue('2', 'saveInField');
        $this->assertRecord(123, ['saveInField' => 2]);
    }

    public function test_multiple_new(): void
    {
        $form = new dbFormHandler();

        $this->setConnectedTable($form, "test");
        static::assertFalse($form->isPosted());

        $this->addSelectField($form, 'saveInFieldString', true);

        $aExpected = [
            'Options from a table:<select name="saveInFieldString[]" id="saveInFieldString" size="4" multiple="multiple">',
            '<option  value="2" >bar</option>',
            '<option  value="1" >foo</option>',
            '</select>error_saveInFieldString'
        ];

        static::assertFormFlushContains($form, $aExpected);
    }

    public function test_multiple_insert(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['saveInFieldString'] = ['0' => "2", '1' => "1"];

        $form = new dbFormHandler();

        static::assertTrue($form->isPosted());

        $this->setConnectedTable($form, "test");
        $this->addSelectField($form, 'saveInFieldString');

        $this->setCallbackOnSaved($form);

        $r = $form->flush(true);

        static::assertEquals("", $r);
        $this->assertSavedId(1);
        $this->assertSavedValue(['0' => "2", '1' => "1"], 'saveInFieldString');
        $this->assertRecord(1, ['saveInFieldString' => '2, 1']);
    }

    public function test_multiple_update(): void
    {
        $this->insertRecord(['id' => 123, 'saveInFieldString' => '2, 1']);

        $_POST['FormHandler_submit'] = "1";
        $_POST['saveInFieldString'] = ['0' => "2"];
        $_GET['id'] = "123";

        $form = new dbFormHandler();

        static::assertTrue($form->isPosted());

        $this->setConnectedTable($form, "test");
        $this->addSelectField($form, 'saveInFieldString');

        $this->setCallbackOnSaved($form);

        $r = $form->flush(true);

        static::assertEquals("", $r);
        $this->assertSavedId(123);
        $this->assertSavedValue(['0' => "2"], 'saveInFieldString');
        $this->assertRecord(123, ['saveInFieldString' => '2']);
    }
};
