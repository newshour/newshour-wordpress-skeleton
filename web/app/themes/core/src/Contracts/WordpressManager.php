<?php
/**
 * Managers encapsulate various bootstrap routines, configurations and settings for Wordpress by
 * running any assigned tasks.
 *
 * @version 1.0.0
 */
namespace App\Themes\CoreTheme\Contracts;

interface WordpressManager {

    /**
     * Runs any 'actions/triggers/tasks/etc' found in the page controller.
     *
     * @return void
     */
    public function run();

}
