<?php

require __DIR__ . '/_layout.php';
example_start();

$form = new FormHandler();

// like dateField, with a calendar popup
$form->jsDateField('Birthday', 'birthday', FH_NOT_EMPTY);

$form->submitButton('Save');
$form->onCorrect('example_on_correct');

example_end($form);
