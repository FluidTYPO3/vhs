.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-iterator-indexof:

================
iterator.indexOf
================


Searches $haystack for index of $needle, returns -1 if $needle
is not in $haystack.

Arguments
=========


.. _iterator.indexof_then:

then
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if met.

.. _iterator.indexof_else:

else
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if not met.

.. _iterator.indexof_needle:

needle
------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Needle to search for in haystack

.. _iterator.indexof_haystack:

haystack
--------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Haystack in which to look for needle

.. _iterator.indexof_considerkeys:

considerKeys
------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Tell whether to consider keys in the search assuming haystack is an array.
