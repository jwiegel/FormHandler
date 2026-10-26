<?php

declare(strict_types=1);

// The CLI SAPI always sets DOCUMENT_ROOT to '', but config.inc.php needs it to
// locate the FH3 directory. Use the project root, wherever it is checked out.
if (empty($_SERVER['DOCUMENT_ROOT']))
{
    $_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', dirname(__DIR__, 2));
}
