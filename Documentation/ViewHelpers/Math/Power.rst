:navigation-title: math.power
.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-math-power:

========================================
math.power ViewHelper `<vhs:math.power>`
========================================


Math: Power

Performs pow($a, $b) where $a is the base and $b is the exponent.


.. _fluidtypo3-vhs-math-power_arguments:

Arguments
=========


.. _math.power_a:

a
-

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   First number for calculation

.. _math.power_b:

b
-

:aspect:`DataType`
   mixed

:aspect:`Required`
   true
:aspect:`Description`
   Second number or Iterator/Traversable/Array for calculation

.. _math.power_fail:

fail
----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, throws an Exception if argument "a" is not specified and no child content or inline argument is found. Usually okay to use a NULL value (as integer zero).
