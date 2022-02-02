# NewsHour Wordpress Skeleton

This MVC skeleton should be used when creating Wordpress-based websites for NewsHour. It leverages [Bedrock](https://roots.io/bedrock/) for the project structure, [Composer](https://getcomposer.org/) for dependencies, [Timber](https://timber.github.io/docs/) for theming and the NewsHour's [Core Theme Components](https://github.com/newshour/wp-core-theme-components) library.

## Installation

PHP 7.4 is required for installation.

1. Download and install [Composer](https://getcomposer.org/).
2. From your terminal, run `composer create-project newshour/wp-project-skeleton path --repository "{\"type\": \"vcs\", \"url\": \"https://github.com/newshour/newshour-wordpress-skeleton\"}"`. (_path_ should be changed to your desired folder.)
3. Update the .env file at the project root. This is your local env file to set environment variables for the project. `DB_NAME`, `DB_USER`, `DB_PASSWORD` and all salts/keys should be set to desired values.
4. From the project directory, run `npm install` && `npm run (development|watch)`.
5. Set your local web server's document root to the `/web` folder.
6. Login to the Wordpress Admin and enabled all plugins.
7. Select "CoreTheme" as the theme under "Appearances".

### Docker

After running steps 1 - 4, you can then run this project with Docker: `docker-compose up`. Note that `WP_HOME` in your .env file should be set to `http://localhost`.

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

You should follow [PHP-FIG](http://www.php-fig.org/) coding styles and conventions as closely as possible. This will help other developers grok your code and keep things organized. The theme's standard is set to **PSR-12**.

### Documentation

You can build documentation pages by installing [phpDocumentor](https://phpdoc.org/) and running `phpdoc` from the root
project folder. The documentation pages will become available at `localhost/docs`. The Core Theme Components documentation can be found [here](https://newshour.github.io/wp-core-theme-components-docs/).

### Dependencies

The main dependency of the Core Theme is the [Core Theme Components library](https://github.com/newshour/wp-core-theme-components) which bootstraps a familiar MVC environment using a combination of Symfony components and unique customizations for Wordpress. Further dependencies can be added to the `composer.json` file.

**Carbon**

[Carbon](http://carbon.nesbot.com/docs/) is included as a default dependency. While not a requirement, you should utilize Carbon whenever you are handling/passing datetime values.

### Plugins

Public Wordpress plugins should be set in the composer.json file and use [Wordpress Packagist](https://wpackagist.org/) for installation. Custom plugins or plugins that require a license can also be setup as repositories in the file. Please refer to the Composer documentation on how to add additional repositories.

The following plugins are included by default in the composer.json file:

* Advanced Custom Fields
* Classic Editor
* Debug Bar
* WP Mail SMTP

**Heads up:** Careful thought should be made when deciding whether or not to use a plugin. Symfony's many components are available to use with the Core Theme and can be wired up via configuration files (YAML).

### Theming

While the project sets up an MVC environment for you via the Core Theme Components library, [Timber](https://upstatement.com/timber/) is incorprated into the project and provides functionality for rendering templates via Twig (e.g. the "view"). It also creates data models for Wordpress post types, provides a Router and other useful helper utilities for theming.  See [Timber's documentation](https://timber.github.io/docs/) and [Twig's documentation](https://twig.symfony.com/) for more information on what all you can do with Timber and Twig.

## MVC

### Controllers

The theme is structured into Models (setup via Timber class mappings), Views (templates in our case) and Controllers. Traditional Wordpress "template" parts (e.g. single.php) act as entry points from which to launch desired Controller classes. Here single.php needs only 3 lines of code to launch into a cleaner, more familiar MVC structure. The first argument defines the controller class, while the second argument defines which controller method to load.

```php
use NewsHour\WPCoreThemeComponents\Controllers\FrontController;
use App\Themes\CoreTheme\Controllers\Posts\SingleController;

FrontController::run(SingleController::class, 'view');
```

When a Controller class is loaded, a Dependecy Injection (DI) container becomes available and autowires the Controller classes. This allows for service classes to be type-hinted as constructor arguments (or other [via other methods](https://symfony.com/doc/current/service_container/injection_types.html)) depending on the controller's needs:

```php
public function __construct(Context $context, MetaFactory $metaFactory)
{
    $this->context = $context;
    $this->metaFactory = $metaFactory;
}
```

The Core Theme Components library also includes these services "out of the box":

- Context: Context (default context interface), AjaxContext, PageContext, PostContext.
- MetaFactory: a factory for retrieving objects which build HTML/schema.org meta data and tags.

Additional service classes can be registered in `config/services.yaml`. The Core Theme Components library also includes Symfony's Framework Bundle, Debug Bundle and Monolog Bundle which means any service provided by Symfony not in the exclusion list below can also be used with the theme.

_Syfmony Component Exclusions_:
- URL routing (use Timber's Router, see "Routing" section below)
- Translator (use Wordpress translation utilities)

#### Contexts

Different Context classes can be passed depending on the needs of the Controller/route. Single "post" pages can be passed a `PostContext` or single "page" pages can be passed a `PageContext`. The default `Context` interface can be type-hinted in the controller's constructor to let the theme automatically choose a context class depending on the Wordpress template "type" (e.g. single, page, archive, home...). A Request object can also be retrieved from a Context class by using the `getRequest()` method.

Custom Context classes can also be registered with the service container and type-hinted in a Controller. For example, if you have a Controller/route which loads RSS feeds, you may wish to create a specific RSS Context class. See the `FilterManager` class for an example of how a custom `Context` is registered with the service container.

#### Routing

Routing can be achieved through two different methods. The first is the standard Wordpress method of routing (e.g. [WP_Rewrite](https://developer.wordpress.org/reference/classes/wp_rewrite/)) and the second is via [Timber's Router](https://timber.github.io/docs/guides/routing/) class which is based off the AltoRouter library.

Using WP_Rewrite is often confusing and error prone, so it is simpler to use Timber's routing capabilities. Custom routes can be placed in `routes.php` of the theme's directory.

#### Controller Annotations

##### HTTP Methods annotation

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

##### Login Required annotation

Using the `LoginRequired` code annotation, controller classes and methods can be limited to users who are currently logged in. Behind the scenes, this annotation calls the Wordpress method `is_user_logged_in`. Access to these methods will return a 403 status message if the login check fails.

```php
/**
 * @LoginRequired
 */
```

A [capabililty](https://wordpress.org/support/article/roles-and-capabilities/) string may also be passed as an argument which will further limit access to those users which have the required capability.

```php
/**
 * @LoginRequired("edit_posts")
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

**Heads up:** Models in the context of Wordpress are essentially read-only entities and do not provide functionality with regards to Create, Update or Delete. To perform these actions, you will still need to use the internal Wordpress functions. e.g. `wp_update_post(...` when appropriate.

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

Since `render()` returns a [Response](https://symfony.com/doc/current/components/http_foundation.html#response) object, you can also set headers directly on the object.

```php
$response = return $this->render('pages/index.twig', $this->context);
$response->setCache([
    'max_age' => 300,
    'public' => true
]);

return $response;
```

## Admin

Extending the Wordpress admin can be done in two ways: 1) using Advanced Custom Fields to create additional UI components and 2) using [Wordpress screens](https://codex.wordpress.org/Plugin_API/Admin_Screen_Reference) to create additional business logic.

The Core Theme encapsulates admin business logic into "screens" by implementing the interface `ScreenInterface`. Each Wordpress admin "screen" can be mapped in the service container to a corresponding class that implements this interface. To do this, you simply need to add the `$screenId` argument, which is any valid ID value returned by [WP_Screen](https://developer.wordpress.org/reference/classes/wp_screen/), in `config/services.yaml` to each screen class constructor:

```yaml
App\Themes\CoreTheme\Admin\Screens\PostScreen:
    arguments:
        $screenId: 'post'
    tags: ['wp.screen']
```

...or by setting a class constant in your screen class:

```php
class PostScreen extends AbstractScreen
{
    // The WP_Screen ID. This is used to map our classes to Wordpress "screens".
    public const SCREEN_ID = 'post';

```

**Heads up:** For each screen class, you must tag it with `wp.screen` in your `config/services.yaml` file.

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

**Heads up:** you should set any container/service configurations in `config/services.yaml`

`core_theme_response` &mdash; Get the [Response](https://symfony.com/doc/current/components/http_foundation.html#response) object before it is outputted back to the client.

```php
add_filter('core_theme_response', function ($response) {
    // Do something with the response object and return it.
    return $response;
});
```

## Commands

The project structure is fully compatible with [WP CLI](http://wp-cli.org/). You can build custom commands to perform a wide variety of tasks to run under a crontab. Commands are stored in the `src/Commands/` folder and loaded by the _ManagerService_ in `functions.php` just like Controllers. See the [HelloWorldCommand](https://github.com/newshour/newshour-wordpress-skeleton/blob/master/web/app/themes/mysite/src/Commands/HelloWorldCommand.php) for an example.

### Symfony Console

Symfony's console application can also be invoked just like any other Symfony application via `php bin/console`.

Any Commands in `src/Commands` are automatically loaded into the container and services can be type-hinted in the same way as Controllers.

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
