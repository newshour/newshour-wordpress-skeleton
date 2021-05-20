<?php
/**
 * Provides a context object for AJAX requests/responses.
 *
 * @version 1.0.0
 */
namespace App\Themes\MySite\Contexts;

use Symfony\Component\HttpFoundation\Request;

class AjaxContext extends BaseContext {

    public function __construct(Request $request, array $initial = []) {

        if (!defined('DOING_AJAX') && $request->isXmlHttpRequest()) {
            define('DOING_AJAX', true);
        }

        parent::__construct($request, $initial);

    }

}
