<?php

require __DIR__ . '/_layout.php';
example_start();

$form = new FormHandler();

// <input type="date">: the browser shows its own date picker, the value is yyyy-mm-dd
$form->html5DateField('Date', 'date', FH_NOT_EMPTY);

// today's date in a new form, d.m.Y in view mode
$form->html5DateField('Date (today)', 'date2', null, 'd.m.Y', true);

$form->submitButton('Save');
$form->onCorrect('example_on_correct');

example_end($form);
