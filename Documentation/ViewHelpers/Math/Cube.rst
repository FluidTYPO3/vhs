:navigation-title: math.cube
.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-math-cube:

======================================
math.cube ViewHelper `<vhs:math.cube>`
======================================


Math: Square

Performs $a ^ 3.


.. _fluidtypo3-vhs-math-cube_arguments:

Arguments
=========


.. _math.cube_a:

a
-

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   First number for calculation

.. _math.cube_fail:

fail
----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, throws an Exception if argument "a" is not specified and no child content or inline argument is found. Usually okay to use a NULL value (as integer zero).
