<?php
/**
 * Interface for serializer objects.
 *
 * @version 1.0.0
 */
namespace App\Themes\MySite\Contracts;

interface Serializer {

    /**
     * @return string
     */
    public function __toString(): string;

}