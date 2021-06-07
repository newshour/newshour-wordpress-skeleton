<?php
/**
 * A controller for tag pages.
 *
 * @version 1.0.0
 */
namespace App\Themes\CoreTheme\Http\Controllers\Archives;

use Timber\Term;

use App\Themes\CoreTheme\Contracts\Context;
use App\Themes\CoreTheme\Http\Controllers\Controller;

class TagPageController extends Controller {

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

        // Add the tag term object.
        $this->context['term'] = new Term();

        // Render our template and send it back to the client.
        return $this->render('archives/tag.twig', $this->context);

    }

}
