<?php

/**
 * @version 1.0.0
 */

namespace App\Themes\CoreTheme\Http\Models\Traits;

use ReflectionClass;
use ReflectionMethod;

use App\Themes\CoreTheme\Services\Managers\TimberManager;
use App\Themes\CoreTheme\Services\Repositories\PostsResultSet;
use App\Themes\CoreTheme\Contracts\ResultSet;

/**
 * Adds data fetching methods to Model classes.
 */
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

        return (new ReflectionMethod($resultSet, 'factory'))->invoke(null, self::class);

    }

    /**
     * Returns the ResultSet class in use.
     *
     * A model can define its own ResultSet class by setting the following
     * constant:
     *
     * `public const RESULT_SET_CLASS = SomeCustomResultSetClass::class;`
     *
     * Defaults to PostsResultSet::class.
     *
     * @see PostsResultSet
     * @return string
     */
    public static function getResultSetClass(): string {

        return defined(__CLASS__ . '::RESULT_SET_CLASS') ? self::RESULT_SET_CLASS : PostsResultSet::class;

    }

}