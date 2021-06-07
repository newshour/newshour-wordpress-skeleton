<?php

/**
 * @version 1.0.0
 */

namespace App\Themes\CoreTheme\Http\Controllers\Pages;

use App\Themes\CoreTheme\Contracts\Context;
use App\Themes\CoreTheme\Http\Controllers\Controller;

/**
 * A controller for pages.
 */
class PageController extends Controller {

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

        // Render our template and send it back to the client.
        return $this->render('pages/page.twig', $this->context);

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
