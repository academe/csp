<?php

namespace Academe\Csp;

/**
 * A single policy can have multiple directives.
 * A web page can be delivered with multiple policies, which are enforced
 * according to the RFC (http://www.w3.org/TR/2014/WD-CSP11-20140211/)
 * Rendering a policy will involve rendering all the directives and combining
 * them together.
 */

class Policy implements \Iterator
{
    /**
     * Multiple directives can be held in this policy.
     */

    protected $directive_list = array();

    /**
     * The joiner of directives in a policy.
     * The space is optional in the RFC, but used here for visual clarity.
     */

    protected $policy_joiner = '; ';

    /**
     * Flags to indicate how to handle duplicates when adding directives.
     */

    // Raise an exception.
    const DUP_ERROR = 1;

    // Replace the existing directive.
    const DUP_REPLACE = 2;

    // Silectly discard the new directive.
    const DUP_DISCARD = 3;

    // Append the new directive to the existing one.
    const DUP_APPEND = 4;

    /**
     * Iterator methods for looping over the difrectives.
     */

    function rewind() {
        return reset($this->directive_list);
    }
    function current() {
        return current($this->directive_list);
    }
    function key() {
        return key($this->directive_list);
    }
    function next() {
        return next($this->directive_list);
    }
    function valid() {
        return key($this->directive_list) !== null;
    }

    /**
     * Convert to a string.
     */

    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Convert to a string for use in a header or meta tag.
     */

    public function toString()
    {
        return implode($this->policy_joiner, $this->directive_list);
    }

    /**
     * Add a directive to the list.
     * Each directive can only be used once, so we have a choice
     * on how to handle duplicates, depending on what we are doing (building,
     * parsing, validating).
     */

    public function addDirective(Directive $directive, $duplicate_handler = self::DUP_ERROR)
    {
        // Get the name for indexing the directive list.
        $normalised_name = $directive->getNormalisedName();

        if ( ! isset($this->directive_list[$normalised_name]) || $duplicate_handler == self::DUP_REPLACE) {
            // Not a duplicate, or we are replacing duplicates, just add it to the list.
            $this->directive_list[$normalised_name] = $directive;
        } else {
            // We have been given a duplicate, so we need to handle that.
            if ($duplicate_handler == self::DUP_ERROR) {
                // TODO: raise exception.
            } elseif ($duplicate_handler == self::DUP_APPEND) {
                // The directive will handle duplicate source expressions given to it.
                $this->directive_list[$normalised_name]->addSourceExpressionList($directive->getSourceExpressionList());
            } elseif ($duplicate_handler == self::DUP_DISCARD) {
                // Discarding, so just do nothing.
                // This is how a browser would interpret directive duplicates.
            } else {
                // TODO: unknown handler flag; raise an exception.
            }
        }

        return $this;
    }
}

