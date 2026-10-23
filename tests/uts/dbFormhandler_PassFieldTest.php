<?php

declare(strict_types=1);

final class dbFormhandler_PassFieldTest extends dbFormhandlerTestCase
{
    public function test_new(): void
    {
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
        $this->insertRecord(['id' => 100, 'textNullable' => 'text1', 'pass' => 'secret']);

        $_GET['id'] = "100";

        $form = new dbFormHandler();

        static::assertFalse($form->insert);
        static::assertTrue($form->edit);
        static::assertFalse($form->isPosted());

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

        $this->setCallbackOnSaved($form);

        $r = $form->flush(true);

        static::assertEmpty($r);
        $this->assertSavedId(1);
        $this->assertSavedValue('secret', 'pass');
        $this->assertSavedValue('thetext', 'textNullable');
        $this->assertRecord(1, ['pass' => 'secret', 'textNullable' => 'thetext']);
    }

    public function test_update(): void
    {
        $this->insertRecord(['id' => 4715, 'pass' => 'secret']);

        $_POST['FormHandler_submit'] = "1";
        $_GET['id'] = "4715";
        $_POST['pass'] = "newpass";

        $form = new dbFormHandler();

        static::assertFalse($form->insert);
        static::assertTrue($form->edit);
        static::assertTrue($form->isPosted());

        $this->setConnectedTable($form, "test");

        $form->passField("Your password", "pass", FH_PASSWORD);

        $this->setCallbackOnSaved($form);

        $r = $form->flush(true);

        static::assertEmpty($r);
        $this->assertSavedId(4715);
        $this->assertSavedValue("newpass", "pass");
        $this->assertRecord(4715, ['pass' => 'newpass']);
    }
};
