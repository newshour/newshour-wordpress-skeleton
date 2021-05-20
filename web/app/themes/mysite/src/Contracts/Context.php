<?php

/**
 * Interface for context objects.
 *
 * @version 1.0.0
 */

namespace App\Themes\MySite\Contracts;

use Symfony\Component\HttpFoundation\Request;

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
