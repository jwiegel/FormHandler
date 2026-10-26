<?php

require __DIR__ . '/_layout.php';
example_start();

$form = new FormHandler();

$form->timeField('Start time', 'start', null, true);
$form->timeField('End time (12 hours, optional)', 'end', null, false, 12);

$form->submitButton('Save');
$form->onCorrect('example_on_correct');

example_end($form);
