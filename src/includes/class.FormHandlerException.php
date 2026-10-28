<?php

declare(strict_types=1);

/**
 * class FormHandlerException
 *
 * Thrown when FormHandler is used in a wrong way, e.g. with an unknown language,
 * a callback that does not exist or an invalid database connection.
 * Replaces trigger_error(..., E_USER_ERROR), which is deprecated since PHP 8.4.
 *
 * @package FormHandler
 */
class FormHandlerException extends RuntimeException
{
}
