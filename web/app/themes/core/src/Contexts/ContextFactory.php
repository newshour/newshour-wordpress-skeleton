<?php

/**
 * Factory class for creating Context objects.
 */

namespace App\Themes\CoreTheme\Contexts;

use Timber\Timber;

use App\Themes\CoreTheme\Contracts\Context;
use App\Themes\CoreTheme\Http\Factories\RequestFactory;

/**
 * Factory class for creating Context objects.
 *
 * @final
 */
final class ContextFactory {

    /**
     * Returns the default context object depending on WP's "template type". If
     * an AJAX request is detected, AjaxContext is returned.
     *
     * - single-*.php > PostContext
     * - front-page.php > PageContext
     * - page.php > PageContext
     *
     * etc...
     *
     * Defaults to PageContext.
     *
     * @return Context
     */
    public static function default(): Context {

        if (is_single()) {
            return self::post();
        }

        if (is_home() || is_front_page() || is_page()) {
            return self::page();
        }

        if (wp_doing_ajax() || RequestFactory::get()->isXmlHttpRequest()) {
            return self::ajax();
        }

        return self::page();

    }

    /**
     * Returns a "page" context object.
     *
     * @return Context
     */
    public static function page(): Context {

        return new PageContext(RequestFactory::get(), Timber::context());

    }

    /**
     * Returns a "post" context object.
     *
     * @return Context
     */
    public static function post(): Context {

        return new PostContext(RequestFactory::get(), Timber::context());

    }

    /**
     * Returns an AJAX context object.
     *
     * @return Context
     */
    public static function ajax(): Context {

        return new AjaxContext(RequestFactory::get());

    }

}
