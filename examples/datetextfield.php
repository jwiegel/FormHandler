<?php

require __DIR__ . '/_layout.php';
example_start();

$form = new FormHandler();

$form->dateTextField('Date', 'date', FH_NOT_EMPTY);

// also accepts 2026-10-24, 24/10/26, ...
$form->dateTextField('Date (other formats)', 'date2', null, 'd-m-Y', true);

$form->submitButton('Save');
$form->onCorrect('example_on_correct');

example_end($form);
