<?php

/**
 * @version 1.0.0
 */

namespace App\Themes\CoreTheme\Admin\Screens;

use NewsHour\WPCoreThemeComponents\Admin\Screens\AbstractScreen;

class PostScreen extends AbstractScreen
{
    // The WP_Screen ID. This is used to map our classes to Wordpress "screens".
    public const SCREEN_ID = 'post';

    /**
     * The main entry into the "screen".
     *
     * @return void
     */
    public function main(): void
    {
        // ...here we can run things related to the `post` screen...but heads up, we can only hook
        // into Wordpress actions/filters that fire after the `current_screen` action.
        add_action('admin_notices', function() {
            $user = wp_get_current_user();
            $name = empty($user->first_name) ? $user->user_nicename : $user->first_name;

            echo sprintf(
                '<div class="notice notice-info is-dismissible"><p>Hello <b>%s</b>, this message was generated in: <pre>%s</pre></p></div>',
                $name,
                __CLASS__
            );
        });
    }
}
