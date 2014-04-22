<?php

namespace Academe\Csp\Value;

/**
 * Hash source expression.
 * TODO: the Hash can just extend the Nonce (except for the constructor parameters?).
 */

class SourceHash extends SourceAbstract
{
    /**
     * The source type.
     */

    const SOURCE_TYPE = 'hash';

    /**
     * The valid algorithms.
     */

    const ALGO_SHE256 = 'sha256';
    const ALGO_SHE384 = 'sha384';
    const ALGO_SHE512 = 'sha512';

    const VALUE_LIST_PREFIX = 'ALGO_';

    /**
     * The selected algorithm.
     */

    protected $algo;

    /**
     * The hash base64 value.
     */

    protected $base64_value = '';

    /**
     * Set the algo on construction and optionally the non-encoded value.
     */

    public function __construct($algo, $hash_value = null)
    {
        $this->setAlgo($algo);

        if (isset($hash_value)) {
            $this->setValue($hash_value);
        }
    }

    /**
     * Return an array of lower-case valid algos, keyed on the constant name.
     */

    public static function validAlgos()
    {
        return static::getPrefixedConstants(static::VALUE_LIST_PREFIX);
    }

    /**
     * Set the hash algo.
     */

    public function setAlgo($algo)
    {
        // Algo is case-insensitive. We will just make it lower case, as we don't
        // know where it could have come from while parsing.

        // Can also pass in the string name of the constant, which will be useful
        // as keys in drop-down lists.
        if (defined('static::' . $algo)) {
            $algo = constant('static::' . $algo);
        }

        if ( ! $this->isValidValue(self::VALUE_LIST_PREFIX, $algo)) {
            // TODO: throw custom exception
            throw new \InvalidArgumentException('Invalid source hash algo ' . $algo);
        }

        $this->algo = $algo;

        return $this;
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
     * TODO: we probably want to handle invalid base64 strings, since they
     * can come from unknown sources.
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
        return "'" . $this->algo . '-' . $this->base64_value . "'";
    }
}

