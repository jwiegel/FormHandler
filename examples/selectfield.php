<?php

require __DIR__ . '/_layout.php';
example_start();

$form = new FormHandler();

$countries = array('de' => 'Germany', 'nl' => 'Netherlands', 'at' => 'Austria', 'ch' => 'Switzerland');

// the array keys are the submitted values
$form->selectField('Country', 'country', $countries, FH_NOT_EMPTY, true);

// several values can be selected
$form->selectField('Languages', 'languages', array('German', 'Dutch', 'English', 'French'), null, false, true, 4);

$form->submitButton('Save');
$form->onCorrect('example_on_correct');

example_end($form);
