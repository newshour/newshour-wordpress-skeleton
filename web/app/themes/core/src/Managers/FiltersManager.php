<?php

/**
 * @version 1.0.0
 */

namespace App\Themes\CoreTheme\Managers;

use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;
use NewsHour\WPCoreThemeComponents\Managers\Manager;

/**
 * Bootstraps custom Wordpress filters.
 */
class FiltersManager extends Manager
{
    /**
     * @return void
     */
    public function run(): void
    {
        add_action('init', [$this, 'registerInitFilters'], 1);
        add_action('pre_get_posts', [$this, 'registerDefaultQueryFilters'], 1);
        add_action('after_setup_theme', [$this, 'registerCoreThemeFilters'], 1);
    }

    /**
     * Adds filters that hook into 'init' action.
     *
     * @return void
     */
    public function registerInitFilters()
    {
        // Set allowed origins for CORS. References internal WP filter `allowed_http_origins`.
        add_filter('allowed_http_origins', function ($origins) {
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

        // Allow wp_enqueue_script() to add defer and async attributes.
        add_filter('script_loader_tag', function ($tag, $handle) {
            $allowedAttrs = ['async', 'defer', 'async|defer', 'defer|async'];
            $wpScriptsInstance = wp_scripts();

            foreach ($allowedAttrs as $v) {
                if ($wpScriptsInstance->get_data($handle, $v) !== false) {
                    return str_replace(
                        '></',
                        sprintf(' %s></', str_replace('|', ' ', $v)),
                        $tag
                    );
                }
            }

            return $tag;
        }, 10, 2);
    }

    /**
     * Alters the default queries made by Wordpress.
     *
     * @param WP_Query $query
     * @return void
     */
    public function registerDefaultQueryFilters($query): void
    {
        if (!is_page() && is_front_page() && $query->is_main_query()) {
            $query->set('posts_per_page', 1);
        }
    }

    /**
     * Filters which are specific to the Core Theme and Core Theme Components library.
     *
     * @return void
     */
    public function registerCoreThemeFilters(): void
    {
        /**
         * Sets a list of partner organizations that contribute articles.
         *
         * @param array $organizations
         * @return array
         */
        add_filter('core_theme_partner_organizations', function ($organizations) {
            return [
                'Associated Press',
                'Reuters'
            ];
        });

        /**
         * Sets the version strategy to use the Mix manifest JSON file. The default strategy
         * used if no strategy is set is EmptyVersionStrategy.
         *
         * @return VersionStrategyInterface
         */
        add_filter('core_theme_default_asset_strategy', function ($default) {
            if (file_exists($manifest = trailingslashit(BASE_DIR) . 'web/static/mix-manifest.json')) {
                return new JsonManifestVersionStrategy($manifest);
            }
        });

        /**
         * Get the container used for dependency injection. If you are configuring services, this should be
         * done within `config/services.yaml` or `config/packages/...`.
         * @see https://symfony.com/doc/current/components/dependency_injection.html
         */

        /*
        add_filter('core_theme_container', function ($container) {
            // Do something with the container and return it.
            return $container;
        });
        */

        /**
         * Get the Response object before it is outputted back to the client.
         * @see https://symfony.com/doc/current/components/http_foundation.html#response
         */

        /*
        add_filter('core_theme_response', function ($response) {
            // Do something with the response object and return it.
            return $response;
        });
        */
    }
}
