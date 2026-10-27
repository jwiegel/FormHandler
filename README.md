# FormHandler

[![GitHub license](https://img.shields.io/badge/license-GPLv2-blue.svg)](https://raw.githubusercontent.com/FormHandler/FormHandler/master/LICENSE)
[![Build Status](https://travis-ci.org/FormHandler/FormHandler.svg?branch=master)](https://travis-ci.org/FormHandler/FormHandler)
[![PHPUnit](https://github.com/tombrain/FormHandler/actions/workflows/phpunit.yml/badge.svg)](https://github.com/tombrain/FormHandler/actions/workflows/phpunit.yml)
[![PHPStan](https://github.com/tombrain/FormHandler/actions/workflows/phpstan.yml/badge.svg)](https://github.com/tombrain/FormHandler/actions/workflows/phpstan.yml)

FormHandler is a PHP written "module" which allows you to create dynamic forms in an easy way.
So easy that you can build a fully working form, including field validations, within 10 lines!

## Installation

Install the latest version with

```bash
$ composer require formhandler/formhandler
```

When the vendor directory is outside the root directory of your web application use a symlink on the FHTML directory to make it publicly available.

For example: map vendor/formhandler/formhandler/src/FHTML to /FHTML/ of your website root. Then add to your application

```php
<?php

fh_conf('FH_FHTML_DIR', '/FHTML/');
```

**File manager:** `FHTML/filemanager` (used by `browserField()` and by `editor()` with a
`$path`) has no login: whoever can request it can upload files and list folders. It is
off unless the web server sets the environment variable `FH_FILEMANAGER_ENABLED=1`
(Apache: `SetEnv FH_FILEMANAGER_ENABLED 1`). Only enable it for a folder that is
protected by a login, e.g. the admin area of your site.

## Basic Usage

```php
<?php

//FormHandler and all its classes are loaded by Composer's autoloader
require __DIR__ . '/vendor/autoload.php';

//create a new FormHandler object
$form = new FormHandler();

//some fields.. (see manual for examples)
$form->textField("Name", "name", FH_STRING, 20, 40);
$form->textField("Age", "age", FH_INTEGER, 4, 2);

//button for submitting
$form->submitButton();

//set the 'commit-after-form' function
$form->onCorrect('doRun');

//display the form
$form->flush();

//the 'commit-after-form' function
function doRun($data)
{
    echo "Hello ". $data['name'].", you are ".$data['age'] ." years old!";
}
```

### Documentation

For an extended version of the documentation please check our manual located at
http://www.fh3.nl/manual/manual.html

The manual describes version 3. The changes of every version are in `CHANGELOG.md`,
and `examples/` has a page for every element (see `SETUP.md`).

### New in 4.x (not in the manual)

**Database connection with Doctrine DBAL.** `dbInfo()` and `dbConnect()` work as before.
An open connection has to be a DBAL connection:

```php
$conn = \Doctrine\DBAL\DriverManager::getConnection([
    'driver' => 'pdo_mysql', 'host' => 'localhost',
    'user' => 'user', 'password' => 'secret', 'dbname' => 'mydb',
]);
$form = new dbFormHandler();
$form->setConnectionResource($conn, 'users');
```

**HTML5 date field** (4.2). The browser shows its own date picker, the value is always
`yyyy-mm-dd`. The mask is only used in view mode; `true` sets today's date in a new form.

```php
// html5DateField($title, $name, $validator, $mask, $setCurDate, $extra)
$form->html5DateField('Birthday', 'birthday', FH_NOT_EMPTY, 'd.m.Y');
```

**The editor HTML is sanitized on the server** (4.4). `editor()` only keeps safe HTML:
scripts, event handlers, `javascript:` links, iframes and forms are removed, also when the
request is sent by hand. The `style` attribute is removed as well; to keep colors and
font sizes, extend `Editor`:

```php
class StyledEditor extends Editor
{
    protected function isStyleAllowed()
    {
        return true;
    }
}
```

`examples/editor.php` shows how to add such an editor to a form; override
`getSanitizerConfig()` for another allow list.

**Errors are exceptions** (4.1). A wrong use of FormHandler (unknown language, missing
callback, invalid date mask, editing a record that does not exist, ...) throws a
`FormHandlerException` instead of `trigger_error(..., E_USER_ERROR)`.

**Composer only** (4.3). All classes are loaded by Composer's autoloader, so you can extend
e.g. `TextArea` in your own fields. Including `class.FormHandler.php` without Composer is
no longer supported.

### License

FormHandler is licensed under the GNU Lesser General Public License Version 2.1 - see the `LICENSE` file for details
