<?php

namespace Academe\Csp\Metadata;

/**
 * Metadata for Directives.
 */

class Directive
{
    /**
     * A list of directive names.
     * Deprecated
     */

    // CSP 1.0
    /*
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
    */

    // CSP 1.1
    /*
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
    */

    // Types of source expressions.
    // TODO: move this somewhere else.
    // The source expressions are constructed from component parts, which
    // differ for each scheme, so it is probably worth encapsulating the
    // construction of sources into separate classes.
    // e.g. a hash is a 'quoted' string containing one of sha256, sha384 or sha512 a
    // dash (-) and then a base64 encoded string.

    protected $sources = array(
        'scheme',
        'host',
        'keyword',
        'nonce',
        'hash',
    );

    // HTTP headers.
    // Prior to FF v23, X-Content-Security-Policy and X-Content-Security-Policy-Report-Only
    // was supported. This is deprecated but not yet removed from FF. However, even IE10 only
    // supports the X-* variants and not the full 1.0 or 1.1 standard.
    // (Also belongs somewhere else.)

    /*
    protected $headers = array(
        'Content-Security-Policy',
        'Content-Security-Policy-Report-Only',
    );
    */

    /**
     * Return directive names.
     * Deprecated
     */

    /*
    public function getNames($version = '1.1')
    {
        if ($version == '1.0') {
            return $this->directives_v1_0;
        } else {
            return $this->directives_v1_0 + $this->directives_v1_1_only;
        }
    }
    */
}
