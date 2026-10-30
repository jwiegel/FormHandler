# Changelog

All notable changes to this project are documented in this file.
The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/)
and the project follows [Semantic Versioning](https://semver.org/).

## [4.7.0] - 2026-10-30

### Added

- `FormHandler::getField()` returns the object of a field, e.g. to use its getters. It
  throws a `FormHandlerException` for an unknown field.
- Getters for what was only reachable through public properties: `Field::getErrorMessage()`
  (the error without `FH_ERROR_MASK`), `SelectField::getOptions()`, `RadioButton::getOptions()`,
  `TimeField::getHourField()` and `getMinuteField()`.
- `Field::getExtraAfter()`, the counterpart of `setExtraAfter()`.
- The scripts of `linkSelectFields()` may answer JSON (`[["1", "Text"], "Other"]`); the
  javascript which sets `options` still works.

### Changed

- PHPStan checks on level 6, without a baseline, now also `ImageConverter`. All classes have
  PHPDoc types for their properties, parameters and return values, and all private
  properties and methods also native types; protected and public members keep PHPDoc types
  only, so existing subclasses which override them still work.
- The internal properties of `DateTextField` and `jsDateTextField` (declared with `var`, so
  public) are protected.
- `dbFormHandler` calls `getInsertId()` without the table name, as the `DatabaseAdapter`
  interface defines it (the argument was a Yadal leftover and ignored).
- `captchaField()` uses the package `gregwar/captcha` instead of the bundled Securimage 1.0
  from 2008. **Projects using `captchaField()` have to run `composer require gregwar/captcha`**;
  without it the field throws a `FormHandlerException`. The image is part of the form (a
  data URI), so there is no image script in `FHTML` any more. A code works only once, and
  `FH_CAPTCHA_LENGTH` and `FH_CAPTCHA_WIDTH` are used now.
- The AJAX validator (`enableAjaxValidator()`) needs no jQuery any more: plain javascript
  (`fetch()`), and `validate.php` answers JSON, which is shown as text. The second parameter
  of `enableAjaxValidator()` (include jQuery) is not used any more. Messages are put into
  the javascript with `json_encode()` instead of `addslashes()`.
- `colorPicker()` uses the color input of the browser instead of jscolor 1.0.9: a color input
  (without a name) next to the text field fills it. The value is unchanged (`FFCC00`, empty
  when no color is chosen), so stored colors and validators keep working.
- The help texts (`setHelpText()`) are shown by the small `FHTML/js/helptext.js` instead of
  overLIB 4.21 (2004), also when the help icon gets the focus with the keyboard. The text is
  HTML and the title text, as before. Text and title are in `data-fh-help` attributes now:
  **a project with its own `FH_HELP_MASK` that calls `overlib()` has to change it** (see
  `config.inc.php`). `FH_USE_OVERLIB` keeps its name and now includes `helptext.js`.
- `jsDateField()` and `jsDateTextField()` open the date picker of the browser instead of
  CalendarPopup (2003). The fields, their values and the year interval are unchanged; the
  calendar shows the language and the first weekday of the browser, so
  `FH_JSCALENDARPOPUP_USE_DROPDOWN` and `FH_JSCALENDARPOPUP_STARTDAY` are not used any more.
  `cal_<field>.setReturnFunction()`, `set<field>Values()` and `LZ()` still work. New forms
  should rather use `html5DateField()`.
- `setAutoComplete()` and `setAutoCompleteAfter()` show a list of suggestions under the
  field (in the font of the field, class `fh_autocomplete`; arrow keys, enter, escape and
  the mouse) instead of completing the text while typing. The options are in an HTML5
  `datalist`, escaped as HTML, which the browser shows when javascript is off.
- `textSelectField()` and `dbTextSelectField()` use the same list: a click shows all
  options, typing filters them. The default `FH_TEXTSELECT_MASK` and
  `FH_TEXTSELECT_OPTION_MASK` put the options into a `datalist`; own masks which call
  `FH_SET_TEXTSELECT()` still get these functions.
- `linkSelectFields()` loads the options with `fetch()` instead of the old XMLHttpRequest
  wrapper with Internet Explorer fallbacks, and encodes the values it sends.

### Deprecated

- The public properties of the fields (`$_sError`, `$_viewMode`, `$_aOptions`,
  `$_bUseArrayKeyAsValue`, `$_oHour`, `$_oMinute`, `$_bRequired`): use the getters and
  setters; they become protected in 5.0. `ColorPicker::$sTitleAdd` is not used any more.
- `browserField()` (`BrowserField`) and `Editor::setServerPath()`: the file manager behind
  them is removed (see below). `browserField()` is a plain text field now, the `$path`
  of `editor()` is not used any more.

### Removed

- The PHP 4 fallbacks for `array_key_exists()` and `array_fill()` in `config.inc.php`.
- The bundled Securimage in `src/FHTML/securimage` (see `captchaField()` above).
- The bundled jQuery 1.4.2 (2010, with known security issues); only the AJAX validator used it.
- The bundled jscolor 1.0.9 and the unused images of an older color picker (`FHTML/images/ColorPicker`).
- The bundled overLIB 4.21 with its Internet Explorer workaround `overlib_hideform.js`.
- The bundled CalendarPopup (`calendar_popup.js`, `calendar_hideselect.js`).
- **The file manager `FHTML/filemanager`** (FCKeditor 2, up to 2010). It had no login and was
  off since 4.5 unless the web server set `FH_FILEMANAGER_ENABLED=1`. `browserField()` has
  no button any more, and `editor()` with a `$path` no longer shows "Browse server" and
  "Upload". A project which needs them can link its own file browser behind its login,
  e.g. with `editor(..., $config)` and `filebrowserBrowseUrl`.
- `FormHandler::isUnittestmode()` and the unit test mode of `UploadField`. The upload is in
  the overridable methods `UploadField::_isUploadedFile()` and `_moveUploadedFile()`, the
  unit tests replace them and so test the same code as a real upload.

### Fixed

- The options of `textSelectField()` and `dbTextSelectField()` were not escaped: a value
  with HTML (e.g. from the database) was put into the page as HTML.
- The AJAX validator only checked the first validator of a field (`"IsString|IsDigit"`).
- `getFileInfo()` and `isUploaded()` rejected subclasses of `UploadField` ("the field is not
  an uploadfield"), they compared the class name.

## [4.6.4] - 2026-10-30

### Fixed

- The texts of most languages showed broken characters on UTF-8 pages (the default
  `FH_HTML_ENCODING`), e.g. "ung�ltig" in German: 11 language files were saved in old
  encodings (ISO-8859-1/-2/-9, Windows-1256, GB2312, Big5). All language files are UTF-8
  now, and FormHandler converts the texts when a page uses another `FH_HTML_ENCODING`.
- Characters lost by an old conversion are restored in the Czech, Serbian and Chinese
  language files (they had "?" or replacement characters instead of e.g. "č", "с", "九").

## [4.6.3] - 2026-10-30

### Security

- `FHTML/ajax/validate.php` (the AJAX validator) is reachable without a form. It put the
  parameters `msg` and `msgbox` of the request unescaped into its HTML and javascript
  answer, so a prepared link could run javascript on the website (reflected XSS). The
  message is escaped now and `msgbox` has to be the id of an error box.
- It also included the classes from a path of the request (`includedir`); it uses its own
  directory now. The form does not send the server path `FH_INCLUDE_DIR` to the browser
  any more.

## [4.6.2] - 2026-10-28

### Fixed

- `enableAjaxValidator()` stopped every form with a fatal error since the conversion to
  PHP 7: `AjaxValidator` read the protected properties `_fields` and `_customMsg` of the
  form. `AjaxValidator::CreateObservers()` now gets them as parameters.

## [4.6.1] - 2026-10-28

### Fixed

- The unique check of `dbFormHandler` read the undefined property `_quoteNumbers` of the
  database adapter (a Yadal leftover) instead of calling `quoteNumbers()`: a PHP warning
  for every number in a unique field.

## [4.6.0] - 2026-10-28

### Security

- Every form is protected against cross-site request forgery (CSRF). FormHandler had no
  protection, so another website could make the browser of a logged-in user submit a
  form (e.g. save or change a record). Now each form has a hidden field
  `<form name>_csrf` with a token of the PHP session; a posted form without the right
  token is not processed, the form is shown again with the message "The form has
  expired or was not sent from this page" (text 44 in the language files).
  - On by default (config `FH_CSRF_PROTECTION`), per form `setCsrfProtection(false)`.
  - It needs a PHP session: FormHandler starts it if no output was sent yet. Start it
    yourself before any output otherwise; without a session the form warns and is not
    protected.
  - Own AJAX requests that post a form have to send the token, see `getCsrfToken()`.
    Requests that post all fields of the form (including the hidden ones) work as before.
  - The token is not part of the values passed to `onCorrect`/`onSaved`, the database or
    `getAsMailBody()`.

## [4.5.1] - 2026-10-27

### Fixed

- Values loaded from the database are strings (or `null`) again, like with the Yadal
  drivers of 3.x. Since 4.0 `DbalAdapter` returned ints and floats as the drivers do
  since PHP 8.1, so code with `strict_types` that compared or trimmed the loaded values
  (e.g. to log changed values) stopped with a `TypeError`.

## [4.5.0] - 2026-10-27

### Security

- The file manager in `src/FHTML/filemanager` (used by `browserField()` and the
  editor's "Browse server") has no login, and its connector was enabled: anyone who
  could request it could upload files (images, documents, archives, ...), create
  folders and list folders. `ServerPath` came unchecked from the URL, so with `..`
  this worked outside the document root too.
  - The connector is off unless the web server sets the environment variable
    `FH_FILEMANAGER_ENABLED=1`. Only enable it behind a login.
  - `ServerPath` with `..`, backslashes, colons or control characters turns the
    connector off.
  - `delete.php` is removed: it deleted any file below the document root without a
    path check (only a session variable `login`), and its trash icon in the file list.
  - `editor()` only links the file manager when it gets a `$path`.

### Fixed

- The file manager connector printed a PHP warning (`path_info`) into its XML answer
  on Apache with PHP 8, so the file manager could not read it.

## [4.4.0] - 2026-10-27

### Security

- `editor()` sanitizes its HTML on the server ([tombrain/FormHandler#22](https://github.com/tombrain/FormHandler/issues/22)).
  It accepted any HTML, so a request sent by hand could store scripts, event handlers
  (`onerror`, `onmouseover`) or `javascript:` links (stored XSS). Every value (posted,
  loaded from the database, `setValue()`) now only keeps the safe elements and attributes
  of `symfony/html-sanitizer`: text, lists, tables, absolute and relative links and images.
  Scripts, event handlers, `javascript:`/`data:` links, iframes and forms are removed.
  HTML that is already stored is sanitized when it is loaded and saved again.

### Added

- `Editor::isStyleAllowed()` and the config `FH_EDITOR_ALLOW_STYLE` (default `false`):
  keep the `style` attribute for colors, font sizes and alignment. A style with `url()`,
  `expression()`, `javascript:`, `@import` or CSS escapes is removed (`EditorStyleSanitizer`).
  Set it with `fh_conf('FH_EDITOR_ALLOW_STYLE', true)` before the first form, or override
  `isStyleAllowed()` in a subclass. `Editor::getSanitizerConfig()` can be overridden for
  another allow list.
- New dependency: `symfony/html-sanitizer` (`^7.4 || ^8.0`).

### Fixed

- `editor()` with `$path = false` (or `Editor::setServerPath(false)`) did not turn the
  file browser of the editor off: it wrote to a misspelled property, so the "Browse
  server" button kept linking to the file manager.
- The Composer branch alias of `dev-master` was still `4.0.x-dev`, so `dev-master`
  did not satisfy constraints like `^4.3`. It is `4.x-dev` now and fits every 4.x release.

## [4.3.0] - 2026-10-27

### Changed

- All classes of FormHandler (fields, buttons, `dbFormHandler`, `Validator`,
  `ImageConverter`, ...) are loaded by Composer's autoloader (`classmap`). The
  `require_once` of these classes in the form methods are removed, so projects can
  extend e.g. `TextArea` or `TextField` without loading the file themselves, and
  PHPStan and IDEs find the classes.
- FormHandler must be loaded with Composer's autoloader (`vendor/autoload.php`).
  Including `class.FormHandler.php` without Composer is no longer supported: the field
  and button classes would not be found.

## [4.2.1] - 2026-10-27

### Fixed

- PHPDoc: `setValue()` of `FormHandler` and `dbFormHandler` accepts any value
  (`mixed`), e.g. an `int` id or `null`; it was documented as `string`. `DbalAdapter`
  documents `recordCount()`, `getRecord()` and `result()` with `mixed`, as they already
  accept anything that is not a `DbalResult`, and `DatabaseAdapter::query()` has a
  PHPDoc. Projects that run PHPStan on a high level no longer get false errors.

## [4.2.0] - 2026-10-27

### Added

- `html5DateField()` and the class `Html5DateField`: a date field rendered as HTML5
  `<input type="date">`, so the browser shows its own date picker. The value is always
  `yyyy-mm-dd`; the mask (same chars as `dateTextField()`) is only used in view mode.
  `$setCurDate` sets today's date in a new form. Invalid dates fail the validation, and
  `getAsArray()` returns `null` with a warning, like `dateTextField()`.
- Example page `examples/html5datefield.php`.

## [4.1.0] - 2026-10-27

### Changed

- Errors in the use of FormHandler throw a `FormHandlerException` (a `RuntimeException`)
  instead of `trigger_error(..., E_USER_ERROR)`, which is deprecated since PHP 8.4.
  This affects an unknown language, a missing `onCorrect`/`onSaved` callback or
  validator method, an invalid date mask, an invalid database adapter, connection or
  type, editing a record that does not exist, and a failed move of an uploaded file.
  As before, the script stops unless the exception is caught.
- `DateTextField::getAsArray()` (and `getAsArray()` of the form for a date text field)
  reports an invalid date with `E_USER_WARNING` instead of `E_USER_ERROR` and returns
  `null`. The value can come from the user, so it no longer stops the script.
- PHPStan checks on level 5 instead of 4. The date fields cast the parts of a date to
  `int` before they pass them to `mktime()`, `checkdate()` and `date()`.

### Added

- GitHub Actions runs the tests on PHP 8.2, 8.3 and 8.4.
- The PHP version of the Docker container can be chosen with `FH_PHP_VERSION`.

### Fixed

- `UploadField` could not create an upload directory with more than one missing
  parent directory: it passed the path instead of the mode to `mkdir()` (`TypeError`
  since PHP 8).

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
- **Security:** the bundled CKEditor (`editor()`) is updated from 4.3.1 to 4.22.1, the
  last open source version of CKEditor 4 (GPL/LGPL/MPL). This fixes 9 of the 13 published
  CKEditor 4 vulnerabilities; the others are only fixed in the commercial 4.23+ LTS versions.
  The `samples` folder is not shipped any more, all 70 editor languages are. The skin is
  `moono-lisa` now; `moono` is gone and `setSkin('moono')` uses `moono-lisa`, and the toolbars no longer have the removed buttons
  `Flash`, `SpellChecker`, `DocProps` and `CreatePlaceholder`. `config.versionCheck` is
  off, so the users of a form don't see CKEditor's "version is not secure" notification.
  Remaining known issues of 4.22.1: CVE-2024-24815 (CDATA, medium), CVE-2024-43407 (only
  with the codesnippetgeshi plugin), CVE-2024-24816 and CVE-2023-4771 (only in the
  samples, not shipped). Sanitize the submitted HTML on the server ([tombrain/FormHandler#22](https://github.com/tombrain/FormHandler/issues/22)).
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
- `config.inc.php` no longer triggers a warning on the command line (empty
  `DOCUMENT_ROOT`); this broke consumers that load FormHandler via Composer in CLI.
- The constructor read `$_SERVER['REQUEST_METHOD']` without a guard, a warning on the
  command line.
- The error handler used the constant `E_STRICT`, a deprecation on every PHP error
  with PHP 8.4.
- `dateTextField()` with `$parseOtherPresentations` passed `null` to `str_replace()`
  when the field had no value (deprecation since PHP 8.1).
- **Security:** `addJunctionTable()` put the submitted values and the edit id into
  its SQL without escaping (SQL injection).
- `FH_URL` rejected URLs with a scheme like `https://www.example.org`; it accepts an
  optional scheme, port, path and query now.
- `FH_IP` accepted numbers above 255 and any character instead of the last dot.
- `FH_BOOL` accepted every value starting with `1` (e.g. `10`), and `Validator::IsBool()`
  converted `"false"` to `true`.
- `Validator::IsEmail()` and four other validators could not be called statically.
- The calendar popup of `jsDateField()`/`jsDateTextField()` threw a JavaScript error
  when the clicked element had no target, and the help text tooltips (overLIB) failed
  on Internet Explorer 10 and newer.
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
