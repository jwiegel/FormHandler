<?php

require __DIR__ . '/_layout.php';
example_start();

$form = new FormHandler();

$form->textField('Name', 'name', FH_STRING);
$form->textField('E-mail', 'email', FH_EMAIL);
$form->selectField('Country', 'country', array('de' => 'Germany', 'nl' => 'Netherlands'), null, true);
// the mask is only used in view mode, the field itself shows the date picker
$form->html5DateField('Birthday', 'birthday', null, 'd.m.Y');

$form->setValue('name', 'Anna Meyer');
$form->setValue('email', 'anna@example.org');
$form->setValue('country', 'nl');
$form->setValue('birthday', '1990-04-14');

if (isset($_GET['all']))
{
    // the whole form shows only the values
    $form->enableViewMode();
}
else
{
    // only this field shows its value
    $form->setFieldViewMode('email');
}

$form->submitButton('Save');
$form->onCorrect('example_on_correct');

echo '<p><a href="viewmode.php">one field in view mode</a> · <a href="viewmode.php?all=1">whole form in view mode</a></p>';

example_end($form);
