<?php

/**
 * The options for linkselect.php: linkSelectFields() posts "field" (the select field to
 * fill) and "filter" (the value of the field before it). The answer is JSON, a list of
 * [value, text] (or just strings, then value and text are the same).
 */

declare(strict_types=1);

$options = array(
    'city' => array(
        'DE' => array(array('BER', 'Berlin'), array('HAM', 'Hamburg'), array('MUC', 'Munich')),
        'NL' => array(array('AMS', 'Amsterdam'), array('RTM', 'Rotterdam')),
        'AT' => array(array('VIE', 'Vienna'), array('GRZ', 'Graz')),
    ),
    'district' => array(
        'BER' => array('Mitte', 'Kreuzberg', 'Spandau'),
        'HAM' => array('Altona', 'Eimsbüttel'),
        'MUC' => array('Schwabing', 'Sendling'),
        'AMS' => array('Centrum', 'Noord'),
        'RTM' => array('Centrum', 'Kralingen'),
        'VIE' => array('Innere Stadt', 'Leopoldstadt'),
        'GRZ' => array('Innere Stadt', 'Lend'),
    ),
);

$field = isset($_POST['field']) && is_string($_POST['field']) ? $_POST['field'] : '';
$filter = isset($_POST['filter']) && is_string($_POST['filter']) ? $_POST['filter'] : '';

header('Content-Type: application/json; charset=utf-8');
echo json_encode(isset($options[$field][$filter]) ? $options[$field][$filter] : array());
