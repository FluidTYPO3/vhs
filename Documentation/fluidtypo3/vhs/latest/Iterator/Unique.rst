.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-iterator-unique:

===============
iterator.unique
===============


Iterator Unique Values ViewHelper
=================================

Implementation of `array_unique` for Fluid

Accepts an input array of values and returns/assigns
a new array containing only the unique values found
in the input array.

Note that the ViewHelper does not support the sorting
parameter - if you wish to sort the result you should
use `v:iterator.sort` in a chain.

Usage examples
--------------

::

    <!--
    Given a (large) array of every user's country with possible duplicates.
    The idea being to output only a unique list of countries' names.
    -->
    
    Countries of our users: {userCountries -> v:iterator.unique() -> v:iterator.implode(glue: ' - ')}

Output:

::

    Countries of our users: USA - USA - Denmark - Germany - Germany - USA - Denmark - Germany

::

    <!-- Given the same use case as above but also implementing sorting -->
    Countries of our users, in alphabetical order:
    {userCountries -> v:iterator.unique()
        -> v:iterator.sort(sortFlags: 'SORT_NATURAL')
        -> v:iterator.implode(glue: ' - ')}

Output:

::

    Countries of our users: Denmark - Germany - USA

Arguments
=========


.. _iterator.unique_subject:

subject
-------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   The input array/Traversable to process

.. _iterator.unique_as:

as
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Template variable name to assign; if not specified the ViewHelper returns the variable instead.
