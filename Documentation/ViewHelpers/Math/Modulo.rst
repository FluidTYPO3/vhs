.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-math-modulo:

===========
math.modulo
===========


Math: Modulo
Perform modulo on $input. Returns the same type as $input,
i.e. if given an array, will transform each member and return
the result. Supports array and Iterator (in the following
descriptions "array" means both these types):

If $a and $b are both arrays of the same size then modulo is
performed on $a using members of $b, by their index (so these
must match in both arrays).

If $a is an array and $b is a number then modulo is performed
on $a using $b for each calculation.

If $a and $b are both numbers simple modulo is performed.

Arguments
=========


.. _math.modulo_a:

a
-

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   First number for calculation

.. _math.modulo_b:

b
-

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Second number or Iterator/Traversable/Array for calculation

.. _math.modulo_fail:

fail
----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, throws an Exception if argument "a" is not specified and no child content or inline argument is found. Usually okay to use a NULL value (as integer zero).
