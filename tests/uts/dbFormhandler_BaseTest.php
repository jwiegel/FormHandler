<?php

declare(strict_types=1);

final class dbFormhandler_BaseTest extends dbFormhandlerTestCase
{
    public function testConstructor(): void
    {
        $form = new dbFormHandler();

        static::assertInstanceOf("dbFormHandler", $form);

        $_sql = $this->getPrivateProperty($form, '_sql');
        static::assertTrue(is_array($_sql));
        static::assertEquals(0, sizeof($_sql));

        $_dbData = $this->getPrivateProperty($form, '_dbData');
        static::assertTrue(is_array($_dbData));
        static::assertEquals(0, sizeof($_dbData));

        $_dontSave = $this->getPrivateProperty($form, '_dontSave');
        static::assertTrue(is_array($_dontSave));
        static::assertEquals(0, sizeof($_dontSave));

        $_id = $this->getPrivateProperty($form, '_id');
        static::assertTrue(is_array($_id));
        static::assertEquals(0, sizeof($_id));

        $dieOnQuery = $this->getPrivateProperty($form, 'dieOnQuery');
        static::assertFalse($dieOnQuery);

        $_editName = $this->getPrivateProperty($form, '_editName');
        static::assertEquals("id", $_editName);

        static::assertTrue($form->insert);
        static::assertFalse($form->edit);
        static::assertFalse($form->isPosted());
    }

    public function test_SetConnectionResource(): void
    {
        $form = new dbFormHandler();

        $this->setConnectedTable($form, "test");

        $_db = $this->getPrivateProperty($form, '_db');
        static::assertInstanceOf("DbalAdapter", $_db);

        static::assertTrue($_db->isConnected());
        static::assertTrue($_db->quoteNumbers());

        $_table = $this->getPrivateProperty($form, '_table');
        static::assertEquals("test", $_table);
    }

    public function test_SetDatabaseAdapter(): void
    {
        $form = new dbFormHandler();
        $adapter = $this->createMock(DatabaseAdapter::class);

        $form->setDatabaseAdapter($adapter);

        static::assertSame($adapter, $this->getPrivateProperty($form, '_db'));
    }
};
