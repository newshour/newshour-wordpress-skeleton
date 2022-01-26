<?php

/**
 * The category page loader.
 *
 * @version 1.0.0
 */

use NewsHour\WPCoreThemeComponents\Controllers\FrontController;
use App\Themes\CoreTheme\Controllers\Archives\CategoryPageController;

FrontController::run(CategoryPageController::class, 'view');
