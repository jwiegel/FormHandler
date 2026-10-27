<?php

require __DIR__ . '/_layout.php';
example_start();

$form = new FormHandler();

$form->textArea('Message', 'message', FH_TEXT, 40, 6);
$form->textArea('Note (optional, max. 100 characters)', 'note', null, 40, 3);
$form->setMaxLength('note', 100);

$form->submitButton('Save');
$form->onCorrect('example_on_correct');

example_end($form);
