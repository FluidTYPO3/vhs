.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-condition-page-islanguage:

=========================
condition.page.isLanguage
=========================


Condition: Is current language
==============================

A condition ViewHelper which renders the `then` child if
current language matches the provided language uid or language
title. When using language titles like 'de' it is required to
provide a default title to distinguish between the standard
and a non existing language.

Arguments
=========


.. _condition.page.islanguage_then:

then
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if met.

.. _condition.page.islanguage_else:

else
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if not met.

.. _condition.page.islanguage_language:

language
--------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Language to check

.. _condition.page.islanguage_defaulttitle:

defaultTitle
------------

:aspect:`DataType`
   string

:aspect:`Default`
   'en'

:aspect:`Required`
   false
:aspect:`Description`
   Title of the default language
