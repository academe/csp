<?php

namespace Academe\Csp\Source;

/**
 * "None" source expression.
 */

class None extends SourceAbstract
{
    /**
     * The source type.
     */

    const SOURCE_TYPE = 'none';

    /**
     * The empty directive set has just one fixed value..
     */

    const EMPTY_SET_EXPRESSION = "'none'";

    /**
     * Render the source expression.
     */

    public function render()
    {
        return static::EMPTY_SET_EXPRESSION;
    }
}

