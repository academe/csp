<?php

namespace Academe\Csp\Value;

/**
 * Nonce source expression.
 */

class SourceNonce extends SourceAbstract
{
    /**
     * The source type.
     */

    const SOURCE_TYPE = 'nonce';

    /**
     * The string for rendering.
     */

    const NONCE_NAME = 'nonce';

    /**
     * The nonce base64 value.
     */

    protected $base64_value = '';

    /**
     * Nonce value (not base64 encoded) can be supplied on construction.
     */

    public function __construct($nonce_value = null)
    {
        if (isset($nonce_value)) {
            $this->base64_value = base64_encode($nonce_value);
        }
    }

    /**
     * Set the hash base64 value.
     * This is handy when parsing, to reduce the number of decode/encode cycles.
     */

    public function setValueBase64($value)
    {
        // TODO: validate the value; make sure it is a base64 string.

        $this->base64_value = $value;

        return $this;
    }

    /**
     * Set the hash actual value.
     */

    public function setValue($value)
    {
        $this->setValueBase64(base64_encode($value));

        return $this;
    }

    /**
     * Get the actual hash value.
     */

    public function getValue()
    {
        return base64_decode($this->base64_value);
    }

    /**
     * Render the source expression.
     */

    public function render()
    {
        return "'" . static::NONCE_NAME . '-' . $this->base64_value . "'";
    }
}

