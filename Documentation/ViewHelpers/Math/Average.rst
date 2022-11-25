.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-math-average:

============
math.average
============


Math: Average

Performs average across an array. If $a is an array and
$b is an array, each member of $a is averaged against the
same member in $b. If $a is an array and $b is a number,
each member of $a is averaged agained $b. If $a is an array
this array is averaged to one number. If $a is a number and
$b is not provided or NULL, $a is gracefully returned as an
average value of itself.

Arguments
=========


.. _math.average_a:

a
-

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   First number for calculation

.. _math.average_b:

b
-

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Optional: Second number or Iterator/Traversable/Array for calculation

.. _math.average_fail:

fail
----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, throws an Exception if argument "a" is not specified and no child content or inline argument is found. Usually okay to use a NULL value (as integer zero).
