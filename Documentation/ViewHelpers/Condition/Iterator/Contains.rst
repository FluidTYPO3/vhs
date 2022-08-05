.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-condition-iterator-contains:

===========================
condition.iterator.contains
===========================


Condition ViewHelper. Renders the then-child if Iterator/array
haystack contains needle value.

Example:
========

::

    {v:condition.iterator.contains(needle: 'foo', haystack: {0: 'foo'}, then: 'yes', else: 'no')}

Arguments
=========


.. _condition.iterator.contains_then:

then
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if met.

.. _condition.iterator.contains_else:

else
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if not met.

.. _condition.iterator.contains_needle:

needle
------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Needle to search for in haystack

.. _condition.iterator.contains_haystack:

haystack
--------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Haystack in which to look for needle

.. _condition.iterator.contains_considerkeys:

considerKeys
------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Tell whether to consider keys in the search assuming haystack is an array.
