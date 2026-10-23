<?php

require __DIR__ . '/_layout.php';
example_start();

$form = new FormHandler();

// opens the file manager of src/FHTML/filemanager for the folder examples/uploads/
$form->browserField('File', 'file', '/examples/uploads', FH_NOT_EMPTY, 40);

$form->submitButton('Save');
$form->onCorrect('example_on_correct');

example_end($form);
