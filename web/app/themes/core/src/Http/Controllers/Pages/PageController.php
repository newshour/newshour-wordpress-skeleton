<?php

/**
 * @version 1.0.0
 */

namespace App\Themes\CoreTheme\Http\Controllers\Pages;

use Timber\User;

use NewsHour\WPCoreThemeComponents\Annotations\LoginRequired;
use NewsHour\WPCoreThemeComponents\Contexts\Context;
use NewsHour\WPCoreThemeComponents\Controllers\Controller;
use NewsHour\WPCoreThemeComponents\Components\Meta\MetaFactory;

use App\Themes\CoreTheme\Http\Contexts\ExampleContext;

/**
 * A controller for pages.
 */
class PageController extends Controller {

    private Context $context;

    /**
     * @param ExampleContext $context
     * @param MetaFactory $metaFactory
     */
    public function __construct(ExampleContext $context, MetaFactory $metaFactory) {

        $this->context = $context;

        $post = $this->context['post'];

        if ($post != null) {
            add_action('wp_head', function() use ($post, $metaFactory) {
                echo implode(PHP_EOL, [
                    (string) $metaFactory->getPageMeta($post),
                    (string) $metaFactory->schemas()->getWebPageSchema($post)->asHtml()
                ]);
            });
        }

    }

    /**
     * The main "view" method.
     *
     * @return boolean
     */
    public function view() {

        // Render our template and send it back to the client.
        return $this->render('pages/page.twig', $this->context);

    }

    /**
     * @LoginRequired
     * @return void
     */
    public function loggedInUserView() {

        $this->context['user'] = new User();
        $this->context['page_title'] = 'A page for logged in users';

        return $this->render('pages/user_page.twig', $this->context);

    }

    /**
     * The 404 page view.
     *
     * @return boolean
     */
    public function viewNotFound() {

        return $this->render('pages/404.twig', $this->context, ['status_code' => 404]);

    }

}
