.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-iterator-split:

==============
iterator.split
==============


Converts a string to an array with $length number of bytes
per new array element. Wrapper for PHP's `str_split`.

Arguments
=========


.. _iterator.split_subject:

subject
-------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   The string that will be split into an array

.. _iterator.split_length:

length
------

:aspect:`DataType`
   integer

:aspect:`Default`
   1

:aspect:`Required`
   false
:aspect:`Description`
   Number of bytes per chunk in the new array

.. _iterator.split_as:

as
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Template variable name to assign; if not specified the ViewHelper returns the variable instead.
