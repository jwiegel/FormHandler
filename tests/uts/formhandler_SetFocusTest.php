<?php

declare(strict_types=1);

final class formhandler_SetFocusTest extends FormhandlerTestCase
{
    public function test_default(): void
    {
        $form = new FormHandler();

        $form->textField("Textfield", "textfield");
        $form->textField("Textfield2", "textfield2");

        static::assertFormFlushContains(
            $form,
            "// set the focus on a specific field \n" .
                "var elem = document.getElementById ? document.getElementById('textfield'): document.all? document.all['textfield']: false; \n" .
                "if( (elem) && (elem.type != 'hidden')) {\n" .
                "    try {\n" .
                "      elem.focus();\n" .
                "    } catch(e) {}\n" .
                "}\n"
        );
    }

    public function test_set(): void
    {
        $form = new FormHandler();

        $form->textField("Textfield", "textfield");
        $form->textField("Textfield2", "textfield2");

        static::assertTrue($form->setFocus("textfield2"));
        static::assertFormFlushContains(
            $form,
            "// set the focus on a specific field \n" .
                "var elem = document.getElementById ? document.getElementById('textfield2'): document.all? document.all['textfield2']: false; \n" .
                "if( (elem) && (elem.type != 'hidden')) {\n" .
                "    try {\n" .
                "      elem.focus();\n" .
                "    } catch(e) {}\n" .
                "}\n"
        );
    }

    public function test_nofocus(): void
    {
        $form = new FormHandler();

        $form->textField("Textfield", "textfield");
        $form->textField("Textfield2", "textfield2");

        static::assertTrue($form->setFocus(false));

        $t = $form->flush(true);
        static::assertFalse(strpos($t, "elem.focus()"));
    }

    public function test_nofield(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->setFocus("textfield"));
        $this->assertTriggertError('Could net set focus to unknown field "textfield"', E_USER_NOTICE);
    }

    public function test_set_specialfield_datefield(): void
    {
        $form = new FormHandler();

        $form->dateField("Field", "field");

        static::assertTrue($form->setFocus("field"));

        static::assertFormFlushContains(
            $form,
            "// set the focus on a specific field \n" .
                "var elem = document.getElementById ? document.getElementById('field_day'): document.all? document.all['field_day']: false; \n" .
                "if( (elem) && (elem.type != 'hidden')) {\n" .
                "    try {\n" .
                "      elem.focus();\n" .
                "    } catch(e) {}\n" .
                "}\n"
        );
    }

    public function test_set_specialfield_jsdatefield(): void
    {
        $form = new FormHandler();

        $form->jsDateField("Field", "field");

        static::assertTrue($form->setFocus("field"));

        static::assertFormFlushContains(
            $form,
            "// set the focus on a specific field \n" .
                "var elem = document.getElementById ? document.getElementById('field_day'): document.all? document.all['field_day']: false; \n" .
                "if( (elem) && (elem.type != 'hidden')) {\n" .
                "    try {\n" .
                "      elem.focus();\n" .
                "    } catch(e) {}\n" .
                "}\n"
        );
    }

    public function test_set_specialfield_listfield(): void
    {
        $form = new FormHandler();

        $form->listField("Field", "field", []);

        static::assertTrue($form->setFocus("field"));

        static::assertFormFlushContains(
            $form,
            "// set the focus on a specific field \n" .
                "var elem = document.getElementById ? document.getElementById('field_ListOn'): document.all? document.all['field_ListOn']: false; \n" .
                "if( (elem) && (elem.type != 'hidden')) {\n" .
                "    try {\n" .
                "      elem.focus();\n" .
                "    } catch(e) {}\n" .
                "}\n"
        );
    }

    public function test_set_spezialfield_timefield(): void
    {
        $form = new FormHandler();

        $form->timeField("Field", "field");

        static::assertTrue($form->setFocus("field"));

        static::assertFormFlushContains(
            $form,
            "// set the focus on a specific field \n" .
                "var elem = document.getElementById ? document.getElementById('field_hour'): document.all? document.all['field_hour']: false; \n" .
                "if( (elem) && (elem.type != 'hidden')) {\n" .
                "    try {\n" .
                "      elem.focus();\n" .
                "    } catch(e) {}\n" .
                "}\n"
        );
    }

    public function test_no_focus_possible(): void
    {
        $form = new FormHandler();

        $form->editor("Editor", "editor");
        static::assertFalse($form->setFocus("editor"));

        $form->radioButton("Radiobutton", "radiobutton", []);
        static::assertFalse($form->setFocus("radiobutton"));

        $form->checkBox("Checkbox", "checkbox");
        static::assertFalse($form->setFocus("checkbox"));

        $form->hiddenField("hiddenfield", "val");
        static::assertFalse($form->setFocus("hiddenfield"));

        $form->submitButton("Submitbutton", "submitbutton");
        static::assertFalse($form->setFocus("submitbutton"));

        $form->resetButton("Resetbutton", "resetbutton");
        static::assertFalse($form->setFocus("resetbutton"));

        $form->imageButton("Imagebutton", "imagebutton");
        static::assertFalse($form->setFocus("imagebutton"));

        $form->button("Button", "button");
        static::assertFalse($form->setFocus("button"));

        $t = $form->flush(true);
        static::assertFalse(strpos($t, "elem.focus()"));
    }
};
