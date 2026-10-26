<?php

declare(strict_types=1); ?>
<!DOCTYPE html>

<head>
    <meta charset="utf-8">
</head>

<body>
    <?php

    define('FH_FHTML_DIR', 'http://formhandler.test/src/FHTML/');
    ini_set("display_errors", "1");
    @error_reporting(E_USER_WARNING | E_ALL);

    require_once '../../vendor/autoload.php';

    $form = new FormHandler();
    $form->timeField("Time", "time");
    $form->timeField("Time2", "time2", required: true);

    $form->flush();

    function mOnCorrect(array $fields, FormHandler $fh)
    {
        var_dump($fields);
        var_dump($fh);
    }

    // function to show a message
    function doRun($data)
    {
        return print_r($data);
    }
