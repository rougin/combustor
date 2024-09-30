# Combustor

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]][link-license]
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Combustor is a [Codeigniter 3](https://codeigniter.com/) package that generates controllers, models, and views based from the provided database tables. It uses the [Describe](/describe/) library for getting columns from a database table and as the basis for code generation.

## Features

* Generates code based from the structure of the Codeigniter framework
* Speeds up the code development for prototyping web applications
* View templates are based on Bootstrap which can also be modified later
* Needs to worry only on the database schema, and Combustor will do the rest

## Installation

Download the [latest Codeigniter 3 project](https://github.com/bcit-ci/CodeIgniter/archive/3.1.13.zip) and extract its contents:

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

Lastly, install the ORM wrappers like `Wildfire` or `Doctrine`:

``` bash
$ vendor/bin/combustor install:wildfire
$ vendor/bin/combustor install:doctrine
```

> [!NOTE]
> Using the `install:wildfire` command installs [Wildfire](https://roug.in/wildfire/) package while the `install:doctrine` installs [Credo](https://roug.in/credo/) package.

## Reminders

Prior in executing any commands, kindly ensure that the **database table is defined properly** (foreign keys, indexes, relationships, normalizations) in order to minimize the modifications after the codes has been generated.

Also, proceed first in generating models, views, or controllers to database tables that are having **no relationship with other tables** in the database.

> [!TIP]
> `Combustor` will generate controllers, models, or views based on the specified database schema. If there's something wrong in the specified database schema, `Combustor` will generate a bad codebase.

## Commands

### `create:layout`

Creates a new header and footer file.

**Options**

* `--bootstrap` - includes the Bootstrap tags

**Example**

``` bash
$ vendor/bin/combustor create-layout --bootstrap
```

### `create:controller`

Creates a new HTTP controller.

**Arguments**

* `name` - name of the database table

**Options**

* `--doctrine` - generates a Doctrine-based controller
* `--wildfire` - generates a Wildfire-based controller

> [!NOTE]
> If either `Wildfire` or `Doctrine` is installed, no need to specify it as option for executing a specified command (e.g. `--wildfire`). However if both are installed, a command must have a `--wildfire` or `--doctrine` option added

**Example**

``` bash
$ vendor/bin/combustor create:controller users --camel --wildfire
```

### `create:model`

Creates a new model.

**Arguments**

* `name` - name of the database table

**Options**

* `--doctrine` - generates a Doctrine-based model
* `--wildfire` - generates a Wildfire-based model

**Example**

``` bash
$ vendor/bin/combustor create:model users --camel --wildfire
```

### `create:view`

Creates a new view template.

**Arguments**

* `name` - name of the database table

**Options**

* `--bootstrap` - includes the Bootstrap tags
* `--doctrine` - generates Doctrine-based views
* `--wildfire` - generates Wildfire-based views

**Example**

``` bash
$ vendor/bin/combustor create:view users --bootstrap
```

### `create:scaffold`

Creates a new HTTP controller, model, and view template.

**Arguments**

* `name` - name of the database table

**Options**

* `--bootstrap` - includes the Bootstrap tags
* `--doctrine` - generates a Doctrine-based controller, model, and views
* `--wildfire` - generates a Wildfire-based controller, model, and views

**Example**

``` bash
$ vendor/bin/combustor create:scaffold users --bootstrap --wildfire
```

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

This property specifies the `application` directory. It may updated to a directory as long it can detect the `config/config.php` file:

``` yaml
# combustor.yml

app_path: %%CURRENT_DIRECTORY%%/Sample

# ...
```

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

[ico-code-quality]: https://img.shields.io/scrutinizer/g/rougin/combustor.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/rougin/combustor.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/rougin/combustor.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/rougin/combustor/master.svg?style=flat-square
[ico-version]: https://img.shields.io/packagist/v/rougin/combustor.svg?style=flat-square

[link-changelog]: https://github.com/rougin/combustor/blob/master/CHANGELOG.md
[link-code-quality]: https://scrutinizer-ci.com/g/rougin/combustor
[link-contributors]: https://github.com/rougin/combustor/contributors
[link-downloads]: https://packagist.org/packages/rougin/combustor
[link-license]: https://github.com/rougin/combustor/blob/master/LICENSE.md
[link-packagist]: https://packagist.org/packages/rougin/combustor
[link-scrutinizer]: https://scrutinizer-ci.com/g/rougin/combustor/code-structure
[link-travis]: https://travis-ci.org/rougin/combustor