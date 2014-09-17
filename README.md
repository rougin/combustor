Combustor
=========

Yet another code generator console application for [CodeIgniter](https://ellislab.com/codeigniter/) to speed up the development of web applications. This library generates [CRUD](http://en.wikipedia.org/wiki/Create,_read,_update_and_delete) interface (with [Bootstrap](http://www.getbootstrap.com), optionally) with an integration of an [ORM library](http://www.doctrine-project.org/).

Instructions
============

1. Download the latest version of CodeIgniter and install the Doctrine ORM. To make it easy for you, I've created a [simple script](https://github.com/rougin/ignite.php) for that. If you want to know more about Doctrine ORM and its usage, you can always read their documentation [here](doctrine-orm.readthedocs.org/en/latest/tutorials/getting-started.html).

2. Install it via [Composer](http://www.getcomposer.com). A [Symfony Console](https://github.com/symfony/Console) is required in order to get this console application working.
	
	```
		"require": {
			"rougin/combustor": "dev-master",
			"symfony/console": "2.4.x-dev"
		}
	```

3. Access the application via the PHP CLI and to retrieve the list of commands
	
	```php vendor/bin/combustor```
