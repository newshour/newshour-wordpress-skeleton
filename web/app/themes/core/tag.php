<?php
/**
 * The tag page loader.
 *
 * @version 1.0.0
 */
use App\Themes\CoreTheme\Http\Controllers\FrontController;
use App\Themes\CoreTheme\Http\Controllers\Archives\TagPageController;

FrontController::run(TagPageController::class, 'view');
