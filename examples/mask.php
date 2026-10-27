<?php

require __DIR__ . '/_layout.php';
example_start();

$form = new FormHandler();

// no table, every field in its own paragraph
$form->useTable(false);
$form->setMask("<p><label>%title%</label><br>%field% %error%</p>\n", true);

$form->textField('Name', 'name', FH_STRING);
$form->textField('E-mail', 'email', FH_EMAIL);

$form->submitButton('Save');
$form->onCorrect('example_on_correct');

example_end($form);
