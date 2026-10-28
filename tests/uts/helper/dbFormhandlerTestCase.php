<?php

declare(strict_types=1);

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

require_once 'FormhandlerTestCase.php';

abstract class dbFormhandlerTestCase extends FormhandlerTestCase
{
    private ?Connection $_connection = null;

    private $_expectedResult;

    protected function assertSavedId(int $expected): void
    {
        static::assertTrue(is_array($this->_expectedResult), "function 'dbFormhandlerTestCase::setCallbackOnSaved' not called");
        static::assertTrue(sizeof($this->_expectedResult) > 0, "formhandler don't call callback function");

        static::assertEquals($expected, $this->_expectedResult['id']);
    }
    protected function assertSavedValue($expected, string $field): void
    {
        static::assertTrue(is_array($this->_expectedResult), "function 'dbFormhandlerTestCase::setCallbackOnSaved' not called");
        static::assertTrue(sizeof($this->_expectedResult) > 0, "formhandler don't call callback function");

        static::assertTrue(array_key_exists($field, $this->_expectedResult['values']), "field not saved");
        static::assertEquals($expected, $this->_expectedResult['values'][$field]);
    }
    protected function assertSavedValueEmtpy(string $field): void
    {
        static::assertTrue(is_array($this->_expectedResult), "function 'dbFormhandlerTestCase::setCallbackOnSaved' not called");
        static::assertTrue(sizeof($this->_expectedResult) > 0, "formhandler don't call callback function");

        static::assertTrue(array_key_exists($field, $this->_expectedResult['values']), "field not saved");
        static::assertEmpty($this->_expectedResult['values'][$field]);
    }

    /**
     * assert the stored values of a record in the table "test"
     *
     * @param integer $id
     * @param array $expected field => value
     * @return void
     */
    protected function assertRecord(int $id, array $expected): void
    {
        $record = $this->getConnection()->fetchAssociative(
            'SELECT ' . implode(', ', array_keys($expected)) . ' FROM test WHERE id = ?',
            [$id]
        );

        static::assertIsArray($record, "record $id not found");
        static::assertSame($expected, $record);
    }

    protected function assertRecordCount(int $expected): void
    {
        static::assertEquals($expected, $this->getConnection()->fetchOne('SELECT COUNT(*) FROM test'));
    }

    protected function setUp(): void
    {
        parent::setUp();

        // in-memory database, with the columns MySQL would report for the old test table
        $this->_connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        $this->_connection->executeStatement(
            "CREATE TABLE test (
                id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                saveInField INTEGER NULL,
                saveInFieldString VARCHAR(10) NULL,
                textNullable VARCHAR(255) NULL,
                textNotNullable VARCHAR(255) NOT NULL DEFAULT '',
                pass VARCHAR(255) NOT NULL DEFAULT '',
                dateNullable DATE NULL
            )"
        );
        $this->_connection->executeStatement(
            'CREATE TABLE loadFromTable (keyField INTEGER NOT NULL PRIMARY KEY, valueField VARCHAR(10) NOT NULL)'
        );
        $this->_connection->executeStatement("INSERT INTO loadFromTable VALUES (1, 'foo'), (2, 'bar')");

        $this->_expectedResult = null;
    }

    final protected function getFormhandlerType(): string
    {
        return "dbFormhandler";
    }

    protected function getConnection(): Connection
    {
        return $this->_connection;
    }

    /**
     * add a record to the table "test" (before the form is connected, so an edit form can load it)
     *
     * @param array $record field => value
     * @return void
     */
    protected function insertRecord(array $record): void
    {
        $this->getConnection()->insert('test', $record);
    }

    protected function setConnectedTable(dbFormHandler $form, string $table): void
    {
        $form->setConnectionResource($this->_connection, $table);
    }

    /**
     * set callback for validation
     *
     * @param dbFormHandler $form
     * @return void
     */
    protected function setCallbackOnSaved(dbFormHandler $form): void
    {
        $this->_expectedResult = array();
        $form->onSaved(array($this, "callback_onSaved"));
    }

    /**
     * Function for callback after save
     *
     * @param integer $id
     * @param array $values
     * @param dbFormHandler $form
     * @return void
     */
    final public function callback_onSaved(int $id, array $values, dbFormHandler $form): void
    {
        $this->_expectedResult['id'] = $id;
        $this->_expectedResult['values'] = $values;
    }
};
