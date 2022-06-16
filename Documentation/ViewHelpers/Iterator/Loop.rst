.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-iterator-loop:

=============
iterator.loop
=============


Repeats rendering of children $count times while updating $iteration.

Arguments
=========


.. _iterator.loop_iteration:

iteration
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Variable name to insert result into, suppresses output

.. _iterator.loop_count:

count
-----

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Number of times to render child content

.. _iterator.loop_minimum:

minimum
-------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Minimum number of loops before stopping

.. _iterator.loop_maximum:

maximum
-------

:aspect:`DataType`
   integer

:aspect:`Default`
   9223372036854775807

:aspect:`Required`
   false
:aspect:`Description`
   Maxiumum number of loops before stopping
