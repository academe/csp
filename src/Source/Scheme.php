<?php

namespace Academe\Csp\Source;

/**
 * Scheme source expression.
 * TODO: all source classes need a common interface for parameter validation.
 */

class Scheme
{
    /**
     * The scheme (without training colon).
     */

    protected $scheme;

    /**
     * Set the scheme on construction.
     */

    public function __construct($scheme)
    {
        $this->setScheme($scheme);;
    }

    /**
     * Get the scheme.
     */

    public function getScheme($scheme)
    {
        return $this->scheme;
    }

    /**
     * Set the scheme.
     */

    public function setScheme($scheme)
    {
        // Remove any trailing colon.

        $scheme = rtrim($scheme, ':');

        // TODO: The scheme is validated to RFC 3986, section 3.1
        // scheme = ALPHA *( ALPHA / DIGIT / "+" / "-" / "." )

        if ( ! preg_match('/^[a-z][a-z0-9+.-]*$/i', $scheme)) {
            // TODO: throw custom exception
            throw new \InvalidArgumentException('Invalid source scheme ' . $scheme);
        }

        $this->scheme = $scheme;

        return $this;
    }

    /**
     * Render the source expression.
     */

    public function render()
    {
        return $this->scheme . ':';
    }
}
