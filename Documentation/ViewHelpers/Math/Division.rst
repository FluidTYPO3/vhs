.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-math-division:

=============
math.division
=============


Math: Division

Performs division of $a using $b. A can be an array and $b a
number, in which case each member of $a gets divided by $b.
If both $a and $b are arrays, each member of $a is summed
against the corresponding member in $b compared using index.

Arguments
=========


.. _math.division_a:

a
-

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   First number for calculation

.. _math.division_b:

b
-

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Second number or Iterator/Traversable/Array for calculation

.. _math.division_fail:

fail
----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, throws an Exception if argument "a" is not specified and no child content or inline argument is found. Usually okay to use a NULL value (as integer zero).
