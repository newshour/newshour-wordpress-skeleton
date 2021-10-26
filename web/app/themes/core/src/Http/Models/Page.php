<?php

/**
 * @version 1.0.0
 */

namespace App\Themes\CoreTheme\Http\Models;

use NewsHour\WPCoreThemeComponents\Models\CorePost;
use NewsHour\WPCoreThemeComponents\Query\Queryable;

/**
 * A post model class for "pages".
 */
class Page extends CorePost {

    // We can set a custom ResultSet class here.
    // public const RESULT_SET_CLASS = SomeCustomResultSetClass::class;

    use Queryable;

}