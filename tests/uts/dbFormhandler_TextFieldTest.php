<?php

declare(strict_types=1);

final class dbFormhandler_TextFieldTest extends dbFormhandlerTestCase
{
    public function test_new(): void
    {
        $form = new dbFormHandler();

        static::assertTrue($form->insert);
        static::assertFalse($form->edit);
        static::assertFalse($form->isPosted());

        $this->setConnectedTable($form, "test");

        $form->textField("TextNullable", "textNullable");
        $form->textField("TextNotNullable", "textNotNullable");

        static::assertEmpty($form->getValue("textNullable"));
        static::assertEmpty($form->getValue("textNotNullable"));

        static::assertFormFlushContains($form, [
            'input type="text" name="textNullable" id="textNullable" value=""',
            'input type="text" name="textNotNullable" id="textNotNullable" value=""'
        ]);
    }

    public function test_edit_noDataset(): void
    {
        $_GET['id'] = "123";

        $form = new dbFormHandler();

        static::assertFalse($form->insert);
        static::assertTrue($form->edit);
        static::assertFalse($form->isPosted());

        $this->expectException(FormHandlerException::class);
        $this->expectExceptionMessage("Try to edit a none existing record!");

        $this->setConnectedTable($form, "test");
    }

    public function test_edit(): void
    {
        $this->insertRecord(['id' => 100, 'textNullable' => 'text1', 'textNotNullable' => 'text2']);

        $_GET['id'] = "100";

        $form = new dbFormHandler();

        static::assertFalse($form->insert);
        static::assertTrue($form->edit);
        static::assertFalse($form->isPosted());

        $this->setConnectedTable($form, "test");

        $form->textField("TextNullable", "textNullable");
        $form->textField("TextNotNullable", "textNotNullable");

        static::assertEquals("text1", $form->getValue("textNullable"));
        static::assertEquals("text2", $form->getValue("textNotNullable"));

        static::assertFormFlushContains($form, [
            'input type="text" name="textNullable" id="textNullable" value="text1"',
            'input type="text" name="textNotNullable" id="textNotNullable" value="text2"'
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

        $form->textField("TextNullable", "textNullable");
        $form->textField("TextNotNullable", "textNotNullable");

        $this->setCallbackOnSaved($form);

        $r = $form->flush(true);

        static::assertEmpty($r);
        $this->assertSavedId(1);
        $this->assertSavedValueEmtpy('textNullable');
        $this->assertSavedValueEmtpy('textNotNullable');
        $this->assertRecord(1, ['textNullable' => null, 'textNotNullable' => '']);
    }

    public function test_insert(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['textNullable'] = "thetext";
        $_POST['textNotNullable'] = "anothertext";

        $form = new dbFormHandler();

        static::assertTrue($form->insert);
        static::assertFalse($form->edit);
        static::assertTrue($form->isPosted());

        $this->setConnectedTable($form, "test");

        $form->textField("TextNullable", "textNullable");
        $form->textField("TextNotNullable", "textNotNullable");

        $this->setCallbackOnSaved($form);

        $r = $form->flush(true);

        static::assertEmpty($r);
        $this->assertSavedId(1);
        $this->assertSavedValue('thetext', 'textNullable');
        $this->assertSavedValue('anothertext', 'textNotNullable');
        $this->assertRecord(1, ['textNullable' => 'thetext', 'textNotNullable' => 'anothertext']);
    }

    public function test_insert_quotes(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['textNullable'] = "it's";
        $_POST['textNotNullable'] = "\"double\" \\ back";

        $form = new dbFormHandler();
        $this->setConnectedTable($form, "test");

        $form->textField("TextNullable", "textNullable");
        $form->textField("TextNotNullable", "textNotNullable");

        static::assertEmpty($form->flush(true));
        $this->assertRecord(1, ['textNullable' => "it's", 'textNotNullable' => "\"double\" \\ back"]);
    }

    public function test_update_noValues(): void
    {
        $this->insertRecord(['id' => 4714, 'textNullable' => 'text1', 'textNotNullable' => 'text2']);

        $_POST['FormHandler_submit'] = "1";
        $_GET['id'] = "4714";

        $form = new dbFormHandler();

        static::assertFalse($form->insert);
        static::assertTrue($form->edit);
        static::assertTrue($form->isPosted());

        $this->setConnectedTable($form, "test");

        $form->textField("TextNullable", "textNullable");
        $form->textField("TextNotNullable", "textNotNullable");

        $this->setCallbackOnSaved($form);

        $r = $form->flush(true);

        static::assertEmpty($r);
        $this->assertSavedId(4714);
        $this->assertSavedValueEmtpy('textNullable');
        $this->assertSavedValueEmtpy('textNotNullable');
        $this->assertRecord(4714, ['textNullable' => null, 'textNotNullable' => '']);
    }

    public function test_update(): void
    {
        $this->insertRecord(['id' => 4715, 'textNullable' => 'text1', 'textNotNullable' => 'text2']);

        $_POST['FormHandler_submit'] = "1";
        $_GET['id'] = "4715";
        $_POST['textNullable'] = "thetext";
        $_POST['textNotNullable'] = "anothertext";

        $form = new dbFormHandler();

        static::assertFalse($form->insert);
        static::assertTrue($form->edit);
        static::assertTrue($form->isPosted());

        $this->setConnectedTable($form, "test");

        $form->textField("TextNullable", "textNullable");
        $form->textField("TextNotNullable", "textNotNullable");

        $this->setCallbackOnSaved($form);

        $r = $form->flush(true);

        static::assertEmpty($r);
        $this->assertSavedId(4715);
        $this->assertSavedValue('thetext', 'textNullable');
        $this->assertSavedValue('anothertext', 'textNotNullable');
        $this->assertRecord(4715, ['textNullable' => 'thetext', 'textNotNullable' => 'anothertext']);
    }
};
