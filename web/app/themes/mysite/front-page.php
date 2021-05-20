<?php
/**
 * The homepage loader.
 *
 * @version 1.0.0
 */
use App\Themes\MySite\Http\Controllers\FrontController;
use App\Themes\MySite\Http\Controllers\Home\HomePageController;

FrontController::run(HomePageController::class, 'view');
