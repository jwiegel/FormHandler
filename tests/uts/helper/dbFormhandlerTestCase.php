<?php

declare(strict_types=1);

use Cz\PHPUnit\MockDibi\Drivers\DriversFactory;
use Cz\PHPUnit\MockDibi\Mock;
use Dibi\IConnection;

require_once 'FormhandlerTestCase.php';
require_once 'class.Dibi.php';

abstract class dbFormhandlerTestCase extends FormhandlerTestCase
{
    use Cz\PHPUnit\MockDibi\MockTrait;

    private ?Dibi\Connection $_localDBConnection = null;
    private ?Mock $_mock = null;

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

    protected function setUp(): void
    {
        parent::setUp();
        /** 
         * Your Database Connection (In This Example MySQLi) 
         */
        $factory = $this->getDriversFactory();

        $this->_localDBConnection = new Dibi\Connection([
            'driver' => $factory->createMySqliDriver()  // or whatever other driver you may be needing.
        ]);
        $this->_expectedResult = null;
    }

    final protected function getFormhandlerType(): string
    {
        return "dbFormhandler";
    }

    protected function getLocalConnection(): Dibi\Connection
    {
        return $this->_localDBConnection;
    }

    protected function setConnectedTable(dbFormHandler $form, string $table): void
    {
        $db = new YadalDibi();
        $db->setConnectionResource($this->_localDBConnection);
        $this->setPrivateProperty($form, "_table", $table);
        $this->setPrivateProperty($form, "_db", $db);

        if ($form->edit)
            $this->executePrivateMethod($form, "_loadDbData", array());
    }

    protected function getDatabaseMock(): Mock
    {
        if ($this->_mock == null)
            $this->_mock = $this->createDatabaseMock($this->_localDBConnection);

        return $this->_mock;
    }

    /**
     * @return  DriversFactory
     */
    private function getDriversFactory(): DriversFactory
    {
        return new DriversFactory;
    }

    protected function createMocksForTable(): void
    {
        $this->getDatabaseMock()
            ->expects($this->any())
            ->query($this->stringStartsWith('SHOW KEYS FROM test'))
            ->willReturnResultSet([
                ['Table' => 'test', 'Non_unique' => '0', 'Key_name' => 'PRIMARY', 'Column_name' => 'id'],
            ]);
        $this->getDatabaseMock()
            ->expects($this->any())
            ->query($this->stringStartsWith('DESCRIBE test'))
            ->willReturnResultSet([
                [
                    'Field' => 'id',
                    'Type' => 'int(11)',
                    'Null' => 'NO',
                    'Key' => 'PRI',
                    'Default' => null,
                    'Extra' => 'auto_increment'
                ],
                [
                    'Field' => 'saveInField',
                    'Type' => 'int(11)',
                    'Null' => 'YES',
                    'Key' => '',
                    'Default' => null,
                    'Extra' => ''
                ],
                [
                    'Field' => 'saveInFieldString',
                    'Type' => 'varchar(10)',
                    'Null' => 'YES',
                    'Key' => '',
                    'Default' => null,
                    'Extra' => ''
                ],
                [
                    'Field' => 'textNullable',
                    'Type' => 'varchar(255)',
                    'Null' => 'YES',
                    'Key' => '',
                    'Default' => null,
                    'Extra' => ''
                ],
                [
                    'Field' => 'textNotNullable',
                    'Type' => 'varchar(255)',
                    'Null' => 'NO',
                    'Key' => '',
                    'Default' => null,
                    'Extra' => ''
                ],
                [
                    'Field' => 'pass',
                    'Type' => 'varchar(255)',
                    'Null' => 'NO',
                    'Key' => '',
                    'Default' => null,
                    'Extra' => ''
                ],
            ]);
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
