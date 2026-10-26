<?php

/**
 * Shared layout of the example pages.
 *
 * A page creates its form between example_start() and example_end($form).
 * example_end() shows the submitted values, the form and the source of the page.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

// the captcha keeps its code in the session
session_start();

/**
 * onCorrect callback of the examples: remember the values and show the form again
 *
 * @param array $data the values of the form
 * @param FormHandler $form
 * @return bool false, so that FormHandler displays the form again
 */
function example_on_correct($data, $form)
{
    $GLOBALS['example_values'] = $data;
    return false;
}

function example_start(): void
{
    $pages = require __DIR__ . '/_pages.php';
    $file = basename($_SERVER['SCRIPT_NAME']);
    $title = 'FormHandler examples';
    $manual = null;
    foreach ($pages as $group)
    {
        if (isset($group[$file]))
        {
            list($title, $manual) = $group[$file];
        }
    }

    ob_start();
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8">';
    echo '<title>' . htmlspecialchars($title) . ' – FormHandler</title>';
    echo '<link rel="stylesheet" href="example.css"></head><body>';
    echo '<nav><a href="index.php">&larr; all examples</a>';
    if ($manual)
    {
        echo ' · <a href="https://www.fh3.nl/manual/' . $manual . '" target="_blank" rel="noopener">manual</a>';
    }
    echo '</nav><h1>' . htmlspecialchars($title) . '</h1>';
}

/**
 * @param FormHandler $form
 * @param callable|null $after prints more HTML after the form (called after the form is saved)
 */
function example_end(FormHandler $form, ?callable $after = null): void
{
    $html = $form->flush(true);

    if (isset($GLOBALS['example_values']))
    {
        echo '<section class="values"><h2>Submitted and valid</h2><pre>';
        echo htmlspecialchars(print_r($GLOBALS['example_values'], true));
        echo '</pre></section>';
    }
    else if ($form->isPosted())
    {
        echo '<section class="values invalid"><h2>Submitted, but not valid</h2></section>';
    }

    echo '<section class="form">' . $html . '</section>';

    if ($after)
    {
        $after();
    }

    echo '<details><summary>Source of this page</summary>';
    highlight_file($_SERVER['SCRIPT_FILENAME']);
    echo '</details></body></html>';
    ob_end_flush();
}
