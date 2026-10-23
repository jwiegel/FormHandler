<?php

declare(strict_types=1);

$pages = require __DIR__ . '/_pages.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>FormHandler examples</title>
    <link rel="stylesheet" href="example.css">
</head>
<body>
    <h1>FormHandler examples</h1>
    <p>
        One page per element of the <a href="https://www.fh3.nl/manual/manual.html" target="_blank" rel="noopener">manual</a>.
        Every page shows the submitted values and its own source code.
    </p>
    <?php foreach ($pages as $group => $items): ?>
        <h2><?= htmlspecialchars($group) ?></h2>
        <ul class="examples">
            <?php foreach ($items as $file => list($title)): ?>
                <li><a href="<?= $file ?>"><?= htmlspecialchars($title) ?></a></li>
            <?php endforeach ?>
        </ul>
    <?php endforeach ?>
</body>
</html>
