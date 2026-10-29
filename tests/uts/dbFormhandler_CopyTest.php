<?php

declare(strict_types=1);

/**
 * Copies a record like the projects using FormHandler do (?id=...&funktion=copy):
 * the form loads the record like an edit form and saves it as a new record.
 */
class copyTest_dbFormHandler extends dbFormHandler
{
    private bool $_isCopy = false;

    public function __construct($name = null, $action = null, $extra = null)
    {
        parent::__construct($name, $action, $extra);

        if (isset($_GET['funktion']) && $_GET['funktion'] == "copy")
        {
            $this->_isCopy = true;
            $this->insert = true;
        }
    }

    public function flush($return = false)
    {
        if ($this->_isCopy)
        {
            $this->_id = array();
            $this->edit = false;
        }

        return parent::flush($return);
    }
}

final class dbFormhandler_CopyTest extends dbFormhandlerTestCase
{
    public function test_copy_showsTheRecord(): void
    {
        $this->insertRecord(['id' => 100, 'textNullable' => 'text1', 'textNotNullable' => 'text2']);

        $_GET['id'] = "100";
        $_GET['funktion'] = "copy";

        $form = new copyTest_dbFormHandler();
        $this->setConnectedTable($form, "test");

        $form->textField("TextNullable", "textNullable");
        $form->textField("TextNotNullable", "textNotNullable");

        static::assertFormFlushContains($form, [
            'input type="text" name="textNullable" id="textNullable" value="text1"',
            'input type="text" name="textNotNullable" id="textNotNullable" value="text2"'
        ]);
    }

    public function test_copy_savesANewRecord(): void
    {
        $this->insertRecord(['id' => 100, 'textNullable' => 'text1', 'textNotNullable' => 'text2']);

        $_POST['FormHandler_submit'] = "1";
        $_POST['textNullable'] = "copy1";
        $_POST['textNotNullable'] = "copy2";
        $_GET['id'] = "100";
        $_GET['funktion'] = "copy";

        $form = new copyTest_dbFormHandler();
        $this->setConnectedTable($form, "test");

        $form->textField("TextNullable", "textNullable");
        $form->textField("TextNotNullable", "textNotNullable");

        $this->setCallbackOnSaved($form);
        $form->flush(true);

        $this->assertSavedId(101);
        $this->assertRecordCount(2);
        $this->assertRecord(100, ['textNullable' => 'text1', 'textNotNullable' => 'text2']);
        $this->assertRecord(101, ['textNullable' => 'copy1', 'textNotNullable' => 'copy2']);
    }
}
