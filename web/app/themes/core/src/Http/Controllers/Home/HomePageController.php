<?php

/**
 * @version 1.0.0
 */

namespace App\Themes\CoreTheme\Http\Controllers\Home;

use NewsHour\WPCoreThemeComponents\Annotations\HttpMethods;
use NewsHour\WPCoreThemeComponents\Contexts\Context;
use NewsHour\WPCoreThemeComponents\Controllers\Controller;
use NewsHour\WPCoreThemeComponents\Components\Meta\MetaFactory;
use App\Themes\CoreTheme\Http\Models\Article;
use App\Themes\CoreTheme\Http\Models\Page;

/**
 * A home page controller.
 */
class HomePageController extends Controller
{
    private Context $context;
    private MetaFactory $metaFactory;

    /**
     * @param Context $context
     * @param MetaFactory $metaFactory
     */
    public function __construct(Context $context, MetaFactory $metaFactory)
    {
        $this->context = $context;
        $this->metaFactory = $metaFactory;
    }

    /**
     * Sets up HTML meta tags.
     *
     * @return void
     */
    public function setupPageMeta(): void
    {
        // Facebook Open Graph tags.
        $faceBookMeta = $this->metaFactory->getFacebookMeta()
            ->setDescription(get_bloginfo('description'))
            ->setUrl(home_url())
            ->setTitle(get_bloginfo('name'))
            ->setSiteName(get_bloginfo('name'));

        // Twitter meta tags.
        $twitterMeta = $this->metaFactory->getTwitterMeta()
            ->setSite('SomeTwitterHandleHere')
            ->setTitle(get_bloginfo('name'));

        // HTML meta tags.
        $pageMeta = $this->metaFactory->getPageMeta()
            ->setCanonicalUrl(home_url())
            ->setDescription(get_bloginfo('description'))
            ->setFacebookMeta($faceBookMeta)
            ->setTwitterMeta($twitterMeta);

        echo (string) $pageMeta . PHP_EOL;

        // schema.org ld+json data.
        $schemaFactory = $this->metaFactory->schemas();

        $searchActionSchema = $schemaFactory->getSearchActionSchema()
            ->setQueryInput('required name=search_term')
            ->setTarget(home_url('/page/to/searchResults?q={search_term}'));

        $webPageSchema = $schemaFactory->getWebPageSchema()
            ->setName(get_bloginfo('name'))
            ->setDescription(get_bloginfo('description'))
            ->setThumbnail(get_option('core_theme_social_img_url'))
            ->setPotentialAction($searchActionSchema)
            ->setOrganization(
                $schemaFactory->getOrganizationSchema(true)
            );

        echo (string) $webPageSchema->asHtml();
    }

    /**
     * The main "view" method.
     *
     * @return boolean
     */
    public function view()
    {
        add_action('wp_head', [$this, 'setupPageMeta']);

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

        // Fetch all the pages.
        $this->context['latest_pages'] = Page::objects()->latest()->get();

        // Render our template and send it back to the client.
        return $this->render('pages/index.twig', $this->context);
    }

    /**
     * A basic example of handling a POST request.
     *
     * @HttpMethods("POST")
     */
    public function doHelloPostRequest()
    {
        $request = $this->context->getRequest();

        // Validate the nonce.
        valid_nonce_or_abort($request->request->get(NONCE_FIELD_NAME));

        $firstName = $request->request->get('first_name');

        if (empty($firstName)) {
            $this->context['error_msg'] = 'You did not add your first name!';
            return $this->view();
        }

        $this->context['success_msg'] = sprintf(
            'Hello %s! You have successfully made a POST request. Next, try refreshing this page as a GET request.',
            esc_html($request->request->get('first_name'))
        );

        return $this->view();
    }
}
