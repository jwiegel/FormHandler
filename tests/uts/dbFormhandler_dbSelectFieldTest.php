<?php

declare(strict_types=1);

final class dbFormhandler_dbSelectFieldTest extends dbFormhandlerTestCase
{
    public function test_new(): void
    {
        $form = new dbFormHandler();

        $this->setConnectedTable($form, "test");

        $this->getDatabaseMock()
            ->expects($this->once())
            ->query($this->matches('SELECT keyField, valueField FROM loadFromTable ORDER BY valueField'))
            ->willReturnResultSet([
                ['keyField' => 1, 'valueField' => 'foo'],
                ['keyField' => 2, 'valueField' => 'bar'],
            ]);

        $form->dbSelectField(
            'Options from a table',
            'saveInField',
            'loadFromTable',
            array('keyField', 'valueField'),
            'ORDER BY valueField',
            FH_NOT_EMPTY
        );

        $aExpected = [
            'Options from a table:<select name="saveInField" id="saveInField" size="1">',
            '<option  value="1" >foo</option>',
            '<option  value="2" >bar</option>',
            '</select>error_saveInField'
        ];

        static::assertFormFlushContains($form, $aExpected);
    }

    public function test_edit(): void
    {
        $this->createMocksForTable();

        $_GET['id'] = "123";

        $form = new dbFormHandler();

        static::assertFalse($form->insert);
        static::assertTrue($form->edit);
        static::assertFalse($form->isPosted());

        $this->getDatabaseMock()
            ->expects($this->exactly(1))
            ->query($this->matches("SELECT * FROM test WHERE id = '123'"))
            ->willReturnResultSet([
                ['id' => '123', 'saveInField' => '2'],
            ]);

        $this->setConnectedTable($form, "test");

        $this->getDatabaseMock()
            ->expects($this->once())
            ->query($this->matches('SELECT keyField, valueField FROM loadFromTable ORDER BY valueField'))
            ->willReturnResultSet([
                ['keyField' => 1, 'valueField' => 'foo'],
                ['keyField' => 2, 'valueField' => 'bar'],
            ]);

        $form->dbSelectField(
            'Options from a table',
            'saveInField',
            'loadFromTable',
            array('keyField', 'valueField'),
            'ORDER BY valueField',
            FH_NOT_EMPTY
        );

        static::assertEquals("2", $form->getValue("saveInField"));

        $aExpected = [
            'Options from a table:<select name="saveInField" id="saveInField" size="1">',
            '<option  value="1" >foo</option>',
            '<option  value="2"  selected="selected">bar</option>',
            '</select>error_saveInField'
        ];

        $a = static::assertFormFlushContains($form, $aExpected);
    }

    public function test_insert(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['saveInField'] = "2";

        $form = new dbFormHandler();

        $this->setConnectedTable($form, "test");
        $this->createMocksForTable();

        static::assertTrue($form->isPosted());

        $this->getDatabaseMock()
            ->expects($this->once())
            ->query($this->stringStartsWith('SELECT keyField, valueField FROM loadFromTable'))
            ->willReturnResultSet([
                ['keyField' => 1, 'valueField' => 'foo'],
                ['keyField' => 2, 'valueField' => 'bar'],
            ]);

        $form->dbSelectField(
            'Options from a table',
            'saveInField',
            'loadFromTable',
            array('keyField', 'valueField'),
            'ORDER BY valueField',
            FH_NOT_EMPTY
        );

        $this->getDatabaseMock()
            ->expects($this->once())
            ->query("INSERT INTO test (saveInField) VALUES ('2');")
            ->willSetAffectedRows(1)
            ->willSetLastInsertId(4711);


        $this->setCallbackOnSaved($form);

        $r = $form->flush(true);

        static::assertEquals("", $r);
        $this->assertSavedId(4711);
        $this->assertSavedValue('2', 'saveInField');
    }

    // public function test_insert_wrongValue(): void
    // {
    //     $_POST['FormHandler_submit'] = "1";
    //     $_POST['saveInField'] = "3";

    //     $form = new dbFormHandler();

    //     $this->setConnectedTable($form, "test");
    //     $this->createMocksForTable();

