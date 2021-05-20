<?php
/**
 * Provides a context object for Wordpress pages.
 *
 * @version 1.0.0
 */
namespace App\Themes\MySite\Contexts;

use Symfony\Component\HttpFoundation\Request;

class PageContext extends BaseContext {

    /**
     * @param array $kwargs
     */
    public function __construct(Request $request, array $kwargs = []) {

        parent::__construct($request, $kwargs);
        parent::set('environment', WP_ENV);

        if (is_singular() && isset($this->data['posts'][0])) {
            parent::set('post', $this->data['posts'][0]);
        }

        // Set the page title.
        parent::set(
            'page_title',
            isset($kwargs['page_title']) ? $kwargs['page_title'] : $this->getPageTitle()
        );

    }

    /**
     * @return string
     */
    private function getPageTitle(): string {

        $title = wp_title(TITLE_SEPARATOR, false, 'right');

        if (is_home() || is_front_page()) {
            return $title;
        }

        return $title . SITE_NAME;

    }

}
