<?php

namespace Academe\Csp;

/**
 * A single policy can have multiple directives.
 * A web page can be delivered with multiple policies, which are enforced
 * according to the RFC (http://www.w3.org/TR/2014/WD-CSP11-20140211/)
 * Rendering a policy will involve rendering all the directives and combining
 * them together.
 * TODO: implement an iterator interface for the directives list.
 */

class Policy
{
    /**
     * Multiple directives can be held in this policy.
     */

    protected $directives = array();

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
     * Add a directive to the list.
     * Each directive can only be used once, so we have a choice
     * on how to handle duplicates, depending on what we are doing (building,
     * parsing, validating).
     */

    public function addDirective(Directive $directive, $duplicate_handler = self::DUP_ERROR)
    {
        // Get the name for indexing the directive list.
        $normalised_name = $directive->getNormalisedName();

        if ( ! isset($this->directives[$normalised_name]) || $duplicate_handler == self::DUP_REPLACE) {
            // Not a duplicate, or we are replacing, just add it to the list.
            $this->directives[$normalised_name] = $directive;
        } else {
            // We have been given a duplicate, so we need to handle that.
            if ($duplicate_handler == self::DUP_ERROR) {
                // TODO: raise exception.
            } elseif ($duplicate_handler == self::DUP_APPEND) {
                // TODO: do we need to check and skip duplicate sources when
                // appending? Or will the Directive we are appending to handle
                // that for us?
            } elseif ($duplicate_handler == self::DUP_DISCARD) {
                // Discarding, so just do nothing.
            } else {
                // TODO: unknown handler flag; raise an exception.
            }
        }

        return $this;
    }
}

