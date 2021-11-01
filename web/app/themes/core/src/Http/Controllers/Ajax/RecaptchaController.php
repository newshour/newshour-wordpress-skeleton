<?php

/**
 * @version 1.0.0
 */

namespace App\Themes\CoreTheme\Http\Controllers\Ajax;

use NewsHour\WPCoreThemeComponents\Annotations\HttpMethods;
use NewsHour\WPCoreThemeComponents\Contexts\Context;

/**
 * A controller for verifying Recaptcha tokens.
 */
class RecaptchaController extends AjaxController {

    private Context $context;

    public function __construct(Context $context) {

        parent::__construct($context);

        $this->context = $context;
    }

    /**
     * Calls Google's recaptcha API endpoints to verify the token
     * generated on the frontend. Response is JSON with score and
     * result values.
     *
     * @HttpMethods("POST")
     * @return void
     */
    public function doVerify() {

        $request = $this->context->getRequest();
        $token = $request->request->get('token');

        if (empty($token)) {
            return $this->renderJson(
                ['error' => 'Recaptcha token missing.'],
                $this->context,
                ['status_code' => 400]
            );
        }

        $response = wp_remote_post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'user-agent' => 'CoreThemeComponents',
                'httpversion' => '1.1',
                'body' => [
                    'secret' => RECAPTCHA_V3_SECRET_KEY,
                    'response' => $token
                ]
            ]
        );

        if (is_wp_error($response) || !isset($response['body'])) {
            return $this->renderJson(
                ['error' => 'Recaptcha validation failed. Code 1.'],
                $this->context,
                ['status_code' => 400]
            );
        }

        $jsonResponse = json_decode($response['body'], true);

        if (!is_array($jsonResponse)) {
            return $this->renderJson(
                ['error' => 'Recaptcha validation failed. Code 2.'],
                $this->context,
                ['status_code' => 400]
            );
        }

        $score = isset($jsonResponse['score']) ? floatval($jsonResponse['score']) : 0;
        $threshold = defined('RECAPTCHA_V3_SCORE') ? floatval(RECAPTCHA_V3_SCORE) : 0.5;

        return $this->renderJson(
            [
                'score' => $score,
                'result' => ($score >= $threshold) ? true : false,
            ],
            $this->context
        );

    }

}
