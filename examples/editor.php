<?php

require __DIR__ . '/_layout.php';
example_start();

$form = new FormHandler();

// CKEditor from src/FHTML/ckeditor
$form->editor('Text', 'text', FH_NOT_EMPTY);

$form->submitButton('Save');
$form->onCorrect('example_on_correct');

example_end($form);
