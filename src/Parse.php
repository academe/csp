<?php

namespace Academe\Csp;

/**
 * Provide CSP string parsing and construction functionality.
 */

class Parse
{
    // A list of directive names.
    // TODO: find somewhere more central for metadata.
    // TODO: perhaps have a think about whether v1.0 and v1.1 support should be switchable in some way.

    protected $directives = array(
        // CSP 1.0
        'default-src',
        'script-src',
        'object-src',
        'img-src',
        'media-src',
        'frame-src',
        'font-src',
        'connect-src',
        'style-src',
        'sandbox',
        'report-uri',

        // CSP 1.1
        'base-uri',
        'child-src',
        'form-action',
        'frame-ancestors',
        'plugin-types',
        'referrer',
        'reflected-xss',
        'options',
        'nonce-value',
    );

    // Policy keywords.

    protected $keywords = array(
        // Matches no sources.
        "'none'",
        // Curremnt origin.
        "'self'",
        // Inline JS and CSS.
        // Not using this is likely to break much legacy code. I cannot imagine
        // not using this keyword on WordPress, but not using it is a worthwhile
        // aim.
        "'unsafe-inline'",
        // Text-to-JS mechanisms.
        "'unsafe-eval'",
    );

    // HTTP headers.
    // Prior to FF v23, X-Content-Security-Policy and X-Content-Security-Policy-Report-Only
    // was supported. This is deprecated but not yet removed from FF. However, even IE10 only
    // supports the X-* variants and not the full 1.0 or 1.1 standard.

    protected $headers = array(
        'Content-Security-Policy',
        'Content-Security-Policy-Report-Only',
    );

    // White space characters.
    const WSP = " \t";

    /**
     * Parse a security policy string into a Policy object.
     */

    public function parsePolicy($policy_string)
    {
        // TODO: confirm the directives is a string.
        // The directives are separated by semi-colons.
        // Semi-colons are not allowed anywhere else in the directive list without
        // being percentage escaped.

        $directive_list = explode(';', $policy_string);

        // The Policy object we will return.
        // TODO: some kind of factory.

        $policy = new Policy();

        // Parse each individual directive.

        foreach($directive_list as $directive_string) {
            // Parse this single directive.

            $directive = $this->parseDirective(ltrim($directive_string, self::WSP));

            // If it was not parsable, then skip it.

            if ( ! isset($directive)) {
                continue;
            }

            // Add the directive to the policy, discarding if we already have this directive.
            $policy->addDirective($directive, Policy::DUP_DISCARD);
        }

        return $policy;
    }

    /**
     * Parse a single directive string into a Directive object.
     * Returns null if not parsable.
     */

    public function parseDirective($directive_string)
    {
        // The token up to the first WSP is the name of the directive.
        // After the first space is the list of policies.

        $directive_split = preg_split('/[' . self::WSP . ']+/', $directive_string, 2);

        // If we don't have two parts, then the directive has no value (just a name).

        if (count($directive_split) == 2) {
            list($directive_name, $directive_value) = $directive_split;
        } else {
            list($directive_name) = $directive_split;
            $directive_value = '';
        }

        // If the directive has no name, then skip it.
        // A trailing semi-colon is allowed after the last directive in a policy, and
        // that will look like an empty directive when split.

        if ($directive_name == '') {
            return null;
        }

        // We have not checked if the directive name or value is valid at this stage.
        // The name can contain only letters, digits and a dash.
        // TODO: some kind of factory here.

        $directive = new Directive($directive_name);

        $source_list = $this->parseDirectiveValue($directive_value);

        $directive->addSourceExpressionList($source_list);

        return $directive;
    }

    /**
     * Parse a directive value string into an array of source expression strings.
     */

    public function parseDirectiveValue($directive_value)
    {
        $source_list = array();

        // Trim both leading and trailing space.
        $directive_value = trim($directive_value, self::WSP);

        // If empty, then nothing more to do.
        if ($directive_value == '') return $source_list;

        // Split on white spaces. Multiple spaces are permitted.
        $split = preg_split('/[' . self::WSP . ']+/', $directive_value);

        foreach($split as $source_expression) {
            // TODO: skip (or mark as invalid) if the source expression does not
            // meet the valid grammar.

            // TODO: (maybe in a seprate method) parse the expression and perform URL
            // i18n decoding if necessary. We will decode as much as possible when
            // parsing, to make the data as humanly readble as we can. When reconstructing
            // the policy string, any relevant encoding will be reapplied.

            // Decode percentage encodings.
            // CHECKME: does this apply to ANY source expression, or just the URLs?

            $source_list[] = $this->decodeSourceExpression($source_expression);
        }

        return $source_list;
    }

    /**
     * Decode percentage encoding from a source expression.
     * The RFC states that only ; and , will be encoded, and only into %3B and %2C respectively.
     * We will take it at face value and just decode those two characters.
     * It may make more sence to decode ANY percent-encoded character using rawurldecode() and make it
     * case-insensitive.
     */

    public function decodeSourceExpression($source_expression)
    {
        return str_replace(
            array('%3B', '%2C'),
            array(';', ','),
            $source_expression
        );
    }
}

