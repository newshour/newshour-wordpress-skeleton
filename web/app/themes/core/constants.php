<?php

// ----------------------------------------------------------------------------
// Common Constants
// ----------------------------------------------------------------------------

define('ASSETS_DIR', trailingslashit(BASE_DIR) . 'web/static');
define('ASSETS_URL', home_url('/static'));
define('PUBLISHER_NAME', 'Organization Name Here');
define('SITE_THEME_URL', get_template_directory_uri());
define('SITE_THEME_DIR', dirname(__FILE__) . '/');
define('SITE_NAME', get_bloginfo('name'));
define('TITLE_SEPARATOR', '|');

// Override the locations where Timber looks for Twig templates. The default is:
// BASE_DIR + 'templates'
# define('TIMBER_TEMPLATE_DIRS', '/full/path/to/templates/folder');
