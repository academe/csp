<?php

namespace Academe\Csp\Value;

/**
 * Sandbox directive value.
 */

class Sandbox extends Referrer
{
    /**
     * Allowed values.
     */

    const SANDBOX_FORMS = 'allow-forms';
    const SANDBOX_ALLOW_POINTER_LOCK = 'allow-pointer-lock';
    const SANDBOX_ALLOW_POPUPS = 'allow-popups';
    const SANDBOX_ALLOW_SAME_ORIGIN = 'allow-same-origin';
    const SANDBOX_ALLOW_SCRIPTS = 'allow-scripts';
    const SANDBOX_ALLOW_TOP_NAVIGATION = 'allow-top-navigation';

    /**
     * Prefix for allowed values constants.
     */

    const VALUE_LIST_PREFIX = 'SANDBOX_';
}
