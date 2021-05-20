<?php
/**
 * A post model class for "articles".
 *
 * @version 1.0.0
 */
namespace App\Themes\MySite\Http\Models;

use App\Themes\MySite\Http\Models\Traits\Queryable;

class Article extends CorePost {

    // We can set a custom ResultSet class here.
    // public const RESULT_SET_CLASS = SomeCustomResultSetClass::class;

    use Queryable;

}