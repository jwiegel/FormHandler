<?php

require __DIR__ . '/_layout.php';
example_start();

$form = new FormHandler();

$form->borderStart('Person');
$form->textField('Name', 'name', FH_STRING);
$form->textField('E-mail', 'email', FH_EMAIL);
$form->borderStop();

$form->addLine('Address');
$form->textField('Street', 'street');
$form->textField('City', 'city');

$form->addHTML('<tr><td colspan="3"><em>addHTML() inserts any HTML into the form.</em></td></tr>');

$form->submitButton('Save');
$form->onCorrect('example_on_correct');

example_end($form);
