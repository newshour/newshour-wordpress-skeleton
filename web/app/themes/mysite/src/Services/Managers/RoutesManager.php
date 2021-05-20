<?php
/**
 * Sets up custom routes. Heads up: be careful not to setup routes
 * that conflict with interal WP routes.
 *
 * @version 1.0.0
 */
namespace App\Themes\MySite\Services\Managers;

use Routes;

use App\Themes\MySite\Http\Controllers\FrontController;
use App\Themes\MySite\Http\Controllers\Ajax\AjaxController;

class RoutesManager extends Manager {

    /**
     * @return void
     */
    public function run(): void {

        add_action('init', [$this, 'routes'], 1);

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
