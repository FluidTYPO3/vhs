.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-variable-convert:

================
variable.convert
================


Convert ViewHelper
==================

Converts $value to $type which can be one of 'string', 'integer',
'float', 'boolean', 'array' or 'ObjectStorage'. If $value is NULL
sensible defaults are assigned or $default which obviously has to
be of $type as well.

Arguments
=========


.. _variable.convert_value:

value
-----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to convert into a different type

.. _variable.convert_type:

type
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Data type to convert the value into. Can be one of "string", "integer", "float", "boolean", "array" or "ObjectStorage".

.. _variable.convert_default:

default
-------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Optional default value to assign to the converted variable in case it is NULL.
