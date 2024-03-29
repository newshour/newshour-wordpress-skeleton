<?php

/**
 * @version 1.0.0
 */

namespace App\Themes\CoreTheme\Managers;

use WP_Error;
use NewsHour\WPCoreThemeComponents\Utilities;
use NewsHour\WPCoreThemeComponents\Managers\Manager;

/**
 * Bootstraps WordPress theme related functions, most importantly enqueuing
 * javascript and styles. Automatic feed and JSON endpoints are also disabled
 * by default. If you wish to enable these, you can comment out the appropriate
 * lines in run().
 *
 * A note on automatic feeds: by default, WP enables RSS feed endpoints for just
 * about everything. This is often undesirable and better control can be achieved
 * by creating a specific feed route(s) (in RoutesManager), controller and context
 * classes. This will allow you to create context-specific feeds and give you much
 * more fine grain control over your feeds. For example, you can create different
 * feeds for different services that are consuming the feeds, public/private feeds,
 * etc.
 */
class ThemeManager extends Manager
{
    /**
     * @return string
     */
    public function __toString(): string
    {
        return self::class;
    }

    /**
     * Runs initialization tasks for the theme.
     *
     * @return void
     */
    public function run(): void
    {
        add_action('init', [$this, 'themeSetup'], 999);
        add_action('init', [$this, 'requireRestAuth'], 999);
        add_action('init', [$this, 'cleanup'], 1);
        add_action('wp_enqueue_scripts', [$this, 'enqueue'], 999);

        // Comment out if you want to keep default Wordpress feed behavior.
        add_action('wp_loaded', [$this, 'removeDefaultFeeds'], 1);

        // Comment out if you want wp-json paths to be available to public clients.
        add_action('wp_loaded', [$this, 'removeDefaultJson'], 1);

        // Needs to run before init...
        add_theme_support('html5', ['comment-form', 'search-form', 'gallery', 'caption', 'style', 'script']);
    }

    public function themeSetup()
    {
        // Make sure 'pages' can set excerpts.
        add_post_type_support('page', 'excerpt');

        // Add a default image sizes.
        add_image_size('large_1200', 1200, 9999);
        add_image_size('large_1200_fixed', 1200, 900, true);

        // Limit srcset to 1200 widths.
        add_filter(
            'max_srcset_image_width',
            function ($maxWidth) {
                return 1200;
            }
        );

        // Add thumbnail support.
        add_theme_support('post-thumbnails');

        // Only show admin bar in development mode. WP debug bar relies on it.
        if (WP_ENV != 'development') {
            add_filter('show_admin_bar', '__return_false');
        }
    }

    /**
     * Cleans up and removes unnecessary Wordpress bloat.
     *
     * @return void
     */
    public function cleanup()
    {
        remove_action('template_redirect', 'rest_output_link_header', 11, 0);
        remove_action('template_redirect', 'wp_shortlink_header', 11);
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
        remove_action('wp_head', 'feed_links', 2);
        remove_action('wp_head', 'feed_links_extra', 3);
        remove_action('wp_head', 'noindex', 1);
        remove_action('wp_head', 'parent_post_rel_link');
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_head', 'rel_canonical');
        remove_action('wp_head', 'rest_output_link_wp_head');
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'start_post_rel_link');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        remove_action('wp_head', 'wp_oembed_add_host_js');
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'wp_resource_hints', 2);
        remove_action('wp_head', 'wp_shortlink_wp_head');
        remove_action('wp_print_styles', 'print_emoji_styles');

        remove_filter('wp_robots', 'wp_robots_max_image_preview_large');

        // Remove pingbacks from cron jobs.
        if (defined('DOING_CRON') && DOING_CRON) {
            remove_action('do_pings', 'do_all_pings');
            wp_clear_scheduled_hook('do_pings');
        }
    }

    /**
     * Cleans up the default feed endpoints.
     *
     * @return void
     */
    public function removeDefaultFeeds()
    {
        add_action('do_feed', fn () => abort(404), 1);
        add_action('do_feed_rdf', fn () => abort(404), 1);
        add_action('do_feed_rss', fn () => abort(404), 1);
        add_action('do_feed_rss2', fn () => abort(404), 1);
        add_action('do_feed_atom', fn () => abort(404), 1);
        add_action('do_feed_rss2_comments', fn () => abort(404), 1);
        add_action('do_feed_atom_comments', fn () => abort(404), 1);
    }

    /**
     * Cleans up the default JSON/XML-RPC endpoints.
     *
     * @return void
     */
    public function removeDefaultJson()
    {
        // WP 5.x admin needs access to wp-json path.
        if (!is_admin()) {
            add_filter('json_enabled', '__return_false');
            add_filter('json_jsonp_enabled', '__return_false');
            add_filter('xmlrpc_enabled', '__return_false');
        }
    }

    /**
     * Forces REST endpoints to be accessed by logged in users only. See
     * https://developer.wordpress.org/rest-api/using-the-rest-api/frequently-asked-questions/#require-authentication-for-all-requests
     *
     * @return void
     */
    public function requireRestAuth()
    {
        add_filter('rest_authentication_errors', function ($result) {
            if (!empty($result)) {
                return $result;
            }

            if (!is_user_logged_in()) {
                return new WP_Error(
                    'rest_not_logged_in',
                    'You are not currently logged in.',
                    ['status' => 401]
                );
            }

            return $result;
        });
    }

    /**
     * Bootstraps Javascript and CSS files.
     *
     * @return void
     */
    public function enqueue()
    {
        // Remove wordpress oembed
        wp_deregister_script('wp-embed');

        // Don't use WordPress jquery on public pages.
        // Remove block library from public pages.
        if (!is_admin()) {
            wp_deregister_script('jquery');
            wp_dequeue_style('wp-block-library');
        }

        // Enqueue jQuery file.
        if (!is_admin()) {
            wp_enqueue_script(
                'jquery',
                Utilities::staticUrl('/dist/js/jquery.min.js'),
                [],
                null
            );
        }

        // Enqueue Laravel Mix files.
        $this->enqueueMixFiles();

        // Enqueue main app.js file.
        wp_enqueue_script(
            'app',
            Utilities::staticUrl('/dist/js/app.js'),
            [],
            null,
            true
        );

        // Ajax support
        wp_localize_script(
            'app',
            'scriptvars',
            [
                'ajax_url' => home_url('ajax'),
                'base_path' => parse_url(trailingslashit(home_url()), PHP_URL_PATH)
            ]
        );
    }

    /**
     * Enqueues Laravel Mix files manifest.js and vendor.js.
     *
     * @return void
     */
    public function enqueueMixFiles()
    {
        // Enqueue manifest.js file.
        wp_enqueue_script(
            'manifest',
            Utilities::staticUrl('/dist/js/manifest.js'),
            [],
            null,
            true
        );

        // Enqueue vendor.js file if we have one.
        if (file_exists(trailingslashit(ASSETS_DIR) . 'dist/js/vendor.js')) {
            wp_enqueue_script(
                'vendor',
                Utilities::staticUrl('/dist/js/vendor.js'),
                [],
                null,
                true
            );
        }
    }
}
