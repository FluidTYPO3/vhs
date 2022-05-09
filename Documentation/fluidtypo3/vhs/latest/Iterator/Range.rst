.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-iterator-range:

==============
iterator.range
==============


Iterator Range ViewHelper
=========================

Implementation of `range` for Fluid

Creates a new array of numbers from the low to the high given
value, incremented by the step value.

Usage examples
--------------

::

    Numbers 1-10: {v:iterator.implode(glue: ',') -> v:iterator.range(low: 1, high: 10)}
    Even numbers 0-10: {v:iterator.implode(glue: ',') -> v:iterator.range(low: 0, high: 10, step: 2)}

Arguments
=========


.. _iterator.range_low:

low
---

:aspect:`DataType`
   integer

:aspect:`Default`
   1

:aspect:`Required`
   false
:aspect:`Description`
   The low number of the range to be generated

.. _iterator.range_high:

high
----

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   The high number of the range to be generated

.. _iterator.range_step:

step
----

:aspect:`DataType`
   integer

:aspect:`Default`
   1

:aspect:`Required`
   false
:aspect:`Description`
   The step (increment amount) between each number

.. _iterator.range_as:

as
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Template variable name to assign; if not specified the ViewHelper returns the variable instead.
