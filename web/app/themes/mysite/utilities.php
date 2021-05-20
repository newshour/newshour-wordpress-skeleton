<?php

/**
 * Extra utility functions.
 */

// ----------------------------------------------------------------------------

/**
 * Abort and send a status response back to the client.
 *
 * @param  integer $statusCode - default 404
 * @return void
 */
function abort($statusCode = 404, $message = '') {

    add_filter('wp_headers', function($headers) {
        if (!wp_is_json_request()) {
            $headers['Content-Type'] = 'text/html';
            return $headers;
        }
    }, 99, 1);

    add_filter('nocache_headers', function($headers) {
        if (!wp_is_json_request()) {
            $headers['Content-Type'] = 'text/html';
            return $headers;
        }
    }, 99, 1);

    // Handle 404s first.
    if ($statusCode == 404) {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        nocache_headers();
        include get_query_template('404');
        exit;
    }

    switch ($statusCode) {
        case 400:
            $message = empty($message) ? 'Error: Bad request.' : $message;
            $title = 'Bad request';
            break;

        case 403:
            $message = empty($message) ? 'Error: Access Forbidden.' : $message;
            $title = 'Access Forbidden';
            break;

        case 405:
            $message = empty($message) ? 'Error: Method Not Allowed.' : $message;
            $title = 'Method Not Allowed';
            break;

        default:
            $message = $statusCode;
            $title = 'Error';
            break;

    }

    wp_die(
        $message,
        apply_filters('wp_title', $title),
        ['response' => $statusCode]
    );

}

// ----------------------------------------------------------------------------

/**
 * Returns the scheme (protocol) and hostname of a URL. If the URL is malformed,
 * returns an empty string.
 *
 * @param string $url
 * @return string
 */
function get_scheme_and_host($url) {

    $parsed = parse_url($url);

    if ($parsed === false) {
        return '';
    }

    return sprintf('%s://%s', $parsed['scheme'], $parsed['host']);

}

// ----------------------------------------------------------------------------

/**
 * Performs a case-insensitive key check.
 *
 * @param mixed $key
 * @param array $array
 * @return boolean
 */
function has_key($key, array $array) {

    // Do some quick checks first.
    if (count($array) < 1) {
        return false;
    }

    if (array_key_exists($key, $array)) {
        return true;
    }

    // ...now look for the key.
    $generator = fn($array) => (yield from $array);

    foreach ($generator($array) as $k => $v) {
        if (is_string($k) && strcasecmp($k, $key) == 0) {
            return true;
        }
    }

    return false;

}

// ----------------------------------------------------------------------------