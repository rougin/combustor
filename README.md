# Combustor

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]][link-license]
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Combustor is a [Codeigniter](https://codeigniter.com/) library that generates controllers, models, and views based from database tables. It uses the [Describe](https://rougin.github.io/describe/) library as the basis for retrieving database tables and for [CRUD](https://en.wikipedia.org/wiki/Create,_read,_update_and_delete) code generation.

## Features

* Generates code based from the structure of the Codeigniter framework
* Needs to worry only on the database schema, and Combustor will do the rest
* Speeds up the code development for prototyping web applications
* View templates are based on Bootstrap which can also be modified later

## Installation

1. Download the Codeigniter framework [here](https://github.com/bcit-ci/CodeIgniter/archive/3.1.9.zip) and extract it to the web server.
2. Configure the database connectivity settings in **application/config/database.php**.
3. Install `Combustor` via [Composer](https://getcomposer.org/):

    ``` bash
    $ composer require rougin/combustor --dev
    ```

4. Install the ORM wrappers **Wildfire** and **Doctrine ORM** or both:

    ``` bash
    $ vendor/bin/combustor install:wildfire
    $ vendor/bin/combustor install:doctrine
    ```

## Commands

### `create:layout`

Creates a new header and footer file.

#### Options

* `--bootstrap` - includes the Bootstrap tags

#### Example

``` bash
$ vendor/bin/combustor create-layout --bootstrap
```

### `create:controller`

Creates a new HTTP controller.

#### Arguments

* `name` - name of the database table

#### Options

* `--camel` - uses camel case naming convention for the accessor and mutators
* `--doctrine` - generates a controller based on Doctrine
* `--keep` - keeps the name to be used
* `--lowercase` - keeps the first character of the name to lowercase
* `--wildfire` - generates a controller based from Wildfire

#### Example

``` bash
$ vendor/bin/combustor create:controller users --camel --wildfire
```

### `create:model`

Creates a new model.

#### Arguments

* `name` - name of the database table

#### Options

* `--camel` - uses camel case naming convention for the accessor and mutators
* `--doctrine` - generates a controller based on Doctrine
* `--keep` - keeps the name to be used
* `--lowercase` - keeps the first character of the name to lowercase
* `--wildfire` - generates a controller based from Wildfire

#### Example

``` bash
$ vendor/bin/combustor create:model users --camel --wildfire
```

### `create:view`

Creates a new view template.

#### Arguments

* `name` - name of the database table

#### Options

* `--bootstrap` - includes the Bootstrap tags
* `--camel` - uses camel case naming convention for the accessor and mutators
* `--doctrine` - generates a controller based on Doctrine
* `--keep` - keeps the name to be used
* `--lowercase` - keeps the first character of the name to lowercase
* `--wildfire` - generates a controller based from Wildfire

#### Example

``` bash
$ vendor/bin/combustor create:view users --bootstrap
```

### `create:scaffold`

Creates a new HTTP controller, model, and view template.

#### Arguments

* `name` - name of the database table

#### Options

* `--bootstrap` - includes the Bootstrap tags
* `--camel` - uses camel case naming convention for the accessor and mutators
* `--doctrine` - generates a controller based on Doctrine
* `--keep` - keeps the name to be used
* `--lowercase` - keeps the first character of the name to lowercase
* `--wildfire` - generates a controller based from Wildfire

#### Example

``` bash
$ vendor/bin/combustor create:scaffold users --bootstrap --wildfire
```

## Wilfire's Methods

The following methods below are available if `--wildfire` is installed:

### `delete($table, $delimiters = [])`

Deletes the specified data from storage.

#### Arguments

* `$table` - name of the database table
* `$delimiters` - delimits the list of rows to be returned

#### Example

``` php
$this->wildfire->delete('users', ['id' => 3]);
```

### `find($table, $delimiters = [])`

Finds the row from the specified ID or with the list of delimiters from the specified table.

#### Arguments

* `$table` - name of the database table
* `$delimiters` - delimits the list of rows to be returned

#### Example

``` php
$this->wildfire->delete('users', ['id' => 3]);
```

### `get_all($table, $delimiters = [])`

Returns all rows from the specified table

#### Arguments

* `$table` - name of the database table
* `$delimiters` - delimits the list of rows to be returned
    * `keyword` - used for searching the data from the storage
    * `per_page` - defines the number of rows per page

#### Returned methods

* `as_dropdown($description)` - returns the list of rows that can be used in `form_dropdown()`
    * `description` - the field to be displayed in the result (the default value is `description`)
* `result()` - returns the list of rows from the storage in a model
* `total_rows()` - returns the total number of rows based from the result

#### Example

``` php
$delimiters = ['keyword' => 'test', 'per_page' = 3];

$result = $this->wildfire->all('users', $delimiters);

var_dump((array) $result->result());
```

**NOTE**: This method is also available if `--doctrine` is installed.

## Reminders

* If either Wildfire or Doctrine is installed, no need to specify it as option for executing a specified command (e.g. `vendor/bin/combustor create:controller --wildfire`). However if both are installed, the command to be executed must have a `--wildfire` or `--doctrine` option added.

* To learn more about Doctrine's functionalities and its concepts, the documentation page can be found [here](http://doctrine-orm.readthedocs.org/en/latest).

* Before generating the models, views, and controllers, please make sure that the **database is defined properly** (foreign keys, indexes, relationships, normalizations) in order to minimize the modifications after the codes has been generated. Also, generate the models, views, and controllers first to tables that are having **no relationship with other tables** in the database.

    * *The reason for this is that Combustor will generate controllers, models, and views based on the specified database schema. If there's something wrong in the said database, Combustor will definitely generate a bad codebase.*

* For found bugs or suggestions, feel free to [open an issue](https://github.com/rougin/combustor/issues) or [create a pull request](https://github.com/rougin/combustor/compare).

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