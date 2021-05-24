<?php

/**
 * Interface for building WP CLI commands.
 *
 * @version 1.0.0
 */

namespace App\Themes\CoreTheme\Contracts;

interface Command
{

    /**
     * @return string
     */
    public function __toString();
}
