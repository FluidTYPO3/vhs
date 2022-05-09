.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-condition-form-isrequired:

=========================
condition.form.isRequired
=========================


Is Field Required ViewHelper (condition)
========================================

Takes a property (dotted path supported) and renders the
then-child if the property at the given path has an

Arguments
=========


.. _condition.form.isrequired_then:

then
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if met.

.. _condition.form.isrequired_else:

else
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if not met.

.. _condition.form.isrequired_property:

property
--------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   The property name, dotted path supported, to determine required.

.. _condition.form.isrequired_validatorname:

validatorName
-------------

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   The name of the validator that must exist for the condition to be true.

.. _condition.form.isrequired_object:

object
------

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Optional object - if not specified, grabs the associated form object.
