:navigation-title: condition.type.isInstanceOf
.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-condition-type-isinstanceof:

==========================================================================
condition.type.isInstanceOf ViewHelper `<vhs:condition.type.isInstanceOf>`
==========================================================================


Condition: Value is an instance of a class
==========================================

Condition ViewHelper which renders the `then` child if provided
value is an instance of provided class name.


.. _fluidtypo3-vhs-condition-type-isinstanceof_arguments:

Arguments
=========


.. _condition.type.isinstanceof_then:

then
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if met.

.. _condition.type.isinstanceof_else:

else
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if not met.

.. _condition.type.isinstanceof_value:

value
-----

:aspect:`DataType`
   mixed

:aspect:`Required`
   true
:aspect:`Description`
   Value to check

.. _condition.type.isinstanceof_class:

class
-----

:aspect:`DataType`
   mixed

:aspect:`Required`
   true
:aspect:`Description`
   ClassName to check against
