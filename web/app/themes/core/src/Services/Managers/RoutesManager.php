<?php

/**
 * @version 1.0.0
 */

namespace App\Themes\CoreTheme\Services\Managers;

use Routes;

use NewsHour\WPCoreThemeComponents\Controllers\FrontController;
use NewsHour\WPCoreThemeComponents\Managers\Manager;

use App\Themes\CoreTheme\Http\Controllers\Ajax\AjaxController;

/**
 * Sets up custom routes. Heads up: be careful not to setup routes
 * that conflict with interal WP routes.
 */
class RoutesManager extends Manager {

    /**
     * @return void
     */
    public function run(): void {

        if (!is_admin()) {
            add_action('init', [$this, 'routes'], 1);
        }

    }

    /**
     * Add custom routes.
     *
     * @return void
     */
    public function routes(): void {

        Routes::map('/ajax/helloWorld.do', function($params) {
            FrontController::run(AjaxController::class, 'dohelloWorld');
        });

    }

}
