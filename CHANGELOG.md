# Changelog

All notable changes to this project are documented in this file.
The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/)
and the project follows [Semantic Versioning](https://semver.org/).

## [4.0.0] - 2026-10-23

FormHandler now uses [Doctrine DBAL](https://www.doctrine-project.org/projects/dbal.html)
for all database access. The bundled Yadal abstraction layer is gone.

### Breaking changes

- `dbFormHandler::setConnectionResource()` only accepts a `Doctrine\DBAL\Connection`.
  Native `mysqli`, `pgsql` or `mssql` connection resources are no longer supported.
- The database types `access` and `odbc` are no longer supported.
- Yadal is removed: the `newYadal()` function, the `Yadal*` classes and the
  `FH_YADAL_DIR` config constant no longer exist.
- New dependency: `doctrine/dbal ^4.2`.
- Requires PHP 8.2 or newer. Composer's platform check is enabled again, so the
  autoloader stops on older PHP versions.

#### Migrating from 3.x

`dbInfo()` and `dbConnect()` keep working with the same database types.
If you passed an open connection, pass a DBAL connection instead:

```php
// 3.x
$form->setConnectionResource($mysqli, 'users', 'mysqli');

// 4.0
$conn = \Doctrine\DBAL\DriverManager::getConnection([
    'driver'   => 'mysqli',
    'host'     => 'localhost',
    'user'     => 'user',
    'password' => 'secret',
    'dbname'   => 'mydb',
]);
$form->setConnectionResource($conn, 'users');
```

### Added

- `DbalAdapter`, a `DatabaseAdapter` on top of Doctrine DBAL. It works with every
  database DBAL supports (MySQL, MariaDB, PostgreSQL, SQLite, SQL Server, ...).
- `DatabaseAdapter` interface with every method dbFormHandler and the db* fields use.
  It can be replaced with `createMock(DatabaseAdapter::class)` in unit tests.
- `dbFormHandler::setDatabaseAdapter()` to use any `DatabaseAdapter` implementation.
- `TextField::getTypedField($type)` (protected), so that subclasses can render HTML5
  input types like `date` or `email`.
- `dbInfo()` accepts the type `sqlite` (the database name is the file) and every
  DBAL driver name, e.g. `pdo_mysql`.
- Example pages for every element of the manual in `examples/`, served by the new
  Docker service `web` (see `SETUP.md`). The workflow `Examples` requests every page
  on each push.

### Changed

- `dbInfo()`/`dbConnect()` open the connection with DBAL. The old types map to DBAL
  drivers: `mysql`/`mysqli` to `mysqli`, `postgresql`/`postgres`/`pgsql` to
  `pdo_pgsql`, `mssql` to `pdo_sqlsrv`.
- The database tests run against an in-memory SQLite database instead of a mocked
  driver, and check the stored records instead of the generated SQL.
- `SETUP.md` describes the Docker setup.
- PHPUnit 11.5 instead of 9.6.
- `phpunit/phpunit` and `phpstan/phpstan` moved to `require-dev`, so projects that
  install FormHandler no longer get them.

### Removed

- `src/yadal` with the MySQL, MySQLi, PostgreSQL, MSSQL, Access and ODBC drivers.
- The test dependencies `dibi/dibi`, `jdorn/sql-formatter`, `tombrain/phpunit-mock-dibi`,
  `tombrain/phpunit-mock-db` and `tombrain/phpunit-sql`.
- `phan/phan`, which was required but never used.
- The NetBeans project files (`nbproject/`).

### Fixed

- The Composer branch alias (`extra.branch-alias`) had no effect.
- **Security:** `addJunctionTable()` put the submitted values and the edit id into
  its SQL without escaping (SQL injection).
- `FH_URL` rejected URLs with a scheme like `https://www.example.org`; it accepts an
  optional scheme, port, path and query now.
- `FH_IP` accepted numbers above 255 and any character instead of the last dot.
- `FH_BOOL` accepted every value starting with `1` (e.g. `10`), and `Validator::IsBool()`
  converted `"false"` to `true`.
- `Validator::IsEmail()` and four other validators could not be called statically.
- The captcha image of `CaptchaField` was broken when PHP displays deprecations
  (float to int conversions in the bundled securimage, PHP 8.1+).

## [3.4.0] - 2026-10-23

### Added

- Docker development environment with PHP 8.2 (`docker-compose.yml`).
- GitHub Actions workflows for PHPUnit and PHPStan on PHP 8.2.
- First version of the `DatabaseAdapter` interface and
  `dbFormHandler::setDatabaseAdapter()`.

### Changed

- Composer resolves dependencies for PHP 8.2 (`config.platform.php`);
  `composer.lock` is now committed.
- PHPStan 2.x instead of 1.x.

### Fixed

- `Yadal::result()` used `each()`, which no longer exists in PHP 8.
- `DateTextField` computed the year 2600 instead of 2026 for two-digit years like `26-10-23`.
- `ListField` stored empty values that are not one of the options.
- `PassField` and `UploadField` created the dynamic property `_dontSave` on forms
  without database (deprecated since PHP 8.2).
- `Button` triggers an error for an invalid form object.
- Several always-true checks and invalid PHPDoc tags.

## [3.3.10] - 2025-05-03

### Fixed

- Transparent GIFs lost their transparency when resized.
- `htmlentities()` was called with `null` (deprecated since PHP 8.1).
- Possible access to an undefined array index.
- Type fixes in `ListField`.

## [3.3.9] - 2022-12-06

### Added

- PHPUnit test suite, run on GitHub Actions.
- PHPStan analysis on level 4.
- Composer autoloading.

### Fixed

- PHP 8 compatibility fixes throughout the code.
- Only empty fields are unset when saving.

[4.0.0]: https://github.com/tombrain/FormHandler/compare/v3.4.0...v4.0.0
[3.4.0]: https://github.com/tombrain/FormHandler/compare/v3.3.10...v3.4.0
[3.3.10]: https://github.com/tombrain/FormHandler/compare/v3.3.9...v3.3.10
[3.3.9]: https://github.com/tombrain/FormHandler/compare/v3.3.8...v3.3.9
