<?php
/**
 * Provides a fluent interface for fetching data from WP. For example, to retrieve the
 * 10 latest posts of a give Model class:
 *
 * Model::objects()->filter(['posts_per_page' => 10])->orderBy('post_date')->desc()->get();
 *
 * or even more concise:
 *
 * Model::objects()->latest()->get();
 *
 * @version 1.0.0
 */
namespace App\Themes\MySite\Services\Repositories;

use App\Themes\MySite\Contracts\ResultSet;

use Timber\PostQuery;

final class PostsResultSet implements ResultSet {

    // Stores the cache expires time in seconds. -1 is no cache, 0 is cache forever.
    private $cacheInSeconds = -1;

    // The model class that Timber will use to instantiate.
    private $postClass = '';

    // Stores the query params.
    private $queryParams = [];

    // Stores the data.
    private $data = [];

    /**
     * @param string $postClass
     * @param array $queryParams
     */
    private function __construct(string $postClass = '', array $queryParams = []) {

        $initial = [
            'order' => 'DESC',
            'orderby' => 'ID',
            'post_type' => 'post',
            'post_status' => 'publish',
            'no_found_rows' => true
        ];

        $this->postClass = empty($postClass) ? '\Timber\Post' : $postClass;
        $this->queryParams = array_merge($initial, $queryParams);

    }

    /**
     * @return string
     */
    public function __toString(): string {

        return sprintf(
            '%s<%s> [query params: %s]',
            self::class, $this->postClass,
            http_build_query($this->queryParams)
        );

    }

    /**
     * Creates a ResultSet instance.
     *
     * @param string $postClass
     * @param array $params
     * @return ResultSet
     */
    public static function factory($postClass = '', array $params = []): ResultSet {

        return new PostsResultSet($postClass, $params);

    }

    // ------------------------------------------------------------------------
    // Methods that WILL hit the database.
    // ------------------------------------------------------------------------

    /**
     * Returns the result set array based on the set query params.
     * This method will hit the database.
     *
     * @return array
     */
    public function get(): array {

        if (!empty($this->data)) {
            return $this->data;
        }

        $useCache = $this->cacheInSeconds > -1 ? true : false;

        if ($useCache) {

            $cacheKey = __FUNCTION__ . http_build_query($this->queryParams);
            $cacheGroup = __CLASS__ . "<{$this->postClass}>";
            $cachedPosts = wp_cache_get($cacheKey, $cacheGroup);

            if ($cachedPosts !== false && count($cachedPosts) > 0) {
                $this->data = $cachedPosts;
                return $this->data;
            }

        }

        $this->data = (new PostQuery($this->queryParams, $this->postClass))->get_posts();

        if ($useCache && count($this->data) > 0) {
            wp_cache_set($cacheKey, $this->data, $cacheGroup);
        }

        return $this->data;

    }

    /**
     * Retrieve only the first result. This method will hit the database.
     *
     * @return array
     */
    public function first(): array {

        return $this->limit(1)->get();

    }

    /**
     * Returns a slice of the collection starting at the given index.
     * Similar to Laravel's slice(). This method will hit the database.
     *
     * @param int $start
     * @return array
     */
    public function slice($start): array {

        $localArray = $this->get();

        if (count($localArray) < 1) {
            return [];
        }

        return array_slice($localArray, $start);

    }

    /**
     * Shuffles (and slices) the result set. This method will hit the database.
     *
     * @param integer $andSlice - optional
     * @return array
     */
    public function shuffle($andSlice = 0): array {

        $localArray = $this->get();

        if (count($localArray) < 1) {
            return [];
        }

        shuffle($localArray);

        if ($andSlice < 1) {
            return $localArray;
        }

        return array_slice($localArray, 0, $andSlice);

    }

    // ------------------------------------------------------------------------
    // Methods that WILL NOT hit the database.
    // ------------------------------------------------------------------------

    /**
     * Retrieve results with any status.
     *
     * @return ResultSet
     */
    public function any(): ResultSet {

        $this->queryParams['post_status'] = 'any';
        return $this;

    }

    public function asc(): ResultSet {

        return $this->order('ASC');

    }

    /**
     * Sets the cache expires time in seconds. -1 is no cache, 0 is
     * cache forever.
     *
     * @param int $seconds
     * @return ResultSet
     */
    public function cache($seconds): ResultSet {

        $this->cacheInSeconds = (int)$seconds < 0 ? -1 : (int)$seconds;
        return $this;

    }

    /**
     * Sets the cache to store forever.
     *
     * @return ResultSet
     */
    public function cacheForever(): ResultSet {

        return $this->cache(0);

    }

    /**
     * @return ResultSet
     */
    public function desc(): ResultSet {

        return $this->order('DESC');

    }

    /**
     * Exclude by ID or parent ID.
     *
     * @param array $excludeIds
     * @param boolean $parent
     * @return ResultSet
     */
    public function exclude(array $excludeIds, $parent = false): ResultSet {

        $key = $parent ? 'post_parent__not_in' : 'post__not_in';
        $this->queryParams[$key] = $excludeIds;

        return $this;

    }

    /**
     * Filter a query by keyword args. See WP_Query documentation for a full
     * list of args.
     *
     * @param array $params
     * @return ResultSet
     */
    public function filter(array $params): ResultSet {

        $this->queryParams = array_merge($params, $this->queryParams);
        return $this;

    }

    /**
     * Fetch the latest entries by post_date.
     *
     * @param int $limit
     * @return ResultSet
     */
    public function latest($limit = 10): ResultSet {

        return $this->orderBy('post_date')->limit((int)$limit);

    }

    /**
     * Set the `posts_per_page` field.
     *
     * @param int $limit
     * @return ResultSet
     */
    public function limit($limit): ResultSet {

        $this->queryParams['posts_per_page'] = (int)$limit;
        return $this;

    }

    /**
     * Sets the `fields` parameter to `ids`.
     *
     * @return ResultSet
     */
    public function ids(): ResultSet {

        $this->queryParams['fields'] = 'ids';
        return $this;

    }

    /**
     * Undocumented function
     *
     * @return ResultSet
     */
    public function nocache(): ResultSet {

        return $this->cache(0);

    }

    /**
     * Sets the `order` parameter.
     *
     * @param string $order
     * @return ResultSet
     */
    public function order($order = 'DESC'): ResultSet {

        $_order = strtoupper($order);

        if (in_array($_order, ['DESC', 'ASC'])) {
            $this->queryParams['order'] = $_order;
        }

        return $this;

    }

    /**
     * Sets the `orderby` value.
     *
     * @param string $by
     * @return ResultSet
     */
    public function orderBy($by): ResultSet {

        if (!empty($by)) {
            $this->queryParams['orderby'] = $by;
        }

        return $this;

    }

    /**
     * Sets the `paged` parameter and sets `no_found_rows` to false.
     *
     * @param [type] $num
     * @return void
     */
    public function page($num): ResultSet {

        if ((int)$num > -1) {
            $this->queryParams['paged'] = (int)$num;
            $this->queryParams['no_found_rows'] = false;
        }

        return $this;

    }

}