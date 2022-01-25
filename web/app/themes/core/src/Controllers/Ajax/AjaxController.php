<?php

/**
 * @version 1.0.0
 */

namespace App\Themes\CoreTheme\Controllers\Ajax;

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
     * @param string $exampleParameter
     * @return Response
     */
    public function doHelloWorld(string $exampleParameter): Response
    {
        // This is our dictionary to be encoded as JSON sent by the response.
        $data = [
            'html' => 'This content was rendered from an AJAX request. It also has set a custom HTTP header.',
            'example_parameter' => $exampleParameter
        ];

        // We can send extra headers along with the response.
        $extra = [
            'headers' => [
                'Custom-Header' => 'Hello World!'
            ]
        ];

        $response = $this->renderJson($data, $this->context, $extra);
        $response->setCache(['max_age' => 60, 'public' => true]);

        return $response;
    }
}
