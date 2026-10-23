<?php

require __DIR__ . '/_layout.php';
example_start();

$form = new FormHandler();

$form->textField('Name', 'name', FH_STRING);

$form->submitButton('Submit', 'submit', null, true);    // disabled after the first click
$form->resetButton('Reset');
$form->button('Plain button', 'plain', 'onclick="alert(\'clicked\')"');
$form->cancelButton('Cancel', 'index.php');              // goes to the given URL
$form->backButton('Back');                               // history.back()
$form->imageButton('../src/FHTML/images/calendar.gif');  // submits like a submit button

$form->onCorrect('example_on_correct');

example_end($form);
