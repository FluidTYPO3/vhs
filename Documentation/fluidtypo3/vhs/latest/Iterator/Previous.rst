.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-iterator-previous:

=================
iterator.previous
=================


Returns previous element in array $haystack from position of $needle.

Arguments
=========


.. _iterator.previous_then:

then
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if met.

.. _iterator.previous_else:

else
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if not met.

.. _iterator.previous_needle:

needle
------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Needle to search for in haystack

.. _iterator.previous_haystack:

haystack
--------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Haystack in which to look for needle

.. _iterator.previous_considerkeys:

considerKeys
------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Tell whether to consider keys in the search assuming haystack is an array.
