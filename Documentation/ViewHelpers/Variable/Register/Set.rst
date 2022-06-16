.. include:: /Includes.rst.txt

.. _fluidtypo3-vhs-variable-register-set:

=====================
variable.register.set
=====================


Variable\Register: Set
======================

Sets a single register in the TSFE-register.

Using as `{value -> v:variable.register.set(name: 'myVar')}` makes $GLOBALS["TSFE"]->register['myVar']
contain `{value}`.

Arguments
=========


.. _variable.register.set_value:

value
-----

:aspect:`DataType`
   mixed

:aspect:`Required`
   false
:aspect:`Description`
   Value to set

.. _variable.register.set_name:

name
----

:aspect:`DataType`
   string

:aspect:`Required`
   false
:aspect:`Description`
   Name of register
