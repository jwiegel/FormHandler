<?php

require __DIR__ . '/_layout.php';
example_start();

$form = new FormHandler();

// the files are stored in examples/uploads/
$form->uploadField('Picture', 'picture', array(
    'path'   => __DIR__ . '/uploads',
    'type'   => 'jpg jpeg png gif',
    'exists' => 'rename',
), FH_NOT_EMPTY);

// resize the uploaded picture to at most 200 x 200 pixels
$form->resizeImage('picture', null, 200, 200);

$form->submitButton('Save');
$form->onCorrect('example_on_correct');

example_end($form);
