<?php

namespace Academe\Csp\Source;

/**
 * Keyword source expression.
 * TODO: all source classes need a common interface for parameter validation.
 */

class Keyword
{
    /**
     * The current keyword.
     */

    protected $keyword;

    /**
     * The valid keywords that can be used.
     */

    const KEYWORD_NONE = "'none'";
    const KEYWORD_SELF = "'self'";
    const KEYWORD_UNSAFE_INLINE = "'unsafe-inline'";
    const KEYWORD_UNSAFE_EVAL = "'unsafe-eval'";

    /**
     * Return an array of valid keywords, keyed on the constant name.
     */

    public static function validKeywords()
    {
        // Get the constants.
        $reflect = new \ReflectionClass(get_called_class());
        $constants = $reflect->getConstants();

        // Filter out constants that don't start with KEYWORD_
        foreach($constants as $name => $value) {
            if (substr($name, 0, 8) != 'KEYWORD_') {
                unset($constants[$name]);
            }
        }

        return $constants;
    }

    /**
     * Set the keyword.
     * The surrounding quotes are optional, for convenience.
     * Can also pass in the constant name as a string, e.g. 'KEYWORD_SELF'.
     */

    public function setKeyword($keyword)
    {
        // Make sure it has quotes.
        $word = trim($keyword, "'");

        // Check it is in the valid list.
        $valid_keywords = static::validKeywords();

        // Can also pass in the string name of the constant, which will be useful
        // as keys in drop-down lists.
        if (isset($valid_keywords[$word])) {
            $keyword = $valid_keywords[$word];
        } else {
            $keyword = "'" . $word . "'";
        }

        if ( ! in_array($keyword, $valid_keywords)) {
            // TODO: throw custom exception
            throw new \InvalidArgumentException('Invalid source keyword ' . $keyword);
        }

        $this->keyword = $keyword;

        return $this;
    }

    /**
     * Provide the keyword at construction, e.g. \Academe\Csp\Source\Keyword::KEYWORD_SELF
     * TODO: make this optional? Then see note on render().
     */

    public function __construct($keyword)
    {
        $this->setKeyword($keyword);
    }

    /**
     * Render the source expression.
     * TODO: what is sensible to render if the keyword has not been set?
     */

    public function render()
    {
        return $this->keyword;
    }
}

