<?php

namespace Academe\Csp;

/**
 * The directive model. A single directive.
 * Purpose:
 * - Holds main directive details
 * - Holds list of source expressions
 * TODO: make this an abstract, so it can be extended.
 * TODO: implement a status so it can be marked as invalid if appropriate.
 * Is is http://www.php.net/manual/en/class.arrayobject.php we want, rather than \Interator?
 * TODO: The RFC lists some source expressions that don't seem to fit into the standard list
 * of types, and appear to be keywords without quotes, e.g. "allow" is a value for "reflected-xss".
 * A think about how validation works there would be useful. Reading between the lines, source-list
 * is just ONE of several sets of parameters that a directive could take. Some directives do not
 * even take a source-list.
 */

class Directive implements \Iterator
{
    /**
     * A list of directive names.
     */

    // 1.0 and 1.1
    // This group all take a source-list, i.e. a list of sources.
    const DIR_DEFAULT_SRC = 'default-src';
    const DIR_SCRIPT_SRC = 'script-src';
    const DIR_OBJECT_SRC = 'object-src';
    const DIR_IMG_SRC = 'img-src';
    const DIR_MEDIA_SRC = 'media-src';
    const DIR_FRAME_SRC = 'frame-src';
    const DIR_FONT_SRC = 'font-src';
    const DIR_CONNECT_SRC = 'connect-src';
    const DIR_STYLE_SRC = 'style-src';

    // sandbox takes space-separated tokens from RFC 2616, same as the sandbox attribute for iframes
    // i.e. allow-forms, allow-pointer-lock, allow-popups, allow-same-origin, allow-scripts, and allow-top-navigation.
    const DIR_SANDBOX_SRC = 'sandbox'; 
    // URI
    const DIR_REPORT_URI = 'report-uri';

    // 1.1
    // These four take a source-list
    const DIR_BASE_URI = 'base-uri';
    const DIR_CHILD_SRC = 'child-src';
    const DIR_FORM_ACTION = 'form-action';
    const DIR_FRAME_ANCESTORS = 'frame-ancestors';

    // These four take the specified values.

    // media-type-list, type/sub-type from RFC 2045 (experimental)
    const DIR_PLUGIN_TYPES = 'plugin-types';

    // "never" / "default" / "origin" / "always"
    const DIR_REFERRER = 'referrer';

    // "allow" / "block" / "filter"
    const DIR_REFLECTED_XSS = 'reflected-xss';

    // Values not documented in the RFC, but they look like a CSV list of tokens.
    const DIR_OPTIONS = 'options';

    /**
     * The whole expression list when set as the empty set.
     * TODO: use Source\None::EMPTY_SET_EXPRESSION to avoid duplication.
     */

    const EMPTY_SET_EXPRESSION = "'none'";

    /**
     * True if this directive represents the empty set, i.e. match nothing.
     */

    protected $is_empty_set = false;

    /**
     * The directive name.
     * The name can take any mix of letter case as the name is case insensitive in use.
     */

    protected $name;

    /**
     * The list of sources.
     */

    protected $source_list = array();

    /**
     * Iterator methods for looping over the directives.
     * Can this go into an iterator abstract, as both Policyt and Directive need it.
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
     * Directive name must be set on creation.
     */

    public function __construct($directive_name)
    {
        $this->setName($directive_name);
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
     * TODO: Some directive types may use different separators when imploding (e.g. options). Some only
     * support one value (so we just render the first or the last, or error if there is more than one).
     * The values array property should probably not even be called "source_list" now.
     */

    public function render()
    {
        // Join the sources together with a space and prefix the directive name.
        return trim($this->getName() . ' ' . implode(' ', $this->source_list));
    }

    /**
     * Return a normalised directive name, so unique names can be compared.
     */

    public function getNormalisedName()
    {
        return static::normalise($this->name);
    }

    /**
     * Normalise a directive name string.
     */

    public static function normalise($name)
    {
        return strtolower($name);
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
     */

    public function setName($directive_name)
    {
        if ( ! $this->isValidDirective($directive_name)) {
            // TODO: create some custom exceptions
            throw new \InvalidArgumentException('Invalid directive name ' . $directive_name);
        }

        $this->name = $directive_name;

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
     * If 'none' is supplied, then that overrides the entire source list.
     * Similarly, if 'none' already is the source list, then no other sources
     * can be added.
     * TODO: a source is just one type of value that can be added. Some directives
     * accept only one value, some accept non-source values (e.g. media types).
     */

    public function addSource(Source\SourceInterface $source)
    {
        // If the empty list expression is supplied, then the whole source list becomes 'empty'.

        if ($source->getSourceType() == 'none') {
            return $this->setEmpty(true);
        }

        // Add the source expression to the list if this is not the empty set,
        // and the source expression has not already been added.

        // Key the sources on a hash of the rendered source.
        // This is just to weed out duplicates.
        $hash = $this->sourceExpressionHash($source);

        if ( ! isset($this->source_list[$hash])) {
            // If the Directive is the empty set now, then reset that state.
            if ($this->is_empty_set) {
                $this->setEmpty(false);
            }

            $this->source_list[$hash] = $source;
        }

        return $this;
    }

    /**
     * Provide a hash for the source expression.
     */

    public function sourceExpressionHash($source_expression)
    {
        return md5($source_expression);
    }

    /**
     * Add a source expression list (an array).
     */

    public function addSourceList($source_list)
    {
        foreach($source_list as $source) {
            $this->addSource($source);
        }

        return $this;
    }

    /**
     * Get all the source expressions.
     */

    public function getSourceList($source_list)
    {
        return $this->source_list;
    }

    /**
     * Set this directive to the "empty set".
     * This resets all source expressions to a single 'none' and prevents
     * further expressions from being added.
     * This should only apply to directives that accept a source list.
     */

    public function setEmpty($state = true)
    {
        if ( ! $this->is_empty_set && $state) {
            // Set the empty set state.
            // TODO: we need to set the empty set keyword source object, and not a simple string.

            $this->source_list = array(static::EMPTY_SET_EXPRESSION);
            $this->is_empty_set = true;
        } elseif ($this->is_empty_set && ! $state) {
            // Reset (remove) the empty set state.

            $this->source_list = array();
            $this->is_empty_set = false;
        }

        return $this;
    }

    /**
     * Remove the empty set flag and 'none' source expression, if set.
     * TODO: is there a more elegant way than this?
     */

    public function setNotEmpty()
    {
        return $this->setEmpty(false);
    }

    /**
     * Return an array of lower-case valid directives, keyed on the constant name.
     */

    public static function validDirectives()
    {
        // Get the constants.
        $reflect = new \ReflectionClass(get_called_class());
        $constants = $reflect->getConstants();

        // Filter out constants that don't start with KEYWORD_
        foreach($constants as $name => $value) {
            if (substr($name, 0, 4) != 'DIR_') {
                unset($constants[$name]);
            }
        }

        return $constants;
    }

    /**
     * Check if a string value is a valid directive name.
     * If check_keys is true, then also allow the name to be a
     * valid directive key, e.g. 'DIR_IMG_SRC' as well as 'img-src'.
     */

    public static function isValidDirective($name, $allow_keys = false)
    {
        $valid_names = static::validDirectives();

        if ($allow_keys && isset($valid_names[$name])) {
            return true;
        }

        return in_array(static::normalise($name), $valid_names);
    }
}

