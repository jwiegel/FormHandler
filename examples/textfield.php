<?php

require __DIR__ . '/_layout.php';
example_start();

$form = new FormHandler();

$form->textField('Name', 'name', FH_STRING, 30, 50);
$form->textField('E-mail', 'email', FH_EMAIL, 30);
$form->textField('Age (optional)', 'age', _FH_DIGIT, 3, 3);
$form->textField('Homepage', 'url', _FH_URL, 40, null, 'placeholder="https://..."');

$form->submitButton('Save');
$form->onCorrect('example_on_correct');

example_end($form);
