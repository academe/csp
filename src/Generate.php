<?php

namespace Academe\Csp;

/**
 * Convenient methods to generate headers and metatags.
 * Your framework will probably have better ways to handle this.
 */

class Generate
{
    // HTTP headers.
    // Prior to FF v23, X-Content-Security-Policy, X-Content-Security-Policy-Report-Only,
    // X-WebKit-CSP and X-WebKit-CSP-Report-Only was supported.
    // This is deprecated but not yet removed from FF. However, even IE10 only
    // supports the X-* variants and not the full 1.0 or 1.1 standard.

    const HEADER_ENFORCE = 'Content-Security-Policy';
    const HEADER_REPORT = 'Content-Security-Policy-Report-Only';
}


