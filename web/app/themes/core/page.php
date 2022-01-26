<?php

/**
 * The standard page loader.
 *
 * @version 1.0.0
 */

use NewsHour\WPCoreThemeComponents\Controllers\FrontController;
use App\Themes\CoreTheme\Controllers\Pages\PageController;

FrontController::run(PageController::class, 'view');
