.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-math-median:

===========
math.median
===========


Math: Median

Gets the median value from an array of numbers. If there
is an odd number of numbers the middle value is returned.
If there is an even number of numbers an average of the
two middle numbers is returned.

Arguments
=========


.. _math.median_a:

a
-

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   First number for calculation

.. _math.median_fail:

fail
----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, throws an Exception if argument "a" is not specified and no child content or inline argument is found. Usually okay to use a NULL value (as integer zero).
