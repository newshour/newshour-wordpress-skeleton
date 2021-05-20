<?php
/**
 * Loads controller classes from the Wordpress "template" files. e.g. single.php,
 * single-some-post-type.php, page.php, etc.
 *
 * @version 1.0.0
 */
namespace App\Themes\MySite\Http\Controllers;

use Exception;

use ReflectionClass;
use ReflectionMethod;
use ReflectionException;

use App\Themes\MySite\Contracts\Context;
use App\Themes\MySite\Contexts\ContextFactory;
use App\Themes\MySite\Http\Controllers\Controller;

final class FrontController {

    /**
     * Loads a controller and invokes the method. An optional context object can
     * be passed. If one is not, the default context defined in ContextFactory
     * is loaded.
     *
     * @param  string  $className
     * @param string $method
     * @param  Context $context
     * @return Controller
     */
    public static function run(string $controllerClass, string $method, Context $context = null) {

        try {

            $reflectedClass = new ReflectionClass($controllerClass);

            if (!$reflectedClass->isInstantiable() || !$reflectedClass->isSubclassOf(Controller::class)) {
                trigger_error(
                    sprintf(
                        'Error: <b>%s</b> is not a valid Controller.',
                        $controllerClass
                    ),
                    E_USER_ERROR
                );
            }

            if (!$reflectedClass->hasMethod($method)) {
                trigger_error(
                    sprintf(
                        'A method named <b>%s</b> could not be found in <i>%s</i>.',
                        $method,
                        $controllerClass
                    ),
                    E_USER_ERROR
                );
            }

            if ($context === null) {
                $context = ContextFactory::default();
            }

            $instance = $reflectedClass->newInstance($context);

            return (new ReflectionMethod($controllerClass, $method))->invoke($instance);

        } catch (ReflectionException $re) {

            trigger_error(
                sprintf('%s [stack trace] %s', $re->getMessage(), $re->getTraceAsString()),
                E_USER_ERROR
            );

        } catch (Exception $e) {

            trigger_error(
                sprintf('%s [stack trace] %s', $e->getMessage(), $e->getTraceAsString()),
                E_USER_ERROR
            );

        }

    }

}
