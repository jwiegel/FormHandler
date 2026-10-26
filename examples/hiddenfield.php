<?php

require __DIR__ . '/_layout.php';
example_start();

$form = new FormHandler();

// not visible, but submitted with the form
$form->hiddenField('customer_id', '4711', FH_DIGIT);
$form->textField('Name', 'name', FH_STRING);

$form->submitButton('Save');
$form->onCorrect('example_on_correct');

example_end($form);
