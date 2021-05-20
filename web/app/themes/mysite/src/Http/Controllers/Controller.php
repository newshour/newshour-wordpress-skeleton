<?php
/**
 * The parent Controller class.
 *
 * @version 1.0.0
 */
namespace App\Themes\MySite\Http\Controllers;

use InvalidArgumentException;

use Timber\Loader;
use Timber\Timber;

use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Themes\MySite\Contracts\Context;

abstract class Controller {

    /**
     * Renders the view as HTML
     *
     * @param string $template
     * @param Context $context
     * @param array $kwargs
     * @return void
     */
    protected function render(string $template, Context $context, array $kwargs = []): void {

        $expires = isset($kwargs['expires']) && $kwargs['expires'] > -1 ? $kwargs['expires'] : false;
        $cacheMode = empty($kwargs['cache_mode']) ? Loader::CACHE_USE_DEFAULT : $kwargs['cache_mode'];
        $statusCode = empty($kwargs['status_code']) ? Response::HTTP_OK : $kwargs['status_code'];

        $headers = array_merge(
            $this->getQueuedHeaders(),
            empty($kwargs['headers']) ? [] : $kwargs['headers']
        );

        $content = Timber::fetch(
            $template,
            $context->toArray(),
            $expires,
            $cacheMode
        );

        if ($content === false) {
            trigger_error(
                sprintf(
                    'The template "%s" could not be rendered. Please make sure the template exists and is a valid Twig file.',
                    $template
                ),
                E_USER_ERROR
            );
        }

        if (!has_key('Content-Type', $headers)) {
            $headers['Content-Type'] = 'text/html; charset=' . get_option('blog_charset');
        }

        try {

            // Build the response.
            $response = new Response($content, $statusCode, $headers);
            $response->prepare($context->getRequest());
            $response->send();

            // We're all done. Wordpress will run its `shutdown` action on exit.
            exit;

        } catch (InvalidArgumentException $iae) {

            trigger_error($iae);

        }

    }

    /**
     * Render to JSON.
     *
     * @param array $data
     * @param Context $context
     * @param array $kwargs
     * @return void
     */
    protected function renderJson(array $data, Context $context, array $kwargs = []): void {

        $options = empty($kwargs['json_encode_options']) ? 0 : $kwargs['json_encode_options'];
        $statusCode = empty($kwargs['status_code']) ? Response::HTTP_OK : $kwargs['status_code'];
        $headers = empty($kwargs['headers']) ? [] : $kwargs['headers'];

        // Add CORS headers.
        $this->addCorsHeaders($context->getRequest(), $headers);

        if (!has_key('Content-Type', $headers)) {
            $headers['Content-Type'] = 'application/json; charset=' . get_option('blog_charset');
        }

        try {

            // Build the JSON.
            $content = wp_json_encode($data, (int)$options);

            if ($content === false) {
                $content = json_encode(['message' => 'Data could not be encoded as JSON.']);
                $statusCode = 400;
            }

            // Build the response.
            $response = new Response($content, $statusCode, $headers);
            $response->prepare($context->getRequest());
            $response->send();

            // We're all done. Wordpress will run its `shutdown` action on exit.
            exit;

        } catch (InvalidArgumentException $iae) {

            trigger_error($iae);

        }

    }

    /**
     * Add CORS headers to the headers array.
     *
     * @param Request $request
     * @param array $headers
     * @return void
     */
    private function addCorsHeaders(Request $request, array &$headers): void {

        $origins = get_allowed_http_origins();

        if (!is_array($origins)) {
            return;
        }

        $origins = array_map('trim', array_unique($origins));
        $clientOrigin = $request->headers->get('Origin');
        $allowed = '';

        if (in_array('*', $origins)) {

            $allowed = '*';

        } else if (!is_null($clientOrigin) && in_array($clientOrigin, $origins)) {

            $allowed = $clientOrigin;

        }

        if (!empty($allowed)) {
            $headers['Vary'] = 'Origin';
            $headers['Access-Control-Allow-Origin'] = $allowed;
        }

    }

    /**
     * Retrieves the headers set by Wordpress so that they can be into our
     * Response object.
     *
     * @return array
     */
    private function getQueuedHeaders(): array {

        $queued = headers_list();
        $keyed = [];

        foreach ($queued as $header) {
            if (is_array($split = HeaderUtils::split($header, ':'))) {
                $keyed[$split[0]] = isset($split[1]) ? $split[1] : '';
                header_remove($split[0]);
            }
        }

        return $keyed;

    }

}