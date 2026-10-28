<?php

require __DIR__ . '/_layout.php';
example_start();

/**
 * An editor that keeps the style attribute (colors, font sizes, alignment).
 * By default the sanitizer removes it.
 */
class StyledEditor extends Editor
{
    protected function isStyleAllowed()
    {
        return true;
    }
}

/**
 * A form with a method for the styled editor, like a project would add it
 */
class ExampleForm extends FormHandler
{
    public function styledEditor($title, $name)
    {
        $fld = new StyledEditor($this, $name);
        $this->_registerField($name, $fld, $title);
    }
}

$form = new ExampleForm();

// CKEditor from src/FHTML/ckeditor
$form->editor('Text', 'text', FH_NOT_EMPTY);
$form->styledEditor('Text with styles', 'styled');

// The HTML is sanitized on the server, whatever the browser sends: the script, the
// event handler and the javascript: link are removed. The color stays only in the
// second editor.
$html = '<p style="color:#c00">Red text</p>'
    . '<p onclick="alert(1)">Click me</p>'
    . '<script>alert(2)</script>'
    . '<p><a href="javascript:alert(3)">Link</a> <a href="https://www.example.org">Example</a></p>';
$form->setValue('text', $html);
$form->setValue('styled', $html);

$form->submitButton('Save');
$form->onCorrect('example_on_correct');

example_end($form);
