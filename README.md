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

**Captcha with gregwar/captcha.** `captchaField()` needs the package `gregwar/captcha`
(`composer require gregwar/captcha`), FormHandler does not install it for every project.
The image is part of the form and its code is kept in the PHP session.

**Composer only** (4.3). All classes are loaded by Composer's autoloader, so you can extend
e.g. `TextArea` in your own fields. Including `class.FormHandler.php` without Composer is
no longer supported.

**CSRF protection** (4.6). Every form has a hidden field `<form name>_csrf` with a token of
the PHP session. A form posted without the right token (e.g. from another website) is not
processed; it is shown again with the message "The form has expired or was not sent from
this page".

- It needs a PHP session. FormHandler starts it when no output was sent yet; otherwise
  call `session_start()` before any output. Without a session the form warns and is not
  protected.
- Own AJAX requests that post a form have to send the token. Requests that post all fields
  of the form, including the hidden ones, already do; otherwise add it yourself:

  ```php
  // in the page: pass the token to your JavaScript
  echo '<script>var formToken = ' . json_encode($form->getCsrfToken()) . ';</script>';
  // in the request: POST FormHandler_csrf=<formToken> (the field is "<form name>_csrf")
  ```

- Turn it off for a single form with `$form->setCsrfProtection(false)`, or for all forms
  with `fh_conf('FH_CSRF_PROTECTION', false)` before the first form is created (the
  `fh_conf()` values become constants then).

### Images and files in the editor

The bundled file manager is removed (4.7): it had no login. Uploading or choosing files
has to happen behind the login of your application, so link your own scripts with the
`$config` of `editor()`. Both scripts must check the login themselves.

**Upload images** (the simplest way). CKEditor uploads pasted or dropped images and the
images of the "Upload" tab of the image dialog to your script:

```php
$form->editor('Text', 'text', config: [
    'uploadUrl'            => '/admin/editor-upload.php',
    'filebrowserUploadUrl' => '/admin/editor-upload.php?responseType=json',
]);
```

```php
<?php
// /admin/editor-upload.php: the file comes in the field "upload", the answer is JSON
session_start();
header('Content-Type: application/json');
if (empty($_SESSION['user'])) { // your own login check
    exit(json_encode(['uploaded' => 0, 'error' => ['message' => 'Not logged in']]));
}

$file = $_FILES['upload'] ?? null;
$types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
$type = $file && $file['error'] === UPLOAD_ERR_OK ? mime_content_type($file['tmp_name']) : '';
if (!isset($types[$type])) {
    exit(json_encode(['uploaded' => 0, 'error' => ['message' => 'Only images can be uploaded']]));
}

// an own file name: never use the name from the browser as path
$name = bin2hex(random_bytes(8)) . '.' . $types[$type];
move_uploaded_file($file['tmp_name'], __DIR__ . '/../images/editor/' . $name);

echo json_encode(['uploaded' => 1, 'fileName' => $name, 'url' => '/images/editor/' . $name]);
```

**Browse files on the server.** A file manager such as
[elFinder](https://github.com/Studio-42/elFinder) (`composer require studio-42/elfinder`,
BSD license) can be opened by the "Browse Server" buttons of the image and link dialogs:

```php
$form->editor('Text', 'text', config: [
    'filebrowserBrowseUrl' => '/admin/elfinder.html',
]);
```

- `/admin/elfinder.html` shows elFinder (it needs jQuery and jQuery UI, only on this
  page) and gives the chosen file back to the editor:

  ```js
  // the CKEditor dialog which opened the window
  var funcNum = new URLSearchParams(location.search).get('CKEditorFuncNum');
  $('#elfinder').elfinder({
      url: '/admin/elfinder-connector.php',
      getFileCallback: function (file) {
          window.opener.CKEDITOR.tools.callFunction(funcNum, file.url);
          window.close();
      }
  });
  ```

- `/admin/elfinder-connector.php` is the connector of elFinder (see its
  `connector.minimal.php`): check the login first, then allow only one folder, e.g. with
  the roots option `'path' => __DIR__ . '/../files/', 'URL' => '/files/'` and an
  `uploadAllow` list of file types.

A text field for a file (the former `browserField()`) works the same way: a
`textField()` with your own button which opens the file manager and writes the chosen URL
into the field.

### License

FormHandler is licensed under the GNU Lesser General Public License Version 2.1 - see the `LICENSE` file for details
