<?php

declare(strict_types=1);

final class dbFormhandler_PassFieldTest extends dbFormhandlerTestCase
{
    public function test_new(): void
    {
        $this->createMocksForTable();

        $form = new dbFormHandler();

        static::assertTrue($form->insert);
        static::assertFalse($form->edit);
        static::assertFalse($form->isPosted());

        $this->setConnectedTable($form, "test");

        $form->passField("Your password", "pass", FH_PASSWORD);
        $form->textField("TextNullable", "textNullable");

        static::assertEmpty($form->getValue("textNullable"));
        static::assertEmpty($form->getValue("textNotNullable"));

        static::assertFormFlushContains($form, [
            'Your password:<input type="password" name="pass" id="pass" size="20" />error_pass',
            'TextNullable:<input type="text" name="textNullable" id="textNullable" value="" size="20" />error_textNullable'
        ]);
    }

    public function test_edit(): void
    {
        $this->createMocksForTable();

        $_GET['id'] = "100";

        $form = new dbFormHandler();

        static::assertFalse($form->insert);
        static::assertTrue($form->edit);
        static::assertFalse($form->isPosted());

        $this->getDatabaseMock()
            ->expects($this->exactly(1))
            ->query($this->matches("SELECT * FROM test WHERE id = '100'"))
            ->willReturnResultSet([
                ['id' => '100', 'textNullable' => 'text1', 'pass' => 'secret'],
            ]);

        $this->setConnectedTable($form, "test");

        $form->passField("Your password", "pass", FH_PASSWORD);
        $form->textField("TextNullable", "textNullable");

        static::assertEquals("text1", $form->getValue("textNullable"));
        static::assertEquals("secret", $form->getValue("pass"));

        static::assertFormFlushContains($form, [
            'Your password:<input type="password" name="pass" id="pass" size="20" />error_pass',
            'TextNullable:<input type="text" name="textNullable" id="textNullable" value="text1" size="20" />error_textNullable'
        ]);
    }

    public function test_insert_noValues(): void
    {
        $_POST['FormHandler_submit'] = "1";

        $form = new dbFormHandler();

        static::assertTrue($form->insert);
        static::assertFalse($form->edit);
        static::assertTrue($form->isPosted());

        $this->setConnectedTable($form, "test");

        $form->passField("Your password", "pass", FH_PASSWORD);

        $e = $form->catchErrors();

        $expected  = "You did not enter a correct value for this field!";
        static::assertStringContainsString($expected, $e['pass']);
    }

    public function test_insert(): void
    {
        $this->createMocksForTable();

        $_POST['FormHandler_submit'] = "1";
        $_POST['pass'] = "secret";
        $_POST['textNullable'] = "thetext";

        $form = new dbFormHandler();

        static::assertTrue($form->insert);
        static::assertFalse($form->edit);
        static::assertTrue($form->isPosted());

        $this->setConnectedTable($form, "test");

        $form->passField("Your password", "pass", FH_PASSWORD);
        $form->textField("TextNullable", "textNullable");

        // only textfield, not passfield
        $this->getDatabaseMock()
            ->expects($this->once())
            ->query("INSERT INTO test (pass, textNullable) VALUES ( 'secret', 'thetext' );")
            ->willSetAffectedRows(1)
            ->willSetLastInsertId(4711);

        $this->setCallbackOnSaved($form);

        $r = $form->flush(true);

        static::assertEmpty($r);
        $this->assertSavedId(4711);
        $this->assertSavedValue('secret', 'pass');
        $this->assertSavedValue('thetext', 'textNullable');
    }

    // public function test_update_noValues(): void
    // {
    //     $this->createMocksForTable();

    //     $_POST['FormHandler_submit'] = "1";
    //     $_GET['id'] = "4714";

    //     $form = new dbFormHandler();

    //     static::assertFalse($form->insert);
    //     static::assertTrue($form->edit);

    //     $this->getDatabaseMock()
    //             ->expects($this->exactly(1))
    //             ->query($this->matches("SELECT * FROM test WHERE id = '4714'"))
    //             ->willReturnResultSet([
    //                 ['id' => '4714', 'textNullable' => 'text'],
    //             ]);

    //     $this->setConnectedTable($form, "test");

    //     $form->passField("Your password", "pass", FH_PASSWORD);
    //     $form->textField("TextNullable", "textNullable");

    //     $this->getDatabaseMock()
    //             ->expects($this->once())
    //             ->query("UPDATE test SET textNullable = NULL WHERE id = '4714'");

    //     $this->setCallbackOnSaved($form);

    //     $r = $form->flush(true);

    //     static::fail("forced failure: empty pass fails");

    //     static::assertEquals("", $r);
    //     $this->assertSavedId(4714);
    //     $this->assertSavedValueEmtpy('textNullable');
    // }

    public function test_update(): void
    {
        $this->createMocksForTable();

        $_POST['FormHandler_submit'] = "1";
        $_GET['id'] = "4715";
        $_POST['pass'] = "newpass";

        $form = new dbFormHandler();

        static::assertFalse($form->insert);
        static::assertTrue($form->edit);
        static::assertTrue($form->isPosted());

        $this->getDatabaseMock()
            ->expects($this->exactly(1))
            ->query($this->matches("SELECT * FROM test WHERE id = '4715'"))
            ->willReturnResultSet([
                ['id' => '4715', 'pass' => 'secret'],
            ]);

        $this->setConnectedTable($form, "test");

        $form->passField("Your password", "pass", FH_PASSWORD);

        $this->getDatabaseMock()
            ->expects($this->once())
            ->query("UPDATE test SET pass = 'newpass' WHERE id = '4715'")
            ->willSetLastInsertId(4715);

        $this->setCallbackOnSaved($form);

        $r = $form->flush(true);

        static::assertEmpty($r);
        $this->assertSavedId(4715);
        $this->assertSavedValue("newpass", "pass");
    }
};
