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

    public function validKeywords()
    {
        return array(
            static::KEYWORD_NONE,
            static::KEYWORD_SELF,
            static::KEYWORD_UNSAFE_INLINE,
            static::KEYWORD_UNSAFE_EVAL,
        );
    }

    /**
     * Set the keywork.
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
     * Provide the keyword at construction.
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
