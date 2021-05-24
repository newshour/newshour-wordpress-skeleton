<?php
/**
 * The homepage loader.
 *
 * @version 1.0.0
 */
use App\Themes\CoreTheme\Http\Controllers\FrontController;
use App\Themes\CoreTheme\Http\Controllers\Home\HomePageController;

FrontController::run(HomePageController::class, 'view');
