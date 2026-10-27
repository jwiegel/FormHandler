<?php

declare(strict_types=1);

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\Deprecations\Deprecation;
use PHPUnit\Framework\TestCase;

final class db_DbalAdapterTest extends TestCase
{
    private Connection $_conn;
    private DbalAdapter $_db;

    protected function setUp(): void
    {
        $this->_conn = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        $this->_conn->executeStatement(
            'CREATE TABLE test (
                id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(10) NOT NULL DEFAULT \'none\',
                email VARCHAR(255) NULL,
                birthday DATE NULL
            )'
        );
        $this->_conn->executeStatement('CREATE UNIQUE INDEX test_email ON test (email)');
        $this->_conn->executeStatement("INSERT INTO test (name, email) VALUES ('anna', 'anna@example.org'), ('bert', NULL)");

        $this->_db = new DbalAdapter($this->_conn);

        Deprecation::enableTrackingDeprecations();
    }

    protected function tearDown(): void
    {
        static::assertSame([], Deprecation::getTriggeredDeprecations(), 'DBAL triggered deprecations');
    }

    public function test_connection(): void
    {
        static::assertTrue($this->_db->isConnected());
        static::assertTrue($this->_db->connect());
        static::assertFalse((new DbalAdapter())->isConnected());
    }

    public function test_query_select(): void
    {
        $sql = $this->_db->query('SELECT name, email FROM test ORDER BY id');

        static::assertEquals(2, $this->_db->recordCount($sql));
        static::assertEquals(['name' => 'anna', 'email' => 'anna@example.org'], $this->_db->getRecord($sql));
        static::assertEquals(['name' => 'bert', 'email' => null], $this->_db->getRecord($sql));
        static::assertFalse($this->_db->getRecord($sql));
        static::assertEquals('SELECT name, email FROM test ORDER BY id', $this->_db->getLastQuery());
    }

    public function test_query_valuesAreStrings(): void
    {
        $this->_conn->executeStatement('CREATE TABLE numbers (i INTEGER, f REAL, n INTEGER NULL)');
        $this->_conn->executeStatement('INSERT INTO numbers VALUES (42, 1.5, NULL)');

        // like Yadal in FormHandler 3: strings (or null), the drivers return ints/floats
        $record = $this->_db->getRecord($this->_db->query('SELECT * FROM numbers'));

        static::assertSame(['i' => '42', 'f' => '1.5', 'n' => null], $record);
        static::assertSame('1', $this->_db->result($this->_db->query('SELECT id FROM test ORDER BY id'), 0, 'id'));
    }

    public function test_query_insert(): void
    {
        $sql = $this->_db->query("INSERT INTO test (name) VALUES ('carl')");

        static::assertNotFalse($sql);
        static::assertEquals(0, $this->_db->recordCount($sql));
        static::assertEquals(3, $this->_db->getInsertId());
    }

    public function test_query_error(): void
    {
        static::assertFalse($this->_db->query('SELECT * FROM missing'));
        static::assertStringContainsString('no such table: missing', $this->_db->getError());
    }

    public function test_result(): void
    {
        static::assertEquals(2, $this->_db->result($this->_db->query('SELECT COUNT(1) AS num FROM test'), 0, 'num'));
        static::assertEquals('bert', $this->_db->result($this->_db->query('SELECT name FROM test ORDER BY id'), 1));
        static::assertEquals('anna', $this->_db->result($this->_db->query('SELECT name FROM test ORDER BY id'), 0, 'NAME'));
        static::assertFalse($this->_db->result($this->_db->query('SELECT name FROM test'), 5));
        static::assertFalse($this->_db->result($this->_db->query('SELECT name FROM test'), 0, 'unknown'));
    }

    public function test_quote(): void
    {
        static::assertEquals('"test"', $this->_db->quote('test'));
        static::assertEquals('"test"."name"', $this->_db->quote('test.name'));
        static::assertEquals("it''s", $this->_db->escapeString("it's"));
        static::assertTrue($this->_db->quoteNumbers());
        static::assertEquals("'2026-10-23'", $this->_db->dbDate('2026', '10', '23'));
    }

    public function test_tableInfo(): void
    {
        static::assertEquals(['id', 'name', 'email', 'birthday'], $this->_db->getFieldNames('test'));
        static::assertEquals(['id', 'name'], $this->_db->getNotNullFields('test'));
        static::assertEquals(['id'], $this->_db->getPrKeys('test'));

        $types = $this->_db->getFieldTypes('test');
        static::assertEquals(['string', 10, 'none'], $types['name']);
        static::assertEquals('date', $types['birthday'][0]);

        static::assertEquals(
            ['PRIMARY' => ['id'], 'test_email' => ['email']],
            $this->_db->getUniqueFields('test')
        );
    }

    public function test_mockable(): void
    {
        $db = $this->createMock(DatabaseAdapter::class);
        $db->method('getPrKeys')->willReturn(['id']);

        static::assertEquals(['id'], $db->getPrKeys('test'));
    }
}
