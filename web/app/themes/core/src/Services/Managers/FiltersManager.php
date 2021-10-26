<?php

/**
 * @version 1.0.0
 */

namespace App\Themes\CoreTheme\Services\Managers;

use NewsHour\WPCoreThemeComponents\Managers\Manager;

/**
 * Bootstraps custom Wordpress filters.
 */
class FiltersManager extends Manager {

    /**
     * @return void
     */
    public function run(): void {

        add_action('init', [$this, 'registerInitFilters'], 1);
        add_action('pre_get_posts', [$this, 'registerDefaultQueryFilters'], 1);

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

    /**
     * Alters the default queries made by Wordpress.
     *
     * @param WP_Query $query
     * @return void
     */
    public function registerDefaultQueryFilters($query): void {

        if (!is_page() && is_front_page() && $query->is_main_query()) {
            $query->set('posts_per_page', 1);
        }

    }

}
