<?php
/**
 * A controller for category pages.
 *
 * @version 1.0.0
 */
namespace App\Themes\CoreTheme\Http\Controllers\Archives;

use Timber\Term;

use App\Themes\CoreTheme\Contracts\Context;
use App\Themes\CoreTheme\Http\Controllers\Controller;

class CategoryPageController extends Controller {

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

        // Add the category term object.
        $this->context['term'] = new Term();

        // Render our template and send it back to the client.
        return $this->render('archives/category.twig', $this->context);

    }

}
