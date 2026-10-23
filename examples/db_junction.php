<?php

require __DIR__ . '/_layout.php';
require __DIR__ . '/_db.php';
example_start();

$conn = example_db();

$form = new dbFormHandler();
$form->setConnectionResource($conn, 'persons');

$form->textField('Name', 'name', FH_STRING);
$form->textField('E-mail (unique)', 'email', FH_EMAIL);

// the chosen hobbies are not a column of "persons": they are saved as one row
// per hobby in the n:m table person_hobbies (person_id, hobby_id)
$form->dbListField('Hobbies', 'hobby_id', 'hobbies', array('id', 'name'), 'ORDER BY name');
$form->addJunctionTable('person_hobbies', 'hobby_id', 'person_id');

$form->submitButton('Save');
$form->onSaved('example_on_saved');

echo '<p>' . ($form->edit ? 'Editing person ' . (int) $_GET['id'] : 'New person') . '</p>';

example_end($form, function () use ($conn) {
    example_table($conn, 'persons', 'db_junction.php');
    example_table($conn, 'person_hobbies');
});
