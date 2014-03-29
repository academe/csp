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

    const DIRECTIVE_DELIMITER = '; ';

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
        return $this->render();
    }

    /**
     * Render as a string for use in a header or meta tag.
     */

    public function render()
    {
        return implode(static::DIRECTIVE_DELIMITER, $this->directive_list);
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
                $this->directive_list[$normalised_name]->addSourceList($directive->getSourceList());
            } elseif ($duplicate_handler == self::DUP_DISCARD) {
                // Discarding, so just do nothing.
                // This is how a browser would interpret directive duplicates.
            } else {
                // Unknown handler flag.
                // TODO raise a custom exception.
                throw new \InvalidArgumentException('Invalid duplicate handler flag ' . $duplicate_handler);
            }
        }

        return $this;
    }

    /**
     * Add a source expression.
     * Think about this. The aim is to add a source expression to the relevant
     * directive, and create that directive if it does not already exist.
     * We need to know the expression (source object or string?) and directive name.
     */

    public function addSource($directive_name, Source\SourceInterface $source)
    {
        // The directive name can be the full name ('default-src')
        // or the constant name ('DIR_DEFAULT_SRC').

        // Create a new directive.
        $directive = new Directive($directive_name);

        // Add the source to the directive.
        $directive->addSource($source);

        // Add this directive to the policy, appending the source if the
        // policy already exists.
        $this->addDirective($directive, self::DUP_APPEND);

        return $this;
    }
}

