:navigation-title: math.ceil
.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-math-ceil:

======================================
math.ceil ViewHelper `<vhs:math.ceil>`
======================================


Math: Ceil

Ceiling on $a which can be either an array-accessible
value (Iterator+ArrayAccess || array) or a raw numeric
value.


.. _fluidtypo3-vhs-math-ceil_arguments:

Arguments
=========


.. _math.ceil_a:

a
-

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   First number for calculation

.. _math.ceil_fail:

fail
----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, throws an Exception if argument "a" is not specified and no child content or inline argument is found. Usually okay to use a NULL value (as integer zero).
