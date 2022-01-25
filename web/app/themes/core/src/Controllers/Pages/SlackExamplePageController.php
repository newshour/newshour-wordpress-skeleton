<?php

/**
 * @version 1.0.0
 */

namespace App\Themes\CoreTheme\Controllers\Pages;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
use NewsHour\WPCoreThemeComponents\Annotations\LoginRequired;
use NewsHour\WPCoreThemeComponents\Annotations\HttpMethods;
use NewsHour\WPCoreThemeComponents\Contexts\Context;
use NewsHour\WPCoreThemeComponents\Controllers\Controller;
use NewsHour\WPCoreThemeComponents\Components\Meta\MetaFactory;

use function Env\env;

/**
 * An example controller which sends a message to Slack. This controller also has the LoginRequired
 * annotation set at the class level.
 *
 * @LoginRequired
 */
class SlackExamplePageController extends Controller
{
    private Context $context;
    private ChatterInterface $slack;

    /**
     * @param Context $context
     * @param MetaFactory $metaFactory
     */
    public function __construct(Context $context, MetaFactory $metaFactory, ChatterInterface $slack)
    {
        $this->context = $context;
        $this->slack = $slack;

        $post = $this->context['post'];

        if ($post != null) {
            add_action('wp_head', function () use ($post, $metaFactory) {
                echo implode(PHP_EOL, [
                    (string) $metaFactory->getPageMeta($post),
                    (string) $metaFactory->schemas()->getWebPageSchema($post)->asHtml()
                ]);
            });
        }
    }

    /**
     * The main "view" method.
     *
     * @return Response
     */
    public function view(): Response
    {

        // Render our template and send it back to the client.
        return $this->render('pages/slack_example_page.twig', $this->context);
    }

    /**
     * This method provides a very basic example of sending messages to Slack using
     * Symfony's Notifier component. In a real web application, you could move much of
     * this logic into a form handler and/or offload the task to a work queue or cronjob
     * to avoid blocking the main request/response.
     *
     * @HttpMethods("POST")
     * @return Response
     */
    public function doSendSlackMessage(): Response
    {
        $request = $this->context->getRequest();

        // Validate the nonce.
        valid_nonce_or_abort($request->request->get(NONCE_FIELD_NAME));

        $slackDsn = env('SLACK_DSN');

        if (empty($slackDsn)) {
            $this->context['error_msg'] = '`SLACK_DSN` must be set in your .env file.';
            return $this->view();
        };

        $slackMessage = $request->request->get('slack_message');

        if (empty($slackMessage)) {
            $this->context['error_msg'] = 'Your Slack message cannot be empty.';
            return $this->view();
        }

        $sentMessage = $this->slack->send(
            new ChatMessage($slackMessage)
        );

        if (empty($sentMessage)) {
            $this->context['error_msg'] = 'Your message could not be sent to Slack.';
        } else {
            $this->context['success_msg'] = sprintf(
                'Your message was sent to Slack! Message ID: %s',
                $sentMessage->getMessageId()
            );
        }

        return $this->view();
    }
}
