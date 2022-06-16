.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-math-round:

==========
math.round
==========


Math: Round

Rounds off $a which can be either an array-accessible
value (Iterator+ArrayAccess || array) or a raw numeric
value.

Arguments
=========


.. _math.round_a:

a
-

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   First number for calculation

.. _math.round_fail:

fail
----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, throws an Exception if argument "a" is not specified and no child content or inline argument is found. Usually okay to use a NULL value (as integer zero).

.. _math.round_decimals:

decimals
--------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Number of decimals
