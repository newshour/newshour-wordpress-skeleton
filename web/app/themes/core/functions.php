<?php
/**
 * Initializes the theme through manager classes. Managers are logical units
 * to help organize Wordpress filters and action callbacks. This keeps things
 * a bit more tidy and helps reduce Wordpress "callback hell".
 *
 * @version 1.0
 */
use App\Themes\CoreTheme\Commands\HelloWorldCommand;
use App\Themes\CoreTheme\Services\Managers\FiltersManager;
use App\Themes\CoreTheme\Services\Managers\ManagerService;
use App\Themes\CoreTheme\Services\Managers\RoutesManager;
use App\Themes\CoreTheme\Services\Managers\ThemeManager;
use App\Themes\CoreTheme\Services\Managers\TimberManager;

// ----------------------------------------------------------------------------
// Load files.
// ----------------------------------------------------------------------------

// Common constants.
require_once 'constants.php';

// Utility functions.
require_once 'utilities.php';

// ----------------------------------------------------------------------------
// Load managers.
// ----------------------------------------------------------------------------

// Create the manager service.
$managerService = ManagerService::instance();

// Add managers.
$managerService->addAll(
    [
        TimberManager::class,
        ThemeManager::class,
        RoutesManager::class,
        FiltersManager::class
    ]
);

// Add CLI commands.
if (defined('WP_CLI') && WP_CLI) {
    $managerService->addAllCommands(
        [
            HelloWorldCommand::class
        ]
    );
}

//...and launch.
$managerService->run();
