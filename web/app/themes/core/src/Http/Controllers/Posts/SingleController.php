<?php

/**
 * @version 1.0.0
 */

namespace App\Themes\CoreTheme\Http\Controllers\Posts;

use NewsHour\WPCoreThemeComponents\Contexts\Context;
use NewsHour\WPCoreThemeComponents\Controllers\Controller;
use NewsHour\WPCoreThemeComponents\Components\Meta\MetaFactory;

/**
 * A controller for single posts.
 */
class SingleController extends Controller
{
    private Context $context;

    /**
     * @param Context $context
     * @param MetaFactory $metaFactory
     */
    public function __construct(Context $context, MetaFactory $metaFactory)
    {
        $this->context = $context;

        $post = $this->context['post'];

        if ($post != null) {
            add_action('wp_head', function () use ($post, $metaFactory) {
                // Output meta data into <head>. Here we are also adding a Twitter "label" card.
                echo implode(PHP_EOL, [
                    (string) $metaFactory->getPageMeta($post)->getTwitterMeta()->addLabels(['Written By' => SITE_NAME]),
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
    public function view()
    {

        // Set a cache-control header.
        $extras = [
            'headers' => [
                'Cache-Control' => 'max-age=300, public'
            ]
        ];

        // Render our template and send it back to the client.
        return $this->render('posts/post.twig', $this->context, $extras);
    }
}
