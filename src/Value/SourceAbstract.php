<?php

namespace Academe\Csp\Value;

/**
 * Source abstract.
 */

abstract class SourceAbstract implements SourceInterface
{
    /**
     * The source type identifies the source name.
     * Values are: 'none', 'scheme', 'host', 'keyword', 'nonce', 'hash'.
     */

    //const SOURCE_TYPE = 'unknown';

    /**
     * Convert the source expression into a string.
     */

    abstract public function render();

    /**
     * Render the source expression.
     */

    public function __toString()
    {
        return $this->render();
    }

    /**
     * Return the source type, i.e. the name of the source.
     */

    public function getSourceType()
    {
        return static::SOURCE_TYPE;
    }
}


