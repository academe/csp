<?php

namespace Academe\Csp\Value;

/**
 * Keyword source expression.
 * TODO: 'none' is not a keyword, but is mutually exclusive to all other source expressions.
 * Have a think about this, because it may be worth leaving it here as an implementaton dertail.
 */

class SourceKeyword extends SourceAbstract
{
    /**
     * The source type.
     */

    const SOURCE_TYPE = 'keyword';

    /**
     * The current keyword.
     */

    protected $keyword;

    /**
     * The valid keywords that can be used.
     */

    //const KEYWORD_NONE = "'none'";
    const KEYWORD_SELF = "'self'";
    const KEYWORD_UNSAFE_INLINE = "'unsafe-inline'";
    const KEYWORD_UNSAFE_EVAL = "'unsafe-eval'";

    const VALUE_LIST_PREFIX = 'KEYWORD_';

    /**
     * Return an array of valid keywords, keyed on the constant name.
     */

    public static function validKeywords()
    {
        return static::getPrefixedConstants('KEYWORD_');
    }

    /**
     * Return true if the supplied string is a valid keyword keyword.
     */

    public static function isValidKeyword($source_expression)
    {
        return static::isValidValue(self::VALUE_LIST_PREFIX, $source_expression);
    }

    /**
     * Get the keyword.
     */

    public function getKeyword()
    {
        return $this->keyword;
    }

    /**
     * Set the keyword.
     * The surrounding quotes are optional, for convenience.
     * Can also pass in the constant name as a string, e.g. 'KEYWORD_SELF'.
     */

    public function setKeyword($keyword)
    {
        // Make sure it has quotes; trim them off first.
        $word = trim($keyword, "'");

        // Can also pass in the string name of the constant, which will be useful
        // as keys in drop-down lists.
        if (defined('static::' . $keyword)) {
            $keyword = constant('static::' . $keyword);
        } else {
            $keyword = "'" . $word . "'";
        }

        if ( ! $this->isValidValue(self::VALUE_LIST_PREFIX, $keyword)) {
            // TODO: throw custom exception
            throw new \InvalidArgumentException('Invalid source keyword ' . $keyword);
        }

        $this->keyword = $keyword;

        return $this;
    }

    /**
     * Provide the keyword at construction,
     * e.g. \Academe\Csp\Value\SourceKeyword::KEYWORD_SELF
     * or "'self'" or "KEYWORD_SELF".
     */

    public function __construct($keyword)
    {
        $this->setKeyword($keyword);
    }

    /**
     * Render the source expression.
     */

    public function render()
    {
        return $this->keyword;
    }
}

