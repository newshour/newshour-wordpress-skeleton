<?php

/**
 * A loader for posts.
 *
 * @version 1.0.0
 */
use App\Themes\MySite\Http\Controllers\FrontController;
use App\Themes\MySite\Http\Controllers\Posts\SingleController;

FrontController::run(SingleController::class, 'view');
