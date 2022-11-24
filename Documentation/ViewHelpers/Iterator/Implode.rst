.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-iterator-implode:

================
iterator.implode
================


Implode ViewHelper

Implodes an array or array-convertible object by $glue.

Arguments
=========


.. _iterator.implode_content:

content
-------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Array or array-convertible object to be imploded by glue

.. _iterator.implode_glue:

glue
----

:aspect:`DataType`
   string

:aspect:`Default`
   ','

:aspect:`Required`
   false
:aspect:`Description`
   String used as glue in the string to be exploded. To read a constant (like PHP_EOL) use v:const.

.. _iterator.implode_as:

as
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Template variable name to assign; if not specified the ViewHelper returns the variable instead.
