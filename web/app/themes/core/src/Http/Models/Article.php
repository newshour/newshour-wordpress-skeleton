<?php

/**
 * @version 1.0.0
 */

namespace App\Themes\CoreTheme\Http\Models;

use App\Themes\CoreTheme\Http\Models\Traits\Queryable;

/**
 * A post model class for "articles".
 */
class Article extends CorePost {

    // We can set a custom ResultSet class here.
    // public const RESULT_SET_CLASS = SomeCustomResultSetClass::class;

    use Queryable;

}