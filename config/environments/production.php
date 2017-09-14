<?php
/** Production */
ini_set('display_errors', 0);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', false);
/** Disable all file modifications including updates and update notifications */
define('DISALLOW_FILE_MODS', true);
define('WP_CACHE', true);
define('WP_POST_REVISIONS', 0);
define('CDN_URL', 'https://d3i6fh83elv35t.cloudfront.net/newshour/');

/**
 * PBS AWS configurations.
 */
// Get browser ip address through proxy.
if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
}

// Get proxy host to fix redirect loop when multisite is enabled.
if (!empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
    $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_X_FORWARDED_HOST'];
}

// If the proxy is served via https, make sure we have a value for 'HTTPS'. This fixes
// a redirect loop for EC2 instances behind SSL enabled proxies.
if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') == 0) {
    $_SERVER['HTTPS'] = 'on';
}