<?php

namespace Academe\Csp;

/**
 * The directive model. A single directive.
 * Purpose:
 * - Holds main directive details
 * - Holds list of source expressions
 * - Should it contain rendering/formatting methods like Active Record,
 *   or just data like Data Mapper? Let's start with the latter and see
 *   where it takes us.
 * TODO: make this an abstract, so it can be extended.
 * TODO: implement a status so it can be marked as invalid if appropriate.
 * Is is http://www.php.net/manual/en/class.arrayobject.php we want, rather than \Interator?
 */

class Directive implements \Iterator
{
    /**
     * The directive name.
     * The name can take any mix of letter case as the name is case insensitive.
     */

    protected $name;

    /**
     * The list of sources.
     */

    protected $source_list = array();

    /**
     * Iterator methods for looping over the directives.
     */

    function rewind() {
        return reset($this->source_list);
    }
    function current() {
        return current($this->source_list);
    }
    function key() {
        return key($this->source_list);
    }
    function next() {
        return next($this->source_list);
    }
    function valid() {
        return key($this->source_list) !== null;
    }

    /**
     * Directive name can be set on creation.
     */

    public function __construct($name = null)
    {
        if (isset($name)) {
            $this->setName($name);
        }
    }

    /**
     * Convert to a string.
     */

    public function __toString()
    {
        return $this->render();
    }

    /**
     * Convert to a string for use in a header or meta tag.
     */

    public function render()
    {
        // Percent encode each source in the list.
        $encoded = array_map(array(__NAMESPACE__ . '\Helper\Encode', 'encodeSourceExpression'), $this->source_list);

        // Join the sources together with a space and prefix the directive name.
        return trim($this->getName() . ' ' . implode(' ', $encoded));
    }

    /**
     * Return a normalised directive name, so unique names can be compared.
     */

    public function getNormalisedName()
    {
        return strtolower($this->name);
    }

    /**
     * Get the directive name.
     */

    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the directive name.
     * TODO: validate it is one of the valid names (case insensitive).
     */

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Add a single source expression string to the list.
     * This source expression should NOT be percent encoded. Encoding
     * is a function of the rendering of a policy to a string, and does
     * not form part of the policy syntax.
     * Duplicates are skipped.
     * These are just strings for now, and I'm not sure the benefit of
     * takening them further as objects.
     */

    public function addSourceExpression($source)
    {
        if ( ! in_array($source, $this->source_list)) {
            $this->source_list[] = $source;
        }

        return $this;
    }

    /**
     * Add a source expression list (an array).
     */

    public function addSourceExpressionList($source_list)
    {
        foreach($source_list as $source) {
            $this->addSourceExpression($source);
        }

        return $this;
    }

    /**
     * Get all the source expressions.
     */

    public function getSourceExpressionList($source_list)
    {
        return $this->source_list;
    }
}

