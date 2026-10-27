<?php

require __DIR__ . '/_layout.php';
example_start();

$form = new FormHandler();

$form->radioButton('Gender', 'gender', array('f' => 'female', 'm' => 'male', 'd' => 'diverse'), FH_NOT_EMPTY, true);
$form->radioButton('Newsletter', 'newsletter', array('yes', 'no'));

$form->submitButton('Save');
$form->onCorrect('example_on_correct');

example_end($form);
