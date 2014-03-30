<?php

namespace Academe\Csp\Source;

/**
 * Host source expression.
 */

class Host extends SourceAbstract
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
     * It may make more sence to decode ANY percent-encoded character using rawurldecode() and make it
     * case-insensitive.
     */

    public static function decode($source_expression)
    {
        return str_replace(
            array('%3B', '%2C'),
            array(';', ','),
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
            array(';', ','),
            array('%3B', '%2C'),
            $source_expression
        );
    }
}
