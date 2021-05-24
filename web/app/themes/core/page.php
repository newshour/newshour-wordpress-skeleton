<?php
/**
 * The standard page loader.
 *
 * @version 1.0.0
 */
use App\Themes\CoreTheme\Http\Controllers\FrontController;
use App\Themes\CoreTheme\Http\Controllers\Pages\PageController;

FrontController::run(PageController::class, 'view');
