:navigation-title: condition.variable.isNull
.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-condition-variable-isnull:

======================================================================
condition.variable.isNull ViewHelper `<vhs:condition.variable.isNull>`
======================================================================


Condition: Value is NULL
========================

Condition ViewHelper which renders the `then` child if provided
value is NULL.


.. _fluidtypo3-vhs-condition-variable-isnull_arguments:

Arguments
=========


.. _condition.variable.isnull_then:

then
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if met.

.. _condition.variable.isnull_else:

else
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if not met.

.. _condition.variable.isnull_value:

value
-----

:aspect:`DataType`
   string

:aspect:`Required`
   true
:aspect:`Description`
   Value to check
