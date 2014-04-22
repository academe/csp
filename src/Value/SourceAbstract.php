<?php

namespace Academe\Csp\Value;

/**
 * Source abstract.
 * TODO: this is now a more generic "directive value" abstract. Rename and perhaps move it.
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

    /**
     * Return an array of constants with a given prefix, in the called class.
     * TODO: can this be cached, and still be called statically?
     */

    protected static function getPrefixedConstants($prefix)
    {
        // Get the constants.
        $reflect = new \ReflectionClass(get_called_class());
        $constants = $reflect->getConstants();

        $prefix_length = strlen($prefix);

        // Filter out constants that don't start with the prefix.
        foreach($constants as $name => $value) {
            if (substr($name, 0, $prefix_length) != $prefix) {
                unset($constants[$name]);
            }
        }

        return $constants;
    }

    /**
     * Check if a value is one of an allowed list of values.
     * The list comes from constants with the supplied prefix.
     * TODO: don't assume the valid values will all be lower case.
     */

    public static function isValidValue($prefix, $value)
    {
        $valid_values = static::getPrefixedConstants($prefix);

        return in_array(strtolower($value), $valid_values);
    }
}


