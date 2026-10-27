<?php

declare(strict_types=1);

final class dbFormhandler_EditorTest extends dbFormhandlerTestCase
{
    public function test_edit_sanitizesStoredHtml(): void
    {
        // HTML saved before the sanitizing (or by another program)
        $this->insertRecord(['id' => 100, 'textNullable' => '<p>Text</p><img src="x" onerror="alert(1)"><script>alert(2)</script>']);

        $_GET['id'] = "100";

        $form = new dbFormHandler();
        $this->setConnectedTable($form, "test");

        $form->editor("Editor", "textNullable");

        static::assertSame('<p>Text</p><img src="x" />', $form->getValue("textNullable"));
    }

    public function test_insert_savesSanitizedHtml(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['textNullable'] = '<p onmouseover="alert(1)">Hallo</p><script>alert(2)</script>';

        $form = new dbFormHandler();
        $this->setConnectedTable($form, "test");

        $form->editor("Editor", "textNullable");

        $this->setCallbackOnSaved($form);

        static::assertEmpty($form->flush(true));
        $this->assertRecord(1, ['textNullable' => '<p>Hallo</p>']);
    }
}
