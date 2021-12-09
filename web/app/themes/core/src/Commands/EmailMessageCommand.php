<?php

/**
 * @version 1.0.0
 */

namespace App\Themes\CoreTheme\Commands;

use WP_CLI;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use NewsHour\WPCoreThemeComponents\Commands\AbstractCommand;

/**
 * Sends an example email message via the CLI.
 *
 * This is an example WP CLI command which will send a Email message using the transport
 * set in `config/packages/mailer.yml`. The command extends AbstractCommand which makes
 * the container available and services can be type-hinted.
 *
 * @final
 */
final class EmailMessageCommand extends AbstractCommand
{
    public const COMMAND_NAME = 'email-example';

    private MailerInterface $mailer;

    /**
     * @param MailerInterface $mailer
     */
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * The name of the command used by the CLI. e.g. `wp command-name args ...`
     *
     * @return string
     */
    public function __toString()
    {
        return self::COMMAND_NAME;
    }

    /**
     * Run the command.
     *
     * @param array $args
     * @return mixed
     */
    public function __invoke($args)
    {
        $email = new Email();
        $email->from('hello@example.com')
            ->to('you@example.com')
            ->subject('Hello')
            ->text('...world!');

        $this->mailer->send($email);

        WP_CLI::success("Your email message was sent.");
    }
}
