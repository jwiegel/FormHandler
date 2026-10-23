<?php

declare(strict_types=1);

/**
 * dbCheckBox, dbRadioButton and dbTextSelectField, with their options from "loadFromTable"
 */
final class dbFormhandler_dbOptionFieldsTest extends dbFormhandlerTestCase
{
    private function addFields(dbFormHandler $form): void
    {
        $form->dbCheckBox('Checkboxes', 'saveInFieldString', 'loadFromTable', array('keyField', 'valueField'));
        $form->dbRadioButton('Radios', 'saveInField', 'loadFromTable', array('keyField', 'valueField'));
        $form->dbTextSelectField('Text', 'textNullable', 'loadFromTable', 'valueField', 'ORDER BY valueField');
    }

    public function test_new(): void
    {
        $form = new dbFormHandler();
        $this->setConnectedTable($form, "test");
        $this->addFields($form);

        static::assertFormFlushContains($form, [
            'Checkboxes:<input type="checkbox" name="saveInFieldString[]" id="saveInFieldString_1" value="1" /><label for="saveInFieldString_1" class="noStyle">foo</label>',
            '<input type="checkbox" name="saveInFieldString[]" id="saveInFieldString_2" value="2" /><label for="saveInFieldString_2" class="noStyle">bar</label>',
            'Radios:<input type="radio" name="saveInField" id="saveInField_1" value="1" /><label for="saveInField_1" class="noStyle">foo</label>',
            '<input type="radio" name="saveInField" id="saveInField_2" value="2" /><label for="saveInField_2" class="noStyle">bar</label>',
            'Text:<input type="text" name="textNullable" id="textNullable" value=""',
            '>bar</a>',
            '>foo</a>',
        ]);
    }

    public function test_edit(): void
    {
        $this->insertRecord(['id' => 5, 'saveInField' => 2, 'saveInFieldString' => '2, 1', 'textNullable' => 'bar']);

        $_GET['id'] = "5";

        $form = new dbFormHandler();
        $this->setConnectedTable($form, "test");
        $this->addFields($form);

        static::assertEquals("2", $form->getValue("saveInField"));
        static::assertEquals("bar", $form->getValue("textNullable"));

        static::assertFormFlushContains($form, [
            'id="saveInFieldString_1" value="1" checked="checked" />',
            'id="saveInFieldString_2" value="2" checked="checked" />',
            'id="saveInField_1" value="1" /><label',
            'id="saveInField_2" value="2" checked="checked" />',
            'name="textNullable" id="textNullable" value="bar"',
        ]);
    }

    public function test_insert(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['saveInFieldString'] = ['2', '1'];
        $_POST['saveInField'] = "1";
        $_POST['textNullable'] = "foo";

        $form = new dbFormHandler();
        $this->setConnectedTable($form, "test");
        $this->addFields($form);
        $this->setCallbackOnSaved($form);

        static::assertEmpty($form->flush(true));
        $this->assertSavedId(1);
        $this->assertRecord(1, ['saveInField' => 1, 'saveInFieldString' => '2, 1', 'textNullable' => 'foo']);
    }

    public function test_update(): void
    {
        $this->insertRecord(['id' => 5, 'saveInField' => 2, 'saveInFieldString' => '2, 1', 'textNullable' => 'bar']);

        $_GET['id'] = "5";
        $_POST['FormHandler_submit'] = "1";
        $_POST['saveInFieldString'] = ['1'];
        $_POST['saveInField'] = "1";
        $_POST['textNullable'] = "own text";

        $form = new dbFormHandler();
        $this->setConnectedTable($form, "test");
        $this->addFields($form);
        $this->setCallbackOnSaved($form);

        static::assertEmpty($form->flush(true));
        $this->assertSavedId(5);
        $this->assertRecord(5, ['saveInField' => 1, 'saveInFieldString' => '1', 'textNullable' => 'own text']);
    }
};
