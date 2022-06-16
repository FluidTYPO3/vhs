.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-condition-variable-isset:

========================
condition.variable.isset
========================


Variable: Isset
===============

Renders the `then` child if the variable name given in
the `name` argument exists in the template. The value
can be zero, NULL or an empty string - but the ViewHelper
will still return TRUE if the variable exists.

Combines well with dynamic variable names:

::

    <!-- if {variableContainingVariableName} is "foo" this checks existence of {foo} -->
    <v:condition.variable.isset name="{variableContainingVariableName}">...</v:condition.variable.isset>
    <!-- if {suffix} is "Name" this checks existence of "variableName" -->
    <v:condition.variable.isset name="variable{suffix}">...</v:condition.variable.isset>
    <!-- outputs value of {foo} if {bar} is defined -->
    {foo -> v:condition.variable.isset(name: bar)}

ONLY WORKS ON TYPO3v8+!

Arguments
=========


.. _condition.variable.isset_then:

then
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if met.

.. _condition.variable.isset_else:

else
----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to be returned if the condition if not met.

.. _condition.variable.isset_name:

name
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Name of the variable
