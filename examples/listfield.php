<?php

require __DIR__ . '/_layout.php';
example_start();

$form = new FormHandler();

$colors = array('r' => 'red', 'g' => 'green', 'b' => 'blue', 'y' => 'yellow', 'w' => 'white');

$form->listField('Colors', 'colors', $colors, FH_NOT_EMPTY, true, 'Chosen', 'Available');

$form->submitButton('Save');
$form->onCorrect('example_on_correct');

example_end($form);
