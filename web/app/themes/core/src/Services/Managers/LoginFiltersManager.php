<?php

/**
 * @version 1.0.0
 */

namespace App\Themes\CoreTheme\Services\Managers;

use Symfony\Component\HttpFoundation\Request;
use NewsHour\WPCoreThemeComponents\Utilities;

/**
 * Bootstraps custom Wordpress login page filters.
 */
class LoginFiltersManager extends ThemeManager
{
    public const RECAPTCHA_NONCE_KEY = 'recaptcha-check';

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    /**
     * @return void
     */
    public function run(): void
    {

        // Google reCaptcha
        $this->loadRecaptcha();
    }

    /**
     * Loads the necessary client-side scripts for reCAPTCHA support. The constant RECAPTCHA_V3_SITE_KEY
     * must exist and must contain a value to load.
     *
     * @return void
     */
    private function loadRecaptcha()
    {
        if (!defined('RECAPTCHA_V3_SITE_KEY') || empty(RECAPTCHA_V3_SITE_KEY)) {
            return;
        }

        // Google reCaptcha
        add_action('login_footer', function () {
            parent::enqueueMixFiles();

            wp_enqueue_script(
                'login-handler',
                Utilities::staticUrl('js/login.js'),
                [],
                '1.0',
                true
            );

            wp_enqueue_script(
                'recaptcha',
                'https://www.google.com/recaptcha/api.js?render=' . RECAPTCHA_V3_SITE_KEY,
                [],
                null
            );

            wp_script_add_data('recaptcha', 'async|defer', true);

            wp_localize_script(
                'login-handler',
                'scriptvars',
                [
                    'base_path' => parse_url(trailingslashit(home_url()), PHP_URL_PATH),
                    'recaptcha_v3_site_key' => RECAPTCHA_V3_SITE_KEY,
                    'nonce' => wp_create_nonce(self::RECAPTCHA_NONCE_KEY)
                ]
            );
        }, 1);
    }
}
