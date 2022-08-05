.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-iterator-reverse:

================
iterator.reverse
================


Iterator Reversal ViewHelper
============================

Reverses the order of every member of an Iterator/Array,
preserving the original keys.

Arguments
=========


.. _iterator.reverse_subject:

subject
-------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The input array/Traversable to reverse

.. _iterator.reverse_as:

as
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Template variable name to assign; if not specified the ViewHelper returns the variable instead.
