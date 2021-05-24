<?php
/**
 * The category page loader.
 *
 * @version 1.0.0
 */
use App\Themes\CoreTheme\Http\Controllers\FrontController;
use App\Themes\CoreTheme\Http\Controllers\Archives\CategoryPageController;

FrontController::run(CategoryPageController::class, 'view');
