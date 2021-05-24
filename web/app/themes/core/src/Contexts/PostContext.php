<?php
/**
 * Provides a context object for Wordpress posts.
 *
 * @version 1.0.0
 */
namespace App\Themes\CoreTheme\Contexts;

use Symfony\Component\HttpFoundation\Request;

class PostContext extends BaseContext {

    /**
     * @param array $kwargs
     */
    public function __construct(Request $request, array $kwargs = []) {

        parent::__construct($request, $kwargs);
        parent::set('environment', WP_ENV);

        if (is_iterable($posts = $this->offsetGet('posts'))) {
            parent::set('post', $posts[0] ?? null);
        }

        // Set the page title.
        parent::set(
            'page_title', wp_title(TITLE_SEPARATOR, false, 'right') . SITE_NAME
        );

    }

}
