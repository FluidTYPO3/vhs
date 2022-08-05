.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-iterator-for:

============
iterator.for
============


Repeats rendering of children with a typical for loop: starting at
index $from it will loop until the index has reached $to.

Arguments
=========


.. _iterator.for_iteration:

iteration
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Variable name to insert result into, suppresses output

.. _iterator.for_to:

to
--

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Number that the index needs to reach before stopping

.. _iterator.for_from:

from
----

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Starting number for the index

.. _iterator.for_step:

step
----

:aspect:`DataType`
   integer

:aspect:`Default`
   1

:aspect:`Required`
   false
:aspect:`Description`
   Stepping number that the index is increased by after each loop
