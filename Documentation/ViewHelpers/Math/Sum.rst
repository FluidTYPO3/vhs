:navigation-title: math.sum
.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-math-sum:

====================================
math.sum ViewHelper `<vhs:math.sum>`
====================================


Math: Sum

Performs sum of $a and $b. A can be an array and $b a
number, in which case each member of $a gets summed with $b.
If $a is an array and $b is not provided then array_sum is
used to return a single numeric value. If both $a and $b are
arrays, each member of $a is summed against the corresponding
member in $b compared using index.


.. _fluidtypo3-vhs-math-sum_arguments:

Arguments
=========


.. _math.sum_a:

a
-

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   First number for calculation

.. _math.sum_b:

b
-

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Optional: Second number or Iterator/Traversable/Array for calculation

.. _math.sum_fail:

fail
----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, throws an Exception if argument "a" is not specified and no child content or inline argument is found. Usually okay to use a NULL value (as integer zero).
