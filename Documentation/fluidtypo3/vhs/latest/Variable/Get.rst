.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-variable-get:

============
variable.get
============


Variable: Get
=============

ViewHelper used to read the value of a current template
variable. Can be used with dynamic indices in arrays:

::

    <v:variable.get name="array.{dynamicIndex}" />
    <v:variable.get name="array.{v:variable.get(name: 'arrayOfSelectedKeys.{indexInArray}')}" />
    <f:for each="{v:variable.get(name: 'object.arrayProperty.{dynamicIndex}')}" as="nestedObject">
        ...
    </f:for>

Or to read names of variables which contain dynamic parts:

::

    <!-- if {variableName} is "Name", outputs value of {dynamicName} -->
    {v:variable.get(name: 'dynamic{variableName}')}

If your target object is an array with unsequential yet
numeric indices (e.g. {123: 'value1', 513: 'value2'},
commonly seen in reindexed UID map arrays) use
`useRawKeys="TRUE"` to indicate you do not want your
array/QueryResult/Iterator to be accessed by locating
the Nth element - which is the default behavior.

::

    Do not try `useRawKeys="TRUE"` on QueryResult or
    ObjectStorage unless you are fully aware what you are
    doing. These particular types require an unpredictable
    index value - the SPL object hash value - when accessing
    members directly. This SPL indexing and the very common
    occurrences of QueryResult and ObjectStorage variables
    in templates is the very reason why `useRawKeys` by
    default is set to `FALSE`.

Arguments
=========


.. _variable.get_name:

name
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Name of variable to retrieve

.. _variable.get_userawkeys:

useRawKeys
----------

:aspect:`DataType`
   boolean

:aspect:`Required`
   false
:aspect:`Description`
   If TRUE, the path is directly passed to ObjectAccess. If FALSE, a custom and compatible VHS method is used
