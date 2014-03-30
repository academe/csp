<?php

namespace Academe\Csp\Source;

/**
 * Scheme source expression.
 */

class Scheme extends SourceAbstract
{
    /**
     * The source type.
     */

    const SOURCE_TYPE = 'scheme';

    /**
     * The scheme expression.
     */

    protected $scheme;

    /**
     * Set the scheme on construction.
     */

    public function __construct($scheme)
    {
        $this->setScheme($scheme);
    }

    /**
     * Get the scheme expression.
     */

    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Validate the scheme expression.
     */

    public function isValidScheme($scheme)
    {
        return preg_match('/^[a-z][a-z0-9+.-]*:$/i', $scheme);
    }

    /**
     * Set the scheme from an expression or just the name (without the colon).
     */

    public function setScheme($scheme)
    {
        // Make sure there is a trailing colon.

        $scheme = rtrim($scheme, ':') . ':';

        // The scheme name is validated to RFC 3986, section 3.1
        // scheme = ALPHA *( ALPHA / DIGIT / "+" / "-" / "." )
        // The scheme expression includes the trailing colon.

        if ( ! $this->isValidScheme($scheme)) {
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
        return $this->scheme;
    }
}
