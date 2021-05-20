<?php
/**
 * The category page loader.
 *
 * @version 1.0.0
 */
use App\Themes\MySite\Http\Controllers\FrontController;
use App\Themes\MySite\Http\Controllers\Archives\CategoryPageController;

FrontController::run(CategoryPageController::class, 'view');
