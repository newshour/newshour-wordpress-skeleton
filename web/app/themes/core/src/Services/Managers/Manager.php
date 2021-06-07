<?php

/**
 * @version 1.0.0
 */

namespace App\Themes\CoreTheme\Services\Managers;

use Symfony\Component\HttpFoundation\Request;

use App\Themes\CoreTheme\Contracts\WordpressManager;

/**
 * Managers encapsulate Wordpress filters/actions and perform any other needed
 * intialization tasks. This allows you to store all of your fitler/action
 * callbacks into organized units.
 *
 * @abstract
 */
abstract class Manager implements WordpressManager {

    // Request object.
    private Request $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request) {

        $this->request = $request;

    }

    /**
     * Run the manager.
     *
     * @return void
     */
    abstract public function run(): void;

    /**
     * @return string
     */
    public function __toString(): string {

        return self::class;

    }

    /**
     * @return Request
     */
    public function getRequest(): Request {

        return $this->request;

    }

}
