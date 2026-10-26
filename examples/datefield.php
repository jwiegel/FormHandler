<?php

require __DIR__ . '/_layout.php';
example_start();

$form = new FormHandler();

$form->dateField('Birthday', 'birthday', FH_NOT_EMPTY);

// own order of the select fields; years from now to 10 years ahead ("back:ahead")
$form->dateField('Start (optional)', 'start', null, false, 'y-m-d', '0:10');

$form->submitButton('Save');
$form->onCorrect('example_on_correct');

example_end($form);
