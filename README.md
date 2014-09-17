Combustor
=========

Yet another code generator based in command line interface (CLI) for speeding up the developments of web applications  in [CodeIgniter](https://ellislab.com/codeigniter/). This library generates [CRUD](http://en.wikipedia.org/wiki/Create,_read,_update_and_delete) interface (with [Bootstrap](http://www.getbootstrap.com), optionally) with an integration of an [ORM library](http://www.doctrine-project.org/).

Instructions
============

1. Install it via [Composer](http://www.getcomposer.com). A [Symfony Console](https://github.com/symfony/Console) is required for this console application.
	
	```
		"require": {
			"rougin/combustor": "dev-master",
			"symfony/console": "2.4.x-dev"
		}
	```

2. Access the application via the PHP CLI and to retrieve the list of commands

	```php vendor/bin/combustor```

If you want to know more about Doctrine ORM and its usage, you can always read their documentation [here](doctrine-orm.readthedocs.org/en/latest/tutorials/getting-started.html).
