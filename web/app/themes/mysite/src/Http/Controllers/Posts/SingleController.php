<?php
/**
 * A controller for single posts.
 *
 * @version 1.0.0
 */
namespace App\Themes\MySite\Http\Controllers\Posts;

use App\Themes\MySite\Contracts\Context;
use App\Themes\MySite\Http\Controllers\Controller;

class SingleController extends Controller {

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
        return $this->render('posts/post.twig', $this->context);

    }

}
