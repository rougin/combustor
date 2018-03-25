# Combustor

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Lets you generate controllers, models, and views from database tables for [CodeIgniter](https://codeigniter.com).

Lets you generate controllers, models, and views from database tables for [Codeigniter](https://codeigniter.com).

## Installation

1. Download the Codeigniter framework [here](https://github.com/bcit-ci/CodeIgniter/archive/3.1.8.zip) and extract it to your web server.
2. Configure the database connectivity settings in **application/config/database.php**.
3. Install Combustor through the [Composer](https://getcomposer.org) package manager:

    ``` bash
    $ composer require rougin/combustor
    ```

4. Choose if you want to install **Wildfire**, **Doctrine ORM** or both:

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

* If you installed either **Wildfire** or **Doctrine**, there's no need to specify it as option in the specified command. You can specify an option, either **--wildfire** or **--doctrine**, if both components were installed in the specified library.

* If you want to learn more about Doctrine's functionalities, just go head to their [documentation](http://doctrine-orm.readthedocs.org/en/latest/) page.

* Before generating the models, views, and controllers, please make sure that you **set up your database** (foreign keys, indexes, relationships, normalizations) properly in order to minimize the modifications after the codes has been generated. Also, generate the models, views, and controllers first to tables that are having **no relationship with other tables** in the database.

    * The reason for this is that Combustor will generate controllers, models, and views based on your specified database schema. If there's something wrong in your database, Combustor will definitely generate a bad codebase.

* If you found a bug or want to suggest something, feel free to open an issue or create a pull request [here](https://github.com/rougin/combustor/issues)!

## Change Log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email rougingutib@gmail.com instead of using the issue tracker.

## Credits

- [Rougin Royce Gutib][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/rougin/combustor.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/rougin/combustor/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/rougin/combustor.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/rougin/combustor.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/rougin/combustor.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/rougin/combustor
[link-travis]: https://travis-ci.org/rougin/combustor
[link-scrutinizer]: https://scrutinizer-ci.com/g/rougin/combustor/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/rougin/combustor
[link-downloads]: https://packagist.org/packages/rougin/combustor
[link-author]: https://github.com/rougin
[link-contributors]: ../../contributors
