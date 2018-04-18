# Changelog

All notable changes to `Combustor` will be documented in this file.

## [1.2.4](https://github.com/rougin/combustor/compare/v1.2.3...v1.2.4) - 2018-04-18

### Fixed
- Bootstrap version in `CreateLayoutCommand`

## [1.2.3](https://github.com/rougin/combustor/compare/v1.2.2...v1.2.3) - 2016-09-16

### Fixed
- Issues in unit testing

## [1.2.2](https://github.com/rougin/combustor/compare/v1.2.1...v1.2.2) - 2016-09-04

### Changed
- From `$table` to `$new_table` in `Wildfire.tpl`

### Fixed
- Updating configuration files on Windows platform

## [1.2.1](https://github.com/rougin/combustor/compare/v1.2.0...v1.2.1) - 2016-05-12

### Changed
- Version of `rougin/codeigniter` to `^3.0.0`

## [1.2.0](https://github.com/rougin/combustor/compare/v1.1.4...v1.2.0) - 2016-05-11

### Added
- Unit tests for other commands
- Contributor Code of Conduct
- `Config` class for handling configurations

### Changed
- Can now only install `Wildfire` or `Doctrine`
- Moved redundant functions to base classes (e.g. `BaseValidator`, `InstallCommand`, `RemoveCommand`)

### Fixed
- Issues in generating code in `Doctrine`
- Functionalities in `Doctrine.php`
- Issue in changing values in `config` directory

## [1.1.4](https://github.com/rougin/combustor/compare/v1.1.3...v1.1.4) - 2015-11-14

### Changed
- Whole code structure from scratch
- Conformed to SOLID design approach

## [1.1.3](https://github.com/rougin/combustor/compare/v1.1.2...v1.1.3) - 2015-10-15

### Added
- Proper form validation rules
- Generation of [Bootstrap](getbootstrap.com)-based views via [Bower](http://bower.io/)
- Special cases for specified fields
- `date` input type in `create.php` and `edit.php`

### Changed
- Generation of indexes for [Doctrine](http://www.doctrine-project.org/projects/orm.html)
- Conformed to PSR standards

### Fixed
- Wrong generation of date and time data in creating models

## [1.1.2](https://github.com/rougin/combustor/compare/v1.1.1...v1.1.2) - 2015-07-01

### Changed
- Version number

### Fixed
- Errors when generating the codebase

## [1.1.1](https://github.com/rougin/combustor/compare/v1.1.0...v1.1.1) - 2015-06-30

### Added
- [SparkPlug](https://github.com/rougin/spark-plug) as a dependency

### Changed
- File permissions
- Functionalities from scratch to conform [Describe](https://github.com/rougin/spark-)'s latest update

### Fixed
- Accessing [SQLite](https://www.sqlite.org/) database

## [1.1.0](https://github.com/rougin/combustor/compare/v1.0.0...v1.1.0) - 2015-06-23

### Added
- Support for multiple databases in the same database connection

### Changed
- Installation process
- Layout views in `create:view`

### Fixed
- Issues regarding [Doctrine](http://www.doctrine-project.org/)

### Removed
- `Inflect.php` for CodeIgniter's [Inflector Helper](http://www.codeigniter.com/userguide3/helpers/inflector_helper.html)
- `MY_Pagination.php` for CodeIgniter's [Pagination Class](http://www.codeigniter.com/userguide3/libraries/pagination.html)

## 1.0.0 - 2015-04-01

### Added
- `Combustor` library
