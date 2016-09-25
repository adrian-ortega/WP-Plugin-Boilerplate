# WordPress Plugin Boilerplate using Composer

A standardized, object-oriented starting point for creating high quality WordPress plugins using composer.

## Repo Contents
* `admin/` - The directory and namespace that contains all classes/logic relating to the WordPress administration panel.
* `frontend/` - The directory and namespace that contains all classes/logic relating to the front end. 
* `includes/` - Core classes and Traits are stored here 
* `languages/` - Internalization files are stored here
* `.gitignore` - Used to exclude certain files from the repository
* `LICENSE` - License statement
* `composer.json` - Composer configuration file
* `index.php` - This is where the plugin get's instantiated

## Boilerplate Features

* This Boilerplate uses the [Plugin API](https://codex.wordpress.org/Plugin_API) (hooks and filters)
* All Classes and variables are documented so you know how to implement them
* This boilerplate uses Composer and PSR auto-loading. All classes need to be namespaced. This allows us to organize our files and code easily.
 
## Installation

Before you attempt to install this, please make sure you have [Composer](https://getcomposer.org/) installed on your machine as you will need it to 
continue.

This boilerplate can be cloned directly into your plugins folder "as-s".
You will need to rename the directory and class namespaces to fit your needs.
This will both need to be done directly on each file as well as on the composer configuration file.

Once you have copied all the files and changed the namespaces, you can run the following in your terminal or command prompt 

`
$: composer dump autoload
`

to get the auto loader to work. If you don't run this, you will encounter a fatal error.