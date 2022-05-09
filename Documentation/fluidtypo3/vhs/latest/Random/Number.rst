.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-random-number:

=============
random.number
=============


Random: Number Generator
========================

Generates a random number. The default minimum number is
set to 100000 in order to generate a longer integer string
representation. Decimal values can be generated as well.

Arguments
=========


.. _random.number_minimum:

minimum
-------

:aspect:`DataType`
   integer

:aspect:`Default`
   100000

:aspect:`Required`
   false
:aspect:`Description`
   Minimum number - defaults to 100000 (default max is 999999 for equal string lengths)

.. _random.number_maximum:

maximum
-------

:aspect:`DataType`
   integer

:aspect:`Default`
   999999

:aspect:`Required`
   false
:aspect:`Description`
   Maximum number - defaults to 999999 (default min is 100000 for equal string lengths)

.. _random.number_minimumdecimals:

minimumDecimals
---------------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Minimum number of also randomized decimal digits to add to number

.. _random.number_maximumdecimals:

maximumDecimals
---------------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Maximum number of also randomized decimal digits to add to number
