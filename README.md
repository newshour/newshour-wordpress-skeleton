# NewsHour Wordpress Skeleton

This skeleton should be used when creating Wordpress-based websites for NewsHour. It leverages [Bedrock](https://roots.io/bedrock/) for the project structure, [Composer](https://getcomposer.org/) for dependencies and [Timber](https://timber.github.io/docs/) for the theming.

## Installation

1. Download and install [Composer](https://getcomposer.org/).
2. From your terminal, run `composer create-project newshour/wp-project-skeleton path --repository "{\"type\": \"vcs\", \"url\": \"https://github.com/newshour/newshour-wordpress-skeleton\"}"`. (_path_ should be changed to your desired folder.)
3. Set your local web server's document root to the `/web` folder.
4. Login to the Wordpress Admin and enabled all plugins.
5. Select "CoreTheme" as the theme under "Appearances".

### Docker

You can also run this project with Docker: `docker-compose up`

## Notes

### Autoloading

The theme is namespaced and classes are set to autoload in composer.json. Any custom plugin should be namespaced and set in composer.json as well.

```
"autoload": {
    "psr-4": {
        "App\\Themes\\CoreTheme\\": "web/app/themes/core/src",
        ...
    }
}
```

### Coding Style

You should follow [PHP-FIG](http://www.php-fig.org/) coding styles and conventions as closely as possible. This will help other developers grok your code and keep things organized. The theme's standard is set to PSR-1.

### Plugins

Public Wordpress plugins should be set it the composer.json file and use [Wordpress Packagist](https://wpackagist.org/) for installation. Custom plugins or plugins that require a license can also be setup as repositories in the file. Please refer to the Composer documentation on how to add additional repositories.

The following plugins are included by default in the composer.json file:

* Advanced Custom Fields
* Classic Editor
* Debug Bar
* Timber
* WP Mail SMTP

### Theming

While the project sets up an MVC environment for you, Timber is incorprated into the project and provides functionality for rendering templates via Twig. It also creates data models for Wordpress post types.  See [Timber's documentation](https://timber.github.io/docs/) and [Twig's documentation](https://twig.symfony.com/) for more information on what all you can do with Timber and Twig.

**Carbon**

[Carbon](http://carbon.nesbot.com/docs/) is included as a default dependency. While not a requirement, you should utilize Carbon whenever you are handling date/time values.

### Commands

The project structure is fully compatible with [WP CLI](http://wp-cli.org/). You can build custom commands to perform a wide variety of tasks to run under a crontab. Commands are stored in the `src/Commands/` folder and loaded by the _ManagerService_ in `functions.php` just like Controllers. See the [HelloWorldCommand](https://github.com/newshour/newshour-wordpress-skeleton/blob/master/web/app/themes/mysite/src/Commands/HelloWorldCommand.php) for an example.

### Project Extensions

Code that operates outside of the Wordpress domain but that still may need to interact with the environment somehow should be added to the _extensions_ folder. For example:

```
/extensions/my_folder_name/my_app.php
```

Classes should be appropriately namespaced and autoloaded via the composer.json file. Any dependencies needed for extensions should be declared as well in the composer.json file. In your application code, you should also load the environment (along with the primary autoload file). This will allow you to access common constants and configuration values of the main Wordpress installation and keep all dependencies to one vendor location.

For example:

```php
file: /extensions/my_folder_name/my_app.php
-

define('WP_BASE_DIR', str_replace('/extensions/my_folder_name', '', dirname(__FILE__)) . '/');

require_once WP_BASE_DIR . 'vendor/autoload.php';

Env::init();

$dotenv = new Dotenv(WP_BASE_DIR);

if (file_exists(WP_BASE_DIR . '/.env')) {
    $dotenv->load();
    $dotenv->required(['DB_NAME', 'DB_USER', 'DB_PASSWORD']);
}
```
