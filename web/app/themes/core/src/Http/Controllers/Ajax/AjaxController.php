<?php

/**
 * @version 1.0.0
 */

namespace App\Themes\CoreTheme\Http\Controllers\Ajax;

use Symfony\Component\HttpFoundation\Response;
use NewsHour\WPCoreThemeComponents\Contexts\Context;
use NewsHour\WPCoreThemeComponents\Controllers\Controller;

/**
 * A controller for AJAX requests. See the RoutesManager class for
 * setting up AJAX routes.
 */
class AjaxController extends Controller
{
    // The Context object.
    private Context $context;

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        if (WP_ENV == 'production' && !wp_doing_ajax()) {
            abort(403);
        }

        $this->context = $context;
    }

    /**
     * @return Response
     */
    public function doHelloWorld(): Response
    {

        // This is our dictionary to be encoded as JSON sent by the response.
        $data = [
            'html' => 'This content was rendered from an AJAX request. It also has set a custom HTTP header.',
        ];

        // We can send extra headers along with the response.
        $extra = [
            'headers' => [
                'Custom-Header' => 'Hello World!',
                'Cache-Control' => 'public, max-age=60'
            ]
        ];

        return $this->renderJson($data, $this->context, $extra);
    }
}
