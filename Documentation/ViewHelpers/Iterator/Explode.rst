.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-iterator-explode:

================
iterator.explode
================


Explode ViewHelper

Explodes a string by $glue.

Arguments
=========


.. _iterator.explode_content:

content
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   String to be exploded by glue

.. _iterator.explode_glue:

glue
----

:aspect:`DataType`
   string

:aspect:`Default`
   ','

:aspect:`Required`
   false
:aspect:`Description`
   String "glue" that separates values. If you need a constant (like PHP_EOL), use v:const to read it.

.. _iterator.explode_limit:

limit
-----

:aspect:`DataType`
   mixed

:aspect:`Default`
   9223372036854775807

:aspect:`Required`
   false
:aspect:`Description`
   If limit is set and positive, the returned array will contain a maximum of limit elements with the last element containing the rest of string. If the limit parameter is negative, all components except the last-limit are returned. If the limit parameter is zero, then this is treated as 1.

.. _iterator.explode_as:

as
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Template variable name to assign; if not specified the ViewHelper returns the variable instead.
