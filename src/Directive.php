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
 * TOOD: implement an interface that allows the source list to be iterated over.
 * TODO: implement a status so it can be marked as invalid if appropriate.
 */

class Directive
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
     * Directive name can be set on creation.
     */

    public function __construct($name = null)
    {
        if (isset($name)) {
            $this->setName($name);
        }
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
     */

    public function addSourceExpression($source)
    {
        $this->source_list[] = $source;

        return $this;
    }

    public function addSourceExpressionList($source_list)
    {
        foreach($source_list as $source) {
            $this->addSourceExpression($source);
        }

        return $this;
    }
}

