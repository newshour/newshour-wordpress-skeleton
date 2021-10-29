<?php

/**
 * @version 1.0.0
 */

namespace App\Themes\CoreTheme\Services\Managers;

use Symfony\Component\HttpFoundation\Request;

use Timber\Timber;

use Twig\Environment;
use Twig\TwigFunction;

use NewsHour\WPCoreThemeComponents\Managers\Manager;

use App\Themes\CoreTheme\Http\Models\Article;
use App\Themes\CoreTheme\Http\Models\Page;

/**
 * Bootstraps Timber settings and filters. Set custom post type class mappings
 * in self::classMap().
 */
class TimberManager extends Manager {

    /**
     * @param Request $request
     */
    public function __construct(Request $request) {

        parent::__construct($request);

    }

    /**
     * @return string
     */
    public function __toString(): string {

        return sprintf(
            '%s [class maps] %s', self::class, http_build_query(self::classMap())
        );

    }

    /**
     * Set your custom post type class mappings here. This will be used when
     * Timber calls the Timber/ClassMap filter.
     *
     * @return array
     */
    public static function classMap(): array {

        return [
            'page' => Page::class,
            'post' => Article::class
        ];

    }

    /**
     * @return void
     */
    public function run(): void {

        $this->initializeTimber();
        add_action('init', [$this, 'registerInitFilters'], 1);
        add_action('init', [$this, 'registerTwigFilters'], 1);

    }

    /**
     * Add any filters to run at the 'init' action.
     *
     * @return void
     */
    public function registerInitFilters(): void {

        add_filter('Timber\PostClassMap', fn($classMap) => self::classMap());

    }

    /**
     * Timber configurations.
     *
     * @return void
     */
    public function initializeTimber(): void {

        if (!defined('BASE_DIR')) {
            wp_die('BASE_DIR is not defined. The constant must be set in "config/application.php"');
        }

        $timber = new Timber();

        // Set the Timber template location.
        $timber::$locations = trailingslashit(BASE_DIR) . 'templates';

        if (defined('TIMBER_TEMPLATE_DIR')) {
            $timber::$locations = TIMBER_TEMPLATE_DIR;
        }

        // Cache twig in staging and production.
        if (WP_ENV != 'development') {
            $timber::$cache = true;
        }

    }

    /**
     * Filters related to Twig template engine.
     *
     * @return void
     */
    public function registerTwigFilters(): void {

        add_filter('timber/twig', function(Environment $twig) {
            $twig->addFunction(new TwigFunction('has_key', 'has_key'));
            return $twig;
        });

        add_filter('timber/twig', function(Environment $twig) {
            $twig->addFunction(new TwigFunction('nonce_field', 'nonce_field'));
            return $twig;
        });

    }

}
