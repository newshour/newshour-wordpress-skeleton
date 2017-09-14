# NewsHour Wordpress Skeleton

This skeleton should be used to when creating Wordpress-based websites. It leverages [Bedrock](https://roots.io/bedrock/) for the project structure, [Composer](https://getcomposer.org/) for dependencies and [Timber](https://timber.github.io/docs/) for the theme framework.

## Usage

1. Download the skeleton into your local development environment.
2. Run ```composer update```.
3. Read the Timber, Bedrock and Composer docs.
4. Start building your theme.

## Notes

### Autoloading

When starting your theme, you should set your theme and custom plugin namespaces in the compser.json file. For example:

```json
"autoload": {
    "psr-4": {
        "NewsHour\\Themes\\PBSNewsHour\\": "web/app/themes/pbs-newshour/src",
        ...
    }
}
```

### Coding Style

You should follow [PHP-FIG](http://www.php-fig.org/) coding styles and conventions as closely as possible. This will help other developers and designers grok your code.

### Plugins

Public Wordpress plugins should be set it the composer.json file and use [Wordpress Packagist](https://wpackagist.org/) for installation. Custom plugins or plugins that require a license and should remain private will need their own GitHub repositories. These plugins should be archived under the NewsHour account.

### Theming

Timber is the framework that you should use to develop your Wordpress theme with. See [Timber's documentation](https://timber.github.io/docs/) for more information.

**Twig**

Timber uses Twig for its templating engine and you should enable template caching in staging and production environments.

```php
file: functions.php

if (WP_ENV != 'development') {
    Timber::$twig_cache = true;
}
```

### Extensions

Code that operates outside of the Wordpress domain but that still may need to interact with the environment somehow should be added to the _extensions_ folder. For example:

```
/extensions/my_folder_name/my_app.php
```

In your application code, you should load the environment and vendor folder. For example:

```php
file: /extensions/my_folder_name/my_app.php

define('WP_BASE_DIR', str_replace('/extensions/my_folder_name', '', dirname(__FILE__)) . '/');

require_once WP_BASE_DIR . 'vendor/autoload.php';

Env::init();

$dotenv = new Dotenv(WP_BASE_DIR);

if (file_exists(WP_BASE_DIR . '/.env')) {
    $dotenv->load();
    $dotenv->required(['DB_NAME', 'DB_USER', 'DB_PASSWORD']);
}
```