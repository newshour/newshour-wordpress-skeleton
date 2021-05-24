<?php
/**
 * Bootstraps WordPress theme related functions, most importantly enqueuing
 * javascript and styles. Automatic feed and JSON endpoints are also disabled
 * by default. If you wish to enable these, you can comment out the appropriate
 * lines in run().
 *
 * A note on automatic feeds: by default, WP enables RSS feed endpoints for just
 * about everything. This is often undesirable and better control can be achieved
 * by creating a specific feed route(s) (in RoutesManager), controller and context
 * classes. This will allow you to create context-specific feeds and give much
 * more fine grain control over your feeds. For example, you can create different
 * feeds for different services that are consuming the feeds, public/private feeds,
 * etc.
 *
 * @version 1.0.0
 */
namespace App\Themes\CoreTheme\Services\Managers;

use WP_Error;

class ThemeManager extends Manager {

    /**
     * @return string
     */
    public function __toString(): string {

        return self::class;

    }

    /**
     * Runs initialization tasks for the theme.
     *
     * @return void
     */
    public function run(): void {

        add_action('init', [$this, 'themeSetup'], 999);
        add_action('init', [$this, 'requireRestAuth'], 999);
        add_action('wp_enqueue_scripts', [$this, 'enqueue'], 999);
        add_action('wp_loaded', [$this, 'cleanup'], 1);

        // Comment out if you want to keep default Wordpress feed behavior.
        add_action('wp_loaded', [$this, 'removeDefaultFeeds'], 1);

        // Comment out if you want wp-json paths to be available to public clients.
        add_action('wp_loaded', [$this, 'removeDefaultJson'], 1);

        // Needs to run before init...
        add_theme_support('html5', ['comment-form', 'search-form', 'gallery', 'caption', 'style', 'script']);

    }

    public function themeSetup() {

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

        // Add post format support
        add_action(
            'after_setup_theme',
            function () {
                add_theme_support('post-formats', ['status']);
                add_theme_support('post-thumbnails');
            }
        );

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
    public function cleanup() {

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
    public function removeDefaultFeeds() {

        add_action('do_feed', 'abort', 1);
        add_action('do_feed_rdf', 'abort', 1);
        add_action('do_feed_rss', 'abort', 1);
        add_action('do_feed_rss2', 'abort', 1);
        add_action('do_feed_atom', 'abort', 1);
        add_action('do_feed_rss2_comments', 'abort', 1);
        add_action('do_feed_atom_comments', 'abort', 1);

    }

    /**
     * Cleans up the default JSON/XML-RPC endpoints.
     *
     * @return void
     */
    public function removeDefaultJson() {

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
    public function requireRestAuth() {

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
    public function enqueue() {

        // Remove wordpress oembed
        wp_deregister_script('wp-embed');

        // Don't use WordPress jquery in production. (admin bar and wordpress debug bar
        // need it in development though.)
        if (WP_ENV != 'development') {
            wp_deregister_script('jquery');
        }

        // Remove block library from public pages.
        if (!is_admin()) {
            wp_dequeue_style('wp-block-library');
        }

        // Enqueue manifest.js file.
        if (file_exists($distManifestJs = trailingslashit(ASSETS_DIST_DIR) . 'manifest.js')) {
            wp_enqueue_script(
                'manifest',
                trailingslashit(ASSETS_DIST_URL) . 'manifest.js',
                [],
                filemtime($distManifestJs),
                true
            );
        }

        // Enqueue vendor.js file.
        if (file_exists($distVendorJs = trailingslashit(ASSETS_DIST_DIR) . 'vendor.js')) {
            wp_enqueue_script(
                'vendor',
                trailingslashit(ASSETS_DIST_URL) . 'vendor.js',
                [],
                filemtime($distVendorJs),
                true
            );
        }

        // Enqueue main app.js file.
        if (file_exists($distAppJs = trailingslashit(ASSETS_DIST_DIR) . 'app.js')) {
            wp_enqueue_script(
                'app',
                trailingslashit(ASSETS_DIST_URL) . 'app.js',
                [],
                filemtime($distAppJs),
                true
            );
        }

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

}
