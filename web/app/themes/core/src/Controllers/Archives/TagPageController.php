<?php

/**
 * @version 1.0.0
 */

namespace App\Themes\CoreTheme\Controllers\Archives;

use Timber\Term;
use NewsHour\WPCoreThemeComponents\Contexts\Context;
use NewsHour\WPCoreThemeComponents\Controllers\Controller;

/**
 * A controller for tag pages.
 */
class TagPageController extends Controller
{
    // The Context object.
    private Context $context;

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * The main "view" method.
     *
     * @return boolean
     */
    public function view()
    {
        // Add the tag term object.
        $this->context['term'] = new Term();

        // Render our template and send it back to the client.
        $response = $this->render('archives/tag.twig', $this->context);
        $response->setCache(['max_age' => 300, 'public' => true]);

        return $response;
    }
}
