<?php
/**
 * Configuration overrides for WP_ENV === 'production'
 */

use Roots\WPConfig\Config;

ini_set('display_errors', 0);

Config::define('WP_DEBUG', false);
Config::define('WP_DEBUG_DISPLAY', false);
Config::define('SCRIPT_DEBUG', false);

/** Disable all file modifications including updates and update notifications */
Config::define('DISALLOW_FILE_MODS', true);
Config::define('WP_CACHE', true);
Config::define('WP_POST_REVISIONS', 0);