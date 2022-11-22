.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-math-maximum:

============
math.maximum
============


Math: Maximum

Gets the highest number in array $a or the highest
number of numbers $a and $b.

Arguments
=========


.. _math.maximum_a:

a
-

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   First number for calculation

.. _math.maximum_b:

b
-

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Second number or Iterator/Traversable/Array for calculation

.. _math.maximum_fail:

fail
----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, throws an Exception if argument "a" is not specified and no child content or inline argument is found. Usually okay to use a NULL value (as integer zero).
