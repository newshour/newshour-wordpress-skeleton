# NewsHour Wordpress Skeleton

This skeleton should be used when creating Wordpress-based websites for NewsHour. It leverages [Bedrock](https://roots.io/bedrock/) for the project structure, [Composer](https://getcomposer.org/) for dependencies and [Timber](https://timber.github.io/docs/) for the theme framework.

## Usage

1. Download the skeleton into your local development environment.
2. Run ```composer update```.
3. Read the Timber, Bedrock and Composer docs.
4. Start building your theme.

## Notes

### Autoloading

When starting your theme, you should set your theme and custom plugin namespaces in the compser.json file. For example:

```
"autoload": {
    "psr-4": {
        "NewsHour\\Themes\\PBSNewsHour\\": "web/app/themes/pbs-newshour/src",
        ...
    }
}
```

### Coding Style

You should follow [PHP-FIG](http://www.php-fig.org/) coding styles and conventions as closely as possible. This will help other developers grok your code and keep things organized.

### Plugins

Public Wordpress plugins should be set it the composer.json file and use [Wordpress Packagist](https://wpackagist.org/) for installation. Custom plugins or plugins that require a license and should remain private will need their own GitHub repositories. These plugins should be archived under the NewsHour account.

The following plugins are included by default in the composer.json file:

* Timber
* Wordfence

### Theming

Timber is the framework that you should use when developing your Wordpress theme. While Timber provides many features "out-of-the-box" to help you develop your theme within the MVC design paradigm, great care and thought still needs to go into your theme's codebase to bring MVC into WP's system. See [Timber's documentation](https://timber.github.io/docs/) for more information.

**Twig**

Timber uses Twig for its templating engine and you should enable template caching in staging and production environments.

```php
file: functions.php
-

if (WP_ENV != 'development') {
    Timber::$twig_cache = true;
}
```

**Carbon**

[Carbon](http://carbon.nesbot.com/docs/) is included as a default dependency. While not a requirement, you should utilize Carbon whenever you are handling date/time values.

### Extensions

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

### WP CLI

The project structure is fully compatible with [WP CLI](http://wp-cli.org/).