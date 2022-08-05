.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-iterator-slice:

==============
iterator.slice
==============


Slice an Iterator by $start and $length.

Arguments
=========


.. _iterator.slice_haystack:

haystack
--------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The input array/Traversable to reverse

.. _iterator.slice_start:

start
-----

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Starting offset

.. _iterator.slice_length:

length
------

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Number of items to slice

.. _iterator.slice_preservekeys:

preserveKeys
------------

:aspect:`DataType`
   boolean

:aspect:`Default`
   true

:aspect:`Required`
   false
:aspect:`Description`
   Whether or not to preserve original keys

.. _iterator.slice_as:

as
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Template variable name to assign; if not specified the ViewHelper returns the variable instead.
