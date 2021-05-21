<?php
/**
 * A home page controller.
 *
 * @version 1.0.0
 */
namespace App\Themes\MySite\Http\Controllers\Home;

use App\Themes\MySite\Contracts\Context;
use App\Themes\MySite\Http\Controllers\Controller;
use App\Themes\MySite\Http\Models\Article;

class HomePageController extends Controller {

    // The Context object.
    private $context;

    // The Request object.
    private $request;

    /**
     * @param Context $context
     */
    public function __construct(Context $context) {

        $this->context = $context;
        $this->request = $context->getRequest();

    }

    /**
     * The main "view" method.
     *
     * @return boolean
     */
    public function view() {

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
