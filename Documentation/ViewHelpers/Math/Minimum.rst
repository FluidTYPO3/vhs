.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-math-minimum:

============
math.minimum
============


Math: Minimum

Gets the lowest number in array $a or the lowest
number of numbers $a and $b.

Arguments
=========


.. _math.minimum_a:

a
-

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   First number for calculation

.. _math.minimum_b:

b
-

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Second number or Iterator/Traversable/Array for calculation

.. _math.minimum_fail:

fail
----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, throws an Exception if argument "a" is not specified and no child content or inline argument is found. Usually okay to use a NULL value (as integer zero).
