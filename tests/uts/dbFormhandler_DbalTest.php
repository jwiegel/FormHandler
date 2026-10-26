<?php

declare(strict_types=1);

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

final class dbFormhandler_DbalTest extends FormhandlerTestCase
{
    private Connection $_conn;
    private ?int $_savedId = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->_conn = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        $this->_conn->executeStatement(
            'CREATE TABLE test (
                id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(10) NOT NULL,
                email VARCHAR(255) NULL
            )'
        );
        $this->_conn->executeStatement('CREATE UNIQUE INDEX test_email ON test (email)');
        $this->_conn->executeStatement("INSERT INTO test (name, email) VALUES ('anna', 'anna@example.org')");
    }

    protected function getFormhandlerType(): string
    {
        return "dbFormhandler";
    }

    public function callback_onSaved(int $id): void
    {
        $this->_savedId = $id;
    }

    private function createForm(): dbFormHandler
    {
        $form = new dbFormHandler();
        $form->setConnectionResource($this->_conn, 'test');
        $form->textField('Name', 'name');
        $form->textField('Email', 'email');
        $form->onSaved(array($this, 'callback_onSaved'));

        return $form;
    }

    public function test_usesDbalAdapter(): void
    {
        static::assertInstanceOf(DbalAdapter::class, $this->getPrivateProperty($this->createForm(), '_db'));
    }

    public function test_insert(): void
    {
        $_POST['FormHandler_submit'] = '1';
        $_POST['name'] = "o'neil";
        $_POST['email'] = '';

        static::assertEmpty($this->createForm()->flush(true));

        static::assertEquals(2, $this->_savedId);
        static::assertEquals(
            ['name' => "o'neil", 'email' => null],
            $this->_conn->fetchAssociative('SELECT name, email FROM test WHERE id = 2')
        );
    }

    public function test_edit(): void
    {
        $_GET['id'] = '1';

        $form = $this->createForm();

        static::assertTrue($form->edit);
        static::assertEquals('anna', $form->getValue('name'));
        static::assertEquals('anna@example.org', $form->getValue('email'));
    }

    public function test_update(): void
    {
        $_GET['id'] = '1';
        $_POST['FormHandler_submit'] = '1';
        $_POST['name'] = 'annabel';
        $_POST['email'] = 'anna@example.org';

        static::assertEmpty($this->createForm()->flush(true));

        static::assertEquals(1, $this->_savedId);
        static::assertEquals('annabel', $this->_conn->fetchOne('SELECT name FROM test WHERE id = 1'));
    }

    public function test_insert_uniqueViolation(): void
    {
        $_POST['FormHandler_submit'] = '1';
        $_POST['name'] = 'bert';
        $_POST['email'] = 'anna@example.org';

        $html = $this->createForm()->flush(true);

        static::assertNotEmpty($html, 'form must be shown again with an error');
        static::assertNull($this->_savedId);
        static::assertEquals(1, $this->_conn->fetchOne('SELECT COUNT(*) FROM test'));
    }
}
