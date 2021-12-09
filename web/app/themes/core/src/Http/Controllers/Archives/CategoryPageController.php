<?php

/**
 * @version 1.0.0
 */

namespace App\Themes\CoreTheme\Http\Controllers\Archives;

use Timber\Term;
use NewsHour\WPCoreThemeComponents\Contexts\Context;
use NewsHour\WPCoreThemeComponents\Controllers\Controller;

/**
 * A controller for category pages.
 */
class CategoryPageController extends Controller
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

        // Add the category term object.
        $this->context['term'] = new Term();

        // Render our template and send it back to the client.
        return $this->render('archives/category.twig', $this->context);
    }
}
