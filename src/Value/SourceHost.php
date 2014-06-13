<?php

namespace Academe\Csp\Value;

/**
 * Host source expression.
 */

use Academe\Csp\Parse as Parse;

class SourceHost extends SourceAbstract
{
    /**
     * The source type.
     */

    const SOURCE_TYPE = 'host';

    /**
     * The scheme expression.
     */

    protected $host;

    /**
     * Set the host on construction.
     */

    public function __construct($host)
    {
        $this->setHost($host);
    }

    /**
     * Get the host expression, in decoded form.
     */

    public function getHost()
    {
        return $this->decode($this->host);
    }

    /**
     * Set the host expression.
     * Passed in without encoding.
     */

    public function setHost($host)
    {
        // Store in encoded form.
        $this->host = $this->encode($host);
    }

    /**
     * Render the source expression.
     */

    public function render()
    {
        return $this->host;
    }

    /**
     * Decode percentage encoding from a source expression.
     * The RFC states that only ; and , will be encoded, and only into %3B and %2C respectively.
     * We will take it at face value and just decode those two characters.
     * TODO: we should make the decoding case-insensitive. Catering for bad encoding is a
     * pragmatic approach to help reliability when we have no control of the source data.
     * It may make more sence to decode ANY percent-encoded character using rawurldecode() and make it
     * case-insensitive.
     * TODO: these are Academe\Csp\Parse::DIRECTIVE_SEP and Academe\Csp\Parse::HEADER_VALUE_SEP
     */

    public static function decode($source_expression)
    {
        return str_replace(
            array('%3B', '%2C'),
            array(Parse::DIRECTIVE_SEP, Parse::HEADER_VALUE_SEP),
            $source_expression
        );
    }

    /**
     * Percent encode a source expression.
     * Only commas and semi-colons need to be encoded.
     */

    public static function encode($source_expression)
    {
        return str_replace(
            array(Parse::DIRECTIVE_SEP, Parse::HEADER_VALUE_SEP),
            array('%3B', '%2C'),
            $source_expression
        );
    }
}
