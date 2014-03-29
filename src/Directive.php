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
 * TODO: implement a status so it can be marked as invalid if appropriate.
 * Is is http://www.php.net/manual/en/class.arrayobject.php we want, rather than \Interator?
 */

class Directive implements \Iterator
{
    /**
     * A list of directive names.
     */

    // 1.0 and 1.1
    const DIR_DEFAULT_SRC = 'default-src';
    const DIR_SCRIPT_SRC = 'script-src';
    const DIR_OBJECT_SRC = 'object-src';
    const DIR_IMG_SRC = 'img-src';
    const DIR_MEDIA_SRC = 'media-src';
    const DIR_FRAME_SRC = 'frame-src';
    const DIR_FONT_SRC = 'font-src';
    const DIR_CONNECT_SRC = 'connect-src';
    const DIR_STYLE_SRC = 'style-src';

    const DIR_SANDBOX_SRC = 'sandbox';
    const DIR_REPORT_URI = 'report-uri';

    // 1.1
    const DIR_BASE_URI = 'base-uri';
    const DIR_CHILD_SRC = 'child-src';
    const DIR_FORM_ACTION = 'form-action';
    const DIR_FRAME_ANCESTORS = 'frame-ancestors';
    const DIR_PLUGIN_TYPES = 'plugin-types';
    const DIR_REFERRER = 'referrer';
    const DIR_REFLECTED_XSS = 'reflected-xss';
    const DIR_OPTIONS = 'options';
    const DIR_NONCE_VALUE = 'nonce-value';

    /**
     * The whole expression list when set as the empty set.
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
     * Directive name can be set on creation.
     */

    public function __construct($name = null)
    {
        if (isset($name)) {
            $this->setName($name);
        }
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
     */

    public function render()
    {
        // Percent encode each source in the list.
        $encoded = array_map(array(__NAMESPACE__ . '\Helper\Encode', 'encodeSourceExpression'), $this->source_list);

        // Join the sources together with a space and prefix the directive name.
        return trim($this->getName() . ' ' . implode(' ', $encoded));
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
     * Duplicates are skipped.
     * These are just strings for now, and I'm not sure the benefit of
     * takening them further as objects.
     * TODO: If 'none' is supplied, then that overrides the entire source list.
     * Similarly, if 'none' already is the source list, then no other sources
     * can be added.
     */

    public function addSourceExpression($source)
    {
        // If the empty list expression is supplied, then it overrides everything.
        if ($source == static::EMPTY_SET_EXPRESSION) {
            $this->setEmpty();
        }

        // Add the source expression to the list if this is not the empty set,
        // and the source expression has not already been added.
        // TODO: not convinced the empty set should lock out other sources.
        // Passing in other sources should override this instead, and release
        // the empty set state.

        if ( ! $this->is_empty_set && ! in_array($source, $this->source_list)) {
            $this->source_list[] = $source;
        }

        return $this;
    }

    /**
     * Add a source expression list (an array).
     */

    public function addSourceExpressionList($source_list)
    {
        foreach($source_list as $source) {
            $this->addSourceExpression($source);
        }

        return $this;
    }

    /**
     * Get all the source expressions.
     */

    public function getSourceExpressionList($source_list)
    {
        return $this->source_list;
    }

    /**
     * Set this directive to the "empty set".
     * This resets all source expressions to a single 'none' and prevents
     * further expressions from being added.
     */

    public function setEmpty($state = true)
    {
        if ( ! $this->is_empty_set && $state) {
            // Set the empty set state.

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

}

