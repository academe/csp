<?php

namespace Academe\Csp\Value;

/**
 * Referrer directive value.
 */

class Referrer extends SourceAbstract
{
    /**
     * Allowed values.
     */

    const REF_NEVER = 'never';
    const REF_DEFAULT = 'default';
    const REF_ORIGIN = 'origin';
    const REF_ALWAYS = 'always';

    /**
     * Return an array of lower-case valid referrer tokens.
     * TODO: this REALLY needs to go into the abstract.
     */

    public static function validTokens()
    {
        // Get the constants.
        $reflect = new \ReflectionClass(get_called_class());
        $constants = $reflect->getConstants();

        // Filter out constants that don't start with KEYWORD_
        foreach($constants as $name => $value) {
            if (substr($name, 0, 4) != 'REF_') {
                unset($constants[$name]);
            }
        }

        return $constants;
    }
}
