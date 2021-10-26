<?php

/**
 * @version 1.0.0
 */

namespace App\Themes\CoreTheme\Services\Managers;

use Symfony\Component\HttpFoundation\Request;

use NewsHour\WPCoreThemeComponents\Managers\Manager;

/**
 * Bootstraps custom Wordpress admin filters.
 */
class AdminFiltersManager extends Manager {

    /**
     * @param Request $request
     */
    public function __construct(Request $request) {

        parent::__construct($request);

    }

    /**
     * @return void
     */
    public function run(): void {

        add_action('admin_init', [$this, 'registerInitFilters'], 1);

    }

    /**
     * Adds filters that hook into 'init' action.
     *
     * @return void
     */
    public function registerInitFilters(): void {

        // Remove import/export tools in production env.
        if (defined('WP_ENV') && WP_ENV == 'production') {

            add_action('admin_menu', function() {
                remove_submenu_page('tools.php', 'export.php');
                remove_submenu_page('tools.php', 'import.php');
                remove_submenu_page('tools.php', 'remove_personal_data');
                remove_submenu_page('tools.php', 'export_personal_data');
            }, 999);

            add_action('admin_head-export.php', function() {
                wp_die('Access forbidden on production.');
            }, 999);

            add_action('admin_head-import.php', function() {
                wp_die('Access forbidden on production.');
            }, 999);

        }

    }

}
