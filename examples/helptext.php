<?php

require __DIR__ . '/_layout.php';
example_start();

$form = new FormHandler();

$form->textField('Name', 'name', FH_STRING);
$form->setHelpText('name', 'First and last name, e.g. "Anna Meyer"', 'Name');

$form->textArea('Comment', 'comment', null, 40, 4);
$form->setMaxLength('comment', 50);   // shows the remaining characters

$form->submitButton('Save');
$form->onCorrect('example_on_correct');

example_end($form);
