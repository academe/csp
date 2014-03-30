<?php

namespace Academe\Csp\Helper;

/**
 * Encoding and decoding.
 * Deprecated: moved to Source\Host, closer to where it is needed.
 */

class Encode
{
    /**
     * Decode percentage encoding from a source expression.
     * The RFC states that only ; and , will be encoded, and only into %3B and %2C respectively.
     * We will take it at face value and just decode those two characters.
     * It may make more sence to decode ANY percent-encoded character using rawurldecode() and make it
     * case-insensitive.
     */

    public static function decodeSourceExpression($source_expression)
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

    public static function encodeSourceExpression($source_expression)
    {
        return str_replace(
            array(';', ','),
            array('%3B', '%2C'),
            $source_expression
        );
    }
}
