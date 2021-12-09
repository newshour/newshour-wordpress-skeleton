<?php

/**
 * Set your custom routes - uses Timber's built-in routing capabilities. Heads up that
 * Timber's router does not route on HTTP method. To check HTTP methods, you can add
 * method annotations in the controller.
 *
 * @version 1.0
 * @see https://timber.github.io/docs/guides/routing/
 */

use NewsHour\WPCoreThemeComponents\Controllers\FrontController;
use App\Themes\CoreTheme\Http\Controllers as Controllers;

// ----------------------------------------------------------------------------
// Routes.
// ----------------------------------------------------------------------------

Routes::map('/ajax/helloWorld.do', function ($params) {
    FrontController::run(
        Controllers\Ajax\AjaxController::class,
        'dohelloWorld'
    );
});

Routes::map('/hello.do', function ($params) {
    FrontController::run(
        Controllers\Home\HomePageController::class,
        'doHelloPostRequest'
    );
});

Routes::map('/login-required-example', function ($params) {
    FrontController::run(
        Controllers\Pages\PageController::class,
        'loggedInUserView'
    );
});

Routes::map('/examples/slack', function ($params) {
    FrontController::run(
        Controllers\Pages\SlackExamplePageController::class,
        'view'
    );
});

Routes::map('/examples/slack/sendMessage.do', function ($params) {
    FrontController::run(
        Controllers\Pages\SlackExamplePageController::class,
        'doSendSlackMessage'
    );
});

// ----------------------------------------------------------------------------
// Recaptcha Routes
// ----------------------------------------------------------------------------

Routes::map('/ajax/recaptchaVerify.do', function ($params) {
    FrontController::run(
        Controllers\Ajax\RecaptchaController::class,
        'doVerify'
    );
});
