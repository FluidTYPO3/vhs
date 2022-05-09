.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-context-get:

===========
context.get
===========


Context: Get
============

Returns the current application context which may include possible sub-contexts.
The application context can be 'Production', 'Development' or 'Testing'.
Additionally each context can be extended with custom sub-contexts like: 'Production/Staging' or
'Production/Staging/Server1'. If no application context has been set by the configuration, then the
default context is 'Production'.

Note about how to set the application context
---------------------------------------------

The context TYPO3 CMS runs in is specified through the environment variable TYPO3_CONTEXT.
It can be set by .htaccess or in the server configuration

See: http://docs.typo3.org/typo3cms/CoreApiReference/ApiOverview/Bootstrapping/Index.html#bootstrapping-context

Arguments
=========


This ViewHelper has no arguments.
