# FormHandler - Lokale Entwicklungsumgebung Setup

## PHP-Installation (bereits erledigt)
PHP 8.2.31 ist konfiguriert unter: `D:\PHP-Entwicklung\php-8.2.31-Win32-vs16-x64`

## Nächste Schritte

### 1. Composer installieren

**Option A: Composer in PHP-Verzeichnis installieren (empfohlen)**
```cmd
cd D:\PHP-Entwicklung\php-8.2.31-Win32-vs16-x64
curl -sS https://getcomposer.org/installer | php
```

**Option B: Composer manuell herunterladen**
1. Download: https://getcomposer.org/download/
2. Speichern als: `D:\PHP-Entwicklung\php-8.2.31-Win32-vs16-x64\composer.phar`

### 2. Dependencies installieren
```cmd
.\composer.cmd install
```

### 3. Tests ausführen
```cmd
.\runAllUTs.cmd
```

## Verfügbare Skripte

- **`setup-env.cmd`** - Setzt die Umgebung für das Terminal (PATH-Variable)
- **`composer.cmd`** - Composer-Wrapper für lokales PHP
- **`runAllUTs.cmd`** - Führt alle Unit-Tests aus
- **`runPhpstan.cmd`** - Führt statische Code-Analyse aus

## Verwendung im Terminal

Um PHP und Composer im Terminal zu verwenden:
```cmd
.\setup-env.cmd
php --version
composer --version
```

Danach können Sie normale PHP/Composer-Befehle ausführen:
```cmd
php -v
composer install
composer update
```

## PHP-Konfiguration

Die PHP-Installation befindet sich unter:
`D:\PHP-Entwicklung\php-8.2.31-Win32-vs16-x64`

Benötigte Extensions (in `php.ini` aktivieren):
- ✅ mysqli
- ✅ mbstring
- ✅ gd
- ⚠️ xdebug (optional, für Code Coverage)

## Hinweise

- Die CMD-Dateien (`runAllUTs.cmd`, `runPhpstan.cmd`) setzen den PHP-Pfad automatisch
- Keine systemweite PHP-Installation erforderlich
- Der PATH wird nur temporär für das jeweilige Skript gesetzt
- Beim `composer install` werden lokale Kompatibilitäts-Patches automatisch angewendet (Composer-Patches Plugin)
