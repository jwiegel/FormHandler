<?php

require __DIR__ . '/_layout.php';
example_start();

$form = new FormHandler();

// page 1
$form->textField('Name', 'name', FH_STRING);
$form->submitButton('Next');

$form->newPage();

// page 2
$form->textField('E-mail', 'email', FH_EMAIL);
$form->submitButton('Save');

$form->onCorrect('example_on_correct');

example_end($form, function () use ($form) {
    echo '<p>getCurrentPage(): ' . $form->getCurrentPage() . ' (the page that was submitted last)</p>';
});
