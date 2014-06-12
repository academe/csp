<?php

namespace Academe\Csp;

/**
 * Provide CSP string parsing and construction functionality.
 * This parses the raw HTTP x header value.
 *
 * TODO: Parse should perhaps catch all exceptions raised during the parsing and set
 * flags to indicate where they occured. Since the policy to parse comes from a third
 * party, we want to make a good attempt at parsing as much of it as we can.
 *
 * TODO: when HTTP headers are parsed, if the same header appears more than once, then
 * each header value may be concatenated as a single string, separated by commas. We
 * need to recognise this here when parsing the header string. Commas in any diective
 * value will be percent-encoded, so we can safely explode the policy string into 
 * multiple directive lists that are then combined into a single directive list.
 * Just substituting commas with semi-colons may be all we need to do.
 */

class Parse
{
    // Allowed white space characters.
    const WSP = " \t";

    // Character that separates directives in a directive list.
    const DIR_SEP = ';';

    // The separator when multiple headers of the same name have their values combined into
    // one string. The PSR-7 HTTP library would do this before we get to see the header value.
    const HEADER_VALUE_SEP = ',';

    /**
     * Parse a security policy string into a Policy object.
     */

    public function parsePolicy($policy_string)
    {
        // TODO: assert the policy is a string.
        // The directives are separated by semi-colons.

        // Replace commas with semi-colons.
        // Un-encoded commas singnify the combining of the values of multiple CSP headers.
        // We just treat it as one header by replacing the header value separator with the
        // directive separator.

        $policy_string = str_replace(static::HEADER_VALUE_SEP, static::DIR_SEP, $policy_string);

        // Semi-colons are not allowed anywhere else in the directive list without
        // being percentage escaped. This will only happen in SourceHost as there
        // are no semi-colons in any of the other directives.

        $directive_list = explode(static::DIR_SEP, $policy_string);

        // The Policy object we will return.
        // TODO: use some kind of factory.

        $policy = new Policy();

        // Parse each individual directive.

        foreach($directive_list as $directive_string) {
            // Parse this single directive.
            // Trim any white space and empty directives.

            $directive = $this->parseDirective(ltrim($directive_string, static::WSP . static::DIR_SEP));

            // If it was not parsable, then skip it.
            // We want to catch as many directives as we can. We probably don't
            // want to skip any completely, but perhaps add a "raw" directive to
            // the policy and mark it as unparsable.

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
        // TODO: use some kind of factory here.

        $directive = new Directive($directive_name);

        $source_list = $this->parseDirectiveValue($directive_value);

        $directive->addSourceList($source_list);

        return $directive;
    }

    /**
     * Parse a directive value string into an array of source expression strings.
     * TODO: Not all directives expect a source list. Determine what is expected
     * by the directive, then parse it appropriately.
     * The Source expression classes probably need to extend a more generic, so that
     * non-source values can be included. The array may also need to replaced with a
     * collection object, as rendering may differ between directive types, e.g. some
     * examples show options being separate by commas instead of white space.
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

            // First we need to work out what kind of source it is, then create the
            // appropriate object.
            $source_list[] = $this->parseSourceExpression($source_expression);
        }

        return $source_list;
    }

    /**
     * Convert a source expression string into a source object.
     * Decoding is done here of any encoding used, to get to the underlying data.
     */

    public function parseSourceExpression($source_expression)
    {
        // Some simple ones first.

        // Match the empty set.
        if ($source_expression == Value\SourceNone::EMPTY_SET_EXPRESSION) {
            return new Value\SourceNone();
        }

        // If it begins with a single quote, then it must be a keyword.
        if (substr($source_expression, 0, 1) == "'") {
            // Match a keyword.
            if (Value\SourceKeyword::isValidKeyword($source_expression)) {
                return new Value\SourceKeyword($source_expression);
            } else {
                // Not a valid keyword, so return an "unknown".
                return new Value\Unknown($source_expression);
            }
        }

        // Match a scheme.
        if (Value\SourceScheme::isValidScheme($source_expression)) {
            return new Value\SourceScheme($source_expression);
        }

        // Match a hash.
        $valid_algos = Value\SourceHash::validAlgos();

        if (preg_match('/^\'(' . implode('|', $valid_algos) . ')-.*\'$/i', $source_expression)) {
            list($algo, $hash_base64_value) = explode('-', trim($source_expression, "'"), 2);

            if ($this->validBase64($hash_base64_value)) {
                $hash = new Value\SourceHash($algo);

                // Pass in the base64 encoded value.
                return $hash->setValueBase64($hash_base64_value);
            } else {
                // base64 validatino fails.
                return new Value\Unknown($source_expression);
            }
        }

        // Match a nonce.
        if (preg_match('/^\'nonce-.*\'$/i', $source_expression)) {
            list(, $nonce_base64_value) = explode('-', trim($source_expression, "'"), 2);

            if ($this->validBase64($nonce_base64_value)) {
                // Is a valid base64 nonce
                return new Value\SourceNonce($nonce_base64_value);
            } else {
                // base64 validatino fails.
                return new Value\Unknown($source_expression);
            }
        }

        // Assume whatever is left, will be a host.
        // CHECKME: the 'options' directive may throw a spanner in that.

        // We can do some rudamentary validation, but the host expression can take many forms.
        // We are parsing policy strings, so assume this expression is encoded for special
        // characters (, and ;) and so needs decoding.
        return new Value\SourceHost(
            Value\SourceHost::decode($source_expression)
        );
    }

    /**
     * Test whether a value is a valid base64 string.
     * TODO: move this. Also useful in Value\SourceHash and Value\SourceNonce
     */

    public function validBase64($value)
    {
        return preg_match('/^[a-z0-9+\/_=-]*$/i', $value);
    }
}

