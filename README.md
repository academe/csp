Content Security Policy
=======================

The aim of this library is to provide some back-end tools for implementing
a Content Security Policy on an application. It is written in PHP.

Autoloading
-----------

This library uses PSR-4 autoloading. The base namespace as \Academe\Csp
It is designed to be framework agnostic.

Development Plan
----------------

The library will start with a parser, to be able to translate a CSP policy into
component parts, and then back into a policy string. This will provide some
deeper understanding of how CSP works and provide support the splitting of a policy
into parts that can be handled in an administration screen, or manipulated on
a page-by-page basis.

It will contain reference data, useful tools and methods, and hopefully enough
functionality to be able to form the core of the CSP library in a framework or
application.

If there are features you want, then please shout out for them. My personal aim
is to use CSP in a Wordpress plugin and Laravel applications. The WP plugin
written by Mozilla has not been updated in a couple of years, and is very heavy
on the JavaScript front end to do a lot of the work in defining the policies. I
have not got it to work yet on WP3.8. The plugin is here:

http://wordpress.org/plugins/content-security-policy/

I have not found any other generic CSP libraries around for PHP, so am hoping
to fill the gap. However, if you know of any, then please let me know,
because there is no point in duplicating effort.
