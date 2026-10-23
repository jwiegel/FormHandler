<?php

require __DIR__ . '/_layout.php';
example_start();

$form = new FormHandler();

$form->passField('Password', 'pass', FH_PASSWORD, 20);
$form->passField('Repeat password', 'pass2', FH_PASSWORD, 20);

// both fields must be equal
$form->checkPassword('pass', 'pass2');

$form->submitButton('Save');
$form->onCorrect('example_on_correct');

example_end($form);
