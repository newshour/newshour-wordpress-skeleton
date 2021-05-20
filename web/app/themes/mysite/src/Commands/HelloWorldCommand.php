<?php
/**
 * This is an example WP CLI command. All commands must be registered in the bootstrapper
 * in functions.php. To run, open your terminal and type the following command from the
 * project root:
 *
 * wp hello-world
 *
 * To see what all you can do with commands, please visit the WP CLI documentation site
 * @ https://wp-cli.org/.
 *
 * To run commands as cron jobs, you will need to provide full paths to the crontab. e.g.:
 *
 * php /usr/local/bin/wp hello-world --path=/path/to/wordpress/web/wp > /dev/null 2>&1
 */
namespace App\Themes\MySite\Commands;

use WP_CLI;
use App\Themes\MySite\Contracts\Command;

class HelloWorldCommand implements Command {

    public const COMMAND_NAME = 'hello-world';

    /**
     * The name of the command used by the CLI. e.g. `wp command-name args ...`
     *
     * @return string
     */
    public function __toString() {

        return self::COMMAND_NAME;

    }

    /**
     * Run the command.
     *
     * @param  array $args
     * @return mixed
     */
    public function __invoke($args) {

        WP_CLI::line("Hello World!");

        WP_CLI::success(
            sprintf("The %s command ran.", (string)$this)
        );

    }

}
