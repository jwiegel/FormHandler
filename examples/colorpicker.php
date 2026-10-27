<?php

require __DIR__ . '/_layout.php';
example_start();

$form = new FormHandler();

$form->colorPicker('Background color', 'background', FH_NOT_EMPTY);

$form->submitButton('Save');
$form->onCorrect('example_on_correct');

example_end($form);
