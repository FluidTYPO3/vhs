.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-iterator-merge:

==============
iterator.merge
==============


Merges arrays/Traversables $a and $b into an array.

Arguments
=========


.. _iterator.merge_a:

a
-

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   First array/Traversable - if not set, the ViewHelper can be in a chain (inline-notation)

.. _iterator.merge_b:

b
-

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Second array or Traversable

.. _iterator.merge_usekeys:

useKeys
-------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE comparison is done while also observing and merging the keys used in each array
