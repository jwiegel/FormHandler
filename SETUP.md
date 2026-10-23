# FormHandler – Entwicklungsumgebung

Entwickelt und getestet wird mit **PHP 8.2 im Docker-Container**. Eine lokale
PHP-Installation ist nicht nötig. GitHub Actions nutzt dieselbe PHP-Version.

## Voraussetzungen

- Docker mit Docker Compose (z. B. Docker Desktop)

## Einrichten

```sh
# Image bauen (PHP 8.2 CLI mit gd, mbstring, mysqli, pdo_mysql, pdo_sqlite, zip, intl, xdebug, Composer)
docker compose build

# Abhängigkeiten installieren
docker compose run --rm php composer install
```

## Tests und statische Analyse

```sh
# PHPUnit (dauert ca. 5 Minuten, weil jeder Test in einem eigenen Prozess läuft)
docker compose run --rm php vendor/bin/phpunit -c tests/uts/phpunit.xml --no-coverage

# einzelne Tests
docker compose run --rm php vendor/bin/phpunit -c tests/uts/phpunit.xml --no-coverage --filter dbFormhandler_TextFieldTest

# mit Code Coverage (HTML-Report in tests/uts/_coverage)
docker compose run --rm php vendor/bin/phpunit -c tests/uts/phpunit.xml

# PHPStan
docker compose run --rm php vendor/bin/phpstan analyse -c phpstan.neon --memory-limit=1G
```

Eine Shell im Container: `docker compose run --rm php bash`

## GitHub Actions

| Workflow | Datei | Inhalt |
|---|---|---|
| PHPUnit | `.github/workflows/phpunit.yml` | Tests mit Coverage auf PHP 8.2 |
| PHPStan | `.github/workflows/phpstan.yml` | statische Analyse auf PHP 8.2 |

Beide laufen bei jedem Push und Pull Request.

## Hinweise

- **PHP-Version:** `composer.json` legt `config.platform.php` auf 8.2 fest. Composer
  wählt deshalb nur Pakete, die mit PHP 8.2 laufen – auch wenn es mit einer neueren
  PHP-Version aufgerufen wird. `composer.lock` ist eingecheckt.
- **Datenbank in Tests:** Die DB-Tests laufen gegen eine SQLite-Datenbank im
  Arbeitsspeicher (`tests/uts/helper/dbFormhandlerTestCase.php`). Ein Datenbankserver
  ist nicht nötig. Für Unit-Tests ohne Datenbank lässt sich `DatabaseAdapter` mit
  `createMock()` ersetzen.
- **`DOCUMENT_ROOT` in Tests:** `src/includes/config.inc.php` braucht
  `$_SERVER['DOCUMENT_ROOT']`, das PHP auf der Kommandozeile leer lässt.
  `tests/uts/phpunit-prepend.php` setzt es als `auto_prepend_file`, damit es auch in
  den isolierten Testprozessen von PHPUnit ankommt. Im Container hängt
  `docker-compose.yml` dafür `tests/uts/php.ini` ein, in GitHub Actions setzt es
  `setup-php` über `ini-values`.
