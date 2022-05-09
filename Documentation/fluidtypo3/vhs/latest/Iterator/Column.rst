.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-iterator-column:

===============
iterator.column
===============


Iterator Column Extraction ViewHelper
=====================================

Implementation of `array_column` for Fluid.

Accepts an input iterator/array and creates a new array
using values from one column and optionally keys from another
column.

Usage examples
--------------

::

    <!-- Given input array of user data arrays with "name" and "uid" column: -->
    <f:for each="{users -> v:iterator.column(columnKey: 'name', indexKey: 'uid')}" as="username" key="uid">
        User {username} has UID {uid}.
    </f:for>

The above demonstrates the logic of the ViewHelper, but the
example itself of course gives the same result as just iterating
the `users` variable itself and outputting `{user.username}` etc.,
but the real power of the ViewHelper comes when using it to feed
other ViewHelpers with data sets:

::

    <!--
    Given same input array as above. Idea being that *any* iterator
    can be supported as input for "options".
    -->
    Select user: <f:form.select options="{users -> v:iterator.column(columnKey: 'name', indexKey: 'uid')}" />

::

    <!-- Given same input array as above. Idea being to output all user UIDs as CSV -->
    All UIDs: {users -> v:iterator.column(columnKey: 'uid') -> v:iterator.implode()}

::

    <!-- Given same input array as above. Idea being to output all unique users' countries as a list: -->
    Our users live in the following countries:
    {users -> v:iterator.column(columnKey: 'countryName')
        -> v:iterator.unique()
        -> v:iterator.implode(glue: ' - ')}

Note that the ViewHelper also supports the "as" argument which
allows you to not return the new array but instead assign it
as a new template variable - like any other "as"-capable ViewHelper.

Caveat
------

This ViewHelper passes the subject directly to `array_column` and
as such it *does not support dotted paths in either key argument
to extract sub-properties*. That means it *does not support Extbase
enties as input unless you explicitly implemented `ArrayAccess` on
the model of the entity and even then support is limited to first
level properties' values without dots in their names*.

Arguments
=========


.. _iterator.column_subject:

subject
-------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Input to work on - Array/Traversable/...

.. _iterator.column_columnkey:

columnKey
---------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Name of the column whose values will become the value of the new array

.. _iterator.column_indexkey:

indexKey
--------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Name of the column whose values will become the index of the new array

.. _iterator.column_as:

as
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Template variable name to assign; if not specified the ViewHelper returns the variable instead.
