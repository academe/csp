<?php

namespace Academe\Csp;

/**
 * Provide CSP string parsing and construction functionality.
 */

class Parse
{
    // A list of directive names.
    // TODO: find somewhere more central for metadata.

    protected $directives = array(
        'base-uri',
        'child-src',
        'frame-src',
        'connect-src',
        'default-src',
        'font-src',
        'form-action',
        'frame-ancestors',
        'frame-src',
        'img-src',
        'media-src',
        'object-src',
        'plugin-types',
        'referrer',
        'reflected-xss',
        'report-uri',
        'sandbox',
        'script-src',
        'style-src',
        'options',
        'nonce-value',
    );

    /**
     * Parse a security policy into a list of directives.
     */

    public function parseDirectives($directives)
    {
        // TODO: confirm the directives is a string.
        // Maybe allow an array and tread them as already split?

        $lc_names = array();

        // The directives are separated by semi-colons.
        // Semi-colons are not allowed anywhere else in the directive list without
        // being percentage escaped.

        $directive_list = explode(';', $directives);

        // The array of parsed directives we will return.
        $parsed = array();

        // Parse each individual directive.

        foreach($directive_list as $directive) {
            // Parse this single directive.

            $parsed_directive = $this->splitDirective(ltrim($directive, " \t"));

            // If it was not parsable, then skip it.

            if ( ! isset($parsed_directive)) {
                continue;
            }

            // Get the directive-name and directive-value.
            // We are really only part way through - the value needs parsing into policies.

            $name = $parsed_directive['name'];
            $value = $parsed_directive['value'];

            // If we have already encountered this directive, then skip it.
            // Only the first instance should be recognised.
            // The match is case-insensitive.

            if (in_array(strtolower($name), $lc_names)) {
                continue;
            }

            $lc_names[] = strtolower($name);

            $parsed[$name] = $this->splitSourceList($value);
        }

        return $parsed;
    }

    /**
     * Split a single directive into a name and value.
     * Returns an array (maybe an object later?) or null if not parsable.
     */

    public function splitDirective($directive)
    {
        // The token up to the first WSP is the name of the directive.
        // After the first space is the list of policies.

        $directive_split = preg_split('/[ \t]+/', $directive, 2);

        // If we don't have two parts, then the directive has no value.

        if (count($directive_split) == 2) {
            list($directive_name, $directive_value) = $directive_split;
        } else {
            list($directive_name) = $directive_split;
            $directive_value = '';
        }

        // If the directive has no name, then skip it.
        // A trailing semi-colon is allowed after the last directive, and that will
        // look like an empty directive when split.

        if ($directive_name == '') {
            return null;
        }

        // We have not checked if the directive name or value is valid at this stage.
        // The name can contain only letters, digits and a dash.

        return array(
            'name' => $directive_name,
            'value' => $directive_value,
        );
    }

    /**
     * Split a directive value source list into an array of source expressions.
     */

    public function splitSourceList($directive_value)
    {
        // Trim both leading and trailing space.
        $directive_value = trim($directive_value, " \t");

        // If empty, then nothing more to do.
        if ($directive_value == '') return array();

        // Split on spaces. Multiple spaces are permitted.
        $split = preg_split('/[ \t]+/', $directive_value);

        $source_expressions = array();

        foreach($split as $source_expression) {
            // TODO: skip (or mark as invalid) if the source expression does not
            // meet the valid grammar.

            // TODO: (maybe in a seprate method) parse the expression and perform URL
            // i18n decoding if necessary. We will decode as much as possible when
            // parsing, to make the data as humanly readble as we can. When reconstructing
            // the policy string, any relevant encoding will be reapplied.

            // Decode percentage encodings here. The RFC states that only ; and ,
            // will be encoded, and only into %3B and %2C respectively. We will take
            // it at face value and just decode those two characters.

            $source_expression = str_replace(
                array('%3B', '%2C'),
                array(';', ','),
                $source_expression
            );

            $source_expressions[] = $source_expression;
        }

        return $source_expressions;
    }
}

