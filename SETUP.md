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

The packages are installed into the Docker volume `formhandler_vendor`, not into the
project's `vendor/` folder on the host (see the notes below). To give your IDE the
current packages for autocompletion, copy them to the host after `composer install`
or `composer update`:

```sh
docker compose run --rm php bash -c "find /host-vendor -mindepth 1 -delete && cp -a vendor/. /host-vendor/"
```

## Tests and static analysis

```sh
# PHPUnit (about 30 seconds; every test runs in its own process)
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

- **`vendor/` in a Docker volume:** every test runs in its own PHP process, which loads
  PHPUnit and the autoloader again. Reading these files through the mounted project
  folder is slow on Docker Desktop (Windows/macOS): the suite took about 12 minutes
  instead of 30 seconds. To start over, remove the volume with
  `docker volume rm formhandler_vendor` and run `composer install` again.
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
