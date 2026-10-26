<?php

require __DIR__ . '/_layout.php';
example_start();

$form = new FormHandler();

// like dateTextField, with a calendar popup
$form->jsDateTextField('Date', 'date', FH_NOT_EMPTY);

$form->submitButton('Save');
$form->onCorrect('example_on_correct');

example_end($form);
