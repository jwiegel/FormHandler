<?php

require __DIR__ . '/_layout.php';
example_start();

$form = new FormHandler();

$form->textField('Name', 'name', FH_STRING);

// the image comes from src/FHTML/securimage, the code is kept in the session
$form->CaptchaField('Code from the image', 'captcha');

$form->submitButton('Save');
$form->onCorrect('example_on_correct');

example_end($form);
