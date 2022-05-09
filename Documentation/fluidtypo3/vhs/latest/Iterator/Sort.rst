.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-iterator-sort:

=============
iterator.sort
=============


Sorts an instance of ObjectStorage, an Iterator implementation,
an Array or a QueryResult (including Lazy counterparts).

Can be used inline, i.e.:

::

    <f:for each="{dataset -> v:iterator.sort(sortBy: 'name')}" as="item">
        // iterating data which is ONLY sorted while rendering this particular loop
    </f:for>

Arguments
=========


.. _iterator.sort_subject:

subject
-------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The array/Traversable instance to sort

.. _iterator.sort_sortby:

sortBy
------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Which property/field to sort by - leave out for numeric sorting based on indexes(keys)

.. _iterator.sort_order:

order
-----

:aspect:`DataType`
   string

:aspect:`Default`
   'ASC'

:aspect:`Required`
   false
:aspect:`Description`
   ASC, DESC, RAND or SHUFFLE. RAND preserves keys, SHUFFLE does not - but SHUFFLE is faster

.. _iterator.sort_sortflags:

sortFlags
---------

:aspect:`DataType`
   string

:aspect:`Default`
   'SORT_REGULAR'

:aspect:`Required`
   false
:aspect:`Description`
   Constant name from PHP for `SORT_FLAGS`: `SORT_REGULAR`, `SORT_STRING`, `SORT_NUMERIC`, `SORT_NATURAL`, `SORT_LOCALE_STRING` or `SORT_FLAG_CASE`. You can provide a comma seperated list or array to use a combination of flags.

.. _iterator.sort_as:

as
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Template variable name to assign; if not specified the ViewHelper returns the variable instead.
