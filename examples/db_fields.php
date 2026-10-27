<?php

require __DIR__ . '/_layout.php';
require __DIR__ . '/_db.php';
example_start();

$conn = example_db();

$form = new dbFormHandler();
$form->setConnectionResource($conn, 'preferences');

// the options come from the lookup tables: array(value field, title field)
$form->dbSelectField('Country', 'country', 'countries', array('code', 'name'), 'ORDER BY name');
$form->dbListField('Hobbies', 'hobbies', 'hobbies', array('id', 'name'), 'ORDER BY name');
$form->dbCheckBox('Languages', 'languages', 'languages', array('id', 'name'));
$form->dbRadioButton('Color', 'color', 'colors', array('code', 'name'));
$form->dbTextSelectField('City', 'city', 'cities', 'name', 'ORDER BY name');

$form->submitButton('Save');
$form->onSaved('example_on_saved');

example_end($form, function () use ($conn) {
    example_table($conn, 'preferences', 'db_fields.php');
});
