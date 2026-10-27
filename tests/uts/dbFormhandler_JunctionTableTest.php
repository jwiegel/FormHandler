<?php

declare(strict_types=1);

/**
 * addJunctionTable(): a multiple choice field saved into a n:m table
 * test_options (test_id -> test.id, option_id -> loadFromTable.keyField)
 */
final class dbFormhandler_JunctionTableTest extends dbFormhandlerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->getConnection()->executeStatement(
            'CREATE TABLE test_options (test_id INTEGER NOT NULL, option_id VARCHAR(50) NOT NULL)'
        );
    }

    private function createForm(): dbFormHandler
    {
        $form = new dbFormHandler();
        $this->setConnectedTable($form, "test");

        $form->textField("TextNullable", "textNullable");
        $form->listField("Options", "option_id", array('1' => 'foo', '2' => 'bar', '3' => 'baz'), null, true);
        $form->addJunctionTable('test_options', 'option_id', 'test_id');

        $this->setCallbackOnSaved($form);

        return $form;
    }

    private function getJunction(): array
    {
        return $this->getConnection()->fetchAllNumeric('SELECT test_id, option_id FROM test_options ORDER BY test_id, option_id');
    }

    public function test_insert(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['textNullable'] = "text";
        $_POST['option_id'] = ['1', '3'];

        static::assertEmpty($this->createForm()->flush(true));

        $this->assertSavedId(1);
        $this->assertRecord(1, ['textNullable' => 'text']);
        static::assertEquals([[1, '1'], [1, '3']], $this->getJunction());
    }

    public function test_edit(): void
    {
        $this->insertRecord(['id' => 5, 'textNullable' => 'text']);
        $this->getConnection()->executeStatement("INSERT INTO test_options VALUES (5, '2'), (5, '3'), (6, '1')");

        $_GET['id'] = "5";

        $form = $this->createForm();

        static::assertEquals(['2', '3'], $form->getValue("option_id"));
    }

    public function test_update(): void
    {
        $this->insertRecord(['id' => 5, 'textNullable' => 'text']);
        $this->getConnection()->executeStatement("INSERT INTO test_options VALUES (5, '2'), (5, '3'), (6, '1')");

        $_GET['id'] = "5";
        $_POST['FormHandler_submit'] = "1";
        $_POST['textNullable'] = "text";
        $_POST['option_id'] = ['1'];

        static::assertEmpty($this->createForm()->flush(true));

        // the rows of record 5 are replaced, record 6 is untouched
        static::assertEquals([[5, '1'], [6, '1']], $this->getJunction());
    }

    public function test_insert_quotesInValue(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['textNullable'] = "text";
        $_POST['option_id'] = ["2'), ('99', '99"];

        static::assertEmpty($this->createForm()->flush(true));

        // the value is stored as it is, it must not add a row for record 99
        static::assertEquals([[1, "2'), ('99', '99"]], $this->getJunction());
    }

    public function test_unknownField(): void
    {
        $form = new dbFormHandler();
        $this->setConnectedTable($form, "test");

        $form->addJunctionTable('test_options', 'option_id', 'test_id');

        $this->assertTriggertError("The field 'option_id' doesn't exist in the form", E_USER_WARNING);
    }

    public function test_definedTwice(): void
    {
        $form = new dbFormHandler();
        $this->setConnectedTable($form, "test");
        $form->listField("Options", "option_id", array('1' => 'foo'), null, true);

        static::assertTrue($form->addJunctionTable('test_options', 'option_id', 'test_id'));
        $form->addJunctionTable('test_options', 'option_id', 'test_id');

        $this->assertTriggertError("The junctiontable 'test_options' is already defined", E_USER_WARNING);
    }
};
