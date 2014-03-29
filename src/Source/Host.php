<?php

namespace Academe\Csp\Source;

/**
 * Host source expression.
 */

class Host implements SourceInterface
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
     * Get the host expression.
     */

    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set the host expression.
     * Passed in without encoding.
     */

    public function setHost($host)
    {
        // TODO: Encode the host string.
        $this->host = $host;
    }

    /**
     * Render the source expression.
     */

    public function render()
    {
        return $this->host;
    }

    /**
     * Render the source expression.
     */

    public function __toString()
    {
        return $this->render();
    }
}
