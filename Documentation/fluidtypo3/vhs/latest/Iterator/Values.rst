.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-iterator-values:

===============
iterator.values
===============


Gets values from an iterator, removing current keys (if any exist).

Arguments
=========


.. _iterator.values_subject:

subject
-------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The array/Traversable instance from which to get values

.. _iterator.values_as:

as
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Template variable name to assign; if not specified the ViewHelper returns the variable instead.
