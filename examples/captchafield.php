<?php

require __DIR__ . '/_layout.php';
example_start();

$form = new FormHandler();

$form->textField('Name', 'name', FH_STRING);

// needs gregwar/captcha (composer require gregwar/captcha); the image is part of the
// form, the code is kept in the session
$form->CaptchaField('Code from the image', 'captcha');

$form->submitButton('Save');
$form->onCorrect('example_on_correct');

example_end($form);
