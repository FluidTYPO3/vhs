.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-math-cubicroot:

==============
math.cubicRoot
==============


Math: CubicRoot

Performs pow($a, 1/3) - cubic or third root.

Arguments
=========


.. _math.cubicroot_a:

a
-

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   First number for calculation

.. _math.cubicroot_fail:

fail
----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, throws an Exception if argument "a" is not specified and no child content or inline argument is found. Usually okay to use a NULL value (as integer zero).
