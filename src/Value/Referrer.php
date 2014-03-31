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
     * Prefix for allowed values constants.
     */

    const VALUE_LIST_PREFIX = 'REF_';

    /**
     * Return an array of lower-case valid referrer tokens.
     */

    public static function validTokens()
    {
        return static::getPrefixedConstants(static::VALUE_LIST_PREFIX);
    }

    /**
     * Return true if the supplied string is a valid keyword keyword.
     */

    public static function isValidToken($token)
    {
        return static::isValidValue(static::VALUE_LIST_PREFIX, $token);
    }

    /**
     * Set the token on construction.
     */

    public function __construct($token)
    {
        $this->setToken($token);
    }

    /**
     * Set the token.
     */

    public function setToken($token)
    {
        if ( ! $this->isValidToken($token)) {
            throw new \InvalidArgumentException('Invalid referrer token ' . $token);
        }

        $this->token = $token;

        return $this;
    }

    /**
     * Get the token.
     */

    public function getToken()
    {
        return $this->token;
    }

    /**
     * Render the token.
     */

    public function render()
    {
        return $this->token;
    }
}
