<?php

/**
 * Interface for building WP CLI commands.
 *
 * @version 1.0.0
 */

namespace App\Themes\MySite\Contracts;

interface Command
{

    /**
     * @return string
     */
    public function __toString();
}
