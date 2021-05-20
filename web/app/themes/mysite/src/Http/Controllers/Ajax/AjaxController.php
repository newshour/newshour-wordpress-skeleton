<?php
/**
 * A controller for AJAX requests. See the RoutesManager class for
 * setting up AJAX routes.
 *
 * @version 1.0.0
 */
namespace App\Themes\MySite\Http\Controllers\Ajax;

use App\Themes\MySite\Contracts\Context;
use App\Themes\MySite\Http\Controllers\Controller;

class AjaxController extends Controller {

    // The Context object.
    private $context;

    // The Request object.
    private $request;

    public function __construct(Context $context) {

        if (WP_ENV == 'production' && !wp_doing_ajax()) {
            abort(403);
        }

        $this->context = $context;
        $this->request = $context->getRequest();

    }

    public function doHelloWorld() {

        // This is our dictionary to be encoded as JSON sent by the response.
        $data = [
            'html' => 'This content was rendered from an AJAX request. It also has set a custom HTTP header.',
        ];

        // We can send extra headers along with the response.
        $extra = [
            'headers' => [
                'Custom-Header' => 'Hello World!'
            ]
        ];

        return $this->renderJson($data, $this->context, $extra);

    }

}
