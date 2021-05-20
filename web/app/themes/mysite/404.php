<?php
/**
 * The 404 page loader.
 *
 * @version 1.0.0
 */
use App\Themes\MySite\Http\Controllers\FrontController;
use App\Themes\MySite\Http\Controllers\Pages\PageController;

FrontController::run(PageController::class, 'viewNotFound');
