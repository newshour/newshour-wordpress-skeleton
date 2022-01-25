<?php

/**
 * @version 1.0.0
 */

namespace App\Themes\CoreTheme\Contexts;

use Symfony\Component\HttpFoundation\Request;
use NewsHour\WPCoreThemeComponents\Contexts\BaseContext;

/**
 * An example custom context which extends BaseContext.
 */
class ExampleContext extends BaseContext
{
    /**
     * @param Request $request
     * @param array $initial
     */
    public function __construct(Request $request, array $initial)
    {
        // These key/value pairs will be available for this context and can be accessed in
        // the controller or in the view template.
        $initial['foo'] = 'bar';
        $initial['environment'] = WP_ENV;
        $initial['page_title'] = wp_title(TITLE_SEPARATOR, false, 'right');

        if (is_singular() && is_iterable($posts = $initial['posts'])) {
            $initial['post'] = $posts[0];
        }

        parent::__construct($request, $initial);
    }
}
