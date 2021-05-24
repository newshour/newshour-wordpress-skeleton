<?php

/**
 * A loader for posts.
 *
 * @version 1.0.0
 */
use App\Themes\CoreTheme\Http\Controllers\FrontController;
use App\Themes\CoreTheme\Http\Controllers\Posts\SingleController;

FrontController::run(SingleController::class, 'view');
