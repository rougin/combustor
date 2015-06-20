# Combustor

[![Latest Stable Version](https://poser.pugx.org/rougin/combustor/v/stable)](https://packagist.org/packages/rougin/combustor) [![Total Downloads](https://poser.pugx.org/rougin/combustor/downloads)](https://packagist.org/packages/rougin/combustor) [![Latest Unstable Version](https://poser.pugx.org/rougin/combustor/v/unstable)](https://packagist.org/packages/rougin/combustor) [![License](https://poser.pugx.org/rougin/combustor/license)](https://packagist.org/packages/rougin/combustor) [![endorse](https://api.coderwall.com/rougin/endorsecount.png)](https://coderwall.com/rougin)

Combustor is a tool for speeding up web development in [CodeIgniter](https://codeigniter.com/).

# Installation

1. Download CodeIgniter [here](https://github.com/bcit-ci/CodeIgniter/archive/3.0.0.zip) and extract it to your web server.

2. Install ```Combustor``` using [Composer](https://getcomposer.org/):

	```$ composer require rougin/combustor```

3. Choose if you want to install Wildfire or Doctrine ORM or both :smile::

	```$ php vendor/bin/combustor install:wildfire```

	```$ php vendor/bin/combustor install:doctrine```

4. Lastly, configure the database connectivity settings in ```application/config/database.php```.

# Features

* Generates a [CRUD](http://en.wikipedia.org/wiki/Create,_read,_update_and_delete) interface based on the specified table.

	* Optionally, it can also generate a CRUD interface with [Bootstrap](http://www.getbootstrap.com) classes and tags.

	* It can also generate specific code on the following fields:

		* ```gender``` - Generates a ```form_dropdown()``` with an array of "male" and "female" values

		* ```password``` - Generates a new and confirm password fields on ```create.php```, while it also generates current, new, and confirm password fields on ```edit.php```

	* Searching data within the table is also integrated. To enable it, just include the following code:

		```php
		<?php echo form_open($this->uri->segment(1), array('method' => 'GET', 'class' => '', 'role' => 'Search')); ?>
			<div class="form-group">
				<?php echo form_input('keyword', $this->input->get('keyword'), 'class="" placeholder="Search"'); ?>
			</div>
		<?php echo form_close(); ?>
		```

* Can integrate [**Doctrine ORM**](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/) or **Wildfire**, my implementation of a design pattern that is based from this [article](http://www.revillweb.com/tutorials/codeigniter-tutorial-learn-codeigniter-in-40-minutes/).

# Commands

#### ```create:layout [options]```

#### Description:

Creates a new header and footer file

#### Options:

* ```--bootstrap``` - Include the [Bootstrap](http://getbootstrap.com/) tags

#### ```create:controller [options] name```

#### Description:

Creates a new controller

#### Arguments:

* ```name``` - Name of the controller

#### Options:

* ```--camel``` - Use the camel case naming convention for the accessor and mutators

* ```--doctrine``` - Generate a controller based from Doctrine

* ```--keep``` - Keeps the name to be used

* ```--lowercase``` - Keep the first character of the name to lowercase

* ```--wildfire``` - Generate a controller based from Wildfire

#### ```create:model [options] name```

#### Description:

Creates a new model

#### Arguments:

* ```name``` - Name of the model

#### Options:

* ```--camel``` - Use the camel case naming convention for the accessor and mutators

* ```--doctrine``` - Generate a model based from Doctrine

* ```--keep``` - Keeps the name to be used

* ```--lowercase``` - Keep the first character of the name to lowercase

* ```--wildfire``` - Generate a model based from Wildfire

#### ```create:view [options] name```

#### Description:

Creates a new view

#### Arguments:

* ```name``` - Name of the directory to be included in the ```views``` directory

#### Options:

* ```--bootstrap``` - Include the [Bootstrap](http://getbootstrap.com/) tags

* ```--camel``` - Use the camel case naming convention for the accessor and mutators

* ```--doctrine``` - Generate a model based from Doctrine

* ```--keep``` - Keeps the name to be used

* ```--wildfire``` - Generate a model based from Wildfire

#### ```create:scaffold [options] name```

#### Description:

Creates a new controller, model, and view

#### Arguments:

* ```name``` - Name of the directory to be included in the ```views``` directory

#### Options:

* ```--bootstrap``` - Include the [Bootstrap](http://getbootstrap.com/) tags

* ```--camel``` - Use the camel case naming convention for the accessor and mutators

* ```--doctrine``` - Generate a model based from Doctrine

* ```--keep``` - Keeps the name to be used

* ```--lowercase``` - Keep the first character of the name to lowercase

* ```--wildfire``` - Generate a model based from Wildfire

# Methods

#### ```$this->wildfire->delete($table, $parameters = array());```

#### Description:

Delete the specified data from storage

#### Arguments:

* ```$table``` - Name of the specified table

* ```$delimiters``` - Delimits the list of rows to be returned

#### ```$this->wildfire->find($table, $parameters = array());```

#### Description:

Find the row from the specified ID or with the list of delimiters from the specified table

#### Arguments:

* ```$table``` - Name of the specified table

* ```$delimiters``` - Delimits the list of rows to be returned

#### ```$this->wildfire->get_all($table, $delimiters = array());``` or

#### ```$this->doctrine->get_all($table, $delimiters = array());```

#### Description:

Return all rows from the specified table

#### Arguments:

* ```$table``` - Name of the specified table

* ```$delimiters``` - Delimits the list of rows to be returned

	* The following required indexes are:

		* ```$delimiters['keyword']``` - Used for searching the data from the storage (this is used in the "search box" implementation)

		* ```$delimiters['per_page']``` - Displays the number of rows per page

* Returned values for ```get_all()```:

	* ```->as_dropdown($description)``` - Returns the list of rows in a ```form_dropdown()``` format

		* ```$description``` - The field to be display in the dropdown

			* The default value is ```description```

	* ```->result()``` - Returns the list of rows from the storage

	* ```->total_rows()``` - Returns the number of rows from the result

# Reminders

* If you installed either ```Wildfire``` or ```Doctrine```, there's no need to specify it as option in the specified command. You can specify an option, either ```--wildfire``` or ```--doctrine```, if both components were installed in the specified library.

* Before generating the models, views, and controllers, please make sure that you **set up your database** (foreign keys, indexes, relationships, normalizations) properly in order to minimize the modifications after the codes has been generated. Also, generate the models, views, and controllers first to tables that are having **no relationship with other tables** in the database. *The reason for this is that Combustor will generate controllers, models, and views based on your specified database schema. If there's something wrong in your database, definitely Combustor will generated a bad codebase.*

* Have you found a bug? Or do you want to contribute? Feel free to open an issue or create a pull request here! :+1: