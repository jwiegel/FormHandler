<?php

declare(strict_types=1);

// The CLI SAPI always sets DOCUMENT_ROOT to '', but config.inc.php needs it to
// locate the FH3 directory. Use the project root, wherever it is checked out.
if (empty($_SERVER['DOCUMENT_ROOT']))
{
    $_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', dirname(__DIR__, 2));
}

// The tests post forms without the CSRF token of the session; formhandler_CsrfTest
// turns the protection on per form with setCsrfProtection(true).
if (!defined('FH_CSRF_PROTECTION'))
{
    define('FH_CSRF_PROTECTION', false);
}
