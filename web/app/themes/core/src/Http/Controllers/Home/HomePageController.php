<?php

/**
 * @version 1.0.0
 */

namespace App\Themes\CoreTheme\Http\Controllers\Home;

use App\Themes\CoreTheme\Contracts\Context;
use App\Themes\CoreTheme\Http\Controllers\Controller;
use App\Themes\CoreTheme\Http\Models\Article;

/**
 * A home page controller.
 */
class HomePageController extends Controller {

    // The Context object.
    private Context $context;

    /**
     * @param Context $context
     */
    public function __construct(Context $context) {

        $this->context = $context;

    }

    /**
     * The main "view" method.
     *
     * @return boolean
     */
    public function view() {

        // You can retrieve the request object this way:
        $request = $this->context->getRequest();

        // Fetch all the latest article posts.
        //$latestPosts = Article::objects()->filter(['posts_per_page' => 10])->orderBy('post_date')->desc()->get();

        // This is the same as above but more concise. latest() uses the 'posts_per_page' value set in wp_options
        // as the default.
        // $latestPosts = Article::objects()->latest()->get();

        // The same but now we are caching the results. Note that since this relies on the internal wp_cache_*
        // functions, some caching plugins, like W3TC, may override the expiration times of the object cache.
        $latestPosts = Article::objects()->latest()->cache(300)->get();

        // Let's pass them to the template.
        $this->context['latest_posts'] = $latestPosts;

        // Render our template and send it back to the client.
        return $this->render('pages/index.twig', $this->context);

    }

}
