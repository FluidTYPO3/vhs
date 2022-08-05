.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-math-range:

==========
math.range
==========


Math: Range

Gets the lowest and highest number from an array of numbers.
Returns an array of [low, high]. For individual low/high
values please use v:math.maximum and v:math.minimum.

Arguments
=========


.. _math.range_a:

a
-

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   First number for calculation

.. _math.range_fail:

fail
----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, throws an Exception if argument "a" is not specified and no child content or inline argument is found. Usually okay to use a NULL value (as integer zero).
