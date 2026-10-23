<?php

require __DIR__ . '/_layout.php';
example_start();

$form = new FormHandler();

// a single checkbox
$form->checkBox('Accept the terms', 'terms', 'yes', FH_NOT_EMPTY);

// a group of checkboxes
$form->checkBox('Interests', 'interests', array('sport' => 'Sport', 'music' => 'Music', 'books' => 'Books'), null, true);

$form->submitButton('Save');
$form->onCorrect('example_on_correct');

example_end($form);
