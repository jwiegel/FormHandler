<?php

require __DIR__ . '/_layout.php';
example_start();

$form = new FormHandler();

// a text field with suggestions
$form->textSelectField('City', 'city', array('Amsterdam', 'Berlin', 'Rotterdam', 'Vienna', 'Zurich'), FH_STRING);

$form->submitButton('Save');
$form->onCorrect('example_on_correct');

example_end($form);
