<?php

declare(strict_types=1);

/**
 * Applies compatibility patches to third-party test utilities after composer install/update.
 */

$projectRoot = dirname(__DIR__);

$patches = [
    [
        'file' => $projectRoot . '/vendor/tombrain/phpunit-mock-db/classes/DatabaseDriverInterface.php',
        'search' => 'public function query($query) : mixed;',
        'replace' => 'public function query(string $query) : mixed;',
    ],
    [
        'file' => $projectRoot . '/vendor/tombrain/phpunit-mock-db/tests/TestingDatabaseDriver.php',
        'search' => 'public function query($query) : mixed',
        'replace' => 'public function query(string $query) : mixed',
    ],
];

foreach ($patches as $patch) {
    $path = $patch['file'];
    if (!file_exists($path)) {
        continue;
    }

    $contents = file_get_contents($path);
    if ($contents === false) {
        throw new RuntimeException('Unable to read file: ' . $path);
    }

    if (strpos($contents, $patch['replace']) !== false) {
        continue;
    }

    if (strpos($contents, $patch['search']) === false) {
        throw new RuntimeException('Expected pattern not found in file: ' . $path);
    }

    $patched = str_replace($patch['search'], $patch['replace'], $contents);
    if ($patched === $contents) {
        throw new RuntimeException('Patch did not modify file: ' . $path);
    }

    if (file_put_contents($path, $patched) === false) {
        throw new RuntimeException('Unable to write file: ' . $path);
    }
}
