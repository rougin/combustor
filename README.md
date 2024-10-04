# Combustor

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]][link-license]
[![Build Status][ico-build]][link-build]
[![Coverage Status][ico-coverage]][link-coverage]
[![Total Downloads][ico-downloads]][link-downloads]

Combustor is a utility package for [Codeigniter 3](https://codeigniter.com/userguide3/) that generates controllers, models, and views based on the provided database tables. It uses the [Describe](https://roug.in/describe/) library for getting columns from a database table and as the basis for code generation.

## Features

* Generates code based on the structure of the `Codeigniter 3` framework;
* Speeds up the code development for prototyping web applications;
* View templates can be based on Bootstrap and are upgradable; and
* Only worry on the database schema, and `Combustor` will do the rest.

## Installation

Extract the contents of the [latest Codeigniter 3 project](https://github.com/bcit-ci/CodeIgniter/archive/3.1.13.zip) first:

``` bash
$ wget https://github.com/bcit-ci/CodeIgniter/archive/3.1.13.zip
$ unzip 3.1.13.zip -d acme
```

Then configure the project's database connectivity settings:

```
$ cd acme
$ nano application/config/database.php
```

``` php
// acme/application/config/database.php

// ...

$db['default'] = array(
    'dsn'   => '',
    'hostname' => 'localhost',
    'username' => '',
    'password' => '',
    'database' => '',
    'dbdriver' => 'mysqli',
    
    // ...
);
```

Next is to proceed in installing `Combustor` via [Composer](https://getcomposer.org/):

``` bash
$ composer require rougin/combustor --dev
```

``` json
// acme/composer.json

{
  // ...

  "require-dev":
  {
    "mikey179/vfsstream": "1.6.*",
    "phpunit/phpunit": "4.* || 5.* || 9.*",
    "rougin/combustor": "~1.0"
  }
}
```

Lastly, install the ORM wrappers like `Wildfire` or `Doctrine`:

``` bash
$ vendor/bin/combustor install:wildfire
$ vendor/bin/combustor install:doctrine
```

> [!NOTE]
> Using the `install:wildfire` command installs [Wildfire](https://roug.in/wildfire/) package while the `install:doctrine` installs [Credo](https://roug.in/credo/) package.

## Reminders

Prior in executing any commands, kindly ensure that the **database tables are defined properly** (foreign keys, indexes, relationships, normalizations) in order to minimize the modifications after the code structure has been generated.

Also, please proceed first in generating models, views, or controllers to database tables that are having _**no relationship with other tables**_ in the database.

> [!TIP]
> `Combustor` will generate controllers, models, or views based on the specified database schema. If there's something wrong in the specified database schema, `Combustor` will generate a bad codebase.

## Commands

### `create:layout`

Create a new header and footer file.

**Options**

* `--bootstrap` - adds styling based on Bootstrap

**Example**

``` bash
$ vendor/bin/combustor create-layout --bootstrap
```

### `create:controller`

Create a new HTTP controller.

**Arguments**

* `table` - name of the database table

**Options**

* `--doctrine` - generates a Doctrine-based controller
* `--wildfire` - generates a Wildfire-based controller

> [!NOTE]
> If either `Wildfire` or `Doctrine` is installed, no need to specify it as option for executing a specified command (e.g. `--wildfire`). However if both are installed, a command must have a `--wildfire` or `--doctrine` option added.

**Example**

``` bash
$ vendor/bin/combustor create:controller users --wildfire
```

### `create:model`

Create a new model.

**Arguments**

* `table` - name of the database table

**Options**

* `--doctrine` - generates a Doctrine-based model
* `--wildfire` - generates a Wildfire-based model

**Example**

``` bash
$ vendor/bin/combustor create:model users --wildfire
```

### `create:repository`

Create a new entity repository.

**Arguments**

* `table` - name of the database table

**Example**

``` bash
$ vendor/bin/combustor create:repository users
```

> [!NOTE]
> This command is only applicable to a [Doctrine](https://roug.in/credo) implementation.

### `create:view`

Create view templates.

**Arguments**

* `table` - name of the database table

**Options**

* `--bootstrap` - adds styling based on Bootstrap
* `--doctrine` - generates Doctrine-based views
* `--wildfire` - generates Wildfire-based views

**Example**

``` bash
$ vendor/bin/combustor create:view users --bootstrap
```

### `create:scaffold`

Create a new HTTP controller, model, and view templates.

**Arguments**

* `table` - name of the database table

**Options**

* `--bootstrap` - adds styling based on Bootstrap
* `--doctrine` - generates a Doctrine-based controller, model, and views
* `--wildfire` - generates a Wildfire-based controller, model, and views

**Example**

``` bash
$ vendor/bin/combustor create:scaffold users --bootstrap --wildfire
```

### `install:doctrine`

Install the [Doctrine](https://roug.in/credo/) package.

**Example**

``` bash
$ vendor/bin/combustor install:doctrine
```

> [!NOTE]
> * This command will be available if `Doctrine` is not installed in the project.
> * It also adds a `Loader.php` in the `core` directory. The said file is used for loading custom repositories extended to `EntityRepository`.

### `install:wildfire`

Install the [Wildfire](https://roug.in/wildfire/) package.

**Example**

``` bash
$ vendor/bin/combustor install:wildfire
```

> [!NOTE]
> This command will be available if `Wildfire` is not installed in the project.

### `remove:doctrine`

Remove the [Doctrine](https://roug.in/credo/) package.

**Example**

``` bash
$ vendor/bin/combustor remove:doctrine
```

> [!NOTE]
> This command will be available if `Doctrine` is installed in the project.

### `remove:wildfire`

Remove the [Wildfire](https://roug.in/wildfire/) package.

**Example**

``` bash
$ vendor/bin/combustor remove:wildfire
```

> [!NOTE]
> This command will be available if `Wildfire` is installed in the project.

## Using `combustor.yml`

`Combustor` currently works out of the box after the configuration based on `Installation`. However, using a `combustor.yml` can be used for complex setups like specifying the new application path and excluding columns:

``` yaml
# combustor.yml

app_path: %%CURRENT_DIRECTORY%%/Sample

excluded_fields:
  - created_at
  - updated_at
  - deleted_at
```

To create a `combustor.yml`, simply run the `initialize` command:

``` bash
$ vendor/bin/combustor initialize
[PASS] "combustor.yml" added successfully!
```

### `app_path`

This property specifies the `application` directory. It may updated to any directory (e.g., `acme/application`, `acme/config`, etc.) as long it can detect the `config/config.php` file from the defined directory:

``` yaml
# combustor.yml

app_path: %%CURRENT_DIRECTORY%%/Sample

# ...
```

> [!NOTE]
> `Combustor` will try to check the path specified in `app_path` if it is a valid `Codeigniter 3` project. Then it will perform another check if the `application` directory exists or if the `config` directory can be accessed directly from the directory defined in `app_path`.

### `excluded_fields`

Specified fields in this property are excluded from generation to the following templates:

* `controllers`
* `models`
* `views` (only for `create` and `edit` templates)

``` yaml
# combustor.yml

# ...

excluded_fields:
  - created_at
  - updated_at
  - deleted_at
```

> [!NOTE]
> By default, the timestamps are added when creating a `combustor.yml` for the first time as they are usually populated automatically by installed ORMs such as `Wildfire` or `Doctrine`.

## Changelog

Please see [CHANGELOG][link-changelog] for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Credits

- [All contributors][link-contributors]

## License

The MIT License (MIT). Please see [LICENSE][link-license] for more information.

[ico-build]: https://img.shields.io/github/actions/workflow/status/rougin/combustor/build.yml?style=flat-square
[ico-coverage]: https://img.shields.io/codecov/c/github/rougin/combustor?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/rougin/combustor.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-version]: https://img.shields.io/packagist/v/rougin/combustor.svg?style=flat-square

[link-build]: https://github.com/rougin/combustor/actions
[link-changelog]: https://github.com/rougin/combustor/blob/master/CHANGELOG.md
[link-contributors]: https://github.com/rougin/combustor/contributors
[link-coverage]: https://app.codecov.io/gh/rougin/combustor
[link-downloads]: https://packagist.org/packages/rougin/combustor
[link-license]: https://github.com/rougin/combustor/blob/master/LICENSE.md
[link-packagist]: https://packagist.org/packages/rougin/combustor