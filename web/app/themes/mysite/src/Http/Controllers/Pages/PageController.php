<?php
/**
 * A controller for pages.
 *
 * @version 1.0.0
 */
namespace App\Themes\MySite\Http\Controllers\Pages;

use App\Themes\MySite\Contracts\Context;
use App\Themes\MySite\Http\Controllers\Controller;

class PageController extends Controller {

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

        // Render our template and send it back to the client.
        return $this->render('pages/single.twig', $this->context);

    }

    /**
     * The 404 page view.
     *
     * @return boolean
     */
    public function viewNotFound() {

        return $this->render('pages/404.twig', $this->context);

    }

}
