.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-math-squareroot:

===============
math.squareRoot
===============


Math: SquareRoot

Performs sqrt($a).

Arguments
=========


.. _math.squareroot_a:

a
-

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   First number for calculation

.. _math.squareroot_fail:

fail
----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, throws an Exception if argument "a" is not specified and no child content or inline argument is found. Usually okay to use a NULL value (as integer zero).
