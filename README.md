Content Security Policy
=======================

The aim of this library is to provide some back-end tools for implementing
a Content Security Policy on an application. It is written in PHP.

Autoloading
-----------

This library uses PSR-4 autoloading. The base namespace as \Academe\Csp

Development Plan
----------------

The library will start with a parser, to be able to translate a CSP policy into
component parts, and then back into a policy string. This will provide some
deeper understanding of how CSP works and provide support the splitting of a policy
into parts that can be handled in an administration screen, or manipulated on
a page-by-page basis.


