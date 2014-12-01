Content Security Policy
=======================

The aim of this library is to provide some back-end tools for implementing
a Content Security Policy on an application. It is written in PHP 5.3+.
There is no compelling reason to use PHP5.4 features at this stage.

Autoloading
-----------

This library uses PSR-4 autoloading. The base namespace as \Academe\Csp
It is designed to be framework agnostic.

Examples
--------

A policy can be converted into objects for manipulation (or passing to an admin
form), and then back into a string for storage and use in headers or meta fields.

    $policy = "default-src 'self' https://www.google.com ;options inline-script eval-script; *.tile.openstreetmap.org *.tile.opencyclemap.org https://www.google.com; script-src 'nonce-c2Rjc2RjZHNj'"
    
    // Parse into a Policy object.
    $policy = $parse->parsePolicy($policy);
    
    // The policy object will contain an iteratable list of directives, each with an iteratable
    // list of source expressions. The source expressions are also objects.
    
    // Back into a policy string.
    $header_policy = $policy->toString();


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
http://www.w3.org/TR/CSP11/

Scriptless Attacks paper (what CSP can protect you from):  
http://www.nds.rub.de/media/emma/veroeffentlichungen/2012/08/16/scriptlessAttacks-ccs2012.pdf

Mike West - XSS. (No, the _other_ "S"); CSSconf.eu 2013:  
https://www.youtube.com/watch?v=eb3suf4REyI

Brandon Sterne's JavaScript bookmarklet:  
http://brandon.sternefamily.net/2010/10/content-security-policy-recommendation-bookmarklet/  
https://github.com/bsterne/bsterne-tools/tree/master/csp-bookmarklet

CSP 1.0 summary and practical examples:  
http://content-security-policy.com/

NCC Group CSP best practices (good examples):  
https://www.isecpartners.com/media/106598/csp_best_practices.pdf

CSP playground with working examples to try out with your browser:  
http://www.cspplayground.com/

Open Web Application Security Project (OWASP) CSP page:  
https://www.owasp.org/index.php/Content_Security_Policy

Another good overview:  
http://www.ibuildings.com/blog/2013/03/4-http-security-headers-you-should-always-be-using
