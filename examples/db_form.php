<?php

require __DIR__ . '/_layout.php';
require __DIR__ . '/_db.php';
example_start();

$conn = example_db();

// without ?id=... the form inserts a new record, with ?id=... it edits that record
$form = new dbFormHandler();
$form->setConnectionResource($conn, 'persons');

$form->textField('Name', 'name', FH_STRING);
$form->textField('E-mail (unique)', 'email', FH_EMAIL);
$form->dateField('Birthday', 'birthday', null, false);
$form->dbSelectField('Country', 'country', 'countries', array('code', 'name'), 'ORDER BY name');

$form->submitButton('Save');
$form->onSaved('example_on_saved');

echo '<p>' . ($form->edit ? 'Editing record ' . (int) $_GET['id'] : 'New record') . '</p>';

example_end($form, function () use ($conn) {
    example_table($conn, 'persons', 'db_form.php');
});
