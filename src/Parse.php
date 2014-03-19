<?php

namespace Academe\Csp;

/**
 * Provide CSP string parsing and construction functionality.
 */

class Parse
{
    /**
     * Parse a list of directives.
     * This is a list of multiple policies as a string.
     */

    public function parseDirectives($directives)
    {
        // TODO: confirm the directives is a string.
        // Maybe allow an array and tread them as already split?

        // The directives are separated by semi-colons.
        // Semi-colons are not allowed anywhere else in the directive list without
        // being percentage escaped.

        $directive_list = explode(';', $directives);

        // The array of parsed directives we will return.
        $parsed = array();

        // Parse each individual directive.

        foreach($directive_list as $directive) {
            // Each directive, after the first one, should have exactly one space
            // prefixing it (separators are "; ").
            // A lazy trim will help clean out multiple spaces or trailing spaces.
            // A more strict approach would treat unexpected spaces as an exception.
            // Parse this single directive.

            $parsed_directive = $this->parseDirective(trim($directive));

            // If it was not parsable, then skip it.

            if ( ! isset($parsed_directive)) {
                continue;
            }

            $name = $parsed_directive['name'];
            $policies = $parsed_directive['policies'];

            // If we have already encountered this directive, then skip it.
            // Only the first instance should be recognised.

            if (isset($parsed[$name])) {
                continue;
            }

            $parsed[$name] = $policies;
        }

        return $parsed;
    }

    /**
     * Parse a single directive.
     * Returns an array (maybe an object later?) or null if not parsable.
     */

    public function parseDirective($directive)
    {
        // The token up to the first space is the name of the directive.
        // After the first space is the list of policies.

        $directive_split = explode(' ', $directive, 2);

        // If we don't have two parts, then it is not a valid format.

        if (count($directive_split) != 2) {
            return null;
        }

        list($name, $policie_list) = $directive_split;

        // We will trim the policy list string, to keep it clean.
        // Strictly it may have a single leading space, and treating
        // additional spaces as an exception may be something that is
        // desireable, but for now we just want to parse as much as we
        // can.

        $policie_list = trim($policie_list);

        // The policy list contains policies separated by a single space.

        // TODO

        // The array is clunky. It should really be an object, then we can put
        // validity flags and error messages etc. into as necessary.

        return array(
            'name' => $name,
            'policies' => $policie_list,
        );
    }
}
