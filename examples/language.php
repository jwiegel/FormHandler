<?php

require __DIR__ . '/_layout.php';
example_start();

$form = new FormHandler();

// the texts of FormHandler (error messages, buttons) in another language: src/language/
$form->setLanguage(isset($_GET['lang']) ? $_GET['lang'] : 'en');

$form->textField('Name', 'name', FH_STRING);
$form->textField('E-mail', 'email', FH_EMAIL);
$form->textField('Phone', 'phone', _FH_PHONE);

$form->setFocus('email');                    // the cursor starts in "E-mail"
$form->setTabIndex(array('email', 'phone', 'name'));

$form->submitButton();                       // caption in the chosen language
$form->onCorrect('example_on_correct');

echo '<p>Submit the empty form to see the error messages: ';
foreach (array('en', 'de-utf8', 'nl', 'fr-utf8', 'es') as $lang)
{
    echo '<a href="language.php?lang=' . $lang . '">' . $lang . '</a> ';
}
echo '</p>';

example_end($form);
