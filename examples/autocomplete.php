<?php

require __DIR__ . '/_layout.php';
example_start();

$form = new FormHandler();

$cities = array('Amsterdam', 'Antwerp', 'Berlin', 'Bern', 'Bremen', 'Rotterdam', 'Vienna', 'Zurich');

$form->textField('City', 'city', FH_STRING);
$form->setAutoComplete('city', $cities);

$form->submitButton('Save');
$form->onCorrect('example_on_correct');

example_end($form);
