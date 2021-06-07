<?php
/**
 * Provides a service for adding new manager classes. This service is run
 * in functions.php.
 *
 * @version 1.0.0
 */
namespace App\Themes\CoreTheme\Services\Managers;

use ReflectionClass;
use SplObjectStorage;

use WP_CLI;

use Symfony\Component\HttpFoundation\Request;

use App\Themes\CoreTheme\Contracts\Command;
use App\Themes\CoreTheme\Contracts\WordpressManager;
use App\Themes\CoreTheme\Http\Factories\RequestFactory;

final class ManagerService {

    // SplObjectStorage
    private SplObjectStorage $managers;

    // Request object.
    private Request $request;

    // The singleton
    private static $instance;

    /**
     * @param Request $request
     */
    public function __construct(Request $request) {

        $this->managers = new SplObjectStorage();
        $this->request = $request;

    }

    /**
     * @return ManagerService
     */
    public static function instance() {

        if (self::$instance == null) {

            // Make sure Timber exists.
            if (!class_exists('Timber')) {

                if (is_admin()) {

                    add_action('admin_notices', function() {
                        echo '<div class="notice notice-error"><p>Timber must be activated before using this theme.</p></div>';
                    });

                } else {

                    trigger_error('Timber has not been activated.', E_USER_ERROR);

                }

            }

            // Make sure WP_HOME exists.
            if (!defined('WP_HOME') || empty(WP_HOME)) {
                trigger_error('The constant WP_HOME is not defined.', E_USER_ERROR);
            }

            self::$instance = new ManagerService(RequestFactory::get());

        }

        return self::$instance;

    }

    /**
     * Add a WordpressManager to the pipeline.
     *
     * @param  string $className
     * @return ManagerService
     */
    public function add($className, array $args = []) {

        $reflector = new ReflectionClass((string)$className);

        if (!$reflector->implementsInterface(WordpressManager::class)) {
            trigger_error((string)$className . ' is not a WordpressManager.', E_USER_WARNING);
            return $this;
        }

        array_unshift($args, $this->request);

        $this->managers->attach(
            $reflector->newInstanceArgs($args)
        );

        return $this;

    }

    /**
     * Add an array of managers.
     *
     * @param  array $classNameList
     * @return ManagerService
     */
    public function addAll(array $classNameList) {

        foreach ($classNameList as $className) {

            if (is_string($className)) {

                $this->add($className);

            } else if (is_array($className)) {

                $_className = array_shift($className);
                $args = count($className) > 0 ? $className : [];
                $this->add($_className, $args);

            }

        }

        return $this;

    }

    /**
     * Add a WP CLI command.
     *
     * @param  Command $command
     * @return ManagerService
     */
    public function addCommand($className) {

        $reflector = new ReflectionClass((string)$className);

        if (!$reflector->implementsInterface(Command::class)) {
            trigger_error((string)$className . ' is not a Command.', E_USER_WARNING);
            return $this;
        }

        $command = $reflector->newInstance();
        WP_CLI::add_command((string)$command, $command);

        return $this;

    }

    /**
     * Add an array of Commands.
     *
     * @param  array $commands
     * @return ManagerService
     */
    public function addAllCommands(array $classNames) {

        if (count($classNames) > 0) {
            foreach ($classNames as $className) {
                $this->addCommand($className);
            }
        }

        return $this;

    }

    /**
     * Run managers.
     *
     * @return void
     */
    public function run() {

        foreach ($this->managers as $manager) {
            $manager->run();
        }

    }

}
