<?php

namespace Academe\Csp\Metadata;

/**
 * Metadata for Directives.
 */

class Directive
{
    /**
     * A list of directive names.
     */

    // CSP 1.0
    protected $directives_v1_0 = array(
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
    );

    // CSP 1.1
    protected $directives_v1_1_only = array(
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

    // Type types of source expressions.
    // TODO: move this somewhere else.
    // The source expressions are constructed from component parts, which
    // differ for each scheme, so it is probably worth encapsulating the
    // construction of sources into separate classes.

    protected $sources = array(
        'scheme',
        'host',
        'keyword',
        'nonce',
        'hash',
    );

    // Source list keywords (probably belongs somewhere else).

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
    // (Also belongs somewhere else.)

    protected $headers = array(
        'Content-Security-Policy',
        'Content-Security-Policy-Report-Only',
    );

    /**
     * Return directive names.
     */

    public function getNames($version = '1.1')
    {
        if ($version == '1.0') {
            return $this->directives_v1_0;
        } else {
            return $this->directives_v1_0 + $this->directives_v1_1_only;
        }
    }
}
