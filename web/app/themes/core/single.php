<?php

/**
 * A loader for posts.
 *
 * @version 1.0.0
 */
use NewsHour\WPCoreThemeComponents\Controllers\FrontController;

use App\Themes\CoreTheme\Http\Controllers\Posts\SingleController;

FrontController::run(SingleController::class, 'view');
