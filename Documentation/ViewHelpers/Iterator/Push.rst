.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-iterator-push:

=============
iterator.push
=============


Adds one variable to the end of the array and returns the result.

Example:

::

    <f:for each="{array -> v:iterator.push(add: additionalObject, key: 'newkey')}" as="combined">
    ...
    </f:for>

Arguments
=========


.. _iterator.push_subject:

subject
-------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Input to work on - Array/Traversable/...

.. _iterator.push_add:

add
---

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Member to add to end of array

.. _iterator.push_key:

key
---

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Optional key to use. If key exists the member will be overwritten!

.. _iterator.push_as:

as
--

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Template variable name to assign; if not specified the ViewHelper returns the variable instead.
