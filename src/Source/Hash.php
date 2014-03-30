<?php

namespace Academe\Csp\Source;

/**
 * Hash source expression.
 * TODO: the Hash can just extend the Nonce (except for the constructor parameters?).
 */

class Hash extends SourceAbstract
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
        // Get the constants.
        $reflect = new \ReflectionClass(get_called_class());
        $constants = $reflect->getConstants();

        // Filter out constants that don't start with KEYWORD_
        foreach($constants as $name => $value) {
            if (substr($name, 0, 5) != 'ALGO_') {
                unset($constants[$name]);
            }
        }

        return $constants;
    }

    /**
     * Set the hash algo.
     * TODO: Retain the letter case supplied, and only normalise
     * the case when checking validity.
     */

    public function setAlgo($algo)
    {
        // Algo is case-insensitive. We will just make it lower case, as we don't
        // know where it could have come from while parsing.

        $algo = strtolower($algo);

        // Check it is in the valid list.
        $valid_algos = static::validAlgos();

        // Can also pass in the string name of the constant, which will be useful
        // as keys in drop-down lists.

        if (isset($valid_algos[$algo])) {
            $algo = $valid_algos[$algo];
        }

        if ( ! in_array($algo, $valid_algos)) {
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

