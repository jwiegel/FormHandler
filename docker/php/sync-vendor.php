<?php

/**
 * Mirror vendor/ from the Docker volume to the host's vendor/ folder, so that the IDE
 * knows the packages. Composer calls it after "install" and "update" (see composer.json),
 * "composer sync-vendor" runs it by hand.
 *
 * In the container vendor/ is the Docker volume and /host-vendor is the host's folder
 * (docker-compose.yml). Anywhere else (CI, a project that requires FormHandler, Composer
 * on the host) there is no /host-vendor and nothing happens.
 *
 * rsync only copies changed files and deletes what is gone from the volume.
 */

$target = '/host-vendor/';
if (!is_dir($target))
{
    exit(0);
}

$source = dirname(__DIR__, 2) . '/vendor/';

// -rlt: recursive, symlinks, times; --modify-window=1: the Windows file system
// has no nanosecond timestamps
passthru(
    'rsync -rlt --delete --modify-window=1 ' . escapeshellarg($source) . ' ' . escapeshellarg($target),
    $exitCode
);

if ($exitCode !== 0)
{
    fwrite(STDERR, "sync-vendor: rsync failed (exit code $exitCode)\n");
    exit($exitCode);
}

echo "sync-vendor: vendor/ mirrored to the host\n";
