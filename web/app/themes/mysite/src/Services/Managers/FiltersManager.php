<?php
/**
 * Bootstraps custom Wordpress filters.
 *
 * @version 1.0.0
 */
namespace App\Themes\MySite\Services\Managers;

class FiltersManager extends Manager {

    /**
     * @return void
     */
    public function run(): void {

        add_action('init', [$this, 'registerInitFilters'], 1);

    }

    /**
     * Adds filters that hook into 'init' action.
     *
     * @return void
     */
    public function registerInitFilters() {

        // Set allowed origins for CORS. References internal WP filter `allowed_http_origins`.
        add_filter('allowed_http_origins', function($origins) {

            // Add additional origins to set.
            return $origins;

        });

        // Add front page to wp_title() output.
        add_filter('wp_title', function ($title) {

            if (is_front_page() || is_home()) {
                return get_bloginfo('name');
            }

            return $title;

        }, 10);

    }

}
