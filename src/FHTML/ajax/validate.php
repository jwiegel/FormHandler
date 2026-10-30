<?php
/**
 * Uses the FormHandler Validator class to validate fields on the fly
 *
 * @package FormHandler
 * @author Johan Wiegel
 * @since 04-12-2008
 */

// the classes are found relative to this file. Older versions included them from the
// request parameter "includedir": a path from the request must never be included.
require_once __DIR__ . '/../../includes/class.Validator.php';
require_once __DIR__ . '/../../includes/class.AjaxValidator.php';

header('Content-Type: application/json; charset=utf-8');

$oAjaxValidator = new AjaxValidator();
echo $oAjaxValidator->Validate($_REQUEST, new Validator());
