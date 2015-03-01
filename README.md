[![endorse](https://api.coderwall.com/rougin/endorsecount.png)](https://coderwall.com/rougin)

Combustor
=========

Combustor is a code generator console application for [CodeIgniter](https://codeigniter.com/) in order to speed up the development of web applications. This library generates a [CRUD](http://en.wikipedia.org/wiki/Create,_read,_update_and_delete) interface (with [Bootstrap](http://www.getbootstrap.com), optional only) with an integration of an [ORM library](http://www.doctrine-project.org/) or with a structure that is based from this [link](http://www.revillweb.com/tutorials/codeigniter-tutorial-learn-codeigniter-in-40-minutes/).

Installation
============

1. Follow the instructions that can be found on [ignite.php](https://github.com/rougin/ignite.php). Or you can manually download the latest version of CodeIgniter from this [link](https://github.com/bcit-ci/CodeIgniter/archive/3.0rc2.zip) and extract it to your web server. Then add a ```composer.json``` file (or update if it already exists) on the directory you recently extracted:

	```
	{
		"description" : "The CodeIgniter framework",
		"name" : "codeigniter/framework",
		"license": "MIT",
		"require": {
			"php": ">=5.2.4",
			"rougin/combustor": "*"
		}
	}
	```

	Then install it in the command line:

	```$ composer install```

2. After the installation, access the application via the **PHP CLI** to retrieve the list of commands:
	
	**For Unix and Mac:**

	```$ php vendor/bin/combustor```

	**For Windows or if there are no symbolic links found at ```vendor/bin``` directory:**

	```$ php vendor/rougin/combustor/bin/combustor```

3. Just select if you want to install the customized factory pattern, or the Doctrine ORM.

	**To install/remove the customized pattern:**
	
	```$ php vendor/bin/combustor install:factory```

	```$ php vendor/bin/combustor remove:factory```
	
	**To install/remove Doctrine ORM:**
	
	```$ php vendor/bin/combustor install:doctrine```

	```$ php vendor/bin/combustor remove:doctrine```

4. Next, configure the database connectivity settings in ```application/config/database.php```.

5. Then create now an awesome application!

Reminders
=========

* **VERY IMPORTANT**: Before generating the models, views, and controllers, please make sure that you **set up your database** (foreign keys, indexes, relationships, normalizations) properly in order to minimize the modifications after the codes has been generated. Also, generate the models, views, and controllers first to tables that are having **no relationship with other tables** in the database. *The reason for this is that Combustor will generate controllers, models, and views based on your database schema. If there's something wrong in your database, definitely the Combustor will generated some bad code.*

* If you want to know more about Doctrine ORM and its functionalities, you can always read their documentation [here](doctrine-orm.readthedocs.org/en/latest/tutorials/getting-started.html).

* Found a bug? Want to contribute? Feel free to open an issue or create a pull request. :+1:

Commands
========

The help for following commands below are also available in the Combustor *command line interface*. Just type the command you want to get help and insert an option of ```--help``` (e.g ```create:controller --help```)

create:layout [options]
=======================

Creates a new header and footer file

#### Options:

```--bootstrap``` Include the [Bootstrap](http://getbootstrap.com/) tags

create:controller [arguments] [options]
=======================================

**NOTE**: You must install the customized factory pattern to view this command.

Creates a new controller

#### Arguments:

```name``` Name of the controller

#### Options:

```--keep``` Keeps the name to be used

```--lowercase``` Keep the first character of the name to lowercase

```--camel``` Use the camel case naming convention for the accessor and mutators

create:model [arguments] [options]
==================================

**NOTE**: You must install the customized factory pattern to view this command.

Creates a new model

#### Arguments:

```name``` Name of the model

#### Options:

```--lowercase``` Keep the first character of the name to lowercase

```--camel``` Use the camel case naming convention for the accessor and mutators

create:view [arguments] [options]
=================================

**NOTE**: This command is also available when you do the command ```install:doctrine```.

Creates a new view

#### Arguments:

```name``` Name of the directory to be included in the ```views``` directory

**NOTE**: This command is also available when you install Doctrine ORM.

#### Options:

```--bootstrap``` Include the [Bootstrap](http://getbootstrap.com/) tags

```--camel``` Use the camel case naming convention for the accessor and mutators

create:scaffold [arguments] [options]
=====================================

**NOTE**: You must install the customized factory pattern to view this command.

Creates a new controller, model, and view

#### Arguments:

```name``` Name of the directory to be included in the ```views``` directory

#### Options:

```--bootstrap``` Include the [Bootstrap](http://getbootstrap.com/) tags

```--camel``` Use the camel case naming convention for the accessor and mutators

```--keep``` Keeps the name to be used

```--lowercase``` Keep the first character of the name to lowercase

doctrine:controller [arguments] [options]
=========================================

**NOTE**: You must install the Doctrine ORM to view this command.

Creates a new Doctrine-based controller

#### Arguments:

```name``` Name of the controller

#### Options:

```--keep``` Keeps the name to be used

```--lowercase``` Keep the first character of the name to lowercase

```--camel``` Use the camel case naming convention for the accessor and mutators

doctrine:model [arguments] [options]
====================================

**NOTE**: You must install the Doctrine ORM to view this command.

Creates a new Doctrine-based model

#### Arguments:

```name``` Name of the model

#### Options:

```--lowercase``` Keep the first character of the name to lowercase

```--camel``` Use the camel case naming convention for the accessor and mutators

doctrine:scaffold [arguments] [options]
=======================================

**NOTE**: You must install the Doctrine ORM to view this command.

Creates a new Doctrine-based controller, Doctrine-based model and a view

#### Arguments:

```name``` Name of the directory to be included in the ```views``` directory

#### Options:

```--bootstrap``` Include the [Bootstrap](http://getbootstrap.com/) tags

```--camel``` Use the camel case naming convention for the accessor and mutators

```--keep``` Keeps the name to be used

```--lowercase``` Keep the first character of the name to lowercase