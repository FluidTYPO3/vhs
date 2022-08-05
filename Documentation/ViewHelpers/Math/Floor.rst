.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-math-floor:

==========
math.floor
==========


Math: Floor

Floors $a which can be either an array-accessible
value (Iterator+ArrayAccess || array) or a raw numeric
value.

Arguments
=========


.. _math.floor_a:

a
-

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   First number for calculation

.. _math.floor_fail:

fail
----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, throws an Exception if argument "a" is not specified and no child content or inline argument is found. Usually okay to use a NULL value (as integer zero).
