<?php
/**
 * An abstract parent class for Model classes.
 *
 * @version 1.0.0
 */
namespace App\Themes\CoreTheme\Http\Models;

use Timber\Post;
use Timber\TextHelper;

abstract class CorePost extends Post {

    // Storage for categories and tags.
    private array $storage = [];

    /**
     * Get the post excerpt.
     *
     * @return string
     */
    public function excerpt(): string {

        if (empty($this->post_excerpt)) {
            return TextHelper::trim_words($this->content(), 55, '...', '');
        }

        return strip_tags($this->post_excerpt);

    }

    /**
     * Overrides parent::categories() so that we store the data.
     *
     * @return array
     */
    public function categories(): array {

        if (empty($this->storage['categories'])) {
            $this->storage['categories'] = parent::categories();
        }

        return $this->storage['categories'];

    }

    /**
     * Overrides parent::tags() so that we store the data.
     *
     * @return array
     */
    public function tags(): array {

        if (empty($this->storage['tags'])) {
            $this->storage['tags'] = parent::tags();
        }

        return $this->storage['tags'];

    }

}