:navigation-title: condition.string.contains
.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-condition-string-contains:

======================================================================
condition.string.contains ViewHelper `<vhs:condition.string.contains>`
======================================================================


Condition: String contains substring
====================================

Condition ViewHelper which renders the `then` child if provided
string $haystack contains provided string $needle.


.. _fluidtypo3-vhs-condition-string-contains_arguments:

Arguments
=========


.. _condition.string.contains_then:

then
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if met.

.. _condition.string.contains_else:

else
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if not met.

.. _condition.string.contains_haystack:

haystack
--------

:aspect:`DataType`
   string

:aspect:`Required`
   true
:aspect:`Description`
   Haystack

.. _condition.string.contains_needle:

needle
------

:aspect:`DataType`
   string

:aspect:`Required`
   true
:aspect:`Description`
   Need
