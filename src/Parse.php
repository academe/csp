<?php

namespace Academe\Csp;

/**
 * Provide CSP string parsing and construction functionality.
 * TODO: Parse should perhaps catch all exceptions raised during the parsing and set
 * flags to indicate where they occured. Since the policy to parse comes from a third
 * party, we want to make a good attempt at parsing as much of it as we can.
 */

class Parse
{
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
     * Decoding is done here of any encoding used just for presentation.
     */

    public function parseSourceExpression($source_expression)
    {
        // Some simple ones first.

        // Match the emoty set.
        if ($source_expression == Value\SourceNone::EMPTY_SET_EXPRESSION) {
            return new Value\SourceNone();
        }

        // Match a keyword.
        if (Value\SourceKeyword::isValidKeyword($source_expression)) {
            return new Value\SourceKeyword($source_expression);
        }

        // Match a scheme.
        if (Value\SourceScheme::isValidScheme($source_expression)) {
            return new Value\SourceScheme($source_expression);
        }

        // Match a hash.
        $valid_algos = Value\SourceHash::validAlgos();

        // TODO: check this RE (matches base64 string).
        if (preg_match('/^\'(' . implode('|', $valid_algos) . ')-[a-z0-9+\/_=-]*\'$/i', $source_expression)) {
            list($algo, $hash_base64_value) = explode('-', trim($source_expression, "'"), 2);
            $hash = new Value\SourceHash($algo);

            // Pass in the 
            return $hash->setValueBase64($hash_base64_value);
        }

        // Match a nonce.
        // TODO: check this RE (matches base64 string).
        if (preg_match('/^\'nonce-[a-z0-9+\/_=-]*\'$/i', $source_expression)) {
            list(, $nonce_base64_value) = explode('-', trim($source_expression, "'"), 2);
            return new Value\SourceNonce($nonce_base64_value);
        }

        // Assume whatever is left, will be a host.
        // CHECKME: the 'options' directive may throw a spanner in that.

        // We can do some rudamentary validation, but the host expression can take many forms.
        // We are parsing policy strings, so assume this expression is encoded and so needs decoding.
        return new Value\SourceHost(
            Value\SourceHost::decode($source_expression)
        );
    }
}

