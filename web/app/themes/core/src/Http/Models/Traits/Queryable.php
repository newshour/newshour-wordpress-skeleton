<?php
/**
 * Adds data fetching methods to Model classes.
 *
 * @version 1.0.0
 */
namespace App\Themes\CoreTheme\Http\Models\Traits;

use ReflectionClass;
use ReflectionMethod;

use App\Themes\CoreTheme\Services\Managers\TimberManager;
use App\Themes\CoreTheme\Services\Repositories\PostsResultSet;
use App\Themes\CoreTheme\Contracts\ResultSet;

trait Queryable {

    /**
     * @return ResultSet
     */
    public static function objects(): ResultSet {

        $resultSet = self::getResultSetClass();
        $reflector = new ReflectionClass($resultSet);

        if (!$reflector->implementsInterface(ResultSet::class)) {
            trigger_error(
                $resultSet . ' must be a valid ResultSet type.',
                E_USER_ERROR
            );
        }

        $methodReflector = new ReflectionMethod($resultSet, 'factory');

        // Get Timber's PostClassMap.
        $postClasses = TimberManager::classMap();

        // ...now check if our Model was registered so that we can pass the
        // post_type as an initial param. All custom post types should be
        // registered here so that Timber knows how to cast the appropriate
        // model class.
        if (is_array($postClasses)) {

            $table = array_flip($postClasses);

            if (!empty($table[self::class])) {
                return $methodReflector->invoke(
                    null,
                    self::class,
                    ['post_type' => $table[self::class]]
                );
            }

        }

        return $methodReflector->invoke(null, self::class);

    }

    /**
     * Returns the ResultSet class in use.
     *
     * @return string
     */
    public static function getResultSetClass(): string {

        return defined(__CLASS__ . '::RESULT_SET_CLASS') ? self::RESULT_SET_CLASS : PostsResultSet::class;

    }

}