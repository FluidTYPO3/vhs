:navigation-title: condition.type.isDomainObject
.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-condition-type-isdomainobject:

==============================================================================
condition.type.isDomainObject ViewHelper `<vhs:condition.type.isDomainObject>`
==============================================================================


Condition: Value is a domain object
===================================

Condition ViewHelper which renders the `then` child if provided
value is a domain object, i.e. it inherits from extbase's base
class.


.. _fluidtypo3-vhs-condition-type-isdomainobject_arguments:

Arguments
=========


.. _condition.type.isdomainobject_then:

then
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if met.

.. _condition.type.isdomainobject_else:

else
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if not met.

.. _condition.type.isdomainobject_value:

value
-----

:aspect:`DataType`
   mixed

:aspect:`Required`
   true
:aspect:`Description`
   Value to check

.. _condition.type.isdomainobject_fullstring:

fullString
----------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Need
