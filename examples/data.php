<?php

require __DIR__ . '/_layout.php';
example_start();

/**
 * own onCorrect check: setError() marks a field as wrong after the validators passed
 */
function data_on_correct($data, $form)
{
    if (strtolower($data['name']) == 'admin')
    {
        $form->setError('name', '"admin" is a reserved name');
        return false;
    }
    return example_on_correct($data, $form);
}

$form = new FormHandler();

$form->textField('Name', 'name', FH_STRING);
$form->textField('City', 'city');

$form->setValue('city', 'Amsterdam');           // default value of a field
$form->addValue('created', date('Y-m-d H:i'));  // extra value without a field

$form->submitButton('Save');
$form->onCorrect('data_on_correct');

echo '<p>Try the name "admin".</p>';

example_end($form);
