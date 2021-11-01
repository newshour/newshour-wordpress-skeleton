<?php

/**
 * @version 1.0.0
 */

namespace App\Themes\CoreTheme\Services\Managers;

use Symfony\Component\HttpFoundation\Request;


/**
 * Bootstraps custom Wordpress login page filters.
 */
class LoginFiltersManager extends ThemeManager {

    /**
     * @param Request $request
     */
    public function __construct(Request $request) {

        parent::__construct($request);

    }

    /**
     * @return void
     */
    public function run(): void {

        // Google reCaptcha
        add_action('login_enqueue_scripts', function() {
            parent::enqueueMixFiles();
            wp_enqueue_script('recaptcha', 'https://www.google.com/recaptcha/api.js');
            wp_enqueue_script('login-handler', trailingslashit(ASSETS_DIST_URL) . 'login.js', [], '1.0', true);
        });

        add_action('login_head', function() {
            wp_localize_script(
                'login-handler',
                'scriptvars',
                [
                    'base_path' => parse_url(trailingslashit(home_url()), PHP_URL_PATH),
                    'recaptcha_v3_site_key' => defined('RECAPTCHA_V3_SITE_KEY') ? RECAPTCHA_V3_SITE_KEY : ''
                ]
            );
        });

    }

}
