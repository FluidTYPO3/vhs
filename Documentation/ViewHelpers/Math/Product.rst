.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-math-product:

============
math.product
============


Math: Product (multiplication)
==============================

Product (multiplication) of $a and $b. A can be an array and $b a
number, in which case each member of $a gets multiplied by $b.
If $a is an array and $b is not provided then array_product is
used to return a single numeric value. If both $a and $b are
arrays, each member of $a is multiplied against the corresponding
member in $b compared using index.

Arguments
=========


.. _math.product_a:

a
-

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   First number for calculation

.. _math.product_b:

b
-

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Second number or Iterator/Traversable/Array for calculation

.. _math.product_fail:

fail
----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, throws an Exception if argument "a" is not specified and no child content or inline argument is found. Usually okay to use a NULL value (as integer zero).
