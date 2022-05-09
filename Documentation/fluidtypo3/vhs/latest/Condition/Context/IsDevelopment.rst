.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-condition-context-isdevelopment:

===============================
condition.context.isDevelopment
===============================


Context: IsDevelopment
======================

Returns true if current root application context is development otherwise false.
If no application context has been set, then the default context is production.

Note about how to set the application context
---------------------------------------------

The context TYPO3 CMS runs in is specified through the environment variable TYPO3_CONTEXT.
It can be set by .htaccess or in the server configuration

See: http://docs.typo3.org/typo3cms/CoreApiReference/ApiOverview/Bootstrapping/Index.html#bootstrapping-context

Arguments
=========


.. _condition.context.isdevelopment_then:

then
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if met.

.. _condition.context.isdevelopment_else:

else
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if not met.
