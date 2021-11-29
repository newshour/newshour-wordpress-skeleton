# NewsHour Wordpress Skeleton

This skeleton should be used when creating Wordpress-based websites for NewsHour. It leverages [Bedrock](https://roots.io/bedrock/) for the project structure, [Composer](https://getcomposer.org/) for dependencies, [Timber](https://timber.github.io/docs/) for theming and the NewsHour's [Core Theme Components](https://github.com/newshour/wp-core-theme-components) library.

## Installation

PHP 7.4 is required for installation.

1. Download and install [Composer](https://getcomposer.org/).
2. From your terminal, run `composer create-project newshour/wp-project-skeleton path --repository "{\"type\": \"vcs\", \"url\": \"https://github.com/newshour/newshour-wordpress-skeleton\"}"`. (_path_ should be changed to your desired folder.)
3. Update the .env file at the project root. This is your local env file to set environment variables for the project. `DB_NAME`, `DB_USER`, `DB_PASSWORD` and all salts/keys should be set to desired values.
4. Set your local web server's document root to the `/web` folder.
5. Login to the Wordpress Admin and enabled all plugins.
6. Select "CoreTheme" as the theme under "Appearances".

### Docker

After running steps 1 - 3, you can then run this project with Docker: `docker-compose up`. Note that `WP_HOME` in your .env file should be set to `http://localhost`.

## Notes

### Autoloading

The theme is namespaced and classes are set to autoload in composer.json. Any custom plugin(s) should be namespaced and set in composer.json as well.

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

### Documentation

You can build documentation pages by installing [phpDocumentor](https://phpdoc.org/) and running `phpdoc` from the root
project folder. The documentation pages will become available at `localhost/docs`. The Core Theme Components documentation can be found [here](https://newshour.github.io/wp-core-theme-components-docs/).

### Plugins

Public Wordpress plugins should be set in the composer.json file and use [Wordpress Packagist](https://wpackagist.org/) for installation. Custom plugins or plugins that require a license can also be setup as repositories in the file. Please refer to the Composer documentation on how to add additional repositories.

The following plugins are included by default in the composer.json file:

* Advanced Custom Fields
* Classic Editor
* Debug Bar
* WP Mail SMTP

### Theming

While the project sets up an MVC environment for you via the [Core Theme Components library](https://github.com/newshour/wp-core-theme-components), [Timber](https://upstatement.com/timber/) is incorprated into the project and provides functionality for rendering templates via Twig (e.g. the "view"). It also creates data models for Wordpress post types and provides useful helper utilities for theming.  See [Timber's documentation](https://timber.github.io/docs/) and [Twig's documentation](https://twig.symfony.com/) for more information on what all you can do with Timber and Twig.

**Carbon**

[Carbon](http://carbon.nesbot.com/docs/) is included as a default dependency. While not a requirement, you should utilize Carbon whenever you are handling date/time values.

## MVC

### Controllers

The theme is structured into Models (setup via Timber class mappings), Views (templates in our case) and Controllers. Traditional Wordpress "template" parts (e.g. single.php) act as entry points from which to launch desired Controller classes. Each Controller class is loaded via a Dependency Injection (DI) container. Context, Request and MetaFactory classes can be type-hinted as constructor arguments depending on the controller's needs:

```php
public function __construct(Context $context, MetaFactory $metaFactory)
{
    $this->context = $context;
    $this->metaFactory = $metaFactory;
}
```

#### Contexts

Different Context classes can be created and passed depending on the needs of the Controller/route. For example, if you have a Controller/route which loads RSS feeds, you may wish to create a specific RSS Context class. The default `Context` interface can be type-hinted in the controller's constructor to let the theme automatically choose a context class depending on the Wordpress template "type" (e.g. single, page, archive, home...).

#### Controller Annotations

**HTTP Methods annotation**

Using the `HttpMethods` code annotation, controller methods can be limited to select HTTP methods. By default, all controller methods which respond to requests are limited to ["safe" HTTP methods](https://developer.mozilla.org/en-US/docs/Glossary/Safe/HTTP). To allow "unsafe" methods, such as POST requests, controller methods must provide annotations to describe the allowed method(s). For example:

```php
file: <theme_dir>src/Http/Controllers/Home/HomePageController.php

use NewsHour\WPCoreThemeComponents\Annotations\HttpMethods;
...
    /**
     * @HttpMethods("POST")
     */
    public function doHelloPostRequest() {
...
}
```

An array of HTTP methods can also be set:

```php
/**
 * @HttpMethods({"GET", "OPTIONS"})
 */
```

**Login Required annotation**

Using the `LoginRequired` code annotation, controller methods can be limited to users who are currently logged in. Behind the scenes, this annotation calls the Wordpress method `is_user_logged_in`. Access to these methods will return a 405 status message if the login check fails.

```php
/**
 * @LoginRequired
 */
```

### Models

Models are created by extending the `CorePost` class (which in turn extends Timber's Post class). Model classes can be mapped to different post types which can then be autoloaded by Timber using `App\Themes\CoreTheme\Services\Managers\TimberManager::classMap()`. Model classes can use the trait `Queryable` to provide a fluent API for data fetching. For example, to fetch the latest posts for a given model:

```php
Model::objects()->filter(['posts_per_page' => 10])->orderBy('post_date')->desc()->get();
```

or simply:

```php
Model::objects()->latest(10)->get();
```

### Views

From a Controller, Twig templates can be loaded and passed Context objects which contain all of the data needed to render the "view". For example:

```php
file: <theme_dir>src/Http/Controllers/Home/HomePageController.php

...
public function view() {

    // Render our template and send it back to the client.
    return $this->render('pages/index.twig', $this->context);

}
```

Under the hood, Timber will render the template and its output is used to create a Symfony Response object. Extra HTTP headers, template caching and status codes can be passed as keyword arguments via an optional third parameter:

```php
$extra = [
    'headers' => [
        'Cache-Control' => 'max-age=300, public'
    ]
];

return $this->render('pages/index.twig', $this->context, $extra);
```

## Theme Filters

The following "Wordpress filters" can be used to hook into certain aspects of the Core Theme Components library:

`core_theme_partner_organizations` &mdash; Define a set of partner organizations that contribute to the site. This set is primarily used to generate meta data.

```php
add_filter('core_theme_partner_organizations', function ($organizations) {
    return [
        'Associated Press',
        'Reuters'
    ];
});
```

`core_theme_default_asset_strategy` &mdash; Define a default asset strategy. The Core Theme Components library uses [Symfony's Asset library](https://symfony.com/doc/current/components/asset.html) to load static assets via `NewsHour\WPCoreThemeComponents\Utilities::staticUrl` (or `static_url` in Twig files). The default strategy is `EmptyVersionStrategy` (passed as `$default`) which will not apply any versioning. The following snippet shows how to use the `mix-manifest.json` file for versioning:

```php
add_filter('core_theme_default_asset_strategy', function($default) {
    return new JsonManifestVersionStrategy(
        trailingslashit(BASE_DIR) . 'web/static/mix-manifest.json'
    );
});
```

`core_theme_container` &mdash; Get the [container](https://symfony.com/doc/current/components/dependency_injection.html) used for dependency injection. This filter is run before any controllers are loaded.

```php
add_filter('core_theme_container', function ($container) {
    // Do something with the container object and return it.
    return $container;
});
```

`core_theme_response` &mdash; Get the [Response](https://symfony.com/doc/current/components/http_foundation.html#response) object before it is outputted back to the client.

```php
add_filter('core_theme_response', function ($response) {
    // Do something with the response object and return it.
    return $response;
});
```

## Commands

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
