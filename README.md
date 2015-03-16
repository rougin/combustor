[![endorse](https://api.coderwall.com/rougin/endorsecount.png)](https://coderwall.com/rougin)

# Combustor

Combustor is a code generator console application for [CodeIgniter](https://codeigniter.com/) in order to speed up the development of web applications.

# Features

* Generates a [CRUD](http://en.wikipedia.org/wiki/Create,_read,_update_and_delete) interface based on the specified table. Giving you time to focus more on the other parts of your awesome application.

	* The generated is greatly based on [MVC](http://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller) architectural pattern.

	* Optionally, it can also generate a CRUD interface with [Bootstrap](http://www.getbootstrap.com) classes and tags.

	* Generates specific code on the following fields:

		* ```gender``` Generates a ```form_dropdown()``` with an array of male and female values

		* ```password``` Generates a new and confirm password fields on ```create.php```, while it also generates current, new, and confirm password fields on ```edit.php```

	* Searching data within the table is also integrated. To enable it, just include the following code:
		
		```
		<?php echo form_open($this->uri->segment(1), array('method' => 'GET', 'class' => 'navbar-form navbar-left', 'role' => 'Search')); ?>
			<div class="form-group">
				<?php echo form_input('keyword', $this->input->get('keyword'), 'class="form-control" placeholder="Search"'); ?>
			</div>
		<?php echo form_close(); ?>
		```

* Integrates [Doctrine](http://www.doctrine-project.org/) with ease or integrates a factory kind of pattern that is based from this [article](http://www.revillweb.com/tutorials/codeigniter-tutorial-learn-codeigniter-in-40-minutes/) to your current CodeIgniter project. Saving you from the hard work of accessing necessary data from the database.

	* It also generates [encapsulation](http://en.wikipedia.org/wiki/Encapsulation_(object-oriented_programming)) to the models, for readbility and more [object-oriented](http://en.wikipedia.org/wiki/Object-oriented_programming) approach

# Installation

1. Follow the instructions that can be found on [ignite.php](https://github.com/rougin/ignite.php). Or you can manually download the latest version of CodeIgniter from this [link](https://github.com/bcit-ci/CodeIgniter/archive/3.0rc2.zip) and extract it to your web server. Then add a ```composer.json``` file (or update if it already exists) on the directory you recently extracted:

	```
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

3. Just select if you want to install the customized factory pattern, or the Doctrine ORM.

	**To install/remove the customized pattern:**
	
	```$ php vendor/bin/combustor install:factory```

	```$ php vendor/bin/combustor remove:factory```
	
	**To install/remove Doctrine ORM:**
	
	```$ php vendor/bin/combustor install:doctrine```

	```$ php vendor/bin/combustor remove:doctrine```

4. Next, configure the database connectivity settings in ```application/config/database.php```.

5. Lastly, create now an awesome web application!

# Reminders

* **VERY IMPORTANT**: Before generating the models, views, and controllers, please make sure that you **set up your database** (foreign keys, indexes, relationships, normalizations) properly in order to minimize the modifications after the codes has been generated. Also, generate the models, views, and controllers first to tables that are having **no relationship with other tables** in the database. *The reason for this is that Combustor will generate controllers, models, and views based on your specified database schema. If there's something wrong in your database, definitely Combustor will generated a bad codebase.*

* If you want to know more about Doctrine ORM and its functionalities, you can always read their documentation [here](doctrine-orm.readthedocs.org/en/latest/tutorials/getting-started.html) to understand their concepts.

* Have you found a bug? Or do you want to contribute? Feel free to open an issue or create a pull request here! :+1:

# Commands

The help for the following commands below are also available in the Combustor *command line interface* (CLI). Just type the command you want to get help and insert an option of ```--help``` (e.g ```create:controller --help```)

#### ```create:layout [--bootstrap]```

#### Description:

Creates a new header and footer file

#### Options:

```--bootstrap``` Include the [Bootstrap](http://getbootstrap.com/) tags

#### ```create:controller [--keep] [--lowercase] name```

**NOTE**: You must install the customized factory pattern to view this command.

#### Description:

Creates a new controller

#### Arguments:

```name``` Name of the controller

#### Options:

```--keep``` Keeps the name to be used

```--lowercase``` Keep the first character of the name to lowercase

#### ```create:model [--lowercase] name```

**NOTE**: You must install the customized factory pattern to view this command.

#### Description:

Creates a new model

#### Arguments:

```name``` Name of the model

#### Options:

```--keep``` Keeps the name to be used

```--lowercase``` Keep the first character of the name to lowercase

#### ```create:view [--bootstrap] [--camel] [--keep] name```

**NOTE**: This command is also available when you do the command ```install:doctrine```.

#### Description:

Creates a new view

#### Arguments:

```name``` Name of the directory to be included in the ```views``` directory

**NOTE**: This command is also available when you install Doctrine ORM.

#### Options:

```--bootstrap``` Include the [Bootstrap](http://getbootstrap.com/) tags

```--camel``` Use the camel case naming convention for the accessor and mutators

```--keep``` Keeps the name to be used

#### ```create:scaffold [--bootstrap] [--camel] [--keep] [--lowercase] name```

**NOTE**: You must install the customized factory pattern to view this command.

#### Description:

Creates a new controller, model, and view

#### Arguments:

```name``` Name of the directory to be included in the ```views``` directory

#### Options:

```--bootstrap``` Include the [Bootstrap](http://getbootstrap.com/) tags

```--camel``` Use the camel case naming convention for the accessor and mutators

```--keep``` Keeps the name to be used

```--lowercase``` Keep the first character of the name to lowercase

#### ```doctrine:controller [--camel] [--keep] [--lowercase] name```

**NOTE**: You must install the Doctrine ORM to view this command.

#### Description:

Creates a new Doctrine-based controller

#### Arguments:

```name``` Name of the controller

#### Options:

```--camel``` Use the camel case naming convention for the accessor and mutators

```--keep``` Keeps the name to be used

```--lowercase``` Keep the first character of the name to lowercase

#### ```doctrine:model [--camel] [--lowercase] name```

**NOTE**: You must install the Doctrine ORM to view this command.

#### Description:

Creates a new Doctrine-based model

#### Arguments:

```name``` Name of the model

#### Options:

```--camel``` Use the camel case naming convention for the accessor and mutators

```--lowercase``` Keep the first character of the name to lowercase

#### ```doctrine:scaffold [--bootstrap] [--camel] [--keep] [--lowercase] name```

**NOTE**: You must install the Doctrine ORM to view this command.

#### Description:

Creates a new Doctrine-based controller, Doctrine-based model and a view

#### Arguments:

```name``` Name of the directory to be included in the ```views``` directory

#### Options:

```--bootstrap``` Include the [Bootstrap](http://getbootstrap.com/) tags

```--camel``` Use the camel case naming convention for the accessor and mutators

```--keep``` Keeps the name to be used

```--lowercase``` Keep the first character of the name to lowercase

# ```Factory.php```'s methods

The following functions/methods are only available when you install the customized factory pattern (```install:factory```).

#### ```$this->factory->delete($table, $parameters = array());```

#### Description:

Delete the specified data from storage

#### Arguments:

```$table``` Name of the specified table

```$delimiters``` Delimits the list of rows to be returned.

#### ```$this->factory->find($table, $parameters = array());```

#### Description:

Find the row from the specified ID or with the list of delimiters from the specified table

#### Arguments:

```$table``` Name of the specified table

```$delimiters``` Delimits the list of rows to be returned.

#### ```$this->factory->get_all($table, $delimiters = array());```

#### Description:

Return all rows from the specified table

#### Arguments:

```$table``` Name of the specified table

```$delimiters``` Delimits the list of rows to be returned. The following required indexes are:

	```$delimiters['keyword']``` Used for searching the data from the storage (this is related when you're implementing the search box)

	```$delimiters['per_page']``` Displays the number of rows per page

#### Returned results

You can also specify the returned values of ```get_all()```:

```$this->factory->get_all()->as_dropdown()``` Returns the list of rows in a ```form_dropdown()``` format

```$this->factory->get_all()->result()``` Returns the list of rows from the storage

```$this->factory->get_all()->total_rows()``` Returns the number of rows from the result