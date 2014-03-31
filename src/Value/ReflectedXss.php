<?php

namespace Academe\Csp\Value;

/**
 * Reflected XSS directive value.
 */

class ReflectedXss extends Referrer
{
    /**
     * Allowed values.
     */

    const XSS_ALLOW = 'allow';
    const XSS_BLOCK = 'block';
    const XSS_FILTER = 'filter';

    /**
     * Prefix for allowed values constants.
     */

    const VALUE_LIST_PREFIX = 'XSS_';
}
