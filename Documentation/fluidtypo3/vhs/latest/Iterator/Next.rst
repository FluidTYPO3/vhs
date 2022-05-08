.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-iterator-next:

=============
iterator.next
=============


Returns next element in array $haystack from position of $needle.

Arguments
=========


.. _iterator.next_then:

then
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if met.

.. _iterator.next_else:

else
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if not met.

.. _iterator.next_needle:

needle
------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Needle to search for in haystack

.. _iterator.next_haystack:

haystack
--------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Haystack in which to look for needle

.. _iterator.next_considerkeys:

considerKeys
------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   Tell whether to consider keys in the search assuming haystack is an array.
