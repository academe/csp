<?php

namespace Academe\Csp\Source;

/**
 * Keyword source expression.
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
     * Return an array of valid keywords.
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
     */

    public function setKeyword($keyword)
    {
        // Make sure it has quotes.
        $keyword = "'" . trim($keyword, "'") . "'";

        // Check it is in the valid list.
        $keywords = $this->validKeywords();

        if ( ! is_array($keywords, $keyword)) {
            // TODO: throw exception
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

