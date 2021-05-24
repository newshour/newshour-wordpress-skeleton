<?php

/**
 * Retrieves the Request object. The Request object is a singleton created
 * by using PHP's super globals.
 *
 * @see https://symfony.com/doc/current/components/http_foundation.html#request
 * @version 1.0.0
 */

namespace App\Themes\CoreTheme\Http\Factories;

use Symfony\Component\HttpFoundation\Request;

final class RequestFactory {

    private static $instance;

    /**
     * @return Request
     */
    public static function get(): Request {

        if (self::$instance == null) {
            self::$instance = Request::createFromGlobals();
        }

        return self::$instance;

    }

}
