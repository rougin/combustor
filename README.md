# Combustor

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]][link-license]
[![Build Status][ico-build]][link-build]
[![Coverage Status][ico-coverage]][link-coverage]
[![Total Downloads][ico-downloads]][link-downloads]

Combustor is a utility package for [Codeigniter 3](https://codeigniter.com/userguide3/) that generates controllers, models, and views based on the provided database tables. It uses the [Describe](https://roug.in/describe/) package for getting columns from a database table and as the basis for code generation.

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
> Using the `install:wildfire` command installs the [Wildfire](https://roug.in/wildfire/) package while the `install:doctrine` installs the [Credo](https://roug.in/credo/) package.

## Reminders

Prior in executing any commands, kindly ensure that the _**database tables are defined properly**_ (foreign keys, indexes, relationships, normalizations) in order to minimize the modifications after the code structure has been generated.

Also, please proceed first in generating models, views, or controllers to database tables that are having _**no relationship with other tables**_ in the database.

> [!TIP]
> `Combustor` will generate controllers, models, or views based on the specified database schema. If there's something wrong in the specified database schema, `Combustor` will generate a bad codebase.

## Commands

### `create:layout`

Create a new header and footer file.

**Options**

* `--bootstrap` - adds styling based on Bootstrap
* `--force` - generates file/s even they already exists

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
* `--empty` - generates an empty HTTP controller
* `--force` - generates file/s even they already exists

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
* `--empty` - generates an empty model
* `--force` - generates file/s even they already exists

**Example**

``` bash
$ vendor/bin/combustor create:model users --wildfire
```

### `create:repository`

Create a new entity repository.

**Arguments**

* `table` - name of the database table

**Options**

* `--force` - generates file/s even they already exists

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
* `--force` - generates file/s even they already exists

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
* `--force` - generates file/s even they already exists

**Example**

``` bash
$ vendor/bin/combustor create:scaffold users --bootstrap --wildfire
```

> [!NOTE]
> If `--doctrine` is selected, the command will also execute the `create:repository` command.

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
> The timestamps are added by default when creating a `combustor.yml` for the first time as they are usually populated automatically by installed ORMs such as `Wildfire` or `Doctrine`.

### `custom_fields`

By default, all of the fields generated by `Combustor` to `create` and `edit` pages will use the `form_input` helper:

``` php
<div class="mb-3">
  <?= form_label('Email', '', ['class' => 'form-label mb-0']) ?>
  <?= form_input('email', set_value('email'), 'class="form-control"') ?>
  <?= form_error('email', '<div><span class="text-danger small">', '</span></div>') ?>
</div>
```

However, some fields like `email` and `boolean` types may need to use other form helpers:

``` php
<div class="mb-3">
  <?= form_label('Email', '', ['class' => 'form-label mb-0']) ?>
  // Still using form_input, but the type is "email" instead
  <?= form_input(['type' => 'email', 'name' => 'email', 'value' => set_value('email'), 'class' => 'form-control']) ?>
  <?= form_error('email', '<div><span class="text-danger small">', '</span></div>') ?>
</div>
```

``` php
<div class="mb-3">
  <?= form_label('Admin', '', ['class' => 'form-label mb-0']) ?>
  // Use "form_checkbox" for boolean-based data types
  <div>
    <?= form_checkbox('admin', true, set_value('admin'), 'class="form-check-input"') ?>
  </div>
  <?= form_error('admin', '<div><span class="text-danger small">', '</span></div>') ?>
</div>
```

To achieve this, `Combustor` provides a utility for handling specified field names or data types using `custom_fields`:

``` yaml
# combustor.yml

# ...

custom_fields:
  - Rougin\Combustor\Template\Fields\BooleanField
```

When adding a custom field, kindly create a class that extends to the `Colfield` class:

``` php
namespace Acme\Fields;

use Rougin\Combustor\Colfield;

class EmailField extends Colfield
{
    protected $class = 'form-control';

    /**
     * If $name is specified, it will check if the current field
     * name matches the in this $name field.
     */
    protected $name = 'email';

    public function getPlate()
    {
        $field = $this->accessor;

        $class = $this->getClass();

        /** @var string */
        $name = $this->getName();

        $html = '<?= form_input([\'type\' => \'email\', \'name\' => \'' . $name . '\', \'value\' => set_value(\'' . $name . '\')]) ?>';

        if ($this->edit)
        {
            $html = str_replace('set_value(\'' . $name . '\')', 'set_value(\'' . $name . '\', ' . $field . ')', $html);
        }

        $html = str_replace(')]) ?>', '), \'class\' => \'' . $class . '\']) ?>', $html);

        return array($html);
    }
}
```

Then after creating the custom field, simply add the class name to the `combustor.yml`:

``` yaml
# combustor.yml

# ...

custom_fields:
  - Rougin\Combustor\Template\Fields\BooleanField
  - Acme\Fields\EmailField
```

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