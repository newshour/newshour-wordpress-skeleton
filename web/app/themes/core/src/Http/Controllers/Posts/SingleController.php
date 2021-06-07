<?php
/**
 * A controller for single posts.
 *
 * @version 1.0.0
 */
namespace App\Themes\CoreTheme\Http\Controllers\Posts;

use App\Themes\CoreTheme\Contracts\Context;
use App\Themes\CoreTheme\Http\Controllers\Controller;

class SingleController extends Controller {

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
        return $this->render('posts/post.twig', $this->context);

    }

}
