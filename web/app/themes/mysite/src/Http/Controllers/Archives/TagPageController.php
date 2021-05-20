<?php
/**
 * A controller for tag pages.
 *
 * @version 1.0.0
 */
namespace App\Themes\MySite\Http\Controllers\Archives;

use Timber\Term;

use App\Themes\MySite\Contracts\Context;
use App\Themes\MySite\Http\Controllers\Controller;

class TagPageController extends Controller {

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

        // Add the tag term object.
        $this->context['term'] = new Term();

        // Render our template and send it back to the client.
        return $this->render('archives/tag.twig', $this->context);

    }

}
