<?php

/**
 * @version 1.0.0
 */

namespace App\Themes\CoreTheme\Contracts;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface for context objects.
 *
 * @version 1.0.0
 */
interface Context {

    /**
     * Returns the context dictionary.
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * Returns a Request object.
     *
     * @return Request
     */
    public function getRequest(): Request;

}
