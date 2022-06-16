.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-iterator-filter:

===============
iterator.filter
===============


Iterator: Filter ViewHelper
===========================

Filters an array by filtering the array, analysing each member
and asserting if it is equal to (weak type) the `filter` parameter.
If `propertyName` is set, the ViewHelper will try to extract this
property from each member of the array.

Iterators and ObjectStorage etc. are supported.

Arguments
=========


.. _iterator.filter_subject:

subject
-------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The subject iterator/array to be filtered

.. _iterator.filter_filter:

filter
------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The comparison value

.. _iterator.filter_propertyname:

propertyName
------------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Optional property name to extract and use for comparison instead of the object; use on ObjectStorage etc. Note: supports dot-path expressions

.. _iterator.filter_preservekeys:

preserveKeys
------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, keys in the array are preserved - even if they are numeric

.. _iterator.filter_invert:

invert
------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Invert the behavior of the filtering

.. _iterator.filter_nullfilter:

nullFilter
----------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE and $filter is NULL (not set) includes only NULL values. Useful with $invert.
