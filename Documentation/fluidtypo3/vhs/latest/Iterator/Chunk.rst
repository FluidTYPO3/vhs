.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-iterator-chunk:

==============
iterator.chunk
==============


Creates chunks from an input Array/Traversable with option to allocate items to a fixed number of chunks

Arguments
=========


.. _iterator.chunk_subject:

subject
-------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The subject Traversable/Array instance to shift

.. _iterator.chunk_count:

count
-----

:aspect:`DataType`
   integer

:aspect:`Required`
   false
:aspect:`Description`
   Number of items/chunk or if fixed then number of chunks

.. _iterator.chunk_as:

as
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Template variable name to assign; if not specified the ViewHelper returns the variable instead.

.. _iterator.chunk_fixed:

fixed
-----

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If true, creates $count chunks instead of $count values per chunk

.. _iterator.chunk_preservekeys:

preserveKeys
------------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If set to true, the original array keys will be preserved
