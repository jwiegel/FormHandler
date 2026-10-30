<?php

require __DIR__ . '/_layout.php';
example_start();

$form = new FormHandler();

$form->selectField('Country', 'country', array('DE' => 'Germany', 'NL' => 'Netherlands', 'AT' => 'Austria'));
$form->selectField('City', 'city', array());
$form->selectField('District', 'district', array());

// when the country changes, the cities are loaded from _linkselect_options.php, and when
// the city changes, the districts (the script gets the POST values field and filter)
$form->linkSelectFields('_linkselect_options.php', 'country', 'city', 'district');

$form->submitButton('Save');
$form->onCorrect('example_on_correct');

example_end($form);
