<?php

/**
 * Smoke test of the example pages: every page linked from examples/index.php is
 * requested once (GET) and once submitted empty (POST). Fails on an HTTP status
 * other than 200 and on PHP errors, warnings, notices and deprecations in the output.
 *
 * Usage: php tests/examples/smoke.php http://127.0.0.1:8000/examples/
 * The server must display errors (display_errors=1, error_reporting=-1).
 */

declare(strict_types=1);

$base = rtrim($argv[1] ?? 'http://127.0.0.1:8000/examples/', '/') . '/';

function request(string $url, ?array $post = null): array
{
    $options = ['http' => ['ignore_errors' => true, 'timeout' => 30]];
    if ($post !== null)
    {
        $options['http']['method'] = 'POST';
        $options['http']['header'] = 'Content-Type: application/x-www-form-urlencoded';
        $options['http']['content'] = http_build_query($post);
    }

    $body = @file_get_contents($url, false, stream_context_create($options));
    $status = isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $m) ? (int) $m[1] : 0;

    return [$status, $body === false ? '' : $body];
}

function problems(int $status, string $body): array
{
    $found = [];
    if ($status !== 200)
    {
        $found[] = "HTTP $status";
    }
    if (preg_match_all('/(Fatal error|Parse error|Warning|Notice|Deprecated)(<\/b>)?:\s+(.{0,160})/', $body, $matches, PREG_SET_ORDER))
    {
        foreach ($matches as $match)
        {
            $found[] = $match[1] . ': ' . trim(strip_tags($match[3]));
        }
    }
    return array_unique($found);
}

[$status, $index] = request($base);
if ($status !== 200 || !preg_match_all('/<li><a href="([a-z_]+\.php)">/', $index, $links))
{
    fwrite(STDERR, "Could not read the index page $base (HTTP $status)\n");
    exit(1);
}

$failed = 0;
foreach ($links[1] as $page)
{
    foreach (['GET' => null, 'POST' => ['FormHandler_submit' => '1']] as $method => $post)
    {
        $issues = problems(...request($base . $page, $post));
        printf("%-4s %-24s %s\n", $method, $page, $issues ? 'FAILED' : 'ok');
        foreach ($issues as $issue)
        {
            echo "       $issue\n";
        }
        $failed += $issues ? 1 : 0;
    }
}

printf("\n%d pages, %d failed\n", count($links[1]), $failed);
exit($failed > 0 ? 1 : 0);
