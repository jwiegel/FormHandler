<?php declare(strict_types=1);

if (empty($_SERVER['DOCUMENT_ROOT']))
{
    $_SERVER['DOCUMENT_ROOT'] = '/var/www';
}

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
require_once __DIR__ . '/helper/FormhandlerTestCase.php';
require_once __DIR__ . '/helper/dbFormhandlerTestCase.php';