    //     $this->getDatabaseMock()
    //             ->expects($this->once())
    //             ->query($this->stringStartsWith('SELECT keyField, valueField FROM loadFromTable'))
    //             ->willReturnResultSet([
    //                 ['keyField' => 1, 'valueField' => 'foo'],
    //                 ['keyField' => 2, 'valueField' => 'bar'],
    //             ]);

    //     $form->dbSelectField(
    //         'Options from a table',
    //         'saveInField',
    //         'loadFromTable',
    //         array('keyField', 'valueField'),
    //         'ORDER BY `valueField`',
    //         FH_NOT_EMPTY
    //     );

    //     $e = $form->catchErrors();

    //     $this->getDatabaseMock()
    //             ->expects($this->once())
    //             ->query("INSERT INTO test (saveInField) VALUES ('3');")
    //             ->willSetAffectedRows(1)
    //             ->willSetLastInsertId(4711);


    //     $this->setCallbackOnSaved($form);

    //     $r = $form->flush(true);

    //     static::assertEquals("", $r);
    //     $this->assertSavedId(4711);
    //     $this->assertSavedValue('3', 'saveInField');

    //     static::fail("forced failure: wrong value will be saved. 3 is not in list.");
    // }

    public function test_update(): void
    {
        $this->createMocksForTable();

        $_POST['FormHandler_submit'] = "1";
        $_GET['id'] = "123";
        $_POST['saveInField'] = "2";

        $form = new dbFormHandler();

        static::assertFalse($form->insert);
        static::assertTrue($form->edit);
        static::assertTrue($form->isPosted());

        $this->getDatabaseMock()
            ->expects($this->exactly(1))
            ->query($this->matches("SELECT * FROM test WHERE id = '123'"))
            ->willReturnResultSet([
                ['id' => '123', 'saveInField' => '1'],
            ]);

        $this->setConnectedTable($form, "test");

        $this->getDatabaseMock()
            ->expects($this->once())
            ->query($this->stringStartsWith('SELECT keyField, valueField FROM loadFromTable'))
            ->willReturnResultSet([
                ['keyField' => 1, 'valueField' => 'foo'],
                ['keyField' => 2, 'valueField' => 'bar'],
            ]);

        $form->dbSelectField(
            'Options from a table',
            'saveInField',
            'loadFromTable',
            array('keyField', 'valueField'),
            'ORDER BY valueField',
            FH_NOT_EMPTY
        );

        $this->getDatabaseMock()
            ->expects($this->once())
            ->query("UPDATE test SET saveInField = '2' WHERE id = '123'")
            ->willSetAffectedRows(1)
            ->willSetLastInsertId(4711);


        $this->setCallbackOnSaved($form);

        $r = $form->flush(true);

        static::assertEquals("", $r);
        $this->assertSavedId(123);
        $this->assertSavedValue('2', 'saveInField');
    }

    public function test_multiple_new(): void
    {
        $form = new dbFormHandler();

        $this->setConnectedTable($form, "test");
        static::assertFalse($form->isPosted());

        $this->getDatabaseMock()
            ->expects($this->once())
            ->query($this->matches('SELECT keyField, valueField FROM loadFromTable ORDER BY valueField'))
            ->willReturnResultSet([
                ['keyField' => 1, 'valueField' => 'foo'],
                ['keyField' => 2, 'valueField' => 'bar'],
            ]);

        $form->dbSelectField(
            'Options from a table',
            'saveInFieldString',
            'loadFromTable',
            array('keyField', 'valueField'),
            'ORDER BY valueField',
            FH_NOT_EMPTY,
            true
        );

        $aExpected = [
            'Options from a table:<select name="saveInFieldString[]" id="saveInFieldString" size="4" multiple="multiple">',
            '<option  value="1" >foo</option>',
            '<option  value="2" >bar</option>',
            '</select>error_saveInFieldString'
        ];

        static::assertFormFlushContains($form, $aExpected);
    }

    // public function test_multiple_edit(): void
    // {
    //     $this->createMocksForTable();

    //     $_GET['id'] = "123";

    //     $form = new dbFormHandler();

    //     static::assertFalse($form->insert);
    //     static::assertTrue($form->edit);

    //     $this->getDatabaseMock()
    //             ->expects($this->exactly(1))
    //             ->query($this->matches("SELECT * FROM test WHERE id = '123'"))
    //             ->willReturnResultSet([
    //                 ['id' => '123', 'saveInFieldString' => '2, 1'],
    //             ]);

