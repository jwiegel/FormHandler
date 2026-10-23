# FormHandler – Development setup

FormHandler is developed and tested with **PHP 8.2 in a Docker container**. You do
not need a local PHP installation. GitHub Actions uses the same PHP version.

## Requirements

- Docker with Docker Compose (e.g. Docker Desktop)

## Setup

```sh
# build the image (PHP 8.2 CLI with gd, mbstring, mysqli, pdo_mysql, pdo_sqlite, zip, intl, xdebug, Composer)
docker compose build

# install the dependencies
docker compose run --rm php composer install
```

## Tests and static analysis

```sh
# PHPUnit (every test runs in its own process; with Docker Desktop on Windows or
# macOS this takes over 10 minutes, because each process reads the mounted project folder)
docker compose run --rm php vendor/bin/phpunit -c tests/uts/phpunit.xml --no-coverage

# single tests
docker compose run --rm php vendor/bin/phpunit -c tests/uts/phpunit.xml --no-coverage --filter dbFormhandler_TextFieldTest

# with code coverage (HTML report in tests/uts/_coverage)
docker compose run --rm php vendor/bin/phpunit -c tests/uts/phpunit.xml

# PHPStan
docker compose run --rm php vendor/bin/phpstan analyse -c phpstan.neon --memory-limit=1G
```

A shell in the container: `docker compose run --rm php bash`

## GitHub Actions

| Workflow | File | Runs |
|---|---|---|
| PHPUnit | `.github/workflows/phpunit.yml` | tests with coverage on PHP 8.2 |
| PHPStan | `.github/workflows/phpstan.yml` | static analysis on PHP 8.2 |

Both run on every push and pull request.

## Notes

- **PHP version:** `composer.json` sets `config.platform.php` to 8.2, so Composer only
  picks packages that run on PHP 8.2, even when it is started with a newer PHP version.
  `composer.lock` is committed.
- **Database in tests:** the database tests run against an in-memory SQLite database
  (`tests/uts/helper/dbFormhandlerTestCase.php`), so no database server is needed. For
  unit tests without a database, replace `DatabaseAdapter` with `createMock()`.
- **`DOCUMENT_ROOT` in tests:** `src/includes/config.inc.php` needs
  `$_SERVER['DOCUMENT_ROOT']`, which PHP leaves empty on the command line.
  `tests/uts/phpunit-prepend.php` sets it as `auto_prepend_file`, so it also reaches
  PHPUnit's isolated test processes. In the container, `docker-compose.yml` mounts
  `tests/uts/php.ini` for this; on GitHub Actions, `setup-php` sets it via `ini-values`.
