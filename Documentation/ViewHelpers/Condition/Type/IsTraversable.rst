:navigation-title: condition.type.isTraversable
.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-condition-type-istraversable:

============================================================================
condition.type.isTraversable ViewHelper `<vhs:condition.type.isTraversable>`
============================================================================


Condition: Value implements interface Traversable
=================================================

Condition ViewHelper which renders the `then` child if provided
value implements interface Traversable.


.. _fluidtypo3-vhs-condition-type-istraversable_arguments:

Arguments
=========


.. _condition.type.istraversable_then:

then
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if met.

.. _condition.type.istraversable_else:

else
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if not met.

.. _condition.type.istraversable_value:

value
-----

:aspect:`DataType`
   mixed

:aspect:`Required`
   true
:aspect:`Description`
   Value to check
