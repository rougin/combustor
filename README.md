[![endorse](https://api.coderwall.com/rougin/endorsecount.png)](https://coderwall.com/rougin)

# Combustor

Combustor is a code generator console application for [CodeIgniter](https://codeigniter.com/) in order to speed up the development of web applications.

# Features

* Generates a [CRUD](http://en.wikipedia.org/wiki/Create,_read,_update_and_delete) interface based on the specified table. Giving you time to focus more on the other parts of your awesome application.

	* The generated is greatly based on [MVC](http://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller) architectural pattern.

	* Optionally, it can also generate a CRUD interface with [Bootstrap](http://www.getbootstrap.com) classes and tags.

	* It can also generate specific code on the following fields:

		* ```gender``` Generates a ```form_dropdown()``` with an array of "male" and "female" values

		* ```password``` Generates a new and confirm password fields on ```create.php```, while it also generates current, new, and confirm password fields on ```edit.php```

	* Searching data within the table is also integrated. To enable it, just include the following code:
		
		```php
		<?php echo form_open($this->uri->segment(1), array('method' => 'GET', 'class' => 'navbar-form navbar-left', 'role' => 'Search')); ?>
			<div class="form-group">
				<?php echo form_input('keyword', $this->input->get('keyword'), 'class="form-control" placeholder="Search"'); ?>
			</div>
		<?php echo form_close(); ?>
		```

* Integrates [**Doctrine**](http://www.doctrine-project.org/) or **Wildfire**, my implementation of a design pattern that is based from this [article](http://www.revillweb.com/tutorials/codeigniter-tutorial-learn-codeigniter-in-40-minutes/), with ease to your current CodeIgniter project. Saving you from the hard work of accessing necessary data from the database.

	* It also generates [encapsulation](http://en.wikipedia.org/wiki/Encapsulation_(object-oriented_programming)) to the models, for readbility and more [object-oriented](http://en.wikipedia.org/wiki/Object-oriented_programming) approach

# Installation

1. Follow the instructions that can be found on [ignite.php](https://github.com/rougin/ignite.php). Or you can manually download the latest version of CodeIgniter from this [link](https://github.com/bcit-ci/CodeIgniter/archive/3.0rc2.zip) and extract it to your web server. Then add a ```composer.json``` file (or update if it already exists) on the directory you recently extracted:

	```json
	{
		"description" : "The CodeIgniter framework",
		"name" : "codeigniter/framework",
		"license": "MIT",
		"require": {
			"php": "5.2.4",
			"rougin/combustor": "*"
		}
	}
	```

	**NOTE**: If you want the latest build, use ```dev-master``` instead of ```*```

	Then install it in the command line:

	```$ composer install```

2. After the installation, access the application via the **PHP CLI** to retrieve the list of commands:
	
	**For Unix and Mac:**

	```$ php vendor/bin/combustor```

	**For Windows or if there are no symbolic links found at ```vendor/bin``` directory:**

	```$ php vendor/rougin/combustor/bin/combustor```

3. Just select if you want to install Wildfire or Doctrine ORM.

	**To install/remove Wildfire:**
	
	```$ php vendor/bin/combustor install:wildfire```

	```$ php vendor/bin/combustor remove:wildfire```
	
	**To install/remove Doctrine ORM:**
	
	```$ php vendor/bin/combustor install:doctrine```

	```$ php vendor/bin/combustor remove:doctrine```

4. Next, configure the database connectivity settings in ```application/config/database.php```.

5. Lastly, create now an awesome web application!

# Commands

The help for the following commands below are also available in the Combustor *command line interface* (CLI). Just type the command you want to get help and insert an option of ```--help``` (e.g ```create:controller --help```)

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

	* This option only works if Doctrine is installed

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

	* This option only works if Doctrine is installed

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

	* This option only works if Doctrine is installed

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

**NOTE**: This method in only available in Wildfire.

Delete the specified data from storage

#### Arguments:

* ```$table``` - Name of the specified table

* ```$delimiters``` - Delimits the list of rows to be returned

#### ```$this->wildfire->find($table, $parameters = array());```

#### Description:

**NOTE**: This method in only available in Wildfire.

Find the row from the specified ID or with the list of delimiters from the specified table

#### Arguments:

* ```$table``` - Name of the specified table

* ```$delimiters``` - Delimits the list of rows to be returned

#### ```$this->wildfire->get_all($table, $delimiters = array());``` or

#### ```$this->doctrine->get_all($table, $delimiters = array());```

**NOTE**: This method in only available in Wildfire and Doctrine.

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

* **VERY IMPORTANT**: Before generating the models, views, and controllers, please make sure that you **set up your database** (foreign keys, indexes, relationships, normalizations) properly in order to minimize the modifications after the codes has been generated. Also, generate the models, views, and controllers first to tables that are having **no relationship with other tables** in the database. *The reason for this is that Combustor will generate controllers, models, and views based on your specified database schema. If there's something wrong in your database, definitely Combustor will generated a bad codebase.*

* **VERY IMPORTANT**: If you installed either ```Wildfire``` or ```Doctrine```, there's no need to specify it as option in the specified command. You can specify an option, either ```--wildfire``` or ```--doctrine```, if both components were installed in the specified library.

* If you want to know more about Doctrine ORM and its functionalities, you can always read their documentation [here](doctrine-orm.readthedocs.org/en/latest/tutorials/getting-started.html) to understand their concepts.

* Have you found a bug? Or do you want to contribute? Feel free to open an issue or create a pull request here! :+1: