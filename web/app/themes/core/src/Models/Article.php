<?php

/**
 * @version 1.0.0
 */

namespace App\Themes\CoreTheme\Models;

use NewsHour\WPCoreThemeComponents\Models\CorePost;
use NewsHour\WPCoreThemeComponents\Query\Queryable;

/**
 * A post model class for "articles".
 */
class Article extends CorePost
{
    use Queryable;

    // We can set a custom ResultSet class here.
    // public const RESULT_SET_CLASS = SomeCustomResultSetClass::class;
}