    //     $this->setConnectedTable($form, "test");

    //     $this->getDatabaseMock()
    //             ->expects($this->once())
    //             ->query($this->matches('SELECT keyField, valueField FROM loadFromTable ORDER BY valueField'))
    //             ->willReturnResultSet([
    //                 ['keyField' => 1, 'valueField' => 'foo'],
    //                 ['keyField' => 2, 'valueField' => 'bar'],
    //             ]);

    //     $form->dbSelectField(
    //         'Options from a table',
    //         'saveInFieldString',
    //         'loadFromTable',
    //         array('keyField', 'valueField'),
    //         'ORDER BY valueField',
    //         FH_NOT_EMPTY,
    //         true
    //     );

    //     static::assertEquals(['0' => "2", '1' => " 1"], $form->getValue("saveInFieldString"));

    //     $aExpected = [
    //         'Options from a table:<select name="saveInFieldString[]" id="saveInFieldString" size="4" multiple="multiple">',
    //         '<option  value="1"  selected="selected">foo</option>',
    //         '<option  value="2"  selected="selected">bar</option>',
    //         '</select>error_saveInFieldString'
    //     ];

    //     $a = static::assertFormFlushContains($form, $aExpected);

    //     static::fail("forced failure: getValue has to deliver trimmed values, not ' 1'");
    // }

    public function test_multiple_insert(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['saveInFieldString'] = ['0' => "2", '1' => "1"];

        $form = new dbFormHandler();

        static::assertTrue($form->isPosted());

        $this->setConnectedTable($form, "test");
        $this->createMocksForTable();

        $this->getDatabaseMock()
            ->expects($this->once())
            ->query($this->stringStartsWith('SELECT keyField, valueField FROM loadFromTable'))
            ->willReturnResultSet([
                ['keyField' => 1, 'valueField' => 'foo'],
                ['keyField' => 2, 'valueField' => 'bar'],
            ]);

        $form->dbSelectField(
            'Options from a table',
            'saveInFieldString',
            'loadFromTable',
            array('keyField', 'valueField'),
            'ORDER BY valueField',
            FH_NOT_EMPTY
        );

        $this->getDatabaseMock()
            ->expects($this->once())
            ->query("INSERT INTO test (saveInFieldString) VALUES ( '2, 1' );")
            ->willSetAffectedRows(1)
            ->willSetLastInsertId(4711);


        $this->setCallbackOnSaved($form);

        $r = $form->flush(true);

        static::assertEquals("", $r);
        $this->assertSavedId(4711);
        $this->assertSavedValue(['0' => "2", '1' => "1"], 'saveInFieldString');
    }

    public function test_multiple_update(): void
    {
        $this->createMocksForTable();
        $_POST['FormHandler_submit'] = "1";
        $_POST['saveInFieldString'] = ['0' => "2"];
        $_GET['id'] = "123";

        $form = new dbFormHandler();

        static::assertTrue($form->isPosted());

        $this->getDatabaseMock()
            ->expects($this->exactly(1))
            ->query($this->matches("SELECT * FROM test WHERE id = '123'"))
            ->willReturnResultSet([
                ['id' => '123', 'saveInFieldString' => '2, 1'],
            ]);

        $this->setConnectedTable($form, "test");

        $this->getDatabaseMock()
            ->expects($this->once())
            ->query($this->stringStartsWith('SELECT keyField, valueField FROM loadFromTable'))
            ->willReturnResultSet([
                ['keyField' => 1, 'valueField' => 'foo'],
                ['keyField' => 2, 'valueField' => 'bar'],
            ]);

        $form->dbSelectField(
            'Options from a table',
            'saveInFieldString',
            'loadFromTable',
            array('keyField', 'valueField'),
            'ORDER BY valueField',
            FH_NOT_EMPTY
        );

        $this->getDatabaseMock()
            ->expects($this->once())
            ->query("UPDATE test SET saveInFieldString = '2' WHERE id = '123'")
            ->willSetAffectedRows(1)
            ->willSetLastInsertId(4711);


        $this->setCallbackOnSaved($form);

        $r = $form->flush(true);

        static::assertEquals("", $r);
        $this->assertSavedId(123);
        $this->assertSavedValue(['0' => "2"], 'saveInFieldString');
    }
};
