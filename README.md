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
have not got it to work yet on WP3.8.

I have not found any other generic CSP libraries around for PHP, so am hoping
to fill the gap. However, if you know of any, then please let me know,
because there is no point in duplicating effort.

The library is very much work-in-progress at the moment, but examples will be
added to the documentation as parts of it become functionally useful.

The CSP Builder service looks like an interesting way to write the policy rules.
It starts by setting very strict policies to make any references to
any external resources reportable. You can then run your site, and check what
has been reported as violating that policy. Then you can extend the policy to
allow those resources that the CSP Builder finds. The WordPress plugin tries to
do this using JavaScript to scan the local site using HTTP, but it does not
parse the HTML it receives very well. This approach leaves the parsing entirely
up to the browser, which is after all, what the browser is designed to do.

External Links
--------------

WordPress plugin written by Mozilla:  
http://wordpress.org/plugins/content-security-policy/

CSP Builder:  
http://cspbuilder.info/

CSP version 1.1:  
http://w3c.github.io/webappsec/specs/content-security-policy/csp-specification.dev.html
